<aside class="sidebar" id="sidebar">

    {{-- Logo / Brand --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
            </svg>
        </div>
        <div class="sidebar-logo-text">
            <div class="sidebar-logo-name">EduSaaS</div>
            <div class="sidebar-logo-sub">School Management</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- Tenant / Sekolah aktif --}}
        <div class="nav-section-label">Sekolah Aktif</div>
        <div class="sidebar-school-badge">
            <i class="ri-building-4-line"></i>
            <span>{{ auth()->user()->schools->first()->school_name ?? '-' }}</span>
            <i class="ri-arrow-down-s-line" style="margin-left:auto;"></i>
        </div>

        <div class="nav-section-label" style="margin-top:8px;">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ri-dashboard-3-line"></i>
            Dashboard
        </a>

        <a href="#"
        {{-- <a href="{{ route('students.index') }}" --}}
        class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="ri-user-3-line"></i>
            Siswa
            <span class="nav-badge">1.2k</span>
        </a>

        <a href="#"
        {{-- <a href="{{ route('teachers.index') }}" --}}
        class="nav-item {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
            <i class="ri-team-line"></i>
            Guru & Staff
        </a>

        <a href="#"
        {{-- <a href="{{ route('classes.index') }}" --}}
        class="nav-item {{ request()->routeIs('classes.*') ? 'active' : '' }}">
            <i class="ri-book-open-line"></i>
            Kelas
        </a>

        <a href="#"
        {{-- <a href="{{ route('schedules.index') }}" --}}
        class="nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
            <i class="ri-calendar-schedule-line"></i>
            Jadwal
        </a>

        <a href="#"
        {{-- <a href="{{ route('attendance.index') }}" --}}
        class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
            <i class="ri-checkbox-circle-line"></i>
            Presensi
        </a>

        <a href="#"
        {{-- <a href="{{ route('grades.index') }}" --}}
        class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
            <i class="ri-bar-chart-2-line"></i>
            Nilai
        </a>

        {{-- =============================================
             AKADEMIK — Tahun Ajaran & Semester
             Visible untuk: school-admin & super-admin
        ============================================== --}}
        <div class="nav-section-label" style="margin-top:8px;">Akademik</div>

        {{-- Tahun Ajaran (parent collapsible) --}}
        <div class="nav-item-group {{ request()->routeIs('academic-year.*') || request()->routeIs('semesters.*') || request()->routeIs('academic-dates.*') ? 'open' : '' }}">

            <a href="#"
               class="nav-item nav-item--has-children {{ request()->routeIs('academic-year.*') || request()->routeIs('semesters.*') || request()->routeIs('academic-dates.*') ? 'active' : '' }}"
               onclick="toggleNavGroup(this); return false;">
                <i class="ri-calendar-2-line"></i>
                Tahun Ajaran
                <i class="ri-arrow-down-s-line nav-arrow" style="margin-left:auto;"></i>
            </a>

            <div class="nav-sub-menu">
                {{-- Sub: Tahun Ajaran --}}
                <a href="{{ route('academic-year.index') }}"
                   class="nav-item nav-item--sub {{ request()->routeIs('academic-year.*') ? 'active' : '' }}">
                    <i class="ri-calendar-check-line"></i>
                    Daftar Tahun Ajaran
                </a>

                {{-- Sub: Semester --}}
                <a href="{{ route('semesters.index') }}"
                   class="nav-item nav-item--sub {{ request()->routeIs('semesters.*') ? 'active' : '' }}">
                    <i class="ri-split-cells-horizontal"></i>
                    Semester
                </a>

                {{-- Sub: Tanggal Penting --}}
                {{-- <a href="{{ route('academic-dates.index') }}" --}}
                <a href="#"
                {{-- <a href="#" --}}
                   class="nav-item nav-item--sub {{ request()->routeIs('academic-dates.*') ? 'active' : '' }}">
                    <i class="ri-calendar-event-line"></i>
                    Tanggal Penting
                </a>
            </div>

        </div>
        {{-- END Akademik --}}

        <div class="nav-section-label" style="margin-top:8px;">Keuangan</div>

        <a href="#"
        {{-- <a href="{{ route('finance.index') }}" --}}
        class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}">
            <i class="ri-money-dollar-circle-line"></i>
            Pembayaran SPP
        </a>

        <a href="#"
        {{-- <a href="{{ route('payroll.index') }}" --}}
        class="nav-item {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
            <i class="ri-wallet-3-line"></i>
            Penggajian
        </a>

        <div class="nav-section-label" style="margin-top:8px;">Administrasi</div>

        {{-- <a href="{{ route('users.index') }}"
        class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="ri-shield-user-line"></i>
            Pengguna
        </a> --}}

        <a href="#"
        {{-- <a href="{{ route('schools.index') }}" --}}
        class="nav-item {{ request()->routeIs('schools.*') ? 'active' : '' }}">
            <i class="ri-community-line"></i>
            Manajemen Sekolah
            @if(auth()->user()->hasRole('super-admin'))
                <span class="nav-badge">SA</span>
            @endif
        </a>

        @if (auth()->user()->hasRole('super-admin'))
            <div class="nav-section-label" style="margin-top:8px;">User & Role Management</div>
            <a href="{{ route('roles.index') }}"
                class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="ri-shield-line"></i>
                    Role & Permission
            </a>
            <a href="{{ route('users.index') }}"
                class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="ri-user-line"></i>
                    User Management
            </a>
            {{-- <a href="#" --}}
            <a href="{{ route('school-management.index') }}"
                class="nav-item {{ request()->routeIs('school-management.*') ? 'active' : '' }}">
                    <i class="ri-community-line"></i>
                    School Management
            </a>
            <div class="nav-section-label" style="margin-top:8px;">School Management</div>
            <a href="#"
            {{-- <a href="{{ route('settings.index') }}" --}}
            class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="ri-settings-4-line"></i>
                Pengaturan
            </a>
        
            
        @endif
    </nav>

    {{-- Sidebar Footer: user info --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role_label ?? 'Administrator' }}</div>
            </div>
            <i class="ri-more-2-fill" style="color:rgba(255,255,255,.3);font-size:16px;"></i>
        </div>
    </div>

</aside>

{{-- =============================================
     CSS tambahan untuk nav collapsible group
     Tambahkan ke file CSS/layout Anda
============================================== --}}

<style>
.nav-item-group .nav-sub-menu {
    display: none;
    padding-left: 12px;
}
.nav-item-group.open .nav-sub-menu {
    display: block;
}
.nav-item-group.open .nav-arrow {
    transform: rotate(180deg);
}
.nav-arrow {
    transition: transform 0.2s ease;
}
.nav-item--sub {
    font-size: 0.8rem;
    padding-top: 6px;
    padding-bottom: 6px;
}
</style>


{{-- =============================================
     JS untuk toggle collapsible group
     Tambahkan ke file JS/layout Anda
============================================== --}}

<script>
function toggleNavGroup(el) {
    const group = el.closest('.nav-item-group');
    group.classList.toggle('open');
}
</script>
