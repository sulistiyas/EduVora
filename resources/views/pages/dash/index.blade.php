@extends('layouts.app')

@section('title', 'Dashboard')

{{-- @section('breadcrumb')
    <li class="flex items-center">
        <i class="ri-arrow-right-s-line"></i>
        <span>Overview</span>
    </li>
@endsection --}}

@section('content')

    {{-- ===== STAT CARDS ===== --}}
    <div class="stats-grid">

        {{-- Total Siswa --}}
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm-7 8a7 7 0 0 1 14 0H5z"/>
                    </svg>
                </div>
                <div class="stat-trend up">
                    <svg viewBox="0 0 10 10" fill="currentColor"><path d="M5 1l4 4H6v4H4V5H1L5 1z"/></svg>
                    +4.2%
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalSiswa ?? 1284) }}</div>
            <div class="stat-label">Total Siswa Aktif</div>
            <div class="sparkline">
                @foreach([40,55,45,60,50,70,65,80,75,90,85,95] as $h)
                    <div class="sparkline-bar" style="height:{{ $h }}%;background:var(--primary-light);"></div>
                @endforeach
            </div>
            <div class="stat-sub">Tahun ajaran {{ $tahunAjaran ?? '2024/2025' }}</div>
        </div>

        {{-- Total Guru --}}
        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 11a1 1 0 0 1 0 2h-1v1a1 1 0 0 1-2 0v-1h-1a1 1 0 0 1 0-2h1V9a1 1 0 0 1 2 0v2h1zM5 14a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-8a5 5 0 1 0 0 10A5 5 0 0 0 8 6zm8 13H2a6 6 0 0 1 12 0z"/>
                    </svg>
                </div>
                <div class="stat-trend up">
                    <svg viewBox="0 0 10 10" fill="currentColor"><path d="M5 1l4 4H6v4H4V5H1L5 1z"/></svg>
                    +1.8%
                </div>
            </div>
            <div class="stat-value">{{ number_format($totalGuru ?? 87) }}</div>
            <div class="stat-label">Guru & Tenaga Didik</div>
            <div class="sparkline">
                @foreach([60,65,55,70,68,72,75,70,80,78,82,87] as $h)
                    <div class="sparkline-bar" style="height:{{ $h }}%;background:#6EE7B7;"></div>
                @endforeach
            </div>
            <div class="stat-sub">{{ $guruAktif ?? 82 }} aktif mengajar saat ini</div>
        </div>

        {{-- Kehadiran Hari Ini --}}
        <div class="stat-card yellow">
            <div class="stat-header">
                <div class="stat-icon yellow">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </div>
                <div class="stat-trend {{ ($persenHadir ?? 92) >= 90 ? 'up' : 'down' }}">
                    <svg viewBox="0 0 10 10" fill="currentColor"><path d="M5 1l4 4H6v4H4V5H1L5 1z"/></svg>
                    {{ $persenHadir ?? 92 }}%
                </div>
            </div>
            <div class="stat-value">{{ number_format($jumlahHadir ?? 1182) }}</div>
            <div class="stat-label">Siswa Hadir Hari Ini</div>
            <div class="sparkline">
                @foreach([88,90,85,92,89,94,91,93,90,92,94,92] as $h)
                    <div class="sparkline-bar" style="height:{{ $h }}%;background:#FCD34D;"></div>
                @endforeach
            </div>
            <div class="stat-sub">{{ $jumlahAlpha ?? 102 }} tidak hadir (alpha/izin/sakit)</div>
        </div>

        {{-- Tagihan SPP --}}
        <div class="stat-card red">
            <div class="stat-header">
                <div class="stat-icon red">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                </div>
                <div class="stat-trend down">
                    <svg viewBox="0 0 10 10" fill="currentColor"><path d="M5 9L1 5h3V1h4v4h3L5 9z"/></svg>
                    -12%
                </div>
            </div>
            <div class="stat-value">Rp {{ number_format(($totalTagihanBelumLunas ?? 24500000)/1000000, 1) }}jt</div>
            <div class="stat-label">Tagihan Belum Lunas</div>
            <div class="sparkline">
                @foreach([70,60,80,50,65,45,55,40,48,35,40,32] as $h)
                    <div class="sparkline-bar" style="height:{{ $h }}%;background:#FCA5A5;"></div>
                @endforeach
            </div>
            <div class="stat-sub">{{ $jumlahTunggakan ?? 86 }} siswa masih menunggak</div>
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
                    <div class="card-subtitle">Log aktivitas pengguna sistem hari ini</div>
                </div>
                {{-- <a href="{{ route('activity.index') }}" class="card-action">Lihat Semua</a> --}}
                <a href="#" class="card-action">Lihat Semua</a>
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
                        @forelse($activities ?? [] as $activity)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar ua-{{ ['blue','green','violet','orange','pink'][($loop->index % 5)] }}">
                                            {{ strtoupper(substr($activity->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $activity->user->name }}</div>
                                            <div class="user-email">{{ $activity->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-text">{{ $activity->description }}</div>
                                    <div class="action-sub">{{ $activity->subject_type ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $activity->module ?? 'Sistem' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $activity->status_badge ?? 'success' }}">
                                        {{ $activity->status_label ?? 'Berhasil' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="date-text">{{ $activity->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @empty
                            {{-- Demo rows ketika belum ada data --}}
                            @php
                                $demoRows = [
                                    ['name'=>'Budi Santoso','email'=>'budi@smansatu.sch.id','ua'=>'ua-blue','aksi'=>'Menambahkan data siswa baru','sub'=>'Ahmad Fauzi - XII IPA 1','modul'=>'Siswa','badge'=>'badge-success','label'=>'Berhasil','time'=>'2 menit lalu'],
                                    ['name'=>'Sari Dewi','email'=>'sari@smansatu.sch.id','ua'=>'ua-green','aksi'=>'Memperbarui jadwal pelajaran','sub'=>'Kelas XI IPS 2 — Senin','modul'=>'Jadwal','badge'=>'badge-success','label'=>'Berhasil','time'=>'14 menit lalu'],
                                    ['name'=>'Rudi Hermawan','email'=>'rudi@smansatu.sch.id','ua'=>'ua-violet','aksi'=>'Input nilai ujian tengah semester','sub'=>'Matematika — X MIPA','modul'=>'Nilai','badge'=>'badge-success','label'=>'Berhasil','time'=>'1 jam lalu'],
                                    ['name'=>'Ani Rahayu','email'=>'ani@smansatu.sch.id','ua'=>'ua-orange','aksi'=>'Konfirmasi pembayaran SPP','sub'=>'Rp 350.000 — Bulan Juni','modul'=>'Keuangan','badge'=>'badge-warning','label'=>'Pending','time'=>'2 jam lalu'],
                                    ['name'=>'Doni Kurniawan','email'=>'doni@smansatu.sch.id','ua'=>'ua-pink','aksi'=>'Ekspor laporan kehadiran','sub'=>'Periode Mei 2025','modul'=>'Laporan','badge'=>'badge-info','label'=>'Proses','time'=>'3 jam lalu'],
                                ];
                            @endphp
                            @foreach($demoRows as $row)
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar {{ $row['ua'] }}">
                                                {{ strtoupper(substr($row['name'], 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="user-name">{{ $row['name'] }}</div>
                                                <div class="user-email">{{ $row['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-text">{{ $row['aksi'] }}</div>
                                        <div class="action-sub">{{ $row['sub'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $row['modul'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $row['badge'] }}">{{ $row['label'] }}</span>
                                    </td>
                                    <td>
                                        <span class="date-text">{{ $row['time'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
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
                        <div class="donut-total">{{ number_format($totalUsers ?? 240) }}</div>
                        <div class="donut-label">Total User</div>
                    </div>
                </div>
            </div>

            {{-- Role bars --}}
            <div class="role-dist">
                @php
                    $roles = [
                        ['label'=>'Super Admin','count'=>$totalSuperAdmin ?? 2,'pct'=>1,'color'=>'#EF4444'],
                        ['label'=>'Admin Sekolah','count'=>$totalAdminSekolah ?? 18,'pct'=>8,'color'=>'#F59E0B'],
                        ['label'=>'Guru','count'=>$totalGuru ?? 87,'pct'=>36,'color'=>'#3B82F6'],
                        ['label'=>'Siswa','count'=>$totalSiswa ?? 1284,'pct'=>55,'color'=>'#10B981'],
                    ];
                @endphp
                @foreach($roles as $role)
                    <div class="role-item">
                        <div class="role-dot" style="background:{{ $role['color'] }};"></div>
                        <div class="role-info">
                            <div class="role-name">
                                {{ $role['label'] }}
                                <span class="role-count">{{ number_format($role['count']) }}</span>
                            </div>
                            <div class="role-bar">
                                <div class="role-bar-fill"
                                     style="width:{{ $role['pct'] }}%;background:{{ $role['color'] }};"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Quick Stats --}}
            <div class="card-header" style="border-top:1px solid var(--border);border-bottom:none;padding-top:14px;">
                <div>
                    <div class="card-title">Sekolah Terdaftar</div>
                    <div class="card-subtitle">Dalam platform EduSaaS</div>
                </div>
                {{-- <a href="{{ route('schools.index') }}" class="card-action">Kelola</a> --}}
                <a href="#" class="card-action">Kelola</a>
            </div>

            <div class="role-dist">
                @php
                    $schoolTypes = [
                        ['label'=>'SMA/SMK','count'=>$totalSma ?? 12,'pct'=>50,'color'=>'#3B82F6'],
                        ['label'=>'SMP','count'=>$totalSmp ?? 8,'pct'=>33,'color'=>'#8B5CF6'],
                        ['label'=>'SD','count'=>$totalSd ?? 4,'pct'=>17,'color'=>'#10B981'],
                    ];
                @endphp
                @foreach($schoolTypes as $type)
                    <div class="role-item">
                        <div class="role-dot" style="background:{{ $type['color'] }};"></div>
                        <div class="role-info">
                            <div class="role-name">
                                {{ $type['label'] }}
                                <span class="role-count">{{ $type['count'] }} sekolah</span>
                            </div>
                            <div class="role-bar">
                                <div class="role-bar-fill"
                                     style="width:{{ $type['pct'] }}%;background:{{ $type['color'] }};"></div>
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
    // Animasi role bars saat halaman load
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