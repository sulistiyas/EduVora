export default function scheduleSearch(config = {}) {
    return {
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:         config.indexUrl         ?? '/schedules',
        storeUrl:         config.storeUrl         ?? '/schedules',
        showUrl:          config.showUrl          ?? '/schedules',
        updateUrl:        config.updateUrl        ?? '/schedules',
        destroyUrl:       config.destroyUrl       ?? '/schedules',
        toggleStatusUrl:  config.toggleStatusUrl  ?? '/schedules',
        semestersUrl:     config.semestersUrl     ?? '/schedules/semesters',
        roomsUrl:         config.roomsUrl         ?? '/schedules/rooms',
        gradeSubjectsUrl: config.gradeSubjectsUrl ?? '/schedules/grade-subjects',

        // ─── State ─────────────────────────────────────────────────────
        schedules:    [],
        meta:         { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        search:       '',
        perPage:      10,
        statusFilter: '',
        semesterFilter:  '',
        dayFilter:       '',
        sessionFilter:   '',
        loading:      false,

        // ─── Dropdown data ──────────────────────────────────────────────
        semesters:     [],
        rooms:         [],
        gradeSubjects: [],

        // ─── Filter dropdowns (toolbar) ─────────────────────────────────
        filterOpen:        false,
        dayFilterOpen:     false,
        sessionFilterOpen: false,

        // ─── Modal ─────────────────────────────────────────────────────
        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        errors:     {},
        form: {
            grade_subject_id: '',
            room_id:          '',
            semester_id:      '',
            day_of_week:      '',
            start_time:       '',
            end_time:         '',
            session_type:     'regular',
            status:           'active',
        },

        // ─── Searchable select state ────────────────────────────────────
        _selects: {
            grade_subject: { open: false, query: '' },
            semester:      { open: false, query: '' },
            day:           { open: false, query: '' },
            room:          { open: false, query: '' },
            status_form:   { open: false, query: '' },
        },

        // ─── Constants ─────────────────────────────────────────────────
        days: [
            { value: 1, label: 'Senin'  },
            { value: 2, label: 'Selasa' },
            { value: 3, label: 'Rabu'   },
            { value: 4, label: 'Kamis'  },
            { value: 5, label: 'Jumat'  },
            { value: 6, label: 'Sabtu'  },
            { value: 7, label: 'Minggu' },
        ],
        sessionTypes: [
            {
                value: 'regular',
                label: 'Reguler',
                icon: 'ri-book-open-line',
                description: 'Pembelajaran kelas biasa',
            },
            {
                value: 'lab',
                label: 'Laboratorium',
                icon: 'ri-flask-line',
                description: 'Praktikum atau eksperimen',
            },
            {
                value: 'exam',
                label: 'Ujian',
                icon: 'ri-file-list-3-line',
                description: 'Sesi ujian atau tes',
            },
            {
                value: 'extracurricular',
                label: 'Ekstrakurikuler',
                icon: 'ri-football-line',
                description: 'Kegiatan non akademik',
            },
            {
                value: 'remedial',
                label: 'Remedial',
                icon: 'ri-refresh-line',
                description: 'Perbaikan atau pengulangan',
            },
        ],

        // ─── Searchable Select API ──────────────────────────────────────

        toggleSelect(name) {
            const was = this._selects[name].open;
            // Tutup semua dulu
            Object.keys(this._selects).forEach(k => {
                this._selects[k].open  = false;
                this._selects[k].query = '';
            });
            this._selects[name].open = !was;

            if (this._selects[name].open) {
                this.$nextTick(() => {
                    const el = document.getElementById(`sch-sel-search-${name}`);
                    if (el) el.focus();
                });
            }
        },

        closeAllSelects() {
            Object.keys(this._selects).forEach(k => {
                this._selects[k].open  = false;
                this._selects[k].query = '';
            });
        },

        _allOptionsFor(name) {
            switch (name) {
                case 'grade_subject':
                    return this.gradeSubjects.map(gs => ({
                        value: gs.id,
                        label: gs.label,
                        sub:   gs.teacher_name ? `👤 ${gs.teacher_name}` : '',
                        icon:  'ri-book-2-line',
                    }));

                case 'semester':
                    return this.semesters.map(s => ({
                        value: s.semester_id,
                        label: s.semester_name,
                        icon:  'ri-calendar-line',
                    }));

                case 'day':
                    return this.days.map(d => ({
                        value: d.value,
                        label: d.label,
                        icon:  'ri-calendar-event-line',
                    }));

                case 'room':
                    return this.rooms.map(r => ({
                        value: r.room_id,
                        label: r.room_name + (r.code ? ` (${r.code})` : ''),
                        sub:   r.building ? `Gedung ${r.building}` : '',
                        icon:  'ri-door-open-line',
                    }));

                case 'status_form':
                    return [
                        { value: 'active',   label: 'Aktif',     icon: 'ri-checkbox-circle-line', color: '#059669' },
                        { value: 'inactive', label: 'Non-Aktif', icon: 'ri-close-circle-line',    color: '#D97706' },
                    ];

                default:
                    return [];
            }
        },

        filteredOptions(name) {
            const q    = (this._selects[name]?.query ?? '').toLowerCase().trim();
            const list = this._allOptionsFor(name);
            if (!q) return list;
            return list.filter(o =>
                o.label.toLowerCase().includes(q) ||
                (o.sub ?? '').toLowerCase().includes(q)
            );
        },

        // Map select name → form field
        _fieldForSelect(name) {
            return {
                grade_subject: 'grade_subject_id',
                semester:      'semester_id',
                day:           'day_of_week',
                room:          'room_id',
                status_form:   'status',
            }[name] ?? name;
        },

        selectLabel(name) {
            const val = this.form[this._fieldForSelect(name)];
            if (val === '' || val === null || val === undefined) {
                return {
                    grade_subject: '— Pilih Kelas / Mata Pelajaran —',
                    semester:      '— Pilih Semester —',
                    day:           '— Pilih Hari —',
                    room:          '— Pilih Ruangan —',
                    status_form:   '— Pilih Status —',
                }[name] ?? 'Pilih...';
            }
            const found = this._allOptionsFor(name).find(o => String(o.value) === String(val));
            return found ? found.label : String(val);
        },

        isSelected(name, value) {
            return String(this.form[this._fieldForSelect(name)]) === String(value);
        },

        pickOption(name, value) {
            this.form[this._fieldForSelect(name)] = value;
            this._selects[name].open  = false;
            this._selects[name].query = '';
            // Clear validation error
            const field = this._fieldForSelect(name);
            if (this.errors[field]) delete this.errors[field];
        },

        hasValue(name) {
            const val = this.form[this._fieldForSelect(name)];
            return val !== '' && val !== null && val !== undefined;
        },

        clearSelect(name, e) {
            e.stopPropagation();
            this.form[this._fieldForSelect(name)] = '';
        },

        // ─── Avatar helpers ─────────────────────────────────────────────
        _avatarColors: ['av-blue', 'av-violet', 'av-green', 'av-pink', 'av-teal', 'av-red', 'av-indigo'],
        avatarColor(index) { return this._avatarColors[index % this._avatarColors.length]; },
        initials(name) {
            return (name || '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() || '').join('').slice(0, 2);
        },

        // ─── Init ───────────────────────────────────────────────────────
        async init() {
            await Promise.all([
                this.fetchSemesters(),
                this.fetchRooms(),
                this.fetchGradeSubjects(),
            ]);
            this.fetchSchedules();
        },

        // ─── Dropdown loaders ──────────────────────────────────────────
        async fetchSemesters() {
            try {
                const res = await fetch(this.semestersUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                this.semesters = await res.json();
            } catch {
                this.semesters = [];
            }
        },

        async fetchRooms() {
            try {
                const res = await fetch(this.roomsUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                this.rooms = await res.json();
            } catch {
                this.rooms = [];
            }
        },

        async fetchGradeSubjects() {
            try {
                const res = await fetch(this.gradeSubjectsUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                this.gradeSubjects = await res.json();
            } catch {
                this.gradeSubjects = [];
            }
        },

        // ─── Fetch schedules ────────────────────────────────────────────
        async fetchSchedules(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()  ? { search:       this.search.trim()  } : {}),
                    ...(this.statusFilter   ? { status:       this.statusFilter   } : {}),
                    ...(this.semesterFilter ? { semester_id:  this.semesterFilter } : {}),
                    ...(this.dayFilter      ? { day_of_week:  this.dayFilter      } : {}),
                    ...(this.sessionFilter  ? { session_type: this.sessionFilter  } : {}),
                });

                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat data.');
                const json     = await res.json();
                this.schedules = json.data;
                this.meta      = json.meta;
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan saat memuat data.');
            } finally {
                this.loading = false;
            }
        },

        // ─── Toolbar filter helpers ─────────────────────────────────────
        statusLabel() {
            if (this.statusFilter === 'active')   return 'Aktif';
            if (this.statusFilter === 'inactive') return 'Non-Aktif';
            return 'Semua Status';
        },
        dayLabel() {
            const found = this.days.find(d => String(d.value) === String(this.dayFilter));
            return found ? found.label : 'Semua Hari';
        },
        sessionLabel() {
            const found = this.sessionTypes.find(s => s.value === this.sessionFilter);
            return found ? found.label : 'Semua Sesi';
        },
        semesterLabel() {
            const found = this.semesters.find(s => String(s.semester_id) === String(this.semesterFilter));
            return found ? found.semester_name : 'Semua Semester';
        },

        setStatusFilter(val)  { this.statusFilter  = val; this.filterOpen        = false; this.fetchSchedules(); },
        setDayFilter(val)     { this.dayFilter      = val; this.dayFilterOpen     = false; this.fetchSchedules(); },
        setSessionFilter(val) { this.sessionFilter  = val; this.sessionFilterOpen = false; this.fetchSchedules(); },
        setSemesterFilter(val){ this.semesterFilter = val; this.fetchSchedules(); },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchSchedules(page);
        },

        // ─── Session type badge ─────────────────────────────────────────
        sessionBadgeStyle(type) {
            const map = {
                regular:         'background:#EFF6FF;color:#1D4ED8',
                lab:             'background:#F0FDF4;color:#15803D',
                exam:            'background:#FEF9C3;color:#854D0E',
                extracurricular: 'background:#F5F3FF;color:#6D28D9',
                remedial:        'background:#FFF7ED;color:#C2410C',
            };
            return map[type] ?? 'background:#F1F5F9;color:#475569';
        },
        sessionBadgeLabel(type) {
            return this.sessionTypes.find(s => s.value === type)?.label ?? type;
        },

        // ─── Modal helpers ──────────────────────────────────────────────
        defaultForm() {
            return {
                grade_subject_id: '',
                room_id:          '',
                semester_id:      '',
                day_of_week:      '',
                start_time:       '',
                end_time:         '',
                session_type:     'regular',
                status:           'active',
            };
        },

        openCreateModal() {
            this.isEditing = false;
            this.editingId = null;
            this.form      = this.defaultForm();
            this.errors    = {};
            this.closeAllSelects();
            this.showModal = true;
        },

        async openEditModal(id) {
            this.isEditing = true;
            this.editingId = id;
            this.errors    = {};
            this.form      = this.defaultForm();
            this.closeAllSelects();
            this.showModal = true;

            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal mengambil data schedule.');
                const data = await res.json();

                this.form = {
                    grade_subject_id: String(data.grade_subject_id ?? ''),
                    room_id:          String(data.room_id          ?? ''),
                    semester_id:      String(data.semester_id      ?? ''),
                    day_of_week:      String(data.day_of_week      ?? ''),
                    start_time:       data.start_time  ?? '',
                    end_time:         data.end_time    ?? '',
                    session_type:     data.session_type ?? 'regular',
                    status:           data.status       ?? 'active',
                };
            } catch (err) {
                this.closeModal();
                this.showToast('error', err.message);
            }
        },

        closeModal() {
            this.showModal  = false;
            this.isEditing  = false;
            this.editingId  = null;
            this.submitting = false;
            this.errors     = {};
            this.closeAllSelects();
        },

        // ─── Validation ─────────────────────────────────────────────────
        validate() {
            this.errors = {};
            if (!this.form.grade_subject_id) this.errors.grade_subject_id = 'Kelas / Mata Pelajaran wajib dipilih.';
            if (!this.form.room_id)          this.errors.room_id          = 'Ruangan wajib dipilih.';
            if (!this.form.semester_id)      this.errors.semester_id      = 'Semester wajib dipilih.';
            if (!this.form.day_of_week)      this.errors.day_of_week      = 'Hari wajib dipilih.';
            if (!this.form.start_time)       this.errors.start_time       = 'Jam mulai wajib diisi.';
            if (!this.form.end_time)         this.errors.end_time         = 'Jam selesai wajib diisi.';
            if (this.form.start_time && this.form.end_time && this.form.end_time <= this.form.start_time) {
                this.errors.end_time = 'Jam selesai harus setelah jam mulai.';
            }
            if (!this.form.session_type) this.errors.session_type = 'Tipe sesi wajib dipilih.';
            return Object.keys(this.errors).length === 0;
        },

        // ─── Submit ─────────────────────────────────────────────────────
        async submitForm() {
            if (!this.validate()) return;
            this.submitting = true;
            try {
                const url    = this.isEditing ? `${this.updateUrl}/${this.editingId}` : this.storeUrl;
                const method = this.isEditing ? 'PUT' : 'POST';

                const res  = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.getCsrfToken(),
                    },
                    body: JSON.stringify({
                        grade_subject_id: parseInt(this.form.grade_subject_id),
                        room_id:          parseInt(this.form.room_id),
                        semester_id:      parseInt(this.form.semester_id),
                        day_of_week:      parseInt(this.form.day_of_week),
                        start_time:       this.form.start_time,
                        end_time:         this.form.end_time,
                        session_type:     this.form.session_type,
                        status:           this.form.status,
                    }),
                });
                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(
                            Object.entries(json.errors).map(([k, v]) => [k, v[0]])
                        );
                        return;
                    }
                    throw new Error(json.message ?? 'Gagal menyimpan data.');
                }

                this.closeModal();
                this.fetchSchedules(this.meta.current_page);
                this.showToast('success', this.isEditing ? 'Schedule berhasil diperbarui.' : 'Schedule berhasil ditambahkan.');
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            } finally {
                this.submitting = false;
            }
        },

        // ─── Delete ─────────────────────────────────────────────────────
        async deleteSchedule(id, label) {
            const result = await Swal.fire({
                title:              `Hapus Schedule "${label}"?`,
                text:               'Data yang dihapus tidak dapat dikembalikan.',
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  'Ya, Hapus!',
                cancelButtonText:   'Batal',
                customClass: { popup: 'swal-popup-rounded' },
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.destroyUrl}/${id}`, {
                    method:  'DELETE',
                    headers: {
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.getCsrfToken(),
                    },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal menghapus schedule.');

                const newTotal   = this.meta.total - 1;
                const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                const targetPage = Math.min(this.meta.current_page, maxPage);
                this.fetchSchedules(targetPage);
                this.showToast('success', 'Schedule berhasil dihapus.');
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        // ─── Toggle Status ──────────────────────────────────────────────
        async toggleStatus(schedule) {
            const willBeActive = schedule.status !== 'active';
            const result = await Swal.fire({
                title:              `${willBeActive ? 'Aktifkan' : 'Nonaktifkan'} schedule ini?`,
                text:               `Schedule akan ${willBeActive ? 'diaktifkan' : 'dinonaktifkan'}.`,
                icon:               'question',
                showCancelButton:   true,
                confirmButtonColor: willBeActive ? '#10B981' : '#F59E0B',
                cancelButtonColor:  '#94A3B8',
                confirmButtonText:  `Ya, ${willBeActive ? 'Aktifkan' : 'Nonaktifkan'}!`,
                cancelButtonText:   'Batal',
                customClass: { popup: 'swal-popup-rounded' },
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.toggleStatusUrl}/${schedule.schedule_id}/toggle-status`, {
                    method:  'PATCH',
                    headers: {
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.getCsrfToken(),
                    },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? 'Gagal mengubah status.');
                this.fetchSchedules(this.meta.current_page);
                this.showToast('success', json.message);
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            }
        },

        // ─── Helpers ───────────────────────────────────────────────────
        showToast(icon, message) {
            Swal.fire({
                toast:             true,
                position:          'top-end',
                icon,
                title:             message,
                showConfirmButton: false,
                timer:             3500,
                timerProgressBar:  true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });
        },
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        },
    };
}