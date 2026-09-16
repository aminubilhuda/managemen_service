<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiket_servis', function (Blueprint $table) {
            if (! Schema::hasColumn('tiket_servis', 'kelengkapan')) {
                $table->string('kelengkapan')->nullable()->after('imei_sn');
            }
            if (! Schema::hasColumn('tiket_servis', 'estimasi_biaya')) {
                $table->decimal('estimasi_biaya', 15, 2)->default(0)->nullable()->after('kondisi_awal');
            }
            if (! Schema::hasColumn('tiket_servis', 'estimasi_selesai')) {
                $table->date('estimasi_selesai')->nullable()->after('estimasi_biaya');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tiket_servis', function (Blueprint $table) {
            $cols = [];
            foreach (['kelengkapan', 'estimasi_biaya', 'estimasi_selesai'] as $col) {
                if (Schema::hasColumn('tiket_servis', $col)) {
                    $cols[] = $col;
                }
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
