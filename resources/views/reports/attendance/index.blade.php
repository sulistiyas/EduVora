@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')

@php
    // ── Safeguard ──────────────────────────────────────────────
    $sessions       ??= null;
    $students       ??= collect();
    $summary        ??= [
        'total_sessions'   => 0,
        'total_present'    => 0,
        'total_permission' => 0,
        'total_sick'       => 0,
        'total_absent'     => 0,
        'total_late'       => 0,
        'total_detail'     => 0,
        'attendance_rate'  => 0,
        'absence_rate'     => 0,
    ];
    $grades         ??= collect();
    $subjects       ??= collect();
    $filters        ??= [];
    $status_options ??= [];

    // Rate color helper
    $rateClass = fn($rate) => $rate >= 80 ? 'ar-rate-good' : ($rate >= 60 ? 'ar-rate-warn' : 'ar-rate-bad');
@endphp

{{-- ── PAGE HEADER ── --}}
<div class="ar-header">
    <div class="ar-header-left">
        <h1 class="ar-title">
            <i class="ri-file-chart-line"></i>
            Attendance Reports
        </h1>
        <p class="ar-subtitle">Rekap presensi siswa berdasarkan sesi mengajar Anda</p>
    </div>
    {{-- <div x-data="attendanceExport('{{ route('teacher.reports.attendance.export', request()->query()) }}')">
        <button @click="triggerExport()" :disabled="loading" class="ar-btn ar-btn-export">
            <span x-show="!loading"><i class="ri-download-2-line"></i> Export Excel</span>
            <span x-show="loading"><i class="ri-loader-4-line spin"></i> Menyiapkan...</span>
        </button>
        <p x-show="error" x-text="error" style="color:var(--danger);font-size:12px;margin-top:6px"></p>
    </div> --}}
</div>

