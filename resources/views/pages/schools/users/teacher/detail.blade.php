{{-- resources/views/pages/school-admin/teachers/detail.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Guru')

@push('styles')
<style>
@keyframes spin   { to { transform:rotate(360deg) } }
@keyframes fadeUp { from { opacity:0;transform:translateY(10px) } to { opacity:1;transform:none } }

.teacher-hero { background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px;animation:fadeUp .4s ease both; }
.hero-banner  { height:100px;background:linear-gradient(135deg,#064E3B 0%,#065F46 35%,#047857 65%,#34D399 100%);position:relative;overflow:hidden; }
.hero-banner::before { content:'';position:absolute;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,.04);top:-120px;right:-60px; }
.hero-body     { padding:60px 28px 24px; }
.hero-identity { display:flex;align-items:flex-end;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-top:-60px; }
.hero-left     { display:flex;align-items:flex-end;gap:14px; }
.teacher-avatar{ width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#065F46,#34D399);display:grid;place-items:center;font-size:26px;font-weight:700;color:#fff;border:4px solid var(--card);box-shadow:0 8px 24px rgba(6,95,70,.28);flex-shrink:0; }

.card-v2 { background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);animation:fadeUp .4s ease both; }
.card-v2.is-editing-card { border-color:#6EE7B7;box-shadow:0 0 0 3px rgba(16,185,129,.1),var(--shadow); }
.card-hd-v2 { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border); }
.card-hd-left-v2 { display:flex;align-items:center;gap:10px; }
.card-hd-icon-v2 { width:30px;height:30px;border-radius:8px;background:#F0FDF4;display:grid;place-items:center;font-size:14px;color:#059669;flex-shrink:0; }
.card-hd-title-v2 { font-size:13px;font-weight:700;color:var(--text-primary); }
.card-hd-sub-v2   { font-size:11px;color:var(--text-muted);margin-top:1px; }

.field-row-v2 { display:flex;align-items:flex-start;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);transition:background .15s; }
.field-row-v2:last-child { border-bottom:none; }
.field-row-v2.is-editing { background:#F0FDF4; }
.field-ico { width:30px;height:30px;border-radius:8px;background:#F0FDF4;display:grid;place-items:center;flex-shrink:0;margin-top:1px;font-size:13px;color:#059669;transition:all .15s; }
.field-row-v2.is-editing .field-ico { background:#059669;color:#fff; }
.field-content { flex:1;min-width:0; }
.field-lbl-v2 { font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:3px; }
.field-val-v2 { font-size:13.5px;font-weight:600;color:var(--text-primary);line-height:1.4; }
.field-val-v2.is-mono  { font-family:var(--font-mono);font-size:12.5px;letter-spacing:.3px; }
.field-val-v2.is-empty { color:var(--text-muted);font-style:italic;font-weight:400; }

.edit-input { width:100%;height:36px;padding:0 11px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--card);font-family:var(--font);font-size:13px;color:var(--text-primary);outline:none;transition:all .18s; }
.edit-input:focus { border-color:#10B981;box-shadow:0 0 0 3px rgba(16,185,129,.12); }
select.edit-input { appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='%2394A3B8'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 9px center;background-color:var(--card);cursor:pointer;padding-right:32px; }
textarea.edit-input { height:80px;padding:9px 11px;resize:vertical;line-height:1.55; }
.form-error { font-size:11px;color:var(--danger);margin-top:4px;display:block; }

.status-toggle-v2 { display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:var(--radius-sm);border:1.5px solid;cursor:pointer;transition:all .2s;margin-bottom:14px; }
.status-toggle-v2.active   { border-color:#6EE7B7;background:linear-gradient(135deg,#ECFDF5,#F0FDF4); }
.status-toggle-v2.inactive { border-color:#CBD5E1;background:var(--bg); }
.toggle-pill { width:42px;height:24px;border-radius:999px;position:relative;transition:background .25s;flex-shrink:0; }
.toggle-pill.on  { background:#10B981; }
.toggle-pill.off { background:#D1D5DB; }
.toggle-pill-thumb { position:absolute;top:4px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.2);transition:transform .25s; }
.toggle-pill-thumb.on  { transform:translateX(22px); }
.toggle-pill-thumb.off { transform:translateX(4px); }

.meta-list { display:flex;flex-direction:column;gap:8px;padding-top:12px;border-top:1px solid var(--border); }
.meta-item { display:flex;justify-content:space-between;align-items:center; }
.meta-item-lbl { font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:5px; }
.meta-item-val { font-size:12px;font-weight:600;color:var(--text-primary);font-family:var(--font-mono); }

.qa-btn { display:flex;align-items:center;gap:10px;width:100%;padding:11px 15px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;text-align:left;transition:all .15s; }
.qa-btn:hover { border-color:#6EE7B7;color:#065F46;background:#ECFDF5; }
.qa-btn i { font-size:16px;flex-shrink:0; }
.qa-btn.amber { border-color:#FDE68A;background:#FFFBEB;color:#92400E; }
.qa-btn.red   { border-color:#FECACA;background:#FEF2F2;color:var(--danger); }
.save-btn-v2 { display:flex;align-items:center;justify-content:center;gap:7px;width:100%;height:42px;border-radius:var(--radius-sm);border:none;background:#059669;color:#fff;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .18s;box-shadow:0 4px 14px rgba(5,150,105,.35); }
.save-btn-v2:hover:not(:disabled) { background:#047857;transform:translateY(-1px); }
.save-btn-v2:disabled { opacity:.6;cursor:not-allowed; }
.cancel-btn-v2 { display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:36px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);color:var(--text-secondary);font-family:var(--font);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s; }

.edit-banner-v2 { display:flex;align-items:center;gap:10px;padding:12px 18px;background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border:1px solid #6EE7B7;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13px;color:#065F46;font-weight:500;animation:fadeUp .25s ease both; }
.edit-banner-cancel { background:none;border:none;cursor:pointer;color:#065F46;font-weight:700;font-size:13px;text-decoration:underline;padding:0;margin-left:auto; }

.detail-grid { display:grid;grid-template-columns:1fr 1fr 280px;gap:18px;align-items:start; }
.detail-col  { display:flex;flex-direction:column;gap:18px; }

@media (max-width:1100px) { .detail-grid { grid-template-columns:1fr 1fr !important; } }
@media (max-width:720px)  { .detail-grid { grid-template-columns:1fr !important; } }
</style>
@endpush

@section('content')
<div
    x-data="teacherDetail({
        teacherData: {{ json_encode($teacher) }},
        baseUrl:     '{{ url('school-admin/teachers') }}',
        indexUrl:    '{{ route('school-admin.teachers.index') }}',
    })"
    x-init="init()">

    {{-- ── BREADCRUMB ─────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;animation:fadeUp .3s ease both">
        <div>
            <ul class="breadcrumb-list">
                <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><a href="{{ route('school-admin.teachers.index') }}" style="color:var(--text-muted);font-size:11px;font-weight:500;text-decoration:none">Manajemen Guru</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span x-text="teacher.profile?.full_name || teacher.name"></span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;margin-top:5px"
                x-text="teacher.profile?.full_name || teacher.name"></h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">Detail profil dan informasi guru</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a href="{{ route('school-admin.teachers.index') }}" class="dt-btn dt-btn-outline" style="height:38px">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <template x-if="!isEditing">
                <button @click="startEdit()" class="dt-btn dt-btn-primary" style="height:38px;background:#059669;border-color:#059669">
                    <i class="ri-pencil-line"></i> Edit Guru
                </button>
            </template>
            <template x-if="isEditing">
                <button @click="cancelEdit()" class="dt-btn dt-btn-outline" style="height:38px">
                    <i class="ri-close-line"></i> Batal Edit
                </button>
            </template>
        </div>
    </div>

    {{-- Edit Banner --}}
    <div class="edit-banner-v2" x-show="isEditing" x-transition>
        <i class="ri-edit-2-line" style="font-size:18px;flex-shrink:0"></i>
        <span>Mode edit aktif — ubah data yang diperlukan lalu klik <strong>Simpan Perubahan</strong>.</span>
        <button class="edit-banner-cancel" @click="cancelEdit()">Batalkan</button>
    </div>

    {{-- ── HERO ─────────────────────────── --}}
    <div class="teacher-hero">
        <div class="hero-banner"></div>
        <div class="hero-body">
            <div class="hero-identity">
                <div class="hero-left">
                    <template x-if="teacher.profile_picture">
                        <img :src="teacher.profile_picture" :alt="teacher.name"
                            style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:4px solid var(--card);box-shadow:0 8px 24px rgba(0,0,0,.15)">
                    </template>
                    <template x-if="!teacher.profile_picture">
                        <div class="teacher-avatar" x-text="initials(teacher.profile?.full_name || teacher.name)"></div>
                    </template>
                    <div style="padding-bottom:4px">
                        <div style="font-size:20px;font-weight:800;color:var(--text-primary);line-height:1.2"
                            x-text="teacher.profile?.full_name || teacher.name"></div>
                        <div style="font-size:13px;color:var(--text-muted);margin-top:3px;display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                            <template x-if="teacher.profile?.position">
                                <span x-text="teacher.profile.position"></span>
                            </template>
                            <template x-if="teacher.profile?.nip">
                                <span>· NIP: <span class="dt-mono" x-text="teacher.profile.nip"></span></span>
                            </template>
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:4px;flex-wrap:wrap">
                    <template x-if="teacher.profile?.employment_status">
                        <span style="padding:5px 14px;border-radius:999px;background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;font-size:12px;font-weight:700"
                            x-text="teacher.profile.employment_status"></span>
                    </template>
                    <span style="padding:5px 14px;border-radius:999px;font-size:12px;font-weight:700;border:1px solid"
                        :style="teacher.status === 'active'
                            ? 'background:#ECFDF5;color:#059669;border-color:#6EE7B7'
                            : 'background:#FFF7ED;color:#D97706;border-color:#FDE68A'"
                        x-text="teacher.status === 'active' ? 'Aktif' : 'Non-Aktif'">
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── DETAIL GRID ──────────────────────── --}}
    <div class="detail-grid">

        {{-- ═══ KOLOM KIRI — Informasi Akun + Data Pribadi ═══ --}}
        <div class="detail-col">

            {{-- Informasi Akun --}}
            <div class="card-v2" :class="{ 'is-editing-card': isEditing }">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-account-circle-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Informasi Akun</div>
                            <div class="card-hd-sub-v2" x-show="isEditing">Mode Edit</div>
                        </div>
                    </div>
                </div>

                {{-- Nama Akun --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-user-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Nama Akun</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.name ? '' : 'is-empty'" x-text="teacher.name || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <div>
                                <input class="edit-input" type="text" x-model="form.name" data-edit-focus placeholder="Nama akun">
                                <span class="form-error" x-show="errors.name" x-text="errors.name"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Email Akun --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-mail-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Email Akun</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="teacher.email ? '' : 'is-empty'" x-text="teacher.email || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <div>
                                <input class="edit-input" type="email" x-model="form.email" placeholder="Email akun">
                                <span class="form-error" x-show="errors.email" x-text="errors.email"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- No. Telepon Akun --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-phone-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">No. Telepon (Akun)</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.phone_number ? '' : 'is-empty'" x-text="teacher.phone_number || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.phone_number" placeholder="08xxxxxxxxxx">
                        </template>
                    </div>
                </div>

                {{-- Password (edit only) --}}
                <template x-if="isEditing">
                    <div>
                        <div class="field-row-v2 is-editing">
                            <div class="field-ico"><i class="ri-lock-password-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Password Baru <span style="font-weight:400;text-transform:none;letter-spacing:0">(kosongkan jika tidak diubah)</span></div>
                                <input class="edit-input" type="password" x-model="form.password" placeholder="Min. 8 karakter">
                                <span class="form-error" x-show="errors.password" x-text="errors.password"></span>
                            </div>
                        </div>
                        <div class="field-row-v2 is-editing">
                            <div class="field-ico"><i class="ri-lock-2-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Konfirmasi Password</div>
                                <input class="edit-input" type="password" x-model="form.password_confirmation" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Data Pribadi --}}
            <div class="card-v2" :class="{ 'is-editing-card': isEditing }" style="animation-delay:.03s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-user-2-line"></i></div>
                        <div><div class="card-hd-title-v2">Data Pribadi</div></div>
                    </div>
                </div>

                {{-- Nama Lengkap --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-profile-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Nama Lengkap</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.full_name ? '' : 'is-empty'" x-text="teacher.profile?.full_name || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.full_name" placeholder="Nama sesuai KTP/SK">
                        </template>
                    </div>
                </div>

                {{-- NIP --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-id-card-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">NIP</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="teacher.profile?.nip ? '' : 'is-empty'" x-text="teacher.profile?.nip || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.nip" placeholder="Nomor Induk Pegawai">
                        </template>
                    </div>
                </div>

                {{-- NIK --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-contacts-book-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">NIK (KTP)</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="teacher.profile?.nik ? '' : 'is-empty'" x-text="teacher.profile?.nik || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.nik" placeholder="16 digit NIK">
                        </template>
                    </div>
                </div>

                {{-- Tempat, Tanggal Lahir --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-cake-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tempat, Tanggal Lahir</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2"
                                :class="(teacher.profile?.birth_place || teacher.profile?.birth_date) ? '' : 'is-empty'"
                                x-text="[
                                    teacher.profile?.birth_place,
                                    teacher.profile?.birth_date ? new Date(teacher.profile.birth_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : null
                                ].filter(Boolean).join(', ') || 'Belum diisi'">
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                <input class="edit-input" type="text" x-model="form.profile.birth_place" placeholder="Kota lahir">
                                <input class="edit-input" type="date" x-model="form.profile.birth_date">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Gender --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-genderless-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Jenis Kelamin</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.gender ? '' : 'is-empty'"
                                x-text="{male:'Laki-laki', female:'Perempuan'}[teacher.profile?.gender] || 'Belum diisi'">
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <select class="edit-input" x-model="form.profile.gender">
                                <option value="">— Pilih —</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </template>
                    </div>
                </div>

                {{-- Agama --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-book-open-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Agama</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.religion ? '' : 'is-empty'" x-text="teacher.profile?.religion || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <select class="edit-input" x-model="form.profile.religion">
                                <option value="">— Pilih —</option>
                                <option>Islam</option><option>Kristen</option><option>Katolik</option>
                                <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                            </select>
                        </template>
                    </div>
                </div>

                {{-- No. HP Pribadi --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-smartphone-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">No. HP Pribadi</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.phone ? '' : 'is-empty'" x-text="teacher.profile?.phone || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.phone" placeholder="08xxxxxxxxxx">
                        </template>
                    </div>
                </div>

                {{-- Email Pribadi --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-mail-send-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Email Pribadi</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="teacher.profile?.email ? '' : 'is-empty'" x-text="teacher.profile?.email || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="email" x-model="form.profile.email" placeholder="Email pribadi">
                        </template>
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-map-pin-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Alamat</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" style="white-space:pre-line"
                                :class="teacher.profile?.address ? '' : 'is-empty'"
                                x-text="teacher.profile?.address || 'Belum diisi'">
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <textarea class="edit-input" x-model="form.profile.address" placeholder="Alamat lengkap"></textarea>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ KOLOM TENGAH — Data Kepegawaian + Sekolah ═══ --}}
        <div class="detail-col">

            {{-- Data Kepegawaian --}}
            <div class="card-v2" :class="{ 'is-editing-card': isEditing }" style="animation-delay:.04s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-briefcase-line"></i></div>
                        <div><div class="card-hd-title-v2">Data Kepegawaian</div></div>
                    </div>
                </div>

                {{-- Status Kepegawaian --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-government-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Status Kepegawaian</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.employment_status ? '' : 'is-empty'" x-text="teacher.profile?.employment_status || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.employment_status" placeholder="PNS, Honorer, GTY, dll">
                        </template>
                    </div>
                </div>

                {{-- Jabatan --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-award-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Jabatan</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.position ? '' : 'is-empty'" x-text="teacher.profile?.position || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.position" placeholder="Guru Kelas, Wali Kelas, dll">
                        </template>
                    </div>
                </div>

                {{-- Grade Level --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-bar-chart-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tingkat Kelas</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.grade_level ? '' : 'is-empty'" x-text="teacher.profile?.grade_level || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.grade_level" placeholder="SD, SMP, SMA, dll">
                        </template>
                    </div>
                </div>

                {{-- Pendidikan Terakhir --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-graduation-cap-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Pendidikan Terakhir</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.education_level ? '' : 'is-empty'" x-text="teacher.profile?.education_level || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <select class="edit-input" x-model="form.profile.education_level">
                                <option value="">— Pilih —</option>
                                <option>S1</option><option>S2</option><option>S3</option>
                                <option>D4</option><option>D3</option><option>SMA/SMK</option>
                            </select>
                        </template>
                    </div>
                </div>

                {{-- Jurusan --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-book-2-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Jurusan / Prodi</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.major ? '' : 'is-empty'" x-text="teacher.profile?.major || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.major" placeholder="Pendidikan Matematika, dll">
                        </template>
                    </div>
                </div>

                {{-- Sertifikasi --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-shield-star-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">No. Sertifikasi</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="teacher.profile?.certification ? '' : 'is-empty'" x-text="teacher.profile?.certification || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.certification" placeholder="No. sertifikat pendidik">
                        </template>
                    </div>
                </div>

                {{-- NPWP --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-bank-card-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">NPWP</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="teacher.profile?.npwp ? '' : 'is-empty'" x-text="teacher.profile?.npwp || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="text" x-model="form.profile.npwp" placeholder="Nomor NPWP">
                        </template>
                    </div>
                </div>

                {{-- Tanggal Bergabung --}}
                <div class="field-row-v2" :class="{ 'is-editing': isEditing }">
                    <div class="field-ico"><i class="ri-calendar-check-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tanggal Bergabung</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="teacher.profile?.join_date ? '' : 'is-empty'"
                                x-text="teacher.profile?.join_date
                                    ? new Date(teacher.profile.join_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'})
                                    : 'Belum diisi'">
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <input class="edit-input" type="date" x-model="form.profile.join_date">
                        </template>
                    </div>
                </div>
            </div>

            {{-- Sekolah --}}
            <div class="card-v2" style="animation-delay:.08s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-building-2-line"></i></div>
                        <div><div class="card-hd-title-v2">Sekolah</div></div>
                    </div>
                </div>
                <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px">
                    <template x-if="teacher.schools && teacher.schools.length > 0">
                        <template x-for="school in teacher.schools" :key="school.school_id">
                            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg)">
                                <i class="ri-building-2-line" style="color:#059669;font-size:14px;flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:600;font-size:13px" x-text="school.school_name"></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:1px" x-text="school.school_type || ''"></div>
                                </div>
                                <span class="dt-badge" :class="school.status === 'active' ? 'aktif' : 'nonaktif'"
                                    x-text="school.status === 'active' ? 'Aktif' : 'Non-Aktif'" style="font-size:10px"></span>
                            </div>
                        </template>
                    </template>
                    <template x-if="!teacher.schools || teacher.schools.length === 0">
                        <p style="font-size:13px;color:var(--text-muted);font-style:italic;margin:4px 0">Belum terhubung ke sekolah.</p>
                    </template>
                </div>
            </div>

        </div>

        {{-- ═══ KOLOM KANAN — Status + Aksi ═══ --}}
        <div class="detail-col">

            {{-- Status --}}
            <div class="card-v2" style="animation-delay:.06s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-pulse-line"></i></div>
                        <div><div class="card-hd-title-v2">Status Akun</div></div>
                    </div>
                </div>
                <div style="padding:16px">
                    <div class="status-toggle-v2" :class="teacher.status === 'active' ? 'active' : 'inactive'" @click="toggleStatus()">
                        <div>
                            <div style="font-size:14px;font-weight:700"
                                :style="teacher.status === 'active' ? 'color:#065F46' : 'color:#475569'"
                                x-text="teacher.status === 'active' ? 'Aktif' : 'Non-Aktif'"></div>
                            <div style="font-size:11px;color:var(--text-muted)">Klik untuk ubah status</div>
                        </div>
                        <div class="toggle-pill" :class="teacher.status === 'active' ? 'on' : 'off'">
                            <div class="toggle-pill-thumb" :class="teacher.status === 'active' ? 'on' : 'off'"></div>
                        </div>
                    </div>
                    <div class="meta-list">
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-shield-check-line"></i> Email</div>
                            <div class="meta-item-val" :style="teacher.is_verified ? 'color:#059669' : 'color:#D97706'"
                                x-text="teacher.is_verified ? 'Terverifikasi' : 'Belum'"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-calendar-event-line"></i> Bergabung</div>
                            <div class="meta-item-val"
                                x-text="teacher.created_at ? new Date(teacher.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-refresh-line"></i> Diperbarui</div>
                            <div class="meta-item-val"
                                x-text="teacher.updated_at ? new Date(teacher.updated_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="card-v2" style="animation-delay:.1s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-flashlight-line"></i></div>
                        <div><div class="card-hd-title-v2">Aksi Cepat</div></div>
                    </div>
                </div>
                <div style="padding:14px;display:flex;flex-direction:column;gap:8px">
                    <template x-if="!isEditing">
                        <button @click="startEdit()" class="qa-btn">
                            <i class="ri-pencil-fill"></i> Edit Informasi Guru
                        </button>
                    </template>
                    <template x-if="isEditing">
                        <div style="display:flex;flex-direction:column;gap:6px">
                            <button @click="submitEdit()" :disabled="submitting" class="save-btn-v2">
                                <svg x-show="submitting" style="width:15px;height:15px;animation:spin 1s linear infinite;flex-shrink:0" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3.5" style="opacity:.25"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.8"></path>
                                </svg>
                                <i x-show="!submitting" class="ri-save-2-fill" style="font-size:15px"></i>
                                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                            <button @click="cancelEdit()" class="cancel-btn-v2">
                                <i class="ri-close-line"></i> Batal
                            </button>
                        </div>
                    </template>

                    <button @click="toggleStatus()" class="qa-btn"
                        :class="teacher.status === 'active' ? 'amber' : ''"
                        :style="teacher.status !== 'active' ? 'border-color:#6EE7B7;background:#ECFDF5;color:#065F46' : ''">
                        <i :class="teacher.status === 'active' ? 'ri-pause-circle-line' : 'ri-play-circle-line'"></i>
                        <span x-text="teacher.status === 'active' ? 'Nonaktifkan Guru' : 'Aktifkan Guru'"></span>
                    </button>

                    <button @click="deleteTeacher()" class="qa-btn red">
                        <i class="ri-delete-bin-6-fill"></i> Hapus Guru
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection