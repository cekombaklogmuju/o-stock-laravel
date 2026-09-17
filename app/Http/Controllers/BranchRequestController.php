<?php

namespace App\Http\Controllers;

use App\Models\BranchRequest;
use App\Models\BranchRequestItem;
use App\Models\Kantor;
use App\Models\Produk;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchRequestController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        $user = Auth::user();

        $requests = BranchRequest::with(['cabangPeminta', 'userPeminta', 'processor', 'items.produk'])
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($user->role === 'cabang' && $user->id_cabang, function ($q) use ($user) {
                $q->where('id_cabang_peminta', $user->id_cabang);
            })
            ->latest('tanggal_permintaan')
            ->paginate(15);

        return view('branch_request.index', compact('requests', 'status'));
    }

    public function create()
    {
        $kantors = Kantor::all();
        $produks = Produk::where('status', 'aktif')->get();
        $generatedNo = 'REQ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        return view('branch_request.create', compact('kantors', 'produks', 'generatedNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_permintaan' => 'required|string|unique:branch_requests,no_permintaan',
            'id_cabang_peminta' => 'required|exists:kantors,id',
            'prioritas' => 'required|in:normal,urgent,critical',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id',
            'items.*.jumlah_diminta' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $branchRequest = BranchRequest::create([
                'no_permintaan' => $request->input('no_permintaan'),
                'tanggal_permintaan' => now(),
                'id_cabang_peminta' => $request->input('id_cabang_peminta'),
                'id_user_peminta' => Auth::id(),
                'status' => 'pending',
                'prioritas' => $request->input('prioritas'),
                'keterangan' => $request->input('keterangan'),
            ]);

            foreach ($request->input('items') as $item) {
                BranchRequestItem::create([
                    'id_branch_request' => $branchRequest->id,
                    'id_produk' => $item['id_produk'],
                    'jumlah_diminta' => $item['jumlah_diminta'],
                    'jumlah_disetujui' => 0,
                ]);
            }
        });

        return redirect()->route('branch_request.index')->with('success', 'Permintaan stok cabang berhasil diajukan.');
    }

    public function show(BranchRequest $branchRequest)
    {
        $branchRequest->load(['cabangPeminta', 'userPeminta', 'processor', 'items.produk']);
        return view('branch_request.show', compact('branchRequest'));
    }

    public function process(Request $request, BranchRequest $branchRequest)
    {
        $action = $request->input('action'); // 'approve' or 'reject'
        $notes = $request->input('catatan_proses');

        if ($branchRequest->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($branchRequest, $action, $notes, $request) {
                if ($action === 'approve') {
                    $itemApprovals = $request->input('approved_qty', []);
                    $hasPartial = false;

                    foreach ($branchRequest->items as $item) {
                        $qtyApproved = isset($itemApprovals[$item->id]) ? (int) $itemApprovals[$item->id] : $item->jumlah_diminta;

                        if ($qtyApproved > 0) {
                            // Check stock availability
                            $this->inventoryService->recordStockMovement(
                                $item->id_produk,
                                $branchRequest->id_cabang_peminta,
                                'masuk',
                                $qtyApproved,
                                $branchRequest->no_permintaan,
                                'Penerimaan stok dari permintaan ' . $branchRequest->no_permintaan
                            );
                        }

                        if ($qtyApproved < $item->jumlah_diminta) {
                            $hasPartial = true;
                        }

                        $item->update(['jumlah_disetujui' => $qtyApproved]);
                    }

                    $branchRequest->update([
                        'status' => $hasPartial ? 'partial' : 'approved',
                        'tanggal_diproses' => now(),
                        'diproses_oleh' => Auth::id(),
                        'catatan_proses' => $notes,
                    ]);
                } else {
                    $branchRequest->update([
                        'status' => 'rejected',
                        'tanggal_diproses' => now(),
                        'diproses_oleh' => Auth::id(),
                        'catatan_proses' => $notes,
                    ]);
                }
            });

            $msg = $action === 'approve' ? 'Permintaan cabang berhasil disetujui dan stok telah ditransfer.' : 'Permintaan cabang telah ditolak.';
            return redirect()->route('branch_request.index')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses permintaan: ' . $e->getMessage());
        }
    }
}
