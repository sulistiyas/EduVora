# Analisis Fitur Aplikasi EduVora
## Fitur yang Sudah Ada vs Fitur yang Kurang/Bisa Ditambah

**Tanggal Analisis:** 28 Juli 2026
**Aplikasi:** EduVora — Multi-tenant School Management SaaS
**Tech Stack:** Laravel 12, PHP 8.2+, PostgreSQL, Vite 7, Tailwind CSS v4, Alpine.js

---

## Ringkasan

Aplikasi EduVora adalah platform manajemen sekolah **multi-tenant SaaS** dengan 4 role aktif + 1 role terencana (`student-parent`). Banyak fitur sudah terimplementasi, namun banyak juga **model database yang sudah ada tapi belum punya UI/CRUD**.

---

## ROLE 1: SUPER-ADMIN

### Fitur yang Sudah Ada

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Dashboard | Statistik total sekolah, siswa, guru, user, tipe sekolah, presensi hari ini, invoice, audit log |
| 2 | Manajemen Sekolah | Full CRUD + detail + toggle status |
| 3 | Manajemen User | Full CRUD + filter by search/status/role + detail |
| 4 | Role & Permission | Full CRUD + toggle status |
| 5 | Audit Log | Read-only + filter by table/user/date range + sorting |
| 6 | Platform Settings | Read/Update key-value settings |

### Fitur yang Kurang/Bisa Ditambah

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | **Impor User Massal** | Bulk import user dari file Excel/CSV |
| 2 | **Impor Sekolah Massal** | Bulk import data sekolah |
| 3 | **User Impersonation** | Super-admin bisa login sebagai user lain untuk troubleshooting |
| 4 | **Manajemen Pengumuman Platform** | Pengumuman untuk semua sekolah (model `announcements` sudah ada) |
| 5 | **Notifikasi Platform** | Sistem notifikasi global ke seluruh user (model `notifications` sudah ada) |
| 6 | **Messaging antar User** | Kirim pesan antar user (model `messages` sudah ada) |
| 7 | **Backup & Restore** | Backup database + restore |
| 8 | **System Health Monitor** | Dashboard status server, queue, cache, storage |
| 9 | **Manajemen Email/SMTP** | Konfigurasi email server untuk notifikasi |
| 10 | **Setup Wizard Sekolah** | Wizard onboarding sekolah baru step-by-step |
| 11 | **Create Platform Settings** | Saat ini settings hanya bisa di-update, tidak bisa di-create/delete |

---

## ROLE 2: SCHOOL-ADMIN

### Fitur yang Sudah Ada

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Dashboard | Statistik siswa, guru, kelas aktif, mata pelajaran, ruangan, semester, presensi, audit log |
| 2 | Manajemen Tahun Ajaran | Full CRUD + toggle status |
| 3 | Manajemen Semester | Full CRUD + toggle status + dropdown academic year |
| 4 | Manajemen Mata Pelajaran | Full CRUD + toggle status |
| 5 | Manajemen Ruangan | Full CRUD + toggle status |
| 6 | Manajemen Kelas | Full CRUD + detail + assign siswa + toggle status |
| 7 | Assign Mata Pelajaran ke Kelas | CRUD + weight config (harian/UTS/UAS) |
| 8 | Manajemen Jadwal | Full CRUD + filter hari/tipe sesi |
| 9 | Manajemen Siswa | Full CRUD + detail + toggle status + create user |
| 10 | Manajemen Guru & Staff | Full CRUD + detail + toggle status + create user |
| 11 | Monitoring Presensi | Read-only view semua sesi presensi sekolah + filter |
| 12 | Tanggal Akademik | CRUD (holiday/exam/event/deadline) |
| 13 | Manajemen Nilai | Full CRUD + publish/unpublish |

