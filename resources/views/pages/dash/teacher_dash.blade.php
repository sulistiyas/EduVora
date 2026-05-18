@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')

@php
// ── Safeguard: pastikan semua variabel selalu terdefinisi ──────────────────
// (controller sudah inject semua ini; fallback di bawah hanya jaga-jaga)

$teacher ??= (object)[
    'name'    => '-',
    'gender'  => 'male',
    'nip'     => '-',
    'photo'   => null,
    'mapel'   => ['—'],
    'semester'=> '—',
];

$statistics ??= [
    'total_kelas'          => 0,
    'total_siswa'          => 0,
    'jadwal_hari_ini'      => 0,
    'tugas_belum_dinilai'  => 0,
    'absensi_belum_diisi'  => 0,
];

$todaySchedules     ??= [];
$pendingAssignments ??= [];
$announcements      ??= [];
$academicEvents     ??= [];
$recentActivities   ??= [];

$attendanceSummary ??= [
    'hadir' => 0,
    'izin'  => 0,
    'sakit' => 0,
    'alpha' => 0,
    'total' => 0,
];

// Konversi ke array biasa agar count() & foreach berjalan
// (controller bisa mengirim Collection maupun array)
$todaySchedules     = collect($todaySchedules)->toArray();
$pendingAssignments = collect($pendingAssignments)->toArray();
$announcements      = collect($announcements)->toArray();
$academicEvents     = collect($academicEvents)->toArray();
$recentActivities   = collect($recentActivities)->toArray();

// Sapaan
$sapaanPrefix = ($teacher->gender === 'female') ? 'Bu' : 'Pak';
$jamSekarang  = now()->hour;
$sapaan = $jamSekarang < 11 ? 'Selamat pagi'
        : ($jamSekarang < 15 ? 'Selamat siang'
        : ($jamSekarang < 18 ? 'Selamat sore'
        : 'Selamat malam'));

// Nama depan saja
$namaDepan = explode(' ', $teacher->name)[0] ?? $teacher->name;
@endphp

