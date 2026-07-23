@extends('layouts.app')

@section('title', 'Nilai Saya')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Nilai Saya</span></li>
    </ol>
</nav>
@endsection

@section('content')

<div style="
    background: linear-gradient(135deg, var(--sidebar-bg) 0%, #D97706 100%);
    border-radius: var(--radius);
    padding: 24px 28px;
    margin-bottom: 24px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 16px;
">
    <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:22px;">
        <i class="ri-bar-chart-2-line"></i>
    </div>
    <div>
        <div style="font-size:1.1rem;font-weight:700;">Nilai Saya</div>
        <div style="font-size:0.82rem;opacity:.75;">
            {{ $activeSemester?->semester_name ?? '-' }}
            @if($gradeName) · {{ $gradeName }} @endif
            · Rata-rata: <strong>{{ $overallAvg }}</strong>
        </div>
    </div>
</div>

{{-- Per-Subject Summary --}}
@if(count($scoresPerSubject) > 0)
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));gap:14px;margin-bottom:24px;">
        @foreach($scoresPerSubject as $s)
            <div class="card" style="padding:18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div style="font-size:0.82rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;">
                        {{ $s['subject'] }}
                    </div>
                </div>
                <div style="font-size:1.8rem;font-weight:800;color:{{ $s['color'] }};">
                    {{ $s['avg'] }}
                </div>
                <div style="display:flex;gap:12px;margin-top:8px;">
                    <div style="font-size:0.75rem;color:var(--text-muted);">
                        Tertinggi: <span style="font-weight:600;color:#10B981;">{{ $s['max'] }}</span>
                    </div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">
                        Terendah: <span style="font-weight:600;color:#EF4444;">{{ $s['min'] }}</span>
                    </div>
                </div>
                <div style="margin-top:10px;width:100%;height:6px;background:#E5E7EB;border-radius:99px;overflow:hidden;">
                    <div style="
                        width:{{ min(100, round($s['avg'])) }}%;
                        height:100%;
                        background:{{ $s['color'] }};
                        border-radius:99px;
                    "></div>
                </div>
                <div style="font-size:0.72rem;color:var(--text-muted);margin-top:4px;">
                    {{ $s['total'] }} penilaian
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Detail Scores Table --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Detail Nilai</div>
            <div class="card-subtitle">Semua penilaian yang sudah dipublikasikan</div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Jenis</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Nilai</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detailScores as $d)
                    @php
                        $pct = $d['percentage'];
                        $pctColor = $pct >= 85 ? '#10B981' : ($pct >= 75 ? '#3B82F6' : ($pct >= 65 ? '#F59E0B' : '#EF4444'));
                    @endphp
                    <tr>
                        <td style="font-weight:600;">{{ $d['mapel'] }}</td>
                        <td>
                            <span class="badge badge-info" style="font-size:0.72rem;">{{ $d['type'] }}</span>
                        </td>
                        <td style="font-size:0.85rem;">{{ $d['title'] }}</td>
                        <td style="color:var(--text-muted);font-size:0.82rem;">{{ $d['date'] }}</td>
                        <td>
                            @if($d['score'] !== null)
                                <span style="font-weight:700;font-size:0.95rem;">
                                    {{ $d['score'] }}
                                </span>
                                <span style="font-size:0.75rem;color:var(--text-muted);">/ {{ $d['max_score'] }}</span>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($pct !== null)
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:60px;height:6px;background:#E5E7EB;border-radius:99px;overflow:hidden;">
                                        <div style="width:{{ $pct }}%;height:100%;background:{{ $pctColor }};border-radius:99px;"></div>
                                    </div>
                                    <span style="font-size:0.78rem;font-weight:600;color:{{ $pctColor }};">{{ $pct }}%</span>
                                </div>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                            <i class="ri-bar-chart-line" style="font-size:40px;display:block;margin-bottom:8px;opacity:.3;"></i>
                            Belum ada nilai dipublikasikan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
