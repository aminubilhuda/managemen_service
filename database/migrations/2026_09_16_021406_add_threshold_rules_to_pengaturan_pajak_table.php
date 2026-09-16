<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaturan_pajak', function (Blueprint $table) {
            $table->string('tipe_aturan', 30)->default('semua')->after('persentase');
            $table->decimal('nominal_batas', 15, 2)->default(0)->after('tipe_aturan');
        });

        // Set default dynamic rules for existing PPN & PPH if present
        DB::table('pengaturan_pajak')
            ->where('nama_pajak', 'like', '%PPN%')
            ->update([
                'tipe_aturan' => 'diatas_nominal',
                'nominal_batas' => 2000000,
                'aktif' => true,
            ]);

        DB::table('pengaturan_pajak')
            ->where('nama_pajak', 'like', '%PPH%')
            ->update([
                'tipe_aturan' => 'dibawah_nominal',
                'nominal_batas' => 2000000,
                'aktif' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_pajak', function (Blueprint $table) {
            $table->dropColumn(['tipe_aturan', 'nominal_batas']);
        });
    }
};
