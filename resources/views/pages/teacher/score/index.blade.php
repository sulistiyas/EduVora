@extends('layouts.app')

@section('title', 'Input Nilai')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-score.css') }}">
@endpush

@section('content')
<div
    x-data="teacherScoreIndex({
        indexUrl:        '{{ route('teacher.scores.index') }}',
        storeUrl:        '{{ route('teacher.scores.store') }}',
        semestersUrl:    '{{ route('teacher.scores.semesters') }}',
        gradeSubjectsUrl:'{{ route('teacher.scores.grade-subjects') }}',
        activeSemesterId:'{{ $activeSemester?->semester_id ?? '' }}',
        scheduleInfo:    {{ Js::from($scheduleInfo) }},
    })"
    x-init="init()"
>

    {{-- ── BREADCRUMB ────────────────────────────────────── --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Input Nilai</span></li>
        </ul>
    </div>

    {{-- ── PAGE HEADER ──────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2">
                Input Nilai
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola & catat nilai siswa per sesi penilaian
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('teacher.schedules.index') }}" class="sc-action">
                <i class="ri-calendar-schedule-line"></i>
                Lihat Jadwal
            </a>
            <button @click="openCreateModal()" class="sc-action primary">
                <i class="ri-add-line"></i>
                Buat Sesi Nilai
            </button>
        </div>
    </div>

    {{-- ── STAT STRIP ───────────────────────────────────── --}}
    <div class="sc-stats">
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-file-list-3-line"></i></div>
            <div class="sc-stat-val">{{ $stats['total'] }}</div>
            <div class="sc-stat-label">Total Sesi</div>
            <div class="sc-stat-bar" style="background:#8B5CF6"></div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#F0FDF4;color:#15803D"><i class="ri-send-plane-line"></i></div>
            <div class="sc-stat-val">{{ $stats['published'] }}</div>
            <div class="sc-stat-label">Dipublikasikan</div>
            <div class="sc-stat-bar" style="background:#22C55E"></div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#FEF9C3;color:#CA8A04"><i class="ri-draft-line"></i></div>
            <div class="sc-stat-val">{{ $stats['draft'] }}</div>
            <div class="sc-stat-label">Draft</div>
            <div class="sc-stat-bar" style="background:#EAB308"></div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#EFF6FF;color:#2563EB"><i class="ri-clipboard-line"></i></div>
            <div class="sc-stat-val">{{ $stats['daily'] + $stats['assignment']}}</div>
            <div class="sc-stat-label">Harian + Tugas</div>
            <div class="sc-stat-bar" style="background:#3B82F6"></div>
        </div>
    </div>

    {{-- ── DATATABLE ─────────────────────────────────────── --}}
    <div class="dt-wrap">

        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">

                {{-- Search --}}
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input
                        x-model="search"
                        @input.debounce.400ms="fetchSessions()"
                        type="text"
                        placeholder="Cari judul, mapel..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchSessions()" class="dt-search-clear">×</button>
                </div>

                {{-- Semester --}}
                <select x-model="semesterFilter" @change="fetchSessions()" class="dt-select" style="min-width:160px">
                    <option value="">Semua Semester</option>
                    <template x-for="sem in semesters" :key="sem.semester_id">
                        <option :value="sem.semester_id" x-text="sem.semester_name"></option>
                    </template>
                </select>

                {{-- Tipe Nilai --}}
                <select x-model="typeFilter" @change="fetchSessions()" class="dt-select">
                    <option value="">Semua Tipe</option>
                    <option value="daily">Ulangan Harian</option>
                    <option value="mid_exam">UTS</option>
                    <option value="final_exam">UAS</option>
                    <option value="assignment">Tugas</option>
                </select>

                {{-- Status --}}
                <select x-model="publishedFilter" @change="fetchSessions()" class="dt-select">
                    <option value="">Semua Status</option>
                    <option value="true">Dipublikasikan</option>
                    <option value="false">Draft</option>
                </select>

                <select x-model="perPage" @change="fetchSessions()" class="dt-select">
                    <option value="10">10 / hal</option>
                    <option value="15">15 / hal</option>
                    <option value="25">25 / hal</option>
                </select>
            </div>

            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" @click="fetchSessions(meta.current_page)" title="Refresh">
                    <i class="ri-refresh-line"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="sc-table-wrap">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Tanggal</th>
                        <th>Judul / Mata Pelajaran</th>
                        <th class="hide-sm">Tipe</th>
                        <th class="hide-sm">Rata-rata</th>
                        <th class="hide-sm">Siswa</th>
                        <th class="hide-sm">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Skeleton --}}
                    <template x-if="loading">
                        <template x-for="i in 5" :key="i">
                            <tr>
                                <td><div class="dt-skel w-24" style="margin:0 auto"></div></td>
                                <td><div class="dt-skel" style="width:100px"></div></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="dt-skel-av"></div>
                                        <div style="flex:1">
                                            <div class="dt-skel w-3/4" style="margin-bottom:6px"></div>
                                            <div class="dt-skel w-1/2"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel" style="width:120px"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td><div class="dt-skel" style="width:60px"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Rows --}}
                    <template x-if="!loading && sessions.length > 0">
                        <template x-for="(s, i) in sessions" :key="s.score_session_id">
                            <tr @click="goToDetail(s.score_session_id)">
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap"
                                         x-text="s.score_date_label"></div>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="dt-av av-violet" x-text="initials(s.subject_name)"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="s.title"></div>
                                            <div class="dt-muted" style="font-size:11px;margin-top:2px"
                                                 x-text="s.subject_name + ' · ' + s.grade_name"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    <span :class="'sc-badge sc-badge-' + s.score_type"
                                          x-text="s.score_type_label"></span>
                                </td>
                                <td class="hide-sm">
                                    <template x-if="s.avg_score !== null">
                                        <div class="sc-avg-wrap">
                                            <div class="sc-avg-bar-track">
                                                <div class="sc-avg-bar-fill"
                                                     :style="'width:' + Math.min((s.avg_score / s.max_score) * 100, 100) + '%;background:' + scoreColor(s.avg_score, s.max_score)">
                                                </div>
                                            </div>
                                            <span class="sc-avg-val" x-text="s.avg_score"></span>
                                        </div>
                                    </template>
                                    <template x-if="s.avg_score === null">
                                        <span class="dt-muted">—</span>
                                    </template>
                                </td>
                                <td class="hide-sm">
                                    <span style="font-size:13px;color:var(--text-secondary)">
                                        <span x-text="s.filled_count"></span>
                                        <span style="color:var(--text-muted)"> / <span x-text="s.total_students"></span></span>
                                    </span>
                                </td>
                                <td class="hide-sm">
                                    <span :class="s.is_published ? 'sc-badge sc-badge-published' : 'sc-badge sc-badge-unpublished'">
                                        <i :class="s.is_published ? 'ri-eye-line' : 'ri-eye-off-line'"></i>
                                        <span x-text="s.is_published ? 'Published' : 'Draft'"></span>
                                    </span>
                                </td>
                                <td @click.stop>
                                    <a :href="detailUrl(s.score_session_id)" class="sc-action" style="white-space:nowrap">
                                        <i class="ri-edit-line"></i>
                                        Isi / Edit
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </template>

                </tbody>
            </table>

            {{-- Empty --}}
            <div x-show="!loading && sessions.length === 0" class="sc-empty">
                <div class="sc-empty-icon"><i class="ri-file-list-3-line"></i></div>
                <div class="sc-empty-title">Belum ada sesi nilai</div>
                <div class="sc-empty-sub">Mulai dari halaman Jadwal dan klik "Input Nilai", atau klik "Buat Sesi Nilai" di atas</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer" x-show="!loading && meta.total > 0">
            <div class="dt-info">
                Menampilkan
                <strong x-text="sessions.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + sessions.length"
                ></strong>
                dari <strong x-text="meta.total"></strong> sesi
            </div>
            <div class="dt-pagination">
                <button class="dt-page" @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <template x-for="page in meta.last_page" :key="page">
                    <button class="dt-page" :class="page === meta.current_page ? 'is-active' : ''"
                        @click="changePage(page)" x-text="page"></button>
                </template>
                <button class="dt-page" @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
        </div>

    </div>{{-- /dt-wrap --}}


    {{-- ══════════════════════════════════════════════════════════════
         MODAL: BUAT SESI NILAI BARU
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="showCreateModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="sc-modal-overlay"
            @click.self="closeCreateModal()"
            @keydown.escape.window="closeCreateModal()"
        >
            <div
                x-show="showCreateModal"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="sc-modal-box"
            >
                {{-- Header --}}
                <div class="sc-modal-head">
                    <div class="sc-modal-title">
                        <div style="width:30px;height:30px;border-radius:8px;background:#F5F3FF;color:#7C3AED;display:grid;place-items:center">
                            <i class="ri-file-add-line" style="font-size:15px"></i>
                        </div>
                        Buat Sesi Nilai Baru
                    </div>
                    <button class="sc-modal-close" @click="closeCreateModal()">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="sc-modal-body">

                    {{-- Info: dari jadwal (hanya muncul jika ada scheduleInfo) --}}
                    <template x-if="scheduleInfo">
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;">
                            <i class="ri-calendar-check-line" style="color:#2563EB;font-size:16px;flex-shrink:0"></i>
                            <div>
                                <div style="font-size:12px;font-weight:700;color:#1D4ED8">Dari Jadwal</div>
                                <div style="font-size:12px;color:#3B82F6" x-text="scheduleInfo.label + ' · ' + scheduleInfo.semester_name"></div>
                            </div>
                        </div>
                    </template>

                    {{-- Tipe Penilaian --}}
                    <div class="sc-field">
                        <div class="sc-field-label">Tipe Penilaian <span>*</span></div>
                        <div class="sc-type-grid">
                            <template x-for="t in scoreTypes" :key="t.value">
                                <button
                                    type="button"
                                    class="sc-type-chip"
                                    :class="form.score_type === t.value ? 'active-' + t.value : ''"
                                    @click="form.score_type = t.value"
                                >
                                    <i :class="t.icon"></i>
                                    <span x-text="t.label"></span>
                                </button>
                            </template>
                        </div>
                        <div x-show="errors.score_type" class="sc-field-label" style="color:var(--danger);font-weight:400" x-text="errors.score_type"></div>
                    </div>

                    {{-- Kelas & Mapel --}}
                    <div class="sc-field">
                        <label class="sc-field-label">Kelas & Mata Pelajaran <span>*</span></label>

                        {{-- Jika dari jadwal: tampilkan sebagai readonly info, bukan dropdown --}}
                        <template x-if="scheduleInfo">
                            <div style="height:38px;padding:0 12px;border:1.5px solid #BFDBFE;border-radius:8px;background:#EFF6FF;display:flex;align-items:center;font-size:13px;font-weight:600;color:#1D4ED8;gap:6px">
                                <i class="ri-lock-line" style="font-size:12px;opacity:.6"></i>
                                <span x-text="scheduleInfo.label"></span>
                            </div>
                        </template>

                        {{-- Jika bukan dari jadwal: dropdown normal --}}
                        <template x-if="!scheduleInfo">
                            <select
                                x-model="form.grade_subject_id"
                                class="sc-field-input"
                                style="cursor:pointer"
                                :disabled="loadingGradeSubjects"
                            >
                                <option value="">
                                    <span x-text="loadingGradeSubjects ? 'Memuat...' : '— Pilih kelas & mapel —'"></span>
                                </option>
                                <template x-for="gs in gradeSubjects" :key="gs.grade_subject_id">
                                    <option :value="gs.grade_subject_id" x-text="gs.label"></option>
                                </template>
                            </select>
                        </template>

                        <div x-show="errors.grade_subject_id" class="sc-field-label" style="color:var(--danger);font-weight:400" x-text="errors.grade_subject_id"></div>
                    </div>

                    {{-- Semester --}}
                    <div class="sc-field">
                        <label class="sc-field-label">Semester <span>*</span></label>
                        <select x-model="form.semester_id" class="sc-field-input" style="cursor:pointer">
                            <option value="">— Pilih semester —</option>
                            <template x-for="sem in semesters" :key="sem.semester_id">
                                <option :value="sem.semester_id" x-text="sem.semester_name"></option>
                            </template>
                        </select>
                        <div x-show="errors.semester_id" class="sc-field-label" style="color:var(--danger);font-weight:400" x-text="errors.semester_id"></div>
                    </div>

                    {{-- Judul --}}
                    <div class="sc-field">
                        <label class="sc-field-label">Judul / Topik <span>*</span></label>
                        <input
                            x-model="form.title"
                            type="text"
                            class="sc-field-input"
                            placeholder="Contoh: Ulangan Bab 3 - Ekosistem"
                            maxlength="255"
                        />
                        <div x-show="errors.title" class="sc-field-label" style="color:var(--danger);font-weight:400" x-text="errors.title"></div>
                    </div>

                    {{-- Tanggal + Nilai Maks --}}
                    <div class="sc-field-row">
                        <div class="sc-field">
                            <label class="sc-field-label">Tanggal <span>*</span></label>
                            <input
                                x-model="form.score_date"
                                type="date"
                                class="sc-field-input"
                            />
                            <div x-show="errors.score_date" class="sc-field-label" style="color:var(--danger);font-weight:400" x-text="errors.score_date"></div>
                        </div>
                        <div class="sc-field">
                            <label class="sc-field-label">Nilai Maksimum <span>*</span></label>
                            <input
                                x-model="form.max_score"
                                type="number"
                                class="sc-field-input"
                                min="1" max="1000"
                                placeholder="100"
                            />
                            <div x-show="errors.max_score" class="sc-field-label" style="color:var(--danger);font-weight:400" x-text="errors.max_score"></div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="sc-field">
                        <label class="sc-field-label">Deskripsi (opsional)</label>
                        <textarea
                            x-model="form.description"
                            class="sc-field-textarea"
                            placeholder="Keterangan tambahan..."
                            maxlength="1000"
                            rows="3"
                        ></textarea>
                    </div>

                </div>{{-- /sc-modal-body --}}

                {{-- Footer --}}
                <div class="sc-modal-foot">
                    <button type="button" class="sc-btn" @click="closeCreateModal()" :disabled="creating">
                        Batal
                    </button>
                    <button
                        type="button"
                        class="sc-btn sc-btn-primary"
                        @click="submitCreate()"
                        :disabled="creating"
                    >
                        <svg x-show="creating" style="width:13px;height:13px;animation:spin .7s linear infinite" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" opacity=".75"></path>
                        </svg>
                        <i x-show="!creating" class="ri-add-line"></i>
                        <span x-text="creating ? 'Membuat...' : 'Buat & Isi Nilai'"></span>
                    </button>
                </div>

            </div>{{-- /sc-modal-box --}}
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
function teacherScoreIndex(config = {}) {
    return {
        // ─── Config ────────────────────────────────────────────
        indexUrl:         config.indexUrl         ?? '/teacher/scores',
        storeUrl:         config.storeUrl         ?? '/teacher/scores',
        semestersUrl:     config.semestersUrl      ?? '/teacher/scores/semesters',
        gradeSubjectsUrl: config.gradeSubjectsUrl  ?? '/teacher/scores/grade-subjects',

        // scheduleInfo dari query ?schedule_id=X — null jika tidak ada
        scheduleInfo:     config.scheduleInfo      ?? null,

        // ─── State ─────────────────────────────────────────────
        sessions:         [],
        meta:             { current_page: 1, per_page: 15, total: 0, last_page: 1 },
        loading:          false,
        search:           '',
        perPage:          15,
        // semesterFilter:   config.activeSemesterId ?? '',
        semesterFilter:   '',
        typeFilter:       '',
        publishedFilter:  '',
        semesters:        [],
        gradeSubjects:    [],
        loadingGradeSubjects: false,

        // ─── Create modal ───────────────────────────────────────
        showCreateModal: false,
        creating:        false,
        errors:          {},
        form: {
            score_type:      'daily',
            grade_subject_id:'',
            semester_id:     config.activeSemesterId ?? '',
            title:           '',
            description:     '',
            score_date:      new Date().toISOString().split('T')[0],
            max_score:       100,
        },

        // ─── Constants ──────────────────────────────────────────
        scoreTypes: [
            { value: 'daily',           label: 'Ulangan',  icon: 'ri-file-text-line' },
            { value: 'mid_exam',        label: 'UTS',      icon: 'ri-file-shield-line' },
            { value: 'final_exam',      label: 'UAS',      icon: 'ri-file-shield-2-line' },
            { value: 'assignment',      label: 'Tugas',    icon: 'ri-task-line' },
        ],

        // ─── Init ───────────────────────────────────────────────
        async init() {
            await this.fetchSemesters();
            this.fetchSessions();
            await this.fetchGradeSubjects();

            // ── Jika masuk dari ?schedule_id=X, langsung buka modal prefilled ──
            if (this.scheduleInfo) {
                this.openCreateModal();
            }
        },

        // ─── Fetch semesters ────────────────────────────────────
        async fetchSemesters() {
            try {
                const res = await fetch(this.semestersUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                this.semesters = await res.json();
                if (!this.form.semester_id) {
                    const active = this.semesters.find(s => s.status === 'active');
                    if (active) {
                        this.form.semester_id = active.semester_id;
                        // this.semesterFilter   = active.semester_id;
                    }
                }
            } catch { this.semesters = []; }
        },

        // ─── Fetch grade subjects ────────────────────────────────
        async fetchGradeSubjects(semesterId = null) {
            this.loadingGradeSubjects = true;
            try {
                const params = new URLSearchParams();
                const sid = semesterId ?? this.form.semester_id;
                if (sid) params.append('semester_id', sid);

                const res = await fetch(`${this.gradeSubjectsUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                this.gradeSubjects = await res.json();
            } catch { this.gradeSubjects = []; }
            finally { this.loadingGradeSubjects = false; }
        },

        // ─── Fetch sessions ─────────────────────────────────────
        async fetchSessions(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage),
                    ...(this.search.trim()     ? { search:       this.search.trim()    } : {}),
                    ...(this.semesterFilter    ? { semester_id:  this.semesterFilter   } : {}),
                    ...(this.typeFilter        ? { score_type:   this.typeFilter       } : {}),
                    ...(this.publishedFilter   ? { is_published: this.publishedFilter  } : {}),
                });
                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json     = await res.json();
                this.sessions  = json.data;
                this.meta      = json.meta;
            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchSessions(page);
        },

        // ─── Create modal ────────────────────────────────────────
        openCreateModal() {
            this.errors = {};

            // ── Tentukan prefill: dari scheduleInfo jika ada, fallback ke default ──
            const si = this.scheduleInfo;

            this.form = {
                score_type:       'daily',
                // Jika dari jadwal → prefill grade_subject_id langsung
                grade_subject_id: si ? si.grade_subject_id : (this.gradeSubjects[0]?.grade_subject_id ?? ''),
                // Jika dari jadwal → prefill semester dari jadwal, else aktif
                semester_id:      si ? si.semester_id
                                     : (this.semesterFilter || (this.semesters.find(s => s.status === 'active')?.semester_id ?? '')),
                title:            '',
                description:      '',
                score_date:       new Date().toISOString().split('T')[0],
                max_score:        100,
            };

            // Jika dari jadwal, pastikan grade_subjects sudah dimuat dengan semester yang sesuai
            // dan grade subject nya ada dalam list (inject manual jika belum ada)
            if (si && this.gradeSubjects.length > 0) {
                const exists = this.gradeSubjects.find(gs => gs.grade_subject_id == si.grade_subject_id);
                if (!exists) {
                    // Inject entry dari scheduleInfo agar tampil di dropdown
                    this.gradeSubjects.unshift({
                        grade_subject_id: si.grade_subject_id,
                        subject_name:     si.subject_name,
                        grade_name:       si.grade_name,
                        grade_id:         si.grade_id ?? null,
                        label:            si.label,
                    });
                }
            }

            this.showCreateModal = true;
        },

        closeCreateModal() {
            if (this.creating) return;
            this.showCreateModal = false;
            this.errors = {};
        },

        async submitCreate() {
            this.errors  = {};
            this.creating = true;
            try {
                const res  = await fetch(this.storeUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.csrf(),
                    },
                    body: JSON.stringify({
                        ...this.form,
                        max_score: parseFloat(this.form.max_score),
                    }),
                });
                const json = await res.json();

                if (res.status === 422) {
                    const errs = json.errors ?? {};
                    this.errors = Object.fromEntries(
                        Object.entries(errs).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
                    );
                    return;
                }

                if (!res.ok) throw new Error(json.message ?? 'Gagal membuat sesi.');

                this.showCreateModal = false;
                window.location.href = this.detailUrl(json.data.score_session_id);

            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.creating = false;
            }
        },

        // ─── Helpers ────────────────────────────────────────────
        goToDetail(id) { window.location.href = this.detailUrl(id); },
        detailUrl(id)  { return this.indexUrl.replace(/\/$/, '') + '/' + id; },

        scoreColor(score, max) {
            const pct = max > 0 ? (score / max) * 100 : 0;
            if (pct >= 80) return '#22C55E';
            if (pct >= 60) return '#F59E0B';
            return '#EF4444';
        },

        initials(name) {
            return (name ?? '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() ?? '').join('').slice(0, 2);
        },

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
                });
            }
        },
    };
}
</script>
@endpush