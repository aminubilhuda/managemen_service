<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailInvoice extends Model
{
    protected $table = 'detail_invoice';

    protected $fillable = [
        'invoice_id',
        'produk_id',
        'deskripsi',
        'qty',
        'harga_satuan',
        'harga_modal_satuan',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:2',
            'harga_modal_satuan' => 'decimal:2',
            'jumlah' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
