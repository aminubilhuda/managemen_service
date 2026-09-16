<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPajak extends Model
{
    protected $table = 'pengaturan_pajak';

    protected $fillable = [
        'nama_pajak',
        'persentase',
        'tipe_aturan',
        'nominal_batas',
        'aktif',
        'berlaku_mulai',
    ];

    protected function casts(): array
    {
        return [
            'persentase' => 'decimal:2',
            'nominal_batas' => 'decimal:2',
            'aktif' => 'boolean',
            'berlaku_mulai' => 'date',
        ];
    }

    /**
     * Scope untuk mendapatkan tarif pajak yang sedang aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Tentukan tarif pajak otomatis berdasarkan nilai nominal subtotal transaksi.
     */
    public static function tentukanPajakOtomatis(float $subtotal): ?self
    {
        $aktifPajak = static::where('aktif', true)->get();

        if ($aktifPajak->isEmpty()) {
            return null;
        }

        // 1. Cek aturan di atas batas nominal (subtotal >= nominal_batas)
        $diatas = $aktifPajak->where('tipe_aturan', 'diatas_nominal')
            ->filter(fn ($p) => $subtotal >= (float) $p->nominal_batas)
            ->sortByDesc('nominal_batas')
            ->first();

        if ($diatas) {
            return $diatas;
        }

        // 2. Cek aturan di bawah batas nominal (subtotal < nominal_batas)
        $dibawah = $aktifPajak->where('tipe_aturan', 'dibawah_nominal')
            ->filter(fn ($p) => $subtotal < (float) $p->nominal_batas)
            ->sortBy('nominal_batas')
            ->first();

        if ($dibawah) {
            return $dibawah;
        }

        // 3. Fallback ke aturan umum ('semua')
        $semua = $aktifPajak->where('tipe_aturan', 'semua')->first();
        if ($semua) {
            return $semua;
        }

        // 4. Default fallback ke item pertama yang aktif
        return $aktifPajak->first();
    }

    /**
     * Label deskriptif kondisi aturan pajak untuk tampilan UI.
     */
    public function getLabelKondisiAttribute(): string
    {
        return match ($this->tipe_aturan) {
            'diatas_nominal' => 'Subtotal ≥ Rp '.number_format((float) $this->nominal_batas, 0, ',', '.'),
            'dibawah_nominal' => 'Subtotal < Rp '.number_format((float) $this->nominal_batas, 0, ',', '.'),
            default => 'Semua Nominal Transaksi',
        };
    }
}
