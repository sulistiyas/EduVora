@extends('layouts.app')

@section('title', $school->school_name ?? 'Detail Sekolah')

@push('styles')
<style>
/* ─────────────────────────────────────────────
   SCHOOL DETAIL — Additional Styles
   (extends main.css + datatable.css)
───────────────────────────────────────────── */

@keyframes spin     { to { transform: rotate(360deg); } }
@keyframes fadeUp   { from { opacity:0; transform:translateY(10px) } to { opacity:1; transform:none } }
@keyframes slideIn  { from { opacity:0; transform:translateX(12px) } to { opacity:1; transform:none } }

/* ── HERO ──────────────────────────────────── */
.school-hero {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 20px;
    animation: fadeUp .4s ease both;
}

.hero-banner {
    height: 120px;
    background: linear-gradient(135deg, #0F2557 0%, #1E3A8A 35%, #2563EB 65%, #3B82F6 85%, #60A5FA 100%);
    position: relative;
    overflow: hidden;
}

/* Decorative circles */
.hero-banner::before {
    content: '';
    position: absolute;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    top: -120px; right: -60px;
}
.hero-banner::after {
    content: '';
    position: absolute;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    bottom: -60px; left: 10%;
}

.hero-body {
    padding: 60px 28px 24px;
}

.hero-identity {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    /* Pull up so avatar overlaps the banner */
    margin-top: -60px;
}

.hero-left {
    display: flex;
    align-items: flex-end;
    gap: 16px;
}

.school-logo {
    width: 88px;
    height: 88px;
    border-radius: 18px;
    background: linear-gradient(135deg, #1E3A8A, #3B82F6);
    display: grid;
    place-items: center;
    font-size: 28px;
    font-weight: 700;
    color: white;
    border: 4px solid var(--card);
    box-shadow: 0 8px 24px rgba(30,58,138,.28);
    flex-shrink: 0;
    letter-spacing: -.5px;
}

.hero-meta {
    padding-bottom: 6px;
}

.hero-school-name {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.5px;
    line-height: 1.2;
    margin-bottom: 8px;
}

.hero-badges {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
}

/* ── STATS STRIP ───────────────────────────── */
.stats-strip {
    display: flex;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    overflow: hidden;
    flex-shrink: 0;
    margin-bottom: 6px;
    align-self: flex-end;
}

.stat-cell {
    padding: 12px 20px;
    text-align: center;
    border-right: 1px solid var(--border);
    min-width: 90px;
}
.stat-cell:last-child { border-right: none; }

.stat-cell-val {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    font-family: var(--font-mono);
    line-height: 1;
}
.stat-cell-val.accent { color: var(--primary); font-size: 20px; }
.stat-cell-lbl {
    font-size: 10px;
    color: var(--text-muted);
    font-weight: 600;
    letter-spacing: .6px;
    text-transform: uppercase;
    margin-top: 4px;
}

/* ── SECTION GRID ──────────────────────────── */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 290px;
    gap: 18px;
    align-items: start;
}

.detail-col { display: flex; flex-direction: column; gap: 18px; }

/* ── FIELD ROWS (view mode) ────────────────── */
.field-row-v2 {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 13px 20px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.field-row-v2:last-child { border-bottom: none; }
.field-row-v2.is-editing { background: #F0F7FF; }

.field-ico {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: var(--primary-xlight);
    display: grid;
    place-items: center;
    flex-shrink: 0;
    margin-top: 1px;
    font-size: 14px;
    color: var(--primary);
    transition: all .15s;
}
.field-row-v2.is-editing .field-ico {
    background: var(--primary);
    color: #fff;
}

.field-content { flex: 1; min-width: 0; }

.field-lbl-v2 {
    font-size: 10px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .7px;
    margin-bottom: 4px;
}

.field-val-v2 {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
}
.field-val-v2.is-mono { font-family: var(--font-mono); font-size: 12.5px; letter-spacing: .3px; }
.field-val-v2.is-empty { color: var(--text-muted); font-style: italic; font-weight: 400; }

/* ── EDIT INPUTS ───────────────────────────── */
.edit-input {
    width: 100%;
    height: 36px;
    padding: 0 11px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--card);
    font-family: var(--font);
    font-size: 13px;
    color: var(--text-primary);
    outline: none;
    transition: all .18s;
}
.edit-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
}
.edit-input.mono-input { font-family: var(--font-mono); font-size: 12.5px; }

textarea.edit-input {
    height: 82px;
    padding: 9px 11px;
    resize: vertical;
    line-height: 1.55;
}

select.edit-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='%2394A3B8'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 9px center;
    background-color: var(--card);
    cursor: pointer;
    padding-right: 32px;
}

