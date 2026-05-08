import Swal from "sweetalert2";

export default function roomsSearch(config = {}) {
    return {
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:        config.indexUrl        ?? "/rooms",
        storeUrl:        config.storeUrl        ?? "/rooms",
        showUrl:         config.showUrl         ?? "/rooms",
        updateUrl:       config.updateUrl       ?? "/rooms",
        destroyUrl:      config.destroyUrl      ?? "/rooms",
        toggleStatusUrl: config.toggleStatusUrl ?? "/rooms",

        // ─── State ─────────────────────────────────────────────────────
        rooms:         [],
        meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        search:        "",
        perPage:       10,
        statusFilter:  "",
        typeFilter:    "",
        floorFilter:   "",
        buildingFilter:"",
        loading:       false,

        // ─── Modal ─────────────────────────────────────────────────────
        showModal:  false,
        isEditing:  false,
        editingId:  null,
        submitting: false,
        form: {
            room_name:  "",
            code:       "",
            type:       "",
            floor:      "",
            building:   "",
            capacity:   "",
            facilitiy:  "",
            status:     "inactive",
        },
        errors: {},

        // ─── Dropdown filter ───────────────────────────────────────────
        filterOpen:        false,
        typeFilterOpen:    false,
        floorFilterOpen:   false,
        buildingFilterOpen:false,

        // ─── Avatar Helpers ────────────────────────────────────────────
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

        // ─── Init ──────────────────────────────────────────────────────
        async init() {
            this.fetchRooms();
        },

        // ─── Fetch Rooms ───────────────────────────────────────────────
        async fetchRooms(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()          ? { search:    this.search.trim()  } : {}),
                    ...(this.statusFilter   !== ""  ? { status:    this.statusFilter   } : {}),
                    ...(this.typeFilter     !== ""  ? { type:      this.typeFilter     } : {}),
                    ...(this.floorFilter    !== ""  ? { floor:     this.floorFilter    } : {}),
                    ...(this.buildingFilter !== ""  ? { building:  this.buildingFilter } : {}),
                });

                const res = await fetch(`${this.indexUrl}?${params}`, {
                    headers: {
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!res.ok) throw new Error("Gagal memuat data.");

                const json  = await res.json();
                this.rooms  = json.data;
                this.meta   = json.meta;
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
            this.fetchRooms();
        },

        setTypeFilter(val) {
            this.typeFilter     = val;
            this.typeFilterOpen = false;
            this.fetchRooms();
        },

        setFloorFilter(val) {
            this.floorFilter     = val;
            this.floorFilterOpen = false;
            this.fetchRooms();
        },

        setBuildingFilter(val) {
            this.buildingFilter     = val;
            this.buildingFilterOpen = false;
            this.fetchRooms();
        },

        // ─── Pagination ────────────────────────────────────────────────
        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchRooms(page);
        },

        // ─── Toggle Status ─────────────────────────────────────────────
        async toggleStatus(room) {

            const nextStatusMap = {
                available:   "maintenance",
                maintenance: "inactive",
                inactive:    "available",
            };

            const nextLabelMap = {
                available:   "Maintenance",
                maintenance: "Non-Aktif",
                inactive:    "Tersedia",
            };

            const nextColorMap = {
                available:   "#F59E0B",
                maintenance: "#EF4444",
                inactive:    "#10B981",
            };

            const currentLabelMap = {
                available:   "Tersedia",
                maintenance: "Maintenance",
                inactive:    "Non-Aktif",
            };

            const nextStatus = nextStatusMap[room.status];
            const nextLabel  = nextLabelMap[room.status];

            const result = await Swal.fire({
                title: `Ubah status ruangan "${room.room_name}"?`,
                html: `
                    <div style="font-size:14px;color:#475569">
                        Status akan diubah dari
                        <b>${currentLabelMap[room.status]}</b>
                        menjadi
                        <b>${nextLabel}</b>.
                    </div>
                `,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: nextColorMap[room.status],
                cancelButtonColor: "#94A3B8",
                confirmButtonText: `Ya, Ubah`,
                cancelButtonText: "Batal",
                customClass: {
                    popup: "swal-popup-rounded",
                },
            });

            if (!result.isConfirmed) return;

            try {

                const res = await fetch(
                    `${this.toggleStatusUrl}/${room.room_id}/toggle-status`,
                    {
                        method: "PATCH",
                        headers: {
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": this.getCsrfToken(),
                        },
                    }
                );

                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.message ?? "Gagal mengubah status.");
                }

                this.fetchRooms(this.meta.current_page);

                this.showToast("success", json.message);

            } catch (err) {

                this.showToast(
                    "error",
                    err.message ?? "Terjadi kesalahan."
                );

            }
        },

        // ─── Modal Helpers ─────────────────────────────────────────────
        openCreateModal() {
            this.isEditing = false;
            this.editingId = null;
            this.form      = {
                room_name: "",
                code:      "",
                type:      "",
                floor:     "",
                building:  "",
                capacity:  "",
                facilitiy: "",
                status:    "inactive",
            };
            this.errors    = {};
            this.showModal = true;
        },

        async openEditModal(id) {
            this.isEditing = true;
            this.editingId = id;
            this.errors    = {};
            this.form      = {
                room_name: "",
                code:      "",
                type:      "",
                floor:     "",
                building:  "",
                capacity:  "",
                facilitiy: "",
                status:    "inactive",
            };
            this.showModal = true;

            try {
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: {
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                if (!res.ok) throw new Error("Gagal mengambil data ruangan.");
                const data         = await res.json();
                this.form.room_name = data.room_name ?? "";
                this.form.code      = data.code      ?? "";
                this.form.type      = data.type      ?? "";
                this.form.floor     = data.floor      ?? "";
                this.form.building  = data.building  ?? "";
                this.form.capacity  = data.capacity  ?? "";
                this.form.facilitiy = data.facilitiy ?? "";
                this.form.status    = data.status    ?? "inactive";
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

            if (!this.form.room_name.trim()) {
                this.errors.room_name = "Nama ruangan wajib diisi.";
            }

            if (!this.form.code.trim()) {
                this.errors.code = "Kode ruangan wajib diisi.";
            }

            if (!this.form.type) {
                this.errors.type = "Tipe ruangan wajib dipilih.";
            }

            if (!this.form.capacity || isNaN(this.form.capacity) || Number(this.form.capacity) < 1) {
                this.errors.capacity = "Kapasitas wajib diisi dan harus berupa angka positif.";
            }

            return Object.keys(this.errors).length === 0;
        },

        // ─── Submit ────────────────────────────────────────────────────
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
                        "Content-Type":     "application/json",
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                    body: JSON.stringify({
                        room_name: this.form.room_name.trim(),
                        code:      this.form.code.trim(),
                        type:      this.form.type,
                        floor:     this.form.floor     || null,
                        building:  this.form.building  || null,
                        capacity:  Number(this.form.capacity),
                        facilitiy: this.form.facilitiy || null,
                        status:    this.form.status,
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
                this.fetchRooms(this.meta.current_page);
                this.showToast(
                    "success",
                    this.isEditing ? "Ruangan berhasil diperbarui." : "Ruangan berhasil ditambahkan."
                );
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        // ─── Delete ────────────────────────────────────────────────────
        async deleteRoom(id, name) {
            const result = await Swal.fire({
                title:              `Hapus Ruangan "${name}"?`,
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
                    throw new Error(json.message ?? "Gagal menghapus ruangan.");
                }

                const newTotal   = this.meta.total - 1;
                const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                const targetPage = Math.min(this.meta.current_page, maxPage);

                this.fetchRooms(targetPage);
                this.showToast("success", `Ruangan "${name}" berhasil dihapus.`);
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