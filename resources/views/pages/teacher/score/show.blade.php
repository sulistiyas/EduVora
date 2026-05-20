@extends('layouts.app')

@section('title', 'Isi Nilai — ' . $session->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-score.css') }}">
@endpush

@section('content')
<div
    x-data="teacherScoreShow({
        sessionId:    {{ $session->score_session_id }},
        saveUrl:      '{{ route('teacher.scores.save-details', $session->score_session_id) }}',
        publishUrl:   '{{ route('teacher.scores.publish', $session->score_session_id) }}',
        deleteUrl:    '{{ route('teacher.scores.destroy', $session->score_session_id) }}',
        indexUrl:     '{{ route('teacher.scores.index') }}',
        initialData:  {{ Js::from($initialData) }}
    })"
    x-init="init()"
>

    {{-- ── BREADCRUMB ────────────────────────────────────── --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><a href="{{ route('teacher.scores.index') }}" style="color:var(--primary);text-decoration:none">Input Nilai</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span x-text="session.title"></span></li>
        </ul>
    </div>

    {{-- ── SESSION INFO BANNER ───────────────────────────── --}}
    <div class="ss-info-card">
        <div style="z-index:1">
            <div style="font-size:11px;color:rgba(255,255,255,.7);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">
                Sesi Penilaian
            </div>
            <div style="font-size:22px;font-weight:700;color:#fff;letter-spacing:-.4px;line-height:1.2"
                 x-text="session.title"></div>
            <div class="ss-info-meta">
                <span class="ss-info-pill">
                    <i class="ri-user-3-line"></i>
                    <span x-text="session.grade_name"></span>
                </span>
                <span class="ss-info-pill">
                    <i class="ri-book-2-line"></i>
                    <span x-text="session.subject_name"></span>
                </span>
                <span class="ss-info-pill">
                    <i class="ri-calendar-line"></i>
                    <span x-text="session.score_date_label"></span>
                </span>
                <span class="ss-info-pill">
                    <i class="ri-file-text-line"></i>
                    <span x-text="session.score_type_label"></span>
                </span>
                <span class="ss-info-pill">
                    <i class="ri-book-open-line"></i>
                    <span x-text="session.semester_name"></span>
                </span>
            </div>
        </div>
        {{-- Stats box --}}
        <div style="display:flex;gap:12px;z-index:1;flex-wrap:wrap">
            <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:12px;padding:14px 20px;text-align:center;flex-shrink:0">
                <div style="font-size:28px;font-weight:800;color:#fff;line-height:1" x-text="details.length"></div>
                <div style="font-size:11px;color:rgba(255,255,255,.75);margin-top:2px">Total Siswa</div>
            </div>
            <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:12px;padding:14px 20px;text-align:center;flex-shrink:0">
                <div style="font-size:28px;font-weight:800;color:#fff;line-height:1" x-text="session.avg_score ?? '—'"></div>
                <div style="font-size:11px;color:rgba(255,255,255,.75);margin-top:2px">Rata-rata</div>
            </div>
            <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:12px;padding:14px 20px;text-align:center;flex-shrink:0">
                <div style="font-size:28px;font-weight:800;color:#fff;line-height:1" x-text="filledCount"></div>
                <div style="font-size:11px;color:rgba(255,255,255,.75);margin-top:2px">Sudah Diisi</div>
            </div>
        </div>
    </div>

    {{-- ── PUBLISHED BANNER ─────────────────────────────── --}}
    <div x-show="session.is_published" class="ss-published-banner">
        <i class="ri-eye-line" style="font-size:18px;flex-shrink:0"></i>
        <span>Nilai ini sudah <strong>dipublikasikan</strong> dan dapat dilihat oleh siswa.</span>
    </div>

    {{-- ── ACTION BAR ────────────────────────────────────── --}}
    <div class="ss-action-bar">
        {{-- Publish badge --}}
        <span :class="session.is_published ? 'sc-badge sc-badge-published' : 'sc-badge sc-badge-unpublished'">
            <i :class="session.is_published ? 'ri-eye-line' : 'ri-eye-off-line'"></i>
            <span x-text="session.is_published ? 'Published' : 'Draft'"></span>
        </span>

        <div style="flex:1"></div>

        {{-- Auto-save indicator --}}
        <div class="ss-save-indicator">
            <span class="ss-save-dot" :class="saveState"></span>
            <span x-text="saveStateLabel"></span>
        </div>

        {{-- Save --}}
        <button @click="saveScores()" :disabled="saving" class="sc-btn sc-btn-save">
            <svg x-show="saving" style="width:13px;height:13px;animation:spin .7s linear infinite" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" opacity=".75"></path>
            </svg>
            <i x-show="!saving" class="ri-save-line"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
        </button>

        {{-- Publish / Unpublish --}}
        <button
            @click="confirmTogglePublish()"
            :class="session.is_published ? 'sc-btn sc-btn-unpublish' : 'sc-btn sc-btn-publish'"
        >
            <i :class="session.is_published ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
            <span x-text="session.is_published ? 'Sembunyikan' : 'Publikasikan'"></span>
        </button>

        {{-- Delete --}}
        <button
            @click="confirmDelete()"
            class="sc-btn sc-btn-danger"
            x-show="!session.is_published"
        >
            <i class="ri-delete-bin-line"></i>
        </button>

        {{-- Back --}}
        <a :href="indexUrl" class="sc-btn">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    {{-- ── RECAP BAR ─────────────────────────────────────── --}}
    <div class="ss-recap">
        <span style="font-size:12px;font-weight:700;color:var(--text-muted);margin-right:4px">Statistik:</span>
        <div class="ss-recap-item">
            <div class="ss-recap-dot" style="background:#F0FDF4;color:#15803D"><i class="ri-arrow-up-line" style="font-size:11px"></i></div>
            <span>Tertinggi:</span>
            <strong x-text="session.highest_score ?? '—'"></strong>
        </div>
        <div class="ss-recap-item">
            <div class="ss-recap-dot" style="background:#FEF2F2;color:#B91C1C"><i class="ri-arrow-down-line" style="font-size:11px"></i></div>
            <span>Terendah:</span>
            <strong x-text="session.lowest_score ?? '—'"></strong>
        </div>
        <div class="ss-recap-item">
            <div class="ss-recap-dot" style="background:#F5F3FF;color:#7C3AED"><i class="ri-bar-chart-line" style="font-size:11px"></i></div>
            <span>Rata-rata:</span>
            <strong x-text="session.avg_score ?? '—'"></strong>
        </div>
        <div class="ss-recap-item">
            <div class="ss-recap-dot" style="background:#EFF6FF;color:#1D4ED8">≤</div>
            <span>Maks:</span>
            <strong x-text="session.max_score"></strong>
        </div>
        <div style="margin-left:auto;font-size:12px;color:var(--text-muted)">
            <span x-text="filledCount"></span> / <span x-text="details.length"></span> terisi
        </div>
    </div>

    {{-- ── SEARCH + BULK ACTIONS ─────────────────────────── --}}
    <div class="ss-search">
        <i class="ri-search-line ss-search-icon"></i>
        <input
            x-model="searchStudent"
            type="text"
            placeholder="Cari nama atau NIS siswa..."
            class="ss-search-input"
        />
    </div>

    <div class="ss-bulk">
        <span class="ss-bulk-label">Isi semua dengan:</span>
        <input
            x-model="bulkScore"
            type="number"
            class="ss-bulk-input"
            placeholder="0"
            min="0"
            :max="session.max_score"
            @keydown.enter="applyBulkScore()"
        />
        <button @click="applyBulkScore()" class="ss-bulk-btn apply">
            <i class="ri-check-line"></i> Terapkan
        </button>
        <button @click="clearAllScores()" class="ss-bulk-btn clear">
            <i class="ri-close-line"></i> Kosongkan
        </button>
        <span
            x-show="filteredDetails.length !== details.length"
            style="font-size:12px;color:var(--text-muted)"
            x-text="'Menampilkan ' + filteredDetails.length + ' dari ' + details.length + ' siswa'"
        ></span>
    </div>

    {{-- ── STUDENT LIST ──────────────────────────────────── --}}
    <div class="ss-student-list">
        {{-- Header --}}
        <div class="ss-student-head">
            <div style="text-align:center">#</div>
            <div>Siswa</div>
            <div>Nilai</div>
            <div>Progress</div>
            <div>Catatan</div>
        </div>

        {{-- Rows --}}
        <template x-for="(d, i) in filteredDetails" :key="d.student_id">
            <div class="ss-student-row">

                {{-- No --}}
                <div class="ss-student-num" x-text="i + 1"></div>

                {{-- Name --}}
                <div>
                    <div class="ss-student-name" x-text="d.student_name"></div>
                    <div class="ss-student-nis" x-text="d.nis"></div>
                </div>

                {{-- Score input --}}
                <div class="ss-score-wrap">
                    <input
                        type="number"
                        x-model.number="d.score"
                        :min="0"
                        :max="session.max_score"
                        class="ss-score-input"
                        :class="scoreColorClass(d.score, session.max_score)"
                        @input="markDirty(); updatePct(d)"
                        @blur="clampScore(d)"
                        placeholder="—"
                    />
                    <span class="ss-score-max">/ <span x-text="session.max_score"></span></span>
                </div>

                {{-- Progress bar --}}
                <div class="ss-score-bar-wrap">
                    <div class="ss-score-bar-track">
                        <div
                            class="ss-score-bar-fill"
                            :style="'width:' + (d.pct ?? 0) + '%;background:' + scoreBarColor(d.pct ?? 0)"
                        ></div>
                    </div>
                    <span class="ss-score-pct" x-text="d.pct !== null ? d.pct + '%' : '—'"></span>
                </div>

                {{-- Note --}}
                <input
                    type="text"
                    x-model="d.notes"
                    placeholder="Catatan (opsional)"
                    class="ss-note-input"
                    @input="markDirty()"
                    maxlength="500"
                />
            </div>
        </template>

        {{-- Empty search --}}
        <div
            x-show="filteredDetails.length === 0 && details.length > 0"
            style="padding:32px;text-align:center;font-size:13px;color:var(--text-muted)"
        >
            <i class="ri-search-line" style="font-size:24px;display:block;margin-bottom:8px"></i>
            Tidak ada siswa yang cocok dengan "<span x-text="searchStudent"></span>"
        </div>
    </div>

    {{-- ── DESCRIPTION SECTION ─────────────────────────── --}}
    <div style="margin-top:16px;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 18px">
        <label style="display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
            <i class="ri-sticky-note-line" style="margin-right:4px"></i> Deskripsi Sesi
        </label>
        <textarea
            x-model="sessionDescription"
            @input="markDirty()"
            placeholder="Deskripsi atau catatan untuk sesi penilaian ini (opsional)..."
            class="ss-notes-area"
            maxlength="1000"
        ></textarea>
        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;text-align:right"
             x-text="(sessionDescription?.length ?? 0) + ' / 1000'"></div>
    </div>

    {{-- ── BOTTOM SAVE BAR (sticky) ─────────────────────── --}}
    <div
        x-show="isDirty"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:200;
               background:var(--card);border:1px solid var(--border);border-radius:14px;
               padding:12px 20px;display:flex;align-items:center;gap:12px;
               box-shadow:0 8px 32px rgba(0,0,0,.15);white-space:nowrap"
    >
        <span style="font-size:13px;color:var(--text-secondary)">
            <i class="ri-edit-circle-line" style="color:#F59E0B"></i>
            Ada perubahan yang belum disimpan
        </span>
        <button @click="saveScores()" :disabled="saving" class="sc-btn sc-btn-save" style="margin:0">
            <i class="ri-save-line"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Sekarang'"></span>
        </button>
        <button @click="revertChanges()" class="sc-btn sc-btn-danger" style="margin:0;font-size:12px;padding:6px 12px">
            Batalkan
        </button>
    </div>

</div>{{-- /x-data --}}
@endsection

@push('scripts')
<script>
function teacherScoreShow(config = {}) {
    return {
        // ─── Config ────────────────────────────────────────────
        sessionId:   config.sessionId,
        saveUrl:     config.saveUrl,
        publishUrl:  config.publishUrl,
        deleteUrl:   config.deleteUrl,
        indexUrl:    config.indexUrl ?? '/teacher/scores',

        // ─── State ─────────────────────────────────────────────
        session:            config.initialData ?? {},
        details:            [],
        _originalDetails:   [],
        sessionDescription: config.initialData?.description ?? '',
        searchStudent:      '',
        saving:             false,
        isDirty:            false,
        saveState:          'saved',
        bulkScore:          '',

        // ─── Computed ───────────────────────────────────────────
        get saveStateLabel() {
            return { saved:'Tersimpan', saving:'Menyimpan...', error:'Gagal menyimpan' }[this.saveState] ?? '';
        },

        get filteredDetails() {
            const q = this.searchStudent.toLowerCase().trim();
            if (!q) return this.details;
            return this.details.filter(d =>
                (d.student_name ?? '').toLowerCase().includes(q) ||
                (d.nis ?? '').toLowerCase().includes(q)
            );
        },

        get filledCount() {
            return this.details.filter(d => d.score !== null && d.score !== '' && d.score !== undefined).length;
        },

        // ─── Init ───────────────────────────────────────────────
        init() {
            this.details = (config.initialData?.details ?? []).map(d => ({
                ...d,
                pct: this.calcPct(d.score, config.initialData?.max_score ?? 100),
            }));
            this._originalDetails = JSON.parse(JSON.stringify(this.details));
            this.sessionDescription = config.initialData?.description ?? '';
        },

        // ─── Score helpers ───────────────────────────────────────
        calcPct(score, max) {
            if (score === null || score === '' || score === undefined) return null;
            const m = parseFloat(max) || 100;
            return Math.round((parseFloat(score) / m) * 1000) / 10;
        },

        updatePct(detail) {
            detail.pct = this.calcPct(detail.score, this.session.max_score);
        },

        clampScore(detail) {
            if (detail.score === null || detail.score === '' || detail.score === undefined) return;
            const max = parseFloat(this.session.max_score) || 100;
            const val = parseFloat(detail.score);
            if (isNaN(val)) { detail.score = null; return; }
            detail.score = Math.min(Math.max(val, 0), max);
            detail.pct   = this.calcPct(detail.score, max);
        },

        scoreColorClass(score, max) {
            if (score === null || score === '' || score === undefined) return '';
            const pct = (parseFloat(score) / parseFloat(max)) * 100;
            if (pct >= 80) return 'score-high';
            if (pct >= 60) return 'score-mid';
            return 'score-low';
        },

        scoreBarColor(pct) {
            if (pct >= 80) return '#22C55E';
            if (pct >= 60) return '#F59E0B';
            return '#EF4444';
        },

        // ─── Bulk ───────────────────────────────────────────────
        applyBulkScore() {
            if (this.bulkScore === '' || this.bulkScore === null) return;
            const val = Math.min(Math.max(parseFloat(this.bulkScore) || 0, 0), parseFloat(this.session.max_score) || 100);
            this.filteredDetails.forEach(d => {
                d.score = val;
                d.pct   = this.calcPct(val, this.session.max_score);
            });
            this.markDirty();
        },

        clearAllScores() {
            this.filteredDetails.forEach(d => { d.score = null; d.pct = null; });
            this.markDirty();
        },

        // ─── Dirty tracking ─────────────────────────────────────
        markDirty() {
            this.isDirty   = true;
            this.saveState = 'saved';
        },

        revertChanges() {
            this.details            = JSON.parse(JSON.stringify(this._originalDetails));
            this.sessionDescription = config.initialData?.description ?? '';
            this.isDirty            = false;
        },

        // ─── API calls ──────────────────────────────────────────
        async saveScores() {
            if (this.saving) return;
            this.saving    = true;
            this.saveState = 'saving';
            try {
                const res  = await fetch(this.saveUrl, {
                    method:  'PATCH',
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.csrf(),
                    },
                    body: JSON.stringify({
                        details: this.details.map(d => ({
                            student_id: d.student_id,
                            score:      d.score !== '' ? d.score : null,
                            max_score:  this.session.max_score,
                            notes:      d.notes ?? null,
                        })),
                        description: this.sessionDescription,
                    }),
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal menyimpan.');

                // Update aggregates from response
                const updated = json.data;
                this.session = { ...this.session,
                    avg_score:     updated.avg_score,
                    highest_score: updated.highest_score,
                    lowest_score:  updated.lowest_score,
                    filled_count:  updated.filled_count,
                    description:   updated.description,
                };
                this._originalDetails = JSON.parse(JSON.stringify(this.details));
                this.isDirty   = false;
                this.saveState = 'saved';
                this.toast('success', 'Nilai berhasil disimpan.');
            } catch (err) {
                this.saveState = 'error';
                this.toast('error', err.message);
            } finally {
                this.saving = false;
            }
        },

        async confirmTogglePublish() {
            const isPublished = this.session.is_published;
            const result = await Swal.fire({
                title:              isPublished ? 'Sembunyikan Nilai?' : 'Publikasikan Nilai?',
                html:               isPublished
                    ? 'Nilai akan disembunyikan dari siswa.'
                    : 'Nilai akan terlihat oleh semua siswa di kelas ini.<br><strong>Pastikan semua nilai sudah benar sebelum dipublikasikan.</strong>',
                icon:               'question',
                showCancelButton:   true,
                confirmButtonColor: isPublished ? '#475569' : '#1D4ED8',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  isPublished ? 'Ya, Sembunyikan' : 'Ya, Publikasikan!',
                cancelButtonText:   'Batal',
            });
            if (!result.isConfirmed) return;

            // Save first if dirty
            if (this.isDirty) await this.saveScores();

            try {
                const res  = await fetch(this.publishUrl, {
                    method:  'PATCH',
                    headers: { Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':this.csrf() },
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal mengubah status.');
                this.session = { ...this.session, is_published: json.data.is_published };
                this.toast('success', json.message);
            } catch (err) { this.toast('error', err.message); }
        },

        async confirmDelete() {
            const result = await Swal.fire({
                title:              'Hapus Sesi Nilai?',
                text:               'Semua data nilai dalam sesi ini akan dihapus permanen.',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Hapus!',
                cancelButtonText:   'Batal',
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(this.deleteUrl, {
                    method:  'DELETE',
                    headers: { Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':this.csrf() },
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal menghapus.');
                window.location.href = this.indexUrl;
            } catch (err) { this.toast('error', err.message); }
        },

        // ─── Utilities ──────────────────────────────────────────
        csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },
        toast(icon, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true, position: 'top-end',
                    icon, title: message,
                    showConfirmButton: false,
                    timer: 3500, timerProgressBar: true,
                    didOpen: t => {
                        t.addEventListener('mouseenter', Swal.stopTimer);
                        t.addEventListener('mouseleave', Swal.resumeTimer);
                    },
                });
            }
        },
    };
}
</script>
@endpush