### Fitur yang Kurang/Bisa Ditambah

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | **Manajemen Keuangan (SPP)** | Schema sudah ada: `fee_types`, `fee_settings`, `student_invoices`, `payments` — belum ada UI |
| 2 | **Penggajian Guru** | Schema `payrolls` sudah ada — sidebar placeholder only |
| 3 | **Manajemen Beasiswa** | Schema `scholarships`, `scholarship_recipients` sudah ada |
| 4 | **Transaksi Keuangan** | Schema `finance_transactions` sudah ada |
| 5 | **Laporan Keuangan** | Laporan pemasukan/pengeluaran, rekap SPP |
| 6 | **Manajemen Pengumuman** | Schema `announcements` sudah ada — belum ada UI |
| 7 | **Manajemen Pustaka** | Schema `books`, `book_copies`, `book_loans` sudah ada — belum ada UI |
| 8 | **Manajemen Aset Sekolah** | Schema `assets`, `asset_loans` sudah ada — belum ada UI |
| 9 | **Manajemen Ujian Online** | Schema `exams`, `exam_questions`, `exam_sessions`, `exam_answers`, `question_bank` sudah ada — belum ada UI |
| 10 | **Manajemen Tugas/PR** | Schema `assigments`, `assigment_submissions` sudah ada — belum ada UI |
| 11 | **Rapor/Cetak Rapor** | Schema `report_cards`, `report_card_details` sudah ada — belum ada UI |
| 12 | **Kenaikan Kelas/Promosi** | Schema `student_grade_histories` sudah ada — belum ada workflow |
| 13 | **Manajemen Orang Tua Siswa** | Schema `student_parents` sudah ada — belum ada CRUD |
| 14 | **Manajemen Ekstrakurikuler** | Schema `extracurriculars`, `extracurriculars_members` sudah ada — belum ada UI |
| 15 | **Manajemen Prestasi** | Schema `achievements` sudah ada — belum ada UI |
| 16 | **Manajemen Pelanggaran** | Schema `violations`, `violation_types` sudah ada — belum ada UI |
| 17 | **Manajemen Bimbingan Konseling** | Schema `counselling_sessions` sudah ada — belum ada UI |
| 18 | **Monitoring Presensi (approve)** | Status `approved` ada di model tapi belum ada workflow approve |
| 19 | **Impor Siswa/Guru Massal** | Bulk import dari Excel/CSV |
| 20 | **Export Data Sekolah** | Export seluruh data siswa/guru/kelas ke Excel |
| 21 | **Dashboard Statistik Lanjutan** | Grafik tren prestasi, grafik kehadiran per bulan |

---

## ROLE 3: TEACHER

### Fitur yang Sudah Ada

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Dashboard | Profil guru, jadwal hari ini, statistik, pengumuman, event akademik |
| 2 | Lihat Jadwal | Read-only filter jadwal per guru |
| 3 | Manajemen Presensi | Full workflow: start session → input per siswa → submit → lock/unlock |
| 4 | Input Nilai | Full CRUD: create session → input per siswa → publish/unpublish |
| 5 | Laporan Presensi + Export Excel | Filter + detail per sesi + download .xlsx |
| 6 | Laporan Nilai + Export Excel | Filter + detail per sesi + download .xlsx |

### Fitur yang Kurang/Bisa Ditambah

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | **Buat Tugas/PR** | Schema `assigments` sudah ada — teacher bisa buat tugas untuk kelasnya |
| 2 | **Koreksi Tugas** | Schema `assigment_submissions` sudah ada — teacher koreksi submission siswa |
| 3 | **Buat Ujian Online** | Schema `exams`, `exam_questions` sudah ada |
| 4 | **Bank Soal** | Schema `question_bank` sudah ada — teacher simpan/reuse soal |
| 5 | **Jurnal Mengajar** | Schema `teaching_journals` sudah ada — teacher catat aktivitas mengajar |
| 6 | **Ajukan Izin/Cuti** | Schema `leave_requests` sudah ada — teacher submit leave request |
| 7 | **Lihat Rapor Siswa** | Teacher lihat rapor siswa di kelasnya |
| 8 | **Progres Belajar Siswa** | Sidebar placeholder — analisis perkembangan siswa |
| 9 | **Statistik Kelas** | Sidebar placeholder — grafik nilai/kehadiran per kelas |
| 10 | **Tingkat Ketercapaian** | Sidebar placeholder — pemetaan kurikulum |
| 11 | **Edit Profil Guru** | Belum ada halaman edit profil |
| 12 | **Manajemen Ekstrakurikuler** | Guru bisa kelola ekskul yang diampu |
| 13 | **Input Prestasi Siswa** | Catat prestasi siswa di bawah binaannya |
| 14 | **Input Pelanggaran Siswa** | Laporkan pelanggaran siswa |
| 15 | **Bimbingan Konseling** | Catat sesi konseling dengan siswa |
| 16 | **Kirim Pengumuman ke Kelas** | Kirim pengumuman ke kelas yang diampu |
| 17 | **Export Raport** | Generate dan download rapor siswa |

---

## ROLE 4: STUDENT

### Fitur yang Sudah Ada

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Dashboard | Profil, jadwal hari ini + status presensi, statistik (nilai rata-rata, presensi, ranking, tugas pending), rangking kelas |
| 2 | Lihat Jadwal | Grid mingguan per kelas, mata pelajaran, guru, ruangan |
| 3 | Histori Presensi | Full history + statistik (hadir/absen/terlambat/izin/sakit) |
| 4 | Lihat Nilai | Nilai published per mata pelajaran + rata-rata/max/min + detail per sesi |

