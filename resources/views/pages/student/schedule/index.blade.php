@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Jadwal Pelajaran</span></li>
    </ol>
</nav>
@endsection

@section('content')

<div style="
    background: linear-gradient(135deg, var(--sidebar-bg) 0%, #7C3AED 100%);
    border-radius: var(--radius);
    padding: 24px 28px;
    margin-bottom: 24px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 16px;
">
    <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:22px;">
        <i class="ri-calendar-schedule-line"></i>
    </div>
    <div>
        <div style="font-size:1.1rem;font-weight:700;">Jadwal Pelajaran</div>
        <div style="font-size:0.82rem;opacity:.75;">
            {{ $activeSemester?->semester_name ?? '-' }}
            @if($gradeName) · {{ $gradeName }} @endif
        </div>
    </div>
</div>

@if(empty($schedules))
    <div class="card" style="text-align:center;padding:60px 20px;color:var(--text-muted);">
        <i class="ri-calendar-line" style="font-size:48px;display:block;margin-bottom:12px;opacity:.3;"></i>
        <div style="font-size:1rem;font-weight:600;margin-bottom:4px;">Belum ada jadwal</div>
        <div style="font-size:0.85rem;">Jadwal pelajaran belum tersedia untuk kelas Anda.</div>
    </div>
@else
    @foreach($dayNames as $dayNum => $dayName)
        @if(isset($schedules[$dayNum]))
            <div class="card" style="margin-bottom:16px;">
                <div class="card-header" style="padding:14px 20px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="
                            width:36px;height:36px;border-radius:10px;
                            background:{{ in_array($dayNum, [6,7]) ? '#F59E0B' : '#3B82F6' }};
                            color:#fff;display:grid;place-items:center;font-size:14px;font-weight:700;
                        ">
                            {{ substr($dayName, 0, 2) }}
                        </div>
                        <div>
                            <div class="card-title" style="margin:0;">{{ $dayName }}</div>
                            <div class="card-subtitle" style="margin:0;">{{ count($schedules[$dayNum]) }} mata pelajaran</div>
                        </div>
                    </div>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Jam</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Ruangan</th>
                                <th>Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules[$dayNum] as $s)
                                <tr>
                                    <td>
                                        <span style="font-family:var(--font-mono);font-size:0.82rem;font-weight:500;">
                                            {{ $s['jam'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;">{{ $s['mapel'] }}</div>
                                    </td>
                                    <td style="color:var(--text-muted);font-size:0.85rem;">
                                        {{ $s['guru'] }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info" style="font-size:0.75rem;">
                                            {{ $s['ruangan'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $tipeLabel = ['regular' => 'Reguler', 'lab' => 'Lab', 'exam' => 'Ujian', 'extracurricular' => 'Ekstrakurikuler', 'remedial' => 'Remedial'];
                                            $tipeBadge = ['regular' => 'info', 'lab' => 'success', 'exam' => 'warning', 'extracurricular' => 'violet', 'remedial' => 'danger'];
                                        @endphp
                                        <span class="badge badge-{{ $tipeBadge[$s['tipe']] ?? 'secondary' }}" style="font-size:0.75rem;">
                                            {{ $tipeLabel[$s['tipe']] ?? $s['tipe'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
@endif

@endsection
