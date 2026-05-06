@extends('layouts.app')

@section('title', 'Detail User')

@push('styles')
<style>
@keyframes spin   { to { transform:rotate(360deg) } }
@keyframes fadeUp { from { opacity:0;transform:translateY(10px) } to { opacity:1;transform:none } }

/* ── HERO ──────────────────────────────────── */
.user-hero {
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow);
    overflow:hidden;margin-bottom:20px;animation:fadeUp .4s ease both;
}
.hero-banner {
    height:100px;
    background:linear-gradient(135deg,#0F2557 0%,#1E3A8A 35%,#2563EB 65%,#3B82F6 85%,#60A5FA 100%);
    position:relative;overflow:hidden;
}
.hero-banner::before {
    content:'';position:absolute;width:280px;height:280px;border-radius:50%;
    background:rgba(255,255,255,.04);top:-120px;right:-60px;
}
.hero-body { padding:60px 28px 24px; }
.hero-identity {
    display:flex;align-items:flex-end;justify-content:space-between;
    gap:20px;flex-wrap:wrap;margin-top:-60px;
}
.hero-left { display:flex;align-items:flex-end;gap:14px; }
.user-avatar {
    width:80px;height:80px;border-radius:50%;
    background:linear-gradient(135deg,#1E3A8A,#3B82F6);
    display:grid;place-items:center;font-size:26px;font-weight:700;color:#fff;
    border:4px solid var(--card);box-shadow:0 8px 24px rgba(30,58,138,.28);flex-shrink:0;
}
.hero-name { font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-bottom:6px; }
.hero-badges { display:flex;align-items:center;gap:6px;flex-wrap:wrap; }

/* ── ROLE BADGE ────────────────────────────── */
.role-badge {
    display:inline-flex;align-items:center;gap:5px;
    padding:3px 12px;border-radius:999px;font-size:11.5px;font-weight:700;
}

/* ── CARD V2 (reuse dari school) ───────────── */
.card-v2 {
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow);
    overflow:visible;
    transition:border-color .2s,box-shadow .2s;animation:fadeUp .4s ease both;
}
.card-v2.is-editing-card { border-color:#93C5FD;box-shadow:0 0 0 3px rgba(59,130,246,.1),var(--shadow); }
.card-hd-v2 {
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 20px;border-bottom:1px solid var(--border);background:var(--card);
}
.card-hd-left-v2 { display:flex;align-items:center;gap:10px; }
.card-hd-icon-v2 {
    width:30px;height:30px;border-radius:8px;background:var(--primary-xlight);
    display:grid;place-items:center;font-size:14px;color:var(--primary);flex-shrink:0;
}
.card-hd-title-v2 { font-size:13px;font-weight:700;color:var(--text-primary); }
.card-hd-sub-v2   { font-size:11px;color:var(--text-muted);margin-top:1px; }

/* ── FIELD ROW ─────────────────────────────── */
.field-row-v2 {
    display:flex;align-items:flex-start;gap:12px;
    padding:12px 20px;border-bottom:1px solid var(--border);transition:background .15s;
}
.field-row-v2:last-child { border-bottom:none; }
.field-row-v2.is-editing { background:#F0F7FF; }
.field-ico {
    width:30px;height:30px;border-radius:8px;background:var(--primary-xlight);
    display:grid;place-items:center;flex-shrink:0;margin-top:1px;
    font-size:13px;color:var(--primary);transition:all .15s;
}
.field-row-v2.is-editing .field-ico { background:var(--primary);color:#fff; }
.field-content { flex:1;min-width:0; }
.field-lbl-v2 { font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:3px; }
.field-val-v2 { font-size:13.5px;font-weight:600;color:var(--text-primary);line-height:1.4; }
.field-val-v2.is-mono { font-family:var(--font-mono);font-size:12.5px;letter-spacing:.3px; }
.field-val-v2.is-empty { color:var(--text-muted);font-style:italic;font-weight:400; }

/* ── EDIT INPUT ────────────────────────────── */
.edit-input {
    width:100%;height:36px;padding:0 11px;
    border:1.5px solid var(--border);border-radius:var(--radius-sm);
    background:var(--card);font-family:var(--font);font-size:13px;
    color:var(--text-primary);outline:none;transition:all .18s;
}
.edit-input:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(59,130,246,.12); }
select.edit-input {
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='%2394A3B8'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 9px center;
    background-color:var(--card);cursor:pointer;padding-right:32px;
}
textarea.edit-input { height:80px;padding:9px 11px;resize:vertical;line-height:1.55; }
.form-label-v2 { font-size:11px;font-weight:700;color:var(--text-secondary);margin-bottom:5px;display:block;text-transform:uppercase;letter-spacing:.4px; }
.form-group-v2 { display:flex;flex-direction:column;margin-bottom:12px; }
.form-group-v2:last-child { margin-bottom:0; }
.form-error { font-size:11px;color:var(--danger);margin-top:4px; }

/* ── STATUS PANEL ──────────────────────────── */
.status-toggle-v2 {
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 16px;border-radius:var(--radius-sm);border:1.5px solid;
    cursor:pointer;transition:all .2s;margin-bottom:14px;
}
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

/* ── QUICK ACTIONS ─────────────────────────── */
.qa-btn { display:flex;align-items:center;gap:10px;width:100%;padding:11px 15px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;text-align:left;transition:all .15s; }
.qa-btn:hover { border-color:var(--primary-light);color:var(--primary);background:var(--primary-xlight); }
.qa-btn i { font-size:16px;flex-shrink:0; }
.qa-btn.amber { border-color:#FDE68A;background:#FFFBEB;color:#92400E; }
.qa-btn.amber:hover { border-color:#FCD34D; }
.qa-btn.red   { border-color:#FECACA;background:#FEF2F2;color:var(--danger); }
.qa-btn.red:hover { border-color:#FCA5A5; }
.save-btn-v2 { display:flex;align-items:center;justify-content:center;gap:7px;width:100%;height:42px;border-radius:var(--radius-sm);border:none;background:var(--primary);color:#fff;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .18s;box-shadow:0 4px 14px rgba(59,130,246,.35); }
.save-btn-v2:hover { background:var(--primary-dark);box-shadow:0 6px 20px rgba(59,130,246,.45);transform:translateY(-1px); }
.save-btn-v2:disabled { opacity:.6;cursor:not-allowed;transform:none; }
.cancel-btn-v2 { display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:36px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);color:var(--text-secondary);font-family:var(--font);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s; }

/* ── DETAIL GRID ───────────────────────────── */
.detail-grid { display:grid;grid-template-columns:1fr 1fr 280px;gap:18px;align-items:start; }
.detail-col  { display:flex;flex-direction:column;gap:18px; }

/* ── SCHOOL CHIPS ──────────────────────────── */
.school-chip {
    display:flex;align-items:center;gap:8px;padding:8px 12px;
    border:1px solid var(--border);border-radius:var(--radius-sm);
    background:var(--bg);font-size:12px;font-weight:500;color:var(--text-primary);
}
.school-chip i { color:var(--primary);font-size:14px;flex-shrink:0; }

/* ── EDIT BANNER ───────────────────────────── */
.edit-banner-v2 {
    display:flex;align-items:center;gap:10px;padding:12px 18px;
    background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #BFDBFE;
    border-radius:var(--radius-sm);margin-bottom:20px;font-size:13px;
    color:#1D4ED8;font-weight:500;animation:fadeUp .25s ease both;
}
.edit-banner-cancel { background:none;border:none;cursor:pointer;color:#1D4ED8;font-weight:700;font-size:13px;text-decoration:underline;padding:0;margin-left:auto; }

@media (max-width:1100px) { .detail-grid { grid-template-columns:1fr 1fr !important; } }
@media (max-width:720px)  { .detail-grid { grid-template-columns:1fr !important; } }
</style>
@endpush

@section('content')
<div
    x-data="userDetail({
        userId:    '{{ $user['id'] }}',
        userType:  '{{ $user['user_type'] }}',
        userData:  {{ json_encode($user) }},
        baseUrl:   '{{ url('users') }}',
        indexUrl:  '{{ route('users.index') }}',
        rolesUrl:  '{{ route('users.roles') }}',
        schoolsUrl:'{{ route('users.schools') }}',
    })"
    x-init="init()">

    {{-- ── BREADCRUMB & HEADER ─────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;animation:fadeUp .3s ease both">
        <div>
            <ul class="breadcrumb-list">
                <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Sistem</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><a :href="indexUrl" style="color:var(--text-muted);font-size:11px;font-weight:500;text-decoration:none">Users Management</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span x-text="user.name"></span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;margin-top:5px" x-text="user.name"></h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">Detail profil dan informasi akun</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a :href="indexUrl" class="dt-btn dt-btn-outline" style="height:38px">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <template x-if="!isEditing">
                <button @click="startEdit()" class="dt-btn dt-btn-primary" style="height:38px">
                    <i class="ri-pencil-line"></i> Edit User
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

    {{-- ── EDIT BANNER ─────────────────────────────── --}}
    <div x-show="isEditing" x-transition class="edit-banner-v2" style="display:none">
        <i class="ri-edit-2-fill"></i>
        <span>Mode edit aktif — ubah data yang ingin diperbarui, lalu klik <strong>Simpan Perubahan</strong></span>
        <button @click="cancelEdit()" class="edit-banner-cancel">Batal</button>
    </div>

    {{-- ══ HERO CARD ══════════════════════════════════ --}}
    <div class="user-hero">
        <div class="hero-banner"></div>
        <div class="hero-body">
            <div class="hero-identity">
                {{-- Avatar --}}
                <div class="hero-left">
                    <template x-if="user.profile_picture">
                        <img :src="user.profile_picture" :alt="user.name"
                            class="user-avatar" style="object-fit:cover">
                    </template>
                    <template x-if="!user.profile_picture">
                        <div class="user-avatar" x-text="initials(user.name)"></div>
                    </template>

                    <div style="padding-bottom:6px">
                        {{-- View: nama --}}
                        <div x-show="!isEditing" class="hero-name" x-text="user.name"></div>

                        {{-- Edit: nama --}}
                        <div x-show="isEditing" style="margin-bottom:8px">
                            <input x-model="form.name" data-edit-focus type="text"
                                class="edit-input" placeholder="Nama user"
                                style="font-size:15px;font-weight:700;height:42px;min-width:260px;max-width:380px">
                            <div x-show="errors.name" class="form-error" x-text="errors.name"></div>
                        </div>

                        <div class="hero-badges">
                            {{-- Role badges --}}
                            <template x-for="role in user.roles" :key="role.role_id">
                                <span class="role-badge" :style="roleStyle(role.role_name)"
                                    x-text="role.role_name"></span>
                            </template>

                            {{-- Status badge --}}
                            <span class="dt-badge" :class="user.status === 'active' ? 'aktif' : 'nonaktif'"
                                x-text="user.status === 'active' ? '● Aktif' : '● Non-Aktif'"></span>

                            {{-- Verified badge --}}
                            <span x-show="user.is_verified"
                                style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:#ECFDF5;color:#059669">
                                <i class="ri-shield-check-line"></i> Terverifikasi
                            </span>
                            <span x-show="!user.is_verified"
                                style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:#FEF9C3;color:#854D0E">
                                <i class="ri-error-warning-line"></i> Belum Verifikasi
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Stats strip kanan --}}
                <div style="display:flex;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;flex-shrink:0;margin-bottom:6px;align-self:flex-end">
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:100px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-primary);font-family:var(--font-mono);line-height:1" x-text="user.phone_number || '—'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Telepon</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:100px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-primary);line-height:1" x-text="user.schools?.length ?? 0"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Sekolah</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;min-width:100px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="user.created_at ? new Date(user.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
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

            {{-- Card: Data Akun (semua role) --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-account-circle-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Data Akun</div>
                            <div class="card-hd-sub-v2">Informasi login & kontak</div>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-mail-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Email</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" x-text="user.email || '—'"></div>
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
                            <div class="field-val-v2" :class="!user.phone_number ? 'is-empty' : ''" x-text="user.phone_number || 'Belum diisi'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.phone_number" type="tel" class="edit-input" placeholder="+62 ...">
                        </template>
                    </div>
                </div>

                {{-- Password (edit only) --}}
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

            {{-- ══════════════════════════════════
                 PROFILE SECTION — DINAMIS PER ROLE
            ════════════════════════════════════ --}}

            {{-- Teacher / Headmaster --}}
            <template x-if="user.user_type === 'teacher' || user.user_type === 'headmaster'">
                <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2"><i class="ri-user-star-line"></i></div>
                            <div>
                                <div class="card-hd-title-v2">Data Guru</div>
                                <div class="card-hd-sub-v2" x-text="user.user_type === 'headmaster' ? 'Profil Kepala Sekolah' : 'Profil Pengajar'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- NIP --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-id-card-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">NIP</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2 is-mono" :class="!user.profile?.nip ? 'is-empty' : ''" x-text="user.profile?.nip || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.nip" type="text" class="edit-input" placeholder="18 digit NIP" style="font-family:var(--font-mono)">
                            </template>
                        </div>
                    </div>

                    {{-- NIK --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-fingerprint-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">NIK</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2 is-mono" :class="!user.profile?.nik ? 'is-empty' : ''" x-text="user.profile?.nik || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.nik" type="text" class="edit-input" placeholder="16 digit NIK" style="font-family:var(--font-mono)">
                            </template>
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-user-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Nama Lengkap</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" x-text="user.profile?.full_name || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.full_name" type="text" class="edit-input" placeholder="Nama lengkap beserta gelar">
                            </template>
                        </div>
                    </div>

                    {{-- Jabatan --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-briefcase-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Jabatan</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.position ? 'is-empty' : ''" x-text="user.profile?.position || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.position" type="text" class="edit-input" placeholder="Guru Matematika, Kepala Sekolah, dll">
                            </template>
                        </div>
                    </div>

                    {{-- Status Kepegawaian --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-government-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Status Kepegawaian</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.employment_status ? 'is-empty' : ''" x-text="user.profile?.employment_status || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <select x-model="form.profile.employment_status" class="edit-input">
                                    <option value="">— Pilih Status —</option>
                                    <option value="PNS">PNS</option>
                                    <option value="PPPK">PPPK</option>
                                    <option value="GTT">GTT (Guru Tidak Tetap)</option>
                                    <option value="Honorer">Honorer</option>
                                    <option value="Yayasan">Guru Yayasan</option>
                                </select>
                            </template>
                        </div>
                    </div>

                    {{-- Tanggal Bergabung --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-calendar-check-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Tanggal Bergabung</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.join_date ? 'is-empty' : ''"
                                    x-text="user.profile?.join_date ? new Date(user.profile.join_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.join_date" type="date" class="edit-input">
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Student --}}
            <template x-if="user.user_type === 'student'">
                <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2"><i class="ri-book-open-line"></i></div>
                            <div>
                                <div class="card-hd-title-v2">Data Siswa</div>
                                <div class="card-hd-sub-v2">Profil & informasi akademik</div>
                            </div>
                        </div>
                    </div>

                    {{-- NIS --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-id-card-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">NIS</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2 is-mono" :class="!user.profile?.nis ? 'is-empty' : ''" x-text="user.profile?.nis || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.nis" type="text" class="edit-input" style="font-family:var(--font-mono)" placeholder="Nomor Induk Siswa">
                            </template>
                        </div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-user-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Nama Lengkap</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" x-text="user.profile?.full_name || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.full_name" type="text" class="edit-input" placeholder="Nama lengkap">
                            </template>
                        </div>
                    </div>

                    {{-- Kelas --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-door-open-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Kelas</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.class_group ? 'is-empty' : ''" x-text="user.profile?.class_group || 'Belum diisi'"></div>
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
                                <div class="field-val-v2" :class="!user.profile?.enrollment_date ? 'is-empty' : ''"
                                    x-text="user.profile?.enrollment_date ? new Date(user.profile.enrollment_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.enrollment_date" type="date" class="edit-input">
                            </template>
                        </div>
                    </div>
                </div>
            </template>
            
        </div>

        {{-- ═══ KOLOM TENGAH ═══ --}}
        <div class="detail-col">
            {{-- Card: Role & Sekolah (edit mode) --}}
            <template x-if="isEditing">
                <div class="card-v2 is-editing-card">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2"><i class="ri-shield-user-line"></i></div>
                            <div>
                                <div class="card-hd-title-v2">Role & Sekolah</div>
                                <div class="card-hd-sub-v2">Ubah akses dan penempatan</div>
                            </div>
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="field-row-v2 is-editing">
                        <div class="field-ico" style="background:var(--primary);color:#fff;margin-top:0;align-self:center">
                            <i class="ri-shield-star-line"></i>
                        </div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Role</div>
                            <select x-model="form.role" class="edit-input">
                                <option value="">— Pilih Role —</option>
                                <template x-for="role in roles" :key="role.role_id">
                                    <option :value="role.role_id" x-text="role.role_name"></option>
                                </template>
                            </select>
                            <div x-show="errors.role" class="form-error" x-text="errors.role"></div>
                        </div>
                    </div>

                    {{-- Sekolah --}}
                    <div class="field-row-v2 is-editing" style="border-bottom:none">
                        <div class="field-ico" style="background:var(--primary);color:#fff;margin-top:0;align-self:center">
                            <i class="ri-school-line"></i>
                        </div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Sekolah</div>
                            <select x-model="form.school" class="edit-input">
                                <option value="">— Pilih Sekolah —</option>
                                <template x-for="school in schools" :key="school.school_id">
                                    <option :value="school.school_id" x-text="school.school_name"></option>
                                </template>
                            </select>
                            <div x-show="errors.school" class="form-error" x-text="errors.school"></div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Card: Data Pribadi (teacher / headmaster / student) --}}
            <template x-if="['teacher','headmaster','student'].includes(user.user_type)">
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

                    {{-- Tempat Lahir (teacher/headmaster only) --}}
                    <template x-if="user.user_type !== 'student'">
                        <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                            <div class="field-ico"><i class="ri-map-pin-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Tempat Lahir</div>
                                <template x-if="!isEditing">
                                    <div class="field-val-v2" :class="!user.profile?.birth_place ? 'is-empty' : ''" x-text="user.profile?.birth_place || 'Belum diisi'"></div>
                                </template>
                                <template x-if="isEditing">
                                    <input x-model="form.profile.birth_place" type="text" class="edit-input" placeholder="Kota lahir">
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Tanggal Lahir --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-cake-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Tanggal Lahir</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.birth_date ? 'is-empty' : ''"
                                    x-text="user.profile?.birth_date ? new Date(user.profile.birth_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'Belum diisi'"></div>
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
                                <div class="field-val-v2" :class="!user.profile?.gender ? 'is-empty' : ''" x-text="user.profile?.gender || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <select x-model="form.profile.gender" class="edit-input">
                                    <option value="">— Pilih —</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </template>
                        </div>
                    </div>

                    {{-- Agama (teacher/headmaster only) --}}
                    <template x-if="user.user_type !== 'student'">
                        <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                            <div class="field-ico"><i class="ri-heart-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Agama</div>
                                <template x-if="!isEditing">
                                    <div class="field-val-v2" :class="!user.profile?.religion ? 'is-empty' : ''" x-text="user.profile?.religion || 'Belum diisi'"></div>
                                </template>
                                <template x-if="isEditing">
                                    <select x-model="form.profile.religion" class="edit-input">
                                        <option value="">— Pilih —</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                    </select>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Alamat --}}
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-home-4-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Alamat</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" style="line-height:1.6" :class="!user.profile?.address ? 'is-empty' : ''" x-text="user.profile?.address || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <textarea x-model="form.profile.address" class="edit-input" placeholder="Alamat lengkap..."></textarea>
                            </template>
                        </div>
                    </div>

                    {{-- Kota & Provinsi (student only) --}}
                    <template x-if="user.user_type === 'student'">
                        <div>
                            <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                                <div class="field-ico"><i class="ri-building-line"></i></div>
                                <div class="field-content">
                                    <div class="field-lbl-v2">Kota</div>
                                    <template x-if="!isEditing">
                                        <div class="field-val-v2" :class="!user.profile?.city ? 'is-empty' : ''" x-text="user.profile?.city || 'Belum diisi'"></div>
                                    </template>
                                    <template x-if="isEditing">
                                        <input x-model="form.profile.city" type="text" class="edit-input" placeholder="Kota">
                                    </template>
                                </div>
                            </div>
                            <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                                <div class="field-ico"><i class="ri-map-2-line"></i></div>
                                <div class="field-content">
                                    <div class="field-lbl-v2">Provinsi</div>
                                    <template x-if="!isEditing">
                                        <div class="field-val-v2" :class="!user.profile?.province ? 'is-empty' : ''" x-text="user.profile?.province || 'Belum diisi'"></div>
                                    </template>
                                    <template x-if="isEditing">
                                        <input x-model="form.profile.province" type="text" class="edit-input" placeholder="Provinsi">
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Card: Pendidikan (teacher/headmaster only) --}}
            <template x-if="user.user_type === 'teacher' || user.user_type === 'headmaster'">
                <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.12s">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2"><i class="ri-graduation-cap-line"></i></div>
                            <div>
                                <div class="card-hd-title-v2">Pendidikan & Sertifikasi</div>
                                <div class="card-hd-sub-v2">Riwayat pendidikan guru</div>
                            </div>
                        </div>
                    </div>
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-medal-2-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Jenjang Pendidikan</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.education_level ? 'is-empty' : ''" x-text="user.profile?.education_level || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <select x-model="form.profile.education_level" class="edit-input">
                                    <option value="">— Pilih —</option>
                                    <option value="S1">S1</option><option value="S2">S2</option>
                                    <option value="S3">S3</option><option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                </select>
                            </template>
                        </div>
                    </div>
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-book-2-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Jurusan</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.major ? 'is-empty' : ''" x-text="user.profile?.major || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.major" type="text" class="edit-input" placeholder="Pendidikan Matematika, dll">
                            </template>
                        </div>
                    </div>
                    <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                        <div class="field-ico"><i class="ri-award-line"></i></div>
                        <div class="field-content">
                            <div class="field-lbl-v2">Sertifikasi</div>
                            <template x-if="!isEditing">
                                <div class="field-val-v2" :class="!user.profile?.certification ? 'is-empty' : ''" x-text="user.profile?.certification || 'Belum diisi'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.profile.certification" type="text" class="edit-input" placeholder="No. sertifikasi">
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Sekolah (semua role kecuali super-admin) --}}
            <template x-if="user.user_type !== 'super-admin'">
                <div class="card-v2" style="animation-delay:.14s">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2"><i class="ri-school-line"></i></div>
                            <div>
                                <div class="card-hd-title-v2">Sekolah Terhubung</div>
                                <div class="card-hd-sub-v2" x-text="`${user.schools?.length ?? 0} sekolah`"></div>
                            </div>
                        </div>
                    </div>
                    <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px">
                        <template x-if="user.schools && user.schools.length > 0">
                            <template x-for="school in user.schools" :key="school.school_id">
                                <div class="school-chip">
                                    <i class="ri-building-2-line"></i>
                                    <div style="flex:1;min-width:0">
                                        <div style="font-weight:600;font-size:13px" x-text="school.school_name"></div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:1px" x-text="school.school_type || ''"></div>
                                    </div>
                                    <span class="dt-badge" :class="school.status === 'active' ? 'aktif' : 'nonaktif'"
                                        x-text="school.status === 'active' ? 'Aktif' : 'Non-Aktif'" style="font-size:10px"></span>
                                </div>
                            </template>
                        </template>
                        <template x-if="!user.schools || user.schools.length === 0">
                            <div style="text-align:center;padding:24px;color:var(--text-muted);font-size:13px">
                                <i class="ri-building-line" style="font-size:28px;display:block;margin-bottom:6px;opacity:.4"></i>
                                Belum terhubung ke sekolah
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>

        {{-- ═══ KOLOM KANAN (sidebar) ═══ --}}
        <div class="detail-col">

            {{-- Status User --}}
            <div class="card-v2" style="animation-delay:.06s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-pulse-line"></i></div>
                        <div><div class="card-hd-title-v2">Status Akun</div></div>
                    </div>
                </div>
                <div style="padding:16px">
                    <div class="status-toggle-v2"
                        :class="user.status === 'active' ? 'active' : 'inactive'"
                        @click="toggleStatus()">
                        <div>
                            <div style="font-size:14px;font-weight:700" :style="user.status === 'active' ? 'color:#065F46' : 'color:#475569'"
                                x-text="user.status === 'active' ? 'Aktif' : 'Non-Aktif'"></div>
                            <div style="font-size:11px;color:var(--text-muted)">Klik untuk ubah status</div>
                        </div>
                        <div class="toggle-pill" :class="user.status === 'active' ? 'on' : 'off'">
                            <div class="toggle-pill-thumb" :class="user.status === 'active' ? 'on' : 'off'"></div>
                        </div>
                    </div>
                    <div class="meta-list">
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-shield-check-line"></i> Email</div>
                            <div class="meta-item-val" :style="user.is_verified ? 'color:#059669' : 'color:#D97706'"
                                x-text="user.is_verified ? 'Terverifikasi' : 'Belum'"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-calendar-event-line"></i> Bergabung</div>
                            <div class="meta-item-val"
                                x-text="user.created_at ? new Date(user.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-refresh-line"></i> Diperbarui</div>
                            <div class="meta-item-val"
                                x-text="user.updated_at ? new Date(user.updated_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
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
                            <i class="ri-pencil-fill"></i> Edit Informasi User
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
                        :class="user.status === 'active' ? 'amber' : ''"
                        :style="user.status !== 'active' ? 'border-color:#A7F3D0;background:#ECFDF5;color:#065F46' : ''">
                        <i :class="user.status === 'active' ? 'ri-pause-circle-line' : 'ri-play-circle-line'"></i>
                        <span x-text="user.status === 'active' ? 'Nonaktifkan User' : 'Aktifkan User'"></span>
                    </button>

                    <button @click="deleteUser()" class="qa-btn red">
                        <i class="ri-delete-bin-6-fill"></i> Hapus User
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection