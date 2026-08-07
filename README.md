# 🎓 EduVora — SaaS Multi-Tenant School Management System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16+-4169E1?style=for-the-badge&logo=postgresql)](https://postgresql.org)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite)](https://vitejs.dev)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

**EduVora** adalah platform manajemen sekolah berbasis cloud (*SaaS Multi-Tenant*) yang dirancang untuk mengelola ekosistem pendidikan — mulai dari manajemen sekolah, data guru & siswa, kelas, jadwal pelajaran, absensi, hingga penilaian dan pelaporan secara efisien dalam satu platform terpusat.

---

## 📋 Daftar Isi

- [✨ Fitur Utama per Role](#-fitur-utama-per-role)
- [🔑 Akun Demonstrasi (Default Credentials)](#-akun-demonstrasi-default-credentials)
- [🏛️ Arsitektur & Multi-Tenancy](#️-arsitektur--multi-tenancy)
- [💻 Tech Stack](#-tech-stack)
- [🚀 Cara Instalasi](#-cara-instalasi)
  - [Prasyarat](#prasyarat)
  - [Quick Setup (Rekomendasi)](#quick-setup-rekomendasi)
  - [Setup Manual](#setup-manual)
- [⚡ Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [🧪 Testing & Quality Control](#-testing--quality-control)
- [📚 Dokumentasi API & Database Schema](#-dokumentasi-api--database-schema)
- [📁 Struktur Direktori Proyek](#-struktur-direktori-proyek)
- [📄 Lisensi](#-lisensi)

---

## ✨ Fitur Utama per Role

### 👑 Super Admin (Platform Manager)
* **Manajemen Sekolah:** Tambah, edit, nonaktifkan, dan monitoring status sekolah terdaftar.
* **Manajemen Pengguna & Role:** Kelola hak akses global seluruh platform.
* **Global Activity Logs & Audit:** Pantau aktivitas terkini di seluruh sekolah.
* **Dashboard Analytics:** Visualisasi statistik total sekolah, user, dan pertumbuhan platform.

### 🏫 School Admin (Administrator Sekolah)
* **Tahun Ajaran & Semester:** Pengaturan periode akademik aktif.
* **Guru & Siswa:** Kelola biodata, akun, dan status keaktifan civitas akademika.
* **Struktur Akademik:** Kelola data kelas, ruang kelas, dan mata pelajaran.
* **Jadwal Pelajaran:** Plotting jadwal per hari, jam pelajaran, mata pelajaran, dan guru pengampu.
* **Penugasan Mata Pelajaran (Grade Subject):** Pengaturan KKM, bobot nilai (harian, UTS, UAS, tugas).
* **Manajemen Siswa per Kelas:** Penetapan siswa ke dalam kelas (*enrolment*).
* **Manajemen Penilaian:** Rekapitulasi nilai dan pengawasan nilai siswa.

### 👨‍🏫 Teacher (Guru Pengampu)
* **Jadwal Mengajar:** Tampilan jadwal harian/mingguan yang diampu.
* **Input & Management Absensi:** Sistem alur absensi (*Draft* → *Submit* → *Lock*).
* **Input Nilai Per Sesi:** Penilaian tugas, harian, UTS, dan UAS.
* **Publishing Nilai:** Kontrol visibilitas nilai agar dapat dilihat oleh siswa/orang tua.
* **Laporan & Export:** Cetak dan ekspor laporan absensi serta nilai dalam format Excel.

### 🎓 Student (Siswa)
* **Dashboard Personal:** Ringkasan informasi akademik, pengumuman, dan absensi.
* **Jadwal Pelajaran Harian:** Melihat jadwal pelajaran lengkap per hari beserta ruang dan guru pengampu.
* **Laporan Nilai & Transkrip:** Akses hasil belajar dan nilai yang telah dipublish.

---

## 🔑 Akun Demonstrasi (Default Credentials)

Setelah menjalankan database seeder (`php artisan db:seed`), Anda dapat menggunakan akun bawaan berikut (Password default: `password123`):

| Role | Email | Password Default | Akses / Deskripsi |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@example.com` | `password123` | Akses penuh ke seluruh sekolah & platform |
| **School Admin** | `admin1@school.com` | `password123` | Admin Sekolah 1 |
| **Headmaster** | `headmaster1@school.com` | `password123` | Kepala Sekolah 1 |
| **Teacher** | `teacher1@school.com` | `password123` | Guru Pengampu |
| **Student** | `student1@school.com` | `password123` | Siswa Sekolah 1 |
| **Parent** | `parent1@school.com` | `password123` | Orang Tua Siswa |

> 💡 *Note: Terdapat juga `admin2@school.com` s/d `admin4@school.com` untuk menguji skenario multi-tenant antar sekolah.*

---

## 🏛️ Arsitektur & Multi-Tenancy

Aplikasi ini menggunakan pola arsitektur **Clean Layered Architecture**:

```
Request ──► Controller ──► Service Layer ──► Repository Layer ──► Eloquent Model ──► Database
```

* **Controller (`app/Http/Controllers/`)**: Menangani HTTP request, respon JSON (AJAX) / Blade view.
* **Service Layer (`app/Services/`)**: Menyimpan seluruh logika bisnis aplikasi.
* **Repository Layer (`app/Repositories/`)**: Mengisolasi query database dan menerapkan isolasi Multi-Tenant.
* **Model (`app/Models/`)**: Mengatur relasi antar entitas database.

### 🛡️ Multi-Tenant Scoping
Setiap query data diisolasi secara ketat berdasarkan `school_id` pengguna yang terautentikasi melalui Trait `HasSchoolScope` (`app/Concerns/HasSchoolScope.php`).

```php
// Contoh penerapan otomatis school scope di Repositories
$schoolId = $this->getAuthSchoolId();
```

---

## 💻 Tech Stack

| Layer | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel 12.x | PHP 8.2+ Architecture |
| **Database** | PostgreSQL 16+ | Production DB (SQLite `:memory:` for testing) |
| **Frontend UI** | Blade Templating | Dynamic & Responsive Layout |
| **Styling & Assets** | Tailwind CSS v4 + Vite 7 | Bundling cepat dengan `@tailwindcss/vite` |
| **Interaktivitas UI** | Alpine.js + SweetAlert2 | Modal CRUD & konfirmasi interaktif |
| **Testing** | Pest 3 | Unit & Feature Testing |
| **API Docs** | Scramble | Auto-generated API documentation dari `routes/web.php` |
| **Report Export** | Maatwebsite Excel | Ekspor data absensi & nilai |

---

## 🚀 Cara Instalasi

### Prasyarat

Pastikan perangkat Anda sudah terinstal:
* PHP >= 8.2 (ekstensi PDO, PGSQL, Mbstring, OpenSSL diaktifkan)
* Composer 2.x
* Node.js >= 18.x & NPM
* Database PostgreSQL

### Quick Setup (Rekomendasi)

Jalankan satu perintah untuk menginstal seluruh dependensi, menjalankan migrasi, dan build frontend:

```bash
git clone https://github.com/sulistiyas/EduVora.git
cd EduVora
composer setup
```

Perintah `composer setup` secara otomatis mengeksekusi:
1. `composer install`
2. Membuat file `.env` dari `.env.example`
3. Generate App Key (`php artisan key:generate`)
4. Migrasi Database (`php artisan migrate --force`)
5. Install paket NPM (`npm install`)
6. Build asset Vite (`npm run build`)

---

### Setup Manual

Jika Anda ingin mengonfigurasi langkah demi langkah secara manual:

```bash
# 1. Clone repository
git clone https://github.com/sulistiyas/EduVora.git
cd EduVora

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env
php artisan key:generate

# 4. Sesuaikan konfigurasi database PostgreSQL di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=eduvora
DB_USERNAME=postgres
DB_PASSWORD=your_password

# 5. Jalankan migrasi database & seeder
php artisan migrate --seed

# 6. Install dependensi JavaScript & compile assets
npm install
npm run build
```

---

## ⚡ Menjalankan Aplikasi

Anda dapat menjalankan server backend, Vite frontend, dan Queue worker secara bersamaan dengan satu perintah:

```bash
composer dev
```

Atau jika ingin menjalankan secara terpisah di terminal terpisah:

```bash
# Terminal 1 - Backend Server
php artisan serve

# Terminal 2 - Frontend Asset Server
npm run dev

# Terminal 3 - Queue Listener
php artisan queue:listen
```

Akses aplikasi melalui browser di **`http://localhost:8000`**.

---

## 🧪 Testing & Quality Control

Proyek ini dilengkapi dengan suite pengujian otomatis menggunakan **Pest 3** yang berjalan di SQLite `:memory:` database untuk performa maksimal.

```bash
# Jalankan seluruh unit & feature test
composer test

# Format kode sesuai standar PSR-12 menggunakan Laravel Pint
vendor/bin/pint
```

---

## 📚 Dokumentasi API & Database Schema

### API Documentation (Scramble)
Dokumentasi API interaktif secara otomatis dibuat dari `routes/web.php`.
1. Jalankan aplikasi (`php artisan serve` / `composer dev`).
2. Buka URL: **`http://localhost:8000/docs`**.

### Database Schema (ERD)
Skema database EduVora terdiri dari **62 tabel** yang saling terelasi. Dokumentasi ERD lengkap dapat dilihat di [docs/ERD.md](docs/ERD.md).

---

## 📁 Struktur Direktori Proyek

```text
EduVora/
├── app/
│   ├── Concerns/          # Shared traits (HasSchoolScope)
│   ├── Enums/             # Definition of Enums
│   ├── Exports/           # Excel Export Classes (Maatwebsite)
│   ├── Http/
│   │   ├── Controllers/   # Academic, Auth, Class, School, Student, Teacher
│   │   └── Requests/      # FormRequest Validation Classes
│   ├── Models/            # Domain-driven Eloquent Models (Academic, Core, Finance, etc.)
│   ├── Repositories/      # Scoped Database Access Layer
│   └── Services/          # Business Logic Layer
├── database/
│   ├── migrations/        # 62 Database Table Migrations
│   └── seeders/           # Database Seeders for testing & initial data
├── docs/                  # Documentation & ERD Diagrams
├── resources/
│   ├── views/             # Blade Template Views (Modal CRUDs & Dashboards)
│   └── css/               # Tailwind CSS styles
├── routes/
│   └── web.php            # All Web & AJAX JSON Routes
├── tests/                 # Pest 3 Unit & Feature Tests
└── vite.config.js         # Vite + Tailwind CSS v4 Configuration
```

---

## 📄 Lisensi

EduVora dilisensikan di bawah [MIT License](LICENSE).

---
<p align="center">Made with ❤️ for Better Education Management</p>

