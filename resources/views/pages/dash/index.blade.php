@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    {{-- ===== STAT CARDS ===== --}}
    <div class="stats-grid">

        {{-- Total Sekolah --}}
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalSekolah) }}</div>
            <div class="stat-label">Total Sekolah</div>
            <div class="stat-sub">
                SMA/SMK {{ $schoolTypes['SMA/SMK'] }} &middot;
                SMP {{ $schoolTypes['SMP'] }} &middot;
                SD {{ $schoolTypes['SD'] }}
            </div>
        </div>

        {{-- Total Siswa --}}
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-7 8a7 7 0 0 1 14 0H5z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalSiswa) }}</div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-sub">Tercatat di seluruh sekolah</div>
        </div>

        {{-- Total Guru --}}
        <div class="stat-card yellow">
            <div class="stat-header">
                <div class="stat-icon yellow">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5 14a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-8a5 5 0 1 0 0 10A5 5 0 0 0 8 6zm8 13H2a6 6 0 0 1 12 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalGuru) }}</div>
            <div class="stat-label">Guru & Tenaga Didik</div>
            <div class="stat-sub">{{ $totalUsers }} user terdaftar</div>
        </div>

        {{-- Kehadiran Hari Ini --}}
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
            <div class="stat-sub">{{ $todayAlpha }} tidak hadir (alpha/izin/sakit)</div>
        </div>

    </div>
    {{-- /STAT CARDS --}}


    {{-- ===== CONTENT GRID ===== --}}
    <div class="content-grid">

        {{-- ===== TABLE: Aktivitas Terbaru ===== --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Aktivitas Terbaru</div>
                    <div class="card-subtitle">Log aktivitas pengguna sistem</div>
                </div>
                <a href="{{ route('audit-logs.index') }}" class="card-action">Lihat Semua</a>
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
                                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $log->user->name ?? '-' }}</div>
                                            <div class="user-email">{{ $log->user->email ?? '' }}</div>
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
                                    <span class="date-text">{{ $log->created_at->diffForHumans() }}</span>
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
        {{-- /TABLE --}}

        {{-- ===== SIDE CARD: Distribusi Pengguna ===== --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Distribusi Role</div>
                    <div class="card-subtitle">Pengguna sistem aktif</div>
                </div>
            </div>

            {{-- Donut Chart --}}
            <div class="donut-wrap">
                <div class="donut">
                    <div class="donut-center">
                        <div class="donut-total">{{ number_format($totalUsers) }}</div>
                        <div class="donut-label">Total User</div>
                    </div>
                </div>
            </div>

            {{-- Role bars --}}
            <div class="role-dist">
                @php
                    $rolesData = [
                        ['label'=>'Super Admin','count'=>$totalSuperAdmin,'color'=>'#EF4444'],
                        ['label'=>'Admin Sekolah','count'=>$totalAdminSekolah,'color'=>'#F59E0B'],
                        ['label'=>'Guru','count'=>$totalGuru,'color'=>'#3B82F6'],
                        ['label'=>'Siswa','count'=>$totalSiswa,'color'=>'#10B981'],
                    ];
                    $rolesTotal = max($totalUsers, 1);
                @endphp
                @foreach($rolesData as $item)
                    <div class="role-item">
                        <div class="role-dot" style="background:{{ $item['color'] }};"></div>
                        <div class="role-info">
                            <div class="role-name">
                                {{ $item['label'] }}
                                <span class="role-count">{{ number_format($item['count']) }}</span>
                            </div>
                            <div class="role-bar">
                                <div class="role-bar-fill"
                                     style="width:{{ round(($item['count'] / $rolesTotal) * 100) }}%;background:{{ $item['color'] }};"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Stats --}}
            <div class="card-header" style="border-top:1px solid var(--border);border-bottom:none;padding-top:14px;">
                <div>
                    <div class="card-title">Sekolah Terdaftar</div>
                    <div class="card-subtitle">Dalam platform EduVora</div>
                </div>
                <a href="{{ route('school-management.index') }}" class="card-action">Kelola</a>
            </div>

            <div class="role-dist">
                @php
                    $schoolData = [
                        ['label'=>'SMA/SMK','count'=>$schoolTypes['SMA/SMK'],'color'=>'#3B82F6'],
                        ['label'=>'SMP','count'=>$schoolTypes['SMP'],'color'=>'#8B5CF6'],
                        ['label'=>'SD','count'=>$schoolTypes['SD'],'color'=>'#10B981'],
                    ];
                    $schoolTotal = max($totalSekolah, 1);
                @endphp
                @foreach($schoolData as $item)
                    <div class="role-item">
                        <div class="role-dot" style="background:{{ $item['color'] }};"></div>
                        <div class="role-info">
                            <div class="role-name">
                                {{ $item['label'] }}
                                <span class="role-count">{{ $item['count'] }} sekolah</span>
                            </div>
                            <div class="role-bar">
                                <div class="role-bar-fill"
                                     style="width:{{ round(($item['count'] / $schoolTotal) * 100) }}%;background:{{ $item['color'] }};"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        {{-- /SIDE CARD --}}

    </div>
    {{-- /CONTENT GRID --}}

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
