<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $produk = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $kategoriList = KategoriProduk::orderBy('nama_kategori')->get();

        return view('produk.index', compact('produk', 'kategoriList'));
    }

    public function create()
    {
        $kategoriList = KategoriProduk::orderBy('nama_kategori')->get();

        return view('produk.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_produk,id',
            'kode_produk' => 'required|string|max:50|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:255',
            'tipe' => 'required|in:sparepart,jasa',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'garansi_hari' => 'nullable|integer|min:0',
            'stok' => 'nullable|integer|min:0',
        ]);

        Produk::create($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $kategoriList = KategoriProduk::orderBy('nama_kategori')->get();

        return view('produk.edit', compact('produk', 'kategoriList'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_produk,id',
            'kode_produk' => 'required|string|max:50|unique:produk,kode_produk,'.$produk->id,
            'nama_produk' => 'required|string|max:255',
            'tipe' => 'required|in:sparepart,jasa',
            'harga_jual' => 'required|numeric|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'garansi_hari' => 'nullable|integer|min:0',
            'stok' => 'nullable|integer|min:0',
        ]);

        $produk->update($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * API: Cari produk untuk form invoice.
     */
    public function search(Request $request)
    {
        $results = Produk::where('nama_produk', 'like', "%{$request->q}%")
            ->orWhere('kode_produk', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'kode_produk', 'nama_produk', 'tipe', 'harga_jual', 'harga_modal', 'stok']);

        return response()->json($results);
    }
}
