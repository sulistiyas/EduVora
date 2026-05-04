import Swal from "sweetalert2";

export default function roleSearch(config = {}) {
    return {
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:           config.indexUrl   ?? "/roles",
        storeUrl:           config.storeUrl   ?? "/roles",
        showUrl:            config.showUrl    ?? "/roles",
        updateUrl:          config.updateUrl  ?? "/roles",
        destroyUrl:         config.destroyUrl ?? "/roles",
        toggleStatusUrl:    config.toggleStatusUrl ?? "/roles",

        // ─── State ─────────────────────────────────────────────────────
        roles:    [],
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
        form: { role_name: "", role_description: "" },
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
            this.fetchRoles();
        },

        // ─── Fetch ──────────────────────────────────────────────────────
        async fetchRoles(page = 1) {
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
                this.roles = json.data;
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
            this.fetchRoles();
        },

        // ─── Pagination ─────────────────────────────────────────────────
        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchRoles(page);
        },

        // ─── Toggle Status ─────────────────────────────────────────────
        async toggleStatus(role) {
            const willBeActive = role.status !== "active";

            const result = await Swal.fire({
                title:              `${willBeActive ? "Aktifkan" : "Nonaktifkan"} role "${role.role_name}"?`,
                text:               `Role akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
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
                const res  = await fetch(`${this.toggleStatusUrl}/${role.role_id}/toggle-status`, {
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
                role.status = json.status;
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        // ─── Modal Helpers ───────────────────────────────────────────────
        openCreateModal() {
            this.isEditing  = false;
            this.editingId  = null;
            this.form       = { role_name: "", role_description: "" };
            this.errors     = {};
            this.showModal  = true;
        },

        async openEditModal(id) {
            this.isEditing  = true;
            this.editingId  = id;
            this.errors     = {};
            this.form       = { role_name: "", role_description: "" };
            this.showModal  = true;

            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                if (!res.ok) throw new Error("Gagal mengambil data role.");
                const data = await res.json();
                this.form.role_name        = data.role_name        ?? "";
                this.form.role_description = data.role_description ?? "";
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
            const name = this.form.role_name.trim();
            if (!name) {
                this.errors.role_name = "Role name wajib diisi.";
            } else if (!/^[a-z0-9-]+$/.test(name)) {
                // FIX: validasi format lowercase dengan tanda hubung sesuai placeholder
                this.errors.role_name = "Gunakan huruf kecil, angka, dan tanda hubung saja.";
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
                        role_name:        this.form.role_name.trim(),
                        role_description: this.form.role_description.trim(),
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
                this.fetchRoles(this.meta.current_page);
                this.showToast(
                    "success",
                    this.isEditing ? "Role berhasil diperbarui." : "Role berhasil ditambahkan."
                );
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        // ─── Delete ─────────────────────────────────────────────────────
        async deleteRole(id, name) {
            const result = await Swal.fire({
                title:              `Hapus role "${name}"?`,
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
                    throw new Error(json.message ?? "Gagal menghapus role.");
                }

                // Auto-adjust page jika halaman sekarang kosong setelah hapus
                const newTotal   = this.meta.total - 1;
                const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                const targetPage = Math.min(this.meta.current_page, maxPage);

                this.fetchRoles(targetPage);
                this.showToast("success", `Role "${name}" berhasil dihapus.`);
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