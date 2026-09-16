<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatusTiket extends Model
{
    protected $table = 'riwayat_status_tiket';

    protected $fillable = [
        'tiket_id',
        'status_sebelum',
        'status_sesudah',
        'catatan',
        'diubah_oleh',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(TiketServis::class, 'tiket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function getStatusAttribute(): ?string
    {
        return $this->status_sesudah;
    }

    public function getStatusLabelAttribute(): string
    {
        return TiketServis::STATUSES[$this->status_sesudah] ?? ucfirst(str_replace('_', ' ', $this->status_sesudah));
    }
}
