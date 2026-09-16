<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $table = 'invoice';

    protected $fillable = [
        'no_invoice',
        'tiket_id',
        'perusahaan_id',
        'tanggal_invoice',
        'subtotal',
        'diskon_persen',
        'diskon_nominal',
        'pajak_id',
        'pajak_persen',
        'pajak_nominal',
        'total_tagihan',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_invoice' => 'datetime',
            'subtotal' => 'decimal:2',
            'diskon_persen' => 'decimal:2',
            'diskon_nominal' => 'decimal:2',
            'pajak_persen' => 'decimal:2',
            'pajak_nominal' => 'decimal:2',
            'total_tagihan' => 'decimal:2',
        ];
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(TiketServis::class, 'tiket_id');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    public function pajak(): BelongsTo
    {
        return $this->belongsTo(PengaturanPajak::class, 'pajak_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailInvoice::class, 'invoice_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'invoice_id');
    }

    /**
     * Hitung total yang sudah dibayar.
     */
    public function getTotalDibayarAttribute(): float
    {
        return (float) $this->pembayaran()->sum('jumlah_dibayar');
    }

    /**
     * Hitung sisa tagihan.
     */
    public function getSisaTagihanAttribute(): float
    {
        return (float) $this->total_tagihan - $this->total_dibayar;
    }

    /**
     * Generate nomor invoice otomatis: INV-YYYY-XXXXXX
     */
    public static function generateNoInvoice(): string
    {
        $tahun = date('Y');
        $prefix = "INV-{$tahun}-";
        $last = static::where('no_invoice', 'like', "{$prefix}%")
            ->orderBy('no_invoice', 'desc')
            ->value('no_invoice');

        if ($last) {
            $lastNumber = (int) substr($last, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung ulang totals berdasarkan detail items.
     */
    public function hitungTotal(): void
    {
        $subtotal = (float) $this->detail()->sum('jumlah');

        // Handle both diskon nominal and diskon persen
        if ($this->diskon_nominal > 0 && ($this->diskon_persen <= 0 || empty($this->diskon_persen))) {
            $diskonNominal = min($subtotal, (float) $this->diskon_nominal);
            $diskonPersen = $subtotal > 0 ? round(($diskonNominal / $subtotal) * 100, 2) : 0;
        } else {
            $diskonPersen = (float) ($this->diskon_persen ?? 0);
            $diskonNominal = $subtotal * ($diskonPersen / 100);
        }

        $setelahDiskon = max(0, $subtotal - $diskonNominal);
        $pajakPersen = (float) ($this->pajak_persen ?? 0);
        $pajakNominal = round($setelahDiskon * ($pajakPersen / 100), 2);
        $totalTagihan = round($setelahDiskon + $pajakNominal);

        $this->update([
            'subtotal' => $subtotal,
            'diskon_persen' => $diskonPersen,
            'diskon_nominal' => $diskonNominal,
            'pajak_persen' => $pajakPersen,
            'pajak_nominal' => $pajakNominal,
            'total_tagihan' => $totalTagihan,
        ]);
    }

    /**
     * Update status berdasarkan total pembayaran.
     */
    public function updateStatusPembayaran(): void
    {
        if ($this->status === 'void') {
            return;
        }

        $totalDibayar = round((float) $this->pembayaran()->sum('jumlah_dibayar'), 2);
        $totalTagihan = round((float) $this->total_tagihan, 2);

        if ($totalDibayar <= 0) {
            $this->update(['status' => 'unpaid']);
        } elseif ($totalDibayar >= ($totalTagihan - 0.01)) {
            $this->update(['status' => 'paid']);
            if ($this->tiket_id && $this->tiket) {
                $this->tiket->update(['biaya_final' => $this->total_tagihan]);
            }
        } else {
            $this->update(['status' => 'partial']);
        }
    }
}
