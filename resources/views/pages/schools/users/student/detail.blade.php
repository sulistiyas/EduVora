{{-- resources/views/pages/school-admin/students/detail.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Siswa')

@push('styles')
<style>
@keyframes spin   { to { transform:rotate(360deg) } }
@keyframes fadeUp { from { opacity:0;transform:translateY(10px) } to { opacity:1;transform:none } }

.student-hero { background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px;animation:fadeUp .4s ease both; }
.hero-banner  { height:100px;background:linear-gradient(135deg,#0C4A6E 0%,#0369A1 35%,#0284C7 65%,#38BDF8 100%);position:relative;overflow:hidden; }
.hero-banner::before { content:'';position:absolute;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,.04);top:-120px;right:-60px; }
.hero-body     { padding:60px 28px 24px; }
.hero-identity { display:flex;align-items:flex-end;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-top:-60px; }
.hero-left     { display:flex;align-items:flex-end;gap:14px; }
.student-avatar{ width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#0369A1,#38BDF8);display:grid;place-items:center;font-size:26px;font-weight:700;color:#fff;border:4px solid var(--card);box-shadow:0 8px 24px rgba(3,105,161,.28);flex-shrink:0; }

.card-v2 { background:var(--card);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);animation:fadeUp .4s ease both; }
.card-v2.is-editing-card { border-color:#93C5FD;box-shadow:0 0 0 3px rgba(59,130,246,.1),var(--shadow); }
.card-hd-v2 { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border); }
.card-hd-left-v2 { display:flex;align-items:center;gap:10px; }
.card-hd-icon-v2 { width:30px;height:30px;border-radius:8px;background:var(--primary-xlight);display:grid;place-items:center;font-size:14px;color:var(--primary);flex-shrink:0; }
.card-hd-title-v2 { font-size:13px;font-weight:700;color:var(--text-primary); }
.card-hd-sub-v2   { font-size:11px;color:var(--text-muted);margin-top:1px; }

.field-row-v2 { display:flex;align-items:flex-start;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);transition:background .15s; }
.field-row-v2:last-child { border-bottom:none; }
.field-row-v2.is-editing { background:#F0F7FF; }
.field-ico { width:30px;height:30px;border-radius:8px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0;margin-top:1px;font-size:13px;color:var(--primary);transition:all .15s; }
.field-row-v2.is-editing .field-ico { background:var(--primary);color:#fff; }
.field-content { flex:1;min-width:0; }
.field-lbl-v2 { font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:3px; }
.field-val-v2 { font-size:13.5px;font-weight:600;color:var(--text-primary);line-height:1.4; }
.field-val-v2.is-mono  { font-family:var(--font-mono);font-size:12.5px;letter-spacing:.3px; }
.field-val-v2.is-empty { color:var(--text-muted);font-style:italic;font-weight:400; }

.edit-input { width:100%;height:36px;padding:0 11px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--card);font-family:var(--font);font-size:13px;color:var(--text-primary);outline:none;transition:all .18s; }
.edit-input:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(59,130,246,.12); }
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
.qa-btn:hover { border-color:var(--primary-light);color:var(--primary);background:var(--primary-xlight); }
.qa-btn i { font-size:16px;flex-shrink:0; }
.qa-btn.amber { border-color:#FDE68A;background:#FFFBEB;color:#92400E; }
.qa-btn.red   { border-color:#FECACA;background:#FEF2F2;color:var(--danger); }
.save-btn-v2 { display:flex;align-items:center;justify-content:center;gap:7px;width:100%;height:42px;border-radius:var(--radius-sm);border:none;background:var(--primary);color:#fff;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .18s;box-shadow:0 4px 14px rgba(59,130,246,.35); }
.save-btn-v2:hover:not(:disabled) { background:var(--primary-dark);transform:translateY(-1px); }
.save-btn-v2:disabled { opacity:.6;cursor:not-allowed; }
.cancel-btn-v2 { display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:36px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);color:var(--text-secondary);font-family:var(--font);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s; }

.edit-banner-v2 { display:flex;align-items:center;gap:10px;padding:12px 18px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #BFDBFE;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13px;color:#1D4ED8;font-weight:500;animation:fadeUp .25s ease both; }
.edit-banner-cancel { background:none;border:none;cursor:pointer;color:#1D4ED8;font-weight:700;font-size:13px;text-decoration:underline;padding:0;margin-left:auto; }

.detail-grid { display:grid;grid-template-columns:1fr 1fr 280px;gap:18px;align-items:start; }
.detail-col  { display:flex;flex-direction:column;gap:18px; }

@media (max-width:1100px) { .detail-grid { grid-template-columns:1fr 1fr !important; } }
@media (max-width:720px)  { .detail-grid { grid-template-columns:1fr !important; } }
</style>
@endpush

@section('content')
<div
    x-data="studentDetail({
        studentData: {{ json_encode($student) }},
        baseUrl:     '{{ url('school-admin/students') }}',
        indexUrl:    '{{ route('school-admin.students.index') }}',
    })"
    x-init="init()">

    {{-- ── BREADCRUMB & HEADER ───────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;animation:fadeUp .3s ease both">
        <div>
            <ul class="breadcrumb-list">
                <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><a href="{{ route('school-admin.students.index') }}" style="color:var(--text-muted);font-size:11px;font-weight:500;text-decoration:none">Manajemen Siswa</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span x-text="student.name"></span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;margin-top:5px" x-text="student.name"></h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">Detail profil dan informasi siswa</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a href="{{ route('school-admin.students.index') }}" class="dt-btn dt-btn-outline" style="height:38px">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <template x-if="!isEditing">
                <button @click="startEdit()" class="dt-btn dt-btn-primary" style="height:38px">
                    <i class="ri-pencil-line"></i> Edit Siswa
                </button>
            </template>
            <template x-if="isEditing">
                <div style="display:flex;gap:8px">
                    <button @click="cancelEdit()" style="display:inline-flex;align-items:center;gap:6px;height:38px;padding:0 16px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer">
                        <i class="ri-close-line"></i> Batal
                    </button>
                    <button @click="submitEdit()" :disabled="submitting" class="dt-btn dt-btn-primary" style="height:38px">
                        <svg x-show="submitting" style="width:14px;height:14px;animation:spin 1s linear infinite;flex-shrink:0" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.75"></path>
                        </svg>
                        <i x-show="!submitting" class="ri-save-2-line"></i>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    {{-- Edit Banner --}}
    <div x-show="isEditing" x-transition class="edit-banner-v2" style="display:none">
        <i class="ri-edit-2-fill"></i>
        <span>Mode edit aktif — ubah data yang ingin diperbarui, lalu klik <strong>Simpan Perubahan</strong></span>
        <button @click="cancelEdit()" class="edit-banner-cancel">Batal</button>
    </div>

    {{-- ══ HERO CARD ════════════════════════════════════ --}}
    <div class="student-hero">
        <div class="hero-banner"></div>
        <div class="hero-body">
            <div class="hero-identity">
                <div class="hero-left">
                    <div class="student-avatar" x-text="initials(student.name)"></div>
                    <div style="padding-bottom:6px">
                        <div x-show="!isEditing" style="font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;margin-bottom:6px" x-text="student.name"></div>
                        <div x-show="isEditing" style="margin-bottom:8px">
                            <input x-model="form.name" data-edit-focus type="text" class="edit-input"
                                placeholder="Nama siswa" style="font-size:15px;font-weight:700;height:42px;min-width:260px;max-width:380px">
                            <div x-show="errors.name" class="form-error" x-text="errors.name"></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                            <span style="display:inline-block;padding:3px 12px;border-radius:999px;font-size:11.5px;font-weight:700;background:#EFF6FF;color:#1D4ED8">
                                <i class="ri-book-open-line"></i> Student
                            </span>
                            <span class="dt-badge" :class="student.status === 'active' ? 'aktif' : 'nonaktif'"
                                x-text="student.status === 'active' ? '● Aktif' : '● Non-Aktif'"></span>
                            <span x-show="student.profile?.class_group"
                                style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:#F0FDF4;color:#15803D">
                                <i class="ri-door-open-line"></i>
                                <span x-text="student.profile?.class_group"></span>
                            </span>
                            <span x-show="student.profile?.nis"
                                style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:#F8FAFC;color:#475569;font-family:var(--font-mono)">
                                NIS: <span x-text="student.profile?.nis"></span>
                            </span>
                        </div>
                    </div>
                </div>
                {{-- Stats strip --}}
                <div style="display:flex;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;flex-shrink:0;margin-bottom:6px;align-self:flex-end">
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:100px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-primary);font-family:var(--font-mono);line-height:1" x-text="student.phone_number || '—'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Telepon</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:90px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="student.profile?.enrollment_date ? new Date(student.profile.enrollment_date).getFullYear() : '—'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Angkatan</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;min-width:110px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="student.created_at ? new Date(student.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Bergabung</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MAIN GRID ════════════════════════════════════ --}}
    <div class="detail-grid">

        {{-- ═══ KOLOM KIRI ═══ --}}
        <div class="detail-col">

            {{-- Data Akun --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-account-circle-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Data Akun</div>
                            <div class="card-hd-sub-v2">Informasi login</div>
                        </div>
                    </div>
                </div>
                {{-- Email --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-mail-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Email</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" x-text="student.email || '—'"></div>
                        </template>
                        <template x-if="isEditing">
                            <div>
                                <input x-model="form.email" type="email" class="edit-input" placeholder="email@domain.com">
                                <div x-show="errors.email" class="form-error" x-text="errors.email"></div>
                            </div>
                        </template>
                    </div>
                </div>
                {{-- Telepon --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-phone-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Nomor Telepon</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.phone_number ? 'is-empty' : ''" x-text="student.phone_number || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.phone_number" type="tel" class="edit-input" placeholder="+62 ...">
                        </template>
                    </div>
                </div>
                {{-- Status (edit) --}}
                <template x-if="isEditing">
                    <div class="field-row-v2 is-editing">
                        <div class="field-ico" style="background:var(--primary);color:#fff"><i class="ri-pulse-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Status</div>
                            <select x-model="form.status" class="edit-input">
                                <option value="active">Aktif</option>
                                <option value="inactive">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </template>
                {{-- Password (edit) --}}
                <template x-if="isEditing">
                    <div>
                        <div class="field-row-v2 is-editing">
                            <div class="field-ico" style="background:var(--primary);color:#fff"><i class="ri-lock-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Password Baru <span style="font-weight:400;color:var(--text-muted)">(opsional)</span></div>
                                <input x-model="form.password" type="password" class="edit-input" placeholder="Kosongkan jika tidak diubah">
                                <div x-show="errors.password" class="form-error" x-text="errors.password"></div>
                            </div>
                        </div>
                        <div class="field-row-v2 is-editing">
                            <div class="field-ico" style="background:var(--primary);color:#fff"><i class="ri-lock-password-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Konfirmasi Password</div>
                                <input x-model="form.password_confirmation" type="password" class="edit-input" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Data Akademik --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.06s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-book-open-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Data Akademik</div>
                            <div class="card-hd-sub-v2">Informasi sekolah siswa</div>
                        </div>
                    </div>
                </div>
                {{-- NIS --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-id-card-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">NIS</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" :class="!student.profile?.nis ? 'is-empty' : ''" x-text="student.profile?.nis || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.nis" type="text" class="edit-input" placeholder="Nomor Induk Siswa" style="font-family:var(--font-mono)">
                        </template>
                    </div>
                </div>
                {{-- Nama Lengkap Profil --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-user-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Nama Lengkap (Profil)</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.full_name ? 'is-empty' : ''" x-text="student.profile?.full_name || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.full_name" type="text" class="edit-input" placeholder="Sesuai ijazah">
                        </template>
                    </div>
                </div>
                {{-- Kelas --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-door-open-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Kelas / Rombel</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.class_group ? 'is-empty' : ''" x-text="student.profile?.class_group || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.class_group" type="text" class="edit-input" placeholder="X-A, XI IPA 2, dll">
                        </template>
                    </div>
                </div>
                {{-- Tanggal Masuk --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-calendar-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tanggal Masuk</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.enrollment_date ? 'is-empty' : ''"
                                x-text="student.profile?.enrollment_date ? new Date(student.profile.enrollment_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.enrollment_date" type="date" class="edit-input">
                        </template>
                    </div>
                </div>
                {{-- Tanggal Lulus --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-calendar-check-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tanggal Lulus</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.graduation_date ? 'is-empty' : ''"
                                x-text="student.profile?.graduation_date ? new Date(student.profile.graduation_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.graduation_date" type="date" class="edit-input">
                        </template>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ KOLOM TENGAH ═══ --}}
        <div class="detail-col">

            {{-- Data Pribadi --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.08s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-profile-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Data Pribadi</div>
                            <div class="card-hd-sub-v2">Informasi kependudukan</div>
                        </div>
                    </div>
                </div>
                {{-- Nama Panggilan --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-user-smile-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Nama Panggilan</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.nick_name ? 'is-empty' : ''" x-text="student.profile?.nick_name || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.nick_name" type="text" class="edit-input" placeholder="Nama panggilan">
                        </template>
                    </div>
                </div>
                {{-- Tgl Lahir --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-cake-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tanggal Lahir</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.birth_date ? 'is-empty' : ''"
                                x-text="student.profile?.birth_date ? new Date(student.profile.birth_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.birth_date" type="date" class="edit-input">
                        </template>
                    </div>
                </div>
                {{-- Jenis Kelamin --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-men-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Jenis Kelamin</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.gender ? 'is-empty' : ''"
                                x-text="student.profile?.gender === 'male' ? 'Laki-laki' : student.profile?.gender === 'female' ? 'Perempuan' : 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <select x-model="form.profile.gender" class="edit-input">
                                <option value="">— Pilih —</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </template>
                    </div>
                </div>
                {{-- No HP Siswa --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-phone-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">No. HP Siswa</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.phone_number ? 'is-empty' : ''" x-text="student.profile?.phone_number || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.phone_number" type="tel" class="edit-input" placeholder="08xxxxxxxxxx">
                        </template>
                    </div>
                </div>
                {{-- Alamat --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-home-4-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Alamat</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" style="line-height:1.6" :class="!student.profile?.address ? 'is-empty' : ''" x-text="student.profile?.address || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <textarea x-model="form.profile.address" class="edit-input" placeholder="Alamat lengkap..."></textarea>
                        </template>
                    </div>
                </div>
                {{-- Kota --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-building-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Kota</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.city ? 'is-empty' : ''" x-text="student.profile?.city || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.city" type="text" class="edit-input" placeholder="Kota">
                        </template>
                    </div>
                </div>
                {{-- Provinsi --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-map-2-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Provinsi</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2" :class="!student.profile?.province ? 'is-empty' : ''" x-text="student.profile?.province || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.profile.province" type="text" class="edit-input" placeholder="Provinsi">
                        </template>
                    </div>
                </div>
            </div>

            {{-- Sekolah --}}
            <div class="card-v2" style="animation-delay:.12s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-school-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Sekolah</div>
                            <div class="card-hd-sub-v2" x-text="`${student.schools?.length ?? 0} sekolah`"></div>
                        </div>
                    </div>
                </div>
                <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px">
                    <template x-if="student.schools && student.schools.length > 0">
                        <template x-for="school in student.schools" :key="school.school_id">
                            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg)">
                                <i class="ri-building-2-line" style="color:var(--primary);font-size:14px;flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:600;font-size:13px" x-text="school.school_name"></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:1px" x-text="school.school_type || ''"></div>
                                </div>
                                <span class="dt-badge" :class="school.status === 'active' ? 'aktif' : 'nonaktif'"
                                    x-text="school.status === 'active' ? 'Aktif' : 'Non-Aktif'" style="font-size:10px"></span>
                            </div>
                        </template>
                    </template>
                </div>
            </div>

        </div>

        {{-- ═══ KOLOM KANAN ═══ --}}
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
                    <div class="status-toggle-v2" :class="student.status === 'active' ? 'active' : 'inactive'" @click="toggleStatus()">
                        <div>
                            <div style="font-size:14px;font-weight:700" :style="student.status === 'active' ? 'color:#065F46' : 'color:#475569'"
                                x-text="student.status === 'active' ? 'Aktif' : 'Non-Aktif'"></div>
                            <div style="font-size:11px;color:var(--text-muted)">Klik untuk ubah status</div>
                        </div>
                        <div class="toggle-pill" :class="student.status === 'active' ? 'on' : 'off'">
                            <div class="toggle-pill-thumb" :class="student.status === 'active' ? 'on' : 'off'"></div>
                        </div>
                    </div>
                    <div class="meta-list">
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-shield-check-line"></i> Email</div>
                            <div class="meta-item-val" :style="student.is_verified ? 'color:#059669' : 'color:#D97706'"
                                x-text="student.is_verified ? 'Terverifikasi' : 'Belum'"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-calendar-event-line"></i> Bergabung</div>
                            <div class="meta-item-val"
                                x-text="student.created_at ? new Date(student.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-refresh-line"></i> Diperbarui</div>
                            <div class="meta-item-val"
                                x-text="student.updated_at ? new Date(student.updated_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
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
                            <i class="ri-pencil-fill"></i> Edit Informasi Siswa
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
                        :class="student.status === 'active' ? 'amber' : ''"
                        :style="student.status !== 'active' ? 'border-color:#A7F3D0;background:#ECFDF5;color:#065F46' : ''">
                        <i :class="student.status === 'active' ? 'ri-pause-circle-line' : 'ri-play-circle-line'"></i>
                        <span x-text="student.status === 'active' ? 'Nonaktifkan Siswa' : 'Aktifkan Siswa'"></span>
                    </button>

                    <button @click="deleteStudent()" class="qa-btn red">
                        <i class="ri-delete-bin-6-fill"></i> Hapus Siswa
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection