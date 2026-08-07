@extends('layouts.app')

@section('title', 'Dashboard Saya')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Dashboard Siswa</span></li>
    </ol>
</nav>
@endsection

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     STUDENT PROFILE HERO
═══════════════════════════════════════════════════════════════ --}}
@php
    $jamSekarang = now()->hour;
    $sapaan = $jamSekarang < 11 ? 'Selamat pagi'
            : ($jamSekarang < 15 ? 'Selamat siang'
            : ($jamSekarang < 18 ? 'Selamat sore'
            : 'Selamat malam'));
@endphp

<div style="
    background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 50%, #2563EB 100%);
    border-radius: var(--radius-lg, 16px);
    padding: 28px 32px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;
    position: relative;
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
    margin-bottom: 24px;
">
    {{-- Decorative Background Elements --}}
    <div style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none;filter:blur(20px);"></div>
    <div style="position:absolute;bottom:-60px;right:120px;width:180px;height:180px;border-radius:50%;background:rgba(59,130,246,.3);pointer-events:none;filter:blur(30px);"></div>

    {{-- Left Section: Avatar + Content --}}
    <div style="display:flex;align-items:flex-start;gap:20px;position:relative;z-index:1;flex:1;min-width:0;">
        {{-- Avatar --}}
        <div style="
            width: 64px; height: 64px; border-radius: 16px;
            background: linear-gradient(135deg, #60A5FA, #2563EB);
            display: grid; place-items: center;
            font-size: 22px; font-weight: 800; color: #fff;
            flex-shrink: 0;
            border: 3px solid rgba(255,255,255,.3);
            box-shadow: 0 8px 20px rgba(0,0,0,.2);
            overflow: hidden;
            margin-top: 2px;
        ">
            @if(!empty($student->photo))
                <img src="{{ asset($student->photo) }}" alt="{{ $student->name }}" style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ strtoupper(substr($student->name, 0, 2)) }}
            @endif
        </div>

        {{-- Profile Detail --}}
        <div style="flex:1;min-width:0;">
            {{-- Top row: Greeting + Status Badge --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                <h2 style="font-size:20px;font-weight:800;color:#fff;letter-spacing:-.3px;margin:0;line-height:1.3;">
                    {{ $sapaan }}, {{ $student->name }}! 👋
                </h2>
                <span style="
                    display:inline-flex;align-items:center;gap:5px;
                    font-size:11px;font-weight:700;
                    background:rgba(16,185,129,.25);color:#6EE7B7;
                    border:1px solid rgba(16,185,129,.35);
                    padding:3px 10px;border-radius:99px;
                ">
                    <span style="width:6px;height:6px;border-radius:50%;background:#10B981;display:block;"></span>
                    Siswa Aktif
                </span>
            </div>

            {{-- Subtitle --}}
            <p style="font-size:13px;color:rgba(255,255,255,.85);margin:0 0 14px 0;line-height:1.5;">
                @if($statistics['jadwal_hari_ini'] > 0)
                    Kamu memiliki <strong style="color:#FDE047;font-weight:700;">{{ $statistics['jadwal_hari_ini'] }} jadwal mata pelajaran</strong> untuk diikuti hari ini.
                @else
                    Tidak ada jadwal pelajaran hari ini. Selamat beristirahat! 🎉
                @endif
            </p>

            {{-- Badges / Meta --}}
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="
                    font-size:12px;font-weight:600;
                    background:rgba(255,255,255,.14);
                    border:1px solid rgba(255,255,255,.2);
                    color:#ffffff;
                    padding:4px 12px;border-radius:8px;
                    display:inline-flex;align-items:center;gap:6px;
                    backdrop-filter:blur(4px);
                ">
                    <i class="ri-id-card-line" style="font-size:13px;color:#93C5FD;"></i>
                    NIS: {{ $student->nis }}
                </span>
                <span style="
                    font-size:12px;font-weight:600;
                    background:rgba(255,255,255,.14);
                    border:1px solid rgba(255,255,255,.2);
                    color:#ffffff;
                    padding:4px 12px;border-radius:8px;
                    display:inline-flex;align-items:center;gap:6px;
                    backdrop-filter:blur(4px);
                ">
                    <i class="ri-building-4-line" style="font-size:13px;color:#93C5FD;"></i>
                    Kelas: {{ $student->grade_name }}
                </span>
                <span style="
                    font-size:12px;font-weight:600;
                    background:rgba(255,255,255,.14);
                    border:1px solid rgba(255,255,255,.2);
                    color:#ffffff;
                    padding:4px 12px;border-radius:8px;
                    display:inline-flex;align-items:center;gap:6px;
                    backdrop-filter:blur(4px);
                ">
                    <i class="ri-calendar-event-line" style="font-size:13px;color:#93C5FD;"></i>
                    Semester: {{ $student->semester }}
                </span>
            </div>
        </div>
    </div>

    {{-- Right Section: Date Card Widget --}}
    <div style="
        position:relative;z-index:1;
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.22);
        border-radius:14px;
        padding:14px 22px;
        text-align:center;
        min-width:110px;
        backdrop-filter:blur(10px);
        box-shadow:0 4px 15px rgba(0,0,0,.1);
        flex-shrink:0;
        align-self:center;
    " class="hero-date-card">
        <div style="font-size:26px;font-weight:800;color:#fff;line-height:1;letter-spacing:-0.5px;">{{ now()->format('d') }}</div>
        <div style="font-size:11px;font-weight:700;color:#93C5FD;text-transform:uppercase;letter-spacing:0.5px;margin-top:4px;">{{ now()->translatedFormat('M Y') }}</div>
        <div style="font-size:11px;color:rgba(255,255,255,.8);margin-top:2px;font-weight:500;">{{ now()->translatedFormat('l') }}</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     STAT CARDS ROW
═══════════════════════════════════════════════════════════════ --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">

    {{-- Rata-rata Nilai --}}
    <div class="stat-card blue">
        <div class="stat-header">
            <div class="stat-icon blue"><i class="ri-bar-chart-box-line"></i></div>
            <span class="stat-trend {{ $statistics['rata_rata_nilai'] >= 75 ? 'up' : 'down' }}">
                <i class="{{ $statistics['rata_rata_nilai'] >= 75 ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line' }}"></i>
                {{ $statistics['rata_rata_nilai'] >= 75 ? 'Baik' : 'Perlu Perhatian' }}
            </span>
        </div>
        <div class="stat-value">{{ $statistics['rata_rata_nilai'] }}</div>
        <div class="stat-label">Rata-rata Nilai</div>
        <div class="stat-sub">Semester berjalan · semua mapel</div>
    </div>

    {{-- Kehadiran --}}
    <div class="stat-card green">
        <div class="stat-header">
            <div class="stat-icon green"><i class="ri-checkbox-circle-line"></i></div>
            <span class="stat-trend {{ $statistics['persen_kehadiran'] >= 80 ? 'up' : 'down' }}">
                <i class="{{ $statistics['persen_kehadiran'] >= 80 ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line' }}"></i>
                {{ $statistics['persen_kehadiran'] }}%
            </span>
        </div>
        <div class="stat-value">{{ $statistics['persen_kehadiran'] }}<span style="font-size:16px;font-weight:500;color:var(--text-muted);">%</span></div>
        <div class="stat-label">Persentase Kehadiran</div>
        <div class="stat-sub">{{ $attendanceSummary['total'] }} total pertemuan</div>
    </div>

    {{-- Tugas Pending --}}
    <div class="stat-card {{ $statistics['tugas_pending'] > 0 ? 'yellow' : 'green' }}">
        <div class="stat-header">
            <div class="stat-icon {{ $statistics['tugas_pending'] > 0 ? 'yellow' : 'green' }}">
                <i class="ri-file-list-3-line"></i>
            </div>
            <span class="stat-trend {{ $statistics['tugas_pending'] == 0 ? 'up' : 'flat' }}">
                <i class="ri-time-line"></i>
                Segera
            </span>
        </div>
        <div class="stat-value">{{ $statistics['tugas_pending'] }}</div>
        <div class="stat-label">Tugas Belum Dikumpul</div>
        <div class="stat-sub">Perlu dikerjakan</div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <div class="stat-card blue">
        <div class="stat-header">
            <div class="stat-icon blue"><i class="ri-calendar-schedule-line"></i></div>
            <span class="stat-trend flat">
                <i class="ri-calendar-2-line"></i>
                Hari ini
            </span>
        </div>
        <div class="stat-value">{{ $statistics['jadwal_hari_ini'] }}</div>
        <div class="stat-label">Mata Pelajaran</div>
        <div class="stat-sub">{{ now()->translatedFormat('l, d M Y') }}</div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
     ROW: JADWAL HARI INI + ABSENSI SUMMARY
═══════════════════════════════════════════════════════════════ --}}
<div class="content-grid" style="grid-template-columns:1fr 300px;">

    {{-- JADWAL HARI INI --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="ri-calendar-schedule-line" style="color:var(--primary);margin-right:6px;"></i>Jadwal Hari Ini</div>
                <div class="card-subtitle">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
            <a href="#" class="card-action">Lihat Semua</a>
        </div>

        @if(count($todaySchedules) > 0)
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:10px;">
            @foreach($todaySchedules as $index => $sched)
            @php
                $badgeColors = [
                    'success'   => ['bg'=>'#ECFDF5','color'=>'#065F46','border'=>'#A7F3D0','dot'=>'#10B981'],
                    'info'      => ['bg'=>'#EFF6FF','color'=>'#1D4ED8','border'=>'#BFDBFE','dot'=>'#3B82F6'],
                    'secondary' => ['bg'=>'#F8FAFC','color'=>'#475569','border'=>'#CBD5E1','dot'=>'#94A3B8'],
                    'warning'   => ['bg'=>'#FFFBEB','color'=>'#92400E','border'=>'#FDE68A','dot'=>'#F59E0B'],
                    'danger'    => ['bg'=>'#FEF2F2','color'=>'#991B1B','border'=>'#FECACA','dot'=>'#EF4444'],
                    'light'     => ['bg'=>'#F8FAFC','color'=>'#94A3B8','border'=>'#E2E8F0','dot'=>'#CBD5E1'],
                    'primary'   => ['bg'=>'#EFF6FF','color'=>'#1D4ED8','border'=>'#BFDBFE','dot'=>'#3B82F6'],
                ];
                $bc = $badgeColors[$sched['status_badge']] ?? $badgeColors['secondary'];
                $subjectColors = ['#3B82F6','#10B981','#8B5CF6','#F59E0B','#EC4899','#0D9488','#F97316'];
                $accentColor = $subjectColors[$index % count($subjectColors)];
            @endphp
            <div style="
                display:flex;align-items:center;gap:14px;
                padding:14px 16px;
                background:var(--bg);
                border-radius:10px;
                border:1px solid var(--border);
                border-left:4px solid {{ $accentColor }};
                transition:box-shadow .15s,transform .15s;
            "
            onmouseenter="this.style.boxShadow='var(--shadow-md)';this.style.transform='translateX(2px)';"
            onmouseleave="this.style.boxShadow='';this.style.transform='';"
            >
                {{-- Time --}}
                <div style="text-align:center;min-width:56px;flex-shrink:0;">
                    @php [$startT,$endT] = explode('–', $sched['jam']); @endphp
                    <div style="font-size:13px;font-weight:700;color:var(--text-primary);font-family:var(--font-mono);">{{ $startT }}</div>
                    <div style="font-size:10px;color:var(--text-muted);font-family:var(--font-mono);">{{ $endT }}</div>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:40px;background:var(--border);flex-shrink:0;"></div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <div style="font-size:14px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $sched['mapel'] }}
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;display:flex;align-items:center;gap:10px;">
                        <span><i class="ri-user-line" style="font-size:11px;"></i> {{ $sched['guru'] }}</span>
                        <span><i class="ri-map-pin-line" style="font-size:11px;"></i> {{ $sched['ruangan'] }}</span>
                    </div>
                </div>

                {{-- Status Badge --}}
                <span style="
                    display:inline-flex;align-items:center;gap:4px;
                    padding:4px 10px;border-radius:99px;
                    font-size:11px;font-weight:600;
                    background:{{ $bc['bg'] }};color:{{ $bc['color'] }};
                    border:1px solid {{ $bc['border'] }};
                    flex-shrink:0;
                ">
                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $bc['dot'] }};display:block;flex-shrink:0;"></span>
                    {{ $sched['status_label'] }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="dt-empty" style="padding:40px 24px;">
            <div class="dt-empty-icon">
                <i class="ri-calendar-schedule-line"></i>
            </div>
            <div class="dt-empty-title">Tidak ada jadwal hari ini</div>
            <div class="dt-empty-sub">Nikmati hari liburmu! 🎉</div>
        </div>
        @endif
    </div>

    {{-- REKAP KEHADIRAN --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="ri-pie-chart-line" style="color:var(--primary);margin-right:6px;"></i>Rekap Kehadiran</div>
                <div class="card-subtitle">Semester berjalan</div>
            </div>
        </div>

        {{-- Circular progress --}}
        @php
            $persen = $attendanceSummary['persen_hadir'];
            $deg = round($persen / 100 * 360);
            $colorStop = $persen >= 80 ? '#10B981' : ($persen >= 60 ? '#F59E0B' : '#EF4444');
        @endphp
        <div style="display:flex;justify-content:center;padding:20px 20px 8px;">
            <div style="
                width:100px;height:100px;border-radius:50%;
                background:conic-gradient({{ $colorStop }} 0deg {{ $deg }}deg, #EFF6FF {{ $deg }}deg 360deg);
                display:grid;place-items:center;
                position:relative;
            ">
                <div style="position:absolute;inset:0;display:grid;place-items:center;">
                    <div style="
                        width:66px;height:66px;background:var(--card);border-radius:50%;
                        display:grid;place-items:center;text-align:center;
                    ">
                        <div style="font-size:16px;font-weight:800;color:var(--text-primary);line-height:1;">{{ $persen }}<span style="font-size:10px;">%</span></div>
                        <div style="font-size:9px;color:var(--text-muted);font-weight:500;">Hadir</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail --}}
        <div style="padding:0 16px 16px;display:flex;flex-direction:column;gap:8px;">
            @php
                $absenItems = [
                    ['key'=>'H','label'=>'Hadir',   'color'=>'#10B981','bg'=>'#ECFDF5'],
                    ['key'=>'I','label'=>'Izin',    'color'=>'#3B82F6','bg'=>'#EFF6FF'],
                    ['key'=>'S','label'=>'Sakit',   'color'=>'#F59E0B','bg'=>'#FFFBEB'],
                    ['key'=>'A','label'=>'Alpha',   'color'=>'#EF4444','bg'=>'#FEF2F2'],
                    ['key'=>'L','label'=>'Terlambat','color'=>'#8B5CF6','bg'=>'#F5F3FF'],
                ];
                $total = max(1, $attendanceSummary['total']);
            @endphp
            @foreach($absenItems as $item)
            @php $count = $attendanceSummary[$item['key']]; @endphp
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="
                    width:26px;height:26px;border-radius:7px;
                    background:{{ $item['bg'] }};
                    display:grid;place-items:center;flex-shrink:0;
                ">
                    <span style="font-size:11px;font-weight:800;color:{{ $item['color'] }};">{{ $item['key'] }}</span>
                </div>
                <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;font-size:11.5px;font-weight:600;color:var(--text-primary);margin-bottom:3px;">
                        <span>{{ $item['label'] }}</span>
                        <span>{{ $count }}x</span>
                    </div>
                    <div style="height:4px;background:var(--bg);border-radius:99px;overflow:hidden;">
                        <div style="height:100%;width:{{ round($count/$total*100) }}%;background:{{ $item['color'] }};border-radius:99px;transition:width .8s;"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
     ROW: NILAI PER MAPEL + RANKING KELAS
═══════════════════════════════════════════════════════════════ --}}
<div class="content-grid" style="grid-template-columns:1fr 300px;">

    {{-- NILAI PER MATA PELAJARAN --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="ri-bar-chart-grouped-line" style="color:var(--primary);margin-right:6px;"></i>Nilai Per Mata Pelajaran</div>
                <div class="card-subtitle">Rata-rata nilai yang telah dipublikasikan</div>
            </div>
            @if(count($nilaiPerMapel) > 0)
            <div style="
                display:flex;align-items:center;gap:6px;
                background:var(--bg);border:1px solid var(--border);
                padding:6px 12px;border-radius:8px;
                font-size:12px;font-weight:600;color:var(--text-secondary);
            ">
                <i class="ri-trophy-line" style="color:var(--warning);font-size:13px;"></i>
                Rata-rata: <strong style="color:var(--primary);">{{ $statistics['rata_rata_nilai'] }}</strong>
            </div>
            @endif
        </div>

        @if(count($nilaiPerMapel) > 0)
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:14px;">
            @foreach($nilaiPerMapel as $nilai)
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="
                            width:28px;height:28px;border-radius:7px;
                            background:{{ $nilai['color'] }}18;
                            display:grid;place-items:center;flex-shrink:0;
                        ">
                            <i class="ri-book-open-line" style="font-size:13px;color:{{ $nilai['color'] }};"></i>
                        </div>
                        <span style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $nilai['mapel'] }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="font-size:10px;color:var(--text-muted);">{{ $nilai['total'] }} nilai</span>
                        <span style="
                            font-size:14px;font-weight:800;
                            color:{{ $nilai['color'] }};
                            min-width:36px;text-align:right;
                        ">{{ $nilai['avg'] }}</span>
                    </div>
                </div>
                <div style="height:6px;background:var(--bg);border-radius:99px;overflow:hidden;">
                    <div style="
                        height:100%;
                        width:{{ $nilai['persen'] }}%;
                        background:{{ $nilai['color'] }};
                        border-radius:99px;
                        transition:width 1s cubic-bezier(.4,0,.2,1);
                    "></div>
                </div>
                <div style="display:flex;gap:12px;margin-top:4px;">
                    <span style="font-size:10px;color:var(--text-muted);">Min: <strong style="color:var(--text-secondary);">{{ $nilai['min'] }}</strong></span>
                    <span style="font-size:10px;color:var(--text-muted);">Max: <strong style="color:var(--text-secondary);">{{ $nilai['max'] }}</strong></span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="dt-empty" style="padding:40px 24px;">
            <div class="dt-empty-icon">
                <i class="ri-bar-chart-box-line"></i>
            </div>
            <div class="dt-empty-title">Belum ada nilai</div>
            <div class="dt-empty-sub">Nilai akan muncul setelah guru mempublikasikan hasil penilaian</div>
        </div>
        @endif
    </div>

    {{-- RANKING KELAS --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="ri-trophy-line" style="color:var(--warning);margin-right:6px;"></i>Ranking Kelas</div>
                <div class="card-subtitle">{{ $student->grade_name }}</div>
            </div>
        </div>

        @if(count($rankingData) > 0)
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
            @foreach($rankingData as $r)
            @php
                $rankColors  = [1=>'#F59E0B', 2=>'#94A3B8', 3=>'#CD7F32'];
                $rankIcons   = [1=>'ri-trophy-fill', 2=>'ri-medal-fill', 3=>'ri-award-fill'];
                $rankColor   = $rankColors[$r['rank']] ?? null;
                $rankIcon    = $rankIcons[$r['rank']] ?? null;
            @endphp
            <div style="
                display:flex;align-items:center;gap:12px;
                padding:10px 14px;
                border-radius:10px;
                background:{{ $r['is_me'] ? 'var(--primary-xlight)' : 'var(--bg)' }};
                border:1px solid {{ $r['is_me'] ? 'var(--primary-light)' : 'var(--border)' }};
                {{ $r['is_me'] ? 'box-shadow:0 2px 8px rgba(59,130,246,.12);' : '' }}
            ">
                {{-- Rank badge --}}
                <div style="
                    width:28px;height:28px;border-radius:8px;flex-shrink:0;
                    display:grid;place-items:center;
                    background:{{ $rankColor ? $rankColor.'18' : 'var(--card)' }};
                    {{ $rankColor ? 'border:1px solid '.$rankColor.'30;' : 'border:1px solid var(--border);' }}
                ">
                    @if($rankIcon)
                    <i class="{{ $rankIcon }}" style="font-size:14px;color:{{ $rankColor }};"></i>
                    @else
                    <span style="font-size:11px;font-weight:800;color:var(--text-muted);">{{ $r['rank'] }}</span>
                    @endif
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <div style="
                        font-size:12.5px;font-weight:{{ $r['is_me'] ? '700' : '600' }};
                        color:{{ $r['is_me'] ? 'var(--primary-dark)' : 'var(--text-primary)' }};
                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                    ">
                        {{ $r['is_me'] ? 'Kamu' : $r['nama'] }}
                        @if($r['is_me'])
                        <span style="font-size:10px;font-weight:600;color:var(--primary);margin-left:4px;">(Saya)</span>
                        @endif
                    </div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:1px;">Rank #{{ $r['rank'] }} dari {{ $totalSiswaKelas }} siswa</div>
                </div>

                {{-- Score --}}
                <div style="
                    font-size:15px;font-weight:800;
                    color:{{ $r['is_me'] ? 'var(--primary)' : 'var(--text-primary)' }};
                    flex-shrink:0;
                ">{{ $r['avg'] }}</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="dt-empty" style="padding:40px 24px;">
            <div class="dt-empty-icon">
                <i class="ri-trophy-line"></i>
            </div>
            <div class="dt-empty-title">Belum ada ranking</div>
            <div class="dt-empty-sub">Ranking akan tersedia setelah nilai dipublikasikan</div>
        </div>
        @endif

        {{-- Ranking summary footer --}}
        @if($rankingSiswaIni)
        <div style="
            margin:0 16px 16px;
            padding:12px 14px;
            background:linear-gradient(135deg,var(--primary),#60A5FA);
            border-radius:10px;
            display:flex;align-items:center;justify-content:space-between;
        ">
            <div>
                <div style="font-size:11px;color:rgba(255,255,255,.7);font-weight:500;">Posisi kamu</div>
                <div style="font-size:18px;font-weight:800;color:#fff;letter-spacing:-1px;line-height:1.2;">
                    Rank #{{ $rankingSiswaIni['rank'] }}
                    <span style="font-size:11px;font-weight:500;color:rgba(255,255,255,.7);">/ {{ $totalSiswaKelas }}</span>
                </div>
            </div>
            <i class="ri-user-star-line" style="font-size:32px;color:rgba(255,255,255,.3);"></i>
        </div>
        @endif
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
     ROW: TUGAS PENDING + TUGAS SUBMITTED
═══════════════════════════════════════════════════════════════ --}}
<div class="content-grid" style="grid-template-columns:1fr 1fr;">

    {{-- TUGAS BELUM DIKUMPUL --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">
                    <i class="ri-file-list-3-line" style="color:var(--warning);margin-right:6px;"></i>
                    Tugas Belum Dikumpul
                    @if(count($tugasPending) > 0)
                    <span style="
                        display:inline-flex;align-items:center;
                        background:#FEF2F2;color:#991B1B;
                        font-size:10px;font-weight:700;
                        padding:2px 7px;border-radius:99px;
                        border:1px solid #FECACA;
                        margin-left:6px;
                    ">{{ count($tugasPending) }}</span>
                    @endif
                </div>
                <div class="card-subtitle">Segera kumpulkan sebelum deadline</div>
            </div>
        </div>

        @if(count($tugasPending) > 0)
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:10px;">
            @foreach($tugasPending as $tugas)
            @php
                $urgencyMap = [
                    'danger'    => ['bg'=>'#FEF2F2','color'=>'#991B1B','border'=>'#FECACA','leftColor'=>'#EF4444'],
                    'warning'   => ['bg'=>'#FFFBEB','color'=>'#92400E','border'=>'#FDE68A','leftColor'=>'#F59E0B'],
                    'secondary' => ['bg'=>'#F8FAFC','color'=>'#475569','border'=>'#CBD5E1','leftColor'=>'#94A3B8'],
                ];
                $uc = $urgencyMap[$tugas['urgency']] ?? $urgencyMap['secondary'];
            @endphp
            <div style="
                padding:12px 14px;
                border-radius:10px;
                background:var(--bg);
                border:1px solid var(--border);
                border-left:3px solid {{ $uc['leftColor'] }};
            ">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $tugas['nama'] }}
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;display:flex;align-items:center;gap:8px;">
                            <span><i class="ri-book-open-line" style="font-size:11px;"></i> {{ $tugas['mapel'] }}</span>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="
                            font-size:10px;font-weight:700;
                            background:{{ $uc['bg'] }};color:{{ $uc['color'] }};
                            border:1px solid {{ $uc['border'] }};
                            padding:2px 8px;border-radius:99px;
                            white-space:nowrap;
                        ">
                            @if($tugas['days_left'] <= 0)
                                Hari ini!
                            @elseif($tugas['days_left'] == 1)
                                Besok
                            @else
                                {{ $tugas['days_left'] }} hari lagi
                            @endif
                        </div>
                        <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">{{ $tugas['due_date'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="dt-empty" style="padding:40px 24px;">
            <div class="dt-empty-icon">
                <i class="ri-checkbox-circle-line" style="color:var(--success)!important;"></i>
            </div>
            <div class="dt-empty-title">Semua tugas selesai! 🎉</div>
            <div class="dt-empty-sub">Tidak ada tugas yang perlu dikumpulkan saat ini</div>
        </div>
        @endif
    </div>

    {{-- TUGAS SUDAH DIKUMPUL --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">
                    <i class="ri-checkbox-multiple-line" style="color:var(--success);margin-right:6px;"></i>
                    Riwayat Pengumpulan
                </div>
                <div class="card-subtitle">5 pengumpulan terakhir</div>
            </div>
        </div>

        @if(count($tugasSubmitted) > 0)
        <div style="padding:12px 16px;display:flex;flex-direction:column;gap:10px;">
            @foreach($tugasSubmitted as $tugas)
            <div style="
                padding:12px 14px;
                border-radius:10px;
                background:var(--bg);
                border:1px solid var(--border);
                border-left:3px solid {{ $tugas['graded'] ? '#10B981' : '#94A3B8' }};
            ">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $tugas['nama'] }}
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;display:flex;align-items:center;gap:8px;">
                            <span><i class="ri-book-open-line" style="font-size:11px;"></i> {{ $tugas['mapel'] }}</span>
                        </div>
                        @if($tugas['feedback'])
                        <div style="
                            font-size:11px;color:#065F46;
                            background:#ECFDF5;border:1px solid #A7F3D0;
                            border-radius:6px;padding:4px 8px;margin-top:6px;
                        ">
                            <i class="ri-chat-check-line"></i> {{ $tugas['feedback'] }}
                        </div>
                        @endif
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="
                            font-size:10px;font-weight:700;
                            background:{{ $tugas['graded'] ? '#ECFDF5' : '#F8FAFC' }};
                            color:{{ $tugas['graded'] ? '#065F46' : '#475569' }};
                            border:1px solid {{ $tugas['graded'] ? '#A7F3D0' : '#CBD5E1' }};
                            padding:2px 8px;border-radius:99px;
                        ">
                            {{ $tugas['graded'] ? 'Dinilai ✓' : 'Menunggu' }}
                        </div>
                        <div style="font-size:10px;color:var(--text-muted);margin-top:3px;font-family:var(--font-mono);">{{ $tugas['submitted_at'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="dt-empty" style="padding:40px 24px;">
            <div class="dt-empty-icon">
                <i class="ri-inbox-archive-line"></i>
            </div>
            <div class="dt-empty-title">Belum ada pengumpulan</div>
            <div class="dt-empty-sub">Riwayat tugas yang telah kamu kumpulkan akan muncul di sini</div>
        </div>
        @endif
    </div>

</div>

@endsection

@push('styles')
<style>
@media (max-width: 768px) {
    .hero-date-card { display: none !important; }
}
@media (max-width: 768px) {
    [style*="grid-template-columns:1fr 300px"],
    [style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
    [style*="grid-template-columns:repeat(4,1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
@media (max-width: 480px) {
    [style*="grid-template-columns:repeat(2,1fr)"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush