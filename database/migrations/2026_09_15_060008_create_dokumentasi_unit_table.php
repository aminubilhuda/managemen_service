<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumentasi_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket_servis')->cascadeOnDelete();
            $table->enum('tipe_dokumentasi', ['sebelum', 'sesudah', 'proses']);
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->foreignId('diupload_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('tiket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_unit');
    }
};
