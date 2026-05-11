import Swal from "sweetalert2";

export default function subjectSearch(config = {}) {
    return {
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:        config.indexUrl        ?? "/subjects",
        storeUrl:        config.storeUrl        ?? "/subjects",
        showUrl:         config.showUrl         ?? "/subjects",
        updateUrl:       config.updateUrl       ?? "/subjects",
        destroyUrl:      config.destroyUrl      ?? "/subjects",
        toggleStatusUrl: config.toggleStatusUrl ?? "/subjects",

        // ─── State ─────────────────────────────────────────────────────
        subjects: [],
        meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        search:       "",
        perPage:      10,
        statusFilter: "",   // "" | "active" | "inactive"
        loading:      false,
        activeCount:  0,
        totalCredits: 0,

        // ─── Modal ─────────────────────────────────────────────────────
        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        form: {
            subject_name:  "",
            subject_code:  "",
            category:      "",
            credits:       "",
            hours_per_week:"",
            description:   "",
            status:        "active",
        },
        errors: {},

        // ─── Dropdown filter ───────────────────────────────────────────
        filterOpen: false,

        // ─── Avatar helpers ────────────────────────────────────────────
        _avatarColors: ["av-blue","av-violet","av-green","av-pink","av-teal","av-red","av-indigo"],

        avatarColor(i) {
            return this._avatarColors[i % this._avatarColors.length];
        },

        initials(name) {
            return (name || "")
                .split(/[-_ ]/)
                .map(w => w[0]?.toUpperCase() || "")
                .join("")
                .slice(0, 2);
        },

        defaultForm() {
            return {
                subject_name:   "",
                subject_code:   "",
                category:       "",
                credits:        "",
                hours_per_week: "",
                description:    "",
                status:         "active",
            };
        },

        // ─── Init ──────────────────────────────────────────────────────
        init() {
            this.fetchSubjects();
        },

        // ─── Fetch ─────────────────────────────────────────────────────
        async fetchSubjects(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()       ? { search: this.search.trim() } : {}),
                    ...(this.statusFilter !== "" ? { status: this.statusFilter }  : {}),
                });

                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!res.ok) throw new Error("Gagal memuat data.");

                const json = await res.json();
                this.subjects = json.data;
                this.meta     = json.meta;

                // Update stat cards
                this.activeCount  = json.data.filter(s => s.status === "active").length;
                this.totalCredits = json.data.reduce((sum, s) => sum + (parseInt(s.credits) || 0), 0);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan saat memuat data.");
            } finally {
                this.loading = false;
            }
        },

        // ─── Filter helpers ────────────────────────────────────────────
        filterLabel() {
            if (this.statusFilter === "active")   return "Aktif";
            if (this.statusFilter === "inactive") return "Non-Aktif";
            return "Semua Status";
        },

        setStatusFilter(val) {
            this.statusFilter = val;
            this.filterOpen   = false;
            this.fetchSubjects();
        },

        // ─── Pagination ────────────────────────────────────────────────
        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchSubjects(page);
        },

        // ─── Toggle Status ─────────────────────────────────────────────
        async toggleStatus(subject) {
            const willBeActive = subject.status !== "active";
            const result = await Swal.fire({
                title: `${willBeActive ? "Aktifkan" : "Nonaktifkan"} "${subject.subject_name}"?`,
                text:  `Mata pelajaran akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
                icon:  "question",
                showCancelButton:   true,
                confirmButtonColor: willBeActive ? "#10B981" : "#F59E0B",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  `Ya, ${willBeActive ? "Aktifkan" : "Nonaktifkan"}!`,
                cancelButtonText:   "Batal",
                customClass: { popup: "swal-popup-rounded" },
            });
            if (!result.isConfirmed) return;

            try {
                const res = await fetch(`${this.toggleStatusUrl}/${subject.id}/toggle-status`, {
                    method:  "PATCH",
                    headers: {
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal mengubah status.");

                // Update in-place – no full reload needed
                subject.status = json.status;
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Modal helpers ─────────────────────────────────────────────
        openCreateModal() {
            this.isEditing = false;
            this.editingId = null;
            this.form      = this.defaultForm();
            this.errors    = {};
            this.showModal = true;
        },

        async openEditModal(id) {
            this.isEditing = true;
            this.editingId = id;
            this.errors    = {};
            this.form      = this.defaultForm();
            this.showModal = true;

            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                if (!res.ok) throw new Error("Gagal mengambil data mata pelajaran.");
                const data = await res.json();

                this.form.subject_name   = data.subject_name   ?? "";
                this.form.subject_code   = data.subject_code   ?? "";
                this.form.category       = data.category       ?? "";
                this.form.credits        = data.credits        ?? "";
                this.form.hours_per_week = data.hours_per_week ?? "";
                this.form.description    = data.description    ?? "";
                this.form.status         = data.status         ?? "active";
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

        // ─── Validation ────────────────────────────────────────────────
        validate() {
            this.errors = {};
            if (!this.form.subject_name.trim()) {
                this.errors.subject_name = "Nama mata pelajaran wajib diisi.";
            }
            if (!this.form.subject_code.trim()) {
                this.errors.subject_code = "Kode mata pelajaran wajib diisi.";
            }
            return Object.keys(this.errors).length === 0;
        },

        // ─── Submit ────────────────────────────────────────────────────
        async submitForm() {
            if (!this.validate()) return;

            this.submitting = true;
            try {
                const url    = this.isEditing ? `${this.updateUrl}/${this.editingId}` : this.storeUrl;
                const method = this.isEditing ? "PUT" : "POST";

                const res = await fetch(url, {
                    method,
                    headers: {
                        "Content-Type":     "application/json",
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                    body: JSON.stringify({
                        subject_name:   this.form.subject_name.trim(),
                        subject_code:   this.form.subject_code.trim(),
                        category:       this.form.category.trim(),
                        credits:        this.form.credits,
                        hours_per_week: this.form.hours_per_week,
                        description:    this.form.description.trim(),
                        status:         this.form.status,
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
                this.fetchSubjects(this.meta.current_page);
                this.showToast(
                    "success",
                    this.isEditing
                        ? "Mata pelajaran berhasil diperbarui."
                        : "Mata pelajaran berhasil ditambahkan."
                );
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        // ─── Delete ────────────────────────────────────────────────────
        async deleteSubject(id, name) {
            const result = await Swal.fire({
                title:              `Hapus "${name}"?`,
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
                const res = await fetch(`${this.destroyUrl}/${id}`, {
                    method:  "DELETE",
                    headers: {
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                });
                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.message ?? "Gagal menghapus mata pelajaran.");
                }

                const newTotal   = this.meta.total - 1;
                const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                const targetPage = Math.min(this.meta.current_page, maxPage);

                this.fetchSubjects(targetPage);
                this.showToast("success", `"${name}" berhasil dihapus.`);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Toast ─────────────────────────────────────────────────────
        showToast(icon, message) {
            Swal.fire({
                toast:            true,
                position:         "top-end",
                icon,
                title:            message,
                showConfirmButton: false,
                timer:            3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });
        },

        // ─── CSRF ──────────────────────────────────────────────────────
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? "";
        },
    };
}