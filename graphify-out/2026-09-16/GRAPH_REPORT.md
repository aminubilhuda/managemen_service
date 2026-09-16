# Graph Report - /Users/godboy/Project/laravel/cv-wahyu  (2026-09-16)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 623 nodes · 957 edges · 118 communities (114 shown, 4 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 22 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Invoice
- User
- Illuminate\Http\Request
- Controller
- composer.json
- PengaturanPajak
- devDependencies
- InvoiceExport
- scripts
- Produk
- static
- LoginRequest
- command
- AppServiceProvider
- ExampleTest
- laravel-boost
- profile/edit.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 55 edges
2. `Controller` - 31 edges
3. `Invoice` - 30 edges
4. `TiketServis` - 29 edges
5. `PengaturanPajak` - 20 edges
6. `TestCase` - 20 edges
7. `Pengeluaran` - 18 edges
8. `Produk` - 16 edges
9. `KategoriProduk` - 15 edges
10. `UIFlowTest` - 15 edges

## Surprising Connections (you probably didn't know these)
- `DashboardController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/DashboardController.php → app/Http/Controllers/Controller.php
- `InvoiceController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/InvoiceController.php → app/Http/Controllers/Controller.php
- `KategoriProdukController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/KategoriProdukController.php → app/Http/Controllers/Controller.php
- `LaporanController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/LaporanController.php → app/Http/Controllers/Controller.php
- `PelangganController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/PelangganController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (118 total, 4 thin omitted)

### Community 0 - "Invoice"
Cohesion: 0.06
Nodes (11): InvoiceController, TiketServisController, DokumentasiUnit, Invoice, Pembayaran, RiwayatStatusTiket, TiketServis, Illuminate\Database\Eloquent\Model (+3 more)

### Community 1 - "User"
Cohesion: 0.06
Nodes (17): UserController, User, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Notifications\Notifiable, Spatie\Permission\Traits\HasRoles (+9 more)

### Community 2 - "Illuminate\Http\Request"
Cohesion: 0.07
Nodes (13): DashboardController, LaporanController, PelangganController, PengeluaranController, CheckPermission, DetailInvoice, Pelanggan, Pengeluaran (+5 more)

### Community 3 - "Controller"
Cohesion: 0.08
Nodes (17): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, PasswordController, PasswordResetLinkController, RegisteredUserController (+9 more)

### Community 4 - "composer.json"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 5 - "PengaturanPajak"
Cohesion: 0.07
Nodes (7): PengaturanPajakController, PerusahaanController, PengaturanPajak, Perusahaan, DatabaseSeeder, Illuminate\Database\Seeder, UIFlowTest

### Community 6 - "devDependencies"
Cohesion: 0.07
Nodes (28): alpinejs, autoprefixer, concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, alpinejs, autoprefixer (+20 more)

### Community 7 - "InvoiceExport"
Cohesion: 0.14
Nodes (11): InvoiceExport, LabaRugiExport, TiketExport, Illuminate\Support\Collection, Maatwebsite\Excel\Concerns\FromArray, Maatwebsite\Excel\Concerns\FromCollection, Maatwebsite\Excel\Concerns\WithHeadings, Maatwebsite\Excel\Concerns\WithMapping (+3 more)

### Community 8 - "scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 9 - "Produk"
Cohesion: 0.15
Nodes (4): KategoriProdukController, ProdukController, KategoriProduk, Produk

### Community 10 - "static"
Cohesion: 0.18
Nodes (5): PengaturanAplikasi, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, self, static

### Community 11 - "LoginRequest"
Cohesion: 0.27
Nodes (3): LoginRequest, ProfileUpdateRequest, Illuminate\Foundation\Http\FormRequest

### Community 12 - "command"
Cohesion: 0.20
Nodes (9): command, enabled, type, mcp, laravel-boost, $schema, artisan, boost:mcp (+1 more)

## Knowledge Gaps
- **75 isolated node(s):** `php`, `$schema`, `name`, `type`, `description` (+70 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **4 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User` to `Invoice`, `Illuminate\Http\Request`, `Controller`, `PengaturanPajak`, `static`, `LoginRequest`?**
  _High betweenness centrality (0.084) - this node is a cross-community bridge._
- **Why does `Controller` connect `Controller` to `Invoice`, `User`, `Illuminate\Http\Request`, `PengaturanPajak`, `Produk`?**
  _High betweenness centrality (0.034) - this node is a cross-community bridge._
- **Why does `Invoice` connect `Invoice` to `Illuminate\Http\Request`, `static`, `PengaturanPajak`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **What connects `php`, `$schema`, `name` to the rest of the system?**
  _75 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Invoice` be split into smaller, more focused modules?**
  _Cohesion score 0.05706760316066725 - nodes in this community are weakly interconnected._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.06229508196721312 - nodes in this community are weakly interconnected._
- **Should `Illuminate\Http\Request` be split into smaller, more focused modules?**
  _Cohesion score 0.07329462989840348 - nodes in this community are weakly interconnected._