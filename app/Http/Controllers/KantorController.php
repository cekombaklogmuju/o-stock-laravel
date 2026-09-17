<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KantorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kantors = Kantor::when($search, function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('kode_cabang', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('kantor.index', compact('kantors', 'search'));
    }

    public function create()
    {
        return view('kantor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:kantors,kode_cabang',
            'tipe' => 'required|string|in:Pusat,Cabang',
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . Str::lower($validated['kode_cabang']);

        Kantor::create($validated);

        return redirect()->route('kantor.index')->with('success', 'Cabang/Kantor berhasil ditambahkan.');
    }

    public function edit(Kantor $kantor)
    {
        return view('kantor.edit', compact('kantor'));
    }

    public function update(Request $request, Kantor $kantor)
    {
        $validated = $request->validate([
            'kode_cabang' => 'required|string|max:20|unique:kantors,kode_cabang,' . $kantor->id,
            'tipe' => 'required|string|in:Pusat,Cabang',
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . Str::lower($validated['kode_cabang']);

        $kantor->update($validated);

        return redirect()->route('kantor.index')->with('success', 'Cabang/Kantor berhasil diperbarui.');
    }

    public function destroy(Kantor $kantor)
    {
        if ($kantor->tipe === 'Pusat') {
            return back()->with('error', 'Kantor Pusat tidak dapat dihapus.');
        }

        $kantor->delete();
        return redirect()->route('kantor.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
