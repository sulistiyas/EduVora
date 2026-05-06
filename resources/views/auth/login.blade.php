<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — EduVora</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%233B82F6'/><path d='M8 22V12l8-4 8 4v10l-8 4-8-4z' fill='none' stroke='white' stroke-width='2'/></svg>">

    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <style>
        /* Sembunyikan spinner dari awal — ditampilkan JS saat submit */
        .btn-loading { display: none; }
        /* Ikon mata-coret tersembunyi dari awal */
        .eye-off { display: none; }
    </style>

    @vite(['resources/js/app.js'])
</head>
<body class="auth-body">

<div class="auth-shell">

    {{-- LEFT PANEL --}}
    <div class="auth-left">
        <div class="auth-left-inner">

            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <svg viewBox="0 0 24 24" fill="white">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <span class="auth-brand-name">EduVora</span>
                <span style="font-size:10px;color:rgba(255,255,255,.4);margin-left:6px;">School System</span>
            </div>

            <div class="auth-left-content">
                <h1 class="auth-headline">
                    Kelola semua<br>
                    <span class="auth-headline-accent">aktivitas sekolah</span><br>
                    dalam satu sistem.
                </h1>
                <p class="auth-desc">
                    Platform manajemen sekolah untuk mengatur siswa, guru,
                    jadwal pelajaran, dan administrasi — terintegrasi, efisien, dan modern.
                </p>
                <div class="auth-features">
                    <div class="auth-feature">📘 Manajemen siswa & guru</div>
                    <div class="auth-feature">🗓 Penjadwalan kelas otomatis</div>
                    <div class="auth-feature">🧾 Monitoring akademik & laporan</div>
                </div>
            </div>

            <div class="auth-deco">
                <div class="auth-deco-card">
                    <div class="deco-card-row">
                        <div class="deco-avatar" style="background:linear-gradient(135deg,#3B82F6,#60A5FA)">XI</div>
                        <div class="deco-info">
                            <div class="deco-title">Matematika - Kelas XI IPA</div>
                            <div class="deco-sub">08:00 – 09:30 · Hari ini</div>
                        </div>
                        <span class="deco-badge">Berlangsung</span>
                    </div>
                    <div class="deco-card-row" style="opacity:.7">
                        <div class="deco-avatar" style="background:linear-gradient(135deg,#10B981,#34D399)">BK</div>
                        <div class="deco-info">
                            <div class="deco-title">Bimbingan Konseling</div>
                            <div class="deco-sub">10:00 – 11:00 · Besok</div>
                        </div>
                        <span class="deco-badge pending">Terjadwal</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="auth-right">
        <div class="auth-form-wrap">

            <div class="auth-form-header">
                <h2 class="auth-form-title">Selamat datang di EduVora</h2>
                <p class="auth-form-subtitle">Masuk untuk mengakses sistem manajemen sekolah</p>
            </div>

            @if($errors->any())
            <div class="auth-alert error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('status'))
            <div class="auth-alert info">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="form-input-wrap">
                        <span class="form-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="nama@example.com"
                            autocomplete="email"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <div class="form-label-row">
                        <label class="form-label" for="password">Password</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="form-forgot">Lupa password?</a>
                        @endif
                    </div>
                    <div class="form-input-wrap">
                        <span class="form-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="form-eye-btn" id="togglePassword" tabindex="-1" aria-label="Tampilkan password">
                            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="form-check">
                    <label class="check-label">
                        <input type="checkbox" name="remember" class="check-input" {{ old('remember') ? 'checked' : '' }}>
                        <span class="check-box"></span>
                        Ingat saya selama 30 hari
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-auth" id="submitBtn">
                    <span class="btn-text">Masuk</span>
                    <span class="btn-loading">
                        <svg class="spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                        Memproses...
                    </span>
                </button>

            </form>

            <div class="auth-divider">
                <span>atau masuk dengan akun demo</span>
            </div>

            @if(app()->isLocal())
            <div class="demo-accounts" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <button type="button" class="demo-btn" onclick="fillDemo('superadmin@example.com')" style="justify-content:center;">
                    <span class="demo-dot" style="background:#f63b3b"></span>Super Admin
                </button>
                <button type="button" class="demo-btn" onclick="fillDemo('admin1@school.com')" style="justify-content:center;">
                    <span class="demo-dot" style="background:#3B82F6"></span>Admin
                </button>
                <button type="button" class="demo-btn" onclick="fillDemo('teacher@school.com')" style="justify-content:center;">
                    <span class="demo-dot" style="background:#10B981"></span>Guru
                </button>
                <button type="button" class="demo-btn" onclick="fillDemo('student@school.com')" style="justify-content:center;">
                    <span class="demo-dot" style="background:#94A3B8"></span>Siswa
                </button>
            </div>
            @endif

            <p class="auth-switch">
                Belum punya akun?
                <a href="#">Daftar sekarang</a>
            </p>

        </div>
    </div>

</div>

<script>
    // ── Toggle show/hide password ─────────────────────
    const toggleBtn     = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeOn         = toggleBtn.querySelector('.eye-on');
    const eyeOff        = toggleBtn.querySelector('.eye-off');

    toggleBtn.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type       = isHidden ? 'text' : 'password';
        eyeOn.style.display      = isHidden ? 'none'  : 'block';
        eyeOff.style.display     = isHidden ? 'block' : 'none';
        toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    });

    // ── Submit loading state ──────────────────────────
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText    = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    loginForm.addEventListener('submit', function () {
        submitBtn.disabled       = true;
        btnText.style.display    = 'none';
        btnLoading.style.display = 'inline-flex';
    });

    // ── Demo filler ───────────────────────────────────
    function fillDemo(email) {
        document.getElementById('email').value    = email;
        document.getElementById('password').value = 'password123';
    }
</script>

</body>
</html>