# Roadmap Pengembangan EduVora (Future Master Plan)

Dokumen ini berisi peta jalan (**Roadmap**) pengembangan aplikasi EduVora Multi-tenant SaaS untuk menyelesaikan seluruh sisa modul, role, dan tabel database yang sudah siap (schema only) secara sistematis sesuai arsitektur baku **Controller → Service → Repository → Eloquent Model**.

---

## Ringkasan Status Pengembangan

| Kategori | Status Saat Ini | Total Target |
|----------|----------------|--------------|
| **Role Aktif (UI & Logic Complete)** | 5 (Super-Admin, School-Admin, Teacher, Student, Student-Parent) | 5 / 5 |
| **Portal Orang Tua (Role 5)** | ✅ Selesai 100% (8 Fitur Utama) | 8 / 8 |
| **Sisa Modul Tanpa UI (Schema Only)** | 10 Modul Siap Dibangun | 0 / 10 |
| **Total Estimasi Sisa Fitur** | 63 Fitur Terencana | 63 Fitur |

---

## Rencana Fase Pengembangan Ke Depan

### 🚀 FASE 1: Modul Keuangan & SPP (Finance Module) — PRIORITAS TINGGI
> **Goal:** Menghubungkan seluruh siklus keuangan sekolah dari setting tarif SPP, penerbitan tagihan siswa, transaksi pembayaran, penggajian guru/staff, hingga laporan keuangan & beasiswa.

#### Tabel Database Terkait
`fee_types`, `fee_settings`, `student_invoices`, `payments`, `payrolls`, `scholarships`, `scholarship_recipients`, `finance_transactions`

#### Sub-Fitur & Workflow:
1. **Pengaturan Biaya & SPP (`fee_types`, `fee_settings`):**
   - School-Admin menentukan komponen biaya (SPP Bulanan, Uang Gedung, Seragam, Kegiatan).
   - Pengaturan tarif per tingkat kelas & tahun ajaran.
2. **Generasi Tagihan Siswa (`student_invoices`):**
   - Auto-generate tagihan SPP bulanan untuk seluruh siswa aktif.
   - Status tagihan (`unpaid`, `partially_paid`, `paid`, `overdue`).
3. **Pembayaran & Kasir (`payments`):**
   - Form pencatatan pembayaran manual oleh admin/kasir sekolah.
   - Portal bayar SPP untuk siswa/orang tua & cetak kuitansi/bukti bayar.
4. **Penggajian Guru & Staff (`payrolls`):**
   - Perhitungan gaji pokok, tunjangan, dan potongan kehadiran.
   - Generasi slip gaji digital guru & staff.
5. **Manajemen Beasiswa (`scholarships`, `scholarship_recipients`):**
   - Pendataan program beasiswa & alokasi ke siswa penerima.
   - Otomatisasi pemotongan/diskon tagihan SPP.
6. **Laporan & Buku Kas Keuangan (`finance_transactions`):**
   - Laporan arus kas (Pemasukan & Pengeluaran).
   - Rekap tunggakan SPP per kelas & export Excel.

---

### 🚀 FASE 2: Modul Ujian Online & Bank Soal (Exams & Question Bank) — PRIORITAS TINGGI
> **Goal:** Memungkinkan guru membuat bank soal, menyusun ujian online, dan siswa mengerjakan ujian berwaktu dengan auto-grading.

#### Tabel Database Terkait
`question_bank`, `exams`, `exam_questions`, `exam_sessions`, `exam_answers`

#### Sub-Fitur & Workflow:
1. **Bank Soal Guru (`question_bank`):**
   - Pembuatan soal Pilihan Ganda & Essay per mata pelajaran.
   - Pengelompokan tingkat kesulitan (Mudah, Sedang, Sulit) & Kategori KD.
2. **Manajemen Ujian (`exams`, `exam_questions`):**
   - Penjadwalan UTS, UAS, Kuis Harian, atau Asesmen Nasional.
   - Pengaturan acak soal, acak opsi jawaban, KKM, dan bobot nilai.
3. **Sesi Pelaksanaan Ujian Siswa (`exam_sessions`, `exam_answers`):**
   - Interface ujian siswa dengan countdown timer & indikator ragu-ragu.
   - Auto-save jawaban real-time & deteksi keluar tab (anti-cheat).
4. **Koreksi & Nilai Ujian:**
   - Auto-grading untuk Pilihan Ganda.
   - Interface penilai jawaban Essay oleh guru & sinkronisasi otomatis ke Modul Nilai.

---

### 🚀 FASE 3: Modul Rapor Digital & Cetak Rapor (Report Cards) — PRIORITAS TINGGI
> **Goal:** Otomatisasi pengolahan nilai semester menjadi Rapor Resmi (K13 / Kurikulum Merdeka) yang siap cetak PDF.

#### Tabel Database Terkait
`report_cards`, `report_card_details`, `score_sessions`, `score_details`

#### Sub-Fitur & Workflow:
1. **Pengolahan Nilai Akhir Semester:**
   - Formula perhitungan nilai Rapor (Bobot Harian + UTS + UAS).
   - Konversi nilai angka ke Predikat (A, B, C, D) & Deskripsi Capaian Pembelajaran.
