import Swal from "sweetalert2";

export default function academicYearSearch(config = {}) {
    return{
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:           config.indexUrl   ?? "/academic-year",
        storeUrl:           config.storeUrl   ?? "/academic-year",
        showUrl:            config.showUrl    ?? "/academic-year",
        updateUrl:          config.updateUrl  ?? "/academic-year",
        destroyUrl:         config.destroyUrl ?? "/academic-year",
        toggleStatusUrl:    config.toggleStatusUrl ?? "/academic-year",

        // ─── State ─────────────────────────────────────────────────────
        academicYears:    [],
        meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        search:   "",
        perPage:  10,
        statusFilter: "",   // "" | "active" | "inactive"
        loading:  false,

        // ─── Modal ─────────────────────────────────────────────────────
        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        form: { academic_year_name: "", start_date: "", end_date: "", status: "" },
        errors: {},
        
        // ─── Dropdown filter ───────────────────────────────────────────
        filterOpen: false,
        // ─── Avatar Helpers ─────────────────────────────────────────────
        _avatarColors: ["av-blue","av-violet","av-green","av-pink","av-teal","av-red","av-indigo"],

        avatarColor(index) {
            return this._avatarColors[index % this._avatarColors.length];
        },

        initials(name) {
            return (name || "")
                .split(/[-_ ]/)                         // split by dash, underscore, or space
                .map(w => w[0]?.toUpperCase() || "")
                .join("")
                .slice(0, 2);
        },

        // ─── Init ───────────────────────────────────────────────────────
        init() {
            this.fetchAcademicYears();
        },

        // ─── Fetch Academic Years ──────────────────────────────────────
        async fetchAcademicYears(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()        ? { search: this.search.trim() }    : {}),
                    ...(this.statusFilter !== ""  ? { status: this.statusFilter }     : {}),
                });

                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!res.ok) throw new Error("Gagal memuat data.");

                const json = await res.json();
                this.academicYears = json.data;
                this.meta  = json.meta;
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

        setStatusFilter(val) {
            this.statusFilter = val;
            this.filterOpen   = false;
            this.fetchAcademicYears();
        },

        // ─── Pagination ─────────────────────────────────────────────────
        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchAcademicYears(page);
        },

        // ─── Toggle Status ─────────────────────────────────────────────
        async toggleStatus(academicYear) {
            const willBeActive = academicYear.status !== "active";

            const result = await Swal.fire({
                title:              `${willBeActive ? "Aktifkan" : "Nonaktifkan"} academicYear "${academicYear.academic_year_name}"?`,
                text:               `Academic Year akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
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
                const res  = await fetch(`${this.toggleStatusUrl}/${academicYear.academic_year_id}/toggle-status`, {
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

                // Update lokal optimistik — tidak perlu refetch
                // academicYear.status = json.status;
                this.fetchAcademicYears(this.meta.current_page);
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Modal Helpers ───────────────────────────────────────────────
        openCreateModal() {
            this.isEditing  = false;
            this.editingId  = null;
            this.form       = { academic_year_name: "", start_date: "", end_date: "", status: "inactive" };
            this.errors     = {};
            this.showModal  = true;
        },

        async openEditModal(id) {
            this.isEditing  = true;
            this.editingId  = id;
            this.errors     = {};
            this.form       = { academic_year_name: "", start_date: "", end_date: "", status: "inactive" };
            this.showModal  = true;

            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                if (!res.ok) throw new Error("Gagal mengambil data Academic Year.");
                const data = await res.json();
                this.form.academic_year_name        = data.academic_year_name        ?? "";
                this.form.start_date = data.start_date ?? "";
                this.form.end_date = data.end_date ?? "";
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
            const name = this.form.academic_year_name.trim();
            if (!name) {
                this.errors.academic_year_name = "Nama tahun ajaran wajib diisi.";
            } else if (!/^\d{4}\/\d{4}$/.test(name)) {
                this.errors.academic_year_name = "Format harus seperti 2024/2025.";
            } else {
                const [start, end] = name.split("/").map(Number);
                if (end !== start + 1) {
                    this.errors.academic_year_name = "Tahun akhir harus tepat 1 tahun setelah tahun awal (contoh: 2024/2025).";
                }
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
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": this.getCsrfToken(),
                    },
                    body: JSON.stringify({
                        academic_year_name:        this.form.academic_year_name.trim(),
                        start_date:                this.form.start_date.trim(),
                        end_date:                  this.form.end_date.trim(),
                        status:                    this.form.status.trim(),
                    }),
                });
                const json = await res.json();

                if (!res.ok) {
                    // Laravel 422 validation errors
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(
                            Object.entries(json.errors).map(([k, v]) => [k, v[0]])
                        );
                        return;
                    }
                    throw new Error(json.message ?? "Gagal menyimpan data.");
                }

                this.closeModal();
                this.fetchAcademicYears(this.meta.current_page);
                this.showToast(
                    "success",
                    this.isEditing ? "Academic Year berhasil diperbarui." : "Academic Year berhasil ditambahkan."
                );
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        // ─── Delete ─────────────────────────────────────────────────────
        async deleteAcademicYear(id, name) {
            const result = await Swal.fire({
                title:              `Hapus Academic Year "${name}"?`,
                text:               "Data yang dihapus tidak dapat dikembalikan.",
                icon:               "warning",
                showCancelButton:   true,
                confirmButtonColor: "#EF4444",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  "Ya, Hapus!",
                cancelButtonText:   "Batal",
                customClass: {
                    popup:        "swal-popup-rounded",
                    confirmButton:"swal-btn-danger",
                    cancelButton: "swal-btn-cancel",
                },
            });

            if (!result.isConfirmed) return;

            try {
                const res  = await fetch(`${this.destroyUrl}/${id}`, {
                    method:  "DELETE",
                    headers: {
                        Accept:               "application/json",
                        "X-Requested-With":   "XMLHttpRequest",
                        "X-CSRF-TOKEN":       this.getCsrfToken(),
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.message ?? "Gagal menghapus Academic Year.");
                }

                // Auto-adjust page jika halaman sekarang kosong setelah hapus
                const newTotal   = this.meta.total - 1;
                const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                const targetPage = Math.min(this.meta.current_page, maxPage);

                this.fetchAcademicYears(targetPage);
                this.showToast("success", `Academic Year "${name}" berhasil dihapus.`);
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