<div class="tch-wrap">

    {{-- ── GREETING HEADER ── --}}
    <div class="tch-greeting">
        <div class="tch-greeting-left">
            <div class="tch-avatar">
                @if($teacher->photo)
                    <img src="{{ asset($teacher->photo) }}" alt="{{ $teacher->name }}">
                @else
                    {{ strtoupper(substr($teacher->name, 0, 2)) }}
                @endif
            </div>
            <div>
                <h2 class="tch-greeting-title">
                    {{ $sapaan }}, {{ $sapaanPrefix }} {{ $namaDepan }}! 👋
                </h2>
                <p class="tch-greeting-sub">
                    <strong>{{ $statistics['jadwal_hari_ini'] }} jadwal</strong> hari ini
                    <span class="tch-sem-badge">{{ $teacher->semester }}</span>
                </p>
                <p class="tch-greeting-meta">
                    <i class="ri-calendar-2-line"></i> {{ now()->translatedFormat('l, d F Y') }}
                    &nbsp;·&nbsp;
                    <i class="ri-bookmark-line"></i> {{ implode(', ', $teacher->mapel) }}
                </p>
            </div>
        </div>
        <div class="tch-date-card">
            <div class="tch-date-day">{{ now()->format('d') }}</div>
            <div class="tch-date-month">{{ now()->translatedFormat('M Y') }}</div>
            <div class="tch-date-week">{{ now()->translatedFormat('l') }}</div>
        </div>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="tch-stats">
        @php $statsConfig = [
            ['key'=>'total_kelas',         'label'=>'Kelas Diajar',        'icon'=>'ri-door-open-line',    'color'=>'blue',   'sub'=>'aktif semester ini'],
            ['key'=>'total_siswa',         'label'=>'Total Siswa',         'icon'=>'ri-group-line',         'color'=>'green',  'sub'=>'dari seluruh kelas'],
            ['key'=>'jadwal_hari_ini',     'label'=>'Jadwal Hari Ini',     'icon'=>'ri-time-line',          'color'=>'yellow', 'sub'=>'sesi mengajar'],
            ['key'=>'tugas_belum_dinilai', 'label'=>'Belum Dinilai',       'icon'=>'ri-file-edit-line',     'color'=>'orange', 'sub'=>'perlu review'],
            ['key'=>'absensi_belum_diisi', 'label'=>'Absensi Kosong',      'icon'=>'ri-survey-line',        'color'=>'red',    'sub'=>'segera isi'],
        ]; @endphp

        @foreach($statsConfig as $s)
        <div class="tch-stat {{ $s['color'] }}" style="animation-delay:{{ $loop->index * 0.06 }}s">
            <div class="tch-stat-top">
                <div class="tch-stat-icon {{ $s['color'] }}"><i class="{{ $s['icon'] }}"></i></div>
                @if(in_array($s['key'], ['tugas_belum_dinilai','absensi_belum_diisi']) && $statistics[$s['key']] > 0)
                    <span class="tch-stat-tag warn"><i class="ri-error-warning-line"></i> Aksi</span>
                @else
                    <span class="tch-stat-tag ok"><i class="ri-check-line"></i> Normal</span>
                @endif
            </div>
            <div class="tch-stat-val">{{ $statistics[$s['key']] }}</div>
            <div class="tch-stat-label">{{ $s['label'] }}</div>
            <div class="tch-stat-sub">{{ $s['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── MAIN GRID ── --}}
    <div class="tch-grid">

        {{-- LEFT COLUMN --}}
        <div class="tch-col-main">

            {{-- TODAY SCHEDULE — card list, no table --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="ri-calendar-schedule-line" style="color:var(--primary);margin-right:6px;"></i>
                            Jadwal Mengajar Hari Ini
                        </div>
                        <div class="card-subtitle">{{ now()->translatedFormat('l, d F Y') }}</div>
                    </div>
                    <a href="#" class="card-action">Lihat Semua</a>
                </div>

                @if(count($todaySchedules) > 0)
                <div class="tch-sched-list">
                    @foreach($todaySchedules as $s)
                    @php
                        $badgeClass = ['sudah'=>'badge-success','belum'=>'badge-warning','terlambat'=>'badge-danger'];
                        $badgeLabel = ['sudah'=>'Sudah','belum'=>'Belum','terlambat'=>'Terlambat'];
                    @endphp
                    <div class="tch-sched-item">
                        {{-- time strip --}}
                        <div class="tch-sched-time">
                            <i class="ri-time-line"></i>
                            <span>{{ $s['jam'] }}</span>
                        </div>
                        {{-- info --}}
                        <div class="tch-sched-info">
                            <div class="tch-sched-top">
                                <span class="tch-kelas-badge">{{ $s['kelas'] }}</span>
                                <span class="tch-sched-mapel">{{ $s['mapel'] }}</span>
                            </div>
                            <div class="tch-sched-meta">
                                <span><i class="ri-map-pin-line"></i> {{ $s['ruangan'] }}</span>
                                <span>Absensi: <span class="badge {{ $badgeClass[$s['status_absensi']] }}">{{ $badgeLabel[$s['status_absensi']] }}</span></span>
                                <span>Jurnal: <span class="badge {{ $badgeClass[$s['status_jurnal']] }}">{{ $badgeLabel[$s['status_jurnal']] }}</span></span>
                            </div>
                        </div>
                        {{-- actions --}}
                        <div class="tch-sched-acts">
                            <a href="#" class="tch-act-btn" title="Isi Absensi"><i class="ri-survey-line"></i></a>
                            <a href="#" class="tch-act-btn" title="Input Jurnal"><i class="ri-book-open-line"></i></a>
                            <a href="#" class="tch-act-btn" title="Upload Materi"><i class="ri-upload-cloud-line"></i></a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="tch-empty">
                    <div class="tch-empty-icon"><i class="ri-calendar-2-line"></i></div>
                    <div class="tch-empty-title">Tidak ada jadwal hari ini</div>
                    <p class="tch-empty-sub">Nikmati hari bebas mengajar Anda! ☕</p>
                </div>
                @endif
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="card" style="overflow:visible">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="ri-flashlight-line" style="color:var(--warning);margin-right:6px;"></i>
                            Akses Cepat
                        </div>
                        <div class="card-subtitle">Shortcut fitur yang sering digunakan</div>
                    </div>
                </div>
                <div class="tch-quick-grid">
                    @php $quickActions = [
                        ['icon'=>'ri-pencil-ruler-2-line','label'=>'Input Nilai',     'color'=>'blue',   'href'=>'#'],
                        ['icon'=>'ri-task-line',           'label'=>'Buat Tugas',     'color'=>'green',  'href'=>'#'],
                        ['icon'=>'ri-upload-cloud-2-line', 'label'=>'Upload Materi',  'color'=>'violet', 'href'=>'#'],
                        ['icon'=>'ri-survey-line',         'label'=>'Absensi Kelas',  'color'=>'yellow', 'href'=>'#'],
                        ['icon'=>'ri-calendar-check-line', 'label'=>'Lihat Jadwal',   'color'=>'teal',   'href'=>'#'],
                        ['icon'=>'ri-book-2-line',         'label'=>'Jurnal Mengajar','color'=>'orange', 'href'=>'#'],
                    ]; @endphp
                    @foreach($quickActions as $qa)
                    <a href="{{ $qa['href'] }}" class="tch-quick-card tch-quick-{{ $qa['color'] }}">
                        <div class="tch-quick-icon"><i class="{{ $qa['icon'] }}"></i></div>
                        <span>{{ $qa['label'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- PENDING ASSIGNMENTS --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="ri-file-edit-line" style="color:var(--danger);margin-right:6px;"></i>
                            Tugas Belum Dinilai
                        </div>
                        <div class="card-subtitle">
                            {{ count($pendingAssignments) > 0 ? count($pendingAssignments).' tugas menunggu review' : 'Semua tugas sudah dinilai' }}
                        </div>
                    </div>
                    @if(count($pendingAssignments) > 0)
                    <a href="#" class="card-action">Lihat Semua</a>
                    @endif
                </div>
                @if(count($pendingAssignments) > 0)
                <div style="padding:8px 0">
                    @foreach($pendingAssignments as $pa)
                    <div class="tch-assign-row">
                        <div class="tch-assign-icon"><i class="ri-file-list-3-line"></i></div>
                        <div class="tch-assign-info">
                            <div class="tch-assign-nama">{{ $pa['nama'] }}</div>
                            <div class="tch-assign-meta">
                                <span class="tch-kelas-badge" style="font-size:10px;padding:1px 7px">{{ $pa['kelas'] }}</span>
                                <span><i class="ri-time-line"></i> {{ $pa['deadline'] }}</span>
                            </div>
                        </div>
                        <div class="tch-assign-count">
                            <span class="tch-pending-num">{{ $pa['pending'] }}</span>
                            <span class="tch-pending-label">submission</span>
                        </div>
                        <a href="#" class="tch-review-btn"><i class="ri-eye-line"></i> Review</a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="tch-empty" style="padding:32px 24px">
                    <div class="tch-empty-icon"><i class="ri-checkbox-circle-line" style="color:#10B981"></i></div>
                    <div class="tch-empty-title">Semua sudah dinilai</div>
                    <p class="tch-empty-sub">Tidak ada submission yang menunggu. Kerja bagus! 🎉</p>
                </div>
                @endif
            </div>

        </div>
        {{-- /col-main --}}

        {{-- RIGHT SIDEBAR --}}
        <div class="tch-col-side">

            {{-- ATTENDANCE SUMMARY --}}
            <div class="card">
                <div class="card-header" style="border-bottom:none;padding-bottom:0">
                    <div>
                        <div class="card-title">
                            <i class="ri-bar-chart-grouped-line" style="color:var(--primary);margin-right:6px;"></i>
                            Rekap Kehadiran
                        </div>
                        <div class="card-subtitle">{{ $attendanceSummary['total'] }} total siswa</div>
                    </div>
                </div>
                <div class="tch-attend-wrap">
                    @php $attendItems = [
                        ['label'=>'Hadir','key'=>'hadir','color'=>'#10B981','bg'=>'#ECFDF5'],
                        ['label'=>'Izin', 'key'=>'izin', 'color'=>'#3B82F6','bg'=>'#EFF6FF'],
                        ['label'=>'Sakit','key'=>'sakit','color'=>'#F59E0B','bg'=>'#FFFBEB'],
                        ['label'=>'Alpha','key'=>'alpha','color'=>'#EF4444','bg'=>'#FEF2F2'],
                    ]; @endphp
                    @foreach($attendItems as $ai)
                    @php $pct = $attendanceSummary['total'] > 0
                        ? round($attendanceSummary[$ai['key']] / $attendanceSummary['total'] * 100) : 0; @endphp
                    <div class="tch-attend-row">
                        <div class="tch-attend-label" style="color:{{ $ai['color'] }}">{{ $ai['label'] }}</div>
                        <div class="tch-attend-bar-wrap">
                            <div class="tch-attend-bar" style="background:{{ $ai['bg'] }}">
                                <div class="tch-attend-bar-fill"
                                     style="width:{{ $pct }}%;background:{{ $ai['color'] }}"
                                     data-pct="{{ $pct }}"></div>
                            </div>
                        </div>
                        <div class="tch-attend-num">
                            <strong>{{ $attendanceSummary[$ai['key']] }}</strong>
                            <span>{{ $pct }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ANNOUNCEMENTS --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="ri-megaphone-line" style="color:var(--warning);margin-right:6px;"></i>
                            Pengumuman
                        </div>
                    </div>
                    <a href="#" class="card-action">Semua</a>
                </div>
                <div class="tch-announce-list">
                    @foreach($announcements as $ann)
                    <div class="tch-announce-item">
                        <div class="tch-announce-dot"></div>
                        <div>
                            <div class="tch-announce-judul">{{ $ann['judul'] }}</div>
                            <div class="tch-announce-tanggal"><i class="ri-calendar-event-line"></i> {{ $ann['tanggal'] }}</div>
                            <p class="tch-announce-isi">{{ $ann['isi'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ACADEMIC CALENDAR --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="ri-calendar-todo-line" style="color:var(--primary);margin-right:6px;"></i>
                            Kalender Akademik
                        </div>
                        <div class="card-subtitle">Agenda mendatang</div>
                    </div>
                </div>
                <div class="tch-calendar-list">
                    @php $eventColors = [
                        'deadline'=>['bg'=>'#FEF2F2','color'=>'#EF4444','icon'=>'ri-alarm-warning-line'],
                        'ujian'   =>['bg'=>'#EFF6FF','color'=>'#3B82F6','icon'=>'ri-edit-box-line'],
                        'agenda'  =>['bg'=>'#ECFDF5','color'=>'#10B981','icon'=>'ri-flag-line'],
                        'libur'   =>['bg'=>'#F5F3FF','color'=>'#8B5CF6','icon'=>'ri-sun-line'],
                    ]; @endphp
                    @foreach($academicEvents as $ev)
                    @php $ec = $eventColors[$ev['tipe']]; @endphp
                    <div class="tch-cal-item">
                        <div class="tch-cal-date" style="background:{{ $ec['bg'] }};color:{{ $ec['color'] }}">
                            <i class="{{ $ec['icon'] }}"></i>
                            <span>{{ $ev['tanggal'] }}</span>
                        </div>
                        <span class="tch-cal-label">{{ $ev['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- RECENT ACTIVITY --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">
                            <i class="ri-history-line" style="color:var(--primary);margin-right:6px;"></i>
                            Aktivitas Terbaru
                        </div>
                    </div>
                </div>
                <div class="tch-timeline">
                    @foreach($recentActivities as $act)
                    <div class="tch-timeline-item">
                        <div class="tch-timeline-dot tch-dot-{{ $act['color'] }}">
                            <i class="{{ $act['icon'] }}"></i>
                        </div>
                        <div class="tch-timeline-body">
                            <p class="tch-timeline-teks">{{ $act['teks'] }}</p>
                            <span class="tch-timeline-waktu"><i class="ri-time-line"></i> {{ $act['waktu'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
        {{-- /col-side --}}

    </div>
</div>

@endsection

@push('styles')
<style>
/* ══════════════════════════════════════════════
   TEACHER DASHBOARD — Scoped Styles v2
   Fixes: no horizontal scroll, compact schedule
══════════════════════════════════════════════ */

/* ── Outer wrapper: no overflow-x ── */
.tch-wrap {
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-width: 0;
    width: 100%;
}

/* ── Greeting ── */
.tch-greeting {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    background: linear-gradient(135deg, var(--primary) 0%, #1D4ED8 60%, #1e3a8a 100%);
    border-radius: var(--radius);
    padding: 22px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(59,130,246,.35);
    animation: fadeUp .45s ease both;
    flex-shrink: 0;
}

.tch-greeting::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,.06); pointer-events: none;
}

.tch-greeting-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 0; }

.tch-avatar {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: rgba(255,255,255,.2);
    border: 2px solid rgba(255,255,255,.3);
    display: grid; place-items: center;
    font-size: 18px; font-weight: 800; color: #fff;
    flex-shrink: 0; overflow: hidden;
}

.tch-avatar img { width: 100%; height: 100%; object-fit: cover; }

.tch-greeting-title {
    font-size: 20px; font-weight: 700;
    color: #fff; letter-spacing: -.4px; margin: 0 0 5px;
}

.tch-greeting-sub {
    font-size: 13px; color: rgba(255,255,255,.85);
    margin: 0 0 6px;
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}

.tch-greeting-sub strong { font-weight: 700; color: #fff; }

.tch-sem-badge {
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 99px; padding: 2px 9px;
    font-size: 11px; font-weight: 600;
    color: rgba(255,255,255,.9);
}

.tch-greeting-meta {
    font-size: 12px; color: rgba(255,255,255,.6);
    display: flex; align-items: center; gap: 5px; margin: 0; flex-wrap: wrap;
}

.tch-greeting-meta i { font-size: 13px; }

.tch-date-card {
    text-align: center;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 12px; padding: 14px 20px;
    backdrop-filter: blur(8px); flex-shrink: 0;
}

.tch-date-day   { font-size: 38px; font-weight: 800; color: #fff; line-height: 1; letter-spacing: -2px; }
.tch-date-month { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.75); text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }
.tch-date-week  { font-size: 11px; color: rgba(255,255,255,.5); margin-top: 3px; }

/* ── Stats: 5 col on wide, fewer on small ── */
.tch-stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}

.tch-stat {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px;
    box-shadow: var(--shadow);
    position: relative; overflow: hidden;
    transition: box-shadow .2s, transform .2s;
    animation: fadeUp .4s ease both;
}

.tch-stat:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

.tch-stat::after {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
}

.tch-stat.blue::after   { background: linear-gradient(90deg, var(--primary), var(--primary-light)); }
.tch-stat.green::after  { background: linear-gradient(90deg, #10B981, #6EE7B7); }
.tch-stat.yellow::after { background: linear-gradient(90deg, #F59E0B, #FCD34D); }
.tch-stat.orange::after { background: linear-gradient(90deg, #F97316, #FDBA74); }
.tch-stat.red::after    { background: linear-gradient(90deg, #EF4444, #FCA5A5); }

.tch-stat-top {
    display: flex; align-items: flex-start;
    justify-content: space-between; margin-bottom: 12px;
}

.tch-stat-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: grid; place-items: center;
}

.tch-stat-icon i { font-size: 17px; }
.tch-stat-icon.blue   { background: #EFF6FF; color: var(--primary); }
.tch-stat-icon.green  { background: #ECFDF5; color: #10B981; }
.tch-stat-icon.yellow { background: #FFFBEB; color: #F59E0B; }
.tch-stat-icon.orange { background: #FFF7ED; color: #F97316; }
.tch-stat-icon.red    { background: #FEF2F2; color: #EF4444; }

.tch-stat-tag {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 600; padding: 2px 6px; border-radius: 6px;
}

.tch-stat-tag.warn { color: var(--danger); background: #FEF2F2; }
.tch-stat-tag.ok   { color: var(--text-muted); background: var(--bg); }
.tch-stat-tag i    { font-size: 10px; }

.tch-stat-val   { font-size: 26px; font-weight: 700; color: var(--text-primary); letter-spacing: -1px; line-height: 1; margin-bottom: 3px; }
.tch-stat-label { font-size: 12px; color: var(--text-secondary); font-weight: 500; }
.tch-stat-sub   { margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--border); font-size: 11px; color: var(--text-muted); }

/* ── Main 2-col grid ── */
.tch-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 16px;
    align-items: start;
    min-width: 0;
}

.tch-col-main { display: flex; flex-direction: column; gap: 16px; min-width: 0; }
.tch-col-side { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

/* ── Schedule as card list (no wide table) ── */
.tch-sched-list { padding: 8px 0; }

.tch-sched-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid #F1F5F9;
    transition: background .12s;
}

.tch-sched-item:last-child { border-bottom: none; }
.tch-sched-item:hover { background: #F8FAFF; }

.tch-sched-time {
    display: flex; flex-direction: column; align-items: center;
    gap: 3px; flex-shrink: 0; width: 72px;
    background: var(--bg); border: 1px solid var(--border);
    border-radius: 8px; padding: 7px 6px; text-align: center;
}

.tch-sched-time i { font-size: 13px; color: var(--primary); }
.tch-sched-time span { font-size: 11px; font-weight: 700; color: var(--text-primary); font-family: var(--font-mono); white-space: nowrap; }

.tch-sched-info { flex: 1; min-width: 0; }

.tch-sched-top {
    display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;
}

.tch-sched-mapel { font-size: 14px; font-weight: 600; color: var(--text-primary); }

.tch-sched-meta {
    display: flex; align-items: center; gap: 10px;
    font-size: 12px; color: var(--text-muted); flex-wrap: wrap;
}

.tch-sched-meta i { font-size: 12px; }

.tch-sched-acts {
    display: flex; align-items: center; gap: 4px; flex-shrink: 0;
}

.tch-kelas-badge {
    display: inline-block;
    background: var(--primary-xlight);
    color: var(--primary-dark);
    border: 1px solid var(--primary-light);
    border-radius: 6px;
    font-size: 11.5px; font-weight: 700;
    padding: 2px 8px; white-space: nowrap;
}

.tch-act-btn {
    width: 28px; height: 28px; border-radius: 7px;
    border: 1.5px solid var(--border); background: var(--card);
    display: grid; place-items: center;
    color: var(--text-muted); font-size: 14px;
    text-decoration: none; transition: all .15s;
}

.tch-act-btn:hover {
    border-color: var(--primary-light); color: var(--primary);
    background: var(--bg); transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(59,130,246,.15);
}

/* ── Quick Actions ── */
.tch-quick-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 10px;
    padding: 14px 18px 18px;
}

.tch-quick-card {
    display: flex; flex-direction: column; align-items: center; gap: 7px;
    padding: 14px 8px;
    border-radius: 11px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    text-decoration: none;
    color: var(--text-secondary);
    font-size: 11px; font-weight: 600;
    text-align: center; transition: all .2s; cursor: pointer;
}

.tch-quick-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(59,130,246,.15); }

.tch-quick-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: grid; place-items: center; font-size: 19px;
    transition: transform .2s;
}

.tch-quick-card:hover .tch-quick-icon { transform: scale(1.1); }

.tch-quick-blue:hover   { border-color:#BFDBFE;background:#EFF6FF;color:#1D4ED8; }
.tch-quick-blue   .tch-quick-icon { background:#EFF6FF;color:#3B82F6; }
.tch-quick-green:hover  { border-color:#A7F3D0;background:#ECFDF5;color:#065F46; }
.tch-quick-green  .tch-quick-icon { background:#ECFDF5;color:#10B981; }
.tch-quick-violet:hover { border-color:#DDD6FE;background:#F5F3FF;color:#5B21B6; }
.tch-quick-violet .tch-quick-icon { background:#F5F3FF;color:#8B5CF6; }
.tch-quick-yellow:hover { border-color:#FDE68A;background:#FFFBEB;color:#92400E; }
.tch-quick-yellow .tch-quick-icon { background:#FFFBEB;color:#F59E0B; }
.tch-quick-teal:hover   { border-color:#99F6E4;background:#F0FDFA;color:#0F766E; }
.tch-quick-teal   .tch-quick-icon { background:#F0FDFA;color:#0D9488; }
.tch-quick-orange:hover { border-color:#FED7AA;background:#FFF7ED;color:#C2410C; }
.tch-quick-orange .tch-quick-icon { background:#FFF7ED;color:#F97316; }

/* ── Assignments ── */
.tch-assign-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid #F1F5F9;
    transition: background .12s;
}

.tch-assign-row:last-child { border-bottom: none; }
.tch-assign-row:hover { background: #F8FAFF; }

.tch-assign-icon {
    width: 34px; height: 34px; border-radius: 9px;
    background: var(--primary-xlight); color: var(--primary);
    display: grid; place-items: center; font-size: 17px; flex-shrink: 0;
}

.tch-assign-info { flex: 1; min-width: 0; }

.tch-assign-nama {
    font-size: 13px; font-weight: 600; color: var(--text-primary);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.tch-assign-meta {
    display: flex; align-items: center; gap: 7px;
    margin-top: 3px; font-size: 11px; color: var(--text-muted);
}

.tch-assign-count { text-align: center; flex-shrink: 0; }

.tch-pending-num  { display: block; font-size: 18px; font-weight: 800; color: var(--danger); line-height: 1; }
.tch-pending-label { font-size: 10px; color: var(--text-muted); font-weight: 500; }

.tch-review-btn {
    display: inline-flex; align-items: center; gap: 4px;
    height: 28px; padding: 0 10px; border-radius: 7px;
    border: 1.5px solid var(--primary-light);
    background: var(--primary-xlight); color: var(--primary-dark);
    font-size: 11px; font-weight: 600;
    text-decoration: none; transition: all .15s; flex-shrink: 0;
}

.tch-review-btn:hover {
    background: var(--primary); border-color: var(--primary); color: #fff;
    transform: translateY(-1px); box-shadow: 0 4px 10px rgba(59,130,246,.3);
}

/* ── Attendance ── */
.tch-attend-wrap { padding: 12px 20px 18px; }

.tch-attend-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.tch-attend-row:last-child { margin-bottom: 0; }

.tch-attend-label { width: 40px; font-size: 12px; font-weight: 700; flex-shrink: 0; }
.tch-attend-bar-wrap { flex: 1; }
.tch-attend-bar { height: 7px; border-radius: 99px; overflow: hidden; }
.tch-attend-bar-fill { height: 100%; border-radius: 99px; width: 0; transition: width 1s cubic-bezier(.4,0,.2,1); }

.tch-attend-num { display: flex; flex-direction: column; align-items: flex-end; min-width: 46px; }
.tch-attend-num strong { font-size: 13px; font-weight: 700; color: var(--text-primary); }
.tch-attend-num span   { font-size: 10px; color: var(--text-muted); }

/* ── Announcements ── */
.tch-announce-list { padding: 0 20px 8px; }

.tch-announce-item { display: flex; gap: 10px; padding: 12px 0; border-bottom: 1px solid #F1F5F9; }
.tch-announce-item:last-child { border-bottom: none; }

.tch-announce-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--warning); flex-shrink: 0; margin-top: 5px; }

.tch-announce-judul { font-size: 12.5px; font-weight: 600; color: var(--text-primary); line-height: 1.4; }
.tch-announce-tanggal { font-size: 11px; color: var(--text-muted); margin: 2px 0; display: flex; align-items: center; gap: 3px; }
.tch-announce-isi { font-size: 11.5px; color: var(--text-secondary); line-height: 1.5; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

/* ── Calendar ── */
.tch-calendar-list { padding: 8px 20px 14px; display: flex; flex-direction: column; gap: 7px; }

.tch-cal-item { display: flex; align-items: center; gap: 9px; }

.tch-cal-date {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 9px; border-radius: 7px;
    font-size: 11px; font-weight: 700; flex-shrink: 0; white-space: nowrap;
}

.tch-cal-date i { font-size: 11px; }
.tch-cal-label  { font-size: 12px; font-weight: 500; color: var(--text-primary); line-height: 1.4; }

/* ── Timeline ── */
.tch-timeline { padding: 10px 20px 14px; display: flex; flex-direction: column; }

.tch-timeline-item { display: flex; gap: 10px; padding-bottom: 14px; position: relative; }

.tch-timeline-item:not(:last-child)::before {
    content: ''; position: absolute;
    left: 14px; top: 30px; width: 1px; bottom: 0;
    background: var(--border);
}

.tch-timeline-dot {
    width: 29px; height: 29px; border-radius: 8px;
    display: grid; place-items: center; font-size: 14px;
    flex-shrink: 0; position: relative; z-index: 1;
}

.tch-dot-green  { background: #ECFDF5; color: #10B981; }
.tch-dot-blue   { background: #EFF6FF; color: #3B82F6; }
.tch-dot-violet { background: #F5F3FF; color: #8B5CF6; }
.tch-dot-orange { background: #FFF7ED; color: #F97316; }

.tch-timeline-body { flex: 1; padding-top: 3px; }
.tch-timeline-teks { font-size: 12.5px; font-weight: 500; color: var(--text-primary); margin: 0 0 2px; line-height: 1.4; }
.tch-timeline-waktu { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 3px; font-family: var(--font-mono); }

/* ── Empty ── */
.tch-empty { display: flex; flex-direction: column; align-items: center; padding: 40px 24px; text-align: center; gap: 8px; }
.tch-empty-icon { width: 56px; height: 56px; border-radius: 14px; background: var(--bg); border: 1.5px solid var(--border); display: grid; place-items: center; margin-bottom: 4px; }
.tch-empty-icon i { font-size: 24px; color: var(--primary-light); }
.tch-empty-title { font-size: 13.5px; font-weight: 700; color: var(--text-primary); }
.tch-empty-sub   { font-size: 12px; color: var(--text-muted); max-width: 220px; line-height: 1.6; margin: 0; }

/* ══════════════════════
   RESPONSIVE
══════════════════════ */
@media (max-width: 1200px) {
    .tch-stats { grid-template-columns: repeat(3, 1fr); }
    .tch-grid  { grid-template-columns: 1fr 280px; }
}

@media (max-width: 1024px) {
    .tch-grid  { grid-template-columns: 1fr; }
    .tch-col-side { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .tch-quick-grid { grid-template-columns: repeat(6, 1fr); }
}

@media (max-width: 768px) {
    .tch-greeting  { flex-direction: column; align-items: flex-start; padding: 18px 20px; }
    .tch-date-card { display: flex; align-items: center; gap: 10px; padding: 10px 16px; width: 100%; }
    .tch-date-day  { font-size: 28px; }
    .tch-stats     { grid-template-columns: repeat(2, 1fr); }
    .tch-quick-grid { grid-template-columns: repeat(3, 1fr); }
    .tch-col-side  { grid-template-columns: 1fr; }
    .tch-sched-acts { display: none; }
}

@media (max-width: 480px) {
    .tch-stats { grid-template-columns: 1fr 1fr; }
    .tch-quick-grid { grid-template-columns: repeat(2, 1fr); }
    .tch-greeting-title { font-size: 17px; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        document.querySelectorAll('.tch-attend-bar-fill').forEach(bar => {
            bar.style.width = bar.getAttribute('data-pct') + '%';
        });
    }, 400);
});
</script>
@endpush