@extends('layouts.app')

@section('title', 'Rooms Management')

@section('content')
<div
    x-data="roomsSearch({
        indexUrl:        '{{ route('rooms.index') }}',
        storeUrl:        '{{ route('rooms.store') }}',
        showUrl:         '{{ url('rooms') }}',
        updateUrl:       '{{ url('rooms') }}',
        destroyUrl:      '{{ url('rooms') }}',
        toggleStatusUrl: '{{ url('rooms') }}',
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
                <li>Sekolah</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Kelas</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>Ruangan</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Rooms Management
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola semua ruangan kelas dalam sekolah
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Ruangan
        </button>
    </div>

    {{-- ── STAT STRIP ──────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="ri-door-open-line"></i></div>
                <div class="stat-trend up"><i class="ri-arrow-up-s-line"></i></div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Total Ruangan</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="rooms.filter(r => r.status === 'active').length || '—'">—</div>
            <div class="stat-label">Ruangan Aktif</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-community-line"></i></div>
                <div class="stat-trend flat">Total</div>
            </div>
            <div class="stat-value" x-text="rooms.reduce((sum, r) => sum + (Number(r.capacity) || 0), 0) || '—'">—</div>
            <div class="stat-label">Total Kapasitas</div>
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
                        @input.debounce.400ms="fetchRooms()"
                        type="text"
                        placeholder="Cari ruangan..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchRooms()" class="dt-search-clear">×</button>
                </div>

                {{-- Filter Type --}}
                <div style="position:relative" @click.outside="typeFilterOpen=false">
                    <div
                        @click="typeFilterOpen = !typeFilterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:160px;cursor:pointer;">
                        <span style="font-size:13px" x-text="typeFilter ? typeFilter : 'Semua Tipe'"></span>
                        <i class="ri-arrow-down-s-line"
                            style="font-size:16px;transition:.2s"
                            :style="typeFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="typeFilterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;min-width:180px;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;">
                        <template x-for="opt in [
                                { label: 'Semua Tipe', value: '' },
                                { label: 'Classroom', value: 'kelas' },
                                { label: 'Lab', value: 'lab' },
                                { label: 'Library', value: 'library' },
                                { label: 'Office', value: 'office' },
                                { label: 'Sport', value: 'sport' },
                            ]" :key="opt.value">
                            <div
                                @click="setTypeFilter(opt.value)"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="typeFilter === opt.value ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="typeFilter === opt.value"
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

                <select x-model="perPage" @change="fetchRooms()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchRooms(meta.current_page)">
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
                        <th>Nama Ruangan</th>
                        <th class="hide-sm">Kode</th>
                        <th class="hide-sm">Tipe</th>
                        <th class="hide-sm">Lantai / Gedung</th>
                        <th class="hide-sm">Kapasitas</th>
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
                                <td class="hide-sm"><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && rooms.length > 0">
                        <template x-for="(room, i) in rooms" :key="room.room_id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div class="dt-user">
                                        <div class="dt-av" :class="avatarColor(i)"
                                             x-text="initials(room.room_name)"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="room.room_name"></div>
                                            <div class="dt-muted" style="font-size:11.5px" x-text="room.facilitiy || '—'"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    <span style="font-family:monospace;font-size:12px;background:var(--bg);padding:2px 8px;border-radius:4px;border:1px solid var(--border)"
                                          x-text="room.code || '—'"></span>
                                </td>
                                <td class="hide-sm dt-muted" x-text="room.type || '—'"></td>
                                <td class="hide-sm dt-muted">
                                    <span x-text="room.floor ? 'Lt. ' + room.floor : '—'"></span>
                                    <span x-show="room.building" x-text="' / ' + room.building" style="color:var(--text-muted)"></span>
                                </td>
                                <td class="hide-sm">
                                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:13px;font-weight:600;color:var(--text-primary)">
                                        <i class="ri-group-line" style="font-size:14px;color:var(--text-muted)"></i>
                                        <span x-text="room.capacity || '—'"></span>
                                    </span>
                                </td>
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(room)"
                                        :title="{
                                            available: 'Ubah ke Maintenance',
                                            maintenance: 'Ubah ke Non-Aktif',
                                            inactive: 'Ubah ke Tersedia'
                                        }[room.status]"

                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"

                                        :style="{
                                            available: 'background:#ECFDF5;color:#059669;',
                                            maintenance: 'background:#FEF3C7;color:#D97706;',
                                            inactive: 'background:#FEF2F2;color:#DC2626;'
                                        }[room.status]"
                                    >
                                        <span
                                            style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"

                                            :style="{
                                                available: 'background:#10B981',
                                                maintenance: 'background:#F59E0B',
                                                inactive: 'background:#D1D5DB'
                                            }[room.status]"
                                        >
                                            <span
                                                style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"

                                                :style="{
                                                    available: 'transform:translateX(14px)',
                                                    maintenance: 'transform:translateX(8px)',
                                                    inactive: 'transform:translateX(2px)'
                                                }[room.status]"
                                            ></span>
                                        </span>

                                        <span
                                            class="dt-badge"
                                            x-text="{
                                                available: 'Tersedia',
                                                maintenance: 'Maintenance',
                                                inactive: 'Non-Aktif'
                                            }[room.status]"
                                        >
                                        </span>
                                    </button>
                                </td>
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act" title="Edit"
                                             @click="openEditModal(room.room_id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus"
                                             @click="deleteRoom(room.room_id, room.room_name)">
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
            <div x-show="!loading && rooms.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-door-open-line"></i></div>
                <div class="dt-empty-title">Tidak ada data ruangan</div>
                <div class="dt-empty-sub">Coba ubah kata kunci pencarian atau tambahkan ruangan baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="rooms.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + rooms.length"
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
                            <i class="ri-door-open-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Ruangan' : 'Tambah Ruangan'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi ruangan' : 'Tambah ruangan baru ke dalam sekolah'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px;max-height:65vh;overflow-y:auto">

                    {{-- Row: Nama & Kode --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Nama Ruangan <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.room_name"
                                type="text"
                                placeholder="Contoh: Ruang 1A"
                                class="form-input"
                                :style="errors.room_name ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.room_name?'var(--danger)':'var(--border)';$el.style.background=errors.room_name?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p x-show="errors.room_name" x-text="errors.room_name"
                               style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Kode <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.code"
                                type="text"
                                placeholder="Contoh: R-1A"
                                class="form-input"
                                :style="errors.code ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.code?'var(--danger)':'var(--border)';$el.style.background=errors.code?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p x-show="errors.code" x-text="errors.code"
                               style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        </div>
                    </div>

                    {{-- Tipe --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Tipe Ruangan <span style="color:var(--danger)">*</span>
                        </label>
                        <select
                            x-model="form.type"
                            class="form-input"
                            :style="errors.type ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.type?'var(--danger)':'var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="">-- Pilih Tipe --</option>
                            <option value="classroom">Classroom</option>
                            <option value="lab">Lab</option>
                            <option value="library">Library</option>
                            <option value="office">Office</option>
                            <option value="sport">Sport</option>
                        </select>
                        <p x-show="errors.type" x-text="errors.type"
                           style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Row: Lantai & Gedung --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Lantai <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                            </label>
                            <input
                                x-model="form.floor"
                                type="number"
                                min="1"
                                placeholder="Contoh: 1"
                                class="form-input"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                            />
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Gedung <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                            </label>
                            <input
                                x-model="form.building"
                                type="text"
                                placeholder="Contoh: Gedung A"
                                class="form-input"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                            />
                        </div>
                    </div>

                    {{-- Kapasitas --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Kapasitas <span style="color:var(--danger)">*</span>
                        </label>
                        <input
                            x-model="form.capacity"
                            type="number"
                            min="1"
                            placeholder="Contoh: 32"
                            class="form-input"
                            :style="errors.capacity ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.capacity?'var(--danger)':'var(--border)';$el.style.background=errors.capacity?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                        />
                        <p x-show="errors.capacity" x-text="errors.capacity"
                           style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Fasilitas --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Fasilitas <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                        </label>
                        <input
                            x-model="form.facilitiy"
                            type="text"
                            placeholder="Contoh: AC, Proyektor, Whiteboard"
                            class="form-input"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                        />
                        <p style="font-size:11px;color:var(--text-muted);margin-top:4px">Pisahkan dengan koma jika lebih dari satu</p>
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
                            <option value="available">Tersedia</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Non-Aktif</option>
                        </select>
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