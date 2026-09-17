<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalesmanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeBranchId = session('active_branch_id');

        $salesmen = Salesman::with('cabang')
            ->when($activeBranchId, function ($q) use ($activeBranchId) {
                $q->where('id_cabang', $activeBranchId);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_telp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('salesman.index', compact('salesmen', 'search'));
    }

    public function create()
    {
        $kantors = Kantor::all();
        return view('salesman.create', compact('kantors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cabang' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:150',
            'no_telp' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . time();
        Salesman::create($validated);

        return redirect()->route('salesman.index')->with('success', 'Salesman berhasil ditambahkan.');
    }

    public function edit(Salesman $salesman)
    {
        $kantors = Kantor::all();
        return view('salesman.edit', compact('salesman', 'kantors'));
    }

    public function update(Request $request, Salesman $salesman)
    {
        $validated = $request->validate([
            'id_cabang' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:150',
            'no_telp' => 'nullable|string|max:50',
        ]);

        $validated['slug'] = Str::slug($validated['nama']) . '-' . $salesman->id;
        $salesman->update($validated);

        return redirect()->route('salesman.index')->with('success', 'Salesman berhasil diperbarui.');
    }

    public function destroy(Salesman $salesman)
    {
        $salesman->delete();
        return redirect()->route('salesman.index')->with('success', 'Salesman berhasil dihapus.');
    }
}
