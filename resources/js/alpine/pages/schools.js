    import Swal from "sweetalert2";

    // ═══════════════════════════════════════════════════════════════
    //  schoolProfilesSearch  —  halaman INDEX / LIST sekolah
    // ═══════════════════════════════════════════════════════════════
    export function schoolProfilesSearch(config = {}) {
        return {
            // ─── URLs ──────────────────────────────────────────────────────
            indexUrl:        config.indexUrl        ?? "/school-management",
            storeUrl:        config.storeUrl        ?? "/school-management",
            showUrl:         config.showUrl         ?? "/school-management",
            updateUrl:       config.updateUrl       ?? "/school-management",
            destroyUrl:      config.destroyUrl      ?? "/school-management",
            toggleStatusUrl: config.toggleStatusUrl ?? "/school-management",

            // ─── State ─────────────────────────────────────────────────────
            schools:      [],
            meta:         { current_page: 1, per_page: 10, total: 0, last_page: 1 },
            search:       "",
            perPage:      10,
            statusFilter: "",
            loading:      false,
            filterOpen:   false,

            // ─── Modal ─────────────────────────────────────────────────────
            showModal:  false,
            isEditing:  false,
            editingId:  null,
            submitting: false,
            form:       {},
            errors:     {},

            // ─── Init ───────────────────────────────────────────────────────
            init() {
                this.form = this.defaultForm();
                this.fetchSchools();
            },

            defaultForm() {
                return {
                    school_name: "", contact_email: "", contact_phone: "",
                    website: "", kkm_default: "", npsn: "", nss: "",
                    accreditation: "", province: "", city: "", district: "",
                    postal_code: "", headmaster_name: "", headmaster_nip: "",
                    school_type: "", address: "",
                };
            },

            // ─── Style Helpers ──────────────────────────────────────────────
            schoolTypeStyle(type) {
                const map = {
                    'Elementary':  { background: '#ECFDF5', color: '#065F46' },
                    'Junior High': { background: '#EFF6FF', color: '#1D4ED8' },
                    'Senior High': { background: '#F5F3FF', color: '#5B21B6' },
                };
                const s = map[type] || { background: '#F1F5F9', color: '#475569' };
                return `background:${s.background};color:${s.color}`;
            },

            accreditationStyle(grade) {
                const map = {
                    'A': { background: '#ECFDF5', color: '#065F46' },
                    'B': { background: '#EFF6FF', color: '#1D4ED8' },
                    'C': { background: '#FFFBEB', color: '#92400E' },
                    'D': { background: '#FFF7ED', color: '#C2410C' },
                    'E': { background: '#FEF2F2', color: '#991B1B' },
                };
                const s = map[grade] || { background: '#F1F5F9', color: '#475569' };
                return `background:${s.background};color:${s.color}`;
            },

            _avatarColors: ["av-blue","av-violet","av-green","av-pink","av-teal","av-red","av-indigo"],
            avatarColor(index) { return this._avatarColors[index % this._avatarColors.length]; },
            initials(name) {
                return (name || "").split(/[-_ ]/).map(w => w[0]?.toUpperCase() || "").join("").slice(0, 2);
            },

            // ─── Fetch ──────────────────────────────────────────────────────
            async fetchSchools(page = 1) {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        page,
                        per_page: parseInt(this.perPage, 10),
                        ...(this.search.trim()       ? { search: this.search.trim() } : {}),
                        ...(this.statusFilter !== "" ? { status: this.statusFilter }  : {}),
                    });
                    const res  = await fetch(`${this.indexUrl}?${params}`, {
                        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
                    });
                    if (!res.ok) throw new Error("Gagal memuat data.");
                    const json    = await res.json();
                    this.schools  = json.data;
                    this.meta     = json.meta;
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan saat memuat data.");
                } finally {
                    this.loading = false;
                }
            },

            filterLabel() {
                if (this.statusFilter === "active")   return "Aktif";
                if (this.statusFilter === "inactive") return "Non-Aktif";
                return "Semua Status";
            },
            setStatusFilter(val) { this.statusFilter = val; this.filterOpen = false; this.fetchSchools(); },
            changePage(page) {
                if (page < 1 || page > this.meta.last_page) return;
                this.fetchSchools(page);
            },

            // ─── Toggle Status (List) ───────────────────────────────────────
            async toggleStatus(school) {
                const willBeActive = school.status !== "active";
                const result = await Swal.fire({
                    title: `${willBeActive ? "Aktifkan" : "Nonaktifkan"} sekolah "${school.school_name}"?`,
                    text:  `Sekolah akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
                    icon:  "question",
                    showCancelButton: true,
                    confirmButtonColor: willBeActive ? "#10B981" : "#F59E0B",
                    cancelButtonColor:  "#94A3B8",
                    confirmButtonText:  `Ya, ${willBeActive ? "Aktifkan" : "Nonaktifkan"}!`,
                    cancelButtonText:   "Batal",
                    customClass: { popup: "swal-popup-rounded" },
                });
                if (!result.isConfirmed) return;
                try {
                    const res  = await fetch(`${this.toggleStatusUrl}/${school.school_id}/toggle-status`, {
                        method:  "PATCH",
                        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal mengubah status.");
                    school.status = json.status;
                    this.showToast("success", json.message);
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan.");
                }
            },

            // ─── Modal Helpers ───────────────────────────────────────────────
            openCreateModal() {
                this.isEditing = false; this.editingId = null;
                this.form = this.defaultForm(); this.errors = {}; this.showModal = true;
            },
            async goToDetail(id) { window.location.href = `${this.showUrl}/${id}/detail`; },
            async openEditModal(id) {
                this.isEditing = true; this.editingId = id;
                this.errors = {}; this.form = this.defaultForm(); this.showModal = true;
                try {
                    const res = await fetch(`${this.showUrl}/${id}`, {
                        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
                    });
                    if (!res.ok) throw new Error("Gagal mengambil data sekolah.");
                    const data = await res.json();
                    Object.assign(this.form, {
                        school_name: data.school_name ?? "", contact_email: data.contact_email ?? "",
                        contact_phone: data.contact_phone ?? "", website: data.website ?? "",
                        kkm_default: data.kkm_default ?? "", npsn: data.npsn ?? "",
                        nss: data.nss ?? "", accreditation: data.accreditation ?? "",
                        province: data.province ?? "", city: data.city ?? "",
                        district: data.district ?? "", postal_code: data.postal_code ?? "",
                        headmaster_name: data.headmaster_name ?? "", headmaster_nip: data.headmaster_nip ?? "",
                        school_type: data.school_type ?? "", address: data.address ?? "",
                    });
                } catch (err) { this.closeModal(); this.showToast("error", err.message); }
            },
            closeModal() {
                this.showModal = false; this.isEditing = false; this.editingId = null;
                this.submitting = false; this.form = this.defaultForm(); this.errors = {};
            },

            // ─── Validation ─────────────────────────────────────────────────
            validate() {
                this.errors = {};
                if (!this.form.school_name.trim()) this.errors.school_name = "Nama sekolah wajib diisi.";
                if (!this.form.school_type)        this.errors.school_type  = "Tipe sekolah wajib dipilih.";
                return Object.keys(this.errors).length === 0;
            },

            // ─── Submit ─────────────────────────────────────────────────────
            async submitForm() {
                if (!this.validate()) return;
                this.submitting = true;
                try {
                    const url    = this.isEditing ? `${this.updateUrl}/${this.editingId}` : this.storeUrl;
                    const method = this.isEditing ? "PUT" : "POST";
                    const res    = await fetch(url, {
                        method,
                        headers: { "Content-Type": "application/json", Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                        body: JSON.stringify({ ...this.form, school_name: this.form.school_name.trim() }),
                    });
                    const json = await res.json();
                    if (!res.ok) {
                        if (res.status === 422 && json.errors) {
                            this.errors = Object.fromEntries(Object.entries(json.errors).map(([k, v]) => [k, v[0]]));
                            return;
                        }
                        throw new Error(json.message ?? "Gagal menyimpan data.");
                    }
                    this.closeModal();
                    this.fetchSchools(this.meta.current_page);
                    this.showToast("success", this.isEditing ? "Sekolah berhasil diperbarui." : "Sekolah berhasil ditambahkan.");
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan.");
                } finally {
                    this.submitting = false;
                }
            },

            // ─── Delete ─────────────────────────────────────────────────────
            async deleteSchool(id, name) {
                const result = await Swal.fire({
                    title: `Hapus sekolah "${name}"?`,
                    text:  "Data yang dihapus tidak dapat dikembalikan.",
                    icon:  "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF4444", cancelButtonColor: "#94A3B8",
                    confirmButtonText: "Ya, Hapus!", cancelButtonText: "Batal",
                    customClass: { popup: "swal-popup-rounded", confirmButton: "swal-btn-danger", cancelButton: "swal-btn-cancel" },
                });
                if (!result.isConfirmed) return;
                try {
                    const res  = await fetch(`${this.destroyUrl}/${id}`, {
                        method:  "DELETE",
                        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal menghapus sekolah.");
                    const newTotal   = this.meta.total - 1;
                    const maxPage    = Math.ceil(newTotal / this.meta.per_page) || 1;
                    const targetPage = Math.min(this.meta.current_page, maxPage);
                    this.fetchSchools(targetPage);
                    this.showToast("success", `Sekolah "${name}" berhasil dihapus.`);
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan.");
                }
            },

            // ─── Helpers ────────────────────────────────────────────────────
            showToast(icon, message) {
                Swal.fire({
                    toast: true, position: "top-end", icon, title: message,
                    showConfirmButton: false, timer: 3500, timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener("mouseenter", Swal.stopTimer);
                        toast.addEventListener("mouseleave", Swal.resumeTimer);
                    },
                });
            },
            getCsrfToken() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ""; },
        };
    }


    // ═══════════════════════════════════════════════════════════════
    //  schoolDetail  —  halaman DETAIL dengan INLINE EDIT MODE
    // ═══════════════════════════════════════════════════════════════
    export function schoolDetail(config = {}) {
        return {
            // ─── Config ─────────────────────────────────────────────────────
            baseUrl:  config.baseUrl  ?? "/school-management",
            indexUrl: config.indexUrl ?? "/school-management",

            // ─── Reactive school object ─────────────────────────────────────
            school: {
                school_id:       config.schoolId       ?? "",
                school_name:     config.schoolName      ?? "",
                status:          config.schoolStatus    ?? "",
                school_type:     config.schoolType      ?? "",
                accreditation:   config.accreditation   ?? "",
                contact_email:   config.contactEmail    ?? "",
                contact_phone:   config.contactPhone    ?? "",
                website:         config.website         ?? "",
                kkm_default:     config.kkm             ?? "",
                npsn:            config.npsn            ?? "",
                nss:             config.nss             ?? "",
                province:        config.province        ?? "",
                city:            config.city            ?? "",
                district:        config.district        ?? "",
                postal_code:     config.postalCode      ?? "",
                address:         config.address         ?? "",
                headmaster_name: config.headmasterName  ?? "",
                headmaster_nip:  config.headmasterNip   ?? "",
                logo_url:        config.logoUrl         ?? "",
                created_at:      config.createdAt       ?? "",
                updated_at:      config.updatedAt       ?? "",
            },

            // ─── Inline Edit State ──────────────────────────────────────────
            isEditing:  false,
            submitting: false,
            form:       {},
            errors:     {},

            // ─── Computed field arrays ──────────────────────────────────────
            get infoFields() {
                return [
                    { label: "Nama Sekolah", icon: "ri-building-2-line",  key: "school_name"  },
                    { label: "NPSN",         icon: "ri-barcode-line",      key: "npsn",         mono: true },
                    { label: "NSS",          icon: "ri-file-list-3-line",  key: "nss",          mono: true },
                    { label: "Akreditasi",   icon: "ri-medal-line",        key: "accreditation" },
                    { label: "KKM Default",  icon: "ri-bar-chart-line",    key: "kkm_default"   },
                ];
            },

            get contactFields() {
                return [
                    { label: "Email",   icon: "ri-mail-line",   key: "contact_email", href: `mailto:${this.school.contact_email}` },
                    { label: "Telepon", icon: "ri-phone-line",  key: "contact_phone", href: `tel:${this.school.contact_phone}`     },
                    { label: "Website", icon: "ri-global-line", key: "website",       href: this.school.website                    },
                ];
            },

            // ─── Init ───────────────────────────────────────────────────────
            init() { /* data sudah di-pass dari blade */ },

            // ─── Style helpers ──────────────────────────────────────────────
            schoolTypeStyle(type) {
                const map = {
                    Elementary:    { background: "#ECFDF5", color: "#065F46" },
                    "Junior High": { background: "#EFF6FF", color: "#1D4ED8" },
                    "Senior High": { background: "#F5F3FF", color: "#5B21B6" },
                };
                const s = map[type] || { background: "#F1F5F9", color: "#475569" };
                return `background:${s.background};color:${s.color}`;
            },
            accreditationStyle(grade) {
                const map = {
                    A: { background: "#ECFDF5", color: "#065F46" },
                    B: { background: "#EFF6FF", color: "#1D4ED8" },
                    C: { background: "#FFFBEB", color: "#92400E" },
                    D: { background: "#FFF7ED", color: "#C2410C" },
                    E: { background: "#FEF2F2", color: "#991B1B" },
                };
                const s = map[grade] || { background: "#F1F5F9", color: "#475569" };
                return `background:${s.background};color:${s.color}`;
            },
            initials(name) {
                return (name || "").split(/[-_ ]/).map(w => w[0]?.toUpperCase() || "").join("").slice(0, 2);
            },

            // ─── Inline Edit ────────────────────────────────────────────────
            startEdit() {
                // Deep-copy school data ke form
                this.form   = { ...this.school };
                this.errors = {};
                this.isEditing = true;
                this.$nextTick(() => {
                    // Focus ke field pertama
                    const first = document.querySelector('[data-edit-focus]');
                    if (first) first.focus();
                });
            },

            cancelEdit() {
                this.isEditing  = false;
                this.submitting = false;
                this.errors     = {};
                this.form       = {};
            },

            // ─── Validation ─────────────────────────────────────────────────
            validate() {
                this.errors = {};
                if (!this.form.school_name?.trim()) this.errors.school_name = "Nama sekolah wajib diisi.";
                if (!this.form.school_type)         this.errors.school_type  = "Tipe sekolah wajib dipilih.";
                return Object.keys(this.errors).length === 0;
            },

            // ─── Submit Edit ────────────────────────────────────────────────
            async submitEdit() {
                if (!this.validate()) return;
                this.submitting = true;
                try {
                    const res  = await fetch(`${this.baseUrl}/${this.school.school_id}`, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": this.getCsrfToken(),
                        },
                        body: JSON.stringify({ ...this.form }),
                    });
                    const json = await res.json();
                    if (!res.ok) {
                        if (res.status === 422 && json.errors) {
                            this.errors = Object.fromEntries(Object.entries(json.errors).map(([k, v]) => [k, v[0]]));
                            return;
                        }
                        throw new Error(json.message ?? "Gagal menyimpan data.");
                    }

                    // ✅ Update reaktif langsung — tidak perlu reload
                    Object.assign(this.school, { ...this.form });

                    this.isEditing  = false;
                    this.submitting = false;
                    this.form       = {};
                    this.showToast("success", "Data sekolah berhasil diperbarui.");
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan.");
                } finally {
                    this.submitting = false;
                }
            },

            // ─── Toggle Status ──────────────────────────────────────────────
            async toggleStatus() {
                const willBeActive = this.school.status !== "active";
                const result = await Swal.fire({
                    title: `${willBeActive ? "Aktifkan" : "Nonaktifkan"} sekolah ini?`,
                    text:  `Sekolah akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
                    icon:  "question",
                    showCancelButton: true,
                    confirmButtonColor: willBeActive ? "#10B981" : "#F59E0B",
                    cancelButtonColor:  "#94A3B8",
                    confirmButtonText:  `Ya, ${willBeActive ? "Aktifkan" : "Nonaktifkan"}!`,
                    cancelButtonText:   "Batal",
                });
                if (!result.isConfirmed) return;
                try {
                    const res  = await fetch(`${this.baseUrl}/${this.school.school_id}/toggle-status`, {
                        method: "PATCH",
                        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal mengubah status.");
                    this.school.status = json.status;
                    this.showToast("success", json.message);
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan.");
                }
            },

            // ─── Delete ─────────────────────────────────────────────────────
            async deleteSchool() {
                const result = await Swal.fire({
                    title: `Hapus sekolah "${this.school.school_name}"?`,
                    text:  "Data yang dihapus tidak dapat dikembalikan.",
                    icon:  "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF4444", cancelButtonColor: "#94A3B8",
                    confirmButtonText: "Ya, Hapus!", cancelButtonText: "Batal",
                });
                if (!result.isConfirmed) return;
                try {
                    const res  = await fetch(`${this.baseUrl}/${this.school.school_id}`, {
                        method: "DELETE",
                        headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                    });
                    const json = await res.json();
                    if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal menghapus sekolah.");
                    this.showToast("success", "Sekolah berhasil dihapus.");
                    setTimeout(() => { window.location.href = this.indexUrl; }, 1200);
                } catch (err) {
                    this.showToast("error", err.message ?? "Terjadi kesalahan.");
                }
            },

            // ─── Helpers ────────────────────────────────────────────────────
            showToast(icon, message) {
                Swal.fire({
                    toast: true, position: "top-end", icon, title: message,
                    showConfirmButton: false, timer: 3500, timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener("mouseenter", Swal.stopTimer);
                        toast.addEventListener("mouseleave", Swal.resumeTimer);
                    },
                });
            },
            getCsrfToken() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ""; },
        };
    }