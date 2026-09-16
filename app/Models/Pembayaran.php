<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'invoice_id',
        'jumlah_dibayar',
        'metode_bayar',
        'tanggal_bayar',
        'dicatat_oleh',
        'bukti_bayar',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_dibayar' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
