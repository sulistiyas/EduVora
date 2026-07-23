@extends('layouts.app')

@section('title', 'Presensi Saya')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Presensi Saya</span></li>
    </ol>
</nav>
@endsection

@section('content')

<div style="
    background: linear-gradient(135deg, var(--sidebar-bg) 0%, #059669 100%);
    border-radius: var(--radius);
    padding: 24px 28px;
    margin-bottom: 24px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 16px;
">
    <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:22px;">
        <i class="ri-checkbox-circle-line"></i>
    </div>
    <div>
        <div style="font-size:1.1rem;font-weight:700;">Presensi Saya</div>
        <div style="font-size:0.82rem;opacity:.75;">
            {{ $activeSemester?->semester_name ?? '-' }}
            @if($gradeName) · {{ $gradeName }} @endif
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="stats-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom:24px;">
    @php
        $cards = [
            ['label' => 'Hadir', 'value' => $summary['H'], 'color' => 'green', 'icon' => 'ri-check-line'],
            ['label' => 'Izin', 'value' => $summary['I'], 'color' => 'blue', 'icon' => 'ri-information-line'],
            ['label' => 'Sakit', 'value' => $summary['S'], 'color' => 'yellow', 'icon' => 'ri-heart-pulse-line'],
            ['label' => 'Alpha', 'value' => $summary['A'], 'color' => 'red', 'icon' => 'ri-close-circle-line'],
            ['label' => 'Terlambat', 'value' => $summary['L'], 'color' => 'orange', 'icon' => 'ri-time-line'],
        ];
    @endphp
    @foreach($cards as $c)
        <div class="stat-card {{ $c['color'] }}">
            <div class="stat-header">
                <div class="stat-icon {{ $c['color'] }}">
                    <i class="{{ $c['icon'] }}"></i>
                </div>
            </div>
            <div class="stat-value">{{ $c['value'] }}</div>
            <div class="stat-label">{{ $c['label'] }}</div>
        </div>
    @endforeach
</div>

{{-- Persentase --}}
@if($summary['total'] > 0)
    <div class="card" style="margin-bottom:24px;padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <span style="font-size:0.85rem;font-weight:600;">Tingkat Kehadiran</span>
            <span style="font-size:0.85rem;font-weight:700;color:{{ $summary['persen_hadir'] >= 90 ? '#10B981' : '#EF4444' }};">
                {{ $summary['persen_hadir'] }}%
            </span>
        </div>
        <div style="width:100%;height:8px;background:#E5E7EB;border-radius:99px;overflow:hidden;">
            <div style="
                width:{{ $summary['persen_hadir'] }}%;
                height:100%;
                background:{{ $summary['persen_hadir'] >= 90 ? '#10B981' : ($summary['persen_hadir'] >= 75 ? '#3B82F6' : '#EF4444') }};
                border-radius:99px;
                transition: width 0.8s ease;
            "></div>
        </div>
        <div style="font-size:0.78rem;color:var(--text-muted);margin-top:6px;">
            {{ $summary['H'] }} hadir dari {{ $summary['total'] }} total pertemuan
        </div>
    </div>
@endif

{{-- History Table --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Riwayat Presensi</div>
            <div class="card-subtitle">Semua catatan kehadiran semester ini</div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Mata Pelajaran</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceHistory as $att)
                    <tr>
                        <td style="font-weight:500;">{{ $att['date'] }}</td>
                        <td style="color:var(--text-muted);font-size:0.85rem;">{{ $att['day'] }}</td>
                        <td>{{ $att['mapel'] }}</td>
                        <td>
                            <span style="font-family:var(--font-mono);font-size:0.82rem;">{{ $att['jam'] }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $att['badge'] }}">{{ $att['status'] }}</span>
                        </td>
                        <td style="color:var(--text-muted);font-size:0.85rem;">
                            {{ $att['note'] ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="ri-checkbox-circle-line" style="font-size:40px;display:block;margin-bottom:8px;opacity:.3;"></i>
                            Belum ada data presensi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
