<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\KartuStok;
use App\Models\Produk;
use Illuminate\Http\Request;

class KartuStokController extends Controller
{
    public function index(Request $request)
    {
        $produkId = $request->input('id_produk');
        $search = $request->input('search');
        $tipe = $request->input('tipe');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = KartuStok::with(['produk.kategori'])
            ->when($produkId, function ($q) use ($produkId) {
                $q->where('id_produk', $produkId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('no_bukti', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('produk', function ($p) use ($search) {
                            $p->where('nama', 'like', "%{$search}%")
                              ->orWhere('kode_produk', 'like', "%{$search}%")
                              ->orWhere('barcode', 'like', "%{$search}%")
                              ->orWhere('lokasi_rak', 'like', "%{$search}%");
                        });
                });
            })
            ->when($tipe === 'masuk', function ($q) {
                $q->where('stok_masuk', '>', 0);
            })
            ->when($tipe === 'keluar', function ($q) {
                $q->where('stok_keluar', '>', 0);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('tanggal', '<=', $endDate);
            })
            ->latest('tanggal')
            ->latest('id');

        $records = $query->paginate(20)->withQueryString();
        $produks = Produk::where('status', 'aktif')->orderBy('nama')->get();

        return view('kartu_stok.index', compact('records', 'produks', 'produkId', 'search', 'tipe', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        $produkId = $request->input('id_produk');
        $tipe = $request->input('tipe');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = KartuStok::with(['produk'])
            ->when($produkId, function ($q) use ($produkId) {
                $q->where('id_produk', $produkId);
            })
            ->when($tipe === 'masuk', function ($q) {
                $q->where('stok_masuk', '>', 0);
            })
            ->when($tipe === 'keluar', function ($q) {
                $q->where('stok_keluar', '>', 0);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('tanggal', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('tanggal', '<=', $endDate);
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        $records = $query->get();
        $selectedProduk = $produkId ? Produk::find($produkId) : null;

        return view('kartu_stok.print', compact('records', 'selectedProduk', 'startDate', 'endDate'));
    }
}
