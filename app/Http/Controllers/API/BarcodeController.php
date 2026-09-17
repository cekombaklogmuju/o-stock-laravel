<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Exact lookup by barcode or product code for scanner integration.
     */
    public function lookup(Request $request): JsonResponse
    {
        $code = trim($request->input('barcode') ?? $request->input('code') ?? $request->input('q') ?? '');

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode barcode tidak boleh kosong.',
            ], 400);
        }

        $produk = Produk::with(['kategori', 'supplier'])
            ->where('barcode', $code)
            ->orWhere('kode_produk', $code)
            ->first();

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => "Produk dengan barcode '{$code}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $produk->id,
                'kode_produk' => $produk->kode_produk,
                'barcode' => $produk->barcode,
                'nama' => $produk->nama,
                'harga_jual' => (float) $produk->harga_jual,
                'stok' => $produk->stok,
                'status' => $produk->status,
                'kategori' => $produk->kategori->nama ?? null,
                'supplier' => $produk->supplier->nama ?? null,
            ],
        ]);
    }

    /**
     * Autocomplete / search query for dropdowns.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        $query = Produk::query()->where('status', 'aktif');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode_produk', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        $items = $query->limit(20)->get(['id', 'kode_produk', 'barcode', 'nama', 'harga_jual', 'stok']);

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }
}
