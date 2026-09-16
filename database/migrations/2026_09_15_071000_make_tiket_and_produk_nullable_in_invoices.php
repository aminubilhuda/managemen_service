<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('tiket_id')->nullable()->change();
        });

        Schema::table('detail_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('produk_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('tiket_id')->nullable(false)->change();
        });

        Schema::table('detail_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('produk_id')->nullable(false)->change();
        });
    }
};
