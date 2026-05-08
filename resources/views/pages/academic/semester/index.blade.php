@extends('layouts.app')

@section('title', 'Semester Management')

@section('content')
<div
    x-data="semesterSearch({
        indexUrl:        '{{ route('semesters.index') }}',
        storeUrl:        '{{ route('semesters.store') }}',
        showUrl:         '{{ url('semesters') }}',
        updateUrl:       '{{ url('semesters') }}',
        destroyUrl:      '{{ url('semesters') }}',
        toggleStatusUrl: '{{ url('semesters') }}',
        academicYearUrl: '{{ route('semesters.academic-years') }}',
    })"
    x-init="init()"
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
                <li><span>Semester</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Semester Management
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola semua semester dalam setiap tahun ajaran
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Semester
        </button>
    </div>

    {{-- ── STAT STRIP ──────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="ri-calendar-schedule-line"></i></div>
                <div class="stat-trend up"><i class="ri-arrow-up-s-line"></i></div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Total Semester</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-calendar-check-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="semesters.filter(s => s.status === 'active').length || '—'">—</div>
            <div class="stat-label">Semester Aktif</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-book-open-line"></i></div>
                <div class="stat-trend flat">Total</div>
            </div>
            <div class="stat-value" x-text="academicYears.length || '—'">—</div>
            <div class="stat-label">Tahun Ajaran Tersedia</div>
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
                        @input.debounce.400ms="fetchSemesters()"
                        type="text"
                        placeholder="Cari semester..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchSemesters()" class="dt-search-clear">×</button>
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div style="position:relative" @click.outside="academicYearFilterOpen=false">
                    <div
                        @click="academicYearFilterOpen = !academicYearFilterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:180px;cursor:pointer;">
                        <span x-text="academicYearFilterLabel()" style="font-size:13px"></span>
                        <i class="ri-arrow-down-s-line"
                            style="font-size:16px;transition:.2s"
                            :style="academicYearFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="academicYearFilterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:200px;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;max-height:220px;overflow-y:auto">
                        <div
                            @click="setAcademicYearFilter(''); academicYearFilterOpen=false"
                            style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                            @mouseenter="$el.style.background='var(--bg)'"
                            @mouseleave="$el.style.background='transparent'"
                            :style="academicYearFilter === '' ? 'background:var(--bg);font-weight:600' : ''">
                            <span>Semua Tahun Ajaran</span>
                            <i x-show="academicYearFilter === ''" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                        </div>
                        <template x-for="ay in academicYears" :key="ay.academic_year_id">
                            <div
                                @click="setAcademicYearFilter(ay.academic_year_id)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="String(academicYearFilter) === String(ay.academic_year_id) ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="ay.academic_year_name"></span>
                                <i x-show="String(academicYearFilter) === String(ay.academic_year_id)"
                                    class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Filter Status --}}
                <div style="position:relative" @click.outside="filterOpen=false">
                    <div
                        @click="filterOpen = !filterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:160px;cursor:pointer;">
                        <span x-text="filterLabel()"></span>
                        <i class="ri-arrow-down-s-line"
                            style="font-size:16px;transition:.2s"
                            :style="filterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="filterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;">
                        <template x-for="opt in [
                                { label: 'Semua Status', value: '' },
                                { label: 'Aktif',        value: 'active' },
                                { label: 'Non-Aktif',    value: 'inactive' },
                            ]" :key="opt.value">
                            <div
                                @click="setStatusFilter(opt.value); filterOpen=false"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="statusFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="statusFilter === opt.value"
                                    class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                <select x-model="perPage" @change="fetchSemesters()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchSemesters(meta.current_page)">
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
                        <th>Nama Semester</th>
                        <th class="hide-sm">Tahun Ajaran</th>
                        <th class="hide-sm">Tanggal Mulai</th>
                        <th class="hide-sm">Tanggal Selesai</th>
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
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && semesters.length > 0">
                        <template x-for="(semester, i) in semesters" :key="semester.semester_id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div class="dt-user">
                                        <div class="dt-av" :class="avatarColor(i)"
                                             x-text="initials(semester.semester_name)"></div>
                                        <div class="dt-user-name" x-text="semester.semester_name"></div>
                                    </div>
                                </td>
                                <td class="hide-sm dt-muted"
                                    x-text="semester.academic_year?.academic_year_name || '—'"></td>
                                <td class="hide-sm dt-muted"
                                    x-text="semester.start_date || '—'"></td>
                                <td class="hide-sm dt-muted"
                                    x-text="semester.end_date || '—'"></td>
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(semester)"
                                        :title="semester.status === 'active' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="semester.status === 'active'
                                            ? 'background:#ECFDF5;color:#059669;'
                                            : 'background:#FFF7ED;color:#D97706;'"
                                    >
                                        <span
                                            style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"
                                            :style="semester.status === 'active' ? 'background:#10B981' : 'background:#D1D5DB'"
                                        >
                                            <span
                                                style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"
                                                :style="semester.status === 'active' ? 'transform:translateX(14px)' : 'transform:translateX(2px)'"
                                            ></span>
                                        </span>
                                        <span
                                            class="dt-badge"
                                            :class="semester.status === 'active' ? 'aktif' : 'nonaktif'"
                                            x-text="semester.status === 'active' ? 'Aktif' : 'Non-Aktif'">
                                        </span>
                                    </button>
                                </td>
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act" title="Edit"
                                             @click="openEditModal(semester.semester_id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus"
                                             @click="deleteSemester(semester.semester_id, semester.semester_name)">
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
            <div x-show="!loading && semesters.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-calendar-schedule-line"></i></div>
                <div class="dt-empty-title">Tidak ada data semester</div>
                <div class="dt-empty-sub">Coba ubah kata kunci pencarian atau tambahkan semester baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="semesters.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + semesters.length"
                ></strong>
                dari <strong x-text="meta.total"></strong> data
            </div>
            <div class="dt-pagination">
                <button class="dt-page"
                    @click="changePage(meta.current_page - 1)"
                    :disabled="meta.current_page <= 1">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <template x-for="page in meta.last_page" :key="page">
                    <button class="dt-page" :class="page === meta.current_page ? 'is-active' : ''"
                        @click="changePage(page)" x-text="page"></button>
                </template>
                <button class="dt-page"
                    @click="changePage(meta.current_page + 1)"
                    :disabled="meta.current_page >= meta.last_page">
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
                style="max-width:560px"
            >
                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-calendar-schedule-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Semester' : 'Tambah Semester'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi semester' : 'Buat semester baru untuk tahun ajaran'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px;max-height:65vh;overflow-y:auto">

                    {{-- Semester Name --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Nama Semester <span style="color:var(--danger)">*</span>
                        </label>
                        <input
                            x-model="form.semester_name"
                            type="text"
                            placeholder="Contoh: Semester Ganjil"
                            class="form-input"
                            :style="errors.semester_name ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.semester_name?'var(--danger)':'var(--border)';$el.style.background=errors.semester_name?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                        />
                        <p x-show="errors.semester_name" x-text="errors.semester_name"
                           style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Tahun Ajaran --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Tahun Ajaran <span style="color:var(--danger)">*</span>
                        </label>
                        <select
                            x-model="form.academic_year_id"
                            class="form-input"
                            :style="errors.academic_year_id ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.academic_year_id?'var(--danger)':'var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <template x-for="ay in academicYears" :key="ay.academic_year_id">
                                <option :value="ay.academic_year_id" x-text="ay.academic_year_name"></option>
                            </template>
                        </select>
                        <p x-show="errors.academic_year_id" x-text="errors.academic_year_id"
                           style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Tanggal Mulai & Selesai --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px" >
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Tanggal Mulai <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.start_date"
                                type="date"
                                class="form-input"
                                :style="errors.start_date ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.start_date?'var(--danger)':'var(--border)';$el.style.background=errors.start_date?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p x-show="errors.start_date" x-text="errors.start_date"
                               style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Tanggal Selesai <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.end_date"
                                type="date"
                                class="form-input"
                                :style="errors.end_date ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.end_date?'var(--danger)':'var(--border)';$el.style.background=errors.end_date?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p x-show="errors.end_date" x-text="errors.end_date"
                               style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        </div>
                    </div>

                    {{-- UTS --}}
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Periode UTS <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                        </label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div class="form-group">
                                <input
                                    x-model="form.midterm_start_date"
                                    type="date"
                                    placeholder="Mulai UTS"
                                    style="width:100%;height:40px;padding:0 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;transition:all .18s"
                                    @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                    @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                                />
                                <p style="font-size:11px;color:var(--text-muted);margin-top:4px">Mulai UTS</p>
                            </div>
                            <div class="form-group">
                                <input
                                    x-model="form.midterm_end_date"
                                    type="date"
                                    style="width:100%;height:40px;padding:0 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;transition:all .18s"
                                    @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                    @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                                />
                                <p style="font-size:11px;color:var(--text-muted);margin-top:4px">Selesai UTS</p>
                            </div>
                        </div>
                    </div>

                    {{-- UAS --}}
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Periode UAS <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                        </label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div class="form-group">
                                <input
                                    x-model="form.final_start_date"
                                    type="date"
                                    style="width:100%;height:40px;padding:0 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;transition:all .18s"
                                    @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                    @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                                />
                                <p style="font-size:11px;color:var(--text-muted);margin-top:4px">Mulai UAS</p>
                            </div>
                            <div class="form-group">
                                <input
                                    x-model="form.final_end_date"
                                    type="date"
                                    style="width:100%;height:40px;padding:0 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;transition:all .18s"
                                    @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                    @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                                />
                                <p style="font-size:11px;color:var(--text-muted);margin-top:4px">Selesai UAS</p>
                            </div>
                        </div>
                    </div>

                    {{-- Status (Edit only) --}}
                    <div x-show="isEditing">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Status
                        </label>
                        <select
                            x-model="form.status"
                            class="form-input"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="inactive">Non-Aktif</option>
                            <option value="active">Aktif</option>
                        </select>
                        <p style="font-size:11.5px;color:var(--text-muted);margin-top:5px">
                            Hanya satu semester yang dapat aktif per tahun ajaran
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:16px 24px;border-top:1px solid var(--border);background:var(--bg)">
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