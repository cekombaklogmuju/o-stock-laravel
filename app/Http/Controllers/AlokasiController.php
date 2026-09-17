<?php

namespace App\Http\Controllers;

use App\Models\Alokasi;
use App\Models\AlokasiItem;
use App\Models\Kantor;
use App\Models\Produk;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AlokasiController extends Controller
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

        $alokasis = Alokasi::with(['cabang', 'items.produk'])
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('id_cabang', $activeBranchId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where('no_alokasi', 'like', "%{$search}%");
            })
            ->latest('tanggal')
            ->paginate(15);

        return view('alokasi.index', compact('alokasis', 'search'));
    }

    public function create()
    {
        $kantors = Kantor::all();
        $produks = Produk::where('status', 'aktif')->get();
        $generatedNo = 'ALK-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        return view('alokasi.create', compact('kantors', 'produks', 'generatedNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_alokasi' => 'required|string|unique:alokasis,no_alokasi',
            'id_cabang' => 'required|exists:kantors,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $cabang = Kantor::findOrFail($request->input('id_cabang'));

                $alokasi = Alokasi::create([
                    'no_alokasi' => $request->input('no_alokasi'),
                    'id_cabang' => $cabang->id,
                    'tanggal' => $request->input('tanggal'),
                    'keterangan' => $request->input('keterangan'),
                ]);

                foreach ($request->input('items') as $itemData) {
                    AlokasiItem::create([
                        'id_alokasi' => $alokasi->id,
                        'id_produk' => $itemData['id_produk'],
                        'jumlah' => $itemData['jumlah'],
                    ]);

                    // Increase stock & record in KartuStok
                    $this->inventoryService->recordStockMovement(
                        $itemData['id_produk'],
                        $cabang->id,
                        'masuk',
                        $itemData['jumlah'],
                        $alokasi->no_alokasi,
                        'Alokasi persediaan ke ' . $cabang->nama
                    );
                }
            });

            return redirect()->route('alokasi.index')->with('success', 'Alokasi stok berhasil dicatat dan masuk ke kartu stok.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses alokasi: ' . $e->getMessage());
        }
    }

    public function show(Alokasi $alokasi)
    {
        $alokasi->load(['cabang', 'items.produk']);
        return view('alokasi.show', compact('alokasi'));
    }
}