### Fitur yang Kurang/Bisa Ditambah

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | **Submit Tugas/PR** | Schema `assigment_submissions` sudah ada — student upload jawaban |
| 2 | **Kerjakan Ujian Online** | Schema `exam_sessions`, `exam_answers` sudah ada |
| 3 | **Lihat Rapor** | Schema `report_cards`, `report_card_details` sudah ada |
| 4 | **Ajukan Izin Sakit** | Student submit izin dengan lampiran surat dokter |
| 5 | **Edit Profil Siswa** | Belum ada halaman edit profil |
| 6 | **Lihat Pengumuman** | Schema `announcements` sudah ada |
| 7 | **Lihat Pustaka/Buku** | Cari dan lihat katalog buku perpustakaan |
| 8 | **Lihat Ekstrakurikuler** | Lihat dan daftar ekskul |
| 9 | **Lihat Prestasi** | Lihat pencapaian diri sendiri |
| 10 | **Lihat Pelanggaran** | Lihat catatan pelanggaran diri sendiri |
| 11 | **Histori Kenaikan Kelas** | Lihat riwayat kenaikan kelas |
| 12 | **Kalender Akademik** | Lihat tanggal libur, UTS, UAS, event |
| 13 | **Pesan ke Guru** | Kirim pesan/consultasi ke guru |
| 14 | **Lihat Jadwal Ujian** | Lihat jadwal ujian yang sudah dijadwalkan admin |

---

## ROLE 5: STUDENT-PARENT (Sudah Terimplementasi ✅)

Fitur portal orang tua telah selesai dibangun secara utuh (**Controller → Service → Repository → Model**) dengan 8 fitur utama:

### Fitur yang Sudah Terimplementasi

| No | Fitur | Status | Keterangan |
|----|-------|--------|------------|
| 1 | **Dashboard Orang Tua** | ✅ Selesai | Ringkasan profil anak, statistik kehadiran, nilai, SPP, & jadwal |
| 2 | **Presensi Anak** | ✅ Selesai | Rekap kehadiran harian & per sesi (Hadir, Izin, Sakit, Alpa, Terlambat) |
| 3 | **Nilai Anak** | ✅ Selesai | Transkrip nilai per mapel & detail sesi penilaian |
| 4 | **Pembayaran SPP** | ✅ Selesai | Monitoring tagihan & histori pembayaran SPP |
| 5 | **Profil Anak** | ✅ Selesai | Data lengkap anak, NIS, wali kelas, & kontak |
| 6 | **Pesan ke Guru** | ✅ Selesai | Kontak pengajar & wali kelas (WhatsApp / Email) |
| 7 | **Rapor Anak** | ✅ Selesai | Rekapitulasi nilai akhir semester & cetak rapor |
| 8 | **Histori Akademik** | ✅ Selesai | Riwayat kenaikan kelas & pencapaian semester |

---

## MODULE YANG BELUM ADA UI SAMA SEKALI (Schema Sudah Ada)

| Module | Tabel | Prioritas |
|--------|-------|-----------|
| **Finance** | `fee_types`, `fee_settings`, `student_invoices`, `payments`, `payrolls`, `scholarships`, `scholarship_recipients`, `finance_transactions` | Tinggi |
| **Exams** | `exams`, `exam_questions`, `exam_sessions`, `exam_answers`, `question_bank` | Tinggi |
| **Assignments** | `assigments`, `assigment_submissions` | Tinggi |
| **Report Cards** | `report_cards`, `report_card_details` | Tinggi |
| **Communication** | `announcements`, `messages`, `notifications` | Sedang |
| **Library** | `books`, `book_copies`, `book_loans` | Sedang |
| **Assets** | `assets`, `asset_loans` | Rendah |
| **Student Activity** | `extracurriculars`, `extracurriculars_members`, `achievements`, `violations`, `violation_types`, `counselling_sessions` | Sedang |
| **Teacher Activity** | `teaching_journals`, `leave_requests` | Sedang |
| **Student Info** | `student_parents`, `student_grade_histories`, `registrations` | Sedang |

---

## STATISTIK RINGKAS

| Kategori | Jumlah |
|----------|--------|
| Total Role | 5 Aktif |
| Fitur Sudah Ada (Super-Admin) | 6 |
| Fitur Kurang (Super-Admin) | 11 |
| Fitur Sudah Ada (School-Admin) | 13 |
| Fitur Kurang (School-Admin) | 21 |
| Fitur Sudah Ada (Teacher) | 6 |
| Fitur Kurang (Teacher) | 17 |
| Fitur Sudah Ada (Student) | 4 |
| Fitur Kurang (Student) | 14 |
| Fitur Portal Orang Tua (Parent) | 8 (Selesai ✅) |
| Module Tanpa UI (Schema Only) | 10 |
| **Total Fitur Kurang/Bisa Ditambah** | **63** |
