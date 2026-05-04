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

                <div class="topbar-avatar" title="Profil saya">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
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