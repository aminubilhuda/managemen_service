<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_pengeluaran');
            $table->string('deskripsi');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete();
            $table->string('bukti')->nullable();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('kategori_pengeluaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
