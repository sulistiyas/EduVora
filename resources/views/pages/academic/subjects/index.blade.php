@extends('layouts.app')

@section('title', 'Subject Management')

@section('content')
<div
    x-data="subjectSearch({
        indexUrl:           '{{ route('subjects.index') }}',
        storeUrl:           '{{ route('subjects.store') }}',
        showUrl:            '{{ url('subjects') }}',
        updateUrl:          '{{ url('subjects') }}',
        destroyUrl:         '{{ url('subjects') }}',
        toggleStatusUrl:    '{{ url('subjects') }}',
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
                <li><span>Mata Pelajaran</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Subject Management
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola semua mata pelajaran yang tersedia dalam sistem
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Mata Pelajaran
        </button>
    </div>

    {{-- ── STAT STRIP ──────────────────────────────────────────── --}}
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue"><i class="ri-book-open-line"></i></div>
                <div class="stat-trend up"><i class="ri-arrow-up-s-line"></i></div>
            </div>
            <div class="stat-value" x-text="meta.total || '—'">—</div>
            <div class="stat-label">Total Mata Pelajaran</div>
        </div>
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green"><i class="ri-checkbox-circle-line"></i></div>
                <div class="stat-trend up">Aktif</div>
            </div>
            <div class="stat-value" x-text="activeCount || '—'">—</div>
            <div class="stat-label">Mata Pelajaran Aktif</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-header">
                <div class="stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-stack-line"></i></div>
                <div class="stat-trend flat">Total</div>
            </div>
            <div class="stat-value" x-text="totalCredits || '—'">—</div>
            <div class="stat-label">Total SKS</div>
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
                        @input.debounce.400ms="fetchSubjects()"
                        type="text"
                        placeholder="Cari mata pelajaran..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchSubjects()" class="dt-search-clear">×</button>
                </div>

                {{-- Status Filter Dropdown --}}
                <div style="position:relative" @click.outside="filterOpen=false">
                    <div
                        @click="filterOpen = !filterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:160px;cursor:pointer;">
                        <span x-text="filterLabel()"></span>
                        <i class="ri-arrow-down-s-line"
                            style="font-size:16px;transition:.2s"
                            :style="filterOpen ? 'transform:rotate(180deg)' : ''">
                        </i>
                    </div>
                    <div
                        x-show="filterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50;">
                        <template x-for="opt in [
                                { label: 'Semua Status', value: '',         icon: 'ri-list-check-3' },
                                { label: 'Aktif',        value: 'active',   icon: 'ri-checkbox-circle-line' },
                                { label: 'Non-Aktif',    value: 'inactive', icon: 'ri-close-circle-line' },
                            ]" :key="opt.value">
                            <div
                                @click="setStatusFilter(opt.value); filterOpen=false"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="statusFilter === opt.value ? 'background:var(--bg); font-weight:600' : ''">
                                <span x-text="opt.label"></span>
                                <i x-show="statusFilter === opt.value"
                                    class="ri-check-line"
                                    style="font-size:14px;color:var(--primary)">
                                </i>
                            </div>
                        </template>
                    </div>
                </div>

                <select x-model="perPage" @change="fetchSubjects()" class="dt-select">
                    <option value="5">5 / halaman</option>
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchSubjects(meta.current_page)">
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
                        <th>Mata Pelajaran</th>
                        <th class="hide-sm">Kode</th>
                        <th class="hide-sm">Kategori</th>
                        <th class="hide-sm">SKS / Jam</th>
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
                                <td class="hide-sm"><div class="dt-skel w-1/2"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="col-act"><div class="dt-skel" style="width:60px;margin:0 auto"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && subjects.length > 0">
                        <template x-for="(subject, i) in subjects" :key="subject.id">
                            <tr>
                                <td class="col-no dt-mono"
                                    x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div class="dt-user">
                                        <div class="dt-av" :class="avatarColor(i)"
                                             x-text="initials(subject.subject_name)"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="subject.subject_name"></div>
                                            <div class="dt-muted" style="font-size:11.5px" x-text="subject.description || '—'"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hide-sm">
                                    <span style="font-family:monospace;font-size:12px;background:var(--bg);border:1px solid var(--border);padding:2px 8px;border-radius:4px"
                                          x-text="subject.subject_code || '—'"></span>
                                </td>
                                <td class="hide-sm dt-muted" x-text="subject.category || '—'"></td>
                                <td class="hide-sm dt-muted">
                                    <span x-text="(subject.credits || '—') + ' SKS'"></span>
                                    <span style="color:var(--border)"> / </span>
                                    <span x-text="(subject.hours_per_week || '—') + ' Jam'"></span>
                                </td>
                                <td class="hide-sm">
                                    <button
                                        @click="toggleStatus(subject)"
                                        :title="subject.status === 'active' ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan'"
                                        style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px 5px 8px;border-radius:999px;border:none;cursor:pointer;font-size:12px;font-weight:600;font-family:var(--font);transition:all .2s"
                                        :style="subject.status === 'active'
                                            ? 'background:#ECFDF5;color:#059669;'
                                            : 'background:#FFF7ED;color:#D97706;'"
                                    >
                                        <span
                                            style="position:relative;display:inline-block;width:28px;height:16px;border-radius:999px;transition:background .25s;flex-shrink:0"
                                            :style="subject.status === 'active' ? 'background:#10B981' : 'background:#D1D5DB'"
                                        >
                                            <span
                                                style="position:absolute;top:2px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s"
                                                :style="subject.status === 'active' ? 'transform:translateX(14px)' : 'transform:translateX(2px)'"
                                            ></span>
                                        </span>
                                        <span
                                            class="dt-badge"
                                            :class="subject.status === 'active' ? 'aktif' : 'nonaktif'"
                                            x-text="subject.status === 'active' ? 'Aktif' : 'Non-Aktif'">
                                        </span>
                                    </button>
                                </td>
                                <td class="col-act">
                                    <div class="dt-actions">
                                        <div class="dt-act" title="Edit"
                                             @click="openEditModal(subject.id)">
                                            <i class="ri-pencil-line"></i>
                                        </div>
                                        <div class="dt-act danger" title="Hapus"
                                             @click="deleteSubject(subject.id, subject.subject_name)">
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
            <div x-show="!loading && subjects.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-book-open-line"></i></div>
                <div class="dt-empty-title">Tidak ada data mata pelajaran</div>
                <div class="dt-empty-sub">Coba ubah kata kunci pencarian atau tambahkan mata pelajaran baru</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="subjects.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + subjects.length"
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
            >
                {{-- Header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:var(--primary-xlight);display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-book-open-line" style="font-size:18px;color:var(--primary)"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi mata pelajaran' : 'Tambahkan mata pelajaran baru ke sistem'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px">

                    {{-- Subject Name --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Nama Mata Pelajaran <span style="color:var(--danger)">*</span>
                        </label>
                        <input
                            x-model="form.subject_name"
                            type="text"
                            placeholder="Contoh: Matematika"
                            class="form-input"
                            :style="errors.subject_name ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.subject_name?'var(--danger)':'var(--border)';$el.style.background=errors.subject_name?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                        />
                        <p x-show="errors.subject_name" x-text="errors.subject_name"
                           style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Subject Code & Category (side by side) --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Kode Mata Pelajaran <span style="color:var(--danger)">*</span>
                            </label>
                            <input
                                x-model="form.subject_code"
                                type="text"
                                placeholder="Contoh: MTK"
                                class="form-input"
                                :style="errors.subject_code ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.subject_code?'var(--danger)':'var(--border)';$el.style.background=errors.subject_code?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p x-show="errors.subject_code" x-text="errors.subject_code"
                               style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Kategori
                            </label>
                            <input
                                x-model="form.category"
                                type="text"
                                placeholder="Contoh: Wajib / Pilihan"
                                class="form-input"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                            />
                        </div>
                    </div>

                    {{-- Credits & Hours per Week (side by side) --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                SKS (Credits)
                            </label>
                            <input
                                x-model="form.credits"
                                type="number"
                                min="0"
                                placeholder="Contoh: 3"
                                class="form-input"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                            />
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Jam per Minggu
                            </label>
                            <input
                                x-model="form.hours_per_week"
                                type="number"
                                min="0"
                                placeholder="Contoh: 4"
                                class="form-input"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                            />
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Deskripsi
                        </label>
                        <textarea
                            x-model="form.description"
                            rows="3"
                            placeholder="Deskripsi singkat mata pelajaran (opsional)"
                            class="form-input"
                            style="resize:vertical;min-height:80px;padding-top:10px;padding-bottom:10px"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                        ></textarea>
                    </div>

                    {{-- Status (Edit only) --}}
                    <div x-show="isEditing">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Status
                        </label>
                        <select
                            x-model="form.status"
                            style="width:100%;height:40px;padding:0 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);background:var(--bg);font-family:var(--font);font-size:13.5px;color:var(--text-primary);outline:none;transition:all .18s;cursor:pointer"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="inactive">Non-Aktif</option>
                            <option value="active">Aktif</option>
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