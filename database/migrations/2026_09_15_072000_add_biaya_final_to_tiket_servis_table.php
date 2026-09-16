<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket_servis', function (Blueprint $table) {
            if (! Schema::hasColumn('tiket_servis', 'biaya_final')) {
                $table->decimal('biaya_final', 15, 2)->nullable()->after('estimasi_selesai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket_servis', function (Blueprint $table) {
            if (Schema::hasColumn('tiket_servis', 'biaya_final')) {
                $table->dropColumn('biaya_final');
            }
        });
    }
};
