@extends('layouts.app')

@section('title', 'Detail Sesi Presensi')

@section('content')

@php
    // ── Safeguard ──────────────────────────────────────────────
    $detail ??= [
        'all'        => collect(),
        'present'    => collect(),
        'permission' => collect(),
        'sick'       => collect(),
        'absent'     => collect(),
        'late'       => collect(),
        'counts'     => [
            'present'    => 0,
            'permission' => 0,
            'sick'       => 0,
            'absent'     => 0,
            'late'       => 0,
            'total'      => 0,
        ],
    ];

    // $session = $detail['all']->first()?->session ?? null;
    $session ??= null;
    $counts  = $detail['counts'];
    $total   = $counts['total'] ?: 1; // hindari division by zero

    $attendanceRate = $total > 0
        ? round($counts['present'] / $total * 100, 1)
        : 0;
@endphp

{{-- ── PAGE HEADER ── --}}
<div class="ar-header">
    <div class="ar-header-left">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <a href="{{ route('teacher.reports.attendance.index') }}"
               class="ar-btn ar-btn-outline" style="height:32px;padding:0 12px;font-size:12px">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
        </div>
        <h1 class="ar-title">
            <i class="ri-file-list-3-line"></i>
            Detail Sesi Presensi
        </h1>
        @if($session)
        <p class="ar-subtitle">
            {{ $session->grade?->grade_name ?? '-' }} ·
            {{ $session->subject?->subject_name ?? '-' }} ·
            {{ $session->attendance_date->translatedFormat('l, d F Y') }}
        </p>
        @endif
    </div>
    <div class="ar-header-actions">
        <a href="{{ route('teacher.reports.attendance.export', ['session_id' => $session?->attendance_session_id]) }}"
           class="ar-btn ar-btn-export">
            <i class="ri-download-2-line"></i> Export
        </a>
    </div>
</div>

