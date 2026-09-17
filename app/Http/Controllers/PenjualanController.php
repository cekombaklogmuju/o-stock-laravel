<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\Konsumen;
use App\Models\Penjualan;
use App\Models\PenjualanItem;
use App\Models\Produk;
use App\Models\Salesman;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenjualanController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeBranchId = session('active_branch_id');

        $penjualans = Penjualan::with(['cabang', 'konsumen', 'salesman', 'items.produk'])
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('id_cabang', $activeBranchId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('no_invoice', 'like', "%{$search}%")
                        ->orWhereHas('konsumen', function ($k) use ($search) {
                            $k->where('nama', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('tanggal')
            ->latest('id')
            ->paginate(15);

        return view('penjualan.index', compact('penjualans', 'search'));
    }

    public function create()
    {
        $activeBranchId = session('active_branch_id');
        $user = Auth::user();

        // If user is tied to a specific branch, default to it
        $selectedBranchId = $activeBranchId ?? ($user->id_cabang ?? Kantor::first()->id ?? 1);

        $kantors = Kantor::all();
        $konsumens = Konsumen::where('id_cabang', $selectedBranchId)->get();
        if ($konsumens->isEmpty()) {
            $konsumens = Konsumen::all();
        }

        $salesmen = Salesman::where('id_cabang', $selectedBranchId)->get();
        if ($salesmen->isEmpty()) {
            $salesmen = Salesman::all();
        }

        $produks = Produk::where('status', 'aktif')->where('stok', '>', 0)->get();
        $generatedInvoice = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        return view('penjualan.create', compact(
            'kantors',
            'konsumens',
            'salesmen',
            'produks',
            'selectedBranchId',
            'generatedInvoice'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_invoice' => 'required|string|unique:penjualans,no_invoice',
            'tanggal' => 'required|date',
            'id_cabang' => 'required|exists:kantors,id',
            'id_konsumen' => 'required|exists:konsumens,id',
            'id_salesman' => 'required|exists:salesmen,id',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        try {
            $penjualan = DB::transaction(function () use ($request) {
                $cabang = Kantor::findOrFail($request->input('id_cabang'));

                $totalHarga = 0;
                $lineItems = [];

                foreach ($request->input('items') as $itemData) {
                    $harga = (float) $itemData['harga'];
                    $jumlah = (int) $itemData['jumlah'];
                    $discountPercent = isset($itemData['discount']) ? (float) $itemData['discount'] : 0;

                    $subtotal = ($harga * $jumlah) * (1 - ($discountPercent / 100));
                    $totalHarga += $subtotal;

                    $lineItems[] = [
                        'id_produk' => $itemData['id_produk'],
                        'harga' => $harga,
                        'jumlah' => $jumlah,
                        'discount' => $discountPercent,
                        'subtotal' => $subtotal,
                    ];
                }

                $penjualan = Penjualan::create([
                    'no_invoice' => $request->input('no_invoice'),
                    'tanggal' => $request->input('tanggal'),
                    'id_cabang' => $cabang->id,
                    'id_konsumen' => $request->input('id_konsumen'),
                    'id_salesman' => $request->input('id_salesman'),
                    'total_harga' => $totalHarga,
                    'catatan' => $request->input('catatan'),
                ]);

                foreach ($lineItems as $line) {
                    PenjualanItem::create([
                        'id_penjualan' => $penjualan->id,
                        'id_produk' => $line['id_produk'],
                        'harga' => $line['harga'],
                        'jumlah' => $line['jumlah'],
                        'discount' => $line['discount'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    // Deduct stock and write KartuStok
                    $this->inventoryService->recordStockMovement(
                        $line['id_produk'],
                        $cabang->id,
                        'keluar',
                        $line['jumlah'],
                        $penjualan->no_invoice,
                        'Penjualan invoice ' . $penjualan->no_invoice
                    );
                }

                return $penjualan;
            });

            return redirect()->route('penjualan.show', $penjualan->id)
                ->with('success', 'Transaksi penjualan berhasil disimpan dan stok otomatis dipotong.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['cabang', 'konsumen', 'salesman', 'items.produk']);
        return view('penjualan.show', compact('penjualan'));
    }
}
