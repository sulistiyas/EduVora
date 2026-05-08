import Swal from "sweetalert2";

export default function gradeSearch(config = {}) {
    return {
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:         config.indexUrl         ?? "/grades",
        storeUrl:         config.storeUrl         ?? "/grades",
        showUrl:          config.showUrl          ?? "/grades",
        updateUrl:        config.updateUrl        ?? "/grades",
        destroyUrl:       config.destroyUrl       ?? "/grades",
        toggleStatusUrl:  config.toggleStatusUrl  ?? "/grades",
        roomsUrl:         config.roomsUrl         ?? "/grades/rooms",
        teachersUrl:      config.teachersUrl      ?? "/grades/teachers",
        academicYearsUrl: config.academicYearsUrl ?? "/grades/academic-years",

        // ─── State ─────────────────────────────────────────────────────
        grades:        [],
        rooms:         [],
        teachers:      [],
        academicYears: [],
        meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },

        search:             "",
        perPage:            10,
        statusFilter:       "",
        roomFilter:         "",
        academicYearFilter: "",
        loading:            false,

        // ─── Modal ─────────────────────────────────────────────────────
        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        form: {
            grade_name:          "",
            level:               "",
            academic_year_id:    "",
            room_id:             "",
            homeroom_teacher_id: "",
            status:              "active",
        },
        errors: {},

        // ─── Toolbar filter dropdowns ───────────────────────────────────
        filterStatusOpen:       false,
        filterRoomOpen:         false,
        filterAcademicYearOpen: false,

        // ─── Searchable select state ────────────────────────────────────
        _selects: {
            academic_year: { open: false, query: "" },
            room:          { open: false, query: "" },
            teacher:       { open: false, query: "" },
            level:         { open: false, query: "" },
            status_form:   { open: false, query: "" },
        },

        levelOptions: [
            { value: 7,  label: "VII  — Kelas 7"  },
            { value: 8,  label: "VIII — Kelas 8"  },
            { value: 9,  label: "IX   — Kelas 9"  },
            { value: 10, label: "X    — Kelas 10" },
            { value: 11, label: "XI   — Kelas 11" },
            { value: 12, label: "XII  — Kelas 12" },
        ],

        statusOptions: [
            { value: "active",    label: "Aktif",       icon: "ri-checkbox-circle-line",  color: "#059669" },
            { value: "inactive",  label: "Non-Aktif",   icon: "ri-close-circle-line",     color: "#D97706" },
            { value: "graduated", label: "Lulus",       icon: "ri-graduation-cap-line",   color: "#2563EB" },
            { value: "archived",  label: "Diarsipkan",  icon: "ri-archive-line",          color: "#6B7280" },
        ],

        // ─── Searchable select API ──────────────────────────────────────

        toggleSelect(name) {
            const was = this._selects[name].open;
            // Tutup semua
            Object.keys(this._selects).forEach(k => {
                this._selects[k].open  = false;
                this._selects[k].query = "";
            });
            this._selects[name].open = !was;

            if (this._selects[name].open) {
                this.$nextTick(() => {
                    const el = document.getElementById(`sel-search-${name}`);
                    if (el) el.focus();
                });
            }
        },

        closeAllSelects() {
            Object.keys(this._selects).forEach(k => {
                this._selects[k].open  = false;
                this._selects[k].query = "";
            });
        },

        _allOptionsFor(name) {
            switch (name) {
                case "academic_year":
                    return this.academicYears.map(ay => ({
                        value: ay.academic_year_id,
                        label: ay.academic_year_name,
                        icon:  "ri-calendar-line",
                    }));
                case "room":
                    return [
                        { value: "", label: "Tidak Ada / Kosongkan", icon: "ri-forbid-line", muted: true },
                        ...this.rooms.map(r => ({
                            value: r.room_id,
                            label: r.room_name + (r.code ? ` (${r.code})` : ""),
                            sub:   r.building ? `Gedung ${r.building}` : "",
                            icon:  "ri-door-open-line",
                        })),
                    ];
                case "teacher":
                    return [
                        { value: "", label: "Belum Ditentukan", icon: "ri-user-unfollow-line", muted: true },
                        ...this.teachers.map(t => ({
                            value: t.teacher_id,
                            label: t.full_name,
                            sub:   t.nip || "",
                            icon:  "ri-user-3-line",
                        })),
                    ];
                case "level":
                    return this.levelOptions.map(o => ({ ...o, icon: "ri-sort-number-asc" }));
                case "status_form":
                    return this.statusOptions;
                default:
                    return [];
            }
        },

        filteredOptions(name) {
            const q    = (this._selects[name]?.query ?? "").toLowerCase().trim();
            const list = this._allOptionsFor(name);
            if (!q) return list;
            return list.filter(o =>
                o.label.toLowerCase().includes(q) ||
                (o.sub ?? "").toLowerCase().includes(q)
            );
        },

        _fieldForSelect(name) {
            return {
                academic_year: "academic_year_id",
                room:          "room_id",
                teacher:       "homeroom_teacher_id",
                level:         "level",
                status_form:   "status",
            }[name] ?? name;
        },

        selectLabel(name) {
            const val = this.form[this._fieldForSelect(name)];
            if (val === "" || val === null || val === undefined) {
                return {
                    academic_year: "Pilih Tahun Ajaran",
                    room:          "Tidak Ada",
                    teacher:       "Belum Ditentukan",
                    level:         "Pilih Tingkatan",
                    status_form:   "Pilih Status",
                }[name] ?? "Pilih...";
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
            this._selects[name].query = "";
            const field = this._fieldForSelect(name);
            if (this.errors[field]) delete this.errors[field];
        },

        hasValue(name) {
            const val = this.form[this._fieldForSelect(name)];
            return val !== "" && val !== null && val !== undefined;
        },

        clearSelect(name, e) {
            e.stopPropagation();
            this.form[this._fieldForSelect(name)] = "";
        },

        // ─── Avatar / misc helpers ──────────────────────────────────────
        _avatarColors: ["av-blue", "av-violet", "av-green", "av-pink", "av-teal", "av-red", "av-indigo"],
        avatarColor(i) { return this._avatarColors[i % this._avatarColors.length]; },
        initials(name) {
            return (name || "").split(/[-_ ]/).map(w => w[0]?.toUpperCase() || "").join("").slice(0, 2);
        },

        levelLabel(level) {
            return { 7:"VII", 8:"VIII", 9:"IX", 10:"X", 11:"XI", 12:"XII" }[level] ?? `Lvl ${level}`;
        },

        statusConfig(status) {
            return {
                active:    { bg: "#ECFDF5", color: "#059669", label: "Aktif" },
                inactive:  { bg: "#FFF7ED", color: "#D97706", label: "Non-Aktif" },
                graduated: { bg: "#EFF6FF", color: "#2563EB", label: "Lulus" },
                archived:  { bg: "#F3F4F6", color: "#6B7280", label: "Diarsipkan" },
            }[status] ?? { bg: "#F3F4F6", color: "#6B7280", label: status };
        },

        // ─── Init ───────────────────────────────────────────────────────
        async init() {
            await Promise.all([this.fetchRooms(), this.fetchTeachers(), this.fetchAcademicYears()]);
            this.fetchGrades();
        },

        async fetchRooms() {
            try {
                const res  = await fetch(this.roomsUrl, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
                const json = await res.json();
                this.rooms = json.data ?? [];
            } catch (_) {}
        },

        async fetchTeachers() {
            try {
                const res  = await fetch(this.teachersUrl, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
                const json = await res.json();
                this.teachers = json.data ?? [];
            } catch (_) {}
        },

        async fetchAcademicYears() {
            try {
                const res  = await fetch(this.academicYearsUrl, { headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" } });
                const json = await res.json();
                this.academicYears = json.data ?? [];
            } catch (_) {}
        },

        // ─── Fetch Grades ───────────────────────────────────────────────
        async fetchGrades(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()      ? { search: this.search.trim() }                : {}),
                    ...(this.statusFilter       ? { status: this.statusFilter }                 : {}),
                    ...(this.roomFilter         ? { room_id: this.roomFilter }                  : {}),
                    ...(this.academicYearFilter ? { academic_year_id: this.academicYearFilter } : {}),
                });

                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error("Gagal memuat data.");

                const json  = await res.json();
                this.grades = json.data;
                this.meta   = json.meta;
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan saat memuat data.");
            } finally {
                this.loading = false;
            }
        },

        // ─── Toolbar filter helpers ─────────────────────────────────────
        statusFilterLabel() {
            return { active:"Aktif", inactive:"Non-Aktif", graduated:"Lulus", archived:"Diarsipkan" }[this.statusFilter] ?? "Semua Status";
        },
        roomFilterLabel() {
            if (!this.roomFilter) return "Semua Ruangan";
            return this.rooms.find(r => String(r.room_id) === String(this.roomFilter))?.room_name ?? "Semua Ruangan";
        },
        academicYearFilterLabel() {
            if (!this.academicYearFilter) return "Semua Tahun Ajaran";
            return this.academicYears.find(ay => String(ay.academic_year_id) === String(this.academicYearFilter))?.academic_year_name ?? "Semua Tahun Ajaran";
        },
        setStatusFilter(val)      { this.statusFilter = val;      this.filterStatusOpen = false;       this.fetchGrades(); },
        setRoomFilter(val)        { this.roomFilter = val;        this.filterRoomOpen = false;         this.fetchGrades(); },
        setAcademicYearFilter(val){ this.academicYearFilter = val; this.filterAcademicYearOpen = false; this.fetchGrades(); },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchGrades(page);
        },

        // ─── Toggle Status ──────────────────────────────────────────────
        async toggleStatus(grade) {
            const nextLabel = { active:"Non-Aktifkan", inactive:"Aktifkan", graduated:"Arsipkan", archived:"Non-Aktifkan" };

            const result = await Swal.fire({
                title:              `${nextLabel[grade.status] ?? "Ubah status"} kelas "${grade.grade_name}"?`,
                text:               "Status kelas akan diperbarui.",
                icon:               "question",
                showCancelButton:   true,
                confirmButtonColor: "#3B82F6",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  "Ya, Ubah!",
                cancelButtonText:   "Batal",
                customClass: { popup: "swal-popup-rounded" },
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.toggleStatusUrl}/${grade.grade_id}/toggle-status`, {
                    method: "PATCH",
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal mengubah status.");
                this.fetchGrades(this.meta.current_page);
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Modal ──────────────────────────────────────────────────────
        openCreateModal() {
            this.isEditing = false; this.editingId = null;
            this.resetForm(); this.errors = {}; this.closeAllSelects();
            this.showModal = true;
        },

        async openEditModal(id) {
            this.isEditing = true; this.editingId = id;
            this.errors = {}; this.resetForm(); this.closeAllSelects();
            this.showModal = true;
            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error("Gagal mengambil data kelas.");
                const data = await res.json();
                this.form.grade_name          = data.grade_name          ?? "";
                this.form.level               = data.level               ?? "";
                this.form.academic_year_id    = data.academic_year_id    ?? "";
                this.form.room_id             = data.room_id             ?? "";
                this.form.homeroom_teacher_id = data.homeroom_teacher_id ?? "";
                this.form.status              = data.status              ?? "active";
            } catch (err) {
                this.closeModal();
                this.showToast("error", err.message);
            }
        },

        resetForm() {
            this.form = { grade_name: "", level: "", academic_year_id: "", room_id: "", homeroom_teacher_id: "", status: "active" };
        },

        closeModal() {
            this.showModal = false; this.isEditing = false; this.editingId = null;
            this.submitting = false; this.errors = {}; this.closeAllSelects();
        },

        validate() {
            this.errors = {};
            if (!this.form.grade_name.trim()) this.errors.grade_name       = "Nama kelas wajib diisi.";
            if (!this.form.level)             this.errors.level             = "Tingkatan wajib dipilih.";
            if (!this.form.academic_year_id)  this.errors.academic_year_id = "Tahun ajaran wajib dipilih.";
            return Object.keys(this.errors).length === 0;
        },

        async submitForm() {
            if (!this.validate()) return;
            this.submitting = true;
            try {
                const url    = this.isEditing ? `${this.updateUrl}/${this.editingId}` : this.storeUrl;
                const method = this.isEditing ? "PUT" : "POST";

                const res  = await fetch(url, {
                    method,
                    headers: { "Content-Type": "application/json", Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                    body: JSON.stringify({
                        grade_name:          this.form.grade_name.trim(),
                        level:               parseInt(this.form.level, 10),
                        academic_year_id:    this.form.academic_year_id,
                        room_id:             this.form.room_id             || null,
                        homeroom_teacher_id: this.form.homeroom_teacher_id || null,
                        status:              this.form.status,
                    }),
                });
                const json = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(Object.entries(json.errors).map(([k,v]) => [k, v[0]]));
                        return;
                    }
                    throw new Error(json.message ?? "Gagal menyimpan data.");
                }

                this.closeModal();
                this.fetchGrades(this.meta.current_page);
                this.showToast("success", this.isEditing ? "Kelas berhasil diperbarui." : "Kelas berhasil ditambahkan.");
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        async deleteGrade(id, name) {
            const result = await Swal.fire({
                title: `Hapus Kelas "${name}"?`, text: "Data yang dihapus tidak dapat dikembalikan.",
                icon: "warning", showCancelButton: true,
                confirmButtonColor: "#EF4444", cancelButtonColor: "#94A3B8",
                confirmButtonText: "Ya, Hapus!", cancelButtonText: "Batal",
                customClass: { popup: "swal-popup-rounded" },
            });
            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.destroyUrl}/${id}`, {
                    method: "DELETE",
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal menghapus kelas.");

                const maxPage = Math.ceil((this.meta.total - 1) / this.meta.per_page) || 1;
                this.fetchGrades(Math.min(this.meta.current_page, maxPage));
                this.showToast("success", `Kelas "${name}" berhasil dihapus.`);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        showToast(icon, message) {
            Swal.fire({
                toast: true, position: "top-end", icon, title: message,
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
                didOpen: t => {
                    t.addEventListener("mouseenter", Swal.stopTimer);
                    t.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
        },

        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? "";
        },
    };
}