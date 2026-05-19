@extends('layouts.app')

@section('title', 'Presensi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-attendance.css') }}">
@endpush

@section('content')
<div
    x-data="teacherAttendanceIndex({
        indexUrl:     '{{ route('teacher.attendance.index') }}',
        semestersUrl: '{{ route('teacher.attendance.semesters') }}',
        activeSemesterId: '{{ $activeSemester?->semester_id ?? '' }}',
    })"
    x-init="init()"
>
    {{-- ── BREADCRUMB ────────────────────────────────────── --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Presensi</span></li>
        </ul>
    </div>

    {{-- ── PAGE HEADER ──────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2">
                Presensi
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Riwayat & kelola sesi absensi kelas Anda
            </p>
        </div>
        {{-- Shortcut ke jadwal hari ini --}}
        <a href="{{ route('teacher.schedules.index') }}" class="ta-action primary">
            <i class="ri-calendar-schedule-line"></i>
            Lihat Jadwal Hari Ini
        </a>
    </div>

    {{-- ── STAT STRIP ───────────────────────────────────── --}}
    <div class="ta-stats">
        <div class="ta-stat">
            <div class="ta-stat-icon" style="background:#EFF6FF;color:#2563EB"><i class="ri-calendar-check-line"></i></div>
            <div class="ta-stat-val">{{ $stats['total'] }}</div>
            <div class="ta-stat-label">Total Sesi</div>
            <div class="ta-stat-bar" style="background:#3B82F6"></div>
        </div>
        <div class="ta-stat">
            <div class="ta-stat-icon" style="background:#FEF9C3;color:#CA8A04"><i class="ri-edit-line"></i></div>
            <div class="ta-stat-val">{{ $stats['draft'] }}</div>
            <div class="ta-stat-label">Draft</div>
            <div class="ta-stat-bar" style="background:#EAB308"></div>
        </div>
        <div class="ta-stat">
            <div class="ta-stat-icon" style="background:#EFF6FF;color:#2563EB"><i class="ri-send-plane-line"></i></div>
            <div class="ta-stat-val">{{ $stats['submitted'] }}</div>
            <div class="ta-stat-label">Disubmit</div>
            <div class="ta-stat-bar" style="background:#2563EB"></div>
        </div>
        <div class="ta-stat">
            <div class="ta-stat-icon" style="background:#F0FDF4;color:#16A34A"><i class="ri-checkbox-circle-line"></i></div>
            <div class="ta-stat-val">{{ $stats['approved'] }}</div>
            <div class="ta-stat-label">Disetujui</div>
            <div class="ta-stat-bar" style="background:#22C55E"></div>
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
                        placeholder="Cari kelas, mapel..."
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

                {{-- Status --}}
                <select x-model="statusFilter" @change="fetchSessions()" class="dt-select">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                </select>

                {{-- Date from/to --}}
                <input
                    x-model="dateFrom"
                    @change="fetchSessions()"
                    type="date"
                    class="dt-select"
                    placeholder="Dari tanggal"
                    style="min-width:130px"
                />
                <input
                    x-model="dateTo"
                    @change="fetchSessions()"
                    type="date"
                    class="dt-select"
                    placeholder="Sampai tanggal"
                    style="min-width:130px"
                />

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
        <div class="ta-table-wrap">
            <table class="ta-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran / Kelas</th>
                        <th class="hide-sm">Pertemuan ke-</th>
                        <th class="hide-sm">Rekap Kehadiran</th>
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
                                <td><div class="dt-skel" style="width:60px"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Rows --}}
                    <template x-if="!loading && sessions.length > 0">
                        <template x-for="(s, i) in sessions" :key="s.attendance_session_id">
                            <tr @click="goToDetail(s.attendance_session_id)">
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap"
                                         x-text="s.attendance_date_label"></div>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="dt-av av-blue" x-text="initials(s.subject_name)"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="s.subject_name"></div>
                                            <div class="dt-muted" style="font-size:11px;margin-top:2px" x-text="s.grade_name"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm dt-muted">
                                    Pertemuan <strong x-text="s.meeting_number ?? '—'"></strong>
                                </td>
                                <td class="hide-sm">
                                    <div class="ta-count-row">
                                        <span class="ta-count ta-count-h"><i class="ri-check-line"></i> <span x-text="s.present_count"></span></span>
                                        <span class="ta-count ta-count-a"><i class="ri-close-line"></i> <span x-text="s.absent_count"></span></span>
                                        <template x-if="s.sick_count > 0">
                                            <span class="ta-count ta-count-s">S <span x-text="s.sick_count"></span></span>
                                        </template>
                                        <template x-if="s.permission_count > 0">
                                            <span class="ta-count ta-count-i">I <span x-text="s.permission_count"></span></span>
                                        </template>
                                        <template x-if="s.late_count > 0">
                                            <span class="ta-count ta-count-l">L <span x-text="s.late_count"></span></span>
                                        </template>
                                        <span style="font-size:11px;color:var(--text-muted)">/ <span x-text="s.total_students"></span> siswa</span>
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    <div style="display:flex;align-items:center;gap:5px">
                                        <span :class="statusBadgeClass(s.status)" class="ta-badge">
                                            <i :class="statusIcon(s.status)"></i>
                                            <span x-text="statusLabel(s.status)"></span>
                                        </span>
                                        <template x-if="s.is_locked">
                                            <span class="ta-badge ta-badge-locked" style="margin-left:4px">
                                                <i class="ri-lock-line"></i> Terkunci
                                            </span>
                                        </template>
                                    </div>
                                </td>
                                <td @click.stop>
                                    <a
                                        :href="detailUrl(s.attendance_session_id)"
                                        class="ta-action"
                                        style="white-space:nowrap"
                                    >
                                        <i class="ri-eye-line"></i>
                                        <span x-text="s.is_locked ? 'Lihat' : 'Isi / Edit'"></span>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </template>

                </tbody>
            </table>

            {{-- Empty --}}
            <div x-show="!loading && sessions.length === 0" class="ta-empty">
                <div class="ta-empty-icon"><i class="ri-calendar-close-line"></i></div>
                <div class="ta-empty-title">Belum ada sesi absensi</div>
                <div class="ta-empty-sub">Mulai dari halaman Jadwal Saya dan klik "Presensi" pada jadwal yang ingin diabsen</div>
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

</div>
@endsection

@push('scripts')
<script>
function teacherAttendanceIndex(config = {}) {
    return {
        indexUrl:     config.indexUrl     ?? '/teacher/attendance',
        semestersUrl: config.semestersUrl ?? '/teacher/attendance/semesters',

        sessions:       [],
        meta:           { current_page: 1, per_page: 15, total: 0, last_page: 1 },
        loading:        false,
        search:         '',
        perPage:        15,
        semesterFilter: '',
        statusFilter:   '',
        dateFrom:       '',
        dateTo:         '',
        semesters:      [],

        async init() {
            await this.fetchSemesters();
            this.fetchSessions();
        },

        async fetchSemesters() {
            try {
                const res = await fetch(this.semestersUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                this.semesters = await res.json();
            } catch { this.semesters = []; }
        },

        async fetchSessions(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage),
                    ...(this.search.trim()    ? { search:       this.search.trim()    } : {}),
                    ...(this.semesterFilter   ? { semester_id:  this.semesterFilter   } : {}),
                    ...(this.statusFilter     ? { status:       this.statusFilter     } : {}),
                    ...(this.dateFrom         ? { date_from:    this.dateFrom         } : {}),
                    ...(this.dateTo           ? { date_to:      this.dateTo           } : {}),
                });
                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json = await res.json();

                console.log(json);

                this.sessions = json.data;
                this.meta = json.meta;
            } catch (err) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.message, showConfirmButton:false, timer:3500 });
                }
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchSessions(page);
        },

        goToDetail(id) {
            window.location.href = this.detailUrl(id);
        },
        detailUrl(id) {
            return this.indexUrl.replace(/\/$/, '') + '/' + id;
        },

        statusLabel(status) {
            return { draft: 'Draft', submitted: 'Submitted', approved: 'Approved' }[status] ?? status;
        },
        statusBadgeClass(status) {
            return { draft: 'ta-badge-draft', submitted: 'ta-badge-submitted', approved: 'ta-badge-approved' }[status] ?? 'ta-badge-draft';
        },
        statusIcon(status) {
            return { draft: 'ri-edit-line', submitted: 'ri-send-plane-line', approved: 'ri-checkbox-circle-line' }[status] ?? 'ri-circle-line';
        },
        initials(name) {
            return (name ?? '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() ?? '').join('').slice(0, 2);
        },
    };
}
</script>
@endpush