import Swal from "sweetalert2";

export function teacherScore(config = {}) {
    return {
        // ─── Config ────────────────────────────────────────────────
        indexUrl:    config.indexUrl    ?? '/teacher/scores',
        storeUrl:    config.storeUrl    ?? '/teacher/scores',
        finalUrl:    config.finalUrl    ?? '/teacher/scores/final-score',
        semestersUrl:config.semestersUrl ?? '/teacher/schedules/semesters',
        

        // ─── State ─────────────────────────────────────────────────
        scores:   [],
        meta:     { current_page: 1, per_page: 15, total: 0, last_page: 1 },
        counts:   { harian: 0, uts: 0, uas: 0 },
        loading:  false,
        filters:  { search: '', semester_id: '', score_type: '', per_page: 15 },

        // ─── Dropdown data ──────────────────────────────────────────
        semesters:     [],
        gradeSubjects: [],
        students:      [],

        // ─── Modal ─────────────────────────────────────────────────
        showModal:  false,
        isEdit:     false,
        submitting: false,
        editId:     null,
        form: {
            student_id:       '',
            grade_subject_id: '',
            semester_id:      '',
            score_type:       'harian',
            score:            '',
            max_score:        100,
            description:      '',
            date:             '',
        },
        errors: {},

        scoreTypes: [
            { value: 'harian', label: 'Harian' },
            { value: 'uts',    label: 'UTS' },
            { value: 'uas',    label: 'UAS' },
        ],

        // ─── Init ───────────────────────────────────────────────────
        async init() {
            await this.fetchSemesters();
            await this.fetchScores();
        },

        // ─── Fetch semesters ────────────────────────────────────────
        async fetchSemesters() {
            try {
                const res = await fetch(this.semestersUrl + '?per_page=all&academic_year_status=active', {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                const json = await res.json();
                this.semesters = Array.isArray(json) ? json : (json.data ?? []);
                const active = this.semesters.find(s => s.status === 'active');
                if (active) this.filters.semester_id = active.semester_id;
            } catch { this.semesters = []; }
        },

        // ─── Fetch scores ────────────────────────────────────────────
        async fetchScores(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.filters.per_page),
                    ...(this.filters.search      ? { search:       this.filters.search      } : {}),
                    ...(this.filters.semester_id ? { semester_id:  this.filters.semester_id } : {}),
                    ...(this.filters.score_type  ? { score_type:   this.filters.score_type  } : {}),
                });
                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data nilai.');
                const json = await res.json();

                // Laravel pagination ada di json.data (object), bukan json.data.data
                const pagination  = json.data;
                this.scores       = pagination.data ?? [];
                this.meta         = {
                    current_page: pagination.current_page,
                    per_page:     pagination.per_page,
                    total:        pagination.total,
                    last_page:    pagination.last_page,
                };
                this.updateCounts();
            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.loading = false;
            }
        },

        updateCounts() {
            this.counts = {
                harian: this.scores.filter(s => s.score_type === 'harian').length,
                uts:    this.scores.filter(s => s.score_type === 'uts').length,
                uas:    this.scores.filter(s => s.score_type === 'uas').length,
            };
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchScores(page);
        },

        // ─── Grade subjects by grade ────────────────────────────────
        async fetchGradeSubjects() {
            if (!this.form.semester_id) {
                this.gradeSubjects = [];
                return;
            }

            try {
                const res = await fetch(
                    `/teacher/scores/grade-subjects?semester_id=${this.form.semester_id}`,
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );

                if (!res.ok) return;

                const json = await res.json();

                this.gradeSubjects = json.data ?? json;

            } catch {
                this.gradeSubjects = [];
            }
        },

        // ─── Students by grade ──────────────────────────────────────
        async fetchStudents(gradeSubjectId) {

            if (!gradeSubjectId) {
                this.students = [];
                return;
            }

            try {
                const res = await fetch(
                    `/teacher/scores/students?grade_subject_id=${gradeSubjectId}`,
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );

                if (!res.ok) return;

                const json = await res.json();

                this.students = json.data ?? json;

            } catch {
                this.students = [];
            }
        },

        onSemesterChange() {
            this.form.grade_subject_id = '';
            this.form.student_id = '';
            this.gradeSubjects = [];
            this.students = [];
        },

        async onSubjectChange() {
            this.form.student_id = '';
            this.students = [];
            await this.fetchStudents(this.form.grade_subject_id);
        },

        // ─── Modal ─────────────────────────────────────────────────
        openCreate() {
            this.isEdit  = false;
            this.editId  = null;
            this.errors  = {};
            this.form    = {
                student_id: '', grade_subject_id: '', semester_id: this.filters.semester_id ?? '',
                score_type: 'harian', score: '', max_score: 100,
                description: '', date: new Date().toISOString().split('T')[0],
            };
            this.showModal = true;
        },

        openEdit(item) {
            this.isEdit  = true;
            this.editId  = item.student_score_id;
            this.errors  = {};
            this.form    = {
                score:       item.score,
                max_score:   item.max_score,
                description: item.description ?? '',
                date:        item.date,
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
        },

        async submit() {
            this.errors    = {};
            this.submitting = true;
            try {
                const url    = this.isEdit
                    ? `${this.indexUrl}/${this.editId}`
                    : this.storeUrl;
                const method = this.isEdit ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(
                            Object.entries(json.errors).map(([k, v]) => [k, v[0]])
                        );
                        return;
                    }
                    throw new Error(json.message ?? 'Terjadi kesalahan.');
                }

                this.toast('success', this.isEdit ? 'Nilai berhasil diperbarui.' : 'Nilai berhasil disimpan.');
                this.closeModal();
                await this.fetchScores(this.meta.current_page);

            } catch (err) {
                this.toast('error', err.message);
            } finally {
                this.submitting = false;
            }
        },

        async confirmDelete(id) {
            if (!confirm('Yakin hapus nilai ini?')) return;
            try {
                const res = await fetch(`${this.indexUrl}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Gagal menghapus.');
                this.toast('success', 'Nilai berhasil dihapus.');
                await this.fetchScores(this.meta.current_page);
            } catch (err) {
                this.toast('error', err.message);
            }
        },

        // ─── Helpers ────────────────────────────────────────────────
        scoreTypeLabel(type) {
            return { harian: 'Harian', uts: 'UTS', uas: 'UAS' }[type] ?? type;
        },
        scoreTypeBadge(type) {
            return { harian: 'aktif', uts: 'admin', uas: 'guru' }[type] ?? '';
        },
        formatDate(date) {
            if (!date) return '—';
            return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        initials(name) {
            return (name ?? '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() ?? '').join('').slice(0, 2);
        },
        toast(icon, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ toast:true, position:'top-end', icon, title: message, showConfirmButton:false, timer:3500, timerProgressBar:true });
            }
        },
    };
}
export function teacherScoreForm(config = {}) {
    return {
        storeUrl:        config.storeUrl        ?? '',
        updateUrl:       config.updateUrl        ?? '',
        indexUrl:        config.indexUrl         ?? '',
        semestersUrl:    config.semestersUrl     ?? '',
        gradeSubjectsUrl:config.gradeSubjectsUrl ?? '',
        studentsUrl:     config.studentsUrl      ?? '',
        isEdit:          config.isEdit           ?? false,
        existing:        config.existing         ?? {},

        semesters:     [],
        gradeSubjects: [],
        students:      [],
        loadingSubjects: false,
        loadingStudents: false,
        submitting:    false,
        errors:        {},

        form: {
            student_id:       '',
            grade_subject_id: '',
            semester_id:      '',
            score_type:       'harian',
            score:            '',
            max_score:        100,
            description:      '',
            date:             new Date().toISOString().split('T')[0],
        },

        scoreTypes: [
            { value: 'harian', label: 'Harian' },
            { value: 'uts',    label: 'UTS' },
            { value: 'uas',    label: 'UAS' },
        ],

        async init() {
            if (this.isEdit) {
                this.form = {
                    score:       this.existing.score,
                    max_score:   this.existing.max_score,
                    description: this.existing.description ?? '',
                    date:        this.existing.date,
                };
            } else {
                await this.fetchSemesters();
            }
        },

        async fetchSemesters() {
            try {
                const res = await fetch(this.semestersUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                const json = await res.json();
                this.semesters = Array.isArray(json) ? json : (json.data ?? []);
                const active = this.semesters.find(s => s.status === 'active');
                if (active) {
                    this.form.semester_id = active.semester_id;
                    await this.fetchGradeSubjects();
                }
            } catch { this.semesters = []; }
        },

        async fetchGradeSubjects() {
            if (!this.form.semester_id) { this.gradeSubjects = []; return; }
            this.loadingSubjects = true;
            try {
                const res = await fetch(
                    `${this.gradeSubjectsUrl}?semester_id=${this.form.semester_id}`,
                    { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }
                );
                if (!res.ok) return;
                const json = await res.json();
                this.gradeSubjects = json.data ?? json;
            } catch { this.gradeSubjects = []; }
            finally { this.loadingSubjects = false; }
        },

        async fetchStudents() {
            if (!this.form.grade_subject_id) { this.students = []; return; }
            this.loadingStudents = true;
            try {
                const res = await fetch(
                    `${this.studentsUrl}?grade_subject_id=${this.form.grade_subject_id}`,
                    { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }
                );
                if (!res.ok) return;
                const json = await res.json();
                this.students = json.data ?? json;
            } catch { this.students = []; }
            finally { this.loadingStudents = false; }
        },

        async onSemesterChange() {
            this.form.grade_subject_id = '';
            this.form.student_id = '';
            this.gradeSubjects = [];
            this.students = [];
            await this.fetchGradeSubjects();
        },

        async onSubjectChange() {
            this.form.student_id = '';
            this.students = [];
            await this.fetchStudents();
        },

        async submit() {
            this.errors    = {};
            this.submitting = true;
            try {
                const url    = this.isEdit ? this.updateUrl : this.storeUrl;
                const method = this.isEdit ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form),
                });

                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(
                            Object.entries(json.errors).map(([k, v]) => [k, v[0]])
                        );
                        return;
                    }
                    throw new Error(json.message ?? 'Terjadi kesalahan.');
                }

                window.location.href = this.indexUrl;

            } catch (err) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ toast:true, position:'top-end', icon:'error',
                        title: err.message, showConfirmButton:false, timer:3500 });
                }
            } finally {
                this.submitting = false;
            }
        },
    };
}