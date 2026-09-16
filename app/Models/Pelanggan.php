<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'alamat',
        'telp',
        'email',
    ];

    public function tiketServis(): HasMany
    {
        return $this->hasMany(TiketServis::class, 'pelanggan_id');
    }
}