/* ── CARD TWEAK ────────────────────────────── */
.card-v2 {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
    animation: fadeUp .4s ease both;
}
.card-v2.is-editing-card {
    border-color: #93C5FD;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1), var(--shadow);
}

.card-hd-v2 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--card);
}
.card-hd-left-v2 { display: flex; align-items: center; gap: 10px; }
.card-hd-icon-v2 {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: var(--primary-xlight);
    display: grid;
    place-items: center;
    font-size: 15px;
    color: var(--primary);
    flex-shrink: 0;
}
.card-hd-title-v2 { font-size: 13.5px; font-weight: 700; color: var(--text-primary); }
.card-hd-sub-v2   { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

.editing-badge {
    font-size: 10.5px;
    font-weight: 600;
    color: var(--primary);
    background: var(--primary-xlight);
    border: 1px solid var(--primary-light);
    padding: 3px 10px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* ── HEADMASTER CARD ───────────────────────── */
.hm-display {
    display: flex;
    align-items: center;
    gap: 16px;
    background: linear-gradient(135deg, #EFF6FF 0%, #F0F9FF 100%);
    border: 1px solid var(--primary-light);
    border-radius: var(--radius-sm);
    padding: 18px;
}
.hm-av {
    width: 56px; height: 56px;
    border-radius: 15px;
    background: linear-gradient(135deg, var(--primary), #60A5FA);
    display: grid;
    place-items: center;
    font-size: 18px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(59,130,246,.3);
}
.hm-name { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 3px; }
.hm-role { font-size: 11.5px; color: var(--text-muted); }
.hm-nip  {
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-family: var(--font-mono);
    color: var(--text-secondary);
    background: var(--card);
    border: 1px solid var(--border);
    padding: 3px 10px;
    border-radius: 5px;
}

/* ── CONTACT ITEMS ─────────────────────────── */
.contact-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 13px;
    border-radius: var(--radius-sm);
    background: var(--bg);
    border: 1px solid var(--border);
    margin-bottom: 7px;
    transition: border-color .15s;
}
.contact-row:last-child { margin-bottom: 0; }
.contact-row:hover { border-color: var(--primary-light); }
.contact-ico {
    width: 34px; height: 34px;
    border-radius: 9px;
    background: var(--primary-xlight);
    display: grid;
    place-items: center;
    font-size: 15px;
    color: var(--primary);
    flex-shrink: 0;
}
.contact-lbl { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 2px; }
.contact-val { font-size: 13px; font-weight: 600; color: var(--primary); text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.contact-val:hover { text-decoration: underline; }
.contact-val.empty { color: var(--text-muted); font-style: italic; font-weight: 400; }

/* ── ADDRESS BOX ───────────────────────────── */
.address-box {
    display: flex;
    gap: 11px;
    align-items: flex-start;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 13px 15px;
    margin-bottom: 10px;
}
.address-ico { font-size: 15px; color: var(--primary); margin-top: 2px; flex-shrink: 0; }
.address-lbl { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 4px; }
.address-val { font-size: 13px; font-weight: 500; color: var(--text-primary); line-height: 1.6; }

.loc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.loc-cell {
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    transition: border-color .15s;
}
.loc-cell.is-editing { background: var(--card); border-color: var(--border); }
.loc-lbl { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 5px; }
.loc-val { font-size: 13px; font-weight: 600; color: var(--text-primary); }
.loc-val.empty { color: var(--text-muted); font-style: italic; font-weight: 400; }

/* ── STATUS PANEL ──────────────────────────── */
.status-toggle-v2 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-radius: var(--radius-sm);
    border: 1.5px solid;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: 14px;
}
.status-toggle-v2.active   { border-color: #6EE7B7; background: linear-gradient(135deg, #ECFDF5, #F0FDF4); }
.status-toggle-v2.inactive { border-color: #CBD5E1; background: var(--bg); }
.status-toggle-label { font-size: 14px; font-weight: 700; }
.status-toggle-hint  { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

.toggle-pill {
    width: 42px; height: 24px;
    border-radius: 999px;
    position: relative;
    transition: background .25s;
    flex-shrink: 0;
}
.toggle-pill.on  { background: #10B981; }
.toggle-pill.off { background: #D1D5DB; }
.toggle-pill-thumb {
    position: absolute;
    top: 4px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
    transition: transform .25s;
}
.toggle-pill-thumb.on  { transform: translateX(22px); }
.toggle-pill-thumb.off { transform: translateX(4px); }

.meta-list { display: flex; flex-direction: column; gap: 8px; padding-top: 12px; border-top: 1px solid var(--border); }
.meta-item { display: flex; justify-content: space-between; align-items: center; }
.meta-item-lbl { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 5px; }
.meta-item-lbl i { font-size: 13px; }
.meta-item-val { font-size: 12px; font-weight: 600; color: var(--text-primary); font-family: var(--font-mono); }

/* ── QUICK ACTIONS ─────────────────────────── */
.qa-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 11px 15px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    background: var(--card);
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    text-align: left;
    transition: all .15s;
}
.qa-btn:hover { border-color: var(--primary-light); color: var(--primary); background: var(--primary-xlight); }
.qa-btn i { font-size: 16px; flex-shrink: 0; }
.qa-btn.amber { border-color: #FDE68A; background: #FFFBEB; color: #92400E; }
.qa-btn.amber:hover { border-color: #FCD34D; }
.qa-btn.red   { border-color: #FECACA; background: #FEF2F2; color: var(--danger); }
.qa-btn.red:hover { border-color: #FCA5A5; }

.save-btn-v2 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    width: 100%;
    height: 42px;
    border-radius: var(--radius-sm);
    border: none;
    background: var(--primary);
    color: #fff;
    font-family: var(--font);
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all .18s;
    box-shadow: 0 4px 14px rgba(59,130,246,.35);
}
.save-btn-v2:hover { background: var(--primary-dark); box-shadow: 0 6px 20px rgba(59,130,246,.45); transform: translateY(-1px); }
.save-btn-v2:disabled { opacity: .6; cursor: not-allowed; transform: none; }

.cancel-btn-v2 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    height: 36px;
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--border);
    background: var(--card);
    color: var(--text-secondary);
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
}
.cancel-btn-v2:hover { border-color: var(--border); background: var(--bg); }

/* ── EDIT MODE BANNER ──────────────────────── */
.edit-banner-v2 {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    border: 1px solid #BFDBFE;
    border-radius: var(--radius-sm);
    margin-bottom: 20px;
    font-size: 13px;
    color: #1D4ED8;
    font-weight: 500;
    animation: fadeUp .25s ease both;
}
.edit-banner-v2 i { font-size: 16px; flex-shrink: 0; }
.edit-banner-v2 strong { font-weight: 700; }
.edit-banner-cancel {
    background: none; border: none; cursor: pointer;
    color: #1D4ED8; font-weight: 700; font-size: 13px;
    text-decoration: underline; padding: 0; margin-left: auto; white-space: nowrap;
}

/* ── CHIP TYPE SELECT ──────────────────────── */
.type-chips { display: flex; gap: 7px; flex-wrap: wrap; margin-top: 2px; }
.type-chip {
    display: inline-flex; align-items: center; gap: 5px;
    height: 32px; padding: 0 14px;
    border-radius: 999px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    font-size: 12.5px; font-weight: 600;
    color: var(--text-muted);
    cursor: pointer; transition: all .15s;
}
.type-chip:hover { border-color: var(--primary-light); color: var(--primary); background: var(--card); }
.type-chip.selected {
    border-color: var(--primary);
    background: var(--primary-xlight);
    color: var(--primary-dark);
}
.type-chip i { font-size: 11px; }

/* ── FORM LABEL V2 ─────────────────────────── */
.form-label-v2 {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
    margin-bottom: 5px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.form-group-v2 { display: flex; flex-direction: column; margin-bottom: 12px; }
.form-group-v2:last-child { margin-bottom: 0; }

/* ── RESPONSIVE ────────────────────────────── */
@media (max-width: 1100px) { .detail-grid { grid-template-columns: 1fr 1fr !important; } }
@media (max-width: 720px)  { .detail-grid { grid-template-columns: 1fr !important; } }
@media (max-width: 600px) {
    .hero-identity { flex-direction: column; align-items: flex-start; }
    .stats-strip { align-self: stretch; }
    .stat-cell { flex: 1; }
}
</style>
@endpush

@section('content')
<div x-data="schoolDetail({
    schoolId:       '{{ $school->school_id }}',
    schoolName:     '{{ addslashes($school->school_name) }}',
    schoolStatus:   '{{ $school->status }}',
    schoolType:     '{{ $school->school_type ?? '' }}',
    accreditation:  '{{ $school->accreditation ?? '' }}',
    contactEmail:   '{{ addslashes($school->contact_email ?? '') }}',
    contactPhone:   '{{ addslashes($school->contact_phone ?? '') }}',
    website:        '{{ addslashes($school->website ?? '') }}',
    kkm:            '{{ $school->kkm_default ?? '' }}',
    npsn:           '{{ $school->npsn ?? '' }}',
    nss:            '{{ $school->nss ?? '' }}',
    province:       '{{ addslashes($school->province ?? '') }}',
    city:           '{{ addslashes($school->city ?? '') }}',
    district:       '{{ addslashes($school->district ?? '') }}',
    postalCode:     '{{ $school->postal_code ?? '' }}',
    address:        '{{ addslashes($school->address ?? '') }}',
    headmasterName: '{{ addslashes($school->headmaster_name ?? '') }}',
    headmasterNip:  '{{ $school->headmaster_nip ?? '' }}',
    createdAt:      '{{ $school->created_at->format('d M Y') }}',
    updatedAt:      '{{ $school->updated_at->format('d M Y') }}',
    logoUrl:        '{{ $school->logo_url ?? '' }}',
    indexUrl:       '{{ route('school-management.index') }}',
    baseUrl:        '{{ url('school-management') }}',
})" x-init="init()">

    {{-- ── PAGE HEADER ─────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;animation:fadeUp .3s ease both">
        <div>
            <ul class="breadcrumb-list">
                <li>
                    <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                        <i class="ri-home-4-line"></i> Dashboard
                    </a>
                </li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Sistem</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>
                    <a :href="indexUrl" style="color:var(--text-muted);font-size:11px;font-weight:500;text-decoration:none"
                        onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">
                        School Management
                    </a>
                </li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span x-text="school.school_name"></span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:5px"
                x-text="school.school_name"></h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">Detail profil dan informasi sekolah</p>
        </div>

        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a :href="indexUrl" class="dt-btn dt-btn-outline" style="height:38px">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            {{-- Toggle edit/save/cancel --}}
            <template x-if="!isEditing">
                <button @click="startEdit()" class="dt-btn dt-btn-primary" style="height:38px">
                    <i class="ri-pencil-line"></i> Edit Sekolah
                </button>
            </template>
            <template x-if="isEditing">
                <div style="display:flex;gap:8px">
                    <button @click="cancelEdit()" style="display:inline-flex;align-items:center;gap:6px;height:38px;padding:0 16px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:all .15s">
                        <i class="ri-close-line"></i> Batal
                    </button>
                    <button @click="submitEdit()" :disabled="submitting" class="dt-btn dt-btn-primary" style="height:38px">
                        <svg x-show="submitting" style="width:14px;height:14px;flex-shrink:0;animation:spin 1s linear infinite" fill="none" viewBox="0 0 24 24">
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

    {{-- ── EDIT MODE BANNER ───────────────────── --}}
    <div x-show="isEditing"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="edit-banner-v2"
        style="display:none">
        <i class="ri-edit-2-fill"></i>
        <span>Mode edit aktif — ubah data yang ingin diperbarui, lalu klik <strong>Simpan Perubahan</strong></span>
        <button @click="cancelEdit()" class="edit-banner-cancel">Batal</button>
    </div>

    {{-- ══════════════════════════════════════════
         HERO CARD
    ══════════════════════════════════════════════ --}}
    <div class="school-hero">
        <div class="hero-banner"></div>
        <div class="hero-body">
            <div class="hero-identity">
                {{-- Left: Avatar + Name + Badges --}}
                <div class="hero-left">
                    {{-- Logo / Avatar --}}
                    <template x-if="school.logo_url">
                        <img :src="school.logo_url" :alt="school.school_name" class="school-logo" style="object-fit:cover">
                    </template>
                    <template x-if="!school.logo_url">
                        <div class="school-logo" x-text="initials(school.school_name)"></div>
                    </template>

                    <div class="hero-meta">
                        {{-- View: school name --}}
                        <div x-show="!isEditing" class="hero-school-name" x-text="school.school_name"></div>

                        {{-- Edit: name input --}}
                        <div x-show="isEditing" style="margin-bottom:8px">
                            <input x-model="form.school_name"
                                data-edit-focus
                                type="text"
                                class="edit-input"
                                placeholder="Nama sekolah"
                                style="font-size:15px;font-weight:700;height:42px;min-width:280px;max-width:400px">
                            <div x-show="errors.school_name" class="form-error" x-text="errors.school_name" style="margin-top:5px"></div>
                        </div>

                        <div class="hero-badges">
                            {{-- School type pill (view) --}}
                            <template x-if="!isEditing && school.school_type">
                                <span :style="schoolTypeStyle(school.school_type)"
                                    style="display:inline-flex;align-items:center;padding:3px 13px;border-radius:999px;font-size:11.5px;font-weight:600"
                                    x-text="school.school_type"></span>
                            </template>

                            {{-- School type (edit) --}}
                            <template x-if="isEditing">
                                <select x-model="form.school_type" class="edit-input" style="height:30px;padding:0 30px 0 10px;font-size:12px;width:auto">
                                    <option value="">Pilih Tipe</option>
                                    <option value="Elementary">Elementary</option>
                                    <option value="Junior High">Junior High</option>
                                    <option value="Senior High">Senior High</option>
                                </select>
                            </template>

                            {{-- Accreditation badge --}}
                            <template x-if="school.accreditation && !isEditing">
                                <span :style="accreditationStyle(school.accreditation)"
                                    style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:7px;font-size:11px;font-weight:700"
                                    x-text="school.accreditation"></span>
                            </template>

                            {{-- Status badge --}}
                            <span class="dt-badge" :class="school.status === 'active' ? 'aktif' : 'nonaktif'"
                                x-text="school.status === 'active' ? '● Aktif' : '● Non-Aktif'"></span>
                        </div>
                    </div>
                </div>

                {{-- Right: Stats strip --}}
                <div class="stats-strip">
                    <div class="stat-cell">
                        <div class="stat-cell-val" x-text="isEditing ? (form.npsn || '—') : (school.npsn || '—')"></div>
                        <div class="stat-cell-lbl">NPSN</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-cell-val" x-text="isEditing ? (form.nss || '—') : (school.nss || '—')"></div>
                        <div class="stat-cell-lbl">NSS</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-cell-val accent" x-text="isEditing ? (form.kkm_default || '—') : (school.kkm_default || '—')"></div>
                        <div class="stat-cell-lbl">KKM Default</div>
                    </div>
                    <div class="stat-cell">
                        <div class="stat-cell-val" x-text="school.created_at"></div>
                        <div class="stat-cell-lbl">Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MAIN 3-COL GRID
    ══════════════════════════════════════════════ --}}
    <div class="detail-grid">

        {{-- ═══ KOLOM KIRI ═══ --}}
        <div class="detail-col">

            {{-- Informasi Umum --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''"
                style="animation-delay:.05s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-information-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Informasi Umum</div>
                            <div class="card-hd-sub-v2">Data identitas sekolah</div>
                        </div>
                    </div>
                    <div x-show="isEditing" class="editing-badge" style="display:none">
                        <i class="ri-edit-2-line"></i> Diedit
                    </div>
                </div>

                {{-- NPSN --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-barcode-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">NPSN</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" x-text="school.npsn || '—'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.npsn" type="text" class="edit-input mono-input" placeholder="Nomor Pokok Sekolah Nasional">
                        </template>
                    </div>
                </div>

                {{-- NSS --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-file-list-3-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">NSS</div>
                        <template x-if="!isEditing">
                            <div class="field-val-v2 is-mono" x-text="school.nss || '—'"></div>
                        </template>
                        <template x-if="isEditing">
                            <input x-model="form.nss" type="text" class="edit-input mono-input" placeholder="Nomor Statistik Sekolah">
                        </template>
                    </div>
                </div>

                {{-- Akreditasi --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-medal-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Akreditasi</div>
                        <template x-if="!isEditing">
                            <div>
                                <template x-if="school.accreditation">
                                    <span :style="accreditationStyle(school.accreditation)"
                                        style="display:inline-flex;padding:3px 14px;border-radius:7px;font-size:13px;font-weight:700"
                                        x-text="school.accreditation"></span>
                                </template>
                                <span x-show="!school.accreditation" class="field-val-v2 is-empty">Belum diisi</span>
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <select x-model="form.accreditation" class="edit-input">
                                <option value="">— Pilih Akreditasi —</option>
                                <template x-for="a in ['A','B','C','D','E']" :key="a">
                                    <option :value="a" x-text="'Akreditasi ' + a"></option>
                                </template>
                            </select>
                        </template>
                    </div>
                </div>

                {{-- KKM Default --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-bar-chart-grouped-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">KKM Default</div>
                        <template x-if="!isEditing">
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="field-val-v2" style="font-size:18px;color:var(--primary)" x-text="school.kkm_default || '—'"></span>
                                <span x-show="school.kkm_default" style="font-size:11px;color:var(--text-muted);font-weight:500">dari 100</span>
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <div style="display:flex;align-items:center;gap:8px">
                                <input x-model="form.kkm_default" type="number" min="0" max="100" class="edit-input" style="max-width:100px">
                                <span style="font-size:12px;color:var(--text-muted)">/100</span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- School Type (view only extra row) --}}
                <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                    <div class="field-ico"><i class="ri-school-line"></i></div>
                    <div class="field-content">
                        <div class="field-lbl-v2">Tipe Sekolah</div>
                        <template x-if="!isEditing">
                            <div>
                                <template x-if="school.school_type">
                                    <span :style="schoolTypeStyle(school.school_type)"
                                        style="display:inline-flex;padding:4px 14px;border-radius:999px;font-size:12px;font-weight:600"
                                        x-text="school.school_type"></span>
                                </template>
                                <span x-show="!school.school_type" class="field-val-v2 is-empty">Belum dipilih</span>
                            </div>
                        </template>
                        <template x-if="isEditing">
                            <div class="type-chips">
                                <template x-for="t in ['Elementary','Junior High','Senior High']" :key="t">
                                    <button type="button" class="type-chip"
                                        :class="form.school_type === t ? 'selected' : ''"
                                        @click="form.school_type = (form.school_type === t ? '' : t)">
                                        <i :class="form.school_type === t ? 'ri-checkbox-circle-fill' : 'ri-circle-line'"></i>
                                        <span x-text="t"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="errors.school_type" class="form-error" x-text="errors.school_type" style="margin-top:6px"></div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Alamat & Lokasi --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.1s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-map-pin-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Alamat & Lokasi</div>
                            <div class="card-hd-sub-v2">Wilayah operasional sekolah</div>
                        </div>
                    </div>
                </div>
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:0">
                    {{-- Alamat Lengkap --}}
                    <div class="address-box" style="margin-bottom:10px">
                        <i class="ri-map-pin-2-line address-ico"></i>
                        <div style="flex:1;min-width:0">
                            <div class="address-lbl">Alamat Lengkap</div>
                            <template x-if="!isEditing">
                                <div class="address-val" x-text="school.address || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <textarea x-model="form.address" class="edit-input" style="margin-top:4px" placeholder="Jl. ..."></textarea>
                            </template>
                        </div>
                    </div>

                    {{-- 2x2 grid: province, city, district, postal --}}
                    <div class="loc-grid">
                        <div class="loc-cell" :class="isEditing ? 'is-editing' : ''">
                            <div class="loc-lbl">Provinsi</div>
                            <template x-if="!isEditing">
                                <div class="loc-val" :class="!school.province ? 'empty' : ''" x-text="school.province || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.province" type="text" class="edit-input" style="margin-top:5px;height:34px" placeholder="Provinsi">
                            </template>
                        </div>
                        <div class="loc-cell" :class="isEditing ? 'is-editing' : ''">
                            <div class="loc-lbl">Kota / Kab.</div>
                            <template x-if="!isEditing">
                                <div class="loc-val" :class="!school.city ? 'empty' : ''" x-text="school.city || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.city" type="text" class="edit-input" style="margin-top:5px;height:34px" placeholder="Kota/Kabupaten">
                            </template>
                        </div>
                        <div class="loc-cell" :class="isEditing ? 'is-editing' : ''">
                            <div class="loc-lbl">Kecamatan</div>
                            <template x-if="!isEditing">
                                <div class="loc-val" :class="!school.district ? 'empty' : ''" x-text="school.district || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.district" type="text" class="edit-input" style="margin-top:5px;height:34px" placeholder="Kecamatan">
                            </template>
                        </div>
                        <div class="loc-cell" :class="isEditing ? 'is-editing' : ''">
                            <div class="loc-lbl">Kode Pos</div>
                            <template x-if="!isEditing">
                                <div class="loc-val" :class="!school.postal_code ? 'empty' : ''" x-text="school.postal_code || '—'"></div>
                            </template>
                            <template x-if="isEditing">
                                <input x-model="form.postal_code" type="text" class="edit-input" style="margin-top:5px;height:34px" placeholder="55xxx">
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ KOLOM TENGAH ═══ --}}
        <div class="detail-col">

            {{-- Kepala Sekolah --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.08s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-user-star-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Kepala Sekolah</div>
                            <div class="card-hd-sub-v2">Pimpinan sekolah</div>
                        </div>
                    </div>
                </div>
                <div style="padding:18px 20px">
                    {{-- View --}}
                    <template x-if="!isEditing">
                        <template x-if="school.headmaster_name">
                            <div class="hm-display">
                                <div class="hm-av" x-text="initials(school.headmaster_name)"></div>
                                <div>
                                    <div class="hm-name" x-text="school.headmaster_name"></div>
                                    <div class="hm-role">Kepala Sekolah</div>
                                    <template x-if="school.headmaster_nip">
                                        <div class="hm-nip">
                                            <i class="ri-id-card-line" style="font-size:11px"></i>
                                            NIP: <span x-text="school.headmaster_nip" style="font-family:var(--font-mono)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="!school.headmaster_name">
                            <div style="text-align:center;padding:32px 20px">
                                <div style="width:52px;height:52px;border-radius:14px;background:var(--bg);border:1px solid var(--border);display:grid;place-items:center;margin:0 auto 10px;font-size:22px;color:var(--text-muted)">
                                    <i class="ri-user-line"></i>
                                </div>
                                <div style="font-size:13px;color:var(--text-muted)">Belum ada data kepala sekolah</div>
                            </div>
                        </template>
                    </template>

                    {{-- Edit --}}
                    <template x-if="isEditing">
                        <div>
                            <div class="form-group-v2">
                                <label class="form-label-v2">Nama Kepala Sekolah</label>
                                <input x-model="form.headmaster_name" type="text" class="edit-input" placeholder="Nama lengkap beserta gelar">
                            </div>
                            <div class="form-group-v2">
                                <label class="form-label-v2">NIP Kepala Sekolah</label>
                                <input x-model="form.headmaster_nip" type="text" class="edit-input mono-input" placeholder="18 digit NIP">
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Kontak --}}
            <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.13s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-contacts-line"></i></div>
                        <div>
                            <div class="card-hd-title-v2">Informasi Kontak</div>
                            <div class="card-hd-sub-v2">Email, telepon, dan website</div>
                        </div>
                    </div>
                </div>
                <div style="padding:14px 18px">
                    {{-- View --}}
                    <template x-if="!isEditing">
                        <div>
                            <div class="contact-row">
                                <div class="contact-ico"><i class="ri-mail-send-line"></i></div>
                                <div style="flex:1;min-width:0">
                                    <div class="contact-lbl">Email</div>
                                    <template x-if="school.contact_email">
                                        <a :href="'mailto:' + school.contact_email" class="contact-val" x-text="school.contact_email"></a>
                                    </template>
                                    <span x-show="!school.contact_email" class="contact-val empty">Belum diisi</span>
                                </div>
                            </div>
                            <div class="contact-row">
                                <div class="contact-ico"><i class="ri-phone-line"></i></div>
                                <div style="flex:1;min-width:0">
                                    <div class="contact-lbl">Telepon</div>
                                    <template x-if="school.contact_phone">
                                        <a :href="'tel:' + school.contact_phone" class="contact-val" x-text="school.contact_phone"></a>
                                    </template>
                                    <span x-show="!school.contact_phone" class="contact-val empty">Belum diisi</span>
                                </div>
                            </div>
                            <div class="contact-row">
                                <div class="contact-ico"><i class="ri-global-line"></i></div>
                                <div style="flex:1;min-width:0">
                                    <div class="contact-lbl">Website</div>
                                    <template x-if="school.website">
                                        <a :href="school.website" target="_blank" class="contact-val" x-text="school.website"></a>
                                    </template>
                                    <span x-show="!school.website" class="contact-val empty">Belum diisi</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Edit --}}
                    <template x-if="isEditing">
                        <div>
                            <div class="form-group-v2">
                                <label class="form-label-v2"><i class="ri-mail-line"></i> Email</label>
                                <input x-model="form.contact_email" type="email" class="edit-input" placeholder="admin@sekolah.sch.id">
                            </div>
                            <div class="form-group-v2">
                                <label class="form-label-v2"><i class="ri-phone-line"></i> Telepon</label>
                                <input x-model="form.contact_phone" type="tel" class="edit-input" placeholder="+62 ...">
                            </div>
                            <div class="form-group-v2">
                                <label class="form-label-v2"><i class="ri-global-line"></i> Website</label>
                                <input x-model="form.website" type="text" class="edit-input" placeholder="https://...">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ═══ KOLOM KANAN (sidebar) ═══ --}}
        <div class="detail-col">

            {{-- Status Sekolah --}}
            <div class="card-v2" style="animation-delay:.06s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-pulse-line"></i></div>
                        <div><div class="card-hd-title-v2">Status Sekolah</div></div>
                    </div>
                </div>
                <div style="padding:16px">
                    <div class="status-toggle-v2"
                        :class="school.status === 'active' ? 'active' : 'inactive'"
                        @click="toggleStatus()">
                        <div>
                            <div class="status-toggle-label"
                                :style="school.status === 'active' ? 'color:#065F46' : 'color:#475569'"
                                x-text="school.status === 'active' ? 'Aktif' : 'Non-Aktif'"></div>
                            <div class="status-toggle-hint">Klik untuk ubah status</div>
                        </div>
                        <div class="toggle-pill" :class="school.status === 'active' ? 'on' : 'off'">
                            <div class="toggle-pill-thumb" :class="school.status === 'active' ? 'on' : 'off'"></div>
                        </div>
                    </div>
                    <div class="meta-list">
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-calendar-event-line"></i> Terdaftar</div>
                            <div class="meta-item-val" x-text="school.created_at"></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-lbl"><i class="ri-refresh-line"></i> Diperbarui</div>
                            <div class="meta-item-val" x-text="school.updated_at"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="card-v2" style="animation-delay:.12s">
                <div class="card-hd-v2">
                    <div class="card-hd-left-v2">
                        <div class="card-hd-icon-v2"><i class="ri-flashlight-line"></i></div>
                        <div><div class="card-hd-title-v2">Aksi Cepat</div></div>
                    </div>
                </div>
                <div style="padding:14px;display:flex;flex-direction:column;gap:8px">

                    {{-- Edit / Simpan --}}
                    <template x-if="!isEditing">
                        <button @click="startEdit()" class="qa-btn">
                            <i class="ri-pencil-fill"></i> Edit Informasi Sekolah
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

                    {{-- Toggle Status --}}
                    <button @click="toggleStatus()" class="qa-btn"
                        :class="school.status === 'active' ? 'amber' : ''"
                        :style="school.status !== 'active' ? 'border-color:#A7F3D0;background:#ECFDF5;color:#065F46' : ''">
                        <i :class="school.status === 'active' ? 'ri-pause-circle-line' : 'ri-play-circle-line'"></i>
                        <span x-text="school.status === 'active' ? 'Nonaktifkan Sekolah' : 'Aktifkan Sekolah'"></span>
                    </button>

                    {{-- Delete --}}
                    <button @click="deleteSchool()" class="qa-btn red">
                        <i class="ri-delete-bin-6-fill"></i> Hapus Sekolah
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection