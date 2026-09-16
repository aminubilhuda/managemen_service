<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoice')->cascadeOnDelete();
            $table->foreignId('produk_id')->nullable()->constrained('produk')->nullOnDelete();
            $table->string('deskripsi')->nullable();

            $table->integer('qty')->default(1);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('harga_modal_satuan', 15, 2)->default(0);
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_invoice');
    }
};
