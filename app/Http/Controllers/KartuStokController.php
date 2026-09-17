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
        $cabangId = $request->input('id_cabang') ?? session('active_branch_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = KartuStok::with(['produk', 'cabang']);

        if ($cabangId) {
            $query->where('id_cabang', $cabangId);
        }

        if ($produkId) {
            $query->where('id_produk', $produkId);
        }

        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $records = $query->latest('tanggal')->latest('id')->paginate(20)->withQueryString();

        $kantors = Kantor::all();
        $produks = Produk::all();

        return view('kartu_stok.index', compact('records', 'kantors', 'produks', 'produkId', 'cabangId', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        $produkId = $request->input('id_produk');
        $cabangId = $request->input('id_cabang') ?? session('active_branch_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = KartuStok::with(['produk', 'cabang']);

        if ($cabangId) {
            $query->where('id_cabang', $cabangId);
        }
        if ($produkId) {
            $query->where('id_produk', $produkId);
        }
        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $records = $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc')->get();
        $selectedProduk = $produkId ? Produk::find($produkId) : null;
        $selectedCabang = $cabangId ? Kantor::find($cabangId) : null;

        return view('kartu_stok.print', compact('records', 'selectedProduk', 'selectedCabang', 'startDate', 'endDate'));
    }
}
