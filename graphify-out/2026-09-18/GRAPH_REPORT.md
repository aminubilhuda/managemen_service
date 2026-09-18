# Graph Report - cv-wahyu  (2026-09-18)

## Corpus Check
- 347 files · ~237,798 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 1977 nodes · 3000 edges · 278 communities (238 shown, 40 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 78 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0ff3ae35`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- User
- Controller
- Pengeluaran
- composer.json
- Illuminate\Database\Eloquent\Model
- Invoice
- Pelanggan
- PengaturanPajak
- devDependencies
- scripts
- KategoriProduk
- LoginRequest
- command
- static
- ProfileController.php
- CheckPermission.php
- ExampleTest
- laravel-boost
- profile/edit.blade.php
- Colors.ts
- Project-Specific: Cekat Cell
- Illuminate\Http\JsonResponse
- TiketDetailScreen.tsx
- Cloud CLI
- Cloud CLI
- InvoiceCreateScreen.tsx
- Illuminate\Http\Request
- types/api.ts
- Laravel Boost Guidelines
- expo
- AppNavigator.tsx
- PRD — Aplikasi Manajemen Service Center "Cekat Cell"
- MasterScreen.tsx
- V1/TiketServisController.php
- StoreTiketRequest
- Produk
- dependencies
- Illuminate\Foundation\Testing\RefreshDatabase
- TiketServis
- CurrencyInput.tsx
- generate_docx_report.js
- Illuminate\Database\Eloquent\Relations\HasMany
- TestCase
- 6. Spesifikasi Rinci Setiap Layar & Alur Kerja (Screen-by-Screen)
- Detection Checklist
- Process
- Architecture Best Practices
- .agents/skills/laravel-best-practices/SKILL.md
- Security Best Practices
- Tailwind CSS Development
- Detection Checklist
- Process
- Architecture Best Practices
- Tailwind CSS Development
- Security Best Practices
- UIFlowTest
- Advanced Query Best Practices
- Events and Notifications Best Practices
- Migration Best Practices
- Queue and Job Best Practices
- UpdateTiketRequest
- Advanced Query Best Practices
- Events and Notifications Best Practices
- Migration Best Practices
- Queue and Job Best Practices
- require-dev
- mobile-app/package.json
- PRD (Product Requirement Document) — Aplikasi Mobile "Wahyu Teknik Indotama"
- MobileApiTest
- Caching Best Practices
- Database Performance Best Practices
- Eloquent Best Practices
- UploadDokumentasiRequest
- Caching Best Practices
- Database Performance Best Practices
- Eloquent Best Practices
- require
- devDependencies
- Blade and View Best Practices
- Error Handling Best Practices
- Task Scheduling Best Practices
- Endpoint Tests
- Blade and View Best Practices
- Error Handling Best Practices
- Task Scheduling Best Practices
- Endpoint Tests
- setup
- UIFlowTest.php
- README.md
- C. MODUL 3: TIKET SERVIS (`/api/v1/tiket`)
- AuthenticationTest
- Collection Best Practices
- HTTP Client Best Practices
- Mail Best Practices
- Routing and Controller Best Practices
- Convention and Style Best Practices
- Validation and Forms Best Practices
- Assertions
- .agents/skills/testing-best-practices/SKILL.md
- Fakes, Mocks, and Determinism
- Test Suite Performance
- Reviewing Tests
- keywords
- MODUL 5: KELOLA PELANGGAN
- Collection Best Practices
- HTTP Client Best Practices
- Mail Best Practices
- Routing and Controller Best Practices
- .claude/skills/laravel-best-practices/SKILL.md
- Convention and Style Best Practices
- Validation and Forms Best Practices
- Assertions
- .claude/skills/testing-best-practices/SKILL.md
- Fakes, Mocks, and Determinism
- Test Suite Performance
- Reviewing Tests
- config
- capture_all_screens.js
- capture_clean.js
- capture_master.js
- capture_screenshots.js
- Naming and Structure
- UpdateStatusRequest
- Naming and Structure
- 9. Desain Komponen UI Standar (Reusable Components)
- MODUL 3: TIKET SERVIS (ALUR UTAMA SERVIS HP)
- Dokumentasi REST API Cekat Cell (v1)
- G. MODUL 7: INVOICE KASIR & PEMBAYARAN (`/api/v1/invoice`)
- F. MODUL 6: PRODUK & SPAREPART (`/api/v1/produk`)
- E. MODUL 5: PELANGGAN (`/api/v1/pelanggan`)
- A. MODUL 1: AUTENTIKASI & AKUN (`/api/v1/auth`)
- 3. Daftar Endpoint Lengkap
- 2. Standar Struktur Respon JSON
- Factories and Test Data
- Testing Best Practices
- LoginRequest
- UpdatePasswordRequest
- UpdateProfileRequest
- .store
- StorePembayaranRequest
- StorePelangganRequest
- Illuminate\Foundation\Http\FormRequest
- StorePengeluaranRequest
- StoreProdukRequest
- UpdateProdukRequest
- Factories and Test Data
- Testing Best Practices
- psr-4
- MODUL 7: KASIR, INVOICE & PEMBAYARAN
- tsconfig.json
- I. MODUL 9: MASTER DATA & REFERENSI (`/api/v1/master`)
- EmailVerificationTest
- 10. Alur Pengujian & Checklist Kualitas (QA Acceptance Criteria)
- H. MODUL 8: PENGELUARAN TOKO / KAS KELUAR (`/api/v1/pengeluaran`)
- dev
- 1. Latar Belakang & Tujuan Produk
- 8. Arsitektur Komunikasi API & State Management
- test_card_click.js
- test_login_click.js
- axios
- MODUL 6: PRODUK, SPAREPART & JASA
- expo-file-system
- expo-image-picker
- expo-sharing
- @expo/vector-icons
- mobile-app/AGENTS.md
- react-dom
- react-native-safe-area-context
- react-native-screens
- @react-navigation/native
- @react-navigation/native-stack
- @tanstack/react-query
- zustand
- expo-image-manipulator

## God Nodes (most connected - your core abstractions)
1. `Controller` - 53 edges
2. `TiketServis` - 49 edges
3. `User` - 47 edges
4. `Invoice` - 41 edges
5. `Colors` - 32 edges
6. `Pelanggan` - 26 edges
7. `Produk` - 26 edges
8. `PengaturanPajak` - 24 edges
9. `Pengeluaran` - 22 edges
10. `TestCase` - 22 edges

## Surprising Connections (you probably didn't know these)
- `MobileApiTest` --references--> `User`  [EXTRACTED]
  tests/Feature/Api/V1/MobileApiTest.php → app/Models/User.php
- `AuthController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/AuthController.php → app/Http/Controllers/Controller.php
- `DashboardController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/DashboardController.php → app/Http/Controllers/Controller.php
- `DokumentasiUnitController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/DokumentasiUnitController.php → app/Http/Controllers/Controller.php
- `InvoiceController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Api/V1/InvoiceController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (278 total, 40 thin omitted)

### Community 0 - "User"
Cohesion: 0.14
Nodes (8): UserController, User, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens, Spatie\Permission\Traits\HasRoles, PasswordConfirmationTest

### Community 1 - "Controller"
Cohesion: 0.09
Nodes (16): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, PasswordController, PasswordResetLinkController, RegisteredUserController (+8 more)

### Community 2 - "Pengeluaran"
Cohesion: 0.05
Nodes (25): InvoiceExport, LabaRugiExport, TiketExport, DashboardController, LaporanController, PengeluaranController, PerusahaanController, DetailInvoice (+17 more)

### Community 3 - "composer.json"
Cohesion: 0.14
Nodes (13): autoload-dev, psr-4, description, extra, laravel, dont-discover, license, minimum-stability (+5 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.11
Nodes (4): DokumentasiUnit, RiwayatStatusTiket, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 5 - "Invoice"
Cohesion: 0.14
Nodes (3): InvoiceController, Invoice, Pembayaran

### Community 7 - "PengaturanPajak"
Cohesion: 0.17
Nodes (3): PengaturanPajakController, PengaturanPajak, self

### Community 8 - "devDependencies"
Cohesion: 0.07
Nodes (28): alpinejs, autoprefixer, concurrently, @laravel/multiplex, laravel-vite-plugin, devDependencies, alpinejs, autoprefixer (+20 more)

### Community 9 - "scripts"
Cohesion: 0.13
Nodes (15): scripts, post-autoload-dump, post-create-project-cmd, post-update-cmd, pre-package-uninstall, test, Illuminate\\Foundation\\ComposerScripts::postAutoloadDump, Illuminate\\Foundation\\ComposerScripts::prePackageUninstall (+7 more)

### Community 10 - "KategoriProduk"
Cohesion: 0.21
Nodes (3): KategoriProdukController, KategoriProdukController, KategoriProduk

### Community 12 - "command"
Cohesion: 0.17
Nodes (11): instructions, command, enabled, type, mcp, laravel-boost, $schema, artisan (+3 more)

### Community 13 - "static"
Cohesion: 0.21
Nodes (4): PengaturanAplikasi, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 15 - "CheckPermission.php"
Cohesion: 0.60
Nodes (3): CheckPermission, Closure, Symfony\Component\HttpFoundation\Response

### Community 120 - "Colors.ts"
Cohesion: 0.10
Nodes (29): Button(), ButtonProps, styles, EmptyStateProps, styles, Input(), InputProps, styles (+21 more)

### Community 121 - "Project-Specific: Cekat Cell"
Cohesion: 0.05
Nodes (38): APIs & Eloquent Resources, Application Structure & Architecture, Architecture Notes, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files (+30 more)

### Community 122 - "Illuminate\Http\JsonResponse"
Cohesion: 0.10
Nodes (12): AuthController, DokumentasiUnitController, InvoiceController, MasterController, PembayaranController, PengeluaranController, InvoiceResource, errorResponse() (+4 more)

### Community 123 - "TiketDetailScreen.tsx"
Cohesion: 0.10
Nodes (26): SearchBar(), SearchBarProps, styles, StatusBadge(), StatusBadgeProps, styles, DOK_TYPES, DokumentasiUploadModal() (+18 more)

### Community 124 - "Cloud CLI"
Cohesion: 0.06
Nodes (30): Adding a cache to an existing environment, Adding a database to an existing environment, Checklists for Multi-Step Operations, Custom domain setup, Full environment setup (app + database + cache + domain), New app from scratch, Application Setup, Billing and Usage (+22 more)

### Community 125 - "Cloud CLI"
Cohesion: 0.06
Nodes (30): Adding a cache to an existing environment, Adding a database to an existing environment, Checklists for Multi-Step Operations, Custom domain setup, Full environment setup (app + database + cache + domain), New app from scratch, Application Setup, Billing and Usage (+22 more)

### Community 126 - "InvoiceCreateScreen.tsx"
Cohesion: 0.08
Nodes (27): plugins, react, InvoiceCreateScreen(), InvoiceCreateScreenProps, ItemRow, styles, InvoiceDetailScreen(), InvoiceDetailScreenProps (+19 more)

### Community 127 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (11): DetailInvoiceResource, DokumentasiUnitResource, KategoriProdukResource, PelangganResource, PembayaranResource, PengeluaranResource, ProdukResource, RiwayatStatusTiketResource (+3 more)

### Community 128 - "types/api.ts"
Cohesion: 0.14
Nodes (22): api, DEFAULT_BASE_URL, setOnUnauthorizedCallback(), storage, PengeluaranListParams, pengeluaranService, ProdukListParams, AuthState (+14 more)

### Community 129 - "Laravel Boost Guidelines"
Cohesion: 0.07
Nodes (28): APIs & Eloquent Resources, Application Structure & Architecture, Artisan, Conventions, Deployment, Do Things the Laravel Way, Documentation Files, Foundational Context (+20 more)

### Community 130 - "expo"
Cohesion: 0.07
Nodes (27): backgroundColor, backgroundImage, foregroundImage, monochromeImage, adaptiveIcon, package, permissions, predictiveBackGestureEnabled (+19 more)

### Community 131 - "AppNavigator.tsx"
Cohesion: 0.12
Nodes (21): App(), queryClient, Card(), CardProps, styles, formatRupiah(), AppNavigator(), navigationRef (+13 more)

### Community 132 - "PRD — Aplikasi Manajemen Service Center "Cekat Cell""
Cohesion: 0.09
Nodes (22): 10. Kriteria Selesai (Definition of Done), 1. Latar Belakang & Tujuan, 2. Tech Stack, 3. Aktor & Role Pengguna, 4.1 Tabel Inti (dari normalisasi sebelumnya), 4.2 Tabel Tambahan, 4. Skema Database (ERD Naratif), 5.1 Autentikasi & Manajemen User (+14 more)

### Community 133 - "MasterScreen.tsx"
Cohesion: 0.16
Nodes (16): Header(), HeaderProps, styles, MasterScreenProps, styles, PelangganDetailScreenProps, styles, PelangganFormModal() (+8 more)

### Community 134 - "V1/TiketServisController.php"
Cohesion: 0.16
Nodes (3): DashboardController, PelangganController, TiketServisResource

### Community 136 - "Produk"
Cohesion: 0.22
Nodes (3): ProdukController, ProdukController, Produk

### Community 137 - "dependencies"
Cohesion: 0.13
Nodes (15): expo, expo-camera, expo-secure-store, expo-status-bar, dependencies, expo, expo-camera, expo-secure-store (+7 more)

### Community 138 - "Illuminate\Foundation\Testing\RefreshDatabase"
Cohesion: 0.16
Nodes (4): Illuminate\Foundation\Testing\RefreshDatabase, PasswordResetTest, PasswordUpdateTest, RegistrationTest

### Community 139 - "TiketServis"
Cohesion: 0.20
Nodes (3): TiketServisController, TiketServisController, TiketServis

### Community 140 - "CurrencyInput.tsx"
Cohesion: 0.18
Nodes (11): CurrencyInput(), CurrencyInputProps, styles, EmptyState(), KATEGORI_OPTIONS, PengeluaranCreateModal(), PengeluaranCreateModalProps, styles (+3 more)

### Community 141 - "generate_docx_report.js"
Cohesion: 0.21
Nodes (13): buildTable(), createBullet(), createHeading(), createImageParagraph(), createParagraph(), { 
  Document, 
  Packer, 
  Paragraph, 
  TextRun, 
  HeadingLevel, 
  ImageRun, 
  Table, 
  TableRow, 
  TableCell, 
  AlignmentType, 
  WidthType, 
  BorderStyle,
  Header,
  Footer,
  PageNumber
}, fs, generate() (+5 more)

### Community 142 - "Illuminate\Database\Eloquent\Relations\HasMany"
Cohesion: 0.17
Nodes (3): Illuminate\Database\Eloquent\Builder, Illuminate\Database\Eloquent\Relations\HasMany, Illuminate\Database\Eloquent\Relations\HasOne

### Community 143 - "TestCase"
Cohesion: 0.18
Nodes (4): Illuminate\Foundation\Testing\TestCase, ExampleTest, ProfileTest, TestCase

### Community 144 - "6. Spesifikasi Rinci Setiap Layar & Alur Kerja (Screen-by-Screen)"
Cohesion: 0.18
Nodes (11): 6. Spesifikasi Rinci Setiap Layar & Alur Kerja (Screen-by-Screen), MODUL 1: AUTENTIKASI & SETUP, MODUL 2: BERANDA / DASHBOARD, MODUL 4: DOKUMENTASI FOTO UNIT, MODUL 8: PENGELUARAN TOKO (KAS KELUAR), S-01: Layar Pengaturan Server IP / Base URL, S-02: Layar Login, S-03: Layar Profil & Ganti Kata Sandi (+3 more)

### Community 145 - "Detection Checklist"
Cohesion: 0.17
Nodes (11): A. Validation & HTTP input, B. Controllers & routing, C. Authorization, D. Eloquent & models, Detection Checklist, E. Architecture & organization, F. Frontend & views, G. Database & migrations (+3 more)

### Community 146 - "Process"
Cohesion: 0.17
Nodes (11): Edge cases, Glob mapping, Ground Rules (read before you start), Infer Conventions, Process, Step 0: Orient, Step 1: Predefined sweep, Step 2: Open-ended pass (+3 more)

### Community 147 - "Architecture Best Practices"
Cohesion: 0.17
Nodes (11): Architecture Best Practices, Depend on Contracts at Boundaries, Extract Focused Business Operations, Follow Framework Conventions, Inject Required Dependencies, Specify a Deterministic Sort Order, Use Atomic Locks for Race Conditions, Use `Concurrency::run()` for Parallel Execution (+3 more)

### Community 148 - ".agents/skills/laravel-best-practices/SKILL.md"
Cohesion: 0.17
Nodes (10): Configuration Best Practices, Name Repeated Domain Values, Protect Production Secrets, Read Environment Variables in Configuration Files, Use `App::environment()` for Environment Checks, Consistency First, Decision Rules, How to Apply (+2 more)

### Community 149 - "Security Best Practices"
Cohesion: 0.17
Nodes (11): Apply Cross-Site Request Forgery Protection, Audit Dependencies, Authorize Protected Actions, Bind Query Parameters, Control Mass Assignment, Encrypt Sensitive Attributes When Appropriate, Escape Output in Its Context, Keep Secrets Out of Application Code (+3 more)

### Community 150 - "Tailwind CSS Development"
Cohesion: 0.17
Nodes (11): Basic Usage, Common Patterns, Common Pitfalls, Dark Mode, Documentation, Flexbox Layout, Grid Layout, Spacing (+3 more)

### Community 151 - "Detection Checklist"
Cohesion: 0.17
Nodes (11): A. Validation & HTTP input, B. Controllers & routing, C. Authorization, D. Eloquent & models, Detection Checklist, E. Architecture & organization, F. Frontend & views, G. Database & migrations (+3 more)

### Community 152 - "Process"
Cohesion: 0.17
Nodes (11): Edge cases, Glob mapping, Ground Rules (read before you start), Infer Conventions, Process, Step 0: Orient, Step 1: Predefined sweep, Step 2: Open-ended pass (+3 more)

### Community 153 - "Architecture Best Practices"
Cohesion: 0.17
Nodes (11): Architecture Best Practices, Depend on Contracts at Boundaries, Extract Focused Business Operations, Follow Framework Conventions, Inject Required Dependencies, Specify a Deterministic Sort Order, Use Atomic Locks for Race Conditions, Use `Concurrency::run()` for Parallel Execution (+3 more)

### Community 154 - "Tailwind CSS Development"
Cohesion: 0.17
Nodes (11): Basic Usage, Common Patterns, Common Pitfalls, Dark Mode, Documentation, Flexbox Layout, Grid Layout, Spacing (+3 more)

### Community 155 - "Security Best Practices"
Cohesion: 0.17
Nodes (11): Apply Cross-Site Request Forgery Protection, Audit Dependencies, Authorize Protected Actions, Bind Query Parameters, Control Mass Assignment, Encrypt Sensitive Attributes When Appropriate, Escape Output in Its Context, Keep Secrets Out of Application Code (+3 more)

### Community 157 - "Advanced Query Best Practices"
Cohesion: 0.20
Nodes (9): Advanced Query Best Practices, Combine Related Counts with Conditional Aggregates, Compare `whereHas()` with an `IN` Subquery, Consider a Correlated Subquery for Has-Many Ordering, Create Dynamic Relationships with a Subquery Foreign Key, Design Composite Indexes for the Query, Measure Two Simple Queries Against One Complex Query, Reuse Loaded Parent Models with `setRelation()` (+1 more)

### Community 158 - "Events and Notifications Best Practices"
Cohesion: 0.20
Nodes (9): Cache Event Discovery During Production Deployment, Dispatch Queued Notifications After Commit, Events and Notifications Best Practices, Implement `HasLocalePreference` on Notifiable Models, Queue Slow Notifications, Rely on Event Discovery, Route Notification Channels to Dedicated Queues, Use On-Demand Notifications for Non-User Recipients (+1 more)

### Community 159 - "Migration Best Practices"
Cohesion: 0.20
Nodes (9): Define Foreign-Key Constraints Deliberately, Design Indexes for Real Queries, Generate Migrations with Artisan, Keep Migrations Focused, Make Rollbacks Honest, Migration Best Practices, Mirror Defaults Only When Unsaved Models Need Them, Stage Changes That Affect Existing Rows (+1 more)

### Community 160 - "Queue and Job Best Practices"
Cohesion: 0.20
Nodes (9): Back Off Transient Failures, Batch Jobs for Group Coordination, Configure Time-Based Retry Limits Deliberately, Handle Terminal Failure When Needed, Keep Reservation Time Longer Than Execution Time, Queue and Job Best Practices, Rate Limit External Calls, Use Horizon for Redis Queue Operations (+1 more)

### Community 162 - "Advanced Query Best Practices"
Cohesion: 0.20
Nodes (9): Advanced Query Best Practices, Combine Related Counts with Conditional Aggregates, Compare `whereHas()` with an `IN` Subquery, Consider a Correlated Subquery for Has-Many Ordering, Create Dynamic Relationships with a Subquery Foreign Key, Design Composite Indexes for the Query, Measure Two Simple Queries Against One Complex Query, Reuse Loaded Parent Models with `setRelation()` (+1 more)

### Community 163 - "Events and Notifications Best Practices"
Cohesion: 0.20
Nodes (9): Cache Event Discovery During Production Deployment, Dispatch Queued Notifications After Commit, Events and Notifications Best Practices, Implement `HasLocalePreference` on Notifiable Models, Queue Slow Notifications, Rely on Event Discovery, Route Notification Channels to Dedicated Queues, Use On-Demand Notifications for Non-User Recipients (+1 more)

### Community 164 - "Migration Best Practices"
Cohesion: 0.20
Nodes (9): Define Foreign-Key Constraints Deliberately, Design Indexes for Real Queries, Generate Migrations with Artisan, Keep Migrations Focused, Make Rollbacks Honest, Migration Best Practices, Mirror Defaults Only When Unsaved Models Need Them, Stage Changes That Affect Existing Rows (+1 more)

### Community 165 - "Queue and Job Best Practices"
Cohesion: 0.20
Nodes (9): Back Off Transient Failures, Batch Jobs for Group Coordination, Configure Time-Based Retry Limits Deliberately, Handle Terminal Failure When Needed, Keep Reservation Time Longer Than Execution Time, Queue and Job Best Practices, Rate Limit External Calls, Use Horizon for Redis Queue Operations (+1 more)

### Community 166 - "require-dev"
Cohesion: 0.20
Nodes (10): require-dev, fakerphp/faker, laravel/boost, laravel/breeze, laravel/pail, laravel/pao, laravel/pint, mockery/mockery (+2 more)

### Community 167 - "mobile-app/package.json"
Cohesion: 0.20
Nodes (9): main, name, private, scripts, android, ios, start, web (+1 more)

### Community 168 - "PRD (Product Requirement Document) — Aplikasi Mobile "Wahyu Teknik Indotama""
Cohesion: 0.20
Nodes (9): 11. Panduan Memulai Implementasi (Quickstart Prompt untuk Developer / AI), 2. Tech Stack & Arsitektur Mobile, 3.1 Palet Warna Standar (Color Tokens), 3.2 Tipografi & Spacing, 3. Sistem Desain UI & Standar Warna, 4. Struktur Navigasi Aplikasi, 5. Matriks Peran & Izin Pengguna (Role Matrix), 7. Model Data & TypeScript Interfaces (+1 more)

### Community 170 - "Caching Best Practices"
Cohesion: 0.22
Nodes (8): Caching Best Practices, Configure Failover Cache Stores in Production, Consider `Cache::flexible()` for Stale-While-Revalidate, Use `Cache::add()` for Atomic Conditional Writes, Use `Cache::memo()` to Avoid Redundant Hits Within an Execution, Use `Cache::remember()` for Cache-Aside Reads, Use Cache Tags to Invalidate Related Groups, Use `once()` for In-Process Memoization

### Community 171 - "Database Performance Best Practices"
Cohesion: 0.22
Nodes (8): Add Indexes for Measured Query Patterns, Count Relationships Without Loading Them, Database Performance Best Practices, Eager Load Relationships Before Iterating, Keep Queries Out of Blade Templates, Prevent Lazy Loading in Development, Process Large Data Sets Incrementally, Select Only Needed Columns

### Community 172 - "Eloquent Best Practices"
Cohesion: 0.22
Nodes (8): Apply Global Scopes Sparingly, Cast Date and Time Attributes, Define Attribute Casts, Define Precise Relationship Types, Eloquent Best Practices, Keep Application Queries Model-Aware, Use Local Scopes for Reusable Queries, Use `whereBelongsTo()` for Relationship Queries

### Community 174 - "Caching Best Practices"
Cohesion: 0.22
Nodes (8): Caching Best Practices, Configure Failover Cache Stores in Production, Consider `Cache::flexible()` for Stale-While-Revalidate, Use `Cache::add()` for Atomic Conditional Writes, Use `Cache::memo()` to Avoid Redundant Hits Within an Execution, Use `Cache::remember()` for Cache-Aside Reads, Use Cache Tags to Invalidate Related Groups, Use `once()` for In-Process Memoization

### Community 175 - "Database Performance Best Practices"
Cohesion: 0.22
Nodes (8): Add Indexes for Measured Query Patterns, Count Relationships Without Loading Them, Database Performance Best Practices, Eager Load Relationships Before Iterating, Keep Queries Out of Blade Templates, Prevent Lazy Loading in Development, Process Large Data Sets Incrementally, Select Only Needed Columns

### Community 176 - "Eloquent Best Practices"
Cohesion: 0.22
Nodes (8): Apply Global Scopes Sparingly, Cast Date and Time Attributes, Define Attribute Casts, Define Precise Relationship Types, Eloquent Best Practices, Keep Application Queries Model-Aware, Use Local Scopes for Reusable Queries, Use `whereBelongsTo()` for Relationship Queries

### Community 177 - "require"
Cohesion: 0.22
Nodes (9): require, barryvdh/laravel-dompdf, laravel/framework, laravel/sanctum, laravel/tinker, maatwebsite/excel, php, spatie/laravel-activitylog (+1 more)

### Community 178 - "devDependencies"
Cohesion: 0.22
Nodes (9): docx, devDependencies, docx, puppeteer-core, @types/react, typescript, puppeteer-core, @types/react (+1 more)

### Community 179 - "Blade and View Best Practices"
Cohesion: 0.25
Nodes (7): Blade and View Best Practices, Prefer Components for Explicit Interfaces, Return Blade Fragments for Partial Rendering, Share Compatible View Data with a View Composer, Share Parent Component Props with `@aware`, Use `$attributes->merge()` in Component Templates, Use `@pushOnce` for Per-Component Scripts

### Community 180 - "Error Handling Best Practices"
Cohesion: 0.25
Nodes (7): Add Context to Exception Classes, Choose Where to Report and Render Exceptions, Define JSON Rendering for API Routes, Error Handling Best Practices, Mark Exceptions the Handler Should Not Report, Prevent Duplicate Reports of One Exception Instance, Throttle High-Volume Exception Reports

### Community 181 - "Task Scheduling Best Practices"
Cohesion: 0.25
Nodes (7): Bound Work Inside the Task, Group Shared Configuration, Prevent Unwanted Overlap, Restrict Tasks by Environment, Run a Task on One Server, Run Eligible Commands in the Background, Task Scheduling Best Practices

### Community 182 - "Endpoint Tests"
Cohesion: 0.25
Nodes (7): Endpoint Coverage, Endpoint Tests, How to Write the Test, Tenant Isolation, Test Authorization at the Policy Level, Testing Validation, Which Layer Owns Which Case

### Community 183 - "Blade and View Best Practices"
Cohesion: 0.25
Nodes (7): Blade and View Best Practices, Prefer Components for Explicit Interfaces, Return Blade Fragments for Partial Rendering, Share Compatible View Data with a View Composer, Share Parent Component Props with `@aware`, Use `$attributes->merge()` in Component Templates, Use `@pushOnce` for Per-Component Scripts

### Community 184 - "Error Handling Best Practices"
Cohesion: 0.25
Nodes (7): Add Context to Exception Classes, Choose Where to Report and Render Exceptions, Define JSON Rendering for API Routes, Error Handling Best Practices, Mark Exceptions the Handler Should Not Report, Prevent Duplicate Reports of One Exception Instance, Throttle High-Volume Exception Reports

### Community 185 - "Task Scheduling Best Practices"
Cohesion: 0.25
Nodes (7): Bound Work Inside the Task, Group Shared Configuration, Prevent Unwanted Overlap, Restrict Tasks by Environment, Run a Task on One Server, Run Eligible Commands in the Background, Task Scheduling Best Practices

### Community 186 - "Endpoint Tests"
Cohesion: 0.25
Nodes (7): Endpoint Coverage, Endpoint Tests, How to Write the Test, Tenant Isolation, Test Authorization at the Policy Level, Testing Validation, Which Layer Owns Which Case

### Community 187 - "setup"
Cohesion: 0.25
Nodes (8): post-root-package-install, setup, composer install, npm install --ignore-scripts, npm run build, @php artisan key:generate, @php artisan migrate --force, @php -r \"file_exists('.env') || copy('.env.example', '.env');\

### Community 189 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 190 - "C. MODUL 3: TIKET SERVIS (`/api/v1/tiket`)"
Cohesion: 0.25
Nodes (8): 1. Daftar Tiket Servis (List), 2. Buat Tiket Servis Baru (Intake), 3. Detail Tiket Servis, 4. Update Informasi Tiket, 5. Quick Update Status Tiket (Mobile Teknisi), 6. Scan QR / Barcode / IMEI (Kamera HP), 7. Tanda Terima Cetak Penerimaan Unit, C. MODUL 3: TIKET SERVIS (`/api/v1/tiket`)

### Community 192 - "Collection Best Practices"
Cohesion: 0.29
Nodes (6): Choose Between `cursor()` and `lazy()`, Collection Best Practices, Use `#[CollectedBy]` for Custom Collection Classes, Use Higher-Order Messages for Simple Operations, Use `lazyById()` When Updating Records While Iterating, Use `toQuery()` for Bulk Operations on Collections

### Community 193 - "HTTP Client Best Practices"
Cohesion: 0.29
Nodes (6): Fake HTTP Requests in Tests, Handle Errors Explicitly, HTTP Client Best Practices, Pool Independent Requests, Retry Only Safe Operations, Set Explicit Timeouts

### Community 194 - "Mail Best Practices"
Cohesion: 0.29
Nodes (6): Assert the Delivery Mode, Dispatch Queued Mail After Commit, Mail Best Practices, Queue Slow Mail Delivery, Separate Content and Delivery Tests, Use Markdown Mailables When They Fit

### Community 195 - "Routing and Controller Best Practices"
Cohesion: 0.29
Nodes (6): Keep Controllers Focused on HTTP Concerns, Organize Controllers Around Resources, Routing and Controller Best Practices, Scope Nested Bindings, Use Implicit Route Model Binding, Use Resource Routes for Resourceful Actions

### Community 196 - "Convention and Style Best Practices"
Cohesion: 0.29
Nodes (6): Convention and Style Best Practices, Follow Project Naming Conventions, Keep Presentation Code Maintainable, Prefer Clear, Idiomatic Syntax, Use Utilities When They Clarify Intent, Write Comments That Explain Why

### Community 197 - "Validation and Forms Best Practices"
Cohesion: 0.29
Nodes (6): Add Cross-Field Validation After Base Rules, Express Conditional Rules Clearly, Extract Validation When It Improves the Boundary, Prefer Readable Rule Syntax, Use Only Intended Validated Data, Validation and Forms Best Practices

### Community 198 - "Assertions"
Cohesion: 0.29
Nodes (6): Arrange, Act, Assert, Assert a Known Value, Assert the Complete Result, Assertions, How to Find the Correct Assertion, Named Response Assertions

### Community 199 - ".agents/skills/testing-best-practices/SKILL.md"
Cohesion: 0.29
Nodes (3): Built-in Laravel Assertion Methods, How to Find Test Framework Features, Security Tests

### Community 200 - "Fakes, Mocks, and Determinism"
Cohesion: 0.29
Nodes (7): Database, Fakes, Mocks, and Determinism, Framework Fakes, How to Isolate a Dependency, Mocking, Outbound HTTP Testing, Time and Randomness

### Community 201 - "Test Suite Performance"
Cohesion: 0.29
Nodes (6): Common Errors, Global Fakes, How to Find a Slow Test, How to Run the Suite in Parallel, Test Environment, Test Suite Performance

### Community 202 - "Reviewing Tests"
Cohesion: 0.29
Nodes (6): Assertions, Coverage, Data and Determinism, Names and Structure, Reviewing Tests, Test Value

### Community 203 - "keywords"
Cohesion: 0.67
Nodes (3): keywords, framework, laravel

### Community 204 - "MODUL 5: KELOLA PELANGGAN"
Cohesion: 0.67
Nodes (3): MODUL 5: KELOLA PELANGGAN, S-11: Layar Daftar Pelanggan, S-12: Layar Detail Pelanggan & Riwayat Servis

### Community 205 - "Collection Best Practices"
Cohesion: 0.29
Nodes (6): Choose Between `cursor()` and `lazy()`, Collection Best Practices, Use `#[CollectedBy]` for Custom Collection Classes, Use Higher-Order Messages for Simple Operations, Use `lazyById()` When Updating Records While Iterating, Use `toQuery()` for Bulk Operations on Collections

### Community 206 - "HTTP Client Best Practices"
Cohesion: 0.29
Nodes (6): Fake HTTP Requests in Tests, Handle Errors Explicitly, HTTP Client Best Practices, Pool Independent Requests, Retry Only Safe Operations, Set Explicit Timeouts

### Community 207 - "Mail Best Practices"
Cohesion: 0.29
Nodes (6): Assert the Delivery Mode, Dispatch Queued Mail After Commit, Mail Best Practices, Queue Slow Mail Delivery, Separate Content and Delivery Tests, Use Markdown Mailables When They Fit

### Community 208 - "Routing and Controller Best Practices"
Cohesion: 0.29
Nodes (6): Keep Controllers Focused on HTTP Concerns, Organize Controllers Around Resources, Routing and Controller Best Practices, Scope Nested Bindings, Use Implicit Route Model Binding, Use Resource Routes for Resourceful Actions

### Community 209 - ".claude/skills/laravel-best-practices/SKILL.md"
Cohesion: 0.17
Nodes (10): Configuration Best Practices, Name Repeated Domain Values, Protect Production Secrets, Read Environment Variables in Configuration Files, Use `App::environment()` for Environment Checks, Consistency First, Decision Rules, How to Apply (+2 more)

### Community 210 - "Convention and Style Best Practices"
Cohesion: 0.29
Nodes (6): Convention and Style Best Practices, Follow Project Naming Conventions, Keep Presentation Code Maintainable, Prefer Clear, Idiomatic Syntax, Use Utilities When They Clarify Intent, Write Comments That Explain Why

### Community 211 - "Validation and Forms Best Practices"
Cohesion: 0.29
Nodes (6): Add Cross-Field Validation After Base Rules, Express Conditional Rules Clearly, Extract Validation When It Improves the Boundary, Prefer Readable Rule Syntax, Use Only Intended Validated Data, Validation and Forms Best Practices

### Community 212 - "Assertions"
Cohesion: 0.29
Nodes (6): Arrange, Act, Assert, Assert a Known Value, Assert the Complete Result, Assertions, How to Find the Correct Assertion, Named Response Assertions

### Community 213 - ".claude/skills/testing-best-practices/SKILL.md"
Cohesion: 0.29
Nodes (3): Built-in Laravel Assertion Methods, How to Find Test Framework Features, Security Tests

### Community 214 - "Fakes, Mocks, and Determinism"
Cohesion: 0.29
Nodes (7): Database, Fakes, Mocks, and Determinism, Framework Fakes, How to Isolate a Dependency, Mocking, Outbound HTTP Testing, Time and Randomness

### Community 215 - "Test Suite Performance"
Cohesion: 0.29
Nodes (6): Common Errors, Global Fakes, How to Find a Slow Test, How to Run the Suite in Parallel, Test Environment, Test Suite Performance

### Community 216 - "Reviewing Tests"
Cohesion: 0.29
Nodes (6): Assertions, Coverage, Data and Determinism, Names and Structure, Reviewing Tests, Test Value

### Community 217 - "config"
Cohesion: 0.29
Nodes (7): pestphp/pest-plugin, php-http/discovery, config, allow-plugins, optimize-autoloader, preferred-install, sort-packages

### Community 218 - "capture_all_screens.js"
Cohesion: 0.33
Nodes (6): fs, path, puppeteer, run(), SCREENSHOT_DIR, sleep()

### Community 219 - "capture_clean.js"
Cohesion: 0.33
Nodes (6): capture(), fs, path, puppeteer, SCREENSHOT_DIR, sleep()

### Community 220 - "capture_master.js"
Cohesion: 0.33
Nodes (6): fs, path, puppeteer, run(), SCREENSHOT_DIR, sleep()

### Community 221 - "capture_screenshots.js"
Cohesion: 0.33
Nodes (6): fs, path, puppeteer, run(), SCREENSHOT_DIR, sleep()

### Community 222 - "Naming and Structure"
Cohesion: 0.33
Nodes (5): File Layout, Grouping, Naming and Structure, Naming Tests, Test Class and Methods

### Community 225 - "Naming and Structure"
Cohesion: 0.33
Nodes (5): File Layout, Grouping, Naming and Structure, Naming Tests, Test Class and Methods

### Community 226 - "9. Desain Komponen UI Standar (Reusable Components)"
Cohesion: 0.33
Nodes (6): 9.1 `StatusBadge`, 9.2 `SearchBar`, 9.3 `TiketCard`, 9.4 `CurrencyInput`, 9.5 `EmptyState`, 9. Desain Komponen UI Standar (Reusable Components)

### Community 227 - "MODUL 3: TIKET SERVIS (ALUR UTAMA SERVIS HP)"
Cohesion: 0.33
Nodes (6): MODUL 3: TIKET SERVIS (ALUR UTAMA SERVIS HP), S-05: Layar Daftar Tiket Servis, S-06: Layar Penerimaan Servis Baru (Intake Form), S-07: Layar Detail Tiket Servis, S-08: Layar Scan Barcode / QR / IMEI (Kamera), S-09: Layar & Aksi Cetak / Bagikan Tanda Terima Servis

### Community 228 - "Dokumentasi REST API Cekat Cell (v1)"
Cohesion: 0.33
Nodes (5): 1. Konfigurasi Dasar & Header, 4. Tips Integrasi Aplikasi Mobile, Base URL, Dokumentasi REST API Cekat Cell (v1), Header Wajib

### Community 229 - "G. MODUL 7: INVOICE KASIR & PEMBAYARAN (`/api/v1/invoice`)"
Cohesion: 0.33
Nodes (6): 1. List Invoice, 2. Buat Invoice Baru (Billing Kasir), 3. Detail Invoice, 4. Catat Pembayaran Kasir, 5. Batalkan Invoice (Void), G. MODUL 7: INVOICE KASIR & PEMBAYARAN (`/api/v1/invoice`)

### Community 230 - "F. MODUL 6: PRODUK & SPAREPART (`/api/v1/produk`)"
Cohesion: 0.33
Nodes (6): 1. List Kategori Produk, 2. List Produk / Sparepart, 3. Pencarian Cepat Produk / Barcode Scanner, 4. Tambah Produk / Sparepart, 5. Update Produk, F. MODUL 6: PRODUK & SPAREPART (`/api/v1/produk`)

### Community 231 - "E. MODUL 5: PELANGGAN (`/api/v1/pelanggan`)"
Cohesion: 0.33
Nodes (6): 1. List Pelanggan, 2. Autocomplete Pencarian Pelanggan (Mobile Quick Lookup), 3. Tambah Pelanggan, 4. Detail Pelanggan & Riwayat Servis, 5. Update Pelanggan, E. MODUL 5: PELANGGAN (`/api/v1/pelanggan`)

### Community 232 - "A. MODUL 1: AUTENTIKASI & AKUN (`/api/v1/auth`)"
Cohesion: 0.33
Nodes (6): 1. Login Akun Mobile, 2. Profil User Login Saat Ini, 3. Update Profil Pengguna, 4. Ganti Kata Sandi, 5. Logout Akun Mobile, A. MODUL 1: AUTENTIKASI & AKUN (`/api/v1/auth`)

### Community 233 - "3. Daftar Endpoint Lengkap"
Cohesion: 0.33
Nodes (6): 1. Ringkasan Statistik & Dashboard, 1. Upload Foto Dokumentasi Kamera, 2. Hapus Foto Dokumentasi, 3. Daftar Endpoint Lengkap, B. MODUL 2: DASHBOARD MOBILE (`/api/v1/dashboard`), D. MODUL 4: DOKUMENTASI FOTO FISIK UNIT (`/api/v1/tiket/{id}/dokumentasi`)

### Community 234 - "2. Standar Struktur Respon JSON"
Cohesion: 0.33
Nodes (6): 2. Standar Struktur Respon JSON, A. Respon Sukses (Single Object / Action) — HTTP 200 / 201, B. Respon Sukses dengan Paginasi — HTTP 200, C. Respon Validasi Gagal — HTTP 422, D. Respon Tidak Diizinkan / Unauthenticated — HTTP 401, E. Respon Data Tidak Ditemukan — HTTP 404

### Community 235 - "Factories and Test Data"
Cohesion: 0.40
Nodes (4): Data Providers, Each Test Makes Its Own Data, Factories and Test Data, Record Construction

### Community 236 - "Testing Best Practices"
Cohesion: 0.40
Nodes (5): Consistency First, How to Apply, Rule Index, Testing Best Practices, What to Test

### Community 243 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.28
Nodes (3): UpdatePelangganRequest, ProfileUpdateRequest, Illuminate\Foundation\Http\FormRequest

### Community 247 - "Factories and Test Data"
Cohesion: 0.40
Nodes (4): Data Providers, Each Test Makes Its Own Data, Factories and Test Data, Record Construction

### Community 248 - "Testing Best Practices"
Cohesion: 0.40
Nodes (5): Consistency First, How to Apply, Rule Index, Testing Best Practices, What to Test

### Community 249 - "psr-4"
Cohesion: 0.40
Nodes (5): autoload, psr-4, App\\, Database\\Factories\\, Database\\Seeders\\

### Community 250 - "MODUL 7: KASIR, INVOICE & PEMBAYARAN"
Cohesion: 0.40
Nodes (5): MODUL 7: KASIR, INVOICE & PEMBAYARAN, S-15: Layar Daftar Invoice, S-16: Layar Buat Invoice Baru (Billing Kasir), S-17: Layar Detail Invoice & Kasir, S-18: Modal Input Pembayaran Kasir

### Community 251 - "tsconfig.json"
Cohesion: 0.40
Nodes (4): compilerOptions, strict, extends, expo/tsconfig.base

### Community 252 - "I. MODUL 9: MASTER DATA & REFERENSI (`/api/v1/master`)"
Cohesion: 0.40
Nodes (5): 1. Profil Toko (Receipt Header & Branding), 2. Daftar Teknisi Aktif, 3. Status Tiket Resmi, 4. Pengaturan Pajak Aktif, I. MODUL 9: MASTER DATA & REFERENSI (`/api/v1/master`)

### Community 254 - "10. Alur Pengujian & Checklist Kualitas (QA Acceptance Criteria)"
Cohesion: 0.50
Nodes (4): 10.1 Checklist Autentikasi, 10.2 Checklist Alur Servis & Kamera, 10.3 Checklist Kasir & Invoice, 10. Alur Pengujian & Checklist Kualitas (QA Acceptance Criteria)

### Community 256 - "H. MODUL 8: PENGELUARAN TOKO / KAS KELUAR (`/api/v1/pengeluaran`)"
Cohesion: 0.50
Nodes (4): 1. List Pengeluaran, 2. Catat Pengeluaran Baru dari HP, 3. Hapus Pengeluaran, H. MODUL 8: PENGELUARAN TOKO / KAS KELUAR (`/api/v1/pengeluaran`)

### Community 258 - "dev"
Cohesion: 0.67
Nodes (3): dev, Composer\\Config::disableProcessTimeout, @php artisan dev

### Community 261 - "1. Latar Belakang & Tujuan Produk"
Cohesion: 0.67
Nodes (3): 1.1 Masalah yang Diselesaikan, 1.2 Target Pengguna (Roles), 1. Latar Belakang & Tujuan Produk

### Community 262 - "8. Arsitektur Komunikasi API & State Management"
Cohesion: 0.67
Nodes (3): 8.1 Axios Interceptor & Error Handling, 8.2 Struktur Folder Proyek Rekomendasi, 8. Arsitektur Komunikasi API & State Management

### Community 266 - "MODUL 6: PRODUK, SPAREPART & JASA"
Cohesion: 0.67
Nodes (3): MODUL 6: PRODUK, SPAREPART & JASA, S-13: Layar Katalog Sparepart & Jasa, S-14: Layar Tambah / Edit Produk

## Knowledge Gaps
- **839 isolated node(s):** `php`, `$schema`, `name`, `type`, `description` (+834 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **40 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `Controller` to `User`, `Pengeluaran`, `Invoice`, `V1/TiketServisController.php`, `Pelanggan`, `Produk`, `PengaturanPajak`, `KategoriProduk`, `TiketServis`, `ProfileController.php`, `Illuminate\Http\JsonResponse`, `Illuminate\Http\Request`?**
  _High betweenness centrality (0.010) - this node is a cross-community bridge._
- **Why does `dependencies` connect `dependencies` to `mobile-app/package.json`, `axios`, `expo-file-system`, `expo-image-picker`, `expo-sharing`, `@expo/vector-icons`, `react-dom`, `react-native-safe-area-context`, `react-native-screens`, `@react-navigation/native`, `@react-navigation/native-stack`, `@tanstack/react-query`, `zustand`, `expo-image-manipulator`, `InvoiceCreateScreen.tsx`?**
  _High betweenness centrality (0.010) - this node is a cross-community bridge._
- **Why does `react` connect `InvoiceCreateScreen.tsx` to `dependencies`?**
  _High betweenness centrality (0.009) - this node is a cross-community bridge._
- **Are the 30 inferred relationships involving `User` (e.g. with `.login()` and `.teknisi()`) actually correct?**
  _`User` has 30 INFERRED edges - model-reasoned connections that need verification._
- **What connects `php`, `$schema`, `name` to the rest of the system?**
  _839 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `User` be split into smaller, more focused modules?**
  _Cohesion score 0.14285714285714285 - nodes in this community are weakly interconnected._
- **Should `Controller` be split into smaller, more focused modules?**
  _Cohesion score 0.09371980676328502 - nodes in this community are weakly interconnected._