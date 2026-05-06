@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="page-wrapper" x-data="userCreate()" x-init="init()">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header__left" style="align-items:center">
            <a href="{{ route('users.index') }}" class="btn-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="page-title" style="margin-bottom:2px">Tambah User</h1>
                <p class="page-subtitle">Buat akun pengguna baru</p>
            </div>
        </div>
    </div>

    <form @submit.prevent="submitForm" novalidate>
        <div class="form-layout" style="gap:20px">

            {{-- ═══════════════════════ SECTION 1 — INFORMASI AKUN ═══════════════════════ --}}
            <div class="form-card">
                <div class="form-card__header">
                    <div style="display:flex; align-items:center; gap:.625rem">
                        <div class="form-card__icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <h2 class="form-card__title">Informasi Akun</h2>
                    </div>
                    
                </div>
                <div class="form-card__body">
                    <div class="form-grid form-grid--2" style="gap:18px">

                        {{-- Name --}}
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

                        {{-- Phone --}}
                        <div class="form-group" :class="{ 'has-error': errors.phone_number }">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" x-model="form.phone_number"
                                placeholder="08xxxxxxxxxx">
                            <span class="form-error" x-show="errors.phone_number" x-text="errors.phone_number"></span>
                        </div>

                        {{-- Role — custom select, menu pakai x-show bukan CSS display:none --}}
                        <div class="form-group" :class="{ 'has-error': errors.role }">
                            <label class="form-label form-label--required">Role</label>
                            <div class="custom-select" @click.away="roleDropOpen = false">
                                <button 
                                    type="button" 
                                    class="custom-select__trigger" 
                                    @click="roleDropOpen = !roleDropOpen"
                                    style="justify-content:space-between">
                                    <span x-show="!form.role" style="color:var(--text-muted,#94A3B8)">Pilih Role</span>
                                    <span x-show="form.role">
                                        <span class="role-badge" :class="'role-badge--' + selectedRoleName" x-text="selectedRoleLabel"></span>
                                    </span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        :style="roleDropOpen ? 'transform:rotate(180deg);transition:.2s' : 'transition:.2s'"
                                        style="flex-shrink:0;margin-left:auto">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>

                                {{-- Menu pakai x-show + position:fixed agar tidak terpotong overflow --}}
                                <div class="custom-select__menu"
                                    x-show="roleDropOpen"
                                    x-transition:enter="cs-enter"
                                    x-transition:enter-start="cs-enter-start"
                                    x-transition:enter-end="cs-enter-end"
                                    x-transition:leave="cs-enter"
                                    x-transition:leave-start="cs-enter-end"
                                    x-transition:leave-end="cs-enter-start"
                                    @click.stop>
                                    <template x-for="role in roles" :key="role.role_id">
                                        <div class="custom-select__item"
                                            :class="{ active: String(form.role) === String(role.role_id) }"
                                            @click="selectRole(role)">
                                            <span class="role-badge"
                                                :class="'role-badge--' + role.role_name"
                                                x-text="role.role_name"></span>
                                            <svg x-show="String(form.role) === String(role.role_id)"
                                                width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" style="margin-left:auto;color:#2563EB">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </div>
                                    </template>
                                    <div class="custom-select__empty" x-show="roles.length === 0">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="spin" style="display:inline-block"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                        Memuat role...
                                    </div>
                                </div>
                            </div>
                            <span class="form-error" x-show="errors.role" x-text="errors.role"></span>
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

                        {{-- School --}}
                        <div class="form-group" :class="{ 'has-error': errors.school }">
                            <label class="form-label form-label--required">Sekolah</label>

                            <div class="custom-select" @click.away="schoolDropOpen = false">
                                <button 
                                    type="button" 
                                    class="custom-select__trigger" 
                                    @click="schoolDropOpen = !schoolDropOpen"
                                    style="justify-content:space-between">

                                    <span x-show="!form.school" style="color:var(--text-muted,#94A3B8)">
                                        Pilih Sekolah
                                    </span>

                                    <span x-show="form.school">
                                        <span x-text="selectedSchoolLabel"></span>
                                    </span>

                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2"
                                        :style="schoolDropOpen ? 'transform:rotate(180deg);transition:.2s' : 'transition:.2s'"
                                        style="flex-shrink:0;margin-left:auto">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>

                                {{-- Dropdown --}}
                                <div class="custom-select__menu"
                                    x-show="schoolDropOpen"
                                    x-transition
                                    @click.stop>

                                    <template x-for="school in schools" :key="school.school_id">
                                        <div class="custom-select__item"
                                            :class="{ active: String(form.school) === String(school.school_id) }"
                                            @click="selectSchool(school)">

                                            <span x-text="school.school_name"></span>

                                            <svg x-show="String(form.school) === String(school.school_id)"
                                                width="14" height="14" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2.5"
                                                style="margin-left:auto;color:#2563EB">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </div>
                                    </template>

                                    <div class="custom-select__empty" x-show="schools.length === 0">
                                        <svg width="14" height="14" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2"
                                            class="spin">
                                            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                        </svg>
                                        Memuat sekolah...
                                    </div>
                                </div>
                            </div>

                            <span class="form-error" x-show="errors.school" x-text="errors.school"></span>
                        </div>
                    </div>

                    {{-- Passwords --}}
                    <div class="form-grid form-grid--2" style="gap:18px; margin-top:20px">
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

            {{-- ═══════════════════════ SECTION 2 — PROFIL GURU ═══════════════════════ --}}
            <div class="form-card" x-show="isTeacherType" x-transition>
                <div class="form-card__header">
                    <div class="form-card__icon form-card__icon--purple">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <h2 class="form-card__title">Profil Guru</h2>
                </div>
                <div class="form-card__body">
                    <div class="form-grid form-grid--3">
                        <div class="form-group"><label class="form-label">NIP</label><input type="text" class="form-control" x-model="form.profile.nip" placeholder="Nomor Induk Pegawai"></div>
                        <div class="form-group"><label class="form-label">NIK</label><input type="text" class="form-control" x-model="form.profile.nik" placeholder="Nomor Induk Kependudukan"></div>
                        <div class="form-group"><label class="form-label">Nama Lengkap (Profil)</label><input type="text" class="form-control" x-model="form.profile.full_name" placeholder="Sesuai dokumen"></div>
                        <div class="form-group"><label class="form-label">Tempat Lahir</label><input type="text" class="form-control" x-model="form.profile.birth_place" placeholder="Kota kelahiran"></div>
                        <div class="form-group"><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control" x-model="form.profile.birth_date"></div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-control" x-model="form.profile.gender"><option value="">Pilih</option><option value="male">Laki-laki</option><option value="female">Perempuan</option></select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Agama</label>
                            <select class="form-control" x-model="form.profile.religion"><option value="">Pilih</option><option>Islam</option><option>Kristen</option><option>Katolik</option><option>Hindu</option><option>Buddha</option><option>Konghucu</option></select>
                        </div>
                        <div class="form-group"><label class="form-label">No. HP</label><input type="text" class="form-control" x-model="form.profile.phone" placeholder="08xxxxxxxxxx"></div>
                        <div class="form-group"><label class="form-label">Email Profil</label><input type="email" class="form-control" x-model="form.profile.email" placeholder="email@sekolah.sch.id"></div>
                        <div class="form-group">
                            <label class="form-label">Status Kepegawaian</label>
                            <select class="form-control" x-model="form.profile.employment_status"><option value="">Pilih</option><option value="pns">PNS</option><option value="honorer">Honorer</option><option value="kontrak">Kontrak</option><option value="tetap">Tetap</option></select>
                        </div>
                        <div class="form-group"><label class="form-label">Jabatan</label><input type="text" class="form-control" x-model="form.profile.position" placeholder="Wali Kelas / Guru Mapel"></div>
                        <div class="form-group"><label class="form-label">Jenjang Kelas</label><input type="text" class="form-control" x-model="form.profile.grade_level" placeholder="X / XI / XII"></div>
                        <div class="form-group">
                            <label class="form-label">Pendidikan Terakhir</label>
                            <select class="form-control" x-model="form.profile.education_level"><option value="">Pilih</option><option value="S1">S1</option><option value="S2">S2</option><option value="S3">S3</option><option value="D3">D3</option></select>
                        </div>
                        <div class="form-group"><label class="form-label">Jurusan / Prodi</label><input type="text" class="form-control" x-model="form.profile.major" placeholder="Pendidikan Matematika"></div>
                        <div class="form-group"><label class="form-label">Sertifikasi</label><input type="text" class="form-control" x-model="form.profile.certification" placeholder="No. sertifikat (jika ada)"></div>
                        <div class="form-group"><label class="form-label">NPWP</label><input type="text" class="form-control" x-model="form.profile.npwp" placeholder="xx.xxx.xxx.x-xxx.xxx"></div>
                        <div class="form-group"><label class="form-label">Tanggal Bergabung</label><input type="date" class="form-control" x-model="form.profile.join_date"></div>
                    </div>
                    <div class="form-group" style="margin-top:var(--space-4,1rem)">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" rows="3" x-model="form.profile.address" placeholder="Alamat lengkap"></textarea>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════ SECTION 3 — PROFIL SISWA ═══════════════════════ --}}
            <div class="form-card" x-show="isStudentType" x-transition>
                <div class="form-card__header">
                    <div class="form-card__icon form-card__icon--blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </div>
                    <h2 class="form-card__title">Profil Siswa</h2>
                </div>
                <div class="form-card__body">
                    <div class="form-grid form-grid--3">
                        <div class="form-group"><label class="form-label">NIS</label><input type="text" class="form-control" x-model="form.profile.nis" placeholder="Nomor Induk Siswa"></div>
                        <div class="form-group"><label class="form-label">Nama Lengkap (Profil)</label><input type="text" class="form-control" x-model="form.profile.full_name" placeholder="Sesuai ijazah"></div>
                        <div class="form-group"><label class="form-label">Nama Panggilan</label><input type="text" class="form-control" x-model="form.profile.nick_name" placeholder="Nama panggilan"></div>
                        <div class="form-group"><label class="form-label">Email Siswa</label><input type="email" class="form-control" x-model="form.profile.email" placeholder="siswa@sekolah.sch.id"></div>
                        <div class="form-group"><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control" x-model="form.profile.birth_date"></div>
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-control" x-model="form.profile.gender"><option value="">Pilih</option><option value="male">Laki-laki</option><option value="female">Perempuan</option></select>
                        </div>
                        <div class="form-group"><label class="form-label">No. HP</label><input type="text" class="form-control" x-model="form.profile.phone_number" placeholder="08xxxxxxxxxx"></div>
                        <div class="form-group"><label class="form-label">Kota</label><input type="text" class="form-control" x-model="form.profile.city" placeholder="Kota domisili"></div>
                        <div class="form-group"><label class="form-label">Provinsi</label><input type="text" class="form-control" x-model="form.profile.province" placeholder="Provinsi"></div>
                        <div class="form-group"><label class="form-label">Kode Pos</label><input type="text" class="form-control" x-model="form.profile.postal_code" placeholder="12345"></div>
                        <div class="form-group"><label class="form-label">Kelas (Grade ID)</label><input type="text" class="form-control" x-model="form.profile.grade_id" placeholder="ID kelas"></div>
                        <div class="form-group"><label class="form-label">Rombel</label><input type="text" class="form-control" x-model="form.profile.class_group" placeholder="X-A / XI-IPA-1"></div>
                        <div class="form-group"><label class="form-label">Tanggal Masuk</label><input type="date" class="form-control" x-model="form.profile.enrollment_date"></div>
                        <div class="form-group"><label class="form-label">Tanggal Lulus</label><input type="date" class="form-control" x-model="form.profile.graduation_date"></div>
                    </div>
                    <div class="form-group" style="margin-top:var(--space-4,1rem)">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" rows="3" x-model="form.profile.address" placeholder="Alamat lengkap"></textarea>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════ ACTIONS ═══════════════════════ --}}
            <div class="form-actions" style="margin-top:10px">
                <a href="{{ route('users.index') }}" class="btn btn--secondary">Batal</a>
                <button 
                    type="submit" 
                    class="btn btn--primary" 
                    :disabled="submitting"
                    style="min-width:160px; justify-content:center">
                    <span x-show="!submitting" style="display:inline-flex;align-items:center;gap:.4375rem">
                        {{-- <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> --}}
                        Simpan User
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

{{-- @push('scripts')
<script type="module">
    import { userCreate } from '{{ asset('js/users.js') }}';
    window.userCreate = userCreate;
</script>
@endpush --}}