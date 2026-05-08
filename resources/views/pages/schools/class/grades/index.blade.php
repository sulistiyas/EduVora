@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@push('styles')
<style>
/* ─── Searchable Select Component ─────────────────────────────────────────── */
.s2-wrap {
    position: relative;
}

.s2-trigger {
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

.s2-trigger:hover {
    border-color: #9CA3AF;
}

.s2-trigger.is-open {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    background: var(--card);
}

.s2-trigger.has-error {
    border-color: var(--danger);
    background: #FFF5F5;
}

.s2-trigger-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}

.s2-trigger-left i {
    font-size: 15px;
    color: var(--text-muted);
    flex-shrink: 0;
}

.s2-trigger-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.s2-trigger-text.is-placeholder {
    color: var(--text-muted);
}

.s2-trigger-right {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.s2-clear {
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

.s2-clear:hover { background: #9CA3AF; }

.s2-chevron {
    font-size: 16px;
    color: var(--text-muted);
    transition: transform .2s;
}

.s2-chevron.is-open { transform: rotate(180deg); }

/* ─── Dropdown Panel ───────────────────────────────────────────────────────── */
.s2-panel {
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

/* Search bar inside dropdown */
.s2-search-wrap {
    padding: 8px 10px;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
}

.s2-search-input {
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

.s2-search-input:focus {
    border-color: var(--primary);
}

.s2-search-wrap {
    position: relative;
}

.s2-search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: var(--text-muted);
    pointer-events: none;
}

/* Options list */
.s2-list {
    max-height: 210px;
    overflow-y: auto;
    padding: 4px 0;
}

.s2-list::-webkit-scrollbar { width: 4px; }
.s2-list::-webkit-scrollbar-track { background: transparent; }
.s2-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.s2-option {
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

.s2-option:hover {
    background: var(--bg);
}

.s2-option.is-selected {
    background: var(--primary-xlight);
    color: var(--primary);
    font-weight: 600;
}

.s2-option.is-muted {
    color: var(--text-muted);
    font-style: italic;
}

.s2-option-icon {
    font-size: 14px;
    flex-shrink: 0;
    width: 18px;
    text-align: center;
}

.s2-option-body {
    flex: 1;
    min-width: 0;
}

.s2-option-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 13px;
}

.s2-option-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.s2-option-check {
    font-size: 14px;
    color: var(--primary);
    flex-shrink: 0;
}

.s2-empty {
    padding: 14px 12px;
    text-align: center;
    font-size: 12.5px;
    color: var(--text-muted);
}

/* ─── Modal scrollable body ────────────────────────────────────────────────── */
.modal-body-scroll {
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    /* fixed height + scroll so footer always visible */
    max-height: calc(80vh - 140px);
    overflow-y: auto;
    overflow-x: visible;
}

.modal-body-scroll::-webkit-scrollbar { width: 5px; }
.modal-body-scroll::-webkit-scrollbar-track { background: transparent; }
.modal-body-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
.modal-body-scroll::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

/* Sticky modal footer */
.modal-footer-sticky {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    padding: 14px 24px;
    border-top: 1px solid var(--border);
    background: var(--bg);
    border-radius: 0 0 var(--radius) var(--radius);
    flex-shrink: 0;
}

/* Form field label */
.field-label {
    display: block;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 6px;
}

.field-label .req {
    color: var(--danger);
    margin-left: 2px;
}

.field-label .opt {
    font-size: 11px;
    font-weight: 400;
    color: var(--text-muted);
    margin-left: 4px;
}

.field-error {
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
    x-data="gradeSearch({
        indexUrl:         '{{ route('grades.index') }}',
        storeUrl:         '{{ route('grades.store') }}',
        showUrl:          '{{ url('grades') }}',
        updateUrl:        '{{ url('grades') }}',
        destroyUrl:       '{{ url('grades') }}',
        toggleStatusUrl:  '{{ url('grades') }}',
        roomsUrl:         '{{ route('grades.rooms') }}',
        teachersUrl:      '{{ route('grades.teachers') }}',
        academicYearsUrl: '{{ route('grades.academic-years') }}',
    })"
    x-init="init()"
    @click="closeAllSelects()"
>

    {{-- ── PAGE HEADER ──────────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
        <div>
            <ul class="breadcrumb-list">
                <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Akademik</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>Kelas</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Manajemen Kelas
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola semua kelas dan tingkatan dalam setiap tahun ajaran
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Kelas
        </button>
    </div>

    {{-- ── STAT STRIP ───────────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="ri-building-4-line"></i></div>
                <div class="stat-trend up"><i class="ri-arrow-up-s-line"></i></div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Total Kelas</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="grades.filter(g => g.status === 'active').length || '—'">—</div>
            <div class="stat-label">Kelas Aktif</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-graduation-cap-line"></i></div>
                <div class="stat-trend flat">Lulus</div>
            </div>
            <div class="stat-value" x-text="grades.filter(g => g.status === 'graduated').length || '—'">—</div>
            <div class="stat-label">Kelas Lulus</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F3F4F6;color:#6B7280"><i class="ri-archive-line"></i></div>
                <div class="stat-trend flat">Arsip</div>
            </div>
            <div class="stat-value" x-text="grades.filter(g => g.status === 'archived').length || '—'">—</div>
            <div class="stat-label">Diarsipkan</div>
        </div>
    </div>

    {{-- ── DATATABLE ────────────────────────────────────────────────── --}}
    <div class="dt-wrap">

        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">

                {{-- Search --}}
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input x-model="search" @input.debounce.400ms="fetchGrades()"
                        type="text" placeholder="Cari nama kelas, tingkatan..."
                        class="dt-search-input" />
                    <button x-show="search" @click="search=''; fetchGrades()" class="dt-search-clear">×</button>
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div style="position:relative" @click.outside="filterAcademicYearOpen=false">
                    <div @click="filterAcademicYearOpen = !filterAcademicYearOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:180px;cursor:pointer;">
                        <span x-text="academicYearFilterLabel()" style="font-size:13px"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                           :style="filterAcademicYearOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div x-show="filterAcademicYearOpen" x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:200px;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;max-height:220px;overflow-y:auto">
                        <div @click="setAcademicYearFilter('')"
                            style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                            @mouseenter="$el.style.background='var(--bg)'" @mouseleave="$el.style.background='transparent'"
                            :style="academicYearFilter === '' ? 'background:var(--bg);font-weight:600' : ''">
                            <span>Semua Tahun Ajaran</span>
                            <i x-show="academicYearFilter === ''" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                        </div>
                        <template x-for="ay in academicYears" :key="ay.academic_year_id">
                            <div @click="setAcademicYearFilter(ay.academic_year_id)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'" @mouseleave="$el.style.background='transparent'"
                                :style="String(academicYearFilter) === String(ay.academic_year_id) ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="ay.academic_year_name"></span>
                                <i x-show="String(academicYearFilter) === String(ay.academic_year_id)"
                                   class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Filter Ruangan --}}
                <div style="position:relative" @click.outside="filterRoomOpen=false">
                    <div @click="filterRoomOpen = !filterRoomOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:160px;cursor:pointer;">
                        <span x-text="roomFilterLabel()" style="font-size:13px"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                           :style="filterRoomOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div x-show="filterRoomOpen" x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:180px;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;max-height:220px;overflow-y:auto">
                        <div @click="setRoomFilter('')"
                            style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                            @mouseenter="$el.style.background='var(--bg)'" @mouseleave="$el.style.background='transparent'"
                            :style="roomFilter === '' ? 'background:var(--bg);font-weight:600' : ''">
                            <span>Semua Ruangan</span>
                            <i x-show="roomFilter === ''" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                        </div>
                        <template x-for="room in rooms" :key="room.room_id">
                            <div @click="setRoomFilter(room.room_id)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'" @mouseleave="$el.style.background='transparent'"
                                :style="String(roomFilter) === String(room.room_id) ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="room.room_name"></span>
                                <i x-show="String(roomFilter) === String(room.room_id)"
                                   class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Filter Status --}}
                <div style="position:relative" @click.outside="filterStatusOpen=false">
                    <div @click="filterStatusOpen = !filterStatusOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:150px;cursor:pointer;">
                        <span x-text="statusFilterLabel()" style="font-size:13px"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                           :style="filterStatusOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div x-show="filterStatusOpen" x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;">
                        <template x-for="opt in [
                            {label:'Semua Status',value:''},{label:'Aktif',value:'active'},
                            {label:'Non-Aktif',value:'inactive'},{label:'Lulus',value:'graduated'},
                            {label:'Diarsipkan',value:'archived'}]" :key="opt.value">
                            <div @click="setStatusFilter(opt.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'" @mouseleave="$el.style.background='transparent'"
                                :style="statusFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="statusFilter === opt.value" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                <select x-model="perPage" @change="fetchGrades()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchGrades(meta.current_page)">
                    <i class="ri-refresh-line"></i>
                </button>
                <button class="dt-icon-btn" title="Export"><i class="ri-download-2-line"></i></button>
            </div>
        </div>

        {{-- Table --}}
        <div class="dt-table-wrap">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Nama Kelas</th>
                        <th class="hide-sm">Tingkatan</th>
                        <th class="hide-sm">Tahun Ajaran</th>
                        <th class="hide-sm">Ruangan</th>
                        <th class="hide-sm">Wali Kelas</th>
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
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && grades.length > 0">
                        <template x-for="(grade, i) in grades" :key="grade.grade_id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div class="dt-user">
                                        <div class="dt-av" :class="avatarColor(i)"
                                             x-text="initials(grade.grade_name)"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="grade.grade_name"></div>
                                            <div class="dt-muted" style="font-size:11.5px"
                                                 x-text="'Kelas ' + levelLabel(grade.level)"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:40px;height:28px;padding:0 8px;border-radius:6px;background:var(--primary-xlight);color:var(--primary);font-size:13px;font-weight:700"
                                          x-text="levelLabel(grade.level)"></span>
                                </td>
                                <td class="hide-sm dt-muted"
                                    x-text="grade.academic_year?.academic_year_name || '—'"></td>
                                <td class="hide-sm dt-muted"
                                    x-text="grade.room?.room_name || '—'"></td>
                                <td class="hide-sm dt-muted"
                                    x-text="grade.homeroom_teacher?.full_name || '—'"></td>
                                <td class="hide-sm">
                                    <button @click="toggleStatus(grade)"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="`background:${statusConfig(grade.status).bg};color:${statusConfig(grade.status).color};`">
                                        <span style="width:7px;height:7px;border-radius:50%;flex-shrink:0"
                                              :style="`background:${statusConfig(grade.status).color}`"></span>
                                        <span x-text="statusConfig(grade.status).label"></span>
                                    </button>
                                </td>
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act" title="Edit"
                                             @click="openEditModal(grade.grade_id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus"
                                             @click="deleteGrade(grade.grade_id, grade.grade_name)">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>

            <div x-show="!loading && grades.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-building-4-line"></i></div>
                <div class="dt-empty-title">Tidak ada data kelas</div>
                <div class="dt-empty-sub">Coba ubah filter atau tambahkan kelas baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="grades.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + grades.length"></strong>
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


    {{-- ══════════════════════════════════════════════════════════════
         MODAL CREATE / EDIT
    ══════════════════════════════════════════════════════════════════ --}}
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
                {{-- ── Modal Header ── --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border);flex-shrink:0">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-building-4-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Kelas' : 'Tambah Kelas'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi kelas' : 'Buat kelas baru untuk tahun ajaran'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- ── Modal Body (scrollable) ── --}}
                <div class="modal-body-scroll">

                    {{-- Nama Kelas --}}
                    <div>
                        <label class="field-label">
                            Nama Kelas <span class="req">*</span>
                        </label>
                        <input
                            x-model="form.grade_name"
                            type="text"
                            placeholder="Contoh: IPA A, IPS B, RPL 1"
                            class="form-input"
                            :style="errors.grade_name ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.grade_name?'var(--danger)':'var(--border)';$el.style.background=errors.grade_name?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                        />
                        <div x-show="errors.grade_name" class="field-error">
                            <i class="ri-error-warning-line"></i>
                            <span x-text="errors.grade_name"></span>
                        </div>
                    </div>

                    {{-- Tingkatan & Tahun Ajaran (2-col grid) --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

                        {{-- Tingkatan — searchable select --}}
                        <div>
                            <label class="field-label">
                                Tingkatan <span class="req">*</span>
                            </label>
                            <div class="s2-wrap" @click.stop>
                                {{-- Trigger --}}
                                <div
                                    class="s2-trigger"
                                    :class="{ 'is-open': _selects.level.open, 'has-error': !!errors.level }"
                                    @click="toggleSelect('level')"
                                >
                                    <div class="s2-trigger-left">
                                        <i class="ri-sort-number-asc"></i>
                                        <span class="s2-trigger-text"
                                              :class="{ 'is-placeholder': !hasValue('level') }"
                                              x-text="selectLabel('level')"></span>
                                    </div>
                                    <div class="s2-trigger-right">
                                        <button x-show="hasValue('level')"
                                                class="s2-clear"
                                                @click="clearSelect('level', $event)"
                                                title="Hapus pilihan">×</button>
                                        <i class="s2-chevron ri-arrow-down-s-line"
                                           :class="{ 'is-open': _selects.level.open }"></i>
                                    </div>
                                </div>
                                {{-- Panel --}}
                                <div class="s2-panel" x-show="_selects.level.open" x-transition>
                                    <div class="s2-search-wrap">
                                        <i class="ri-search-line s2-search-icon"></i>
                                        <input
                                            :id="`sel-search-level`"
                                            x-model="_selects.level.query"
                                            class="s2-search-input"
                                            placeholder="Cari tingkatan..."
                                            @keydown.escape="closeAllSelects()"
                                            @click.stop
                                        />
                                    </div>
                                    <div class="s2-list">
                                        <template x-for="opt in filteredOptions('level')" :key="opt.value">
                                            <div class="s2-option"
                                                 :class="{ 'is-selected': isSelected('level', opt.value) }"
                                                 @click.stop="pickOption('level', opt.value)">
                                                <i class="s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                                <div class="s2-option-body">
                                                    <div class="s2-option-label" x-text="opt.label"></div>
                                                </div>
                                                <i x-show="isSelected('level', opt.value)"
                                                   class="s2-option-check ri-check-line"></i>
                                            </div>
                                        </template>
                                        <div x-show="filteredOptions('level').length === 0" class="s2-empty">
                                            Tidak ada hasil
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="errors.level" class="field-error">
                                <i class="ri-error-warning-line"></i>
                                <span x-text="errors.level"></span>
                            </div>
                        </div>

                        {{-- Tahun Ajaran — searchable select --}}
                        <div>
                            <label class="field-label">
                                Tahun Ajaran <span class="req">*</span>
                            </label>
                            <div class="s2-wrap" @click.stop>
                                <div
                                    class="s2-trigger"
                                    :class="{ 'is-open': _selects.academic_year.open, 'has-error': !!errors.academic_year_id }"
                                    @click="toggleSelect('academic_year')"
                                >
                                    <div class="s2-trigger-left">
                                        <i class="ri-calendar-line"></i>
                                        <span class="s2-trigger-text"
                                              :class="{ 'is-placeholder': !hasValue('academic_year') }"
                                              x-text="selectLabel('academic_year')"></span>
                                    </div>
                                    <div class="s2-trigger-right">
                                        <button x-show="hasValue('academic_year')"
                                                class="s2-clear"
                                                @click="clearSelect('academic_year', $event)"
                                                title="Hapus pilihan">×</button>
                                        <i class="s2-chevron ri-arrow-down-s-line"
                                           :class="{ 'is-open': _selects.academic_year.open }"></i>
                                    </div>
                                </div>
                                <div class="s2-panel" x-show="_selects.academic_year.open" x-transition>
                                    <div class="s2-search-wrap">
                                        <i class="ri-search-line s2-search-icon"></i>
                                        <input
                                            :id="`sel-search-academic_year`"
                                            x-model="_selects.academic_year.query"
                                            class="s2-search-input"
                                            placeholder="Cari tahun ajaran..."
                                            @keydown.escape="closeAllSelects()"
                                            @click.stop
                                        />
                                    </div>
                                    <div class="s2-list">
                                        <template x-for="opt in filteredOptions('academic_year')" :key="opt.value">
                                            <div class="s2-option"
                                                 :class="{ 'is-selected': isSelected('academic_year', opt.value) }"
                                                 @click.stop="pickOption('academic_year', opt.value)">
                                                <i class="s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                                <div class="s2-option-body">
                                                    <div class="s2-option-label" x-text="opt.label"></div>
                                                </div>
                                                <i x-show="isSelected('academic_year', opt.value)"
                                                   class="s2-option-check ri-check-line"></i>
                                            </div>
                                        </template>
                                        <div x-show="filteredOptions('academic_year').length === 0" class="s2-empty">
                                            Tidak ada hasil
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="errors.academic_year_id" class="field-error">
                                <i class="ri-error-warning-line"></i>
                                <span x-text="errors.academic_year_id"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Ruangan — searchable select --}}
                    <div>
                        <label class="field-label">
                            Ruangan
                        </label>
                        <div class="s2-wrap" @click.stop>
                            <div
                                class="s2-trigger"
                                :class="{ 'is-open': _selects.room.open }"
                                @click="toggleSelect('room')"
                            >
                                <div class="s2-trigger-left">
                                    <i class="ri-door-open-line"></i>
                                    <span class="s2-trigger-text"
                                          :class="{ 'is-placeholder': !hasValue('room') }"
                                          x-text="selectLabel('room')"></span>
                                </div>
                                <div class="s2-trigger-right">
                                    <button x-show="hasValue('room')"
                                            class="s2-clear"
                                            @click="clearSelect('room', $event)"
                                            title="Hapus pilihan">×</button>
                                    <i class="s2-chevron ri-arrow-down-s-line"
                                       :class="{ 'is-open': _selects.room.open }"></i>
                                </div>
                            </div>
                            <div class="s2-panel" x-show="_selects.room.open" x-transition>
                                <div class="s2-search-wrap">
                                    <i class="ri-search-line s2-search-icon"></i>
                                    <input
                                        :id="`sel-search-room`"
                                        x-model="_selects.room.query"
                                        class="s2-search-input"
                                        placeholder="Cari ruangan..."
                                        @keydown.escape="closeAllSelects()"
                                        @click.stop
                                    />
                                </div>
                                <div class="s2-list">
                                    <template x-for="opt in filteredOptions('room')" :key="String(opt.value)">
                                        <div class="s2-option"
                                             :class="{
                                                'is-selected': isSelected('room', opt.value),
                                                'is-muted': opt.muted
                                             }"
                                             @click.stop="pickOption('room', opt.value)">
                                            <i class="s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                            <div class="s2-option-body">
                                                <div class="s2-option-label" x-text="opt.label"></div>
                                                <div x-show="opt.sub" class="s2-option-sub" x-text="opt.sub"></div>
                                            </div>
                                            <i x-show="isSelected('room', opt.value)"
                                               class="s2-option-check ri-check-line"></i>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions('room').length === 0" class="s2-empty">
                                        Tidak ada hasil
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Wali Kelas — searchable select --}}
                    <div>
                        <label class="field-label">
                            Wali Kelas
                        </label>
                        <div class="s2-wrap" @click.stop>
                            <div
                                class="s2-trigger"
                                :class="{ 'is-open': _selects.teacher.open }"
                                @click="toggleSelect('teacher')"
                            >
                                <div class="s2-trigger-left">
                                    <i class="ri-user-3-line"></i>
                                    <span class="s2-trigger-text"
                                          :class="{ 'is-placeholder': !hasValue('teacher') }"
                                          x-text="selectLabel('teacher')"></span>
                                </div>
                                <div class="s2-trigger-right">
                                    <button x-show="hasValue('teacher')"
                                            class="s2-clear"
                                            @click="clearSelect('teacher', $event)"
                                            title="Hapus pilihan">×</button>
                                    <i class="s2-chevron ri-arrow-down-s-line"
                                       :class="{ 'is-open': _selects.teacher.open }"></i>
                                </div>
                            </div>
                            <div class="s2-panel" x-show="_selects.teacher.open" x-transition>
                                <div class="s2-search-wrap">
                                    <i class="ri-search-line s2-search-icon"></i>
                                    <input
                                        :id="`sel-search-teacher`"
                                        x-model="_selects.teacher.query"
                                        class="s2-search-input"
                                        placeholder="Cari nama guru atau NIP..."
                                        @keydown.escape="closeAllSelects()"
                                        @click.stop
                                    />
                                </div>
                                <div class="s2-list">
                                    <template x-for="opt in filteredOptions('teacher')" :key="String(opt.value)">
                                        <div class="s2-option"
                                             :class="{
                                                'is-selected': isSelected('teacher', opt.value),
                                                'is-muted': opt.muted
                                             }"
                                             @click.stop="pickOption('teacher', opt.value)">
                                            <i class="s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"></i>
                                            <div class="s2-option-body">
                                                <div class="s2-option-label" x-text="opt.label"></div>
                                                <div x-show="opt.sub" class="s2-option-sub" x-text="opt.sub"></div>
                                            </div>
                                            <i x-show="isSelected('teacher', opt.value)"
                                               class="s2-option-check ri-check-line"></i>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions('teacher').length === 0" class="s2-empty">
                                        Tidak ada hasil
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status — searchable select (edit only) --}}
                    <div x-show="isEditing">
                        <label class="field-label">Status</label>
                        <div class="s2-wrap" @click.stop>
                            <div
                                class="s2-trigger"
                                :class="{ 'is-open': _selects.status_form.open }"
                                @click="toggleSelect('status_form')"
                            >
                                <div class="s2-trigger-left">
                                    <i class="ri-toggle-line"></i>
                                    <span class="s2-trigger-text"
                                          :class="{ 'is-placeholder': !hasValue('status_form') }"
                                          x-text="selectLabel('status_form')"></span>
                                </div>
                                <div class="s2-trigger-right">
                                    <i class="s2-chevron ri-arrow-down-s-line"
                                       :class="{ 'is-open': _selects.status_form.open }"></i>
                                </div>
                            </div>
                            <div class="s2-panel" x-show="_selects.status_form.open" x-transition>
                                <div class="s2-search-wrap">
                                    <i class="ri-search-line s2-search-icon"></i>
                                    <input
                                        :id="`sel-search-status_form`"
                                        x-model="_selects.status_form.query"
                                        class="s2-search-input"
                                        placeholder="Cari status..."
                                        @keydown.escape="closeAllSelects()"
                                        @click.stop
                                    />
                                </div>
                                <div class="s2-list">
                                    <template x-for="opt in filteredOptions('status_form')" :key="opt.value">
                                        <div class="s2-option"
                                             :class="{ 'is-selected': isSelected('status_form', opt.value) }"
                                             @click.stop="pickOption('status_form', opt.value)">
                                            <i class="s2-option-icon" :class="opt.icon ?? 'ri-circle-line'"
                                               :style="`color:${opt.color ?? 'inherit'}`"></i>
                                            <div class="s2-option-body">
                                                <div class="s2-option-label" x-text="opt.label"></div>
                                            </div>
                                            <i x-show="isSelected('status_form', opt.value)"
                                               class="s2-option-check ri-check-line"></i>
                                        </div>
                                    </template>
                                    <div x-show="filteredOptions('status_form').length === 0" class="s2-empty">
                                        Tidak ada hasil
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p style="font-size:11.5px;color:var(--text-muted);margin-top:5px">
                            Status menentukan apakah kelas sedang berjalan, selesai, atau diarsipkan
                        </p>
                    </div>

                </div>{{-- /modal-body-scroll --}}

                {{-- ── Modal Footer (sticky) ── --}}
                <div class="modal-footer-sticky">
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

            </div>{{-- /modal-box --}}
        </div>{{-- /modal-overlay --}}
    </template>

</div>{{-- /x-data --}}
@endsection