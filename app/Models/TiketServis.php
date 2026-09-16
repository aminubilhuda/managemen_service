<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TiketServis extends Model
{
    public const STATUSES = [
        'diterima' => 'Diterima',
        'dicek' => 'Sedang Dicek',
        'menunggu_sparepart' => 'Menunggu Sparepart',
        'dikerjakan' => 'Sedang Dikerjakan',
        'selesai' => 'Selesai (Siap Diambil)',
        'diambil' => 'Sudah Diambil Pelanggan',
        'batal' => 'Dibatalkan',
    ];

    protected $table = 'tiket_servis';

    protected $fillable = [
        'no_tiket',
        'pelanggan_id',
        'teknisi_id',
        'perangkat',
        'imei_sn',
        'kelengkapan',
        'keluhan',
        'kondisi_awal',
        'estimasi_biaya',
        'estimasi_selesai',
        'biaya_final',
        'status',
        'garansi_sampai',
    ];

    protected function casts(): array
    {
        return [
            'estimasi_biaya' => 'decimal:2',
            'estimasi_selesai' => 'date',
            'biaya_final' => 'decimal:2',
            'garansi_sampai' => 'date',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusTiket::class, 'tiket_id')->orderBy('created_at', 'desc');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(DokumentasiUnit::class, 'tiket_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'tiket_id');
    }

    /**
     * Generate nomor tiket otomatis: SRV-YYYY-XXXXXX
     */
    public static function generateNoTiket(): string
    {
        $tahun = date('Y');
        $prefix = "SRV-{$tahun}-";
        $last = static::where('no_tiket', 'like', "{$prefix}%")
            ->orderBy('no_tiket', 'desc')
            ->value('no_tiket');

        if ($last) {
            $lastNumber = (int) substr($last, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Scope: tiket selesai yang belum punya invoice.
     */
    public function scopeBelumInvoice(Builder $query): Builder
    {
        return $query->where('status', 'selesai')->whereDoesntHave('invoice');
    }
}
