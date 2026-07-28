@extends('layouts.app')

@section('title', 'Rapor Digital Anak')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Rapor Digital Anak</span></li>
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
            <h2 style="font-size: 20px; font-weight: 700; margin: 0;">Rapor Hasil Belajar Siswa</h2>
            <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">
                Nama: <strong>{{ $childData->student->full_name ?? '-' }}</strong> | 
                Kelas: <strong>{{ $childData->grade_name }}</strong> | 
                Semester: <strong>{{ $semesterName }} ({{ $academicYear }})</strong>
            </p>
        </div>
        <button onclick="window.print()" class="btn btn-secondary" style="font-size: 13px;">
            <i class="ri-printer-line"></i> Cetak / Download Rapor
        </button>
    </div>
</div>

<div class="card" style="padding: 24px;">
    <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0;"><i class="ri-file-text-line"></i> Rekapitulasi Nilai Akhir</h3>

    @if(empty($reportScores) || count($reportScores) === 0)
        <p style="color: var(--text-secondary); text-align: center; padding: 24px 0;">Nilai rapor semester ini belum diterbitkan.</p>
    @else
        <div class="table-responsive">
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Mata Pelajaran</th>
                        <th>Kode</th>
                        <th>Nilai Akhir</th>
                        <th>Grade</th>
                        <th>Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportScores as $idx => $r)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td style="font-weight: 600;">{{ $r['subject'] }}</td>
                        <td><code>{{ $r['code'] }}</code></td>
                        <td style="font-weight: 700; font-size: 16px; color: #2563EB;">{{ $r['score'] }}</td>
                        <td><span class="badge badge-primary">{{ $r['grade'] }}</span></td>
                        <td>{{ $r['predikat'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px; padding: 16px; background: var(--bg-hover); border-radius: 12px; border-left: 4px solid #3B82F6;">
            <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 4px 0;">Catatan Wali Kelas:</h4>
            <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">{{ $notes }}</p>
        </div>
    @endif
</div>

@endif

@endsection
