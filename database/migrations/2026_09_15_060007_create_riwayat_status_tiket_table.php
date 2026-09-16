<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status_tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket_servis')->cascadeOnDelete();
            $table->string('status_sebelum')->nullable();
            $table->string('status_sesudah');
            $table->text('catatan')->nullable();
            $table->foreignId('diubah_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('tiket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_tiket');
    }
};
