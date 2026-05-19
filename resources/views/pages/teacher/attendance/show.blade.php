@extends('layouts.app')

@section('title', 'Isi Absensi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-attendance.css') }}">
@endpush

@section('content')
@php
    $initialAttendanceData = app(\App\Services\Academic\AttendanceService::class)
        ->toDetailResource($session);
@endphp
<div
    x-data="teacherAttendanceShow({
        sessionId: {{ $session->attendance_session_id }},
        sessionUrl: '{{ route('teacher.attendance.show', $session->attendance_session_id) }}',
        saveUrl: '{{ route('teacher.attendance.save-details', $session->attendance_session_id) }}',
        submitUrl: '{{ route('teacher.attendance.submit', $session->attendance_session_id) }}',
        lockUrl: '{{ route('teacher.attendance.lock', $session->attendance_session_id) }}',
        unlockUrl: '{{ route('teacher.attendance.unlock', $session->attendance_session_id) }}',
        indexUrl: '{{ route('teacher.attendance.index') }}',
        initialData: {{ Js::from($initialAttendanceData) }}
    })"
    x-init="init()"
>

    {{-- ── BREADCRUMB ────────────────────────────────────── --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><a href="{{ route('teacher.attendance.index') }}" style="color:var(--primary);text-decoration:none">Presensi</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span x-text="session.subject_name + ' — ' + session.attendance_date_label"></span></li>
        </ul>
    </div>

    {{-- ── SESSION INFO BANNER ───────────────────────────── --}}
    <div class="as-info-card">
        <div style="z-index:1">
            <div style="font-size:11px;color:rgba(255,255,255,.7);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">
                Sesi Absensi
            </div>
            <div style="font-size:22px;font-weight:700;color:#fff;letter-spacing:-.4px;line-height:1.2"
                 x-text="session.subject_name"></div>
            <div class="as-info-meta">
                <span class="as-info-pill">
                    <i class="ri-user-3-line"></i>
                    <span x-text="session.grade_name"></span>
                </span>
                <span class="as-info-pill">
                    <i class="ri-calendar-line"></i>
                    <span x-text="session.attendance_date_label"></span>
                </span>
                <span class="as-info-pill">
                    <i class="ri-repeat-line"></i>
                    Pertemuan ke-<span x-text="session.meeting_number ?? '?'"></span>
                </span>
                <span class="as-info-pill">
                    <i class="ri-book-open-line"></i>
                    <span x-text="session.semester_name"></span>
                </span>
            </div>
        </div>
        {{-- Total recap box --}}
        <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:12px;padding:14px 20px;text-align:center;z-index:1;flex-shrink:0">
            <div style="font-size:32px;font-weight:800;color:#fff;line-height:1" x-text="details.length"></div>
            <div style="font-size:12px;color:rgba(255,255,255,.75);margin-top:2px">Total Siswa</div>
        </div>
    </div>

    {{-- ── LOCKED BANNER ─────────────────────────────────── --}}
    <div x-show="session.is_locked" class="as-locked-banner">
        <i class="ri-lock-line" style="font-size:18px;flex-shrink:0"></i>
        <span>Sesi ini <strong>terkunci</strong>. Data tidak dapat diubah. Hubungi admin atau klik "Buka Kunci" untuk mengedit kembali.</span>
    </div>

    {{-- ── ACTION BAR ────────────────────────────────────── --}}
    <div class="as-action-bar">
        {{-- Status badge --}}
        <span class="as-status-badge" :style="statusBannerStyle(session.status)">
            <i :class="statusIcon(session.status)"></i>
            <span x-text="statusLabel(session.status)"></span>
        </span>

        <div style="flex:1"></div>

        {{-- Auto-save indicator --}}
        <div class="as-save-indicator" x-show="!session.is_locked">
            <span class="as-save-dot" :class="saveState"></span>
            <span x-text="saveStateLabel"></span>
        </div>

        {{-- Save button --}}
        <button
            @click="saveDraft()"
            :disabled="session.is_locked || saving"
            class="as-btn as-btn-save"
            x-show="!session.is_locked"
        >
            <svg x-show="saving" style="width:13px;height:13px" class="animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <i x-show="!saving" class="ri-save-line"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Draft'"></span>
        </button>

        {{-- Submit button --}}
        <button
            @click="confirmSubmit()"
            :disabled="session.is_locked || session.status === 'approved'"
            class="as-btn as-btn-submit"
            x-show="session.status !== 'submitted' && session.status !== 'approved'"
        >
            <i class="ri-send-plane-line"></i> Submit
        </button>

        {{-- Lock / Unlock --}}
        <template x-if="!session.is_locked && session.status === 'submitted'">
            <button @click="confirmLock()" class="as-btn as-btn-lock">
                <i class="ri-lock-line"></i> Kunci
            </button>
        </template>
        <template x-if="session.is_locked && session.status !== 'approved'">
            <button @click="confirmUnlock()" class="as-btn as-btn-unlock">
                <i class="ri-lock-unlock-line"></i> Buka Kunci
            </button>
        </template>

        {{-- Back --}}
        <a :href="indexUrl" class="as-btn">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    {{-- ── RECAP BAR ─────────────────────────────────────── --}}
    <div class="as-recap">
        <span style="font-size:12px;font-weight:700;color:var(--text-muted);margin-right:4px">Rekap:</span>
        <div class="as-recap-item">
            <div class="as-recap-dot" style="background:#F0FDF4;color:#15803D">H</div>
            <span x-text="countByStatus('H')"></span> Hadir
        </div>
        <div class="as-recap-item">
            <div class="as-recap-dot" style="background:#FEF2F2;color:#B91C1C">A</div>
            <span x-text="countByStatus('A')"></span> Alpha
        </div>
        <div class="as-recap-item">
            <div class="as-recap-dot" style="background:#FEF9C3;color:#92400E">I</div>
            <span x-text="countByStatus('I')"></span> Izin
        </div>
        <div class="as-recap-item">
            <div class="as-recap-dot" style="background:#EFF6FF;color:#1D4ED8">S</div>
            <span x-text="countByStatus('S')"></span> Sakit
        </div>
        <div class="as-recap-item">
            <div class="as-recap-dot" style="background:#F5F3FF;color:#6D28D9">L</div>
            <span x-text="countByStatus('L')"></span> Terlambat
        </div>
        <div style="margin-left:auto;font-size:12px;color:var(--text-muted)">
            <span x-text="details.length"></span> siswa total
        </div>
    </div>

    {{-- ── SEARCH + BULK ACTIONS ─────────────────────────── --}}
    <div class="as-search">
        <i class="ri-search-line as-search-icon"></i>
        <input
            x-model="searchStudent"
            type="text"
            placeholder="Cari nama atau NIS siswa..."
            class="as-search-input"
        />
    </div>

    <div class="as-bulk" x-show="!session.is_locked">
        <span class="as-bulk-label">Tandai semua:</span>
        <button @click="bulkSetStatus('H')" class="as-bulk-btn as-bulk-h"><i class="ri-check-line"></i> Hadir</button>
        <button @click="bulkSetStatus('A')" class="as-bulk-btn as-bulk-a"><i class="ri-close-line"></i> Alpha</button>
        <button @click="bulkSetStatus('I')" class="as-bulk-btn as-bulk-i">I Izin</button>
        <button @click="bulkSetStatus('S')" class="as-bulk-btn as-bulk-s">S Sakit</button>
        <button @click="bulkSetStatus('L')" class="as-bulk-btn as-bulk-l">L Terlambat</button>
        <span style="margin-left:auto;font-size:12px;color:var(--text-muted)"
              x-show="filteredDetails.length !== details.length"
              x-text="'Menampilkan ' + filteredDetails.length + ' dari ' + details.length + ' siswa'">
        </span>
    </div>

    {{-- ── STUDENT LIST ──────────────────────────────────── --}}
    <div class="as-student-list">
        {{-- Header --}}
        <div class="as-student-head">
            <div style="text-align:center">#</div>
            <div>Siswa</div>
            <div style="text-align:center">H</div>
            <div style="text-align:center">A</div>
            <div style="text-align:center">I</div>
            <div style="text-align:center">S</div>
            <div style="text-align:center">L</div>
            <div>Keterangan</div>
        </div>

        {{-- Skeleton --}}
        <template x-if="loadingDetail">
            <template x-for="i in 8" :key="i">
                <div class="as-student-row">
                    <div class="dt-skel" style="width:24px;height:12px;margin:0 auto"></div>
                    <div>
                        <div class="dt-skel" style="width:70%;height:13px;margin-bottom:5px"></div>
                        <div class="dt-skel" style="width:40%;height:10px"></div>
                    </div>
                    <template x-for="j in 5" :key="j">
                        <div class="dt-skel" style="width:42px;height:30px;border-radius:8px;margin:0 auto"></div>
                    </template>
                    <div class="dt-skel" style="height:30px;border-radius:6px"></div>
                </div>
            </template>
        </template>

        {{-- Rows --}}
        <template x-if="!loadingDetail">
            <template x-for="(d, i) in filteredDetails" :key="d.student_id">
                <div class="as-student-row">

                    {{-- No --}}
                    <div class="as-student-num" x-text="i + 1"></div>

                    {{-- Name --}}
                    <div>
                        <div class="as-student-name" x-text="d.student_name"></div>
                        <div class="as-student-nis" x-text="d.nis"></div>
                    </div>

                    {{-- Status buttons H A I S L --}}
                    <template x-for="st in ['H','A','I','S','L']" :key="st">
                        <div style="display:grid;place-items:center">
                            <button
                                class="as-status-btn"
                                :class="d.status === st ? 'active-' + st : ''"
                                :disabled="session.is_locked"
                                @click="setStatus(d, st)"
                                :title="statusFullLabel(st)"
                            >
                                <span x-text="st"></span>
                            </button>
                        </div>
                    </template>

                    {{-- Note --}}
                    <input
                        type="text"
                        x-model="d.note"
                        :disabled="session.is_locked"
                        placeholder="Catatan (opsional)"
                        class="as-note-input"
                        @input="markDirty()"
                        maxlength="200"
                    />
                </div>
            </template>
        </template>

        {{-- Empty search --}}
        <div
            x-show="!loadingDetail && filteredDetails.length === 0 && details.length > 0"
            style="padding:32px;text-align:center;font-size:13px;color:var(--text-muted)"
        >
            <i class="ri-search-line" style="font-size:24px;display:block;margin-bottom:8px"></i>
            Tidak ada siswa yang cocok dengan "<span x-text="searchStudent"></span>"
        </div>
    </div>

    {{-- ── NOTES SECTION ─────────────────────────────────── --}}
    <div style="margin-top:16px;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 18px">
        <label style="display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
            <i class="ri-sticky-note-line" style="margin-right:4px"></i> Catatan Sesi
        </label>
        <textarea
            x-model="sessionNotes"
            :disabled="session.is_locked"
            @input="markDirty()"
            placeholder="Catatan untuk sesi ini (opsional)..."
            class="as-notes-area"
            maxlength="1000"
        ></textarea>
        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;text-align:right"
             x-text="(sessionNotes?.length ?? 0) + ' / 1000'"></div>
    </div>

    {{-- ── BOTTOM SAVE BAR (sticky) ─────────────────────── --}}
    <div
        x-show="isDirty && !session.is_locked"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:200;
               background:var(--card);border:1px solid var(--border);border-radius:14px;
               padding:12px 20px;display:flex;align-items:center;gap:12px;
               box-shadow:0 8px 32px rgba(0,0,0,.15)"
    >
        <span style="font-size:13px;color:var(--text-secondary)">
            <i class="ri-edit-circle-line" style="color:#F59E0B"></i>
            Ada perubahan yang belum disimpan
        </span>
        <button @click="saveDraft()" :disabled="saving" class="as-btn as-btn-save" style="margin:0">
            <i class="ri-save-line"></i>
            <span x-text="saving ? 'Menyimpan...' : 'Simpan Sekarang'"></span>
        </button>
        <button @click="revertChanges()" class="as-btn as-btn-danger" style="margin:0;font-size:12px;padding:6px 12px">
            Batalkan
        </button>
    </div>

</div>{{-- /x-data --}}
@endsection

@push('scripts')
<script>
function teacherAttendanceShow(config = {}) {
    return {
        // ─── Config ────────────────────────────────────────────
        sessionId:   config.sessionId,
        sessionUrl:  config.sessionUrl,
        saveUrl:     config.saveUrl,
        submitUrl:   config.submitUrl,
        lockUrl:     config.lockUrl,
        unlockUrl:   config.unlockUrl,
        indexUrl:    config.indexUrl  ?? '/teacher/attendance',

        // ─── State ─────────────────────────────────────────────
        session:       config.initialData ?? {},
        details:       [],
        _originalDetails: [], // snapshot for revert
        sessionNotes:  config.initialData?.notes ?? '',
        searchStudent: '',
        loadingDetail: false,
        saving:        false,
        isDirty:       false,
        saveState:     'saved', // 'saved' | 'saving' | 'error'

        // ─── Computed ───────────────────────────────────────────
        get saveStateLabel() {
            return {
                saved:  'Tersimpan',
                saving: 'Menyimpan...',
                error:  'Gagal menyimpan',
            }[this.saveState] ?? '';
        },

        get filteredDetails() {
            const q = this.searchStudent.toLowerCase().trim();
            if (!q) return this.details;
            return this.details.filter(d =>
                (d.student_name ?? '').toLowerCase().includes(q) ||
                (d.nis          ?? '').toLowerCase().includes(q)
            );
        },

        // ─── Init ───────────────────────────────────────────────
        init() {
            console.log(config.initialData);

            this.details = (config.initialData?.details ?? []).map(d => ({ ...d }));

            console.log(this.details);

            this._originalDetails = JSON.parse(JSON.stringify(this.details));
            this.sessionNotes = config.initialData?.notes ?? '';
        },

        // ─── Status helpers ─────────────────────────────────────
        setStatus(detail, status) {
            detail.status = status;
            this.markDirty();
        },

        bulkSetStatus(status) {
            this.filteredDetails.forEach(d => d.status = status);
            this.markDirty();
        },

        countByStatus(status) {
            return this.details.filter(d => d.status === status).length;
        },

        markDirty() {
            this.isDirty   = true;
            this.saveState = 'saved'; // reset to avoid stale state
        },

        revertChanges() {
            this.details      = JSON.parse(JSON.stringify(this._originalDetails));
            this.sessionNotes = config.initialData?.notes ?? '';
            this.isDirty      = false;
        },

        // ─── API calls ──────────────────────────────────────────
        async saveDraft() {
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
                            status:     d.status,
                            note:       d.note ?? null,
                        })),
                        notes: this.sessionNotes,

                        force_draft: this.session.status === 'submitted' && !this.session.is_locked,
                    }),
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal menyimpan.');

                // Update local state from response
                this.session         = json.data;
                this.details         = (json.data.details ?? []).map(d => ({ ...d }));
                this._originalDetails = JSON.parse(JSON.stringify(this.details));
                this.isDirty         = false;
                this.saveState       = 'saved';
            } catch (err) {
                this.saveState = 'error';
                this.toast('error', err.message);
            } finally {
                this.saving = false;
            }
        },

        async confirmSubmit() {
            const result = await Swal.fire({
                title:              'Submit Absensi?',
                html:               `Absensi akan disubmit dan tidak bisa diubah kecuali dibuka kembali.<br><br>
                                     <strong>Hadir: ${this.countByStatus('H')} | Alpha: ${this.countByStatus('A')} | Izin: ${this.countByStatus('I')} | Sakit: ${this.countByStatus('S')} | Terlambat: ${this.countByStatus('L')}</strong>`,
                icon:               'question',
                showCancelButton:   true,
                confirmButtonColor: '#1D4ED8',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Submit!',
                cancelButtonText:   'Batal',
                customClass: { popup: 'swal-popup-rounded' },
            });
            if (!result.isConfirmed) return;

            // Save first if dirty
            if (this.isDirty) await this.saveDraft();

            try {
                const res  = await fetch(this.submitUrl, {
                    method:  'PATCH',
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.csrf(),
                    },
                    body: JSON.stringify({ notes: this.sessionNotes }),
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal submit.');

                this.session = { ...this.session, ...json.data };
                this.toast('success', 'Absensi berhasil disubmit.');
            } catch (err) {
                this.toast('error', err.message);
            }
        },

        async confirmLock() {
            const result = await Swal.fire({
                title:              'Kunci Sesi?',
                text:               'Setelah dikunci, absensi tidak dapat diubah lagi.',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonColor: '#D97706',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Kunci!',
                cancelButtonText:   'Batal',
                customClass: { popup: 'swal-popup-rounded' },
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(this.lockUrl, {
                    method:  'PATCH',
                    headers: { Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':this.csrf() },
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal mengunci.');
                this.session = { ...this.session, ...json.data };
                this.toast('success', 'Sesi berhasil dikunci.');
            } catch (err) { this.toast('error', err.message); }
        },

        async confirmUnlock() {
            const result = await Swal.fire({
                title:              'Buka Kunci Sesi?',
                text:               'Sesi akan dibuka dan dapat diedit kembali.',
                icon:               'question',
                showCancelButton:   true,
                confirmButtonColor: '#059669',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Buka!',
                cancelButtonText:   'Batal',
                customClass: { popup: 'swal-popup-rounded' },
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(this.unlockUrl, {
                    method:  'PATCH',
                    headers: { Accept:'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':this.csrf() },
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal membuka kunci.');
                this.session = { ...this.session, ...json.data };
                this.toast('success', 'Sesi berhasil dibuka kembali.');
            } catch (err) { this.toast('error', err.message); }
        },

        // ─── Badge helpers ───────────────────────────────────────
        statusLabel(status) {
            return { draft:'Draft', submitted:'Submitted', approved:'Approved' }[status] ?? status;
        },
        statusIcon(status) {
            return { draft:'ri-edit-line', submitted:'ri-send-plane-line', approved:'ri-checkbox-circle-line' }[status] ?? 'ri-circle-line';
        },
        statusBannerStyle(status) {
            return {
                draft:     'background:#F1F5F9;color:#475569',
                submitted: 'background:#EFF6FF;color:#1D4ED8',
                approved:  'background:#F0FDF4;color:#15803D',
            }[status] ?? 'background:#F1F5F9;color:#475569';
        },
        statusFullLabel(st) {
            return { H:'Hadir', A:'Alpha', I:'Izin', S:'Sakit', L:'Terlambat' }[st] ?? st;
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