<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class KategoriProdukController extends Controller
{
    public function index()
    {
        $kategori = KategoriProduk::withCount('produk')->orderBy('nama_kategori')->paginate(15);

        return view('kategori-produk.index', compact('kategori'));
    }

    public function create()
    {
        return view('kategori-produk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_produk,nama_kategori',
        ]);

        KategoriProduk::create($validated);

        return redirect()->route('kategori-produk.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriProduk $kategoriProduk)
    {
        return view('kategori-produk.edit', compact('kategoriProduk'));
    }

    public function update(Request $request, KategoriProduk $kategoriProduk)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_produk,nama_kategori,'.$kategoriProduk->id,
        ]);

        $kategoriProduk->update($validated);

        return redirect()->route('kategori-produk.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategoriProduk)
    {
        if ($kategoriProduk->produk()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        $kategoriProduk->delete();

        return redirect()->route('kategori-produk.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
