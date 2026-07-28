@extends('layouts.app')

@section('title', 'Histori Akademik Anak')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Histori Akademik</span></li>
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
    <h2 style="font-size: 20px; font-weight: 700; margin: 0;">Histori Perkembangan Akademik</h2>
    <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Riwayat kelas, semester, dan pencapaian akademik anak dari waktu ke waktu.</p>
</div>

<div class="card" style="padding: 24px;">
    <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0;"><i class="ri-history-line"></i> Riwayat Kenaikan & Capaian Semester</h3>

    <div class="table-responsive">
        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Tahun Ajaran</th>
                    <th>Semester</th>
                    <th>Kelas</th>
                    <th>Rata-rata Nilai</th>
                    <th>Persentase Kehadiran</th>
                    <th>Status Promosi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($histories as $h)
                <tr>
                    <td style="font-weight: 600;">{{ $h['tahun_ajaran'] }}</td>
                    <td>{{ $h['semester'] }}</td>
                    <td><span class="badge badge-primary">{{ $h['kelas'] }}</span></td>
                    <td style="font-weight: 700; color: #2563EB;">{{ $h['rata_rata'] }}</td>
                    <td>{{ $h['kehadiran'] }}</td>
                    <td>
                        @if($h['status_promosi'] === 'Aktif')
                            <span class="badge badge-info">Berjalan</span>
                        @else
                            <span class="badge badge-success">Naik Kelas</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

@endsection
