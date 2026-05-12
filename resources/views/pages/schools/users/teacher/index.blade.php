{{-- resources/views/pages/school-admin/teachers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Guru')

@section('content')
<div
    x-data="teacherSearch({
        indexUrl:        '{{ route('school-admin.teachers.index') }}',
        showUrl:         '{{ url('school-admin/teachers') }}',
        toggleStatusUrl: '{{ url('school-admin/teachers') }}',
        subjectsUrl:     '{{ route('school-admin.teachers.subjects') }}',
    })"
    x-init="init()">

    {{-- ── PAGE HEADER ─────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
        <div>
            <ul class="breadcrumb-list">
                <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Sekolah</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>Manajemen Guru</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Manajemen Guru
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">Kelola data guru di sekolah Anda</p>
        </div>
        <a href="{{ route('school-admin.teachers.create') }}" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Guru
        </a>
    </div>

    {{-- ── STAT STRIP ──────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px">
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-group-line"></i></div>
                <div class="stat-trend flat">Total</div>
            </div>
            <div class="stat-value" x-text="stats.total || '—'">—</div>
            <div class="stat-label">Total Guru</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-user-follow-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="stats.active ?? '—'">—</div>
            <div class="stat-label">Guru Aktif</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-header">
                <div class="stat-icon" style="background:#FFF7ED;color:#D97706"><i class="ri-user-unfollow-line"></i></div>
                <div class="stat-trend flat">Non-Aktif</div>
            </div>
            <div class="stat-value" x-text="stats.inactive ?? '—'">—</div>
            <div class="stat-label">Guru Non-Aktif</div>
        </div>
        <div class="stat-card red">
            <div class="stat-header">
                <div class="stat-icon" style="background:#FEF2F2;color:#DC2626"><i class="ri-mail-close-line"></i></div>
                <div class="stat-trend flat">Perlu Aksi</div>
            </div>
            <div class="stat-value" x-text="stats.unverified ?? '—'">—</div>
            <div class="stat-label">Belum Verifikasi</div>
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
                        @input.debounce.400ms="fetchTeachers()"
                        type="text"
                        placeholder="Cari nama, NIP, mata pelajaran..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchTeachers()" class="dt-search-clear">×</button>
                </div>

                {{-- Filter Status --}}
                <div style="position:relative" @click.outside="filterOpen=false">
                    <div @click="filterOpen = !filterOpen" class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:150px;cursor:pointer">
                        <span x-text="statusLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                            :style="filterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div x-show="filterOpen" x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50">
                        <template x-for="opt in [
                            { label: 'Semua Status', value: '' },
                            { label: 'Aktif',        value: 'active' },
                            { label: 'Non-Aktif',    value: 'inactive' },
                        ]" :key="opt.value">
                            <div @click="setStatusFilter(opt.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="statusFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="statusFilter === opt.value" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Filter Tipe Pegawai --}}
                <div style="position:relative" @click.outside="empTypeFilterOpen=false">
                    <div @click="empTypeFilterOpen = !empTypeFilterOpen" class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:130px;cursor:pointer">
                        <span x-text="empTypeLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                            :style="empTypeFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div x-show="empTypeFilterOpen" x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50">
                        <template x-for="opt in [
                            { label: 'Semua Tipe', value: '' },
                            { label: 'PNS / Tetap', value: 'permanent' },
                            { label: 'Honorer',     value: 'honorary' },
                            { label: 'Kontrak',     value: 'contract' },
                        ]" :key="opt.value">
                            <div @click="setEmpTypeFilter(opt.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="empTypeFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="empTypeFilter === opt.value" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Filter Mata Pelajaran --}}
                <div style="position:relative" @click.outside="subjectFilterOpen=false">
                    <div @click="subjectFilterOpen = !subjectFilterOpen" class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:160px;cursor:pointer">
                        <span x-text="subjectFilterLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                            :style="subjectFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div x-show="subjectFilterOpen" x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:160px;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;max-height:220px;overflow-y:auto">
                        <template x-for="opt in subjects" :key="opt.value">
                            <div @click="setSubjectFilter(opt.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;white-space:nowrap"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="subjectFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="subjectFilter === opt.value" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- Per Page --}}
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-muted)">
                <span>Tampilkan</span>
                <select class="dt-select" style="min-width:60px" x-model="perPage" @change="fetchTeachers()">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Guru</th>
                        <th class="hide-sm">NIP</th>
                        <th class="hide-sm">Mata Pelajaran</th>
                        <th class="hide-sm">Tipe</th>
                        <th class="hide-sm">Status</th>
                        <th class="hide-sm">Tgl Bergabung</th>
                        <th class="col-act">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Skeleton --}}
                    <template x-if="loading">
                        <template x-for="i in perPage" :key="i">
                            <tr>
                                <td><div class="dt-skel w-6"></div></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="dt-skel" style="width:36px;height:36px;border-radius:50%;flex-shrink:0"></div>
                                        <div style="flex:1">
                                            <div class="dt-skel w-32" style="margin-bottom:5px"></div>
                                            <div class="dt-skel w-40"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-28"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-20"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-20"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data Rows --}}
                    <template x-if="!loading && teachers.length > 0">
                        <template x-for="(teacher, i) in teachers" :key="teacher.id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>

                                {{-- Avatar + Nama + Email --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <template x-if="teacher.profile_picture">
                                            <img :src="teacher.profile_picture" :alt="teacher.name"
                                                style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">
                                        </template>
                                        <template x-if="!teacher.profile_picture">
                                            <div class="dt-av" :class="avatarColor(i)"
                                                style="width:36px;height:36px;border-radius:50%;font-size:13px;flex-shrink:0"
                                                x-text="initials(teacher.name)"></div>
                                        </template>
                                        <div>
                                            <div class="dt-user-name" x-text="teacher.name || '—'"></div>
                                            <div class="dt-muted" style="font-size:11px;margin-top:2px"
                                                x-text="teacher.email || '—'"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- NIP --}}
                                <td class="hide-sm">
                                    <span class="dt-mono" style="font-size:12px"
                                        x-text="teacher.profile?.nip || '—'"></span>
                                </td>

                                {{-- Mata Pelajaran --}}
                                <td class="hide-sm">
                                    <template x-if="teacher.profile?.subject">
                                        <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:#F0FDF4;color:#15803D"
                                            x-text="teacher.profile.subject"></span>
                                    </template>
                                    <template x-if="!teacher.profile?.subject">
                                        <span class="dt-muted">—</span>
                                    </template>
                                </td>

                                {{-- Tipe Pegawai --}}
                                <td class="hide-sm">
                                    <template x-if="teacher.profile?.employee_type">
                                        <span x-text="{permanent:'PNS / Tetap', honorary:'Honorer', contract:'Kontrak'}[teacher.profile.employee_type] ?? teacher.profile.employee_type"
                                            style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600"
                                            :style="teacher.profile.employee_type === 'permanent'
                                                ? 'background:#EFF6FF;color:#1D4ED8'
                                                : teacher.profile.employee_type === 'honorary'
                                                ? 'background:#FFF7ED;color:#D97706'
                                                : 'background:#F5F3FF;color:#7C3AED'">
                                        </span>
                                    </template>
                                    <template x-if="!teacher.profile?.employee_type">
                                        <span class="dt-muted">—</span>
                                    </template>
                                </td>

                                {{-- Status --}}
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(teacher)"
                                        :title="teacher.status === 'active' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="teacher.status === 'active'
                                            ? 'background:#ECFDF5;color:#059669'
                                            : 'background:#FFF7ED;color:#D97706'">
                                        <span style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"
                                            :style="teacher.status === 'active' ? 'background:#10B981' : 'background:#D1D5DB'">
                                            <span style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"
                                                :style="teacher.status === 'active' ? 'transform:translateX(14px)' : 'transform:translateX(2px)'"></span>
                                        </span>
                                        <span x-text="teacher.status === 'active' ? 'Aktif' : 'Non-Aktif'"></span>
                                    </button>
                                </td>

                                {{-- Tgl Bergabung --}}
                                <td class="hide-sm dt-muted" style="font-size:12px"
                                    x-text="teacher.profile?.join_date
                                        ? new Date(teacher.profile.join_date).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})
                                        : '—'">
                                </td>

                                {{-- Aksi --}}
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act primary" title="Detail" @click="goToDetail(teacher.id)">
                                            <i class="ri-eye-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus" @click="deleteTeacher(teacher.id, teacher.name)">
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
            <div x-show="!loading && teachers.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-user-star-line"></i></div>
                <div class="dt-empty-title">Tidak ada data guru</div>
                <div class="dt-empty-sub">Coba ubah filter atau tambahkan guru baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="teachers.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + teachers.length"></strong>
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
</div>
@endsection