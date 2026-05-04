@extends('layouts.app')

@section('title', 'Roles Management')

@section('content')
<div
    x-data="roleSearch({
        indexUrl:           '{{ route('roles.index') }}',
        storeUrl:           '{{ route('roles.store') }}',
        showUrl:            '{{ url('roles') }}',
        updateUrl:          '{{ url('roles') }}',
        destroyUrl:         '{{ url('roles') }}',
        toggleStatusUrl:    '{{ url('roles') }}',
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
                <li>Sistem</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>Roles</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Roles Management
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola semua role dan hak akses pengguna sistem
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Role
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
            <div class="stat-label">Total Role</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-user-follow-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Role Aktif</div>
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
                        @input.debounce.400ms="fetchRoles()"
                        type="text"
                        placeholder="Cari role..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchRoles()" class="dt-search-clear">×</button>
                </div>
                {{-- <div style="position:relative" @click.outside="filterOpen=false">
                    <button
                        @click="filterOpen = !filterOpen"
                        class="dt-select"
                        style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;min-width:148px;justify-content:space-between;padding:0 10px;height:36px">
                        <span style="display:flex;align-items:center;gap:6px">
                            <i class="ri-filter-3-line" style="font-size:14px;color:var(--text-muted)"></i>
                            <span x-text="filterLabel()" style="font-size:13px"></span>
                        </span>
                        <i class="ri-arrow-down-s-line"
                        style="font-size:14px;color:var(--text-muted);transition:transform .2s"
                        :style="filterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </button>

                    <div
                        x-show="filterOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        style="position:absolute;top:calc(100% + 6px);left:0;min-width:160px;background:var(--card);border:1px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:50;overflow:hidden"
                    >
                        <template x-for="opt in [
                                { label: 'Semua Status', value: '',         icon: 'ri-list-check-3' },
                                { label: 'Aktif',        value: 'active',   icon: 'ri-checkbox-circle-line' },
                                { label: 'Non-Aktif',    value: 'inactive', icon: 'ri-close-circle-line' },
                            ]" :key="opt.value">
                            <button
                                @click="setStatusFilter(opt.value)"
                                style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 14px;background:transparent;border:none;cursor:pointer;font-family:var(--font);font-size:13px;color:var(--text-primary);transition:background .15s"
                                :style="statusFilter === opt.value
                                    ? 'background:var(--primary-xlight);color:var(--primary);font-weight:600'
                                    : ''"
                            >
                                <i :class="opt.icon"
                                style="font-size:15px"
                                :style="opt.value === 'active'   ? 'color:#10B981' :
                                        opt.value === 'inactive' ? 'color:#F59E0B' :
                                        'color:var(--text-muted)'"></i>
                                <span x-text="opt.label"></span>
                                <i x-show="statusFilter === opt.value"
                                class="ri-check-line"
                                style="margin-left:auto;font-size:14px;color:var(--primary)"></i>
                            </button>
                        </template>
                    </div>
                </div> --}}
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
                <select x-model="perPage" @change="fetchRoles()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchRoles(meta.current_page)">
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
                        <th>Role Name</th>
                        <th class="hide-sm">Description</th>
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
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && roles.length > 0">
                        <template x-for="(role, i) in roles" :key="role.role_id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div class="dt-user">
                                        <div class="dt-av" :class="avatarColor(i)"
                                             x-text="initials(role.role_name)"></div>
                                        <div class="dt-user-name" x-text="role.role_name"></div>
                                    </div>
                                </td>
                                <td class="hide-sm dt-muted"
                                    x-text="role.role_description || '—'"></td>
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(role)"
                                        :title="role.status === 'active' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="role.status === 'active'
                                            ? 'background:#ECFDF5;color:#059669;'
                                            : 'background:#FFF7ED;color:#D97706;'"
                                    >
                                        {{-- Toggle pill ──────────────────────────────── --}}
                                        <span
                                            style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"
                                            :style="role.status === 'active' ? 'background:#10B981' : 'background:#D1D5DB'"
                                        >
                                            <span
                                                style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"
                                                :style="role.status === 'active' ? 'transform:translateX(14px)' : 'transform:translateX(2px)'"
                                            ></span>
                                        </span>
                                        <span 
                                            class="dt-badge"
                                            :class="role.status === 'active' ? 'aktif' : 'nonaktif'"
                                            x-text="role.status === 'active' ? 'Aktif' : 'Non-Aktif'">
                                        </span>
                                    </button>
                                </td>
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act" title="Edit"
                                             @click="openEditModal(role.role_id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus"
                                             @click="deleteRole(role.role_id, role.role_name)">
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
            <div x-show="!loading && roles.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-shield-keyhole-line"></i></div>
                <div class="dt-empty-title">Tidak ada data role</div>
                <div class="dt-empty-sub">Coba ubah kata kunci pencarian atau tambahkan role baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="roles.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + roles.length"
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
         FIX: Pakai class .modal-overlay & .modal-box (bukan inline display:flex)
              agar tidak konflik dengan Alpine x-show toggle
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
            >
                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-shield-keyhole-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Role' : 'Tambah Role'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi role' : 'Buat role baru untuk sistem'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div style="padding:20px 24px">
                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Role Name <span style="color:var(--danger)">*</span>
                        </label>
                        <input
                            x-model="form.role_name"
                            type="text"
                            placeholder="Contoh: super-admin"
                            style="width:100%;height:40px;padding:0 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;transition:all .18s"
                            :style="errors.role_name ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.role_name?'var(--danger)':'var(--border)';$el.style.background=errors.role_name?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                        />
                        <p x-show="errors.role_name" x-text="errors.role_name"
                           style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        <p x-show="!errors.role_name"
                           style="font-size:11.5px;color:var(--text-muted);margin-top:5px">
                            Gunakan format lowercase dengan tanda hubung
                        </p>
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Deskripsi
                        </label>
                        <textarea
                            x-model="form.role_description"
                            rows="3"
                            placeholder="Deskripsi role (opsional)"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;resize:none;transition:all .18s;min-height:80px;display:block"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                        ></textarea>
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