<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_pajak', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->decimal('persentase', 5, 2)->default(0);
            $table->boolean('aktif')->default(true);
            $table->date('berlaku_mulai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pajak');
    }
};
