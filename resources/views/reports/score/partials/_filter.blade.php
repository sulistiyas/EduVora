{{-- ─────────────────────────────────────────────────────────────────
     resources/views/teacher/reports/score/partials/_filter.blade.php
     Score Report — Filter Bar
────────────────────────────────────────────────────────────────── --}}

<div class="ar-filter" x-data="scoreFilter()">
    <form method="GET" action="{{ route('teacher.reports.score.index') }}" id="filter-form">
        <div class="ar-filter-grid" style="grid-template-columns: 1fr 1fr 1fr auto">

            {{-- Kelas --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Kelas</label>
                <select name="grade_id" class="ar-filter-select" @change="submitForm()">
                    <option value="">Semua Kelas</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->grade_id }}"
                            {{ ($filters['grade_id'] ?? '') == $grade->grade_id ? 'selected' : '' }}>
                            {{ $grade->grade_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Semester --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Semester</label>
                <select name="semester_id" class="ar-filter-select" @change="submitForm()">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->semester_id }}"
                            {{ ($filters['semester_id'] ?? '') == $semester->semester_id ? 'selected' : '' }}>
                            {{ $semester->semester_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipe Nilai --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">Tipe Nilai</label>
                <div class="sr-type-toggle">
                    @php
                        $types = [
                            'harian' => 'Harian',
                            'uts'    => 'UTS',
                            'uas'    => 'UAS',
                        ];
                    @endphp
                    @foreach($types as $val => $label)
                        <button type="button"
                                class="sr-type-btn {{ ($filters['score_type'] ?? 'harian') === $val ? 'active' : '' }}"
                                onclick="this.closest('form').querySelector('[name=score_type]').value='{{ $val }}'; this.closest('form').submit()">
                            {{ $label }}
                        </button>
                    @endforeach
                    <input type="hidden" name="score_type" value="{{ $filters['score_type'] ?? 'harian' }}">
                </div>
            </div>

            {{-- Actions --}}
            <div class="ar-filter-group">
                <label class="ar-filter-label">&nbsp;</label>
                <div class="ar-filter-actions">
                    <button type="submit" class="ar-btn ar-btn-primary">
                        <i class="ri-search-line"></i> Filter
                    </button>
                    <a href="{{ route('teacher.reports.score.index') }}"
                       class="ar-btn ar-btn-outline" title="Reset filter">
                        <i class="ri-refresh-line"></i>
                    </a>
                </div>
            </div>

        </div>

        {{-- Active Filter Chips --}}
        <div class="ar-chips">
            @if(!empty($filters['grade_id']))
                @php $activeGrade = $grades->firstWhere('grade_id', $filters['grade_id']); @endphp
                <span class="ar-chip">
                    <i class="ri-door-open-line" style="font-size:11px"></i>
                    {{ $activeGrade?->grade_name ?? $filters['grade_id'] }}
                    <a href="{{ route('teacher.reports.score.index', array_merge(request()->query(), ['grade_id' => ''])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
            @if(!empty($filters['semester_id']))
                @php $activeSemester = $semesters->firstWhere('semester_id', $filters['semester_id']); @endphp
                <span class="ar-chip">
                    <i class="ri-calendar-2-line" style="font-size:11px"></i>
                    {{ $activeSemester?->semester_name ?? $filters['semester_id'] }}
                    <a href="{{ route('teacher.reports.score.index', array_merge(request()->query(), ['semester_id' => ''])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
            @if(!empty($filters['score_type']) && $filters['score_type'] !== 'harian')
                <span class="ar-chip">
                    <i class="ri-file-list-3-line" style="font-size:11px"></i>
                    {{ strtoupper($filters['score_type']) }}
                    <a href="{{ route('teacher.reports.score.index', array_merge(request()->query(), ['score_type' => 'harian'])) }}"
                       class="ar-chip-x"><i class="ri-close-line"></i></a>
                </span>
            @endif
        </div>

    </form>
</div>