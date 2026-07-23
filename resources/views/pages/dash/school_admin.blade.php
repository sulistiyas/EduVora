@extends('layouts.app')

@section('title', 'Dashboard Admin Sekolah')

@section('content')

    <div class="stats-grid">

        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-7 8a7 7 0 0 1 14 0H5z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalSiswa) }}</div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-sub">{{ $school->school_name ?? '-' }}</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5 14a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-8a5 5 0 1 0 0 10A5 5 0 0 0 8 6zm8 13H2a6 6 0 0 1 12 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalGuru) }}</div>
            <div class="stat-label">Guru & Staff</div>
            <div class="stat-sub">Aktif mengajar</div>
        </div>

        <div class="stat-card yellow">
            <div class="stat-header">
                <div class="stat-icon yellow">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalKelas) }}</div>
            <div class="stat-label">Kelas Aktif</div>
            <div class="stat-sub">{{ $totalMapel }} mata pelajaran</div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <div class="stat-icon red">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </div>
                <div class="stat-trend {{ $persenHadir >= 90 ? 'up' : 'down' }}">
                    {{ $persenHadir }}%
                </div>
            </div>
            <div class="stat-value">{{ number_format($todayHadir) }}</div>
            <div class="stat-label">Siswa Hadir Hari Ini</div>
            <div class="stat-sub">{{ $todayAlpha }} tidak hadir</div>
        </div>

    </div>

    <div class="content-grid">

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Aktivitas Terbaru</div>
                    <div class="card-subtitle">Log aktivitas sekolah</div>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Modul</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar ua-{{ ['blue','green','violet','orange','pink'][($loop->index % 5)] }}">
                                            {{ strtoupper(substr($log->user_name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $log->user_name ?? '-' }}</div>
                                            <div class="user-email">{{ $log->user_email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-text">{{ $log->description }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $log->module }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $log->status_badge }}">
                                        {{ $log->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="date-text">{{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->diffForHumans() : '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">
                                    Belum ada aktivitas tercatat
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Ringkasan Sekolah</div>
                    <div class="card-subtitle">{{ $school->school_name ?? '-' }}</div>
                </div>
            </div>

            <div class="role-dist">
                @php
                    $summaryData = [
                        ['label' => 'Siswa Aktif', 'count' => $totalSiswa, 'color' => '#3B82F6'],
                        ['label' => 'Guru & Staff', 'count' => $totalGuru, 'color' => '#10B981'],
                        ['label' => 'Kelas', 'count' => $totalKelas, 'color' => '#F59E0B'],
                        ['label' => 'Ruangan', 'count' => $totalRuangan, 'color' => '#8B5CF6'],
                    ];
                    $total = max($totalSiswa + $totalGuru + $totalKelas + $totalRuangan, 1);
                @endphp
                @foreach($summaryData as $item)
                    <div class="role-item">
                        <div class="role-dot" style="background:{{ $item['color'] }};"></div>
                        <div class="role-info">
                            <div class="role-name">
                                {{ $item['label'] }}
                                <span class="role-count">{{ number_format($item['count']) }}</span>
                            </div>
                            <div class="role-bar">
                                <div class="role-bar-fill"
                                     style="width:{{ round(($item['count'] / $total) * 100) }}%;background:{{ $item['color'] }};"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($activeSemester)
                <div class="card-header" style="border-top:1px solid var(--border);border-bottom:none;padding-top:14px;">
                    <div>
                        <div class="card-title">Semester Aktif</div>
                        <div class="card-subtitle">{{ $activeSemester->semester_name }}</div>
                    </div>
                </div>
                <div style="padding:0 20px 16px;">
                    <div style="font-size:0.85rem;color:var(--text-muted);">
                        {{ \Carbon\Carbon::parse($activeSemester->start_date)->translatedFormat('d M Y') }}
                        —
                        {{ \Carbon\Carbon::parse($activeSemester->end_date)->translatedFormat('d M Y') }}
                    </div>
                </div>
            @endif

            @if($totalTugas > 0)
                <div class="card-header" style="border-top:1px solid var(--border);border-bottom:none;padding-top:14px;">
                    <div>
                        <div class="card-title">Tugas Aktif</div>
                        <div class="card-subtitle">Belum jatuh tempo</div>
                    </div>
                    <span class="badge badge-warning">{{ $totalTugas }}</span>
                </div>
            @endif

        </div>

    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bars = document.querySelectorAll('.role-bar-fill');
        bars.forEach((bar, i) => {
            const target = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width .8s cubic-bezier(.4,0,.2,1)';
                bar.style.width = target;
            }, 300 + i * 100);
        });
    });
</script>
@endpush
