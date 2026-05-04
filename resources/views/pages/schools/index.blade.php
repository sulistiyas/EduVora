@extends('layouts.app')

@section('title', 'School Profiles Management')

@section('content')
<div
    x-data="schoolProfilesSearch({
        indexUrl:           '{{ route('school-management.index') }}',
        storeUrl:           '{{ route('school-management.store') }}',
        showUrl:            '{{ url('school-management') }}',
        updateUrl:          '{{ url('school-management') }}',
        destroyUrl:         '{{ url('school-management') }}',
        toggleStatusUrl:    '{{ url('school-management') }}',
    })"
    x-init="init()">
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
                <li>Sistem</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>School Management</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                School Management
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola sekolah di EduSaaS
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Sekolah
        </button>
    </div>

    {{-- ── STAT STRIP ──────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="ri-shield-keyhole-line"></i></div>
                <div class="stat-trend up"><i class="ri-arrow-up-s-line"></i> +2</div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Total Sekolah</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-user-follow-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Sekolah Aktif</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-group-line"></i></div>
                <div class="stat-trend flat">Total</div>
            </div>
            <div class="stat-value">{{ \App\Models\Core\User::count() }}</div>
            <div class="stat-label">Pengguna Terkelola</div>
        </div>
    </div>

    {{-- ── DATATABLE ────────────────────────────────────────────── --}}
    <div class="dt-wrap">

        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input
                        x-model="search"
                        @input.debounce.400ms="fetchSchools()"
                        type="text"
                        placeholder="Cari sekolah..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchSchools()" class="dt-search-clear">×</button>
                </div>
                <div style="position:relative" @click.outside="filterOpen=false">
                    <div
                        @click="filterOpen = !filterOpen"
                        class="dt-select"
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            min-width:160px;
                            cursor:pointer;
                        ">
                        <span x-text="filterLabel()"></span>

                        <i class="ri-arrow-down-s-line"
                            style="font-size:16px;transition:.2s"
                            :style="filterOpen ? 'transform:rotate(180deg)' : ''">
                        </i>
                    </div>
                    <div
                        x-show="filterOpen"
                        x-transition
                        style="
                            position:absolute;
                            top:calc(100% + 4px);
                            left:0;
                            width:100%;
                            background:var(--card);
                            border:1.5px solid var(--border);
                            border-radius:var(--radius-sm);
                            box-shadow:0 6px 16px rgba(0,0,0,.08);
                            overflow:hidden;
                            z-index:50;">
                        <template x-for="opt in [
                                { label: 'Semua Status', value: '',         icon: 'ri-list-check-3' },
                                { label: 'Aktif',        value: 'active',   icon: 'ri-checkbox-circle-line' },
                                { label: 'Non-Aktif',    value: 'inactive', icon: 'ri-close-circle-line' },
                            ]" :key="opt.value">

                            <div
                                @click="setStatusFilter(opt.value); filterOpen=false"
                                style="
                                    padding:9px 12px;
                                    font-size:13px;
                                    cursor:pointer;
                                    display:flex;
                                    justify-content:space-between;
                                    align-items:center;
                                "
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="statusFilter === opt.value
                                    ? 'background:var(--bg); font-weight:600'
                                    : ''">
                                <span x-text="opt.label"></span>

                                <i x-show="statusFilter === opt.value"
                                    class="ri-check-line"
                                    style="font-size:14px;color:var(--primary)">
                                </i>
                            </div>

                        </template>
                    </div>
                </div>
                <select x-model="perPage" @change="fetchSchools()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchSchools(meta.current_page)">
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
                        <th style="width:52px">Logo</th>
                        <th>Nama Sekolah</th>
                        <th class="hide-sm">NPSN</th>
                        <th class="hide-sm">Tipe</th>
                        <th class="hide-sm">Akreditasi</th>
                        <th class="hide-sm">Kota</th>
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
                                <td><div class="dt-skel-av" style="width:36px;height:36px;border-radius:8px"></div></td>
                                <td>
                                    <div style="flex:1">
                                        <div class="dt-skel w-3/4" style="margin-bottom:6px"></div>
                                        <div class="dt-skel w-1/2"></div>
                                    </div>
                                </td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && schools.length > 0">
                        <template x-for="(school, i) in schools" :key="school.school_id">
                            <tr>
                                {{-- No --}}
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>

                                {{-- Logo --}}
                                <td>
                                    <template x-if="school.logo_url">
                                        <img :src="school.logo_url" :alt="school.school_name"
                                            style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1px solid var(--border)">
                                    </template>
                                    <template x-if="!school.logo_url">
                                        <div class="dt-av" :class="avatarColor(i)"
                                            style="width:36px;height:36px;border-radius:8px;font-size:13px"
                                            x-text="initials(school.school_name)"></div>
                                    </template>
                                </td>

                                {{-- Nama Sekolah --}}
                                <td>
                                    <div class="dt-user-name" x-text="school.school_name"></div>
                                    <div class="dt-muted" style="font-size:11px;margin-top:2px"
                                        x-text="school.contact_email || '—'"></div>
                                </td>

                                {{-- NPSN --}}
                                <td class="hide-sm dt-mono" style="font-size:12px"
                                    x-text="school.npsn || '—'"></td>

                                {{-- Tipe --}}
                                <td class="hide-sm">
                                    <template x-if="school.school_type">
                                        <span :style="schoolTypeStyle(school.school_type)"
                                            style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600"
                                            x-text="school.school_type"></span>
                                    </template>
                                    <template x-if="!school.school_type">
                                        <span class="dt-muted">—</span>
                                    </template>
                                </td>

                                {{-- Akreditasi --}}
                                <td class="hide-sm">
                                    <template x-if="school.accreditation">
                                        <span :style="accreditationStyle(school.accreditation)"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;font-size:12px;font-weight:700"
                                            x-text="school.accreditation"></span>
                                    </template>
                                    <template x-if="!school.accreditation">
                                        <span class="dt-muted">—</span>
                                    </template>
                                </td>

                                {{-- Kota --}}
                                <td class="hide-sm dt-muted" x-text="school.city || '—'"></td>

                                {{-- Status --}}
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(school)"
                                        :title="school.status === 'active' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="school.status === 'active'
                                            ? 'background:#ECFDF5;color:#059669;'
                                            : 'background:#FFF7ED;color:#D97706;'"
                                    >
                                        <span
                                            style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"
                                            :style="school.status === 'active' ? 'background:#10B981' : 'background:#D1D5DB'"
                                        >
                                            <span
                                                style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"
                                                :style="school.status === 'active' ? 'transform:translateX(14px)' : 'transform:translateX(2px)'"
                                            ></span>
                                        </span>
                                        <span 
                                            class="dt-badge"
                                            :class="school.status === 'active' ? 'aktif' : 'nonaktif'"
                                            x-text="school.status === 'active' ? 'Aktif' : 'Non-Aktif'">
                                        </span>
                                    </button>
                                </td>

                                {{-- Aksi --}}
                                <td class="col-act">
                                    <div class="dt-actions">
                                        
                                        <!-- Detail -->
                                        <div class="dt-act primary" title="Detail"
                                            @click="goToDetail(school.school_id)">
                                            <i class="ri-eye-line"></i>
                                        </div>

                                        <!-- Edit -->
                                        <div class="dt-act warning" title="Edit"
                                            @click="openEditModal(school.school_id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>

                                        <!-- Delete -->
                                        <div class="dt-act danger" title="Hapus"
                                            @click="deleteSchool(school.school_id, school.school_name)">
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
            <div x-show="!loading && schools.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-shield-keyhole-line"></i></div>
                <div class="dt-empty-title">Tidak ada data sekolah</div>
                <div class="dt-empty-sub">Coba ubah kata kunci pencarian atau tambahkan sekolah baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="schools.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + schools.length"
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
            class="modal-overlay">
            <div
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="modal-box"
            >
                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-building-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Sekolah' : 'Tambah Sekolah'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi sekolah' : 'Daftarkan sekolah baru'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <div style="padding:24px;display:flex;flex-direction:column;gap:20px">

                        {{-- GRID 2 KOLOM --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

                            {{-- Nama Sekolah --}}
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="ri-building-2-line"></i>
                                    Nama Sekolah *
                                </label>
                                <input x-model="form.school_name" type="text" class="form-input"
                                    placeholder="SDN 01 Jakarta">
                                <div x-show="errors.school_name" class="form-error" x-text="errors.school_name"></div>
                            </div>

                            {{-- NPSN --}}
                            <div class="form-group">
                                <label class="form-label">NPSN</label>
                                <input x-model="form.npsn" type="text" class="form-input">
                            </div>

                            {{-- NSS --}}
                            <div class="form-group">
                                <label class="form-label">NSS</label>
                                <input x-model="form.nss" type="text" class="form-input">
                            </div>

                            {{-- Akreditasi --}}
                            <div class="form-group">
                                <label class="form-label">Akreditasi</label>
                                <select x-model="form.accreditation" class="form-input">
                                    <option value="">Pilih</option>
                                    <template x-for="a in ['A','B','C','D','E']">
                                        <option :value="a" x-text="a"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input x-model="form.contact_email" type="email" class="form-input">
                            </div>

                            {{-- Phone --}}
                            <div class="form-group">
                                <label class="form-label">No. Telepon</label>
                                <input x-model="form.contact_phone" type="text" class="form-input">
                            </div>

                            {{-- Website --}}
                            <div class="form-group">
                                <label class="form-label">Website</label>
                                <input x-model="form.website" type="text" class="form-input">
                            </div>

                            {{-- KKM --}}
                            <div class="form-group">
                                <label class="form-label">KKM Default</label>
                                <input x-model="form.kkm_default" type="number" class="form-input">
                            </div>

                            {{-- Provinsi --}}
                            <div class="form-group">
                                <label class="form-label">Provinsi</label>
                                <input x-model="form.province" type="text" class="form-input">
                            </div>

                            {{-- Kota --}}
                            <div class="form-group">
                                <label class="form-label">Kota</label>
                                <input x-model="form.city" type="text" class="form-input">
                            </div>

                            {{-- Kecamatan --}}
                            <div class="form-group">
                                <label class="form-label">Kecamatan</label>
                                <input x-model="form.district" type="text" class="form-input">
                            </div>

                            {{-- Kode Pos --}}
                            <div class="form-group">
                                <label class="form-label">Kode Pos</label>
                                <input x-model="form.postal_code" type="text" class="form-input">
                            </div>

                            {{-- Kepala Sekolah --}}
                            <div class="form-group">
                                <label class="form-label">Nama Kepala Sekolah</label>
                                <input x-model="form.headmaster_name" type="text" class="form-input">
                            </div>

                            {{-- NIP --}}
                            <div class="form-group">
                                <label class="form-label">NIP Kepala Sekolah</label>
                                <input x-model="form.headmaster_nip" type="text" class="form-input">
                            </div>

                        </div>

                        {{-- FULL WIDTH --}}
                        
                        {{-- Tipe Sekolah --}}
                        <div class="form-group">
                            <label class="form-label">
                                <i class="ri-school-line"></i>
                                Tipe Sekolah
                            </label>

                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <template x-for="type in [
                                    { value: 'Elementary', label: 'Elementary' },
                                    { value: 'Junior High', label: 'Junior High' },
                                    { value: 'Senior High', label: 'Senior High' },
                                ]">
                                    <button type="button"
                                        @click="form.school_type = (form.school_type === type.value ? '' : type.value)"
                                        class="form-chip"
                                        :class="form.school_type === type.value ? 'active' : ''">

                                        <i :class="form.school_type === type.value 
                                            ? 'ri-checkbox-circle-fill' 
                                            : 'ri-circle-line'"></i>

                                        <span x-text="type.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="form-group">
                            <label class="form-label">Alamat</label>
                            <textarea x-model="form.address"
                                class="form-input"
                                style="height:80px;padding:10px"></textarea>
                        </div>

                        {{-- Status --}}
                        <div class="form-group">
                            <label class="form-label">Status</label>

                            <div style="display:flex;gap:8px">
                                <button type="button"
                                    @click="form.status = 'active'"
                                    class="form-chip"
                                    :class="form.status === 'active' ? 'active' : ''">
                                    Aktif
                                </button>

                                <button type="button"
                                    @click="form.status = 'inactive'"
                                    class="form-chip"
                                    :class="form.status === 'inactive' ? 'active' : ''">
                                    Non Aktif
                                </button>
                            </div>
                        </div>

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
</div>
@endsection