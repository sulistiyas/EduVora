<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EduSaaS</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Remix Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    {{-- Main CSS (CSS variables & base styles sudah ada di sini) --}}
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    @stack('styles')
</head>
<body>

<div class="shell">

    {{-- ===== SIDEBAR ===== --}}
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
                <span>{{ auth()->user()->school->name ?? 'SMA Negeri 1' }}</span>
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

            <a href="#"
            {{-- <a href="{{ route('users.index') }}" --}}
            class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="ri-shield-user-line"></i>
                Pengguna
            </a>

            <a href="#"
            {{-- <a href="{{ route('schools.index') }}" --}}
            class="nav-item {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                <i class="ri-community-line"></i>
                Manajemen Sekolah
                @if(auth()->user()->hasRole('super-admin'))
                    <span class="nav-badge">SA</span>
                @endif
            </a>

            <a href="#"
            {{-- <a href="{{ route('settings.index') }}" --}}
            class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="ri-settings-4-line"></i>
                Pengaturan
            </a>

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
    {{-- /SIDEBAR --}}

    {{-- ===== MAIN AREA ===== --}}
    <div class="main">

        {{-- ===== TOPBAR ===== --}}
        <header class="topbar">

            {{-- Mobile sidebar toggle --}}
            <button class="icon-btn sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="ri-menu-line"></i>
            </button>

            {{-- Breadcrumb + Page Title --}}
            <div class="topbar-title">
                <h1>@yield('title', 'Dashboard')</h1>
                <nav class="breadcrumb-nav">
                    <ol class="breadcrumb">
                        {{-- <li class="breadcrumb-home">
                            <a href="{{ route('dashboard') }}">
                                <i class="ri-home-4-line"></i>
                                Beranda
                            </a>
                        </li> --}}
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>

            {{-- Right actions --}}
            <div class="topbar-actions">
                <div class="topbar-date">
                    <i class="ri-calendar-2-line" style="font-size:11px;margin-right:4px;"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                {{-- Search --}}
                <button class="icon-btn" title="Cari">
                    <i class="ri-search-line"></i>
                </button>

                {{-- Notifikasi --}}
                <button class="icon-btn" title="Notifikasi">
                    <i class="ri-notification-3-line"></i>
                    <span class="notif-dot"></span>
                </button>

                {{-- Avatar / Profile --}}
                <div class="topbar-avatar" title="Profil saya">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
            </div>

        </header>
        {{-- /TOPBAR --}}

        {{-- ===== CONTENT ===== --}}
        <main class="content">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-line"></i>
                    {{ session('success') }}
                    <button class="alert-close"><i class="ri-close-line"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line"></i>
                    {{ session('error') }}
                    <button class="alert-close"><i class="ri-close-line"></i></button>
                </div>
            @endif

            @yield('content')

        </main>
        {{-- /CONTENT --}}

    </div>
    {{-- /MAIN --}}

</div>
{{-- /SHELL --}}

@stack('scripts')

<script>
    // Sidebar mobile toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('sidebar--open');
    });

    // Auto-dismiss flash alerts
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.alert').remove());
    });
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => el.remove());
    }, 5000);
</script>

</body>
</html>