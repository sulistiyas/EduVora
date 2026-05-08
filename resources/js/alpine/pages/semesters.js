import Swal from "sweetalert2";

export default function semesterSearch(config = {}) {
    return {
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:        config.indexUrl        ?? "/semesters",
        storeUrl:        config.storeUrl        ?? "/semesters",
        showUrl:         config.showUrl         ?? "/semesters",
        updateUrl:       config.updateUrl       ?? "/semesters",
        destroyUrl:      config.destroyUrl      ?? "/semesters",
        toggleStatusUrl: config.toggleStatusUrl ?? "/semesters",
        academicYearUrl: config.academicYearUrl ?? "/academic-year", // untuk dropdown

        // ─── State ─────────────────────────────────────────────────────
        semesters:      [],
        academicYears:  [], // untuk dropdown filter & form
        meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        search:              "",
        perPage:             10,
        statusFilter:        "",  // "" | "active" | "inactive"
        academicYearFilter:  "",  // academic_year_id filter
        loading:             false,

        // ─── Modal ─────────────────────────────────────────────────────
        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        form: {
            semester_name:      "",
            academic_year_id:   "",
            start_date:         "",
            end_date:           "",
            midterm_start_date: "",
            midterm_end_date:   "",
            final_start_date:   "",
            final_end_date:     "",
            status:             "inactive",
        },
        errors: {},

        // ─── Dropdown filter ───────────────────────────────────────────
        filterOpen:           false,
        academicYearFilterOpen: false,

        // ─── Avatar Helpers ─────────────────────────────────────────────
        _avatarColors: ["av-blue", "av-violet", "av-green", "av-pink", "av-teal", "av-red", "av-indigo"],

        avatarColor(index) {
            return this._avatarColors[index % this._avatarColors.length];
        },

        initials(name) {
            return (name || "")
                .split(/[-_ ]/)
                .map(w => w[0]?.toUpperCase() || "")
                .join("")
                .slice(0, 2);
        },

        // ─── Init ───────────────────────────────────────────────────────
        async init() {
            await this.fetchAcademicYears();
            this.fetchSemesters();
        },

        // ─── Fetch Academic Years (untuk dropdown) ─────────────────────
        async fetchAcademicYears() {
            try {
                const res = await fetch(this.academicYearUrl, {  
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                if (!res.ok) return;
                const json = await res.json();
                this.academicYears = json.data ?? [];
            } catch (_) {
                // silent — dropdown kosong tidak fatal
            }
        },

        // ─── Fetch Semesters ───────────────────────────────────────────
        async fetchSemesters(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()             ? { search: this.search.trim() }                   : {}),
                    ...(this.statusFilter !== ""        ? { status: this.statusFilter }                    : {}),
                    ...(this.academicYearFilter !== ""  ? { academic_year_id: this.academicYearFilter }    : {}),
                });

                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!res.ok) throw new Error("Gagal memuat data.");

                const json = await res.json();
                this.semesters = json.data;
                this.meta      = json.meta;
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan saat memuat data.");
            } finally {
                this.loading = false;
            }
        },

        // ─── Filter Helpers ────────────────────────────────────────────
        filterLabel() {
            if (this.statusFilter === "active")   return "Aktif";
            if (this.statusFilter === "inactive") return "Non-Aktif";
            return "Semua Status";
        },

        academicYearFilterLabel() {
            if (!this.academicYearFilter) return "Semua Tahun Ajaran";
            const found = this.academicYears.find(
                ay => String(ay.academic_year_id) === String(this.academicYearFilter)
            );
            return found ? found.academic_year_name : "Semua Tahun Ajaran";
        },

        setStatusFilter(val) {
            this.statusFilter = val;
            this.filterOpen   = false;
            this.fetchSemesters();
        },

        setAcademicYearFilter(val) {
            this.academicYearFilter      = val;
            this.academicYearFilterOpen  = false;
            this.fetchSemesters();
        },

        // ─── Pagination ─────────────────────────────────────────────────
        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchSemesters(page);
        },

        // ─── Toggle Status ─────────────────────────────────────────────
        async toggleStatus(semester) {
            const willBeActive = semester.status !== "active";

            const result = await Swal.fire({
                title:              `${willBeActive ? "Aktifkan" : "Nonaktifkan"} semester "${semester.semester_name}"?`,
                text:               `Semester akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
                icon:               "question",
                showCancelButton:   true,
                confirmButtonColor: willBeActive ? "#10B981" : "#F59E0B",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  `Ya, ${willBeActive ? "Aktifkan" : "Nonaktifkan"}!`,
                cancelButtonText:   "Batal",
                customClass: { popup: "swal-popup-rounded" },
            });

            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.toggleStatusUrl}/${semester.semester_id}/toggle-status`, {
                    method:  "PATCH",
                    headers: {
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.message ?? "Gagal mengubah status.");
                }

                // Refetch supaya semester lain yang ikut dinonaktifkan juga terupdate
                this.fetchSemesters(this.meta.current_page);
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Modal Helpers ───────────────────────────────────────────────
        openCreateModal() {
            this.isEditing = false;
            this.editingId = null;
            this.form      = {
                semester_name:      "",
                academic_year_id:   "",
                start_date:         "",
                end_date:           "",
                midterm_start_date: "",
                midterm_end_date:   "",
                final_start_date:   "",
                final_end_date:     "",
                status:             "inactive",
            };
            this.errors    = {};
            this.showModal = true;
        },

        async openEditModal(id) {
            this.isEditing = true;
            this.editingId = id;
            this.errors    = {};
            this.form      = {
                semester_name:      "",
                academic_year_id:   "",
                start_date:         "",
                end_date:           "",
                midterm_start_date: "",
                midterm_end_date:   "",
                final_start_date:   "",
                final_end_date:     "",
                status:             "inactive",
            };
            this.showModal = true;

            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                if (!res.ok) throw new Error("Gagal mengambil data Semester.");
                const data = await res.json();
                this.form.semester_name      = data.semester_name      ?? "";
                this.form.academic_year_id   = data.academic_year_id   ?? "";
                this.form.start_date         = data.start_date         ?? "";
                this.form.end_date           = data.end_date           ?? "";
                this.form.midterm_start_date = data.midterm_start_date ?? "";
                this.form.midterm_end_date   = data.midterm_end_date   ?? "";
                this.form.final_start_date   = data.final_start_date   ?? "";
                this.form.final_end_date     = data.final_end_date     ?? "";
                this.form.status             = data.status             ?? "inactive";
            } catch (err) {
                this.closeModal();
                this.showToast("error", err.message);
            }
        },

        closeModal() {
            this.showModal  = false;
            this.isEditing  = false;
            this.editingId  = null;
            this.submitting = false;
            this.errors     = {};
        },

        // ─── Validation ─────────────────────────────────────────────────
        validate() {
            this.errors = {};

            if (!this.form.semester_name.trim()) {
                this.errors.semester_name = "Nama semester wajib diisi.";
            }

            if (!this.form.academic_year_id) {
                this.errors.academic_year_id = "Tahun ajaran wajib dipilih.";
            }

            if (!this.form.start_date) {
                this.errors.start_date = "Tanggal mulai wajib diisi.";
            }

            if (!this.form.end_date) {
                this.errors.end_date = "Tanggal selesai wajib diisi.";
            }

            if (this.form.start_date && this.form.end_date && this.form.end_date <= this.form.start_date) {
                this.errors.end_date = "Tanggal selesai harus setelah tanggal mulai.";
            }

            return Object.keys(this.errors).length === 0;
        },

        // ─── Submit ─────────────────────────────────────────────────────
        async submitForm() {
            if (!this.validate()) return;

            this.submitting = true;
            try {
                const url    = this.isEditing
                    ? `${this.updateUrl}/${this.editingId}`
                    : this.storeUrl;
                const method = this.isEditing ? "PUT" : "POST";

                const res  = await fetch(url, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        Accept:         "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": this.getCsrfToken(),
                    },
                    body: JSON.stringify({
                        semester_name:      this.form.semester_name.trim(),
                        academic_year_id:   this.form.academic_year_id,
                        start_date:         this.form.start_date,
                        end_date:           this.form.end_date,
                        midterm_start_date: this.form.midterm_start_date || null,
                        midterm_end_date:   this.form.midterm_end_date   || null,
                        final_start_date:   this.form.final_start_date   || null,
                        final_end_date:     this.form.final_end_date     || null,
                        status:             this.form.status,
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
                    throw new Error(json.message ?? "Gagal menyimpan data.");
                }

                this.closeModal();
                this.fetchSemesters(this.meta.current_page);
                this.showToast(
                    "success",
                    this.isEditing ? "Semester berhasil diperbarui." : "Semester berhasil ditambahkan."
                );
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        // ─── Delete ─────────────────────────────────────────────────────
        async deleteSemester(id, name) {
            const result = await Swal.fire({
                title:              `Hapus Semester "${name}"?`,
                text:               "Data yang dihapus tidak dapat dikembalikan.",
                icon:               "warning",
                showCancelButton:   true,
                confirmButtonColor: "#EF4444",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  "Ya, Hapus!",
                cancelButtonText:   "Batal",
                customClass: {
                    popup:         "swal-popup-rounded",
                    confirmButton: "swal-btn-danger",
                    cancelButton:  "swal-btn-cancel",
                },
            });

            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.destroyUrl}/${id}`, {
                    method:  "DELETE",
                    headers: {
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.message ?? "Gagal menghapus Semester.");
                }

                const newTotal   = this.meta.total - 1;
                const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                const targetPage = Math.min(this.meta.current_page, maxPage);

                this.fetchSemesters(targetPage);
                this.showToast("success", `Semester "${name}" berhasil dihapus.`);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Toast ──────────────────────────────────────────────────────
        showToast(icon, message) {
            Swal.fire({
                toast:             true,
                position:          "top-end",
                icon,
                title:             message,
                showConfirmButton:  false,
                timer:             3500,
                timerProgressBar:  true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
        },

        // ─── CSRF ───────────────────────────────────────────────────────
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? "";
        },
    };
}