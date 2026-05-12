// resources/js/school-admin/students.js
// Import di app.js:
//   import { studentsSearch, studentDetail, studentCreate } from './school-admin/students';
//   window.studentsSearch = studentsSearch;
//   window.studentDetail  = studentDetail;
//   window.studentCreate  = studentCreate;

// ═══════════════════════════════════════════════════════════════
//  INDEX PAGE — Daftar Siswa
// ═══════════════════════════════════════════════════════════════
export function studentSearch(config = {}) {
    return {
        // ── URLs ──────────────────────────────────────────────
        indexUrl:        config.indexUrl        ?? '/school-admin/students',
        showUrl:         config.showUrl         ?? '/school-admin/students',
        toggleStatusUrl: config.toggleStatusUrl ?? '/school-admin/students',
        classGroupsUrl:  config.classGroupsUrl  ?? '/school-admin/students/class-groups',

        // ── State ─────────────────────────────────────────────
        students:     [],
        meta:         { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        stats:        { total: 0, active: 0, inactive: 0, unverified: 0 },
        search:       '',
        perPage:      10,
        statusFilter: '',
        classFilter:  '',
        classGroups:  [],
        loading:      false,
        filterOpen:       false,
        classFilterOpen:  false,

        // ── Init ──────────────────────────────────────────────
        init() {
            this.fetchClassGroups();
            this.fetchStudents();
        },

        // ── Avatar helpers ────────────────────────────────────
        _avatarColors: ['av-blue', 'av-violet', 'av-green', 'av-pink', 'av-teal', 'av-red', 'av-indigo'],
        avatarColor(index) { return this._avatarColors[index % this._avatarColors.length]; },
        initials(name) {
            return (name || '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() || '').join('').slice(0, 2);
        },

        // ── Fetch class groups (dropdown filter) ──────────────
        async fetchClassGroups() {
            try {
                const res  = await fetch(this.classGroupsUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                const data = await res.json();
                this.classGroups = [{ value: '', label: 'Semua Kelas' }, ...data.map(g => ({ value: g, label: g }))];
            } catch {
                this.classGroups = [{ value: '', label: 'Semua Kelas' }];
            }
        },

        classFilterLabel() {
            const found = this.classGroups.find(c => c.value === this.classFilter);
            return found ? found.label : 'Semua Kelas';
        },
        setClassFilter(val) { this.classFilter = val; this.classFilterOpen = false; this.fetchStudents(); },

        // ── Fetch students ────────────────────────────────────
        async fetchStudents(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()       ? { search: this.search.trim() }      : {}),
                    ...(this.statusFilter !== '' ? { status: this.statusFilter }        : {}),
                    ...(this.classFilter  !== '' ? { class_group: this.classFilter }    : {}),
                });
                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json        = await res.json();
                this.students     = json.data;
                this.meta         = json.meta;
                this.stats        = json.stats ?? { total: 0, active: 0, inactive: 0, unverified: 0 };
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan saat memuat data.');
            } finally {
                this.loading = false;
            }
        },

        statusLabel() {
            if (this.statusFilter === 'active')   return 'Aktif';
            if (this.statusFilter === 'inactive') return 'Non-Aktif';
            return 'Semua Status';
        },
        setStatusFilter(val) { this.statusFilter = val; this.filterOpen = false; this.fetchStudents(); },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchStudents(page);
        },

        // ── Toggle Status ─────────────────────────────────────
        async toggleStatus(student) {
            const willBeActive = student.status !== 'active';
            const result = await Swal.fire({
                title: `${willBeActive ? 'Aktifkan' : 'Nonaktifkan'} siswa "${student.name}"?`,
                text:  `Siswa akan ${willBeActive ? 'diaktifkan' : 'dinonaktifkan'}.`,
                icon:  'question',
                showCancelButton:   true,
                confirmButtonColor: willBeActive ? '#10B981' : '#F59E0B',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  `Ya, ${willBeActive ? 'Aktifkan' : 'Nonaktifkan'}!`,
                cancelButtonText:   'Batal',
                customClass: { popup: 'swal-popup-rounded' },
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.toggleStatusUrl}/${student.id}/toggle-status`, {
                    method:  'PATCH',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal mengubah status.');
                student.status = json.status;
                this.showToast('success', json.message);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        // ── Delete ────────────────────────────────────────────
        async deleteStudent(id, name) {
            const result = await Swal.fire({
                title: `Hapus siswa "${name}"?`,
                text:  'Data yang dihapus tidak dapat dikembalikan.',
                icon:  'warning',
                showCancelButton:   true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Hapus!',
                cancelButtonText:   'Batal',
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.showUrl}/${id}`, {
                    method:  'DELETE',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal menghapus siswa.');
                this.showToast('success', 'Siswa berhasil dihapus.');
                this.fetchStudents(this.meta.current_page);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        goToDetail(id) {
            window.location.href = `${this.showUrl}/${id}/detail`;
        },

        // ── Helpers ───────────────────────────────────────────
        showToast(icon, message) {
            Swal.fire({
                toast: true, position: 'top-end', icon, title: message,
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
                didOpen: t => {
                    t.addEventListener('mouseenter', Swal.stopTimer);
                    t.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });
        },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ''; },
    };
}

// ═══════════════════════════════════════════════════════════════
//  DETAIL PAGE
// ═══════════════════════════════════════════════════════════════
export function studentDetail(config = {}) {
    return {
        baseUrl:  config.baseUrl  ?? '/school-admin/students',
        indexUrl: config.indexUrl ?? '/school-admin/students',

        student:    config.studentData ?? {},
        isEditing:  false,
        submitting: false,
        form:       {},
        errors:     {},

        init() {},

        initials(name) {
            return (name || '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() || '').join('').slice(0, 2);
        },

        // ── Edit ──────────────────────────────────────────────
        startEdit() {
            this.form = {
                name:                  this.student.name          ?? '',
                email:                 this.student.email         ?? '',
                phone_number:          this.student.phone_number  ?? '',
                password:              '',
                password_confirmation: '',
                status:                this.student.status        ?? 'active',
                profile: this.student.profile ? { ...this.student.profile } : {},
            };
            this.errors    = {};
            this.isEditing = true;
            this.$nextTick(() => document.querySelector('[data-edit-focus]')?.focus());
        },

        cancelEdit() {
            this.isEditing  = false;
            this.submitting = false;
            this.errors     = {};
            this.form       = {};
        },

        validate() {
            this.errors = {};
            if (!this.form.name?.trim())  this.errors.name  = 'Nama wajib diisi.';
            if (!this.form.email?.trim()) this.errors.email = 'Email wajib diisi.';
            if (this.form.password && this.form.password !== this.form.password_confirmation) {
                this.errors.password = 'Konfirmasi password tidak cocok.';
            }
            return Object.keys(this.errors).length === 0;
        },

        async submitEdit() {
            if (!this.validate()) return;
            this.submitting = true;
            try {
                const payload = { ...this.form };
                if (!payload.password) { delete payload.password; delete payload.password_confirmation; }

                const res  = await fetch(`${this.baseUrl}/${this.student.id}`, {
                    method:  'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(Object.entries(json.errors).map(([k, v]) => [k, v[0]]));
                        return;
                    }
                    throw new Error(json.message ?? 'Gagal menyimpan data.');
                }

                this.student   = { ...this.student, ...json.data };
                this.isEditing = false;
                this.form      = {};
                this.showToast('success', 'Data siswa berhasil diperbarui.');
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            } finally {
                this.submitting = false;
            }
        },

        // ── Toggle Status ─────────────────────────────────────
        async toggleStatus() {
            const willBeActive = this.student.status !== 'active';
            const result = await Swal.fire({
                title: `${willBeActive ? 'Aktifkan' : 'Nonaktifkan'} siswa ini?`,
                icon:  'question',
                showCancelButton: true,
                confirmButtonColor: willBeActive ? '#10B981' : '#F59E0B',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  `Ya, ${willBeActive ? 'Aktifkan' : 'Nonaktifkan'}!`,
                cancelButtonText:   'Batal',
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.baseUrl}/${this.student.id}/toggle-status`, {
                    method:  'PATCH',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal mengubah status.');
                this.student.status = json.status;
                this.showToast('success', json.message);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        // ── Delete ────────────────────────────────────────────
        async deleteStudent() {
            const result = await Swal.fire({
                title: `Hapus siswa "${this.student.name}"?`,
                text:  'Data yang dihapus tidak dapat dikembalikan.',
                icon:  'warning',
                showCancelButton:   true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Hapus!',
                cancelButtonText:   'Batal',
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.baseUrl}/${this.student.id}`, {
                    method:  'DELETE',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal menghapus siswa.');
                this.showToast('success', 'Siswa berhasil dihapus.');
                setTimeout(() => { window.location.href = this.indexUrl; }, 1200);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        showToast(icon, message) {
            Swal.fire({
                toast: true, position: 'top-end', icon, title: message,
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
                didOpen: t => {
                    t.addEventListener('mouseenter', Swal.stopTimer);
                    t.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });
        },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ''; },
    };
}

// ═══════════════════════════════════════════════════════════════
//  CREATE PAGE
// ═══════════════════════════════════════════════════════════════
export function studentCreate(config = {}) {
    return {
        storeUrl:    config.storeUrl    ?? '/school-admin/students',
        indexUrl:    config.indexUrl    ?? '/school-admin/students',

        showPassword: false,
        submitting:   false,
        errors:       {},

        form: {
            name:                  '',
            email:                 '',
            phone_number:          '',
            password:              '',
            password_confirmation: '',
            status:                'active',
            profile: {
                nis:             '',
                full_name:       '',
                nick_name:       '',
                birth_date:      '',
                gender:          '',
                phone_number:    '',
                address:         '',
                city:            '',
                province:        '',
                postal_code:     '',
                grade_id:        '',
                class_group:     '',
                enrollment_date: '',
                graduation_date: '',
            },
        },

        init() {},

        validate() {
            this.errors = {};
            if (!this.form.name?.trim())  this.errors.name  = 'Nama wajib diisi.';
            if (!this.form.email?.trim()) this.errors.email = 'Email wajib diisi.';
            if (!this.form.status)        this.errors.status = 'Status wajib dipilih.';
            if (!this.form.password)      this.errors.password = 'Password wajib diisi.';
            if (this.form.password && this.form.password.length < 8) {
                this.errors.password = 'Password minimal 8 karakter.';
            }
            if (this.form.password !== this.form.password_confirmation) {
                this.errors.password_confirmation = 'Konfirmasi password tidak cocok.';
            }
            return Object.keys(this.errors).length === 0;
        },

        async submitForm() {
            if (!this.validate()) {
                this.$nextTick(() => document.querySelector('.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                return;
            }
            this.submitting = true;
            try {
                // Bersihkan profile kosong
                const profile = Object.fromEntries(
                    Object.entries(this.form.profile).filter(([, v]) => v !== '' && v !== null)
                );

                const payload = { ...this.form, profile: Object.keys(profile).length ? profile : undefined };

                const res  = await fetch(this.storeUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(Object.entries(json.errors).map(([k, v]) => [k, v[0]]));
                        this.$nextTick(() => document.querySelector('.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                        return;
                    }
                    throw new Error(json.message ?? 'Gagal menyimpan data.');
                }

                this.showToast('success', 'Siswa berhasil ditambahkan!');
                setTimeout(() => { window.location.href = this.indexUrl; }, 1200);

            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            } finally {
                this.submitting = false;
            }
        },

        showToast(icon, message) {
            Swal.fire({
                toast: true, position: 'top-end', icon, title: message,
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
                didOpen: t => {
                    t.addEventListener('mouseenter', Swal.stopTimer);
                    t.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });
        },
        csrfToken() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ''; },
    };
}