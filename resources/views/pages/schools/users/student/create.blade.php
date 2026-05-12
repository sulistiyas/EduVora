{{-- resources/views/pages/school-admin/students/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="page-wrapper" x-data="studentCreate({
    storeUrl:  '{{ route('school-admin.students.store') }}',
    indexUrl:  '{{ route('school-admin.students.index') }}',
})" x-init="init()">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header__left" style="display:flex;align-items:center;gap:12px">
            <a href="{{ route('school-admin.students.index') }}" class="btn-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <ul class="breadcrumb-list" style="margin-bottom:4px">
                    <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                    <li><i class="ri-arrow-right-s-line"></i></li>
                    <li><a href="{{ route('school-admin.students.index') }}" style="color:var(--text-muted);font-size:11px;font-weight:500;text-decoration:none">Manajemen Siswa</a></li>
                    <li><i class="ri-arrow-right-s-line"></i></li>
                    <li><span>Tambah Siswa</span></li>
                </ul>
                <h1 class="page-title" style="margin-bottom:2px">Tambah Siswa</h1>
                <p class="page-subtitle">Buat akun siswa baru untuk sekolah Anda</p>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitForm" novalidate>
        <div class="form-layout" style="gap:20px">

            {{-- ═══════════ SECTION 1 — INFORMASI AKUN ═══════════ --}}
            <div class="form-card">
                <div class="form-card__header">
                    <div style="display:flex;align-items:center;gap:.625rem">
                        <div class="form-card__icon">
                            <i class="ri-account-circle-line" style="font-size:18px"></i>
                        </div>
                        <h2 class="form-card__title">Informasi Akun</h2>
                    </div>
                </div>
                <div class="form-card__body">

                    {{-- Info role otomatis --}}
                    <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:var(--radius-sm);margin-bottom:18px;font-size:13px;color:#1D4ED8">
                        <i class="ri-information-line" style="font-size:16px;flex-shrink:0"></i>
                        <span>Siswa akan otomatis mendapat role <strong>Student</strong> dan terhubung ke sekolah Anda.</span>
                    </div>

                    <div class="form-grid form-grid--2" style="gap:18px">
                        {{-- Nama --}}
                        <div class="form-group" :class="{ 'has-error': errors.name }">
                            <label class="form-label form-label--required">Nama Lengkap</label>
                            <input type="text" class="form-control" x-model="form.name"
                                placeholder="Masukkan nama lengkap" data-edit-focus>
                            <span class="form-error" x-show="errors.name" x-text="errors.name"></span>
                        </div>

                        {{-- Email --}}
                        <div class="form-group" :class="{ 'has-error': errors.email }">
                            <label class="form-label form-label--required">Email</label>
                            <input type="email" class="form-control" x-model="form.email"
                                placeholder="contoh@email.com">
                            <span class="form-error" x-show="errors.email" x-text="errors.email"></span>
                        </div>

                        {{-- No. Telepon --}}
                        <div class="form-group" :class="{ 'has-error': errors.phone_number }">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" x-model="form.phone_number"
                                placeholder="08xxxxxxxxxx">
                            <span class="form-error" x-show="errors.phone_number" x-text="errors.phone_number"></span>
                        </div>

                        {{-- Status --}}
                        <div class="form-group" :class="{ 'has-error': errors.status }">
                            <label class="form-label form-label--required">Status</label>
                            <div class="radio-group">
                                <label class="radio-card" :class="{ active: form.status === 'active' }">
                                    <input type="radio" x-model="form.status" value="active" hidden>
                                    <span class="radio-card__dot radio-card__dot--active"></span>
                                    <span>Aktif</span>
                                </label>
                                <label class="radio-card" :class="{ active: form.status === 'inactive' }">
                                    <input type="radio" x-model="form.status" value="inactive" hidden>
                                    <span class="radio-card__dot radio-card__dot--inactive"></span>
                                    <span>Non-Aktif</span>
                                </label>
                            </div>
                            <span class="form-error" x-show="errors.status" x-text="errors.status"></span>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="form-grid form-grid--2" style="gap:18px;margin-top:18px">
                        <div class="form-group" :class="{ 'has-error': errors.password }">
                            <label class="form-label form-label--required">Password</label>
                            <div class="input-password">
                                <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                    x-model="form.password" placeholder="Min. 8 karakter">
                                <button type="button" class="input-password__toggle" @click="showPassword = !showPassword">
                                    <svg x-show="!showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg x-show="showPassword"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                </button>
                            </div>
                            <span class="form-error" x-show="errors.password" x-text="errors.password"></span>
                        </div>
                        <div class="form-group" :class="{ 'has-error': errors.password_confirmation }">
                            <label class="form-label form-label--required">Konfirmasi Password</label>
                            <div class="input-password">
                                <input :type="showPassword ? 'text' : 'password'" class="form-control"
                                    x-model="form.password_confirmation" placeholder="Ulangi password">
                            </div>
                            <span class="form-error" x-show="errors.password_confirmation" x-text="errors.password_confirmation"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════ SECTION 2 — PROFIL SISWA ═══════════ --}}
            <div class="form-card">
                <div class="form-card__header">
                    <div style="display:flex;align-items:center;gap:.625rem">
                        <div class="form-card__icon form-card__icon--blue">
                            <i class="ri-book-open-line" style="font-size:18px"></i>
                        </div>
                        <div>
                            <h2 class="form-card__title">Profil Siswa</h2>
                            <p style="font-size:11px;color:var(--text-muted);margin:0">Opsional — dapat diisi nanti</p>
                        </div>
                    </div>
                </div>
                <div class="form-card__body">
                    <div class="form-grid form-grid--3" style="gap:16px">
                        <div class="form-group">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control" x-model="form.profile.nis" placeholder="Nomor Induk Siswa">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap (Profil)</label>
                            <input type="text" class="form-control" x-model="form.profile.full_name" placeholder="Sesuai ijazah">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Panggilan</label>
                            <input type="text" class="form-control" x-model="form.profile.nick_name" placeholder="Nama panggilan">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" x-model="form.profile.birth_date">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-control" x-model="form.profile.gender">
                                <option value="">— Pilih —</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. HP Siswa</label>
                            <input type="text" class="form-control" x-model="form.profile.phone_number" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kelas / Rombel</label>
                            <input type="text" class="form-control" x-model="form.profile.class_group" placeholder="X-A, XI IPA 2, dll">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Grade ID</label>
                            <input type="number" class="form-control" x-model="form.profile.grade_id" placeholder="ID kelas di sistem">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" x-model="form.profile.postal_code" placeholder="12345">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kota</label>
                            <input type="text" class="form-control" x-model="form.profile.city" placeholder="Kota domisili">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Provinsi</label>
                            <input type="text" class="form-control" x-model="form.profile.province" placeholder="Provinsi">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" class="form-control" x-model="form.profile.enrollment_date">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Lulus</label>
                            <input type="date" class="form-control" x-model="form.profile.graduation_date">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:16px">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" rows="3" x-model="form.profile.address" placeholder="Alamat lengkap siswa"></textarea>
                    </div>
                </div>
            </div>

            {{-- ═══════════ ACTIONS ═══════════ --}}
            <div class="form-actions" style="margin-top:10px">
                <a href="{{ route('school-admin.students.index') }}" class="btn btn--secondary">Batal</a>
                <button type="submit" class="btn btn--primary" :disabled="submitting" style="min-width:160px;justify-content:center">
                    <span x-show="!submitting" style="display:inline-flex;align-items:center;gap:.4375rem">
                        <i class="ri-save-line"></i> Simpan Siswa
                    </span>
                    <span x-show="submitting" class="btn-spinner">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>

        </div>{{-- /form-layout --}}
    </form>
</div>
@endsection
