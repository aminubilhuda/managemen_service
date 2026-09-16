<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_produk')->cascadeOnDelete();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->enum('tipe', ['sparepart', 'jasa']);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('harga_modal', 15, 2)->default(0);
            $table->integer('garansi_hari')->nullable();
            $table->integer('stok')->nullable();
            $table->timestamps();

            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
