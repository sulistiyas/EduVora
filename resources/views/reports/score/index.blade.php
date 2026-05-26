{{-- reports/score/index.blade.php --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports/score.css') }}">
@endpush

@section('content')
<div class="sr-page">

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <div class="sr-header">
        <div class="sr-header__inner">

            {{-- Title --}}
            <div class="sr-header__title-row">
                <div class="sr-header__title-wrap">
                    <div class="sr-header__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                    </div>
                    <div>
                        <div class="sr-header__title">Laporan Nilai Siswa</div>
                        <div class="sr-header__subtitle">Score Report</div>
                    </div>
                </div>

                {{-- Export buttons --}}
                <div class="sr-header__actions">
                    <a href="{{ route('teacher.reports.score.export', array_filter([
                                    'grade_subject_id' => $filters['grade_subject_id'],
                                    'semester_id'      => $filters['semester_id'],
                                    'score_type'       => $filters['score_type'],
                                    'type'             => 'summary',
                                ])) }}"
                            class="sr-btn sr-btn--outline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Rekap Siswa
                    </a>
                     <a href="{{ route('teacher.reports.score.export', array_filter([
                                    'grade_subject_id' => $filters['grade_subject_id'],
                                    'semester_id'      => $filters['semester_id'],
                                    'score_type'       => $filters['score_type'],
                                    'type'             => 'sessions',
                                ])) }}"
                            class="sr-btn sr-btn--primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('teacher.reports.score.index') }}"
                  class="sr-filters" id="score-filter-form">
                <input type="hidden" name="grade_subject_id" value="{{ request('grade_subject_id') }}">
                {{-- Kelas --}}
                <div class="sr-filter-group">
                    <label class="sr-filter-label">Kelas</label>
                    <select name="grade_id" class="sr-select" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->grade_id }}"
                                    {{ request('grade_id') == $grade->grade_id ? 'selected' : '' }}>
                                {{ $grade->grade_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Mapel --}}
                <div class="sr-filter-group">
                    <label class="sr-filter-label">Mapel</label>
                    <select name="subject_id" class="sr-select" onchange="this.form.submit()">
                        <option value="">Semua Mapel</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                    {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Semester --}}
                <div class="sr-filter-group">
                    <label class="sr-filter-label">Semester</label>
                    <select name="semester_id" class="sr-select" onchange="this.form.submit()">
                        <option value="">Semua Semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->semester_id }}"
                                    {{ request('semester_id') == $semester->semester_id ? 'selected' : '' }}>
                                {{ $semester->semester_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Score type toggle --}}
                <div class="sr-filter-group">
                    <label class="sr-filter-label">Tipe Nilai</label>
                    <div class="sr-type-toggle">
                        <button type="submit" name="score_type" value=""
                                class="sr-type-btn {{ !request('score_type') ? 'sr-type-btn--active' : '' }}">
                            Semua
                        </button>
                        @foreach($score_types as $type)
                            <button type="submit" name="score_type" value="{{ $type['value'] }}"
                                    class="sr-type-btn {{ request('score_type') === $type['value'] ? 'sr-type-btn--active' : '' }}">
                                {{ $type['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @if(request()->hasAny(['grade_id', 'subject_id', 'semester_id', 'score_type', 'grade_subject_id']))
                <div class="sr-filter-group sr-filter-group--reset">
                    <label class="sr-filter-label">&nbsp;</label>
                    <a href="{{ route('teacher.reports.score.index') }}" class="sr-btn-reset">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                        </svg>
                        Reset Filter
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- ══ BODY ════════════════════════════════════════════════════════════ --}}
    <div class="sr-body">

        {{-- ── Summary Cards ─────────────────────────────────────────────── --}}
        <div class="sr-cards">
            <div class="sr-card">
                <div class="sr-card__label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                    Rata-rata Kelas
                </div>
                <div class="sr-card__value" style="color:#4F46E5">{{ $summary['class_avg_fmt'] }}</div>
                <div class="sr-card__sub">dari {{ $summary['total_sessions'] }} sesi</div>
            </div>

            <div class="sr-card">
                <div class="sr-card__label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Tertinggi
                </div>
                <div class="sr-card__value" style="color:#16A34A">{{ $summary['highest_fmt'] }}</div>
                <div class="sr-card__sub">nilai terbaik</div>
            </div>

            <div class="sr-card">
                <div class="sr-card__label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Terendah
                </div>
                <div class="sr-card__value" style="color:#DC2626">{{ $summary['lowest_fmt'] }}</div>
                <div class="sr-card__sub">perlu perhatian</div>
            </div>

            <div class="sr-card">
                <div class="sr-card__label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                    Di Bawah KKM
                </div>
                <div class="sr-card__value" style="color:#EA580C">{{ $summary['below_kkm_count'] }}</div>
                <div class="sr-card__sub">KKM: {{ $summary['kkm'] }}</div>
            </div>
        </div>

        {{-- ── KKM Alert ──────────────────────────────────────────────────── --}}
        @if($summary['has_below_kkm'])
        <div class="sr-alert sr-alert--warning">
            <span class="sr-alert__icon">⚠️</span>
            <span>
                <strong>{{ $summary['below_kkm_count'] }} siswa</strong> memiliki rata-rata nilai
                di bawah KKM ({{ $summary['kkm'] }}). Ditandai merah pada tabel di bawah.
            </span>
        </div>
        @endif

        {{-- ── Tabs ──────────────────────────────────────────────────────── --}}
        <div class="sr-panel"
             x-data="scoreReport({{ json_encode([
                 'activeTab'      => 'rekap',
                 'selectedSession'=> null,
                 'sessions'       => $sessions->items(),
             ]) }})"
             x-init="init()">

            {{-- Tab nav --}}
            <div class="sr-tabs">
                <button class="sr-tab" :class="{ 'sr-tab--active': activeTab === 'rekap' }"
                        @click="activeTab = 'rekap'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                    Rekap Per Siswa
                    <span class="sr-tab__badge sr-tab__badge--primary">DEFAULT</span>
                </button>

                <button class="sr-tab" :class="{ 'sr-tab--active': activeTab === 'sesi' }"
                        @click="activeTab = 'sesi'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Daftar Sesi
                    <span class="sr-tab__badge">{{ $sessions->total() }}</span>
                </button>
            </div>

            {{-- ── Tab: Rekap Per Siswa ───────────────────────────────────── --}}
            <div x-show="activeTab === 'rekap'" x-cloak>
                <div class="sr-table-wrap">
                    <table class="sr-table">
                        <thead>
                            <tr>
                                <th class="sr-th sr-th--left">Nama Siswa</th>
                                <th class="sr-th sr-th--left">NIS</th>
                                <th class="sr-th sr-th--center">Harian (avg)</th>
                                <th class="sr-th sr-th--center">UTS</th>
                                <th class="sr-th sr-th--center">UAS</th>
                                <th class="sr-th sr-th--center">Rata-rata</th>
                                <th class="sr-th sr-th--left">Status KKM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student_summary as $row)
                            <tr class="sr-tr {{ $row->is_below_kkm ? 'sr-tr--below-kkm' : '' }}">
                                <td class="sr-td">
                                    <div class="sr-student">
                                        <div class="sr-student__avatar {{ $row->is_below_kkm ? 'sr-student__avatar--red' : '' }}">
                                            {{ mb_substr($row->full_name, 0, 1) }}
                                        </div>
                                        <span>{{ $row->full_name }}</span>
                                    </div>
                                </td>
                                <td class="sr-td sr-td--mono">{{ $row->nis }}</td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-badge {{ ($row->avg_harian ?? 0) < $summary['kkm'] ? 'sr-score-badge--fail' : 'sr-score-badge--pass' }}">
                                        {{ $row->avg_harian_fmt }}
                                    </span>
                                </td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-badge {{ ($row->avg_uts ?? 0) < $summary['kkm'] ? 'sr-score-badge--fail' : 'sr-score-badge--pass' }}">
                                        {{ $row->avg_uts_fmt }}
                                    </span>
                                </td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-badge {{ ($row->avg_uas ?? 0) < $summary['kkm'] ? 'sr-score-badge--fail' : 'sr-score-badge--pass' }}">
                                        {{ $row->avg_uas_fmt }}
                                    </span>
                                </td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-badge sr-score-badge--lg {{ $row->is_below_kkm ? 'sr-score-badge--fail' : 'sr-score-badge--pass' }}">
                                        {{ $row->final_score_fmt }}
                                    </span>
                                </td>
                                <td class="sr-td">
                                    <span class="sr-kkm-status {{ $row->is_below_kkm ? 'sr-kkm-status--fail' : 'sr-kkm-status--pass' }}">
                                        {{ $row->is_below_kkm ? '⚠ Di bawah KKM' : '✓ Lulus KKM' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="sr-empty">
                                    <div class="sr-empty__icon">📋</div>
                                    <div>Belum ada data siswa untuk filter ini.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer rekap --}}
                <div class="sr-table-footer">
                    <span>Menampilkan {{ $student_summary->count() }} siswa · KKM: <strong>{{ $summary['kkm'] }}</strong></span>
                    <span>Rata-rata kelas: <strong style="color:#4F46E5">{{ $summary['class_avg_fmt'] }}</strong></span>
                </div>
            </div>

            {{-- ── Tab: Daftar Sesi ───────────────────────────────────────── --}}
            <div x-show="activeTab === 'sesi'" x-cloak>
                <div class="sr-table-wrap">
                    <table class="sr-table">
                        <thead>
                            <tr>
                                <th class="sr-th sr-th--left">Tanggal</th>
                                <th class="sr-th sr-th--left">Judul</th>
                                <th class="sr-th sr-th--left">Tipe</th>
                                <th class="sr-th sr-th--center">Rata-rata</th>
                                <th class="sr-th sr-th--center">Tertinggi</th>
                                <th class="sr-th sr-th--center">Terendah</th>
                                <th class="sr-th sr-th--left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                            <tr class="sr-tr">
                                <td class="sr-td sr-td--nowrap">
                                    {{ $session->score_date?->format('d M Y') }}
                                </td>
                                <td class="sr-td">
                                    <div class="sr-session-title">{{ $session->title }}</div>
                                    @if($session->description)
                                        <div class="sr-session-desc">{{ Str::limit($session->description, 60) }}</div>
                                    @endif
                                </td>
                                <td class="sr-td">
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
                                </td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-val {{ $session->avg_score < $summary['kkm'] ? 'sr-score-val--fail' : 'sr-score-val--pass' }}">
                                        {{ number_format($session->avg_score, 1) }}
                                    </span>
                                </td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-val sr-score-val--high">{{ number_format($session->highest_score, 1) }}</span>
                                </td>
                                <td class="sr-td sr-td--center">
                                    <span class="sr-score-val sr-score-val--low">{{ number_format($session->lowest_score, 1) }}</span>
                                </td>
                                <td class="sr-td">
                                    <a href="{{ route('teacher.reports.score.show', $session->score_session_id) }}"
                                       class="sr-btn-detail">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8zM12 9a3 3 0 100 6 3 3 0 000-6z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="sr-empty">
                                    <div class="sr-empty__icon">📋</div>
                                    <div>Belum ada sesi untuk filter ini.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($sessions->hasPages())
                <div class="sr-pagination">
                    {{ $sessions->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        </div>{{-- end sr-panel --}}
    </div>{{-- end sr-body --}}
</div>{{-- end sr-page --}}
@endsection
