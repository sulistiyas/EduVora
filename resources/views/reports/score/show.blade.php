{{-- reports/score/show.blade.php --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports/score.css') }}">
@endpush

@section('content')
<div class="sr-page">

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="sr-header">
        <div class="sr-header__inner">
            <div class="sr-header__title-row">

                {{-- Back + Title --}}
                <div class="sr-header__title-wrap">
                    <a href="{{ route('teacher.reports.score.index') }}" class="sr-back-btn" title="Kembali">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 5l-7 7 7 7"/>
                        </svg>
                    </a>
                    <div class="sr-header__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                    </div>
                    <div>
                        <div class="sr-header__title">{{ $session->title }}</div>
                        <div class="sr-header__subtitle">
                            {{ $session->gradeSubject?->grade?->grade_name }} ·
                            {{ $session->gradeSubject?->subject?->subject_name }} ·
                            {{ $session->semester?->semester_name }}
                        </div>
                    </div>
                </div>

                {{-- Meta + Export --}}
                <div class="sr-header__actions">
                    <div class="sr-session-meta">
                        @php
                            $typeLabel = match($session->score_type) {
                                'daily'      => 'Harian',
                                'assignment' => 'Tugas',
                                'mid_exam'   => 'UTS',
                                'final_exam' => 'UAS',
                                default      => ucfirst($session->score_type),
                            };
                        @endphp
                        <span class="sr-type-badge sr-type-badge--{{ $session->score_type }}">
                            {{ $typeLabel }}
                        </span>
                        <span class="sr-session-date">
                            {{ $session->score_date?->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ BODY ════════════════════════════════════════════════════════════ --}}
    <div class="sr-body">

        {{-- ── Summary mini cards ──────────────────────────────────────────── --}}
        <div class="sr-cards sr-cards--show">
            <div class="sr-card">
                <div class="sr-card__label">Jumlah Siswa</div>
                <div class="sr-card__value" style="color:#4F46E5">{{ $detail['counts']['total'] }}</div>
                <div class="sr-card__sub">siswa dinilai</div>
            </div>
            <div class="sr-card">
                <div class="sr-card__label">Rata-rata</div>
                <div class="sr-card__value" style="color:#4F46E5">{{ $detail['stats']['avg'] }}</div>
                <div class="sr-card__sub">nilai rata-rata sesi</div>
            </div>
            <div class="sr-card">
                <div class="sr-card__label">Tertinggi</div>
                <div class="sr-card__value" style="color:#16A34A">{{ $detail['stats']['highest'] }}</div>
                <div class="sr-card__sub">nilai terbaik</div>
            </div>
            <div class="sr-card">
                <div class="sr-card__label">Terendah</div>
                <div class="sr-card__value" style="color:#DC2626">{{ $detail['stats']['lowest'] }}</div>
                <div class="sr-card__sub">perlu perhatian</div>
            </div>
            <div class="sr-card">
                <div class="sr-card__label">Di Bawah KKM</div>
                <div class="sr-card__value" style="color:#EA580C">{{ $detail['counts']['below_kkm'] }}</div>
                <div class="sr-card__sub">dari {{ $detail['counts']['total'] }} siswa</div>
            </div>
        </div>

        {{-- ── KKM Alert ────────────────────────────────────────────────── --}}
        @if($detail['counts']['below_kkm'] > 0)
        <div class="sr-alert sr-alert--warning">
            <span class="sr-alert__icon">⚠️</span>
            <span>
                <strong>{{ $detail['counts']['below_kkm'] }} siswa</strong> mendapat nilai
                di bawah KKM pada sesi ini.
            </span>
        </div>
        @endif

        {{-- ── Detail Table ─────────────────────────────────────────────── --}}
        <div class="sr-panel">

            {{-- Panel header --}}
            <div class="sr-panel__header">
                <div class="sr-panel__title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    Nilai Per Siswa
                </div>
                <a href="{{ route('teacher.reports.score.export', ['type' => 'summary', 'grade_subject_id' => $session->grade_subject_id, 'semester_id' => $session->semester_id]) }}"
                   class="sr-btn sr-btn--outline sr-btn--sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    Export Excel
                </a>
            </div>

            <div class="sr-table-wrap">
                <table class="sr-table">
                    <thead>
                        <tr>
                            <th class="sr-th sr-th--no">#</th>
                            <th class="sr-th sr-th--left">Nama Siswa</th>
                            <th class="sr-th sr-th--left">NIS</th>
                            <th class="sr-th sr-th--center">Nilai</th>
                            <th class="sr-th sr-th--center">Maks</th>
                            <th class="sr-th sr-th--center">%</th>
                            <th class="sr-th sr-th--left">Status</th>
                            @if(collect($detail['details'])->whereNotNull('notes')->isNotEmpty())
                            <th class="sr-th sr-th--left">Catatan</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $kkm = $detail['details']->first()?->scoreSession?->gradeSubject?->kkm ?? 0; @endphp
                        @forelse($detail['details']->sortByDesc('score') as $idx => $item)
                        @php $belowKkm = $item->score < $kkm; @endphp
                        <tr class="sr-tr {{ $belowKkm ? 'sr-tr--below-kkm' : '' }}">
                            <td class="sr-td sr-td--center sr-td--muted">{{ $loop->iteration }}</td>
                            <td class="sr-td">
                                <div class="sr-student">
                                    <div class="sr-student__avatar {{ $belowKkm ? 'sr-student__avatar--red' : '' }}">
                                        {{ mb_substr($item->student?->full_name ?? '?', 0, 1) }}
                                    </div>
                                    <span>{{ $item->student?->full_name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="sr-td sr-td--mono">{{ $item->student?->nis ?? '-' }}</td>
                            <td class="sr-td sr-td--center">
                                <span class="sr-score-badge {{ $belowKkm ? 'sr-score-badge--fail' : 'sr-score-badge--pass' }}">
                                    {{ number_format($item->score, 1) }}
                                </span>
                            </td>
                            <td class="sr-td sr-td--center sr-td--muted">{{ number_format($item->max_score, 0) }}</td>
                            <td class="sr-td sr-td--center">
                                <span class="sr-pct {{ $belowKkm ? 'sr-pct--fail' : 'sr-pct--pass' }}">
                                    {{ $item->percentage }}%
                                </span>
                            </td>
                            <td class="sr-td">
                                <span class="sr-kkm-status {{ $belowKkm ? 'sr-kkm-status--fail' : 'sr-kkm-status--pass' }}">
                                    {{ $belowKkm ? '⚠ Di bawah KKM' : '✓ Lulus' }}
                                </span>
                            </td>
                            @if(collect($detail['details'])->whereNotNull('notes')->isNotEmpty())
                            <td class="sr-td sr-td--muted">{{ $item->notes ?? '' }}</td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="sr-empty">
                                <div class="sr-empty__icon">📋</div>
                                <div>Belum ada data nilai untuk sesi ini.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="sr-table-footer">
                <span>{{ $detail['counts']['total'] }} siswa · KKM: <strong>{{ $kkm }}</strong></span>
                <span>Lulus: <strong style="color:#16A34A">{{ $detail['counts']['above_kkm'] }}</strong> ·
                      Di bawah KKM: <strong style="color:#DC2626">{{ $detail['counts']['below_kkm'] }}</strong>
                </span>
            </div>

        </div>
    </div>
</div>
@endsection