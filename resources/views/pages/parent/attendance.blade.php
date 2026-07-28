@extends('layouts.app')

@section('title', 'Presensi Anak')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Presensi Anak</span></li>
    </ol>
</nav>
@endsection

@section('content')

@if(!$has_student || !$childData)
<div class="card" style="padding: 40px; text-align: center;">
    <i class="ri-user-unfollow-line" style="font-size: 48px; color: var(--text-tertiary);"></i>
    <h3 style="margin-top: 16px;">Belum Ada Data Anak Terhubung</h3>
    <p style="color: var(--text-secondary); margin-top: 8px;">Akun Anda belum terhubung dengan data siswa di sekolah ini.</p>
</div>
@else

<div class="card" style="padding: 24px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; margin: 0;">Rekap Kehadiran: {{ $childData->student->full_name ?? '-' }}</h2>
            <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Kelas: {{ $childData->grade_name ?? '-' }} | NIS: {{ $childData->student->nis ?? '-' }}</p>
        </div>
        
        <div style="display: flex; gap: 12px;">
            <div style="padding: 10px 16px; background: #ECFDF5; border-radius: 10px; color: #065F46; text-align: center;">
                <div style="font-size: 18px; font-weight: 700;">{{ $summary['H'] ?? 0 }}</div>
                <div style="font-size: 11px;">Hadir</div>
            </div>
            <div style="padding: 10px 16px; background: #EFF6FF; border-radius: 10px; color: #1E40AF; text-align: center;">
                <div style="font-size: 18px; font-weight: 700;">{{ $summary['I'] ?? 0 }}</div>
                <div style="font-size: 11px;">Izin</div>
            </div>
            <div style="padding: 10px 16px; background: #FEF3C7; border-radius: 10px; color: #92400E; text-align: center;">
                <div style="font-size: 18px; font-weight: 700;">{{ $summary['S'] ?? 0 }}</div>
                <div style="font-size: 11px;">Sakit</div>
            </div>
            <div style="padding: 10px 16px; background: #FEE2E2; border-radius: 10px; color: #991B1B; text-align: center;">
                <div style="font-size: 18px; font-weight: 700;">{{ $summary['A'] ?? 0 }}</div>
                <div style="font-size: 11px;">Alpa</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="padding: 24px;">
    <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0;">Histori Kehadiran Sesi Pelajaran</h3>

    @if(empty($logs))
        <p style="color: var(--text-secondary); text-align: center; padding: 24px 0;">Belum ada catatan presensi anak.</p>
    @else
        <div class="table-responsive">
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td style="font-weight: 500;">{{ \Carbon\Carbon::parse($log->attendance_date)->translatedFormat('d M Y') }}</td>
                        <td>{{ substr($log->start_time, 0, 5) }} - {{ substr($log->end_time, 0, 5) }}</td>
                        <td style="font-weight: 600;">{{ $log->mapel }}</td>
                        <td>{{ $log->guru }}</td>
                        <td>
                            @if(($log->status ?? '') === 'H')
                                <span class="badge badge-success">Hadir</span>
                            @elseif(($log->status ?? '') === 'I')
                                <span class="badge badge-info">Izin</span>
                            @elseif(($log->status ?? '') === 'S')
                                <span class="badge badge-warning">Sakit</span>
                            @elseif(($log->status ?? '') === 'A')
                                <span class="badge badge-danger">Alpa</span>
                            @else
                                <span class="badge badge-secondary">Terlambat</span>
                            @endif
                        </td>
                        <td style="color: var(--text-secondary); font-size: 13px;">{{ $log->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endif

@endsection
