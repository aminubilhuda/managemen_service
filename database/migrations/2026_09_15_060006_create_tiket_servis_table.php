<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_servis', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggan')->cascadeOnDelete();
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('perangkat');
            $table->string('imei_sn')->nullable();
            $table->string('kelengkapan')->nullable();
            $table->text('keluhan');
            $table->text('kondisi_awal')->nullable();
            $table->decimal('estimasi_biaya', 15, 2)->default(0)->nullable();
            $table->date('estimasi_selesai')->nullable();

            $table->enum('status', [
                'diterima',
                'dicek',
                'menunggu_sparepart',
                'dikerjakan',
                'selesai',
                'diambil',
                'batal',
            ])->default('diterima');
            $table->date('garansi_sampai')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('no_tiket');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_servis');
    }
};
