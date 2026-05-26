{{-- ─────────────────────────────────────────────────────────────────
     resources/views/teacher/reports/score/partials/_summary.blade.php
     Score Report — Summary Cards + KKM Distribution Bar
────────────────────────────────────────────────────────────────── --}}

@php
    $summary = array_merge([
        'class_avg'       => 0,
        'highest'         => 0,
        'lowest'          => 0,
        'below_kkm_count' => 0,
        'total_students'  => 0,
        'total_sessions'  => 0,
        'kkm'             => 75,
        'pass_count'      => 0,
    ], $summary ?? []);

    $kkm       = $summary['kkm'];
    $total     = $summary['total_students'];
    $belowKkm  = $summary['below_kkm_count'];
    $passCount = $summary['pass_count'] ?: ($total - $belowKkm);

    $passWidth  = $total > 0 ? round($passCount / $total * 100, 1) : 0;
    $belowWidth = $total > 0 ? round($belowKkm / $total * 100, 1) : 0;
@endphp

<div class="ar-summary sr-summary" x-data="scoreSummary()">

    {{-- Rata-rata Kelas --}}
    <div class="ar-sum-card blue">
        <div class="ar-sum-icon blue"><i class="ri-bar-chart-2-line"></i></div>
        <div class="ar-sum-val">{{ number_format($summary['class_avg'], 1) }}</div>
        <div class="ar-sum-label">Rata-rata Kelas</div>
        <div class="ar-sum-rate">
            <i class="ri-information-line"></i>
            <span>dari {{ $summary['total_sessions'] }} sesi</span>
        </div>
    </div>

    {{-- Nilai Tertinggi --}}
    <div class="ar-sum-card green">
        <div class="ar-sum-icon green"><i class="ri-trophy-line"></i></div>
        <div class="ar-sum-val">{{ $summary['highest'] }}</div>
        <div class="ar-sum-label">Nilai Tertinggi</div>
        <div class="ar-sum-rate">
            <strong class="ar-rate-good">nilai terbaik</strong>
        </div>
    </div>

    {{-- Nilai Terendah --}}
    <div class="ar-sum-card red">
        <div class="ar-sum-icon red"><i class="ri-arrow-down-line"></i></div>
        <div class="ar-sum-val">{{ $summary['lowest'] }}</div>
        <div class="ar-sum-label">Nilai Terendah</div>
        <div class="ar-sum-rate">
            <strong class="ar-rate-bad">perlu perhatian</strong>
        </div>
    </div>

    {{-- Di Bawah KKM --}}
    <div class="ar-sum-card yellow">
        <div class="ar-sum-icon yellow"><i class="ri-alert-line"></i></div>
        <div class="ar-sum-val">{{ $summary['below_kkm_count'] }}</div>
        <div class="ar-sum-label">Di Bawah KKM</div>
        <div class="ar-sum-rate">
            <strong class="{{ $summary['below_kkm_count'] > 0 ? 'ar-rate-bad' : 'ar-rate-good' }}">
                KKM: {{ $kkm }}
            </strong>
        </div>
    </div>

    {{-- Total Siswa --}}
    <div class="ar-sum-card slate">
        <div class="ar-sum-icon slate"><i class="ri-group-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_students'] }}</div>
        <div class="ar-sum-label">Total Siswa</div>
        <div class="ar-sum-rate">
            <i class="ri-information-line"></i>
            <span>terdaftar di kelas</span>
        </div>
    </div>

    {{-- Distribusi KKM (full width) --}}
    <div class="ar-sum-card blue" style="grid-column: 1 / -1">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div class="ar-sum-label" style="font-size:12px;font-weight:600;color:var(--text-primary)">
                Distribusi Nilai KKM
            </div>
            <span style="font-size:12px;color:var(--text-muted)">
                Total {{ $total }} siswa · KKM: <strong style="color:var(--text-primary)">{{ $kkm }}</strong>
            </span>
        </div>
        <div class="ar-dist-bar">
            @if($passWidth > 0)
                <div class="ar-dist-segment green sr-dist-segment"
                     data-width="{{ $passWidth }}"
                     style="width:0%"
                     title="Lulus KKM: {{ $passCount }}"></div>
            @endif
            @if($belowWidth > 0)
                <div class="ar-dist-segment red sr-dist-segment"
                     data-width="{{ $belowWidth }}"
                     style="width:0%"
                     title="Di Bawah KKM: {{ $belowKkm }}"></div>
            @endif
        </div>
        <div class="ar-dist-legend">
            <span class="ar-dist-leg-item green">Lulus KKM {{ $passCount }}</span>
            <span class="ar-dist-leg-item red">Di Bawah KKM {{ $belowKkm }}</span>
        </div>
    </div>

</div>

{{-- KKM Warning Banner --}}
@if(($summary['below_kkm_count'] ?? 0) > 0)
    <div class="sr-kkm-banner">
        <span class="sr-kkm-banner-icon">⚠️</span>
        <span>
            <strong>{{ $summary['below_kkm_count'] }} siswa</strong>
            memiliki rata-rata nilai di bawah KKM ({{ $kkm }}).
            Siswa tersebut di-highlight pada tabel rekap di bawah.
        </span>
    </div>
@endif