@extends('layouts.app')

@section('title', 'Tugas Saya')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-score.css') }}">
    <style>
        .badge-pending { background: #FEE2E2; color: #DC2626; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .badge-submitted { background: #FEF9C3; color: #CA8A04; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .badge-graded { background: #DCFCE7; color: #16A34A; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
    </style>
@endpush

@section('content')
<div x-data="studentAssignmentIndex({
        indexUrl: '{{ route('student.assignments.index') }}'
    })"
    x-init="init()"
>
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Tugas Saya</span></li>
        </ul>
    </div>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2">
                Tugas Saya
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Daftar tugas yang harus dikerjakan
            </p>
        </div>
    </div>

    <div class="dt-wrap">
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input x-model="search" @input.debounce.400ms="fetchAssignments()" type="text" placeholder="Cari judul tugas..." class="dt-search-input"/>
                    <button x-show="search" @click="search=''; fetchAssignments()" class="dt-search-clear">×</button>
                </div>
                
                <div class="dt-filter">
                    <select x-model="statusFilter" @change="fetchAssignments()" class="dt-select">
                        <option value="">Semua Status</option>
                        <option value="pending">Belum Dikumpul</option>
                        <option value="submitted">Terkumpul</option>
                        <option value="graded">Dinilai</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="dt-table-wrapper">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th style="width:30%">Judul Tugas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Batas Waktu</th>
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
                                Belum ada tugas untuk Anda.
                            </td>
                        </tr>
                    </template>
                    <template x-for="item in assignments" :key="item.id">
                        <tr>
                            <td>
                                <div style="font-weight:600;color:var(--text-primary)" x-text="item.title"></div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px" x-text="'Diberikan: ' + formatDate(item.assigned_date)"></div>
                            </td>
                            <td>
                                <div x-text="item.subject?.subject_name" style="font-weight:500;"></div>
                            </td>
                            <td>
                                <div x-text="item.teacher?.full_name" style="font-size:13px;"></div>
                            </td>
                            <td>
                                <div x-text="formatDate(item.due_date)"></div>
                                <span x-show="isOverdue(item.due_date) && getStatus(item) === 'pending'" style="font-size:11px;color:#DC2626;font-weight:500;">Terlewat</span>
                            </td>
                            <td>
                                <template x-if="getStatus(item) === 'pending'">
                                    <span class="badge-pending">Belum Dikumpul</span>
                                </template>
                                <template x-if="getStatus(item) === 'submitted'">
                                    <span class="badge-submitted">Terkumpul</span>
                                </template>
                                <template x-if="getStatus(item) === 'graded'">
                                    <div>
                                        <span class="badge-graded">Dinilai</span>
                                        <div style="font-weight:600;margin-top:4px" x-text="'Skor: ' + getScore(item)"></div>
                                    </div>
                                </template>
                            </td>
                            <td style="text-align:right">
                                <button @click="openSubmitModal(item)" x-show="getStatus(item) !== 'graded'" class="sc-action primary" style="padding:4px 10px;font-size:12px;height:auto">Kumpulkan</button>
                                <button x-show="getStatus(item) === 'submitted'" @click="cancelSubmission(item.id)" class="sc-action" style="padding:4px 10px;font-size:12px;height:auto;border:1px solid #ccc;background:#fff;margin-top:4px">Batal</button>
                                <button x-show="getStatus(item) === 'graded'" @click="openDetailModal(item)" class="sc-action" style="padding:4px 10px;font-size:12px;height:auto;border:1px solid #ccc;background:#fff">Detail</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

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

    {{-- Modal Submit Tugas --}}
    <div class="modal-backdrop" x-show="showModal" style="display:none" x-transition.opacity>
        <div class="modal-content" @click.away="closeModal()" style="max-width:500px">
            <div class="modal-header">
                <h3 class="modal-title">Kumpulkan Tugas</h3>
                <button @click="closeModal()" class="modal-close"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body" style="padding:20px">
                <div style="margin-bottom:15px;background:#F8FAFC;padding:12px;border-radius:6px;border:1px solid #E2E8F0">
                    <div style="font-weight:600;font-size:14px" x-text="selectedAssignment?.title"></div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:4px" x-text="selectedAssignment?.description"></div>
                </div>

                <div style="margin-bottom:15px">
                    <label style="display:block;margin-bottom:6px;font-size:13px;font-weight:500;color:var(--text-primary)">File Jawaban (PDF/DOC/Image)</label>
                    <input type="file" id="assignment_file" class="dt-search-input" style="width:100%;height:auto;padding:8px">
                </div>
            </div>
            <div class="modal-footer">
                <button @click="closeModal()" class="sc-action" style="background:#fff;border:1px solid var(--border-color)">Batal</button>
                <button @click="submitAssignment()" class="sc-action primary" :disabled="submitting">
                    <span x-show="!submitting">Kirim Tugas</span>
                    <span x-show="submitting">Mengirim...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Detail Tugas (Graded) --}}
    <div class="modal-backdrop" x-show="showDetailModal" style="display:none" x-transition.opacity>
        <div class="modal-content" @click.away="closeDetailModal()" style="max-width:400px">
            <div class="modal-header">
                <h3 class="modal-title">Detail Nilai</h3>
                <button @click="closeDetailModal()" class="modal-close"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body" style="padding:20px;text-align:center">
                <div style="font-size:48px;font-weight:700;color:#16A34A" x-text="getScore(selectedAssignment)"></div>
                <div style="font-size:14px;color:var(--text-muted);margin-bottom:15px">Skor Akhir</div>
                <div style="background:#F8FAFC;padding:12px;border-radius:6px;text-align:left">
                    <div style="font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:4px">Catatan Guru:</div>
                    <div style="font-size:14px" x-text="getFeedback(selectedAssignment)"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('studentAssignmentIndex', (config) => ({
        indexUrl: config.indexUrl,
        assignments: [],
        meta: {},
        loading: false,
        search: '',
        statusFilter: '',
        
        showModal: false,
        submitting: false,
        selectedAssignment: null,
        
        showDetailModal: false,

        init() {
            this.fetchAssignments();
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

        getStatus(assignment) {
            if (!assignment.submissions || assignment.submissions.length === 0) return 'pending';
            return assignment.submissions[0].status;
        },

        getScore(assignment) {
            if (!assignment.submissions || assignment.submissions.length === 0) return '-';
            return assignment.submissions[0].score ?? '-';
        },

        getFeedback(assignment) {
            if (!assignment.submissions || assignment.submissions.length === 0) return '-';
            return assignment.submissions[0].feedback ?? '-';
        },

        openSubmitModal(assignment) {
            this.selectedAssignment = assignment;
            this.showModal = true;
            document.getElementById('assignment_file').value = '';
        },

        closeModal() {
            if (this.submitting) return;
            this.showModal = false;
            this.selectedAssignment = null;
        },

        async submitAssignment() {
            if (!this.selectedAssignment) return;
            
            const fileInput = document.getElementById('assignment_file');
            const formData = new FormData();
            
            if (fileInput.files.length > 0) {
                formData.append('attachment', fileInput.files[0]);
            }
            
            this.submitting = true;
            try {
                const res = await fetch(`${this.indexUrl}/${this.selectedAssignment.id}/submit`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf(),
                    },
                    body: formData
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal mengumpulkan tugas');
                
                this.toast('success', json.message);
                this.closeModal();
                this.fetchAssignments(this.meta.current_page);
            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.submitting = false;
            }
        },

        async cancelSubmission(id) {
            if(!confirm('Yakin ingin membatalkan pengumpulan?')) return;
            try {
                const res = await fetch(`${this.indexUrl}/${id}/submission`, {
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
                this.fetchAssignments(this.meta.current_page);
            } catch (err) {
                this.toast('error', err.message);
            }
        },

        openDetailModal(assignment) {
            this.selectedAssignment = assignment;
            this.showDetailModal = true;
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedAssignment = null;
        },

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
