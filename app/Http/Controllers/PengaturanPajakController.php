<?php

namespace App\Http\Controllers;

use App\Models\PengaturanPajak;
use Illuminate\Http\Request;

class PengaturanPajakController extends Controller
{
    public function index()
    {
        $pajak = PengaturanPajak::orderBy('berlaku_mulai', 'desc')->paginate(15);

        return view('pengaturan.pajak.index', compact('pajak'));
    }

    public function create()
    {
        return view('pengaturan.pajak.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pajak' => 'required|string|max:255',
            'persentase' => 'required|numeric|min:0|max:100',
            'tipe_aturan' => 'required|in:semua,diatas_nominal,dibawah_nominal',
            'nominal_batas' => 'nullable|numeric|min:0',
            'aktif' => 'boolean',
            'berlaku_mulai' => 'nullable|date',
        ]);

        $validated['aktif'] = $request->boolean('aktif');
        $validated['nominal_batas'] = (float) ($validated['nominal_batas'] ?? 0);

        // Nonaktifkan pajak lain yang memiliki tipe aturan yang sama jika ini diaktifkan
        if ($validated['aktif']) {
            PengaturanPajak::where('aktif', true)
                ->where('tipe_aturan', $validated['tipe_aturan'])
                ->update(['aktif' => false]);
        }

        PengaturanPajak::create($validated);

        return redirect()->route('pengaturan.pajak.index')->with('success', 'Tarif pajak berhasil ditambahkan.');
    }

    public function edit(PengaturanPajak $pajak)
    {
        return view('pengaturan.pajak.edit', compact('pajak'));
    }

    public function update(Request $request, PengaturanPajak $pajak)
    {
        $validated = $request->validate([
            'nama_pajak' => 'required|string|max:255',
            'persentase' => 'required|numeric|min:0|max:100',
            'tipe_aturan' => 'required|in:semua,diatas_nominal,dibawah_nominal',
            'nominal_batas' => 'nullable|numeric|min:0',
            'aktif' => 'boolean',
            'berlaku_mulai' => 'nullable|date',
        ]);

        $validated['aktif'] = $request->boolean('aktif');
        $validated['nominal_batas'] = (float) ($validated['nominal_batas'] ?? 0);

        if ($validated['aktif']) {
            PengaturanPajak::where('aktif', true)
                ->where('id', '!=', $pajak->id)
                ->where('tipe_aturan', $validated['tipe_aturan'])
                ->update(['aktif' => false]);
        }

        $pajak->update($validated);

        return redirect()->route('pengaturan.pajak.index')->with('success', 'Tarif pajak berhasil diperbarui.');
    }

    public function destroy(PengaturanPajak $pajak)
    {
        $pajak->delete();

        return redirect()->route('pengaturan.pajak.index')->with('success', 'Tarif pajak berhasil dihapus.');
    }
}
