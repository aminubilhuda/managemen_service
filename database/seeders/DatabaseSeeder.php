<?php

namespace Database\Seeders;

use App\Models\PengaturanPajak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // Permissions
        // ========================================
        $permissions = [
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'pelanggan.view',
            'pelanggan.create',
            'pelanggan.edit',
            'pelanggan.delete',
            'produk.view',
            'produk.create',
            'produk.edit',
            'produk.delete',
            'kategori_produk.view',
            'kategori_produk.create',
            'kategori_produk.edit',
            'kategori_produk.delete',
            'tiket.view',
            'tiket.create',
            'tiket.edit',
            'tiket.delete',
            'tiket.update_status',
            'dokumentasi.view',
            'dokumentasi.upload',
            'invoice.view',
            'invoice.create',
            'invoice.edit',
            'invoice.void',
            'invoice.cetak_pdf',
            'pembayaran.view',
            'pembayaran.create',
            'pengeluaran.view',
            'pengeluaran.create',
            'pengeluaran.edit',
            'pengeluaran.delete',
            'laporan.view',
            'laporan.export',
            'pengaturan.view',
            'pengaturan.edit',
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========================================
        // Roles & Permissions Assignment
        // ========================================
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $adminKasir = Role::firstOrCreate(['name' => 'admin_kasir']);
        $adminKasir->syncPermissions([
            'pelanggan.view',
            'pelanggan.create',
            'pelanggan.edit',
            'pelanggan.delete',
            'produk.view',
            'produk.create',
            'produk.edit',
            'kategori_produk.view',
            'kategori_produk.create',
            'kategori_produk.edit',
            'tiket.view',
            'tiket.create',
            'tiket.edit',
            'tiket.update_status',
            'dokumentasi.view',
            'dokumentasi.upload',
            'invoice.view',
            'invoice.create',
            'invoice.edit',
            'invoice.cetak_pdf',
            'pembayaran.view',
            'pembayaran.create',
            'pengeluaran.view',
            'pengeluaran.create',
            'laporan.view',
            'laporan.export',
            'dashboard.view',
        ]);

        $teknisiRole = Role::firstOrCreate(['name' => 'teknisi']);
        $teknisiRole->syncPermissions([
            'tiket.view',
            'tiket.update_status',
            'dokumentasi.view',
            'dokumentasi.upload',
            'produk.view',
            'dashboard.view',
        ]);

        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->syncPermissions([
            'pelanggan.view',
            'produk.view',
            'kategori_produk.view',
            'tiket.view',
            'dokumentasi.view',
            'invoice.view',
            'invoice.cetak_pdf',
            'pembayaran.view',
            'pengeluaran.view',
            'laporan.view',
            'laporan.export',
            'dashboard.view',
        ]);

        // ========================================
        // Staf Users
        // ========================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@wti.com'],
            ['name' => 'Wahyu Super Admin', 'username' => 'admin', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $admin->assignRole('super_admin');

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@wti.com'],
            ['name' => 'Siti Kasir & Front Office', 'username' => 'kasir', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $kasir->assignRole('admin_kasir');

        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@wti.com'],
            ['name' => 'Rian Master Teknisi', 'username' => 'teknisi', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $teknisi->assignRole('teknisi');

        $owner = User::firstOrCreate(
            ['email' => 'owner@wti.com'],
            ['name' => 'Bapak Wahyu (Owner)', 'username' => 'owner', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $owner->assignRole('owner');

        // ========================================
        // Pengaturan Pajak Dinamis
        // ========================================
        PengaturanPajak::firstOrCreate(
            ['nama_pajak' => 'PPN'],
            [
                'persentase' => 11.00,
                'tipe_aturan' => 'diatas_nominal',
                'nominal_batas' => 2000000.00,
                'aktif' => true,
                'berlaku_mulai' => now(),
            ]
        );

        PengaturanPajak::firstOrCreate(
            ['nama_pajak' => 'PPH'],
            [
                'persentase' => 2.00,
                'tipe_aturan' => 'dibawah_nominal',
                'nominal_batas' => 2000000.00,
                'aktif' => true,
                'berlaku_mulai' => now(),
            ]
        );
    }
}
