@extends('layouts.app')

@section('title', 'Daftar Tugas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-score.css') }}">
    <style>
        .badge-draft { background: #FEF9C3; color: #CA8A04; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .badge-published { background: #DCFCE7; color: #16A34A; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .badge-closed { background: #FEE2E2; color: #DC2626; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
    </style>
@endpush

@section('content')
<div
    x-data="teacherAssignmentIndex({
        indexUrl:        '{{ route('teacher.assignments.index') }}',
        storeUrl:        '{{ route('teacher.assignments.store') }}',
        gradeSubjectsUrl:'{{ route('teacher.assignments.grade-subjects') }}'
    })"
    x-init="init()"
>

    {{-- ── BREADCRUMB ────────────────────────────────────── --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Tugas</span></li>
        </ul>
    </div>

    {{-- ── PAGE HEADER ──────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2">
                Daftar Tugas
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Kelola tugas siswa, berikan deskripsi, attachment, dan batas waktu pengumpulan
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button @click="openCreateModal()" class="sc-action primary">
                <i class="ri-add-line"></i>
                Buat Tugas Baru
            </button>
        </div>
    </div>

    {{-- ── STAT STRIP ───────────────────────────────────── --}}
    <div class="sc-stats">
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#F5F3FF;color:#7C3AED"><i class="ri-file-list-3-line"></i></div>
            <div class="sc-stat-val">{{ $stats['total'] }}</div>
            <div class="sc-stat-label">Total Tugas</div>
            <div class="sc-stat-bar" style="background:#8B5CF6"></div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#F0FDF4;color:#15803D"><i class="ri-send-plane-line"></i></div>
            <div class="sc-stat-val">{{ $stats['published'] }}</div>
            <div class="sc-stat-label">Dipublikasikan</div>
            <div class="sc-stat-bar" style="background:#22C55E"></div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#FEF9C3;color:#CA8A04"><i class="ri-draft-line"></i></div>
            <div class="sc-stat-val">{{ $stats['draft'] }}</div>
            <div class="sc-stat-label">Draft</div>
            <div class="sc-stat-bar" style="background:#EAB308"></div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-icon" style="background:#FEE2E2;color:#DC2626"><i class="ri-lock-line"></i></div>
            <div class="sc-stat-val">{{ $stats['closed'] }}</div>
            <div class="sc-stat-label">Ditutup</div>
            <div class="sc-stat-bar" style="background:#EF4444"></div>
        </div>
    </div>

    {{-- ── DATATABLE ─────────────────────────────────────── --}}
    <div class="dt-wrap">
        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input x-model="search" @input.debounce.400ms="fetchAssignments()" type="text" placeholder="Cari judul..." class="dt-search-input"/>
                    <button x-show="search" @click="search=''; fetchAssignments()" class="dt-search-clear">×</button>
                </div>
                
                <div class="dt-filter">
                    <select x-model="statusFilter" @change="fetchAssignments()" class="dt-select">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="dt-table-wrapper">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th style="width:30%">Judul Tugas</th>
                        <th>Kelas & Mapel</th>
                        <th>Batas Waktu</th>
                        <th>Submissions</th>
                        <th>Status</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="loading">
                        <tr><td colspan="6" style="text-align:center;padding:40px"><div class="spinner" style="margin:auto"></div></td></tr>
                    </template>
                    <template x-if="!loading && assignments.length === 0">
                        <tr>
                            <td colspan="6" style="text-align:center;padding:60px 20px;color:var(--text-muted)">
                                <i class="ri-inbox-2-line" style="font-size:32px;opacity:0.5;display:block;margin-bottom:8px"></i>
                                Belum ada data tugas.
                            </td>
                        </tr>
                    </template>
                    <template x-for="item in assignments" :key="item.id">
                        <tr>
                            <td>
                                <div style="font-weight:600;color:var(--text-primary)" x-text="item.title"></div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px" x-text="'Dibuat: ' + formatDate(item.assigned_date)"></div>
                            </td>
                            <td>
                                <div x-text="item.grade?.grade_name" style="font-weight:500;"></div>
                                <div x-text="item.subject?.subject_name" style="font-size:12px;color:var(--text-muted);"></div>
                            </td>
                            <td>
                                <div x-text="formatDate(item.due_date)"></div>
                                <span x-show="isOverdue(item.due_date) && item.status !== 'closed'" style="font-size:11px;color:#DC2626;font-weight:500;">Terlewat</span>
                            </td>
                            <td>
                                <div style="font-size:13px;"><span x-text="item.graded_count"></span> / <span x-text="item.total_submissions"></span> Dinilai</div>
                            </td>
                            <td>
                                <span :class="'badge-' + item.status" x-text="item.status.toUpperCase()"></span>
                            </td>
                            <td style="text-align:right">
                                <button @click="toggleStatus(item.id)" class="dt-btn-icon" title="Toggle Status"><i class="ri-loop-left-line"></i></button>
                                <button @click="goToDetail(item.id)" class="dt-btn-icon" title="Lihat Detail"><i class="ri-eye-line"></i></button>
                                <button @click="deleteAssignment(item.id)" class="dt-btn-icon" style="color:var(--danger)" title="Hapus"><i class="ri-delete-bin-line"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="dt-pagination" x-show="!loading && meta.last_page > 1">
            <div class="dt-pagination-info">
                Menampilkan <span x-text="meta.from"></span> - <span x-text="meta.to"></span> dari <span x-text="meta.total"></span> data
            </div>
            <div class="dt-pagination-nav">
                <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page === 1" class="dt-page-btn"><i class="ri-arrow-left-s-line"></i></button>
                <div class="dt-page-numbers">
                    <template x-for="p in meta.last_page">
                        <button 
                            x-show="p === 1 || p === meta.last_page || Math.abs(p - meta.current_page) <= 1"
                            @click="changePage(p)" 
                            class="dt-page-btn" 
                            :class="p === meta.current_page ? 'active' : ''" 
                            x-text="p">
                        </button>
                    </template>
                </div>
                <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page" class="dt-page-btn"><i class="ri-arrow-right-s-line"></i></button>
            </div>
        </div>
    </div>

    {{-- ── MODAL BUAT TUGAS ──────────────────────────────── --}}
    <div class="modal-backdrop" x-show="showCreateModal" style="display:none" x-transition.opacity>
        <div class="modal-content" @click.away="closeCreateModal()" style="max-width:500px">
            <div class="modal-header">
                <h3 class="modal-title">Buat Tugas Baru</h3>
                <button @click="closeCreateModal()" class="modal-close"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body" style="padding:20px">
                <form id="createForm">
                    <div style="margin-bottom:15px">
                        <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">
                            Pilih Kelas & Mapel <span style="color:var(--danger)">*</span>
                        </label>
                        <select x-model="form.grade_subject_id" class="dt-select" style="width:100%;padding:8px 12px;height:auto">
                            <option value="">-- Pilih --</option>
                            <template x-for="gs in gradeSubjects" :key="gs.grade_id + '_' + gs.subject_id">
                                <option :value="gs.grade_id + '|' + gs.subject_id" x-text="gs.label"></option>
                            </template>
                        </select>
                        <span x-show="errors.grade_id || errors.subject_id" class="form-error" x-text="'Pilih kelas & mapel'" style="color:var(--danger);font-size:12px;margin-top:4px;display:block"></span>
                    </div>

                    <div style="margin-bottom:15px">
                        <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">Judul Tugas <span style="color:var(--danger)">*</span></label>
                        <input type="text" x-model="form.title" class="dt-search-input" style="width:100%;height:36px;border-radius:6px;padding:0 12px" placeholder="Contoh: Tugas Matriks 1">
                        <span x-show="errors.title" class="form-error" x-text="errors.title" style="color:var(--danger);font-size:12px;margin-top:4px;display:block"></span>
                    </div>

                    <div style="margin-bottom:15px">
                        <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">Deskripsi</label>
                        <textarea x-model="form.description" style="width:100%;min-height:80px;padding:8px 12px;border:1px solid var(--border-color);border-radius:6px;font-size:13px;resize:vertical" placeholder="Instruksi tambahan..."></textarea>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:15px">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">Tgl Diberikan <span style="color:var(--danger)">*</span></label>
                            <input type="date" x-model="form.assigned_date" class="dt-search-input" style="width:100%;height:36px;border-radius:6px;padding:0 12px">
                            <span x-show="errors.assigned_date" class="form-error" x-text="errors.assigned_date" style="color:var(--danger);font-size:12px;margin-top:4px;display:block"></span>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">Batas Waktu <span style="color:var(--danger)">*</span></label>
                            <input type="date" x-model="form.due_date" class="dt-search-input" style="width:100%;height:36px;border-radius:6px;padding:0 12px">
                            <span x-show="errors.due_date" class="form-error" x-text="errors.due_date" style="color:var(--danger);font-size:12px;margin-top:4px;display:block"></span>
                        </div>
                    </div>

                    <div style="margin-bottom:15px">
                        <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">Status Awal</label>
                        <select x-model="form.status" class="dt-select" style="width:100%;padding:8px 12px;height:auto">
                            <option value="draft">Draft (Belum tampil di siswa)</option>
                            <option value="published">Published (Tampil di siswa)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button @click="closeCreateModal()" class="sc-action" style="background:#fff;border:1px solid var(--border-color)">Batal</button>
                <button @click="submitCreate()" class="sc-action primary" :disabled="creating">
                    <span x-show="!creating">Simpan Tugas</span>
                    <span x-show="creating">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('teacherAssignmentIndex', (config) => ({
        indexUrl: config.indexUrl,
        storeUrl: config.storeUrl,
        gradeSubjectsUrl: config.gradeSubjectsUrl,
        
        assignments: [],
        meta: {},
        loading: false,
        search: '',
        statusFilter: '',
        
        gradeSubjects: [],
        loadingGradeSubjects: false,
        
        showCreateModal: false,
        creating: false,
        form: {},
        errors: {},

        init() {
            this.fetchAssignments();
            this.fetchGradeSubjects();
        },

        async fetchGradeSubjects() {
            this.loadingGradeSubjects = true;
            try {
                const res = await fetch(this.gradeSubjectsUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                this.gradeSubjects = await res.json();
            } catch { this.gradeSubjects = []; }
            finally { this.loadingGradeSubjects = false; }
        },

        async fetchAssignments(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    ...(this.search.trim() ? { search: this.search.trim() } : {}),
                    ...(this.statusFilter ? { status: this.statusFilter } : {}),
                });
                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json = await res.json();
                this.assignments = json.data;
                this.meta = json; 
            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchAssignments(page);
        },

        openCreateModal() {
            this.errors = {};
            this.form = {
                grade_subject_id: '',
                title: '',
                description: '',
                assigned_date: new Date().toISOString().split('T')[0],
                due_date: new Date().toISOString().split('T')[0],
                status: 'published'
            };
            this.showCreateModal = true;
        },

        closeCreateModal() {
            if (this.creating) return;
            this.showCreateModal = false;
            this.errors = {};
        },

        async submitCreate() {
            this.errors = {};
            
            if (!this.form.grade_subject_id) {
                this.errors.grade_id = 'Pilih kelas & mapel';
                return;
            }

            const [grade_id, subject_id] = this.form.grade_subject_id.split('|');

            this.creating = true;
            try {
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf(),
                    },
                    body: JSON.stringify({
                        ...this.form,
                        grade_id: grade_id,
                        subject_id: subject_id
                    }),
                });
                const json = await res.json();

                if (res.status === 422) {
                    const errs = json.errors ?? {};
                    this.errors = Object.fromEntries(
                        Object.entries(errs).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
                    );
                    return;
                }

                if (!res.ok) throw new Error(json.message ?? 'Gagal membuat tugas.');

                this.showCreateModal = false;
                this.toast('success', json.message);
                this.fetchAssignments(1);

            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.creating = false;
            }
        },

        async toggleStatus(id) {
            try {
                const res = await fetch(`${this.indexUrl}/${id}/status`, {
                    method: 'PATCH',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf(),
                    }
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message);
                this.toast('success', json.message);
                this.fetchAssignments(this.meta.current_page);
            } catch (err) {
                this.toast('error', err.message);
            }
        },

        async deleteAssignment(id) {
            if (!confirm('Yakin ingin menghapus tugas ini?')) return;
            try {
                const res = await fetch(`${this.indexUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf(),
                    }
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message);
                this.toast('success', json.message);
                this.fetchAssignments(1);
            } catch (err) {
                this.toast('error', err.message);
            }
        },

        goToDetail(id) { window.location.href = `${this.indexUrl}/${id}`; },

        formatDate(d) {
            if (!d) return '-';
            return new Date(d).toLocaleDateString('id-ID', { year:'numeric', month:'short', day:'numeric' });
        },

        isOverdue(d) {
            if (!d) return false;
            return new Date(d) < new Date();
        },

        csrf() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },

        toast(icon, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true, position: 'top-end',
                    icon, title: message,
                    showConfirmButton: false,
                    timer: 3500, timerProgressBar: true,
                });
            }
        },
    }));
});
</script>
@endpush
