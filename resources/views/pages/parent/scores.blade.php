@extends('layouts.app')

@section('title', 'Nilai Anak')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Nilai Anak</span></li>
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

<div class="card" style="padding: 24px; margin-bottom: 24px; background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); color: #fff;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin:0;">Capaian Akademik: {{ $childData->student->full_name ?? '-' }}</h2>
            <p style="color: rgba(255,255,255,.8); font-size: 13px; margin-top: 4px;">Kelas: {{ $childData->grade_name }} | Semester: {{ $childData->active_semester?->semester_name ?? 'Ganjil' }}</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 32px; font-weight: 800; color: #fff;">{{ $overallAverage ?? 0 }}</div>
            <div style="font-size: 12px; color: rgba(255,255,255,.8);">Rata-rata Keseluruhan</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    {{-- NILAI PER MAPEL --}}
    <div class="card" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0;"><i class="ri-book-open-line"></i> Rata-rata per Mata Pelajaran</h3>
        
        @if(empty($subjects))
            <p style="color: var(--text-secondary); text-align: center; padding: 24px 0;">Belum ada nilai yang dipublish oleh guru.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($subjects as $sub)
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 6px;">
                        <span>{{ $sub->subject_name ?? '' }}</span>
                        <span style="color: #2563EB;">{{ $sub->avg_score ?? 0 }}</span>
                    </div>
                    <div style="height: 8px; border-radius: 4px; background: var(--bg-hover); overflow: hidden;">
                        <div style="height: 100%; width: {{ min(100, $sub->avg_score ?? 0) }}%; background: linear-gradient(90deg, #3B82F6, #2563EB); border-radius: 4px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-tertiary); margin-top: 4px;">
                        <span>Max: {{ $sub->max_score ?? 0 }} | Min: {{ $sub->min_score ?? 0 }}</span>
                        <span>{{ $sub->total_assessments ?? 0 }} Penilaian</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- DETAIL SESI PENILAIAN --}}
    <div class="card" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0;"><i class="ri-file-list-3-line"></i> Detail Penilaian Terakhir</h3>

        @if(empty($sessions))
            <p style="color: var(--text-secondary); text-align: center; padding: 24px 0;">Belum ada sesi penilaian.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px; max-height: 480px; overflow-y: auto;">
                @foreach($sessions as $sess)
                <div style="padding: 12px 16px; background: var(--bg-hover); border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 14px;">{{ $sess->session_name ?? '' }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">{{ $sess->mapel ?? '' }} • {{ ucfirst($sess->session_type ?? 'Tugas') }}</div>
                    </div>
                    <div style="font-size: 18px; font-weight: 700; color: {{ ($sess->score ?? 0) >= 75 ? '#10B981' : '#EF4444' }};">
                        {{ $sess->score ?? 0 }}
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endif

@endsection