{{-- ── FILTER BAR ── --}}
<div class="ar-filter" x-data="attendanceFilter()">
    <form method="GET" action="{{ route('teacher.reports.attendance.index') }}" id="filter-form">
        <div class="ar-filter-grid">

            {{-- Date Range --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Rentang Tanggal</label>
                <div class="ar-date-wrap">
                    <i class="ri-calendar-line"></i>
                    <input type="text"
                           id="date-range"
                           name="date_range"
                           class="ar-filter-input"
                           placeholder="Pilih rentang tanggal"
                           readonly
                           autocomplete="off">
                    {{-- hidden inputs untuk date_from & date_to --}}
                    <input type="hidden" name="date_from" id="date_from"
                           value="{{ $filters['date_from'] ?? '' }}">
                    <input type="hidden" name="date_to" id="date_to"
                           value="{{ $filters['date_to'] ?? '' }}">
                </div>
            </div>

            {{-- Kelas --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Kelas</label>
                <select name="grade_id" class="ar-filter-select">
                    <option value="">Semua Kelas</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->grade_id }}"
                            {{ ($filters['grade_id'] ?? '') == $grade->grade_id ? 'selected' : '' }}>
                            {{ $grade->grade_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Mata Pelajaran --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Mata Pelajaran</label>
                <select name="subject_id" class="ar-filter-select">
                    <option value="">Semua Mapel</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}"
                            {{ ($filters['subject_id'] ?? '') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Status Kehadiran</label>
                <select name="status" class="ar-filter-select">
                    <option value="">Semua Status</option>
                    @foreach($status_options as $val => $label)
                        <option value="{{ $val }}"
                            {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">&nbsp;</label>
                <div class="ar-filter-actions">
                    <button type="submit" class="ar-btn ar-btn-primary">
                        <i class="ri-search-line"></i> Filter
                    </button>
                    <a href="{{ route('teacher.reports.attendance.index') }}"
                       class="ar-btn ar-btn-outline" title="Reset filter">
                        <i class="ri-refresh-line"></i>
                    </a>
                </div>
            </div>

        </div>

        {{-- Active Filter Chips --}}
        <div class="ar-chips">
            @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                <span class="ar-chip">
                    <i class="ri-calendar-line" style="font-size:11px"></i>
                    {{ $filters['date_from'] ?? '...' }} → {{ $filters['date_to'] ?? '...' }}
                    <a href="{{ route('teacher.reports.attendance.index', array_merge(request()->query(), ['date_from'=>'','date_to'=>''])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
            @if(!empty($filters['grade_id']))
                @php $activeGrade = $grades->firstWhere('grade_id', $filters['grade_id']); @endphp
                <span class="ar-chip">
                    <i class="ri-door-open-line" style="font-size:11px"></i>
                    {{ $activeGrade?->grade_name ?? $filters['grade_id'] }}
                    <a href="{{ route('teacher.reports.attendance.index', array_merge(request()->query(), ['grade_id'=>''])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
            @if(!empty($filters['subject_id']))
                @php $activeSubject = $subjects->firstWhere('id', $filters['subject_id']); @endphp
                <span class="ar-chip">
                    <i class="ri-book-open-line" style="font-size:11px"></i>
                    {{ $activeSubject?->subject_name ?? $filters['subject_id'] }}
                    <a href="{{ route('teacher.reports.attendance.index', array_merge(request()->query(), ['subject_id'=>''])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
            @if(!empty($filters['status']))
                <span class="ar-chip">
                    <i class="ri-checkbox-circle-line" style="font-size:11px"></i>
                    {{ $status_options[$filters['status']] ?? $filters['status'] }}
                    <a href="{{ route('teacher.reports.attendance.index', array_merge(request()->query(), ['status'=>''])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
        </div>

    </form>
</div>

{{-- ── SUMMARY CARDS ── --}}
<div class="ar-summary">

    {{-- Total Sesi --}}
    <div class="ar-sum-card blue">
        <div class="ar-sum-icon blue"><i class="ri-calendar-check-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_sessions'] }}</div>
        <div class="ar-sum-label">Total Sesi</div>
        <div class="ar-sum-rate">
            <i class="ri-information-line"></i>
            <span>{{ $summary['total_detail'] }} total presensi</span>
        </div>
    </div>

    {{-- Hadir --}}
    <div class="ar-sum-card green">
        <div class="ar-sum-icon green"><i class="ri-checkbox-circle-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_present'] }}</div>
        <div class="ar-sum-label">Hadir</div>
        <div class="ar-sum-rate">
            <strong class="{{ $rateClass($summary['attendance_rate']) }}">
                {{ $summary['attendance_rate'] }}%
            </strong>
            <span>tingkat kehadiran</span>
        </div>
    </div>

    {{-- Izin --}}
    <div class="ar-sum-card sky">
        <div class="ar-sum-icon sky"><i class="ri-file-text-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_permission'] }}</div>
        <div class="ar-sum-label">Izin</div>
        <div class="ar-sum-rate">
            @php $pct = $summary['total_detail'] > 0 ? round($summary['total_permission'] / $summary['total_detail'] * 100, 1) : 0; @endphp
            <strong>{{ $pct }}%</strong>
            <span>dari total presensi</span>
        </div>
    </div>

    {{-- Sakit --}}
    <div class="ar-sum-card yellow">
        <div class="ar-sum-icon yellow"><i class="ri-heart-pulse-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_sick'] }}</div>
        <div class="ar-sum-label">Sakit</div>
        <div class="ar-sum-rate">
            @php $pct = $summary['total_detail'] > 0 ? round($summary['total_sick'] / $summary['total_detail'] * 100, 1) : 0; @endphp
            <strong>{{ $pct }}%</strong>
            <span>dari total presensi</span>
        </div>
    </div>

    {{-- Alpha --}}
    <div class="ar-sum-card red">
        <div class="ar-sum-icon red"><i class="ri-close-circle-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_absent'] }}</div>
        <div class="ar-sum-label">Alpha</div>
        <div class="ar-sum-rate">
            <strong class="{{ $rateClass(100 - $summary['absence_rate']) }}">
                {{ $summary['absence_rate'] }}%
            </strong>
            <span>tingkat alpha</span>
        </div>
    </div>

    {{-- Terlambat --}}
    <div class="ar-sum-card slate">
        <div class="ar-sum-icon slate"><i class="ri-time-line"></i></div>
        <div class="ar-sum-val">{{ $summary['total_late'] }}</div>
        <div class="ar-sum-label">Terlambat</div>
        <div class="ar-sum-rate">
            @php $pct = $summary['total_detail'] > 0 ? round($summary['total_late'] / $summary['total_detail'] * 100, 1) : 0; @endphp
            <strong>{{ $pct }}%</strong>
            <span>dari total presensi</span>
        </div>
    </div>

</div>

{{-- ── TABS + TABLE ── --}}
<div x-data="attendanceTable()">

    {{-- Tab toggle --}}
    <div class="ar-tabs">
        <button class="ar-tab" :class="{ active: tab === 'sessions' }"
                @click="tab = 'sessions'" type="button">
            <i class="ri-calendar-schedule-line"></i>
            Per Sesi
        </button>
        <button class="ar-tab" :class="{ active: tab === 'students' }"
                @click="tab = 'students'" type="button">
            <i class="ri-group-line"></i>
            Per Siswa
        </button>
    </div>

    {{-- ══════════════
         TAB: SESSIONS
    ══════════════ --}}
    <div x-show="tab === 'sessions'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        <div class="ar-table-wrap">
            <div class="ar-table-header">
                <div class="ar-table-title">
                    <i class="ri-calendar-schedule-line"></i>
                    Daftar Sesi Presensi
                    @if($sessions)
                        <span class="ar-table-count">{{ $sessions->total() }} sesi</span>
                    @endif
                </div>
            </div>

            @if($sessions && $sessions->count() > 0)
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th class="col-no">#</th>
                            <th>Tanggal</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Pertemuan</th>
                            <th style="text-align:center">Hadir</th>
                            <th style="text-align:center">Izin</th>
                            <th style="text-align:center">Sakit</th>
                            <th style="text-align:center">Alpha</th>
                            <th style="text-align:center">Terlambat</th>
                            <th>Status</th>
                            <th class="col-act">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $i => $session)
                        <tr>
                            <td class="col-no dt-muted">
                                {{ $sessions->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <div class="dt-user-name">
                                    {{ $session->attendance_date->format('d M Y') }}
                                </div>
                                <div class="dt-user-email">
                                    {{ $session->attendance_date->translatedFormat('l') }}
                                </div>
                            </td>
                            <td>
                                <span class="tch-kelas-badge">
                                    {{ $session->grade?->grade_name ?? '-' }}
                                </span>
                            </td>
                            <td class="dt-user-name">
                                {{ $session->subject?->subject_name ?? '-' }}
                            </td>
                            <td class="dt-mono" style="text-align:center">
                                {{ $session->meeting_number }}
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini h">H {{ $session->present_count }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini i">I {{ $session->permission_count }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini s">S {{ $session->sick_count }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini a">A {{ $session->absent_count }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini l">L {{ $session->late_count }}</span>
                            </td>
                            <td>
                                @php
                                    $statusMap = [
                                        'draft'     => ['class'=>'ar-status izin',  'label'=>'Draft'],
                                        'submitted' => ['class'=>'ar-status hadir', 'label'=>'Submitted'],
                                        'approved'  => ['class'=>'ar-status hadir', 'label'=>'Approved'],
                                    ];
                                    $st = $statusMap[$session->status] ?? ['class'=>'ar-status', 'label'=>$session->status];
                                @endphp
                                <span class="{{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td class="col-act">
                                <div class="dt-actions">
                                    <a href="{{ route('teacher.reports.attendance.show', $session->attendance_session_id) }}"
                                       class="dt-act" title="Lihat Detail">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="dt-footer">
                <div class="dt-info">
                    Menampilkan <strong>{{ $sessions->firstItem() }}–{{ $sessions->lastItem() }}</strong>
                    dari <strong>{{ $sessions->total() }}</strong> sesi
                </div>
                <div class="dt-pagination">
                    {{-- Prev --}}
                    <a href="{{ $sessions->previousPageUrl() }}"
                       class="dt-page {{ $sessions->onFirstPage() ? 'disabled' : '' }}"
                       {{ $sessions->onFirstPage() ? 'aria-disabled=true' : '' }}>
                        <i class="ri-arrow-left-s-line"></i>
                    </a>

                    @foreach($sessions->getUrlRange(1, $sessions->lastPage()) as $page => $url)
                        @if($page == 1 || $page == $sessions->lastPage() || abs($page - $sessions->currentPage()) <= 1)
                            <a href="{{ $url }}"
                               class="dt-page {{ $page == $sessions->currentPage() ? 'is-active' : '' }}">
                                {{ $page }}
                            </a>
                        @elseif(abs($page - $sessions->currentPage()) == 2)
                            <span class="dt-page-dots">…</span>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    <a href="{{ $sessions->nextPageUrl() }}"
                       class="dt-page {{ !$sessions->hasMorePages() ? 'disabled' : '' }}"
                       {{ !$sessions->hasMorePages() ? 'aria-disabled=true' : '' }}>
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>

            @else
            <div class="ar-empty">
                <div class="ar-empty-icon"><i class="ri-calendar-2-line"></i></div>
                <div class="ar-empty-title">Tidak ada sesi ditemukan</div>
                <p class="ar-empty-sub">Coba ubah filter atau rentang tanggal untuk melihat data.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════
         TAB: PER SISWA
    ══════════════ --}}
    <div x-show="tab === 'students'" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         style="display:none">

        <div class="ar-table-wrap">
            <div class="ar-table-header">
                <div class="ar-table-title">
                    <i class="ri-group-line"></i>
                    Rekap Per Siswa
                    <span class="ar-table-count">{{ $students->count() }} siswa</span>
                </div>
            </div>

            @if($students->count() > 0)
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th class="col-no">#</th>
                            <th>Siswa</th>
                            <th style="text-align:center">H</th>
                            <th style="text-align:center">I</th>
                            <th style="text-align:center">S</th>
                            <th style="text-align:center">A</th>
                            <th style="text-align:center">L</th>
                            <th style="text-align:center">Total</th>
                            <th>Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                        @php
                            $rate      = $student->attendance_rate ?? 0;
                            $barClass  = $rate >= 80 ? '' : ($rate >= 60 ? 'warn' : 'bad');
                        @endphp
                        <tr>
                            <td class="col-no dt-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="dt-user">
                                    <div class="dt-av av-blue">
                                        {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="dt-user-name">{{ $student->full_name }}</div>
                                        <div class="dt-user-email">{{ $student->nis }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini h">{{ $student->total_present }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini i">{{ $student->total_permission }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini s">{{ $student->total_sick }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini a">{{ $student->total_absent }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="ar-mini l">{{ $student->total_late }}</span>
                            </td>
                            <td style="text-align:center">
                                <span class="dt-mono">{{ $student->total_sessions }}</span>
                            </td>
                            <td>
                                <div class="ar-student-bar-wrap">
                                    <div class="ar-student-bar">
                                        <div class="ar-student-bar-fill {{ $barClass }}"
                                             style="width:{{ $rate }}%"
                                             data-pct="{{ $rate }}">
                                        </div>
                                    </div>
                                    <span class="ar-student-pct
                                        {{ $rate >= 80 ? 'ar-rate-good' : ($rate >= 60 ? 'ar-rate-warn' : 'ar-rate-bad') }}">
                                        {{ $rate }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="ar-empty">
                <div class="ar-empty-icon"><i class="ri-group-line"></i></div>
                <div class="ar-empty-title">Tidak ada data siswa</div>
                <p class="ar-empty-sub">Pilih filter kelas atau semester untuk melihat rekap per siswa.</p>
            </div>
            @endif
        </div>
    </div>

</div>
{{-- end x-data --}}

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports/attendance.css') }}">
{{-- Litepicker --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">
@endpush

@push('scripts')
{{-- Litepicker --}}
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Litepicker date range ──────────────────────────────────
    const dateFrom = document.getElementById('date_from').value;
    const dateTo   = document.getElementById('date_to').value;

    const picker = new Litepicker({
        element:        document.getElementById('date-range'),
        singleMode:     false,
        numberOfMonths: 2,
        numberOfColumns: 2,
        format:         'YYYY-MM-DD',
        autoApply:      true,
        resetButton:    true,
        setup: (picker) => {
            picker.on('selected', (date1, date2) => {
                document.getElementById('date_from').value = date1.format('YYYY-MM-DD');
                document.getElementById('date_to').value   = date2.format('YYYY-MM-DD');
            });
            picker.on('clear:selection', () => {
                document.getElementById('date_from').value = '';
                document.getElementById('date_to').value   = '';
            });
        },
    });

    // Set initial value ke picker kalau ada filter aktif
    if (dateFrom && dateTo) {
        picker.setDateRange(dateFrom, dateTo);
    }

    // ── Progress bar animation (tab siswa) ────────────────────
    function animateBars() {
        document.querySelectorAll('.ar-student-bar-fill').forEach(bar => {
            bar.style.width = bar.getAttribute('data-pct') + '%';
        });
    }

    // Trigger saat tab siswa aktif (Alpine event)
    document.addEventListener('alpine:initialized', () => {
        // Observe tab change via MutationObserver agar bar animate
        const studentTab = document.querySelector('[x-show*="students"]');
        if (!studentTab) return;

        const obs = new MutationObserver(() => {
            if (studentTab.style.display !== 'none') {
                setTimeout(animateBars, 100);
            }
        });

        obs.observe(studentTab, { attributes: true, attributeFilter: ['style'] });
    });

});
</script>
@endpush