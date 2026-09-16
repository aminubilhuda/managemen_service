<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'kategori_id',
        'kode_produk',
        'nama_produk',
        'tipe',
        'harga_jual',
        'harga_modal',
        'garansi_hari',
        'stok',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'harga_modal' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }
}
