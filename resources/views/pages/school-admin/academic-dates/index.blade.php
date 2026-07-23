@extends('layouts.app')

@section('title', 'Tanggal Penting')

@section('content')
<div
    x-data="academicDateIndex({
        indexUrl:  '{{ route('academic-dates.index') }}',
        storeUrl:  '{{ route('academic-dates.store') }}',
        updateUrl: '{{ url('academic-dates') }}',
        destroyUrl:'{{ url('academic-dates') }}',
    })"
    x-init="init()"
>
    {{-- BREADCRUMB --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li>Akademik</li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Tanggal Penting</span></li>
        </ul>
    </div>

    {{-- PAGE HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
        <div>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2">
                Tanggal Penting
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola hari libur, ujian, acara, dan tenggat penting sekolah
            </p>
        </div>
        <button @click="openCreateModal()" class="dt-btn dt-btn-primary">
            <i class="ri-add-line"></i> Tambah Tanggal
        </button>
    </div>

    {{-- DATATABLE --}}
    <div class="dt-wrap">
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input x-model="search" @input.debounce.400ms="fetchDates()" type="text" placeholder="Cari judul..." class="dt-search-input" />
                    <button x-show="search" @click="search=''; fetchDates()" class="dt-search-clear">×</button>
                </div>

                <select x-model="typeFilter" @change="fetchDates()" class="dt-select">
                    <option value="">Semua Tipe</option>
                    <option value="holiday">Libur</option>
                    <option value="exam">Ujian</option>
                    <option value="event">Acara</option>
                    <option value="deadline">Batas Waktu</option>
                    <option value="other">Lainnya</option>
                </select>

                <select x-model="statusFilter" @change="fetchDates()" class="dt-select">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Non-Aktif</option>
                </select>

                <select x-model="perPage" @change="fetchDates()" class="dt-select">
                    <option value="10">10 / hal</option>
                    <option value="15">15 / hal</option>
                    <option value="25">25 / hal</option>
                </select>
            </div>

            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" @click="fetchDates(meta.current_page)" title="Refresh">
                    <i class="ri-refresh-line"></i>
                </button>
            </div>
        </div>

        <div class="ta-table-wrap">
            <table class="ta-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Tanggal</th>
                        <th class="hide-sm">Semester</th>
                        <th class="hide-sm">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <template x-for="i in 5" :key="i">
                            <tr>
                                <td><div class="dt-skel w-24" style="margin:0 auto"></div></td>
                                <td><div class="dt-skel" style="width:140px"></div></td>
                                <td><div class="dt-skel" style="width:80px"></div></td>
                                <td><div class="dt-skel" style="width:140px"></div></td>
                                <td class="hide-sm"><div class="dt-skel" style="width:80px"></div></td>
                                <td class="hide-sm"><div class="dt-skel" style="width:60px"></div></td>
                                <td><div class="dt-skel" style="width:60px"></div></td>
                            </tr>
                        </template>
                    </template>

                    <template x-if="!loading && dates.length > 0">
                        <template x-for="(d, i) in dates" :key="d.academic_date_id">
                            <tr>
                                <td class="col-no dt-mono" x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary)" x-text="d.title"></div>
                                    <div x-show="d.description" class="dt-muted" style="font-size:11px;margin-top:2px;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" x-text="d.description"></div>
                                </td>
                                <td>
                                    <span class="ta-badge" :class="typeBadgeClass(d.type)">
                                        <span x-text="d.type_label"></span>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size:13px;font-weight:500;color:var(--text-primary);white-space:nowrap" x-text="d.duration_label"></div>
                                </td>
                                <td class="hide-sm dt-muted" style="font-size:12.5px" x-text="d.semester_name ?? '—'"></td>
                                <td class="hide-sm">
                                    <span class="ta-badge" :class="d.status === 'active' ? 'ta-badge-approved' : 'ta-badge-draft'">
                                        <span x-text="d.status === 'active' ? 'Aktif' : 'Non-Aktif'"></span>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:4px">
                                        <button @click="openEditModal(d)" class="dt-icon-btn" title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button @click="confirmDelete(d)" class="dt-icon-btn" title="Hapus" style="color:var(--danger)">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>

            <div x-show="!loading && dates.length === 0" class="ta-empty">
                <div class="ta-empty-icon"><i class="ri-calendar-event-line"></i></div>
                <div class="ta-empty-title">Belum ada tanggal penting</div>
                <div class="ta-empty-sub">Klik "Tambah Tanggal" untuk menambahkan hari libur, ujian, atau acara</div>
            </div>
        </div>

        <div class="dt-footer" x-show="!loading && meta.total > 0">
            <div class="dt-info">
                Menampilkan
                <strong x-text="dates.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + dates.length"
                ></strong>
                dari <strong x-text="meta.total"></strong> tanggal
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
    </div>

    {{-- ── MODAL CREATE / EDIT ─────────────────────────────────── --}}
    <template x-if="showModal">
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px"
             @keydown.escape.window="closeModal()">
            <div style="position:absolute;inset:0;background:rgba(0,0,0,.4);backdrop-filter:blur(4px)" @click="closeModal()"></div>
            <div style="position:relative;width:100%;max-width:540px;background:var(--card);border-radius:16px;box-shadow:0 25px 60px rgba(0,0,0,.25);display:flex;flex-direction:column;max-height:90vh;overflow:hidden">

                <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:40px;height:40px;border-radius:10px;background:#FEF3C7;display:grid;place-items:center;flex-shrink:0">
                            <i class="ri-calendar-event-line" style="font-size:18px;color:#D97706"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:var(--text-primary);letter-spacing:-.3px"
                                 x-text="isEditing ? 'Edit Tanggal Penting' : 'Tambah Tanggal Penting'"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px"
                                 x-text="isEditing ? 'Perbarui informasi tanggal' : 'Tambahkan hari libur, ujian, atau acara'"></div>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);display:grid;place-items:center;cursor:pointer;color:var(--text-muted)">
                        <i class="ri-close-line" style="font-size:16px"></i>
                    </button>
                </div>

                <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px;max-height:65vh;overflow-y:auto">

                    {{-- Title --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Judul <span style="color:var(--danger)">*</span>
                        </label>
                        <input x-model="form.title" type="text" placeholder="Contoh: Libur Nasional" class="form-input"
                            :style="errors.title ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.title?'var(--danger)':'var(--border)';$el.style.background=errors.title?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                        />
                        <p x-show="errors.title" x-text="errors.title" style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Description --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Deskripsi <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                        </label>
                        <textarea x-model="form.description" rows="2" placeholder="Keterangan singkat..." class="form-input"
                            style="resize:vertical"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                        ></textarea>
                    </div>

                    {{-- Type --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Tipe <span style="color:var(--danger)">*</span>
                        </label>
                        <select x-model="form.type" class="form-input"
                            :style="errors.type ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor=errors.type?'var(--danger)':'var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="holiday">Libur</option>
                            <option value="exam">Ujian</option>
                            <option value="event">Acara</option>
                            <option value="deadline">Batas Waktu</option>
                            <option value="other">Lainnya</option>
                        </select>
                        <p x-show="errors.type" x-text="errors.type" style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                    </div>

                    {{-- Dates --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Tanggal Mulai <span style="color:var(--danger)">*</span>
                            </label>
                            <input x-model="form.start_date" type="date" class="form-input"
                                :style="errors.start_date ? 'border-color:var(--danger);background:#FFF5F5' : ''"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor=errors.start_date?'var(--danger)':'var(--border)';$el.style.background=errors.start_date?'#FFF5F5':'var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p x-show="errors.start_date" x-text="errors.start_date" style="font-size:11.5px;color:var(--danger);margin-top:5px"></p>
                        </div>
                        <div class="form-group">
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                                Tanggal Selesai <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                            </label>
                            <input x-model="form.end_date" type="date" class="form-input"
                                @focus="$el.style.borderColor='var(--primary)';$el.style.background='var(--card)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                                @blur="$el.style.borderColor='var(--border)';$el.style.background='var(--bg)';$el.style.boxShadow='none'"
                            />
                            <p style="font-size:11px;color:var(--text-muted);margin-top:4px">Kosongkan jika hanya 1 hari</p>
                        </div>
                    </div>

                    {{-- Semester --}}
                    <div class="form-group">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">
                            Semester <span style="font-size:11px;font-weight:400;color:var(--text-muted)">(opsional)</span>
                        </label>
                        <select x-model="form.semester_id" class="form-input"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="">Tanpa Semester</option>
                            <template x-for="sem in semesters" :key="sem.semester_id">
                                <option :value="sem.semester_id" x-text="sem.semester_name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Status (edit only) --}}
                    <div x-show="isEditing">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-secondary);margin-bottom:6px">Status</label>
                        <select x-model="form.status" class="form-input"
                            @focus="$el.style.borderColor='var(--primary)';$el.style.boxShadow='0 0 0 3px rgba(59,130,246,.1)'"
                            @blur="$el.style.borderColor='var(--border)';$el.style.boxShadow='none'"
                        >
                            <option value="active">Aktif</option>
                            <option value="inactive">Non-Aktif</option>
                        </select>
                    </div>
                </div>

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

@push('scripts')
<script>
function academicDateIndex(config = {}) {
    return {
        indexUrl:  config.indexUrl,
        storeUrl:  config.storeUrl,
        updateUrl: config.updateUrl,
        destroyUrl: config.destroyUrl,

        dates:      [],
        meta:       { current_page: 1, per_page: 15, total: 0, last_page: 1 },
        loading:    false,
        search:     '',
        perPage:    15,
        typeFilter: '',
        statusFilter: '',
        semesters:  [],

        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        errors:     {},

        form: { title: '', description: '', start_date: '', end_date: '', type: 'other', semester_id: '', status: 'active' },

        async init() {
            await this.fetchSemesters();
            this.fetchDates();
        },

        async fetchSemesters() {
            try {
                const res = await fetch('{{ route("school-admin.attendance.semesters") }}', {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) this.semesters = await res.json();
            } catch { this.semesters = []; }
        },

        async fetchDates(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage),
                    ...(this.search.trim() ? { search: this.search.trim() } : {}),
                    ...(this.typeFilter   ? { type: this.typeFilter } : {}),
                    ...(this.statusFilter ? { status: this.statusFilter } : {}),
                });
                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json = await res.json();
                this.dates = json.data;
                this.meta = json.meta;
            } catch (err) {
                Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.message, showConfirmButton:false, timer:3500 });
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchDates(page);
        },

        openCreateModal() {
            this.isEditing = false;
            this.editingId = null;
            this.errors = {};
            this.form = { title: '', description: '', start_date: '', end_date: '', type: 'other', semester_id: '', status: 'active' };
            this.showModal = true;
        },

        openEditModal(d) {
            this.isEditing = true;
            this.editingId = d.academic_date_id;
            this.errors = {};
            this.form = {
                title:       d.title,
                description: d.description ?? '',
                start_date:  d.start_date,
                end_date:    d.end_date ?? '',
                type:        d.type,
                semester_id: d.semester_id ?? '',
                status:      d.status,
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.errors = {};
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};
            try {
                const url    = this.isEditing ? `${this.updateUrl}/${this.editingId}` : this.storeUrl;
                const method = this.isEditing ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(this.form),
                });
                const json = await res.json();
                if (!res.ok) {
                    if (json.errors) { this.errors = json.errors; return; }
                    throw new Error(json.message || 'Gagal menyimpan.');
                }
                Swal.fire({ toast:true, position:'top-end', icon:'success', title: json.message, showConfirmButton:false, timer:3000 });
                this.closeModal();
                this.fetchDates(this.meta.current_page);
            } catch (err) {
                Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.message, showConfirmButton:false, timer:3500 });
            } finally {
                this.submitting = false;
            }
        },

        confirmDelete(d) {
            Swal.fire({
                title: 'Hapus Tanggal?',
                text: `"${d.title}" akan dihapus secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(async (result) => {
                if (!result.isConfirmed) return;
                try {
                    const res = await fetch(`${this.destroyUrl}/${d.academic_date_id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal menghapus.');
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title: json.message, showConfirmButton:false, timer:3000 });
                    this.fetchDates(this.meta.current_page);
                } catch (err) {
                    Swal.fire({ toast:true, position:'top-end', icon:'error', title: err.message, showConfirmButton:false, timer:3500 });
                }
            });
        },

        typeBadgeClass(type) {
            return {
                holiday:  'ta-badge-approved',
                exam:     'ta-badge-submitted',
                event:    'ta-badge-draft',
                deadline: 'ta-badge-locked',
                other:    '',
            }[type] ?? '';
        },
    };
}
</script>
@endpush
