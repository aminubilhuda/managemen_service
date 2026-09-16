<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('telp', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pelanggan = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Pelanggan::create($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load(['tiketServis.invoice', 'tiketServis.teknisi']);

        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * API: Cari pelanggan untuk autocomplete.
     */
    public function search(Request $request)
    {
        $results = Pelanggan::where('nama_pelanggan', 'like', "%{$request->q}%")
            ->orWhere('telp', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'nama_pelanggan', 'telp', 'email']);

        return response()->json($results);
    }
}
