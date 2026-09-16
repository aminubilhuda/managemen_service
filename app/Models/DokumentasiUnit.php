<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumentasiUnit extends Model
{
    protected $table = 'dokumentasi_unit';

    protected $fillable = [
        'tiket_id',
        'tipe_dokumentasi',
        'file_path',
        'keterangan',
        'diupload_oleh',
    ];

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(TiketServis::class, 'tiket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diupload_oleh');
    }

    public function getPathFotoAttribute(): ?string
    {
        return $this->file_path;
    }

    public function getFotoUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }
}
