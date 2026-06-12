# Bale Umpak 🏛️

Bale Umpak adalah *foundation package* utama untuk pengembangan landing page di dalam ekosistem Bale. "Umpak" diambil dari bahasa Jawa yang berarti penyangga atau fondasi rumah, yang merepresentasikan peran package ini sebagai dasar yang stabil untuk seluruh tema (theme) landing page.

Package ini menyediakan standardisasi *Data Transfer Objects* (DTO), Repositories, View Composers, Blade Components dasar, dan command scaffolding untuk mempercepat pembuatan package tema landing page baru.

## 📦 Instalasi

Jika package ini belum terinstall di project Laravel utama Anda, tambahkan di `composer.json` atau gunakan command berikut (jika sudah disetup lokal):

```bash
composer require bale/umpak
```

Setelah package terinstall, jalankan perintah instalasi umpak:

```bash
php artisan umpak:install
```
Command ini akan melakukan *publish* file konfigurasi `config/umpak.php` dan aset yang dibutuhkan.

## 🚀 Membangun Tema (Scaffolding)

Fungsi utama Umpak adalah kemampuannya men-scaffold tema landing page baru secara instan.

```bash
php artisan umpak:make <nama-package> <slug-organisasi>
```

**Contoh:**
```bash
php artisan umpak:make bale-dinkes dinkes
```

Perintah di atas akan men-generate folder `packages/bale-dinkes` yang berisi:
- `composer.json` siap pakai.
- `BaleDinkesServiceProvider.php`
- `Http/Controllers/LandingController.php` dengan implementasi repository data Umpak.
- Routing bawaan (home, post, page) di `routes/web.php`.
- Struktur View lengkap (layouts, errors, pages) dengan Tailwind CSS / AlpineJS integration.
- Referensi Blade Components Umpak di dalam view.

Setelah di-*generate*, tambahkan package baru Anda di `composer.json` utama dan jalankan `composer update`, lalu daftarkan provider-nya di `bootstrap/providers.php`.

## 🛠 Alur Kerja & Arsitektur

Umpak memisahkan antara pengambilan data dan presentasi data. Alur kerjanya:

1. **Repositories:** Mengambil data dari API Headless CMS. (Misalnya `PostRepositoryInterface`, `SectionRepositoryInterface`).
2. **DTOs (Data Transfer Objects):** Repository mengembalikan data dalam bentuk objek terstandarisasi (`SectionData`, `PostData`, `PageData`, `OptionData`, `NavigationData`).
3. **Controller:** Controller di tema (scaffolded) memanggil Repository, menerima DTO, dan mengirimkannya ke View.
4. **View Composers:** `LandingPageComposer` secara otomatis menyuntikkan data statis seperti `$umpakOrg` (Data Organisasi) dan `$umpakNav` (Data Menu Navigasi) ke *semua* views.
5. **Blade Components:** Tema memanggil komponen siap pakai milik Umpak, contohnya `<x-umpak::editorjs-renderer :content="$post->content" />` untuk merender isi artikel.

## 🧬 Fitur & Fungsi Utama

### 1. View Variables Otomatis
Anda tidak perlu mengirim data global di setiap controller. Di file `.blade.php` mana pun, Anda bisa mengakses:
- `$umpakOrg`: DTO berisi informasi instansi (Nama, alamat, logo, telepon, medsos). Contoh: `{{ $umpakOrg->organizationName }}`
- `$umpakNav`: Collection dari struktur menu navigasi hierarkis.

### 2. Helpers Bawaan
Umpak menyediakan fungsi helper untuk mempermudah akses resources:
- `cdn_asset('path/ke/aset.jpg')`: Membungkus path aset ke URL CDN (jika CDN aktif).
- `umpak_org('slug')`: Mengambil spesifik data organisasi.
- `umpak_option('social_facebook')`: Mengambil konfigurasi lain dari option.
- `umpak_config('nama_key')`: Mengambil konfigurasi dari `config/umpak.php`.

### 3. EditorJS Renderer Component
Post dan Pages di Bale menggunakan Editor.js. Anda tidak perlu merender array blocks satu persatu.
```blade
<x-umpak::editorjs-renderer :content="$post->content" />
```
Itu otomatis merender header, paragraph, list, image, dll ke dalam bentuk HTML semantic yang siap di-*styling*.

### 4. Blade Components Tambahan
- **Analytics:** `<x-umpak::analytics />` (otomatis memuat script tracker balystics).
- **Breadcrumb:** `<x-umpak::breadcrumb :items="..." />` (Men-generate visual breadcrumb beserta schema JSON-LD untuk SEO Google).
- **Share Button:** `<x-umpak::share-button url="..." title="..." />` (Modal pop-up berbagi ke media sosial).
- **Error Fallback:** `<x-umpak::section-error title="..." />` (Digunakan bila section page builder belum dikonfigurasi).

## 📄 Struktur Direktori Penting

- `src/DTOs/`: Definisi bentuk data pasti yang mengalir ke frontend.
- `src/Contracts/` & `src/Repositories/`: Tempat pengambilan data via API/Database simulasi.
- `src/ViewComposers/`: Logic bind variabel global ke tampilan.
- `src/Console/`: Logic terminal commands (`umpak:make`).
- `resources/views/components/`: Kumpulan utility komponen Umpak.
- `src/helpers.php`: Kumpulan function pembantu.

## 🛡️ Lisensi

The MIT License (MIT).
