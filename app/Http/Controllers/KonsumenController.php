<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\Konsumen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KonsumenController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeBranchId = session('active_branch_id');

        $konsumens = Konsumen::with('cabang')
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('id_cabang', $activeBranchId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%")
                        ->orWhere('no_telp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('konsumen.index', compact('konsumens', 'search'));
    }

    public function create()
    {
        $kantors = Kantor::all();
        return view('konsumen.create', compact('kantors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cabang' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . time();
        Konsumen::create($validated);

        return redirect()->route('konsumen.index')->with('success', 'Konsumen berhasil ditambahkan.');
    }

    public function edit(Konsumen $konsumen)
    {
        $kantors = Kantor::all();
        return view('konsumen.edit', compact('konsumen', 'kantors'));
    }

    public function update(Request $request, Konsumen $konsumen)
    {
        $validated = $request->validate([
            'id_cabang' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . $konsumen->id;
        $konsumen->update($validated);

        return redirect()->route('konsumen.index')->with('success', 'Konsumen berhasil diperbarui.');
    }

    public function destroy(Konsumen $konsumen)
    {
        $konsumen->delete();
        return redirect()->route('konsumen.index')->with('success', 'Konsumen berhasil dihapus.');
    }
}
