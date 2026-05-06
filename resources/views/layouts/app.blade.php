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

    {{-- Main CSS --}}
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/datatable.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<div class="shell">

    {{-- ===== SIDEBAR ===== --}}
    @include('components.sidebar')

    {{-- ===== MAIN AREA ===== --}}
    {{--
        FIX: .main tidak boleh punya overflow:hidden atau transform,
        karena itu membuat new stacking context yang mengurung
        x-teleport modal di dalam elemen ini.
        overflow-y:auto dipindahkan ke .content (scroll area) saja.
    --}}
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
                    <ol class="breadcrumb"></ol>
                </nav>
            </div>

            {{-- Right actions --}}
            <div class="topbar-actions">
                <div class="topbar-date">
                    <i class="ri-calendar-2-line" style="font-size:11px;margin-right:4px;"></i>
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                <button class="icon-btn" title="Cari">
                    <i class="ri-search-line"></i>
                </button>

                <button class="icon-btn" title="Notifikasi">
                    <i class="ri-notification-3-line"></i>
                    <span class="notif-dot"></span>
                </button>

                <div class="topbar-avatar-wrap" x-data="{ open: false }">
                    <div class="topbar-avatar"
                        @click="open = !open"
                        title="Profil saya">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>

                    <div x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        style="
                            position:absolute;
                            top:calc(100% + 10px);
                            right:0;
                            width:220px;
                            background:var(--card);
                            border:1px solid var(--border);
                            border-radius:var(--radius);
                            box-shadow:var(--shadow-md);
                            z-index:1000;
                            overflow:hidden;
                            display:none;
                        ">

                        {{-- Header --}}
                        <div style="display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--bg);border-bottom:1px solid var(--border)">
                            <div style="width:36px;height:36px;border-radius:9px;background:linear-gradient(135deg,var(--primary),#60A5FA);display:grid;place-items:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <div style="min-width:0">
                                <div style="font-size:13px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ auth()->user()->name }}
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>
                        </div>

                        {{-- Menu --}}
                        <div style="padding:4px">
                            <a href="#" class="dropdown-item">
                                <i class="ri-user-line"></i> Profil Saya
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="ri-settings-3-line"></i> Pengaturan
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="dropdown-item dropdown-item-danger"
                                        style="width:100%">
                                    <i class="ri-logout-box-r-line"></i> Keluar
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </header>

        {{-- ===== CONTENT ===== --}}
        {{--
            FIX: .content boleh overflow-y:auto tapi JANGAN pakai
            transform, will-change, atau filter — itu akan membuat
            stacking context baru dan mengurung modal.
            x-teleport sudah teleport ke <body>, tapi stacking context
            yang dibuat ancestor tetap bisa mengurung fixed positioning.
        --}}
        <main class="content">
            @yield('breadcrumb')

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

    </div>

</div>

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