2. **Input Catatan Wali Kelas & Ekstrakurikuler:**
   - Catatan perkembangan karakter siswa oleh Wali Kelas.
   - Rekap nilai ekskul, presensi semester, dan catatan kesehatan.
3. **Generasi & Cetak Rapor PDF:**
   - Layout Rapor Standar Kemdikbud (Halaman Sampul, Data Diri, Nilai Akademik, Catatan).
   - Cetak PDF massal per kelas atau download per siswa di Portal Orang Tua/Siswa.

---

### 🚀 FASE 4: Modul Komunikasi & Pengumuman (Communication) — PRIORITAS SEDANG
> **Goal:** Membangun pusat informasi terpadu sekolah untuk pengumuman, pesan antar user, dan notifikasi real-time.

#### Tabel Database Terkait
`announcements`, `messages`, `notifications`

#### Sub-Fitur & Workflow:
1. **Pengumuman Platform & Sekolah (`announcements`):**
   - Super-Admin kirim pengumuman ke seluruh sekolah.
   - School-Admin & Guru kirim pengumuman khusus per kelas/role target.
2. **Pusat Notifikasi Aplikasi (`notifications`):**
   - Notifikasi tagihan baru, ujian besok, nilai dipublish, dan absensi alpa.
3. **Pesan Internal / Direct Messaging (`messages`):**
   - Fitur obrolan internal antar Guru, Admin, Siswa, dan Orang Tua.

---

### 🚀 FASE 5: Modul Aktivitas Siswa & Kenaikan Kelas (Student Activity & Info) — PRIORITAS SEDANG
> **Goal:** Pencatatan perkembangan non-akademik, pembinaan karakter, dan workflow kenaikan kelas.

#### Tabel Database Terkait
`extracurriculars`, `extracurriculars_members`, `achievements`, `violations`, `violation_types`, `counselling_sessions`, `student_grade_histories`

#### Sub-Fitur & Workflow:
1. **Manajemen Ekstrakurikuler & Anggota:**
   - Pendaftaran ekskul oleh siswa & absensi kegiatan ekskul.
2. **Prestasi & Pelanggaran Siswa (Poin Kedisiplinan):**
   - Input penghargaan/prestasi siswa.
   - Pencatatan pelanggaran siswa berdasarkan poin tipe pelanggaran & tindak lanjut SP.
3. **Bimbingan Konseling (BK):**
   - Jadwal & lembar catatan sesi konseling siswa dengan Guru BK.
4. **Workflow Kenaikan Kelas / Promosi (`student_grade_histories`):**
   - Tool massal lulus/naik kelas akhir tahun ajaran.
   - Penempatan kelas baru siswa & pemetaan histori kelas.

---

### 🚀 FASE 6: Modul Perpustakaan & Manajemen Aset (Library & Assets) — PRIORITAS SEDANG / RENDAH
> **Goal:** Digitalisasi perpustakaan sekolah dan inventarisir sarana prasarana.

#### Tabel Database Terkait
`books`, `book_copies`, `book_loans`, `assets`, `asset_loans`

#### Sub-Fitur & Workflow:
1. **Katalog & Sirkulasi Perpustakaan:**
   - Input katalog buku, stok eksemplar, barcode/QR buku.
   - Peminjaman buku siswa, batas tanggal kembali, dan denda keterlambatan.
2. **Inventaris Aset & Peminjaman Barang Sekolah:**
   - Data inventaris ruangan (Proyektor, Laptop, Ruang Lab).
   - Workflow permohonan pinjam aset oleh guru/siswa & approval admin.

---

### 🚀 FASE 7: Tools Massal, Impersonation & System Health — PRIORITAS SYSTEM
> **Goal:** Kemudahan operasional Super-Admin & School-Admin.

#### Sub-Fitur & Workflow:
1. **Bulk Import & Export Excel/CSV:**
   - Impor massal data Siswa, Guru, Sekolah, dan Mata Pelajaran (`maatwebsite/excel`).
2. **User Impersonation:**
   - Super-Admin / School-Admin login sementara sebagai user tertentu untuk troubleshooting.
3. **System Health & Backup Database:**
   - Dashboard monitoring storage, cache, queue, dan scheduled DB backup download.

---

## Pola Arsitektur Standar yang Wajib Diterapkan

Setiap modul baru wajib mengikuti pola resmi EduVora:

```
[Route web.php] 
     │
     ▼
[FormRequest (Validation)]
     │
     ▼
[Controller] ──returns JSON / Blade view
     │
     ▼
[Service (Business Logic)]
     │
     ▼
[Repository (Uses HasSchoolScope)]
     │
     ▼
[Eloquent Model]
```

### Standar Kualitas & Testing:
- Bahasa Indonesia untuk pesan error, tooltip, & notifikasi.
- Pengujian otomatis wajib menggunakan **Pest 3** (`php artisan test`).
- Code formatting wajib menggunakan **Laravel Pint** (`vendor/bin/pint`).
- Multi-tenant scoping wajib diterapkan di seluruh repository via `school_id`.