{{-- ── SESSION INFO CARD ── --}}
@if($session)
<div class="ar-show-info">
    <div class="ar-show-info-grid">

        <div class="ar-show-info-item">
            <div class="ar-show-info-label">
                <i class="ri-building-4-line"></i> Kelas
            </div>
            <div class="ar-show-info-val">
                {{ $session->grade?->grade_name ?? '-' }}
            </div>
        </div>

        <div class="ar-show-info-item">
            <div class="ar-show-info-label">
                <i class="ri-book-open-line"></i> Mata Pelajaran
            </div>
            <div class="ar-show-info-val">
                {{ $session->subject?->subject_name ?? '-' }}
            </div>
        </div>

        <div class="ar-show-info-item">
            <div class="ar-show-info-label">
                <i class="ri-calendar-2-line"></i> Tanggal
            </div>
            <div class="ar-show-info-val">
                {{ $session->attendance_date->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <div class="ar-show-info-item">
            <div class="ar-show-info-label">
                <i class="ri-sort-asc"></i> Pertemuan ke-
            </div>
            <div class="ar-show-info-val">
                {{ $session->meeting_number }}
            </div>
        </div>

        <div class="ar-show-info-item">
            <div class="ar-show-info-label">
                <i class="ri-split-cells-horizontal"></i> Semester
            </div>
            <div class="ar-show-info-val">
                {{ $session->semester?->semester_name ?? '-' }}
            </div>
        </div>

        <div class="ar-show-info-item">
            <div class="ar-show-info-label">
                <i class="ri-checkbox-circle-line"></i> Status Sesi
            </div>
            <div class="ar-show-info-val">
                @php
                    $statusMap = [
                        'draft'     => ['class' => 'ar-status izin',  'label' => 'Draft'],
                        'submitted' => ['class' => 'ar-status hadir', 'label' => 'Submitted'],
                        'approved'  => ['class' => 'ar-status hadir', 'label' => 'Approved'],
                    ];
                    $st = $statusMap[$session->status] ?? ['class' => 'ar-status', 'label' => $session->status];
                @endphp
                <span class="{{ $st['class'] }}">{{ $st['label'] }}</span>
                @if($session->is_locked)
                    <span class="ar-status alpha" style="margin-left:4px">
                        <i class="ri-lock-line" style="font-size:10px"></i> Terkunci
                    </span>
                @endif
            </div>
        </div>

        @if($session->notes)
        <div class="ar-show-info-item" style="grid-column: 1 / -1">
            <div class="ar-show-info-label">
                <i class="ri-sticky-note-line"></i> Catatan
            </div>
            <div class="ar-show-info-val" style="font-weight:400;color:var(--text-secondary)">
                {{ $session->notes }}
            </div>
        </div>
        @endif

    </div>
</div>
@endif

{{-- ── SUMMARY MINI CARDS ── --}}
<div class="ar-show-summary">

    <div class="ar-show-sum-card green">
        <div class="ar-show-sum-top">
            <div class="ar-show-sum-icon green"><i class="ri-checkbox-circle-line"></i></div>
            <span class="ar-show-sum-pct {{ $attendanceRate >= 80 ? 'ar-rate-good' : ($attendanceRate >= 60 ? 'ar-rate-warn' : 'ar-rate-bad') }}">
                {{ $attendanceRate }}%
            </span>
        </div>
        <div class="ar-show-sum-val">{{ $counts['present'] }}</div>
        <div class="ar-show-sum-label">Hadir</div>
    </div>

    <div class="ar-show-sum-card sky">
        <div class="ar-show-sum-top">
            <div class="ar-show-sum-icon sky"><i class="ri-file-text-line"></i></div>
            <span class="ar-show-sum-pct">
                {{ $counts['total'] > 0 ? round($counts['permission'] / $counts['total'] * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="ar-show-sum-val">{{ $counts['permission'] }}</div>
        <div class="ar-show-sum-label">Izin</div>
    </div>

    <div class="ar-show-sum-card yellow">
        <div class="ar-show-sum-top">
            <div class="ar-show-sum-icon yellow"><i class="ri-heart-pulse-line"></i></div>
            <span class="ar-show-sum-pct">
                {{ $counts['total'] > 0 ? round($counts['sick'] / $counts['total'] * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="ar-show-sum-val">{{ $counts['sick'] }}</div>
        <div class="ar-show-sum-label">Sakit</div>
    </div>

    <div class="ar-show-sum-card red">
        <div class="ar-show-sum-top">
            <div class="ar-show-sum-icon red"><i class="ri-close-circle-line"></i></div>
            <span class="ar-show-sum-pct ar-rate-bad">
                {{ $counts['total'] > 0 ? round($counts['absent'] / $counts['total'] * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="ar-show-sum-val">{{ $counts['absent'] }}</div>
        <div class="ar-show-sum-label">Alpha</div>
    </div>

    <div class="ar-show-sum-card slate">
        <div class="ar-show-sum-top">
            <div class="ar-show-sum-icon slate"><i class="ri-time-line"></i></div>
            <span class="ar-show-sum-pct">
                {{ $counts['total'] > 0 ? round($counts['late'] / $counts['total'] * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="ar-show-sum-val">{{ $counts['late'] }}</div>
        <div class="ar-show-sum-label">Terlambat</div>
    </div>

    {{-- Progress bar kehadiran --}}
    <div class="ar-show-sum-card blue" style="grid-column: 1 / -1">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div class="ar-show-sum-label" style="font-size:12px;font-weight:600;color:var(--text-primary)">
                Distribusi Kehadiran
            </div>
            <span style="font-size:12px;color:var(--text-muted)">
                Total {{ $counts['total'] }} siswa
            </span>
        </div>
        <div class="ar-dist-bar">
            @if($counts['present'] > 0)
            <div class="ar-dist-segment green"
                 style="width:{{ round($counts['present']/$total*100,1) }}%"
                 title="Hadir: {{ $counts['present'] }}"></div>
            @endif
            @if($counts['permission'] > 0)
            <div class="ar-dist-segment sky"
                 style="width:{{ round($counts['permission']/$total*100,1) }}%"
                 title="Izin: {{ $counts['permission'] }}"></div>
            @endif
            @if($counts['sick'] > 0)
            <div class="ar-dist-segment yellow"
                 style="width:{{ round($counts['sick']/$total*100,1) }}%"
                 title="Sakit: {{ $counts['sick'] }}"></div>
            @endif
            @if($counts['absent'] > 0)
            <div class="ar-dist-segment red"
                 style="width:{{ round($counts['absent']/$total*100,1) }}%"
                 title="Alpha: {{ $counts['absent'] }}"></div>
            @endif
            @if($counts['late'] > 0)
            <div class="ar-dist-segment slate"
                 style="width:{{ round($counts['late']/$total*100,1) }}%"
                 title="Terlambat: {{ $counts['late'] }}"></div>
            @endif
        </div>
        <div class="ar-dist-legend">
            <span class="ar-dist-leg-item green">Hadir {{ $counts['present'] }}</span>
            <span class="ar-dist-leg-item sky">Izin {{ $counts['permission'] }}</span>
            <span class="ar-dist-leg-item yellow">Sakit {{ $counts['sick'] }}</span>
            <span class="ar-dist-leg-item red">Alpha {{ $counts['absent'] }}</span>
            <span class="ar-dist-leg-item slate">Terlambat {{ $counts['late'] }}</span>
        </div>
    </div>

</div>

{{-- ── STUDENT LIST WITH STATUS TABS ── --}}
<div x-data="{ statusTab: 'all' }">

    {{-- Status Tab Toggle --}}
    <div class="ar-tabs" style="flex-wrap:wrap">
        @php
            $statusTabs = [
                ['key' => 'all',        'label' => 'Semua',     'count' => $counts['total'],      'icon' => 'ri-group-line'],
                ['key' => 'present',    'label' => 'Hadir',     'count' => $counts['present'],    'icon' => 'ri-checkbox-circle-line'],
                ['key' => 'permission', 'label' => 'Izin',      'count' => $counts['permission'], 'icon' => 'ri-file-text-line'],
                ['key' => 'sick',       'label' => 'Sakit',     'count' => $counts['sick'],       'icon' => 'ri-heart-pulse-line'],
                ['key' => 'absent',     'label' => 'Alpha',     'count' => $counts['absent'],     'icon' => 'ri-close-circle-line'],
                ['key' => 'late',       'label' => 'Terlambat', 'count' => $counts['late'],       'icon' => 'ri-time-line'],
            ];
        @endphp
        @foreach($statusTabs as $t)
        <button class="ar-tab"
                :class="{ active: statusTab === '{{ $t['key'] }}' }"
                @click="statusTab = '{{ $t['key'] }}'"
                type="button">
            <i class="{{ $t['icon'] }}"></i>
            {{ $t['label'] }}
            <span class="ar-tab-count">{{ $t['count'] }}</span>
        </button>
        @endforeach
    </div>

    {{-- Table per status --}}
    @foreach(['all', 'present', 'permission', 'sick', 'absent', 'late'] as $statusKey)
    @php
        $rows = $detail[$statusKey];
    @endphp
    <div x-show="statusTab === '{{ $statusKey }}'"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         {{ $statusKey !== 'all' ? 'style=display:none' : '' }}>

        <div class="ar-table-wrap">
            <div class="ar-table-header">
                <div class="ar-table-title">
                    <i class="ri-user-3-line"></i>
                    Daftar Siswa
                    <span class="ar-table-count">{{ $rows->count() }} siswa</span>
                </div>
            </div>

            @if($rows->count() > 0)
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th class="col-no">#</th>
                            <th>Siswa</th>
                            <th>NIS</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Notifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $d)
                        @php
                            $statusClassMap = [
                                'H' => 'hadir',
                                'I' => 'izin',
                                'S' => 'sakit',
                                'A' => 'alpha',
                                'L' => 'terlambat',
                            ];
                            $statusClass = $statusClassMap[$d->status] ?? '';
                        @endphp
                        <tr>
                            <td class="col-no dt-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="dt-user">
                                    <div class="dt-av av-blue">
                                        {{ strtoupper(substr($d->student?->full_name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="dt-user-name">
                                            {{ $d->student?->full_name ?? '-' }}
                                        </div>
                                        <div class="dt-user-email">
                                            {{ $d->student?->class_group ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="dt-mono">{{ $d->student?->nis ?? '-' }}</td>
                            <td>
                                <span class="ar-status {{ $statusClass }}">
                                    {{ $d->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($d->note)
                                    <span style="font-size:12.5px;color:var(--text-secondary)">
                                        {{ $d->note }}
                                    </span>
                                @else
                                    <span class="dt-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($d->notified_at)
                                    <span class="ar-status hadir" style="font-size:10.5px">
                                        <i class="ri-mail-check-line" style="font-size:10px"></i>
                                        {{ $d->notified_at->format('H:i') }}
                                    </span>
                                @else
                                    <span class="dt-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="ar-empty">
                <div class="ar-empty-icon"><i class="ri-user-3-line"></i></div>
                <div class="ar-empty-title">Tidak ada siswa</div>
                <p class="ar-empty-sub">Tidak ada siswa dengan status ini pada sesi ini.</p>
            </div>
            @endif
        </div>
    </div>
    @endforeach

</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports/attendance.css') }}">
<style>
/* ── Show page specific ─────────────────────────────── */

/* Session info card */
.ar-show-info {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 20px;
}

.ar-show-info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px 24px;
}

.ar-show-info-item {}

.ar-show-info-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 5px;
}

.ar-show-info-label i { font-size: 13px; color: var(--primary); }

.ar-show-info-val {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

/* Mini summary cards (show page) */
.ar-show-summary {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}

.ar-show-sum-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
    animation: fadeUp .35s ease both;
}

.ar-show-sum-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.ar-show-sum-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
}

.ar-show-sum-card.green::after  { background: linear-gradient(90deg, #10B981, #6EE7B7); }
.ar-show-sum-card.sky::after    { background: linear-gradient(90deg, #0EA5E9, #7DD3FC); }
.ar-show-sum-card.yellow::after { background: linear-gradient(90deg, #F59E0B, #FCD34D); }
.ar-show-sum-card.red::after    { background: linear-gradient(90deg, #EF4444, #FCA5A5); }
.ar-show-sum-card.slate::after  { background: linear-gradient(90deg, #64748B, #94A3B8); }
.ar-show-sum-card.blue::after   { background: linear-gradient(90deg, #3B82F6, #93C5FD); }

.ar-show-sum-card:nth-child(1) { animation-delay: .04s; }
.ar-show-sum-card:nth-child(2) { animation-delay: .08s; }
.ar-show-sum-card:nth-child(3) { animation-delay: .12s; }
.ar-show-sum-card:nth-child(4) { animation-delay: .16s; }
.ar-show-sum-card:nth-child(5) { animation-delay: .20s; }
.ar-show-sum-card:nth-child(6) { animation-delay: .24s; }

.ar-show-sum-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 10px;
}

.ar-show-sum-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: grid;
    place-items: center;
}

.ar-show-sum-icon i { font-size: 16px; }
.ar-show-sum-icon.green  { background: #ECFDF5; color: #10B981; }
.ar-show-sum-icon.sky    { background: #F0F9FF; color: #0EA5E9; }
.ar-show-sum-icon.yellow { background: #FFFBEB; color: #F59E0B; }
.ar-show-sum-icon.red    { background: #FEF2F2; color: #EF4444; }
.ar-show-sum-icon.slate  { background: #F8FAFC; color: #64748B; }
.ar-show-sum-icon.blue   { background: #EFF6FF; color: #3B82F6; }

.ar-show-sum-pct {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted);
    font-family: var(--font-mono);
}

.ar-show-sum-val {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -1px;
    line-height: 1;
    margin-bottom: 3px;
}

.ar-show-sum-label {
    font-size: 11.5px;
    color: var(--text-secondary);
    font-weight: 500;
}

/* Distribution bar */
.ar-dist-bar {
    height: 10px;
    border-radius: 99px;
    overflow: hidden;
    display: flex;
    background: var(--bg);
    margin-bottom: 10px;
}

.ar-dist-segment {
    height: 100%;
    transition: width .8s cubic-bezier(.4,0,.2,1);
}

.ar-dist-segment.green  { background: #10B981; }
.ar-dist-segment.sky    { background: #0EA5E9; }
.ar-dist-segment.yellow { background: #F59E0B; }
.ar-dist-segment.red    { background: #EF4444; }
.ar-dist-segment.slate  { background: #94A3B8; }

.ar-dist-legend {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.ar-dist-leg-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-secondary);
}

.ar-dist-leg-item::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: block;
    flex-shrink: 0;
}

.ar-dist-leg-item.green::before  { background: #10B981; }
.ar-dist-leg-item.sky::before    { background: #0EA5E9; }
.ar-dist-leg-item.yellow::before { background: #F59E0B; }
.ar-dist-leg-item.red::before    { background: #EF4444; }
.ar-dist-leg-item.slate::before  { background: #94A3B8; }

/* Tab count badge */
.ar-tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 700;
    background: var(--bg);
    color: var(--text-muted);
    border: 1px solid var(--border);
    margin-left: 2px;
    transition: all .18s;
}

.ar-tab.active .ar-tab-count {
    background: var(--primary-xlight);
    color: var(--primary-dark);
    border-color: var(--primary-light);
}

/* Responsive */
@media (max-width: 1024px) {
    .ar-show-summary    { grid-template-columns: repeat(3, 1fr); }
    .ar-show-info-grid  { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
    .ar-show-summary    { grid-template-columns: repeat(2, 1fr); }
    .ar-show-info-grid  { grid-template-columns: 1fr; }
}
</style>
@endpush