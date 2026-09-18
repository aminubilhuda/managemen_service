<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaan';

    protected $fillable = [
        'nama_perusahaan',
        'deskripsi',
        'alamat',
        'telp',
        'email',
        'logo',
        'npwp',
    ];
}
