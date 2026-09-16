<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoice')->cascadeOnDelete();
            $table->decimal('jumlah_dibayar', 15, 2);
            $table->enum('metode_bayar', ['tunai', 'transfer', 'qris', 'kartu']);
            $table->datetime('tanggal_bayar');
            $table->foreignId('dicatat_oleh')->constrained('users')->cascadeOnDelete();
            $table->string('bukti_bayar')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
