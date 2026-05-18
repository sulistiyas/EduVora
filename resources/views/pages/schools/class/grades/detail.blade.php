@extends('layouts.app')

@section('title', 'Detail Kelas')

@push('styles')
<style>
@keyframes spin   { to { transform:rotate(360deg) } }
@keyframes fadeUp { from { opacity:0;transform:translateY(10px) } to { opacity:1;transform:none } }

/* ── HERO ──────────────────────────────────── */
.grade-hero {
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow);
    overflow:hidden;margin-bottom:20px;animation:fadeUp .4s ease both;
}
.hero-banner {
    height:100px;
    background:linear-gradient(135deg,#064E3B 0%,#065F46 30%,#047857 55%,#059669 75%,#34D399 100%);
    position:relative;overflow:hidden;
}
.hero-banner::before {
    content:'';position:absolute;width:280px;height:280px;border-radius:50%;
    background:rgba(255,255,255,.04);top:-120px;right:-60px;
}
.hero-banner::after {
    content:'';position:absolute;width:160px;height:160px;border-radius:50%;
    background:rgba(255,255,255,.03);bottom:-80px;left:40px;
}
.hero-body { padding:60px 28px 24px; }
.hero-identity {
    display:flex;align-items:flex-end;justify-content:space-between;
    gap:20px;flex-wrap:wrap;margin-top:-60px;
}
.hero-left { display:flex;align-items:flex-end;gap:14px; }
.grade-avatar {
    width:80px;height:80px;border-radius:16px;
    background:linear-gradient(135deg,#065F46,#059669);
    display:grid;place-items:center;font-size:26px;font-weight:700;color:#fff;
    border:4px solid var(--card);box-shadow:0 8px 24px rgba(5,150,105,.28);flex-shrink:0;
}
.hero-name { font-size:20px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-bottom:6px; }

/* ── TAB NAV ───────────────────────────────── */
.tab-nav {
    display:flex;gap:2px;padding:0;margin-bottom:20px;
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow);
    overflow:hidden;animation:fadeUp .35s ease both;
}
.tab-btn {
    display:flex;align-items:center;gap:8px;
    padding:13px 20px;font-family:var(--font);
    font-size:13px;font-weight:600;
    color:var(--text-muted);background:transparent;
    border:none;cursor:pointer;transition:all .2s;
    border-bottom:2px solid transparent;position:relative;
    white-space:nowrap;
}
.tab-btn i { font-size:15px; }
.tab-btn:hover { color:var(--text-primary);background:var(--bg); }
.tab-btn.active {
    color:#059669;
    border-bottom-color:#059669;
    background:linear-gradient(to bottom,transparent,rgba(5,150,105,.04));
}
.tab-btn .tab-count {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:20px;height:20px;padding:0 6px;
    border-radius:999px;font-size:10.5px;font-weight:700;
    background:var(--border);color:var(--text-muted);transition:all .2s;
}
.tab-btn.active .tab-count { background:#ECFDF5;color:#059669; }

/* ── CARD V2 ───────────────────────────────── */
.card-v2 {
    background:var(--card);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow);
    overflow:visible;
    transition:border-color .2s,box-shadow .2s;animation:fadeUp .4s ease both;
}
.card-v2.is-editing-card { border-color:#6EE7B7;box-shadow:0 0 0 3px rgba(5,150,105,.1),var(--shadow); }
.card-hd-v2 {
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 20px;border-bottom:1px solid var(--border);background:var(--card);
    border-radius:var(--radius) var(--radius) 0 0;
}
.card-hd-left-v2 { display:flex;align-items:center;gap:10px; }
.card-hd-icon-v2 {
    width:30px;height:30px;border-radius:8px;background:var(--primary-xlight);
    display:grid;place-items:center;font-size:14px;color:var(--primary);flex-shrink:0;
}
.card-hd-icon-v2.green { background:#ECFDF5;color:#059669; }
.card-hd-title-v2 { font-size:13px;font-weight:700;color:var(--text-primary); }
.card-hd-sub-v2   { font-size:11px;color:var(--text-muted);margin-top:1px; }

/* ── FIELD ROW ─────────────────────────────── */
.field-row-v2 {
    display:flex;align-items:flex-start;gap:12px;
    padding:12px 20px;border-bottom:1px solid var(--border);transition:background .15s;
}
.field-row-v2:last-child { border-bottom:none; }
.field-row-v2.is-editing { background:#F0FDF4; }
.field-ico {
    width:30px;height:30px;border-radius:8px;background:var(--primary-xlight);
    display:grid;place-items:center;flex-shrink:0;margin-top:1px;
    font-size:13px;color:var(--primary);transition:all .15s;
}
.field-ico.green { background:#ECFDF5;color:#059669; }
.field-row-v2.is-editing .field-ico { background:#059669;color:#fff; }
.field-content { flex:1;min-width:0; }
.field-lbl-v2 { font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.7px;margin-bottom:3px; }
.field-val-v2 { font-size:13.5px;font-weight:600;color:var(--text-primary);line-height:1.4; }
.field-val-v2.is-empty { color:var(--text-muted);font-style:italic;font-weight:400; }

/* ── EDIT INPUT ────────────────────────────── */
.edit-input {
    width:100%;height:36px;padding:0 11px;
    border:1.5px solid var(--border);border-radius:var(--radius-sm);
    background:var(--card);font-family:var(--font);font-size:13px;
    color:var(--text-primary);outline:none;transition:all .18s;
}
.edit-input:focus { border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.12); }
select.edit-input {
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='%2394A3B8'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 9px center;
    background-color:var(--card);cursor:pointer;padding-right:32px;
}
.form-error { font-size:11px;color:var(--danger);margin-top:4px; }

/* ── STATUS PANEL ──────────────────────────── */
.status-toggle-v2 {
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 16px;border-radius:var(--radius-sm);border:1.5px solid;
    cursor:pointer;transition:all .2s;margin-bottom:14px;
}
.status-toggle-v2.active    { border-color:#6EE7B7;background:linear-gradient(135deg,#ECFDF5,#F0FDF4); }
.status-toggle-v2.inactive  { border-color:#CBD5E1;background:var(--bg); }
.status-toggle-v2.graduated { border-color:#BFDBFE;background:linear-gradient(135deg,#EFF6FF,#DBEAFE); }
.status-toggle-v2.archived  { border-color:#D1D5DB;background:#F9FAFB; }
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
.save-btn-v2 { display:flex;align-items:center;justify-content:center;gap:7px;width:100%;height:42px;border-radius:var(--radius-sm);border:none;background:#059669;color:#fff;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .18s;box-shadow:0 4px 14px rgba(5,150,105,.35); }
.save-btn-v2:hover { background:#047857;box-shadow:0 6px 20px rgba(5,150,105,.45);transform:translateY(-1px); }
.save-btn-v2:disabled { opacity:.6;cursor:not-allowed;transform:none; }
.cancel-btn-v2 { display:flex;align-items:center;justify-content:center;gap:6px;width:100%;height:36px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);color:var(--text-secondary);font-family:var(--font);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s; }

/* ── DETAIL GRID ───────────────────────────── */
.detail-grid { display:grid;grid-template-columns:1fr 320px;gap:18px;align-items:start; }
.detail-col  { display:flex;flex-direction:column;gap:18px; }

/* ── INFO GRID (2 col dalam kolom kiri) ────── */
.info-grid { display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start; }

@media (max-width:860px) { .info-grid { grid-template-columns:1fr !important; } }

/* ── EDIT BANNER ───────────────────────────── */
.edit-banner-v2 {
    display:flex;align-items:center;gap:10px;padding:12px 18px;
    background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border:1px solid #6EE7B7;
    border-radius:var(--radius-sm);margin-bottom:20px;font-size:13px;
    color:#065F46;font-weight:500;animation:fadeUp .25s ease both;
}
.edit-banner-cancel { background:none;border:none;cursor:pointer;color:#065F46;font-weight:700;font-size:13px;text-decoration:underline;padding:0;margin-left:auto; }

/* ── LEVEL BADGE ───────────────────────────── */
.level-badge {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:44px;height:32px;padding:0 10px;
    border-radius:8px;background:var(--primary-xlight);color:var(--primary);
    font-size:14px;font-weight:700;
}

/* ── SUBJECT TABLE ─────────────────────────── */
.subj-table { width:100%;border-collapse:collapse; }
.subj-table th {
    padding:9px 14px;font-size:10.5px;font-weight:700;
    text-transform:uppercase;letter-spacing:.6px;
    color:var(--text-muted);border-bottom:1.5px solid var(--border);
    text-align:left;background:var(--bg);
}
.subj-table td {
    padding:11px 14px;font-size:13px;border-bottom:1px solid var(--border);
    color:var(--text-primary);vertical-align:middle;
}
.subj-table tr:last-child td { border-bottom:none; }
.subj-table tr:hover td { background:var(--bg); }

/* ── ADD SUBJECT ROW ───────────────────────── */
.add-subject-row {
    display:grid;grid-template-columns:1fr 1fr auto;gap:8px;
    padding:12px 16px;background:var(--bg);border-top:1.5px dashed var(--border);
    border-radius:0 0 var(--radius) var(--radius);
    align-items:center;
}
.add-subject-select {
    height:36px;padding:0 10px;
    border:1.5px solid var(--border);border-radius:var(--radius-sm);
    background:var(--card);font-family:var(--font);font-size:13px;
    color:var(--text-primary);outline:none;transition:border-color .15s;
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='%2394A3B8'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 8px center;
    background-color:var(--card);padding-right:28px;cursor:pointer;
}
.add-subject-select:focus { border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.1); }
.add-subj-btn {
    display:inline-flex;align-items:center;gap:6px;
    height:36px;padding:0 14px;
    border-radius:var(--radius-sm);border:none;
    background:#059669;color:#fff;
    font-family:var(--font);font-size:13px;font-weight:600;
    cursor:pointer;white-space:nowrap;transition:background .15s;
}
.add-subj-btn:hover { background:#047857; }
.add-subj-btn:disabled { opacity:.5;cursor:not-allowed; }

/* ── KKM BADGE ─────────────────────────────── */
.kkm-badge {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:36px;height:22px;padding:0 7px;
    border-radius:6px;font-size:11px;font-weight:700;
    background:#FFF7ED;color:#C2410C;
    border:1px solid #FED7AA;
}

/* ── REMOVE SUBJ BTN ───────────────────────── */
.rm-subj-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border-radius:6px;
    border:1px solid var(--border);background:var(--card);
    color:var(--text-muted);cursor:pointer;transition:all .15s;
}
.rm-subj-btn:hover { border-color:#FCA5A5;background:#FEF2F2;color:var(--danger); }

/* ── EDIT KKM input ────────────────────────── */
.kkm-input {
    width:70px;height:30px;padding:0 8px;
    border:1.5px solid var(--border);border-radius:6px;
    background:var(--card);font-family:var(--font);font-size:12px;
    color:var(--text-primary);outline:none;text-align:center;
}
.kkm-input:focus { border-color:#059669; }

/* ── WEIGHT INPUTS ─────────────────────────── */
.weight-row { display:flex;gap:6px;align-items:center; }
.weight-input {
    width:54px;height:30px;padding:0 6px;
    border:1.5px solid var(--border);border-radius:6px;
    background:var(--card);font-family:var(--font);font-size:12px;
    color:var(--text-primary);outline:none;text-align:center;
}
.weight-input:focus { border-color:#059669; }
.weight-lbl { font-size:10px;color:var(--text-muted);font-weight:600; }

/* ── EMPTY ─────────────────────────────────── */
.subj-empty {
    padding:36px 20px;text-align:center;
    color:var(--text-muted);font-size:13px;
}
.subj-empty i { font-size:32px;display:block;margin-bottom:8px;opacity:.35; }

/* ── SKEL ──────────────────────────────────── */
.sk { background:var(--border);border-radius:4px;animation:pulse 1.4s ease-in-out infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

/* ── STUDENT TABLE ─────────────────────────── */
.stu-table { width:100%;border-collapse:collapse; }
.stu-table th {
    padding:9px 14px;font-size:10.5px;font-weight:700;
    text-transform:uppercase;letter-spacing:.6px;
    color:var(--text-muted);border-bottom:1.5px solid var(--border);
    text-align:left;background:var(--bg);
}
.stu-table td {
    padding:10px 14px;font-size:13px;border-bottom:1px solid var(--border);
    color:var(--text-primary);vertical-align:middle;
}
.stu-table tr:last-child td { border-bottom:none; }
.stu-table tr:hover td { background:var(--bg); }
.stu-table tr.is-selected td { background:#F0FDF4; }
.stu-checkbox {
    width:16px;height:16px;cursor:pointer;accent-color:#059669;
}

/* ── SEARCH INPUT ──────────────────────────── */
.search-input-sm {
    height:36px;padding:0 12px 0 36px;
    border:1.5px solid var(--border);border-radius:var(--radius-sm);
    background:var(--card);font-family:var(--font);font-size:13px;
    color:var(--text-primary);outline:none;transition:all .18s;width:100%;
}
.search-input-sm:focus { border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.1); }
.search-wrap { position:relative; }
.search-wrap i { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;pointer-events:none; }

/* ── GENDER BADGE ──────────────────────────── */
.gender-badge {
    display:inline-flex;align-items:center;justify-content:center;
    height:20px;padding:0 7px;border-radius:5px;font-size:10.5px;font-weight:700;
}
.gender-badge.L { background:#EFF6FF;color:#1D4ED8; }
.gender-badge.P { background:#FDF2F8;color:#9D174D; }

/* ── ASSIGN TOOLBAR ────────────────────────── */
.assign-toolbar {
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 16px;background:var(--bg);border-bottom:1px solid var(--border);
    gap:12px;flex-wrap:wrap;
}
.assign-selected-pill {
    display:inline-flex;align-items:center;gap:6px;
    padding:4px 12px;border-radius:999px;
    background:#ECFDF5;color:#059669;font-size:12px;font-weight:700;
    border:1px solid #6EE7B7;
}
.assign-btn {
    display:inline-flex;align-items:center;gap:7px;
    height:34px;padding:0 16px;border-radius:var(--radius-sm);
    border:none;background:#059669;color:#fff;
    font-family:var(--font);font-size:12.5px;font-weight:700;
    cursor:pointer;transition:all .15s;
    box-shadow:0 2px 8px rgba(5,150,105,.3);
}
.assign-btn:hover { background:#047857; }
.assign-btn:disabled { opacity:.5;cursor:not-allowed; }

/* ── ENROLLED STUDENT ──────────────────────── */
.enrolled-section {
    border-top:2px dashed var(--border);
    margin-top:4px;
}
.enrolled-hd {
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 16px;background:var(--bg);
}

@media (max-width:900px)  { .detail-grid { grid-template-columns:1fr !important; } }
@media (max-width:640px)  { .tab-btn span.tab-label { display:none; } .tab-btn { padding:13px 14px; } }
</style>
@endpush

@section('content')
<div
    x-data="gradeDetail({
        gradeId:          '{{ $grade['grade_id'] }}',
        gradeData:        {{ json_encode($grade) }},
        baseUrl:          '{{ url('grades') }}',
        indexUrl:         '{{ route('grades.index') }}',
        roomsUrl:         '{{ route('grades.rooms') }}',
        teachersUrl:      '{{ route('grades.teachers') }}',
        academicYearsUrl: '{{ route('grades.academic-years') }}',
        subjectsUrl:      '{{ route('grades.subjects') }}',
        gradeSubjectsUrl: '{{ route('grades.grade-subjects.index', $grade['grade_id']) }}',
        studentsUrl:      '{{ url('grades/students/list') }}',
        assignStudentsUrl:'{{ url('grades/' . $grade['grade_id'] . '/assign-students') }}',
    })"
    x-init="init()">

    {{-- ── BREADCRUMB ──────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap;animation:fadeUp .3s ease both">
        <div>
            <ul class="breadcrumb-list">
                <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Akademik</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><a :href="indexUrl" style="color:var(--text-muted);font-size:11px;font-weight:500;text-decoration:none">Manajemen Kelas</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span x-text="grade.grade_name"></span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;margin-top:5px" x-text="grade.grade_name"></h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">Detail informasi dan manajemen mata pelajaran kelas</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <a :href="indexUrl" class="dt-btn dt-btn-outline" style="height:38px">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <template x-if="!isEditing && activeTab === 'info'">
                <button @click="startEdit()" class="dt-btn dt-btn-primary" style="height:38px">
                    <i class="ri-pencil-line"></i> Edit Kelas
                </button>
            </template>
            <template x-if="isEditing">
                <div style="display:flex;gap:8px">
                    <button @click="cancelEdit()"
                        style="display:inline-flex;align-items:center;gap:6px;height:38px;padding:0 16px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer">
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

    {{-- ══ HERO CARD ══════════════════════════════════ --}}
    <div class="grade-hero">
        <div class="hero-banner"></div>
        <div class="hero-body">
            <div class="hero-identity">
                <div class="hero-left">
                    <div class="grade-avatar" x-text="initials(grade.grade_name)"></div>
                    <div style="padding-bottom:6px">
                        <div x-show="!isEditing" class="hero-name" x-text="grade.grade_name"></div>
                        <div x-show="isEditing" style="margin-bottom:8px">
                            <input x-model="form.grade_name" data-edit-focus type="text"
                                class="edit-input"
                                style="font-size:15px;font-weight:700;height:42px;min-width:260px;max-width:380px;border-color:#059669"
                                placeholder="Nama kelas">
                            <div x-show="errors.grade_name" class="form-error" x-text="errors.grade_name"></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <span class="level-badge" x-text="levelLabel(grade.level)"></span>
                            <button @click="toggleStatus()"
                                style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                :style="`background:${statusConfig(grade.status).bg};color:${statusConfig(grade.status).color};`">
                                <span style="width:7px;height:7px;border-radius:50%;flex-shrink:0"
                                      :style="`background:${statusConfig(grade.status).color}`"></span>
                                <span x-text="statusConfig(grade.status).label"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Stats strip --}}
                <div style="display:flex;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;flex-shrink:0;margin-bottom:6px;align-self:flex-end">
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:100px">
                        <div style="font-size:15px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="grade.academic_year?.academic_year_name || '—'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Tahun Ajaran</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:90px">
                        <div style="font-size:15px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="grade.room?.room_name || '—'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Ruangan</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;border-right:1px solid var(--border);min-width:90px">
                        <div style="font-size:15px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="gradeSubjects.length || '0'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Mapel</div>
                    </div>
                    <div style="padding:10px 18px;text-align:center;min-width:90px">
                        <div style="font-size:15px;font-weight:700;color:var(--text-primary);line-height:1"
                            x-text="enrolledStudents.length || '0'"></div>
                        <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.6px;text-transform:uppercase;margin-top:4px">Siswa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TAB NAVIGATION ══════════════════════════════ --}}
    <div class="tab-nav">
        <button class="tab-btn" :class="activeTab === 'info' ? 'active' : ''" @click="activeTab = 'info'">
            <i class="ri-information-line"></i>
            <span class="tab-label">Informasi Kelas</span>
        </button>
        <button class="tab-btn" :class="activeTab === 'subjects' ? 'active' : ''" @click="activeTab = 'subjects'">
            <i class="ri-book-open-line"></i>
            <span class="tab-label">Mata Pelajaran</span>
            <span class="tab-count" x-text="gradeSubjects.length"></span>
        </button>
        <button class="tab-btn" :class="activeTab === 'students' ? 'active' : ''" @click="activeTab = 'students'; fetchStudents()">
            <i class="ri-group-line"></i>
            <span class="tab-label">Siswa</span>
            <span class="tab-count" x-text="enrolledStudents.length"></span>
        </button>
    </div>

    {{-- ══ TAB: INFORMASI KELAS ══════════════════════════ --}}
    <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- Edit banner --}}
        <div x-show="isEditing" x-transition class="edit-banner-v2" style="display:none">
            <i class="ri-edit-2-fill"></i>
            <span>Mode edit aktif — ubah data yang ingin diperbarui, lalu klik <strong>Simpan Perubahan</strong></span>
            <button @click="cancelEdit()" class="edit-banner-cancel">Batal</button>
        </div>

        <div class="detail-grid">
            {{-- ═══ KOLOM KIRI ═══ --}}
            <div class="detail-col">
                <template x-if="isEditing">
                    <div class="card-v2 is-editing-card">
                        <div class="card-hd-v2">
                            <div class="card-hd-left-v2">
                                <div class="card-hd-icon-v2 green"><i class="ri-toggle-line"></i></div>
                                <div>
                                    <div class="card-hd-title-v2">Status Kelas</div>
                                    <div class="card-hd-sub-v2">Ubah status kelas</div>
                                </div>
                            </div>
                        </div>
                        <div class="field-row-v2 is-editing" style="border-bottom:none">
                            <div class="field-ico" style="background:#059669;color:#fff;align-self:center"><i class="ri-pulse-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Status</div>
                                <select x-model="form.status" class="edit-input" style="max-width:260px">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Non-Aktif</option>
                                    <option value="graduated">Lulus</option>
                                    <option value="archived">Diarsipkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="info-grid">
                    {{-- Card: Informasi Kelas --}}
                    <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''">
                        <div class="card-hd-v2">
                            <div class="card-hd-left-v2">
                                <div class="card-hd-icon-v2 green"><i class="ri-building-4-line"></i></div>
                                <div>
                                    <div class="card-hd-title-v2">Informasi Kelas</div>
                                    <div class="card-hd-sub-v2">Data dasar kelas</div>
                                </div>
                            </div>
                        </div>

                        <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                            <div class="field-ico green"><i class="ri-building-4-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Nama Kelas</div>
                                <template x-if="!isEditing">
                                    <div class="field-val-v2" x-text="grade.grade_name || '—'"></div>
                                </template>
                                <template x-if="isEditing">
                                    <div>
                                        <input x-model="form.grade_name" type="text" class="edit-input" placeholder="Nama kelas">
                                        <div x-show="errors.grade_name" class="form-error" x-text="errors.grade_name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                            <div class="field-ico green"><i class="ri-sort-number-asc"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Tingkatan</div>
                                <template x-if="!isEditing">
                                    <div class="field-val-v2" style="display:flex;align-items:center;gap:8px">
                                        <span class="level-badge" x-text="levelLabel(grade.level)"></span>
                                        <span style="font-size:13px;color:var(--text-muted)" x-text="'Kelas ' + (grade.level || '—')"></span>
                                    </div>
                                </template>
                                <template x-if="isEditing">
                                    <div>
                                        <select x-model="form.level" class="edit-input">
                                            <option value="">— Pilih Tingkatan —</option>
                                            <template x-for="opt in levelOptions" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </select>
                                        <div x-show="errors.level" class="form-error" x-text="errors.level"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                            <div class="field-ico green"><i class="ri-calendar-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Tahun Ajaran</div>
                                <template x-if="!isEditing">
                                    <div class="field-val-v2" :class="!grade.academic_year ? 'is-empty' : ''"
                                        x-text="grade.academic_year?.academic_year_name || 'Belum ditentukan'"></div>
                                </template>
                                <template x-if="isEditing">
                                    <div>
                                        <select x-model="form.academic_year_id" class="edit-input">
                                            <option value="">— Pilih Tahun Ajaran —</option>
                                            <template x-for="ay in academicYears" :key="ay.academic_year_id">
                                                <option :value="ay.academic_year_id" x-text="ay.academic_year_name"></option>
                                            </template>
                                        </select>
                                        <div x-show="errors.academic_year_id" class="form-error" x-text="errors.academic_year_id"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="field-row-v2" :class="isEditing ? 'is-editing' : ''">
                            <div class="field-ico green"><i class="ri-user-star-line"></i></div>
                            <div class="field-content">
                                <div class="field-lbl-v2">Wali Kelas</div>
                                <template x-if="!isEditing">
                                    <div>
                                        <div class="field-val-v2" :class="!grade.homeroom_teacher ? 'is-empty' : ''"
                                            x-text="grade.homeroom_teacher?.full_name || 'Belum ditentukan'"></div>
                                        <div x-show="grade.homeroom_teacher?.nip"
                                            style="font-size:11.5px;color:var(--text-muted);margin-top:2px;font-family:var(--font-mono)"
                                            x-text="`NIP: ${grade.homeroom_teacher?.nip ?? ''}`"></div>
                                    </div>
                                </template>
                                <template x-if="isEditing">
                                    <select x-model="form.homeroom_teacher_id" class="edit-input">
                                        <option value="">— Belum Ditentukan —</option>
                                        <template x-for="teacher in teachers" :key="teacher.teacher_id">
                                            <option :value="teacher.teacher_id"
                                                x-text="teacher.full_name + (teacher.nip ? ` — ${teacher.nip}` : '')"></option>
                                        </template>
                                    </select>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Card kanan: Ruangan + Tahun Ajaran --}}
                    <div class="detail-col" style="gap:18px">
                        <div class="card-v2" :class="isEditing ? 'is-editing-card' : ''" style="animation-delay:.06s">
                            <div class="card-hd-v2">
                                <div class="card-hd-left-v2">
                                    <div class="card-hd-icon-v2 green"><i class="ri-door-open-line"></i></div>
                                    <div>
                                        <div class="card-hd-title-v2">Ruangan</div>
                                        <div class="card-hd-sub-v2" x-text="grade.room?.room_name || 'Belum ditentukan'"></div>
                                    </div>
                                </div>
                            </div>
                            <template x-if="isEditing">
                                <div class="field-row-v2 is-editing" style="border-bottom:none">
                                    <div class="field-ico" style="background:#059669;color:#fff"><i class="ri-door-open-line"></i></div>
                                    <div class="field-content">
                                        <div class="field-lbl-v2">Pilih Ruangan</div>
                                        <select x-model="form.room_id" class="edit-input">
                                            <option value="">— Tidak Ada / Kosongkan —</option>
                                            <template x-for="room in rooms" :key="room.room_id">
                                                <option :value="room.room_id" x-text="room.room_name + (room.code ? ` (${room.code})` : '')"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!isEditing && grade.room">
                                <div>
                                    <div class="field-row-v2">
                                        <div class="field-ico green"><i class="ri-code-s-slash-line"></i></div>
                                        <div class="field-content">
                                            <div class="field-lbl-v2">Kode</div>
                                            <div class="field-val-v2" :class="!grade.room?.code ? 'is-empty' : ''" x-text="grade.room?.code || '—'"></div>
                                        </div>
                                    </div>
                                    <div class="field-row-v2">
                                        <div class="field-ico green"><i class="ri-building-line"></i></div>
                                        <div class="field-content">
                                            <div class="field-lbl-v2">Gedung / Lantai</div>
                                            <div class="field-val-v2"
                                                x-text="[grade.room?.building ? `Gedung ${grade.room.building}` : '', grade.room?.floor ? `Lantai ${grade.room.floor}` : ''].filter(Boolean).join(', ') || '—'"></div>
                                        </div>
                                    </div>
                                    <div class="field-row-v2">
                                        <div class="field-ico green"><i class="ri-group-line"></i></div>
                                        <div class="field-content">
                                            <div class="field-lbl-v2">Kapasitas</div>
                                            <div class="field-val-v2" :class="!grade.room?.capacity ? 'is-empty' : ''"
                                                x-text="grade.room?.capacity ? grade.room.capacity + ' siswa' : '—'"></div>
                                        </div>
                                    </div>
                                    <div class="field-row-v2">
                                        <div class="field-ico green"><i class="ri-tools-line"></i></div>
                                        <div class="field-content">
                                            <div class="field-lbl-v2">Fasilitas</div>
                                            <div class="field-val-v2" :class="!grade.room?.facility ? 'is-empty' : ''"
                                                x-text="grade.room?.facility || 'Tidak ada info'"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!isEditing && !grade.room">
                                <div style="padding:20px 16px;text-align:center;color:var(--text-muted)">
                                    <i class="ri-door-open-line" style="font-size:24px;opacity:.3;display:block;margin-bottom:6px"></i>
                                    <div style="font-size:12.5px">Belum ada ruangan</div>
                                </div>
                            </template>
                        </div>

                        <div class="card-v2" style="animation-delay:.08s">
                            <div class="card-hd-v2">
                                <div class="card-hd-left-v2">
                                    <div class="card-hd-icon-v2 green"><i class="ri-calendar-2-line"></i></div>
                                    <div>
                                        <div class="card-hd-title-v2">Tahun Ajaran</div>
                                        <div class="card-hd-sub-v2" x-text="grade.academic_year?.academic_year_name || '—'"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="field-row-v2">
                                <div class="field-ico green"><i class="ri-play-circle-line"></i></div>
                                <div class="field-content">
                                    <div class="field-lbl-v2">Mulai</div>
                                    <div class="field-val-v2"
                                        x-text="grade.academic_year?.start_date ? new Date(grade.academic_year.start_date).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                                </div>
                            </div>
                            <div class="field-row-v2">
                                <div class="field-ico green"><i class="ri-stop-circle-line"></i></div>
                                <div class="field-content">
                                    <div class="field-lbl-v2">Selesai</div>
                                    <div class="field-val-v2"
                                        x-text="grade.academic_year?.end_date ? new Date(grade.academic_year.end_date).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                                </div>
                            </div>
                            <div class="field-row-v2">
                                <div class="field-ico green"><i class="ri-radar-line"></i></div>
                                <div class="field-content">
                                    <div class="field-lbl-v2">Status</div>
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600"
                                        :style="grade.academic_year?.status === 'active' ? 'background:#ECFDF5;color:#059669' : 'background:#F3F4F6;color:#6B7280'"
                                        x-text="grade.academic_year?.status === 'active' ? '● Aktif' : '● ' + (grade.academic_year?.status || '—')">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ KOLOM KANAN (sidebar) ═══ --}}
            <div class="detail-col">
                <div class="card-v2" style="animation-delay:.06s">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2 green"><i class="ri-pulse-line"></i></div>
                            <div><div class="card-hd-title-v2">Status Kelas</div></div>
                        </div>
                    </div>
                    <div style="padding:16px">
                        <div class="status-toggle-v2" :class="grade.status" @click="toggleStatus()">
                            <div>
                                <div style="font-size:14px;font-weight:700"
                                    :style="grade.status === 'active' ? 'color:#065F46' : grade.status === 'graduated' ? 'color:#1D4ED8' : 'color:#475569'"
                                    x-text="statusConfig(grade.status).label"></div>
                                <div style="font-size:11px;color:var(--text-muted)">Klik untuk ubah status</div>
                            </div>
                            <div class="toggle-pill" :class="grade.status === 'active' ? 'on' : 'off'">
                                <div class="toggle-pill-thumb" :class="grade.status === 'active' ? 'on' : 'off'"></div>
                            </div>
                        </div>
                        <div class="meta-list">
                            <div class="meta-item">
                                <div class="meta-item-lbl"><i class="ri-calendar-event-line"></i> Dibuat</div>
                                <div class="meta-item-val"
                                    x-text="grade.created_at ? new Date(grade.created_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-item-lbl"><i class="ri-refresh-line"></i> Diperbarui</div>
                                <div class="meta-item-val"
                                    x-text="grade.updated_at ? new Date(grade.updated_at).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '—'"></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-item-lbl"><i class="ri-book-open-line"></i> Total Mapel</div>
                                <div class="meta-item-val" x-text="gradeSubjects.length"></div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-item-lbl"><i class="ri-group-line"></i> Total Siswa</div>
                                <div class="meta-item-val" x-text="enrolledStudents.length"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-v2" style="animation-delay:.1s">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2 green"><i class="ri-flashlight-line"></i></div>
                            <div><div class="card-hd-title-v2">Aksi Cepat</div></div>
                        </div>
                    </div>
                    <div style="padding:14px;display:flex;flex-direction:column;gap:8px">
                        <template x-if="!isEditing">
                            <button @click="startEdit()" class="qa-btn">
                                <i class="ri-pencil-fill"></i> Edit Informasi Kelas
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
                            :class="grade.status === 'active' ? 'amber' : ''"
                            :style="grade.status !== 'active' ? 'border-color:#A7F3D0;background:#ECFDF5;color:#065F46' : ''">
                            <i :class="grade.status === 'active' ? 'ri-pause-circle-line' : 'ri-play-circle-line'"></i>
                            <span x-text="grade.status === 'active' ? 'Nonaktifkan Kelas' : 'Aktifkan Kelas'"></span>
                        </button>
                        <button @click="activeTab = 'students'; fetchStudents()" class="qa-btn">
                            <i class="ri-user-add-line"></i> Kelola Siswa
                        </button>
                        <button @click="deleteGrade()" class="qa-btn red">
                            <i class="ri-delete-bin-6-fill"></i> Hapus Kelas
                        </button>
                    </div>
                </div>

                <div class="card-v2" style="animation-delay:.12s">
                    <div class="card-hd-v2">
                        <div class="card-hd-left-v2">
                            <div class="card-hd-icon-v2 green"><i class="ri-user-star-line"></i></div>
                            <div><div class="card-hd-title-v2">Wali Kelas</div></div>
                        </div>
                    </div>
                    <template x-if="grade.homeroom_teacher">
                        <div style="padding:14px 16px">
                            <div style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:var(--radius-sm);background:var(--bg);border:1px solid var(--border)">
                                <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#065F46,#059669);display:grid;place-items:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0"
                                    x-text="initials(grade.homeroom_teacher?.full_name)"></div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                        x-text="grade.homeroom_teacher?.full_name"></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;font-family:var(--font-mono)"
                                        x-text="grade.homeroom_teacher?.nip ? 'NIP: ' + grade.homeroom_teacher.nip : 'NIP belum diisi'"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!grade.homeroom_teacher">
                        <div style="padding:20px 16px;text-align:center;color:var(--text-muted)">
                            <i class="ri-user-unfollow-line" style="font-size:24px;opacity:.3;display:block;margin-bottom:6px"></i>
                            <div style="font-size:12.5px">Belum ada wali kelas</div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TAB: MATA PELAJARAN ══════════════════════════ --}}
    <div x-show="activeTab === 'subjects'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:14px;flex-wrap:wrap">
            <div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;margin:0">
                    <i class="ri-book-open-line" style="color:#059669;margin-right:6px"></i>
                    Mata Pelajaran Kelas
                </h3>
                <p style="font-size:12.5px;color:var(--text-muted);margin:3px 0 0">Kelola daftar mata pelajaran yang diajarkan di kelas ini</p>
            </div>
            <button @click="fetchGradeSubjects()"
                style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 12px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:12.5px;font-weight:600;color:var(--text-secondary);cursor:pointer">
                <i class="ri-refresh-line"></i> Refresh
            </button>
        </div>

        <div class="card-v2" style="animation:none">
            <div style="overflow-x:auto">
                <table class="subj-table">
                    <thead>
                        <tr>
                            <th style="width:36px">#</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru Pengajar</th>
                            <th style="width:90px;text-align:center">KKM</th>
                            <th style="width:220px;text-align:center">Bobot Nilai (H / UTS / UAS)</th>
                            <th style="width:70px;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="loadingSubjects">
                            <template x-for="i in 4" :key="i">
                                <tr>
                                    <td><div class="sk" style="width:20px;height:14px"></div></td>
                                    <td><div class="sk" style="width:140px;height:14px;margin-bottom:5px"></div><div class="sk" style="width:80px;height:11px"></div></td>
                                    <td><div class="sk" style="width:120px;height:14px"></div></td>
                                    <td style="text-align:center"><div class="sk" style="width:40px;height:22px;margin:0 auto;border-radius:6px"></div></td>
                                    <td style="text-align:center"><div class="sk" style="width:160px;height:30px;margin:0 auto;border-radius:6px"></div></td>
                                    <td style="text-align:center"><div class="sk" style="width:28px;height:28px;margin:0 auto;border-radius:6px"></div></td>
                                </tr>
                            </template>
                        </template>

                        <template x-if="!loadingSubjects && gradeSubjects.length > 0">
                            <template x-for="(gs, idx) in gradeSubjects" :key="gs.id">
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px;font-family:var(--font-mono)" x-text="idx + 1"></td>
                                    <td>
                                        <div style="font-size:13px;font-weight:600;color:var(--text-primary)" x-text="gs.subject?.subject_name || '—'"></div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:1px" x-text="gs.subject?.code || ''"></div>
                                    </td>
                                    <td>
                                        <template x-if="!gs._editingTeacher">
                                            <div style="display:flex;align-items:center;gap:8px">
                                                <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#065F46,#059669);display:grid;place-items:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0"
                                                    x-text="initials(gs.teacher?.full_name || '?')"></div>
                                                <div>
                                                    <div style="font-size:12.5px;font-weight:600;color:var(--text-primary)" :class="!gs.teacher ? 'is-empty' : ''"
                                                        x-text="gs.teacher?.full_name || 'Belum ditentukan'"></div>
                                                </div>
                                                <button @click="gs._editingTeacher = true"
                                                    style="margin-left:4px;display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:5px;border:1px solid var(--border);background:var(--card);color:var(--text-muted);cursor:pointer">
                                                    <i class="ri-pencil-line" style="font-size:11px"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="gs._editingTeacher">
                                            <div style="display:flex;gap:6px;align-items:center">
                                                <select x-model="gs._newTeacherId" class="add-subject-select" style="min-width:160px;font-size:12.5px">
                                                    <option value="">— Belum Ditentukan —</option>
                                                    <template x-for="t in teachers" :key="t.teacher_id">
                                                        <option :value="t.teacher_id" x-text="t.full_name"></option>
                                                    </template>
                                                </select>
                                                <button @click="updateSubjectTeacher(gs)"
                                                    style="display:inline-flex;align-items:center;justify-content:center;height:30px;padding:0 10px;border-radius:6px;border:none;background:#059669;color:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:var(--font)">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                                <button @click="gs._editingTeacher = false; gs._newTeacherId = gs.teacher_id || ''"
                                                    style="display:inline-flex;align-items:center;justify-content:center;height:30px;padding:0 8px;border-radius:6px;border:1.5px solid var(--border);background:var(--card);color:var(--text-muted);font-size:12px;cursor:pointer">
                                                    <i class="ri-close-line"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                    <td style="text-align:center">
                                        <template x-if="!gs._editingKkm">
                                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                                <span class="kkm-badge" x-text="gs.kkm ?? '—'"></span>
                                                <button @click="gs._editingKkm = true; gs._newKkm = gs.kkm ?? ''"
                                                    style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:4px;border:1px solid var(--border);background:var(--card);color:var(--text-muted);cursor:pointer">
                                                    <i class="ri-pencil-line" style="font-size:10px"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="gs._editingKkm">
                                            <div style="display:flex;flex-direction:column;align-items:center;gap:4px">
                                                <input x-model="gs._newKkm" type="number" min="0" max="100" class="kkm-input" placeholder="KKM">
                                                <div style="display:flex;gap:4px">
                                                    <button @click="updateSubjectKkm(gs)"
                                                        style="display:inline-flex;align-items:center;height:24px;padding:0 8px;border-radius:4px;border:none;background:#059669;color:#fff;font-size:11px;font-weight:600;cursor:pointer;font-family:var(--font)">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                    <button @click="gs._editingKkm = false"
                                                        style="display:inline-flex;align-items:center;height:24px;padding:0 8px;border-radius:4px;border:1.5px solid var(--border);background:var(--card);color:var(--text-muted);font-size:11px;cursor:pointer">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                    <td style="text-align:center">
                                        <template x-if="!gs._editingWeight">
                                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                                <div style="display:flex;gap:4px;align-items:center">
                                                    <span style="font-size:11px;font-weight:600;padding:2px 7px;border-radius:5px;background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0" x-text="(gs.weight_harian ?? 0) + '%'"></span>
                                                    <span style="font-size:10px;color:var(--text-muted)">/</span>
                                                    <span style="font-size:11px;font-weight:600;padding:2px 7px;border-radius:5px;background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE" x-text="(gs.weight_uts ?? 0) + '%'"></span>
                                                    <span style="font-size:10px;color:var(--text-muted)">/</span>
                                                    <span style="font-size:11px;font-weight:600;padding:2px 7px;border-radius:5px;background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA" x-text="(gs.weight_uas ?? 0) + '%'"></span>
                                                </div>
                                                <button @click="gs._editingWeight = true; gs._newWeightH = gs.weight_harian ?? 0; gs._newWeightUts = gs.weight_uts ?? 0; gs._newWeightUas = gs.weight_uas ?? 0"
                                                    style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:4px;border:1px solid var(--border);background:var(--card);color:var(--text-muted);cursor:pointer">
                                                    <i class="ri-pencil-line" style="font-size:10px"></i>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="gs._editingWeight">
                                            <div style="display:flex;flex-direction:column;align-items:center;gap:5px">
                                                <div class="weight-row">
                                                    <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
                                                        <span class="weight-lbl">Harian</span>
                                                        <input x-model="gs._newWeightH" type="number" min="0" max="100" class="weight-input">
                                                    </div>
                                                    <span style="color:var(--text-muted);font-size:13px">/</span>
                                                    <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
                                                        <span class="weight-lbl">UTS</span>
                                                        <input x-model="gs._newWeightUts" type="number" min="0" max="100" class="weight-input">
                                                    </div>
                                                    <span style="color:var(--text-muted);font-size:13px">/</span>
                                                    <div style="display:flex;flex-direction:column;align-items:center;gap:2px">
                                                        <span class="weight-lbl">UAS</span>
                                                        <input x-model="gs._newWeightUas" type="number" min="0" max="100" class="weight-input">
                                                    </div>
                                                </div>
                                                <div style="display:flex;gap:4px">
                                                    <button @click="updateSubjectWeight(gs)"
                                                        style="display:inline-flex;align-items:center;height:24px;padding:0 8px;border-radius:4px;border:none;background:#059669;color:#fff;font-size:11px;font-weight:600;cursor:pointer;font-family:var(--font)">
                                                        <i class="ri-check-line"></i> Simpan
                                                    </button>
                                                    <button @click="gs._editingWeight = false"
                                                        style="display:inline-flex;align-items:center;height:24px;padding:0 8px;border-radius:4px;border:1.5px solid var(--border);background:var(--card);color:var(--text-muted);font-size:11px;cursor:pointer">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </td>
                                    <td style="text-align:center">
                                        <button @click="removeSubject(gs)" class="rm-subj-btn" title="Hapus mapel dari kelas">
                                            <i class="ri-delete-bin-6-line" style="font-size:13px"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="!loadingSubjects && gradeSubjects.length === 0" class="subj-empty">
                <i class="ri-book-open-line"></i>
                <div style="font-weight:600;margin-bottom:4px">Belum ada mata pelajaran</div>
                <div style="font-size:12px">Tambahkan mata pelajaran menggunakan form di bawah</div>
            </div>

            <div class="add-subject-row">
                <div>
                    <div style="font-size:10.5px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px">Tambah Mata Pelajaran</div>
                    <select x-model="newSubjectId" class="add-subject-select" style="width:100%">
                        <option value="">— Pilih Mata Pelajaran —</option>
                        <template x-for="subj in availableSubjects" :key="subj.id">
                            <option :value="subj.id" x-text="subj.subject_name + (subj.code ? ` (${subj.code})` : '')"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <div style="font-size:10.5px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px">Guru Pengajar</div>
                    <select x-model="newTeacherId" class="add-subject-select" style="width:100%">
                        <option value="">— Belum Ditentukan —</option>
                        <template x-for="t in teachers" :key="t.teacher_id">
                            <option :value="t.teacher_id" x-text="t.full_name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <div style="font-size:10.5px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px">&nbsp;</div>
                    <button @click="addSubject()" :disabled="!newSubjectId || addingSubject" class="add-subj-btn">
                        <svg x-show="addingSubject" style="width:13px;height:13px;animation:spin 1s linear infinite;flex-shrink:0" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.75"></path>
                        </svg>
                        <i x-show="!addingSubject" class="ri-add-line"></i>
                        <span x-text="addingSubject ? 'Menambah...' : 'Tambah'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ TAB: SISWA ══════════════════════════════════ --}}
    <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

        {{-- ── Siswa Terdaftar di Kelas Ini ── --}}
        <div style="margin-bottom:18px">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:14px;flex-wrap:wrap">
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;margin:0">
                        <i class="ri-group-line" style="color:#059669;margin-right:6px"></i>
                        Siswa Terdaftar
                    </h3>
                    <p style="font-size:12.5px;color:var(--text-muted);margin:3px 0 0">
                        Siswa yang sudah masuk ke kelas <strong x-text="grade.grade_name"></strong>
                    </p>
                </div>
                <button @click="fetchEnrolledStudents()"
                    style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 12px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:12.5px;font-weight:600;color:var(--text-secondary);cursor:pointer">
                    <i class="ri-refresh-line"></i> Refresh
                </button>
            </div>

            <div class="card-v2" style="animation:none">
                <div style="overflow-x:auto">
                    <table class="stu-table">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th>Siswa</th>
                                <th style="width:100px">NIS</th>
                                <th style="width:80px;text-align:center">Gender</th>
                                <th style="width:80px;text-align:center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Skeleton --}}
                            <template x-if="loadingStudents">
                                <template x-for="i in 4" :key="i">
                                    <tr>
                                        <td><div class="sk" style="width:20px;height:14px"></div></td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px">
                                                <div class="sk" style="width:32px;height:32px;border-radius:8px;flex-shrink:0"></div>
                                                <div>
                                                    <div class="sk" style="width:130px;height:13px;margin-bottom:5px"></div>
                                                    <div class="sk" style="width:80px;height:10px"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><div class="sk" style="width:70px;height:13px"></div></td>
                                        <td style="text-align:center"><div class="sk" style="width:40px;height:20px;margin:0 auto;border-radius:5px"></div></td>
                                        <td style="text-align:center"><div class="sk" style="width:28px;height:28px;margin:0 auto;border-radius:6px"></div></td>
                                    </tr>
                                </template>
                            </template>

                            {{-- Data --}}
                            <template x-if="!loadingStudents && enrolledStudents.length > 0">
                                <template x-for="(stu, idx) in enrolledStudents" :key="stu.id">
                                    <tr>
                                        <td style="color:var(--text-muted);font-size:12px;font-family:var(--font-mono)" x-text="idx + 1"></td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px">
                                                <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#065F46,#059669);display:grid;place-items:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0"
                                                    x-text="initials(stu.full_name)"></div>
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary)" x-text="stu.full_name"></div>
                                                    <div style="font-size:11px;color:var(--text-muted)" x-text="stu.class_group || ''"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size:12px;font-family:var(--font-mono);color:var(--text-muted)" x-text="stu.nis || '—'"></span>
                                        </td>
                                        <td style="text-align:center">
                                            <span class="gender-badge" :class="stu.gender" x-text="stu.gender === 'male' ? 'Laki-laki' : stu.gender === 'female' ? 'Perempuan' : '—'"></span>
                                        </td>
                                        <td style="text-align:center">
                                            <button @click="removeStudent(stu.id)" class="rm-subj-btn" title="Keluarkan dari kelas">
                                                <i class="ri-user-unfollow-line" style="font-size:13px"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="!loadingStudents && enrolledStudents.length === 0" class="subj-empty">
                    <i class="ri-group-line"></i>
                    <div style="font-weight:600;margin-bottom:4px">Belum ada siswa terdaftar</div>
                    <div style="font-size:12px">Assign siswa dari tabel di bawah</div>
                </div>
            </div>
        </div>

        {{-- ── Assign Siswa ke Kelas ── --}}
        <div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:14px;flex-wrap:wrap">
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px;margin:0">
                        <i class="ri-user-add-line" style="color:#059669;margin-right:6px"></i>
                        Assign Siswa ke Kelas
                    </h3>
                    <p style="font-size:12.5px;color:var(--text-muted);margin:3px 0 0">
                        Pilih siswa yang belum punya kelas untuk di-assign ke kelas ini
                    </p>
                </div>
            </div>

            <div class="card-v2" style="animation:none">
                {{-- Toolbar: search + assign button --}}
                <div class="assign-toolbar">
                    <div class="search-wrap" style="flex:1;max-width:320px">
                        <i class="ri-search-line"></i>
                        <input
                            x-model="studentSearch"
                            @input.debounce.400ms="fetchStudents()"
                            type="text"
                            class="search-input-sm"
                            placeholder="Cari nama atau NIS siswa...">
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <template x-if="selectedStudents.length > 0">
                            <span class="assign-selected-pill">
                                <i class="ri-checkbox-circle-fill"></i>
                                <span x-text="selectedStudents.length + ' dipilih'"></span>
                            </span>
                        </template>
                        <button
                            @click="assignSelectedStudents()"
                            :disabled="selectedStudents.length === 0 || assigningStudents"
                            class="assign-btn">
                            <svg x-show="assigningStudents" style="width:13px;height:13px;animation:spin 1s linear infinite;flex-shrink:0" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:.25"></circle>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" style="opacity:.75"></path>
                            </svg>
                            <i x-show="!assigningStudents" class="ri-user-add-line"></i>
                            <span x-text="assigningStudents ? 'Menyimpan...' : 'Assign ke Kelas'"></span>
                        </button>
                    </div>
                </div>

                <div style="overflow-x:auto">
                    <table class="stu-table">
                        <thead>
                            <tr>
                                <th style="width:44px">
                                    <input type="checkbox" class="stu-checkbox"
                                        :checked="availableStudents.length > 0 && selectedStudents.length === availableStudents.length"
                                        :indeterminate="selectedStudents.length > 0 && selectedStudents.length < availableStudents.length"
                                        @change="toggleSelectAll($event)">
                                </th>
                                <th>Siswa</th>
                                <th style="width:110px">NIS</th>
                                <th style="width:80px;text-align:center">Gender</th>
                                <th style="width:130px">Kelas Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Skeleton --}}
                            <template x-if="loadingStudents">
                                <template x-for="i in 5" :key="i">
                                    <tr>
                                        <td><div class="sk" style="width:16px;height:16px;border-radius:3px"></div></td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px">
                                                <div class="sk" style="width:32px;height:32px;border-radius:8px;flex-shrink:0"></div>
                                                <div class="sk" style="width:140px;height:13px"></div>
                                            </div>
                                        </td>
                                        <td><div class="sk" style="width:70px;height:13px"></div></td>
                                        <td style="text-align:center"><div class="sk" style="width:50px;height:20px;margin:0 auto;border-radius:5px"></div></td>
                                        <td><div class="sk" style="width:80px;height:13px"></div></td>
                                    </tr>
                                </template>
                            </template>

                            {{-- Data --}}
                            <template x-if="!loadingStudents && availableStudents.length > 0">
                                <template x-for="stu in availableStudents" :key="stu.id">
                                    <tr :class="selectedStudents.includes(stu.id) ? 'is-selected' : ''"
                                        @click="toggleStudentSelect(stu.id)" style="cursor:pointer">
                                        <td @click.stop>
                                            <input type="checkbox" class="stu-checkbox"
                                                :checked="selectedStudents.includes(stu.id)"
                                                @change="toggleStudentSelect(stu.id)">
                                        </td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:10px">
                                                <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#1E3A5F,#2563EB);display:grid;place-items:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0"
                                                    x-text="initials(stu.full_name)"></div>
                                                <div style="font-size:13px;font-weight:600;color:var(--text-primary)" x-text="stu.full_name"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size:12px;font-family:var(--font-mono);color:var(--text-muted)" x-text="stu.nis || '—'"></span>
                                        </td>
                                        <td style="text-align:center">
                                            <span class="gender-badge" :class="stu.gender" x-text="stu.gender === 'L' ? 'L' : stu.gender === 'P' ? 'P' : '—'"></span>
                                        </td>
                                        <td>
                                            <span x-show="!stu.grade_id" style="font-size:11.5px;font-style:italic;color:var(--text-muted)">Belum ada kelas</span>
                                            <span x-show="stu.grade_id" style="font-size:11.5px;font-weight:600;color:#D97706" x-text="stu.grade_name || 'Kelas lain'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="!loadingStudents && availableStudents.length === 0" class="subj-empty">
                    <i class="ri-user-search-line"></i>
                    <div style="font-weight:600;margin-bottom:4px">Tidak ada siswa ditemukan</div>
                    <div style="font-size:12px" x-text="studentSearch ? 'Coba kata kunci lain' : 'Semua siswa aktif sudah memiliki kelas'"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection