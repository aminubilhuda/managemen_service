<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->foreignId('tiket_id')->nullable()->constrained('tiket_servis')->nullOnDelete();
            $table->foreignId('perusahaan_id')->constrained('perusahaan')->cascadeOnDelete();

            $table->datetime('tanggal_invoice');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 15, 2)->default(0);
            $table->foreignId('pajak_id')->nullable()->constrained('pengaturan_pajak')->nullOnDelete();
            $table->decimal('pajak_persen', 5, 2)->default(0);
            $table->decimal('pajak_nominal', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'void'])->default('unpaid');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('no_invoice');
            $table->index('tanggal_invoice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
