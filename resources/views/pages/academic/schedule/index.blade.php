@extends('layouts.app')

@section('title', 'Schedule Management')

@push('styles')
<style>
/* ─── Searchable Select Component (Schedule) ───────────────────────────── */
.sch-s2-wrap {
    position: relative;
}

.sch-s2-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    width: 100%;
    min-height: 40px;
    padding: 0 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--bg);
    cursor: pointer;
    font-family: var(--font);
    font-size: 13.5px;
    color: var(--text-primary);
    transition: border-color .18s, box-shadow .18s;
    user-select: none;
}
.sch-s2-trigger:hover { border-color: #9CA3AF; }
.sch-s2-trigger.is-open {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    background: var(--card);
}
.sch-s2-trigger.has-error {
    border-color: var(--danger);
    background: #FFF5F5;
}

.sch-s2-trigger-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.sch-s2-trigger-left i {
    font-size: 15px;
    color: var(--text-muted);
    flex-shrink: 0;
}
.sch-s2-trigger-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 13.5px;
}
.sch-s2-trigger-text.is-placeholder { color: var(--text-muted); }

.sch-s2-trigger-right {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.sch-s2-clear {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #D1D5DB;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
    transition: background .15s;
    border: none;
    padding: 0;
}
.sch-s2-clear:hover { background: #9CA3AF; }

.sch-s2-chevron {
    font-size: 16px;
    color: var(--text-muted);
    transition: transform .2s;
}
.sch-s2-chevron.is-open { transform: rotate(180deg); }

/* ─── Dropdown Panel ────────────────────────────────────────────────────── */
.sch-s2-panel {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--card);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    box-shadow: 0 8px 24px rgba(0,0,0,.10);
    z-index: 9999;
    overflow: hidden;
    min-width: 220px;
}

.sch-s2-search-wrap {
    position: relative;
    padding: 8px 10px;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
}
.sch-s2-search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: var(--text-muted);
    pointer-events: none;
}
.sch-s2-search-input {
    width: 100%;
    height: 34px;
    padding: 0 10px 0 32px;
    border: 1.5px solid var(--border);
    border-radius: 6px;
    background: var(--card);
    font-family: var(--font);
    font-size: 12.5px;
    color: var(--text-primary);
    outline: none;
    transition: border-color .15s;
}
.sch-s2-search-input:focus { border-color: var(--primary); }

/* Options list */
.sch-s2-list {
    max-height: 210px;
    overflow-y: auto;
    padding: 4px 0;
}
.sch-s2-list::-webkit-scrollbar { width: 4px; }
.sch-s2-list::-webkit-scrollbar-track { background: transparent; }
.sch-s2-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.sch-s2-option {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 12px;
    font-size: 13px;
    cursor: pointer;
    transition: background .12s;
    color: var(--text-primary);
    user-select: none;
}
.sch-s2-option:hover { background: var(--bg); }
.sch-s2-option.is-selected {
    background: var(--primary-xlight);
    color: var(--primary);
    font-weight: 600;
}

.sch-s2-option-icon {
    font-size: 14px;
    flex-shrink: 0;
    width: 18px;
    text-align: center;
}
.sch-s2-option-body {
    flex: 1;
    min-width: 0;
}
.sch-s2-option-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 13px;
}
.sch-s2-option-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sch-s2-option-check {
    font-size: 14px;
    color: var(--primary);
    flex-shrink: 0;
}
.sch-s2-empty {
    padding: 14px 12px;
    text-align: center;
    font-size: 12.5px;
    color: var(--text-muted);
}

/* ─── Modal scroll & footer ─────────────────────────────────────────────── */
.sch-modal-body {
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: calc(80vh - 140px);
    overflow-y: auto;
    overflow-x: visible;
}
.sch-modal-body::-webkit-scrollbar { width: 5px; }
.sch-modal-body::-webkit-scrollbar-track { background: transparent; }
.sch-modal-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.sch-field-label {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 6px;
}
.sch-field-error {
    font-size: 11.5px;
    color: var(--danger);
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
</style>
@endpush

@section('content')
<div
    x-data="scheduleSearch({
        indexUrl:         '{{ route('schedules.index') }}',
        storeUrl:         '{{ route('schedules.store') }}',
        showUrl:          '{{ url('schedules') }}',
        updateUrl:        '{{ url('schedules') }}',
        destroyUrl:       '{{ url('schedules') }}',
        toggleStatusUrl:  '{{ url('schedules') }}',
        semestersUrl:     '{{ route('schedules.semesters') }}',
        roomsUrl:         '{{ route('schedules.rooms') }}',
        gradeSubjectsUrl: '{{ route('schedules.grade-subjects') }}',
    })"
    x-init="init()"
    @click="closeAllSelects()"
