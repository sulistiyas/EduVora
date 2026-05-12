// resources/js/school-admin/teachers.js
// Import di app.js:
//   import teachersSearch, { teacherDetail, teacherCreate } from './school-admin/teachers';
//   window.teachersSearch = teachersSearch;
//   window.teacherDetail  = teacherDetail;
//   window.teacherCreate  = teacherCreate;

// ═══════════════════════════════════════════════════════════════
//  INDEX PAGE — Daftar Guru
// ═══════════════════════════════════════════════════════════════
export function teacherSearch(config = {}) {
    return {
        // ── URLs ──────────────────────────────────────────────
        indexUrl:        config.indexUrl        ?? '/school-admin/teachers',
        showUrl:         config.showUrl         ?? '/school-admin/teachers',
        toggleStatusUrl: config.toggleStatusUrl ?? '/school-admin/teachers',
        subjectsUrl:     config.subjectsUrl     ?? '/school-admin/teachers/subjects',

        // ── State ─────────────────────────────────────────────
        teachers:       [],
        meta:           { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        stats:          { total: 0, active: 0, inactive: 0, unverified: 0 },
        search:         '',
        perPage:        10,
        statusFilter:   '',
        subjectFilter:  '',
        empTypeFilter:  '',
        subjects:       [],
        loading:        false,
        filterOpen:         false,
        subjectFilterOpen:  false,
        empTypeFilterOpen:  false,

        // ── Init ──────────────────────────────────────────────
        init() {
            this.fetchSubjects();
            this.fetchTeachers();
        },

        // ── Avatar helpers ────────────────────────────────────
        _avatarColors: ['av-blue', 'av-violet', 'av-green', 'av-pink', 'av-teal', 'av-red', 'av-indigo'],
        avatarColor(index) { return this._avatarColors[index % this._avatarColors.length]; },
        initials(name) {
            return (name || '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() || '').join('').slice(0, 2);
        },

        // ── Fetch subjects (dropdown filter) ──────────────────
        async fetchSubjects() {
            try {
                const res  = await fetch(this.subjectsUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                const data = await res.json();
                this.subjects = [{ value: '', label: 'Semua Mata Pelajaran' }, ...data.map(s => ({ value: s, label: s }))];
            } catch {
                this.subjects = [{ value: '', label: 'Semua Mata Pelajaran' }];
            }
        },

        subjectFilterLabel() {
            const found = this.subjects.find(s => s.value === this.subjectFilter);
            return found ? found.label : 'Semua Mata Pelajaran';
        },
        setSubjectFilter(val) { this.subjectFilter = val; this.subjectFilterOpen = false; this.fetchTeachers(); },

        empTypeLabel() {
            const map = { '': 'Semua Tipe', permanent: 'PNS / Tetap', honorary: 'Honorer', contract: 'Kontrak' };
            return map[this.empTypeFilter] ?? 'Semua Tipe';
        },
        setEmpTypeFilter(val) { this.empTypeFilter = val; this.empTypeFilterOpen = false; this.fetchTeachers(); },

        // ── Fetch teachers ─────────────────────────────────────
        async fetchTeachers(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()         ? { search: this.search.trim() }            : {}),
                    ...(this.statusFilter !== ''   ? { status: this.statusFilter }             : {}),
                    ...(this.subjectFilter !== ''  ? { subject: this.subjectFilter }           : {}),
                    ...(this.empTypeFilter !== ''  ? { employee_type: this.empTypeFilter }     : {}),
                });
                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json       = await res.json();
                this.teachers    = json.data;
                this.meta        = json.meta;
                this.stats       = json.stats ?? { total: 0, active: 0, inactive: 0, unverified: 0 };
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
        setStatusFilter(val) { this.statusFilter = val; this.filterOpen = false; this.fetchTeachers(); },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchTeachers(page);
        },

        // ── Toggle Status ─────────────────────────────────────
        async toggleStatus(teacher) {
            const willBeActive = teacher.status !== 'active';
            const result = await Swal.fire({
                title: `${willBeActive ? 'Aktifkan' : 'Nonaktifkan'} guru "${teacher.name}"?`,
                text:  `Guru akan ${willBeActive ? 'diaktifkan' : 'dinonaktifkan'}.`,
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
                const res  = await fetch(`${this.toggleStatusUrl}/${teacher.id}/toggle-status`, {
                    method:  'PATCH',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal mengubah status.');
                teacher.status = json.status;
                this.showToast('success', json.message);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        // ── Delete ────────────────────────────────────────────
        async deleteTeacher(id, name) {
            const result = await Swal.fire({
                title: `Hapus guru "${name}"?`,
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
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal menghapus guru.');
                this.showToast('success', 'Guru berhasil dihapus.');
                this.fetchTeachers(this.meta.current_page);
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
export function teacherDetail(config = {}) {
    return {
        baseUrl:  config.baseUrl  ?? '/school-admin/teachers',
        indexUrl: config.indexUrl ?? '/school-admin/teachers',

        teacher:    config.teacherData ?? {},
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
                name:                  this.teacher.name         ?? '',
                email:                 this.teacher.email        ?? '',
                phone_number:          this.teacher.phone_number ?? '',
                password:              '',
                password_confirmation: '',
                status:                this.teacher.status       ?? 'active',
                profile: this.teacher.profile ? { ...this.teacher.profile } : {},
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

                const res  = await fetch(`${this.baseUrl}/${this.teacher.id}`, {
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

                this.teacher   = { ...this.teacher, ...json.data };
                this.isEditing = false;
                this.form      = {};
                this.showToast('success', 'Data guru berhasil diperbarui.');
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            } finally {
                this.submitting = false;
            }
        },

        // ── Toggle Status ─────────────────────────────────────
        async toggleStatus() {
            const willBeActive = this.teacher.status !== 'active';
            const result = await Swal.fire({
                title: `${willBeActive ? 'Aktifkan' : 'Nonaktifkan'} guru ini?`,
                icon:  'question',
                showCancelButton: true,
                confirmButtonColor: willBeActive ? '#10B981' : '#F59E0B',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  `Ya, ${willBeActive ? 'Aktifkan' : 'Nonaktifkan'}!`,
                cancelButtonText:   'Batal',
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.baseUrl}/${this.teacher.id}/toggle-status`, {
                    method:  'PATCH',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal mengubah status.');
                this.teacher.status = json.status;
                this.showToast('success', json.message);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        // ── Delete ────────────────────────────────────────────
        async deleteTeacher() {
            const result = await Swal.fire({
                title: `Hapus guru "${this.teacher.name}"?`,
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
                const res  = await fetch(`${this.baseUrl}/${this.teacher.id}`, {
                    method:  'DELETE',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': this.csrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal menghapus guru.');
                this.showToast('success', 'Guru berhasil dihapus.');
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
export function teacherCreate(config = {}) {
    return {
        storeUrl: config.storeUrl ?? '/school-admin/teachers',
        indexUrl: config.indexUrl ?? '/school-admin/teachers',

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
                nip:           '',
                full_name:     '',
                nick_name:     '',
                birth_date:    '',
                gender:        '',
                phone_number:  '',
                address:       '',
                city:          '',
                province:      '',
                postal_code:   '',
                subject:       '',
                employee_type: '',
                join_date:     '',
                resign_date:   '',
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

                this.showToast('success', 'Guru berhasil ditambahkan!');
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