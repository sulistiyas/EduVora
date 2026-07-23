# EduVora — SaaS Multi-Tenant School Management System

Manajemen sekolah berbasis cloud untuk mengelola siswa, guru, kelas, jadwal, absensi, nilai, dan laporan — satu platform untuk banyak sekolah.

## Fitur

### Super Admin
- Kelola sekolah (tambah, edit, nonaktifkan)
- Kelola user dan role
- Dashboard monitoring seluruh sekolah

### School Admin
- Kelola tahun ajaran dan semester
- Kelola guru dan siswa
- Kelola kelas, ruangan, mata pelajaran
- Kelola jadwal pelajaran
- Kelola penugasan mata pelajaran ke kelas (grade-subject) dengan KKM dan bobot nilai
- Kelola siswa per kelas (assign/remove)
- Input dan kelola nilai siswa

### Teacher
- Lihat jadwal mengajar sendiri
- Input absensi (draft → submit → lock)
- Input nilai per sesi (harian/UTS/UAS/tugas)
- Publish/unpublish nilai
- Laporan absensi dan nilai + export Excel

### Student
- Dashboard ringkasan

## Arsitektur

```
Controller → Service → Repository → Eloquent Model
```

- **Controller** — menerima request, return JSON (AJAX) atau Blade view
- **Service** — logika bisnis, validasi silang
- **Repository** — query database, enforce multi-tenant scoping
- **Model** — relasi dan akses data

### Multi-Tenant

Setiap query data harus di-scoping ke `school_id` user yang sedang login. Trait `HasSchoolScope` di `app/Concerns/HasSchoolScope.php` menyediakan method `getAuthSchoolId()`.

### Role-Based Access

| Role | Akses |
|------|-------|
| `super-admin` | Semua sekolah, user, role |
| `school-admin` | Data sekolah sendiri (akademik, siswa, guru) |
| `teacher` | Jadwal, absensi, nilai sendiri |
| `student` | Dashboard sendiri |

Middleware `role` didaftarkan di `bootstrap/app.php`. Contoh: `middleware(['role:super-admin,school-admin'])`.

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+, PostgreSQL |
| Frontend | Vite 7, Tailwind CSS v4, Alpine.js, SweetAlert2 |
| Testing | Pest 3, SQLite :memory: |
| API Docs | Scramble (auto-generate dari `routes/web.php`) |
| Export | Maatwebsite Excel |
| Formatting | Laravel Pint (default config) |

## Instalasi

### Prasyarat

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- PostgreSQL
- Git

### Quick Setup

```bash
git clone https://github.com/sulistiyas/EduVora.git
cd EduVora
composer setup
```

`composer setup` akan menjalankan:
1. `composer install`
2. Copy `.env` dari `.env.example`
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

### Setup Manual

```bash
# Install dependency PHP
composer install

# Buat file environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=eduvora
DB_USERNAME=root
DB_PASSWORD=

# Jalankan migrasi
php artisan migrate

# Install dependency JS & build asset
npm install
npm run build

# Seed data (opsional)
php artisan db:seed
```

### Environment Variables Penting

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=eduvora
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

## Menjalankan Aplikasi

```bash
# Semua sekaligus (recommended)
composer dev

# Atau manual (3 terminal terpisah)
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Buka `http://localhost:8000` di browser.

## Perintah Berguna

| Perintah | Fungsi |
|----------|--------|
| `composer dev` | Jalankan server + queue + Vite dev server |
| `composer test` | Jalankan semua test |
| `composer setup` | Setup pertama kali (install, migrate, build) |
| `npm run dev` | Vite dev server |
| `npm run build` | Vite production build |

## Struktur Direktori

```
app/
├── Concerns/              # Shared traits (HasSchoolScope)
├── Enums/                 # PHP enums
├── Exports/               # Maatwebsite Excel exports
├── Http/
│   ├── Controllers/       # Academic, Auth, Class, School, Student, Teacher
│   └── Requests/          # FormRequest classes (16 files)
├── Models/
│   ├── Academic/          # AcademicYear, Semester, Subject, Grade, Room, Schedule
│   ├── Activity/          # Attendance, Violation, Extracurricular
│   ├── Asset/             # Asset management
│   ├── Communication/     # Announcement, Message
│   ├── Core/              # User, Role, School
│   ├── Exam/              # Exam, ReportCard, Assignment
│   ├── Finance/           # Fee, Invoice, Payment, Payroll, Scholarship
│   ├── Library/           # Book, BookCopy, BookLoan
│   ├── Student/           # Student, Score, Registration
│   └── Teacher/           # Teacher
├── Repositories/          # Data access layer
└── Services/              # Business logic layer

database/migrations/       # 62 tabel
resources/views/           # Blade templates
routes/web.php             # Semua route (tidak ada api.php)
```

## Testing

```bash
composer test
```

Test menggunakan SQLite in-memory yang dikonfigurasi di `phpunit.xml`. Trait `RefreshDatabase` di-comment di `tests/Pest.php` — config di phpunit.xml yang handle.

## API Documentation

Scramble auto-generate dokumentasi API dari `routes/web.php`:

```bash
php artisan serve
# Buka /docs di browser
```

## Database

62 tabel — lihat [docs/ERD.md](docs/ERD.md) untuk Entity Relationship Diagram lengkap.

## License

MIT
