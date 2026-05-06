export function usersSearch(config = {}){
    return{
        // ─── URLs ──────────────────────────────────────────────────────
        indexUrl:        config.indexUrl        ?? "/users",
        showUrl:         config.showUrl         ?? "/users",
        storeUrl:        config.storeUrl        ?? "/users",
        updateUrl:       config.updateUrl       ?? "/users",
        destroyUrl:      config.destroyUrl      ?? "/users",
        toggleStatusUrl: config.toggleStatusUrl ?? "/users",

        // ─── State ─────────────────────────────────────────────────────
        users:      [],
        meta:         { current_page: 1, per_page: 10, total: 0, last_page: 1 },
        search:       "",
        perPage:      10,
        statusFilter: "",
        loading:      false,
        filterOpen:   false,
        status: {
                active: 0,
                inactive: 0,
                unverified: 0,
        },
        // Filter state
        query: '',
        roles: [],
        roleFilter: "",
        filterOpen2: false,

        // ─── Form ─────────────────────────────────────────────────────
       
        // Init
        init() { 
            this.fetchRoles();
            this.fetchUsers();

        },

        defaultForm(){
            // 
        },

        _avatarColors: ["av-blue","av-violet","av-green","av-pink","av-teal","av-red","av-indigo"],
        avatarColor(index) { return this._avatarColors[index % this._avatarColors.length]; },
        initials(name) {
            return (name || "").split(/[-_ ]/).map(w => w[0]?.toUpperCase() || "").join("").slice(0, 2);
        },

        // ─── Fetch Roles ───────────────────────────────
        async fetchRoles() {
            try {
                const res  = await fetch('/users/roles', {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                const data = await res.json();

                // Tambah opsi "Semua Role" di awal
                this.roles = [
                    { role_id: '', role_name: 'Semua Role' },
                    ...data,
                ];
            } catch {
                this.roles = [{ role_id: '', role_name: 'Semua Role' }];
            }
        },

        roleLabelFn() {
            const found = this.roles.find(r => r.role_id == this.roleFilter);
            return found ? found.role_name : 'Semua Role';
        },

        setRoleFilter(val) {
            this.roleFilter  = val;
            this.filterOpen2 = false;
            this.fetchUsers();
        },


        // ─── Fetch ─────────────────────────────────────────────────────
        async fetchUsers(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: parseInt(this.perPage, 10),
                    ...(this.search.trim()       ? { search: this.search.trim() } : {}),
                    ...(this.statusFilter !== "" ? { status: this.statusFilter }  : {}),
                    ...(this.roleFilter !== ""   ? { role: this.roleFilter }      : {}),
                });
                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error("Gagal memuat data.");
                const json    = await res.json();
                this.users    = json.data;
                this.meta     = json.meta;

                this.status = json.status ?? {
                    active: 0,
                    inactive: 0,
                    unverified: 0,
                };

            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan saat memuat data.");
            } finally {
                this.loading = false;
            }
        },

        filterLabel(){
            if (this.statusFilter === "active")   return "Aktif";
            if (this.statusFilter === "inactive") return "Non-Aktif";
            return "Semua Status";
        },

        setStatusFilter(val) { this.statusFilter = val; this.filterOpen = false; this.fetchUsers(); },

        changePage(page){
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchUsers(page);
        },

        // Toggle Status
        async toggleStatus(user){
            const willBeActive = user.status !== "active";
            const result = await Swal.fire({
                title: `${willBeActive ? "Aktifkan" : "Nonaktifkan"} user "${user.name}"?`,
                text:  `User akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
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
                const res  = await fetch(`${this.toggleStatusUrl}/${user.id}/toggle-status`, {
                    method:  "PATCH",
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal mengubah status.");
                user.status = json.status;
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        async goToDetail(id){
            window.location.href = `${this.showUrl}/${id}/detail`;
        },

        async openEditModal(id){
            this.isEditing  = true;
            this.editingId  = id;
            this.errors     = {};
            this.form       = { name: "", email: "", password: "", password_confirmation: "", role: "" };
            this.showModal  = true;

            try{
                const res = await fetch(`${this.showUrl}/${id}`, {
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error("Gagal mengambil data user.");
                const data = await res.json();
                // this.form.name        = data.name        ?? "";
                // this.form.email       = data.email       ?? "";
                // this.form.role        = data.role        ?? "";
                // this.form.status      = data.status      ?? "";
                // this.form.created_at  = data.created_at  ?? "";
                // this.form.updated_at  = data.updated_at  ?? "";
            }catch{

            }
        },

        // Helpers
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

// Detail Page
export function userDetail(config = {}) {
    return {
        baseUrl:  config.baseUrl  ?? "/users",
        indexUrl: config.indexUrl ?? "/users",
        rolesUrl:  config.rolesUrl  ?? "/users/roles",    
        schoolsUrl:config.schoolsUrl ?? "/users/schools",

        user:       config.userData ?? {},
        isEditing:  false,
        submitting: false,
        form:       {},
        errors:     {},

        // ── Roles & Schools untuk dropdown ──
        roles:          [],
        schools:        [],
        roleDropOpen:   false,
        schoolDropOpen: false,
        

        dropPos: {
            role:   { top: 0, left: 0, width: 240 },
            school: { top: 0, left: 0, width: 240 },
        },

        init() {
            this.fetchRoles();
            this.fetchSchools();
            // this.$watch('roles', (val) => {
            // if (val.length && this.isEditing) {
            //     this.form.role = this.user.roles?.[0]?.role_id ?? '';
            //     }
            // });

            // this.$watch('schools', (val) => {
            //     if (val.length && this.isEditing) {
            //         this.form.school = this.user.schools?.[0]?.school_id ?? '';
            //     }
            // });
        },

        // ── Fetch dropdown data ─────────────
        async fetchRoles() {
            try {
                const res = await fetch(this.rolesUrl, { 
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                this.roles = await res.json();

                this.$nextTick(() => {
                    this.form.role = String(this.form.role ?? '');
                });
            } catch { this.roles = []; }
        },

        async fetchSchools() {
            try {
                const res = await fetch(this.schoolsUrl, {  
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                this.schools = await res.json();

                this.$nextTick(() => {
                    this.form.school = String(this.form.school ?? '');
                });
            } catch { this.schools = []; }
        },

        // ── Dropdown helpers ───────────────
        get selectedRoleLabel() {
            const found = this.roles.find(r => r.role_id == this.form.role);
            return found ? found.role_name : (this.user.roles?.[0]?.role_name ?? '— Pilih Role —');
        },

        get selectedSchoolLabel() {
            const found = this.schools.find(s => s.school_id == this.form.school);
            return found ? found.school_name : (this.user.schools?.[0]?.school_name ?? '— Pilih Sekolah —');
        },

        
        toggleDrop(which) {
            const triggerId  = which === 'role' ? 'role-trigger' : 'school-trigger';
            const trigger    = document.getElementById(triggerId);
            const rect       = trigger.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            const menuH      = 240;

            // Buka di bawah jika cukup ruang, di atas jika tidak
            const top = spaceBelow >= menuH
                ? rect.bottom + 4
                : rect.top - menuH - 4;

            this.dropPos[which] = {
                top:   top,
                left:  rect.left,
                width: rect.width,
            };

            if (which === 'role') {
                this.roleDropOpen   = !this.roleDropOpen;
                this.schoolDropOpen = false;
            } else {
                this.schoolDropOpen = !this.schoolDropOpen;
                this.roleDropOpen   = false;
            }
        },

        selectRole(role) {
            this.form.role = role.role_id;
            this.roleDropOpen = false;
        },

        selectSchool(school) {
            this.form.school = school.school_id;
            this.schoolDropOpen = false;
        },

        // ── Helpers ───────────────────────
        initials(name) {
            return (name || "").split(/[-_ ]/).map(w => w[0]?.toUpperCase() || "").join("").slice(0, 2);
        },

        roleStyle(roleName) {
            const map = {
                'super-admin':    { background: '#FEF2F2', color: '#991B1B' },
                'school-admin':   { background: '#F0F9FF', color: '#0369A1' },
                'headmaster':     { background: '#F5F3FF', color: '#5B21B6' },
                'teacher':        { background: '#ECFDF5', color: '#065F46' },
                'student':        { background: '#EFF6FF', color: '#1D4ED8' },
                'student-parent': { background: '#FFF7ED', color: '#92400E' },
            };
            const key = roleName?.toLowerCase().replace(' ', '-');
            const s = map[key] || { background: '#F1F5F9', color: '#475569' };
            return `background:${s.background};color:${s.color}`;
        },

        // ── Edit Mode ──────────────────────
        startEdit() {
            this.form = {
                name:                  this.user.name         ?? "",
                email:                 this.user.email        ?? "",
                phone_number:          this.user.phone_number ?? "",
                password:              "",
                password_confirmation: "",
                role:   this.user.roles?.[0]?.role_id   ?? "",
                school: this.user.schools?.[0]?.school_id ?? "",
                profile: this.user.profile ? { ...this.user.profile } : {},
            };
            this.errors        = {};
            this.isEditing     = true;
            this.roleDropOpen  = false;
            this.schoolDropOpen = false;
            // this.$nextTick(() => {
            //     this.form.role   = this.form.role;
            //     this.form.school = this.form.school;
            // });
            this.$nextTick(() => {
                document.querySelector('[data-edit-focus]')?.focus();
            });
        },

        cancelEdit() {
            this.isEditing      = false;
            this.submitting     = false;
            this.errors         = {};
            this.form           = {};
            this.roleDropOpen   = false;
            this.schoolDropOpen = false;
        },

        validate() {
            this.errors = {};
            if (!this.form.name?.trim())  this.errors.name  = "Nama wajib diisi.";
            if (!this.form.email?.trim()) this.errors.email = "Email wajib diisi.";
            if (this.form.password && this.form.password !== this.form.password_confirmation) {
                this.errors.password = "Konfirmasi password tidak cocok.";
            }
            return Object.keys(this.errors).length === 0;
        },

        async submitEdit() {
            if (!this.validate()) return;
            this.submitting = true;
            try {
                const payload = { ...this.form };
                if (!payload.password) {
                    delete payload.password;
                    delete payload.password_confirmation;
                }

                const res  = await fetch(`${this.baseUrl}/${this.user.id}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type":     "application/json",
                        Accept:             "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":     this.getCsrfToken(),
                    },
                    body: JSON.stringify(payload),
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

                // ✅ Update reaktif dari response server (data lengkap)
                const updated = json.data;
                this.user = { ...this.user, ...updated };

                this.isEditing  = false;
                this.submitting = false;
                this.form       = {};
                this.showToast("success", "Data user berhasil diperbarui.");

            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            } finally {
                this.submitting = false;
            }
        },

        async toggleStatus() {
            const willBeActive = this.user.status !== "active";
            const result = await Swal.fire({
                title: `${willBeActive ? "Aktifkan" : "Nonaktifkan"} user ini?`,
                text:  `User akan ${willBeActive ? "diaktifkan" : "dinonaktifkan"}.`,
                icon:  "question",
                showCancelButton:   true,
                confirmButtonColor: willBeActive ? "#10B981" : "#F59E0B",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  `Ya, ${willBeActive ? "Aktifkan" : "Nonaktifkan"}!`,
                cancelButtonText:   "Batal",
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.baseUrl}/${this.user.id}/toggle-status`, {
                    method:  "PATCH",
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal mengubah status.");
                this.user.status = json.status;
                this.showToast("success", json.message);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

        async deleteUser() {
            const result = await Swal.fire({
                title: `Hapus user "${this.user.name}"?`,
                text:  "Data yang dihapus tidak dapat dikembalikan.",
                icon:  "warning",
                showCancelButton:   true,
                confirmButtonColor: "#EF4444",
                cancelButtonColor:  "#94A3B8",
                confirmButtonText:  "Ya, Hapus!",
                cancelButtonText:   "Batal",
            });
            if (!result.isConfirmed) return;
            try {
                const res  = await fetch(`${this.baseUrl}/${this.user.id}`, {
                    method:  "DELETE",
                    headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": this.getCsrfToken() },
                });
                const json = await res.json();
                if (!res.ok || !json.success) throw new Error(json.message ?? "Gagal menghapus user.");
                this.showToast("success", "User berhasil dihapus.");
                setTimeout(() => { window.location.href = this.indexUrl; }, 1200);
            } catch (err) {
                this.showToast("error", err.message ?? "Terjadi kesalahan.");
            }
        },

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
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? "";
        },
    };
}

// Create User
export function userCreate(config = {}) {
    return {
        // ─── URLs ────────────────────────────────────────────────────
        storeUrl: config.storeUrl ?? '/users',
        rolesUrl: config.rolesUrl ?? '/users/roles',
        schoolsUrl: config.schoolsUrl ?? '/users/schools',
 
        // ─── State ───────────────────────────────────────────────────
        roles:        [],
        roleDropOpen: false,
        schools: [],
        schoolDropOpen: false,
        showPassword: false,
        submitting:   false,
        errors:       {},
 
        // ─── Form ────────────────────────────────────────────────────
        form: {
            name:                  '',
            email:                 '',
            phone_number:          '',
            password:              '',
            password_confirmation: '',
            role:                  '',
            school:                '',
            status:                'active',
            profile:               {},
        },
 
        // ─── Init ────────────────────────────────────────────────────
        init() {
            this.fetchRoles();
            this.fetchSchools();
        },
 
        // ─── Role helpers ─────────────────────────────────────────────
        async fetchRoles() {
            try {
                const res  = await fetch(this.rolesUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error();
                this.roles = await res.json();
            } catch {
                this.roles = [];
            }
        },

        async fetchSchools() {
            try{
                const rest = await fetch(this.schoolsUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!rest.ok) throw new Error();
                this.schools = await rest.json();
            } catch {
                this.schools = [];

            }
        },
 
        get selectedRoleLabel() {
            const found = this.roles.find(r => r.role_id == this.form.role);
            return found ? found.role_name : '';
        },
 
        selectRole(role) {
            this.form.role    = role.role_id;
            this.roleDropOpen = false;
            // Reset profil saat role berubah
            this.form.profile = {};
        },

        selectSchool(school) {
            this.form.school    = school.school_id;
            this.schoolDropOpen = false;
        },
 
        // ─── Dynamic profile type ─────────────────────────────────────
        get selectedRoleName() {
            const found = this.roles.find(r => r.role_id == this.form.role);
            return found ? found.role_name.toLowerCase() : '';
        },

        get selectedSchoolLabel() {
            const found = this.schools.find(s => s.school_id == this.form.school);
            return found ? found.school_name : '';
        },
 
        get isTeacherType() {
            return ['teacher', 'headmaster'].includes(this.selectedRoleName);
        },
 
        get isStudentType() {
            return this.selectedRoleName === 'student';
        },
 
        // ─── Validation ──────────────────────────────────────────────
        validate() {
            this.errors = {};
            if (!this.form.name?.trim())  this.errors.name  = 'Nama wajib diisi.';
            if (!this.form.email?.trim()) this.errors.email = 'Email wajib diisi.';
            if (!this.form.role)          this.errors.role  = 'Role wajib dipilih.';
            if (!this.form.school)        this.errors.school = 'Sekolah wajib dipilih.';
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
 
        // ─── Submit ──────────────────────────────────────────────────
        async submitForm() {
            if (!this.validate()) {
                // scroll ke error pertama
                this.$nextTick(() => {
                    document.querySelector('.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
                return;
            }
            this.submitting = true;
            try {
                const payload = { ...this.form };
                // Hapus profile kosong
                if (!this.isTeacherType && !this.isStudentType) delete payload.profile;
 
                const res  = await fetch(this.storeUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type':     'application/json',
                        Accept:             'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     this.getCsrfToken(),
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();
 
                if (!res.ok) {
                    if (res.status === 422 && json.errors) {
                        this.errors = Object.fromEntries(
                            Object.entries(json.errors).map(([k, v]) => [k, v[0]])
                        );
                        this.$nextTick(() => {
                            document.querySelector('.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        });
                        return;
                    }
                    throw new Error(json.message ?? 'Gagal menyimpan data.');
                }
 
                this.showToast('success', 'User berhasil ditambahkan!');
                setTimeout(() => { window.location.href = '/users'; }, 1200);
 
            } catch (err) {
                this.showToast('error', err.message ?? 'Terjadi kesalahan.');
            } finally {
                this.submitting = false;
            }
        },
 
        // ─── Helpers ─────────────────────────────────────────────────
        showToast(icon, message) {
            Swal.fire({
                toast: true, position: 'top-end', icon, title: message,
                showConfirmButton: false, timer: 3500, timerProgressBar: true,
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