>

    {{-- ── PAGE HEADER ─────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
        <div>
            <ul class="breadcrumb-list">
                <li>
                    <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                        <i class="ri-home-4-line"></i> Dashboard
                    </a>
                </li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Akademik</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>Jadwal</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Schedule Management
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola semua jadwal pelajaran dalam sistem
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Jadwal
        </button>
    </div>

    {{-- ── STAT STRIP ──────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="ri-calendar-schedule-line"></i></div>
                <div class="stat-trend up">Total</div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Total Jadwal</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="schedules.filter(s => s.status === 'active').length || '—'">—</div>
            <div class="stat-label">Jadwal Aktif</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-door-open-line"></i></div>
                <div class="stat-trend flat">Ruangan</div>
            </div>
            <div class="stat-value" x-text="[...new Set(schedules.map(s => s.room_id))].length || '—'">—</div>
            <div class="stat-label">Ruangan Terpakai</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-header">
                <div class="stat-icon" style="background:#FFF7ED;color:#D97706"><i class="ri-book-open-line"></i></div>
                <div class="stat-trend flat">Semester</div>
            </div>
            <div class="stat-value" x-text="semesters.length || '—'">—</div>
            <div class="stat-label">Semester Tersedia</div>
        </div>
    </div>

    {{-- ── DATATABLE ────────────────────────────────────────────── --}}
    <div class="dt-wrap">

        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">

                {{-- Search --}}
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input
                        x-model="search"
                        @input.debounce.400ms="fetchSchedules()"
                        type="text"
                        placeholder="Cari mata pelajaran, kelas, ruangan..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchSchedules()" class="dt-search-clear">×</button>
                </div>

                {{-- Semester filter --}}
                <select
                    x-model="semesterFilter"
                    @change="setSemesterFilter($event.target.value)"
                    class="dt-select"
                    style="min-width:160px"
                >
                    <option value="">Semua Semester</option>
                    <template x-for="sem in semesters" :key="sem.semester_id">
                        <option :value="sem.semester_id" x-text="sem.semester_name"></option>
                    </template>
                </select>

                {{-- Day filter dropdown --}}
                <div style="position:relative" @click.outside="dayFilterOpen=false">
                    <div
                        @click="dayFilterOpen = !dayFilterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:140px;cursor:pointer"
                    >
                        <span x-text="dayLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                           :style="dayFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="dayFilterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50"
                    >
                        <div
                            @click="setDayFilter('')"
                            style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                            @mouseenter="$el.style.background='var(--bg)'"
                            @mouseleave="$el.style.background='transparent'"
                            :style="dayFilter === '' ? 'background:var(--bg);font-weight:600' : ''"
                        >
                            <span>Semua Hari</span>
                            <i x-show="dayFilter === ''" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                        </div>
                        <template x-for="day in days" :key="day.value">
                            <div
                                @click="setDayFilter(day.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="String(dayFilter) === String(day.value) ? 'background:var(--bg);font-weight:600' : ''"
                            >
                                <span x-text="day.label"></span>
                                <i x-show="String(dayFilter) === String(day.value)" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Session type filter dropdown --}}
                <div style="position:relative" @click.outside="sessionFilterOpen=false">
                    <div
                        @click="sessionFilterOpen = !sessionFilterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:150px;cursor:pointer"
                    >
                        <span x-text="sessionLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                           :style="sessionFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="sessionFilterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50"
                    >
                        <div
                            @click="setSessionFilter('')"
                            style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                            @mouseenter="$el.style.background='var(--bg)'"
                            @mouseleave="$el.style.background='transparent'"
                            :style="sessionFilter === '' ? 'background:var(--bg);font-weight:600' : ''"
                        >
                            <span>Semua Sesi</span>
                            <i x-show="sessionFilter === ''" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                        </div>
                        <template x-for="st in sessionTypes" :key="st.value">
                            <div
                                @click="setSessionFilter(st.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="sessionFilter === st.value ? 'background:var(--bg);font-weight:600' : ''"
                            >
                                <span><span x-text=""></span>&nbsp;<span x-text="st.label"></span></span>
                                <i x-show="sessionFilter === st.value" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Status filter dropdown --}}
                <div style="position:relative" @click.outside="filterOpen=false">
                    <div
                        @click="filterOpen = !filterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:140px;cursor:pointer"
                    >
                        <span x-text="statusLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                           :style="filterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="filterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50"
                    >
                        <template x-for="opt in [
                            { label:'Semua Status', value:'' },
                            { label:'Aktif',        value:'active' },
                            { label:'Non-Aktif',    value:'inactive' },
                        ]" :key="opt.value">
                            <div
                                @click="setStatusFilter(opt.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="statusFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''"
                            >
                                <span x-text="opt.label"></span>
                                <i x-show="statusFilter === opt.value" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                <select x-model="perPage" @change="fetchSchedules()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>

            <div class="dt-divider"></div>

            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchSchedules(meta.current_page)">
                    <i class="ri-refresh-line"></i>
                </button>
                <button class="dt-icon-btn" title="Export">
                    <i class="ri-download-2-line"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="dt-table-wrap">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Mata Pelajaran / Kelas</th>
                        <th class="hide-sm">Hari & Waktu</th>
                        <th class="hide-sm">Ruangan</th>
                        <th class="hide-sm">Semester</th>
                        <th class="hide-sm">Tipe Sesi</th>
                        <th class="hide-sm">Status</th>
                        <th class="col-act">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- Skeleton --}}
                    <template x-if="loading">
                        <template x-for="i in 5" :key="i">
                            <tr>
                                <td class="col-no"><div class="dt-skel w-24" style="margin:0 auto"></div></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:11px">
                                        <div class="dt-skel-av"></div>
                                        <div style="flex:1">
                                            <div class="dt-skel w-3/4" style="margin-bottom:6px"></div>
                                            <div class="dt-skel w-1/2"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && schedules.length > 0">
                        <template x-for="(schedule, i) in schedules" :key="schedule.schedule_id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:11px">
                                        <div class="dt-av" :class="avatarColor(i)"
                                             x-text="initials(schedule.subject_name)"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="schedule.subject_name || '—'"></div>
                                            <div class="dt-muted" style="font-size:11px;margin-top:2px">
                                                <span x-text="schedule.grade_name || '—'"></span>
                                                <template x-if="schedule.teacher_name">
                                                    <span> · <span x-text="schedule.teacher_name"></span></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    <div class="dt-user-name" style="font-size:13px" x-text="schedule.day_name"></div>
                                    <div class="dt-muted" style="font-size:11px;margin-top:2px;font-variant-numeric:tabular-nums"
                                         x-text="schedule.time_range"></div>
                                </td>
                                <td class="hide-sm">
                                    <div class="dt-user-name" style="font-size:13px" x-text="schedule.room_name || '—'"></div>
                                    <div class="dt-muted" style="font-size:11px;margin-top:2px" x-text="schedule.room_code || ''"></div>
                                </td>
                                <td class="hide-sm dt-muted" x-text="schedule.semester_name || '—'"></td>
                                <td class="hide-sm">
                                    <span
                                        style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600"
                                        :style="sessionBadgeStyle(schedule.session_type)"
                                        x-text="sessionBadgeLabel(schedule.session_type)"
                                    ></span>
                                </td>
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(schedule)"
                                        :title="schedule.status === 'active' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="schedule.status === 'active'
                                            ? 'background:#ECFDF5;color:#059669'
                                            : 'background:#FFF7ED;color:#D97706'"
                                    >
                                        <span
                                            style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"
                                            :style="schedule.status === 'active' ? 'background:#10B981' : 'background:#D1D5DB'"
                                        >
                                            <span
                                                style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"
                                                :style="schedule.status === 'active' ? 'transform:translateX(14px)' : 'transform:translateX(2px)'"
                                            ></span>
                                        </span>
                                        <span x-text="schedule.status === 'active' ? 'Aktif' : 'Non-Aktif'"></span>
                                    </button>
                                </td>
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act" title="Edit"
                                             @click="openEditModal(schedule.schedule_id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus"
                                             @click="deleteSchedule(schedule.schedule_id, `${schedule.subject_name} - ${schedule.day_name}`)">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>

                </tbody>
            </table>

            {{-- Empty --}}
            <div x-show="!loading && schedules.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-calendar-schedule-line"></i></div>
                <div class="dt-empty-title">Tidak ada data jadwal</div>
                <div class="dt-empty-sub">Coba ubah filter atau tambahkan jadwal baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="schedules.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + schedules.length"
                ></strong>
                dari <strong x-text="meta.total"></strong> data
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


    {{-- ════════════════════════════════════════════════════════════
         MODAL CREATE / EDIT
    ════════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="closeModal()"
            @keydown.escape.window="closeModal()"
            class="modal-overlay"
        >
            <div
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="modal-box"
                style="max-width:560px;display:flex;flex-direction:column;max-height:90vh"
                @click.stop="closeAllSelects()"
            >
                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border);flex-shrink:0">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-calendar-schedule-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Jadwal' : 'Tambah Jadwal'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi jadwal pelajaran' : 'Buat jadwal pelajaran baru'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="sch-modal-body">

                    {{-- ── 1. Kelas / Mata Pelajaran ── --}}
                    <div>
                        <label class="sch-field-label">
                            Kelas / Mata Pelajaran <span style="color:var(--danger)">*</span>
                        </label>
                        <div class="sch-s2-wrap" @click.stop>
                            {{-- Trigger --}}
                            <div
                                class="sch-s2-trigger"
                                :class="{ 'is-open': _selects.grade_subject.open, 'has-error': !!errors.grade_subject_id }"
                                @click="toggleSelect('grade_subject')"
                            >
                                <div class="sch-s2-trigger-left">
                                    <i class="ri-book-2-line"></i>
                                    <span class="sch-s2-trigger-text"
                                          :class="{ 'is-placeholder': !hasValue('grade_subject') }"
                                          x-text="selectLabel('grade_subject')"></span>
                                </div>
                                <div class="sch-s2-trigger-right">
                                    <button x-show="hasValue('grade_subject')"
                                            class="sch-s2-clear"
                                            @click="clearSelect('grade_subject', $event)"
                                            title="Hapus pilihan">×</button>
                                    <i class="sch-s2-chevron ri-arrow-down-s-line"
                                       :class="{ 'is-open': _selects.grade_subject.open }"></i>
                                </div>
                            </div>
                            {{-- Panel --}}
                            <div class="sch-s2-panel" x-show="_selects.grade_subject.open" x-transition>
                                <div class="sch-s2-search-wrap">
                                    <i class="ri-search-line sch-s2-search-icon"></i>
                                    <input
                                        id="sch-sel-search-grade_subject"
                                        x-model="_selects.grade_subject.query"
                                        class="sch-s2-search-input"
                                        placeholder="Cari kelas atau mata pelajaran..."
                                        @keydown.escape="closeAllSelects()"
                                        @click.stop
                                    />
                                </div>
                                <div class="sch-s2-list">
                                    <template x-for="opt in filteredOptions('grade_subject')" :key="opt.value">
                                        <div class="sch-s2-option"
                                             :class="{ 'is-selected': isSelected('grade_subject', opt.value) }"
                                             @click.stop="pickOption('grade_subject', opt.value)">
                                            <i class="sch-s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                            <div class="sch-s2-option-body">
                                                <div class="sch-s2-option-label" x-text="opt.label"></div>
                                                <div x-show="opt.sub" class="sch-s2-option-sub" x-text="opt.sub"></div>
                                            </div>
                                            <i x-show="isSelected('grade_subject', opt.value)"
                                               class="sch-s2-option-check ri-check-line"></i>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions('grade_subject').length === 0" class="sch-s2-empty">
                                        Tidak ada hasil
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="errors.grade_subject_id" class="sch-field-error">
                            <i class="ri-error-warning-line"></i>
                            <span x-text="errors.grade_subject_id"></span>
                        </div>
                    </div>

                    {{-- ── 2. Semester ── --}}
                    <div>
                        <label class="sch-field-label">
                            Semester <span style="color:var(--danger)">*</span>
                        </label>
                        <div class="sch-s2-wrap" @click.stop>
                            <div
                                class="sch-s2-trigger"
                                :class="{ 'is-open': _selects.semester.open, 'has-error': !!errors.semester_id }"
                                @click="toggleSelect('semester')"
                            >
                                <div class="sch-s2-trigger-left">
                                    <i class="ri-calendar-line"></i>
                                    <span class="sch-s2-trigger-text"
                                          :class="{ 'is-placeholder': !hasValue('semester') }"
                                          x-text="selectLabel('semester')"></span>
                                </div>
                                <div class="sch-s2-trigger-right">
                                    <button x-show="hasValue('semester')"
                                            class="sch-s2-clear"
                                            @click="clearSelect('semester', $event)"
                                            title="Hapus pilihan">×</button>
                                    <i class="sch-s2-chevron ri-arrow-down-s-line"
                                       :class="{ 'is-open': _selects.semester.open }"></i>
                                </div>
                            </div>
                            <div class="sch-s2-panel" x-show="_selects.semester.open" x-transition>
                                <div class="sch-s2-search-wrap">
                                    <i class="ri-search-line sch-s2-search-icon"></i>
                                    <input
                                        id="sch-sel-search-semester"
                                        x-model="_selects.semester.query"
                                        class="sch-s2-search-input"
                                        placeholder="Cari semester..."
                                        @keydown.escape="closeAllSelects()"
                                        @click.stop
                                    />
                                </div>
                                <div class="sch-s2-list">
                                    <template x-for="opt in filteredOptions('semester')" :key="opt.value">
                                        <div class="sch-s2-option"
                                             :class="{ 'is-selected': isSelected('semester', opt.value) }"
                                             @click.stop="pickOption('semester', opt.value)">
                                            <i class="sch-s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                            <div class="sch-s2-option-body">
                                                <div class="sch-s2-option-label" x-text="opt.label"></div>
                                            </div>
                                            <i x-show="isSelected('semester', opt.value)"
                                               class="sch-s2-option-check ri-check-line"></i>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions('semester').length === 0" class="sch-s2-empty">
                                        Tidak ada hasil
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div x-show="errors.semester_id" class="sch-field-error">
                            <i class="ri-error-warning-line"></i>
                            <span x-text="errors.semester_id"></span>
                        </div>
                    </div>

                    {{-- ── 3. Hari & Ruangan (2-col) ── --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

                        {{-- Hari --}}
                        <div>
                            <label class="sch-field-label">
                                Hari <span style="color:var(--danger)">*</span>
                            </label>
                            <div class="sch-s2-wrap" @click.stop>
                                <div
                                    class="sch-s2-trigger"
                                    :class="{ 'is-open': _selects.day.open, 'has-error': !!errors.day_of_week }"
                                    @click="toggleSelect('day')"
                                >
                                    <div class="sch-s2-trigger-left">
                                        <i class="ri-calendar-event-line"></i>
                                        <span class="sch-s2-trigger-text"
                                              :class="{ 'is-placeholder': !hasValue('day') }"
                                              x-text="selectLabel('day')"></span>
                                    </div>
                                    <div class="sch-s2-trigger-right">
                                        <button x-show="hasValue('day')"
                                                class="sch-s2-clear"
                                                @click="clearSelect('day', $event)"
                                                title="Hapus pilihan">×</button>
                                        <i class="sch-s2-chevron ri-arrow-down-s-line"
                                           :class="{ 'is-open': _selects.day.open }"></i>
                                    </div>
                                </div>
                                <div class="sch-s2-panel" x-show="_selects.day.open" x-transition>
                                    <div class="sch-s2-search-wrap">
                                        <i class="ri-search-line sch-s2-search-icon"></i>
                                        <input
                                            id="sch-sel-search-day"
                                            x-model="_selects.day.query"
                                            class="sch-s2-search-input"
                                            placeholder="Cari hari..."
                                            @keydown.escape="closeAllSelects()"
                                            @click.stop
                                        />
                                    </div>
                                    <div class="sch-s2-list">
                                        <template x-for="opt in filteredOptions('day')" :key="opt.value">
                                            <div class="sch-s2-option"
                                                 :class="{ 'is-selected': isSelected('day', opt.value) }"
                                                 @click.stop="pickOption('day', opt.value)">
                                                <i class="sch-s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                                <div class="sch-s2-option-body">
                                                    <div class="sch-s2-option-label" x-text="opt.label"></div>
                                                </div>
                                                <i x-show="isSelected('day', opt.value)"
                                                   class="sch-s2-option-check ri-check-line"></i>
                                            </div>
                                        </template>
                                        <div x-show="filteredOptions('day').length === 0" class="sch-s2-empty">
                                            Tidak ada hasil
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="errors.day_of_week" class="sch-field-error">
                                <i class="ri-error-warning-line"></i>
                                <span x-text="errors.day_of_week"></span>
                            </div>
                        </div>

                        {{-- Ruangan --}}
                        <div>
                            <label class="sch-field-label">
                                Ruangan <span style="color:var(--danger)">*</span>
                            </label>
                            <div class="sch-s2-wrap" @click.stop>
                                <div
                                    class="sch-s2-trigger"
                                    :class="{ 'is-open': _selects.room.open, 'has-error': !!errors.room_id }"
                                    @click="toggleSelect('room')"
                                >
                                    <div class="sch-s2-trigger-left">
                                        <i class="ri-door-open-line"></i>
                                        <span class="sch-s2-trigger-text"
                                              :class="{ 'is-placeholder': !hasValue('room') }"
                                              x-text="selectLabel('room')"></span>
                                    </div>
                                    <div class="sch-s2-trigger-right">
                                        <button x-show="hasValue('room')"
                                                class="sch-s2-clear"
                                                @click="clearSelect('room', $event)"
                                                title="Hapus pilihan">×</button>
                                        <i class="sch-s2-chevron ri-arrow-down-s-line"
                                           :class="{ 'is-open': _selects.room.open }"></i>
                                    </div>
                                </div>
                                <div class="sch-s2-panel" x-show="_selects.room.open" x-transition>
                                    <div class="sch-s2-search-wrap">
                                        <i class="ri-search-line sch-s2-search-icon"></i>
                                        <input
                                            id="sch-sel-search-room"
                                            x-model="_selects.room.query"
                                            class="sch-s2-search-input"
                                            placeholder="Cari ruangan..."
                                            @keydown.escape="closeAllSelects()"
                                            @click.stop
                                        />
                                    </div>
                                    <div class="sch-s2-list">
                                        <template x-for="opt in filteredOptions('room')" :key="opt.value">
                                            <div class="sch-s2-option"
                                                 :class="{ 'is-selected': isSelected('room', opt.value) }"
                                                 @click.stop="pickOption('room', opt.value)">
                                                <i class="sch-s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                                <div class="sch-s2-option-body">
                                                    <div class="sch-s2-option-label" x-text="opt.label"></div>
                                                    <div x-show="opt.sub" class="sch-s2-option-sub" x-text="opt.sub"></div>
                                                </div>
                                                <i x-show="isSelected('room', opt.value)"
                                                   class="sch-s2-option-check ri-check-line"></i>
                                            </div>
                                        </template>
                                        <div x-show="filteredOptions('room').length === 0" class="sch-s2-empty">
                                            Tidak ada hasil
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="errors.room_id" class="sch-field-error">
                                <i class="ri-error-warning-line"></i>
                                <span x-text="errors.room_id"></span>
                            </div>
                        </div>
                    </div>

                    {{-- ── 4. Jam Mulai & Jam Selesai (tetap input time) ── --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label class="sch-field-label">
                                Jam Mulai <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.start_time"
                                type="time"
                                class="form-input"
                                :style="errors.start_time ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.start_time?'var(--danger)':'var(--border)';$el.style.boxShadow='none'"
                            />
                            <div x-show="errors.start_time" class="sch-field-error">
                                <i class="ri-error-warning-line"></i>
                                <span x-text="errors.start_time"></span>
                            </div>
                        </div>
                        <div>
                            <label class="sch-field-label">
                                Jam Selesai <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.end_time"
                                type="time"
                                class="form-input"
                                :style="errors.end_time ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.end_time?'var(--danger)':'var(--border)';$el.style.boxShadow='none'"
                            />
                            <div x-show="errors.end_time" class="sch-field-error">
                                <i class="ri-error-warning-line"></i>
                                <span x-text="errors.end_time"></span>
                            </div>
                        </div>
                    </div>

                    {{-- ── 5. Session Type (card picker — tidak diubah) ── --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:10px">
                            Tipe Sesi <span style="color:var(--danger)">*</span>
                        </label>

                        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px">
                            <template x-for="st in sessionTypes" :key="st.value">
                                <button
                                    type="button"
                                    @click="form.session_type = st.value"
                                    style="position:relative;padding:12px;border-radius:14px;border:1.5px solid var(--border);background:var(--card);cursor:pointer;transition:all .18s ease;text-align:left;display:flex;align-items:flex-start;gap:10px;min-height:72px"
                                    :style="form.session_type === st.value
                                        ? 'border-color:var(--primary);background:var(--primary-xlight);box-shadow:0 0 0 3px rgba(59,130,246,.08)'
                                        : ''"
                                >
                                    <div
                                        style="width:38px;height:38px;border-radius:10px;display:grid;place-items:center;flex-shrink:0;font-size:18px"
                                        :style="form.session_type === st.value
                                            ? 'background:rgba(59,130,246,.12);color:var(--primary)'
                                            : 'background:var(--bg);color:var(--text-muted)'"
                                    >
                                        <i :class="st.icon"></i>
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div style="font-size:13px;font-weight:700;color:var(--text-primary);line-height:1.2"
                                             x-text="st.label"></div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;line-height:1.4"
                                             x-text="st.description"></div>
                                    </div>
                                    <div
                                        x-show="form.session_type === st.value"
                                        x-transition
                                        style="position:absolute;top:10px;right:10px;width:20px;height:20px;border-radius:999px;background:var(--primary);color:#fff;display:grid;place-items:center;font-size:12px"
                                    >
                                        <i class="ri-check-line"></i>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div x-show="errors.session_type" class="sch-field-error" style="margin-top:6px">
                            <i class="ri-error-warning-line"></i>
                            <span x-text="errors.session_type"></span>
                        </div>
                    </div>

                    {{-- ── 6. Status (Edit only) — searchable select ── --}}
                    <div x-show="isEditing">
                        <label class="sch-field-label">Status</label>
                        <div class="sch-s2-wrap" @click.stop>
                            <div
                                class="sch-s2-trigger"
                                :class="{ 'is-open': _selects.status_form.open }"
                                @click="toggleSelect('status_form')"
                            >
                                <div class="sch-s2-trigger-left">
                                    <i class="ri-toggle-line"></i>
                                    <span class="sch-s2-trigger-text"
                                          :class="{ 'is-placeholder': !hasValue('status_form') }"
                                          x-text="selectLabel('status_form')"></span>
                                </div>
                                <div class="sch-s2-trigger-right">
                                    <i class="sch-s2-chevron ri-arrow-down-s-line"
                                       :class="{ 'is-open': _selects.status_form.open }"></i>
                                </div>
                            </div>
                            <div class="sch-s2-panel" x-show="_selects.status_form.open" x-transition>
                                <div class="sch-s2-search-wrap">
                                    <i class="ri-search-line sch-s2-search-icon"></i>
                                    <input
                                        id="sch-sel-search-status_form"
                                        x-model="_selects.status_form.query"
                                        class="sch-s2-search-input"
                                        placeholder="Cari status..."
                                        @keydown.escape="closeAllSelects()"
                                        @click.stop
                                    />
                                </div>
                                <div class="sch-s2-list">
                                    <template x-for="opt in filteredOptions('status_form')" :key="opt.value">
                                        <div class="sch-s2-option"
                                             :class="{ 'is-selected': isSelected('status_form', opt.value) }"
                                             @click.stop="pickOption('status_form', opt.value)">
                                            <i class="sch-s2-option-icon"
                                               :class="opt.icon ?? 'ri-circle-line'"
                                               :style="opt.color ? `color:${opt.color}` : ''"></i>
                                            <div class="sch-s2-option-body">
                                                <div class="sch-s2-option-label" x-text="opt.label"></div>
                                            </div>
                                            <i x-show="isSelected('status_form', opt.value)"
                                               class="sch-s2-option-check ri-check-line"></i>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions('status_form').length === 0" class="sch-s2-empty">
                                        Tidak ada hasil
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /sch-modal-body --}}

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:16px 24px;border-top:1px solid var(--border);background:var(--bg);flex-shrink:0">
                    <button @click="closeModal()"
                        style="height:38px;padding:0 16px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--card);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer">
                        Batal
                    </button>
                    <button @click="submitForm()" :disabled="submitting" class="dt-btn dt-btn-primary" style="height:38px">
                        <svg x-show="submitting" class="animate-spin" style="width:14px;height:14px;flex-shrink:0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <i x-show="!submitting" class="ri-save-2-line"></i>
                        <span x-text="submitting ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan')"></span>
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>{{-- /x-data --}}
@endsection