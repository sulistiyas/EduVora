@extends('layouts.app')

@section('title', 'Edit Nilai')

@section('content')
<div
    x-data="teacherScoreForm({
        storeUrl:    '{{ route('teacher.scores.store') }}',
        updateUrl:   '{{ route('teacher.scores.update', $score->student_score_id) }}',
        indexUrl:    '{{ route('teacher.scores.index') }}',
        semestersUrl:'{{ route('teacher.schedules.semesters') }}',
        isEdit: true,
        existing: {
            student_score_id: {{ $score->student_score_id }},
            score:       '{{ $score->score }}',
            max_score:   '{{ $score->max_score }}',
            description: '{{ $score->description ?? '' }}',
            date:        '{{ $score->date?->format('Y-m-d') }}',
            student_name:'{{ $score->student?->full_name }}',
            subject_name:'{{ $score->gradeSubject?->subject?->subject_name }}',
            semester_name:'{{ $score->semester?->semester_name }}',
            score_type:  '{{ $score->score_type }}',
        },
    })"
    x-init="init()"
>

    {{-- BREADCRUMB --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><a href="{{ route('teacher.scores.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:11px;font-weight:500">Input Nilai</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Edit Nilai</span></li>
        </ul>
    </div>

    {{-- PAGE HEADER --}}
    <div class="page-header" style="margin-bottom:20px">
        <div class="page-header__left">
            <a href="{{ route('teacher.scores.index') }}" class="btn-back">
                <i class="ri-arrow-left-line"></i>
            </a>
            <div>
                <h1 class="page-title">Edit Nilai</h1>
                <p class="page-subtitle" x-text="existing.student_name + ' · ' + existing.subject_name + ' · ' + existing.semester_name"></p>
            </div>
        </div>
    </div>

    <div class="form-layout" style="max-width:720px">

        {{-- INFO CARD (readonly) --}}
        <div class="form-card">
            <div class="form-card__header">
                <div class="form-card__icon form-card__icon--blue">
                    <i class="ri-information-line"></i>
                </div>
                <h2 class="form-card__title">Informasi Nilai</h2>
            </div>
            <div class="form-card__body">
                <div class="form-grid form-grid--2" style="gap:12px">
                    <div class="form-group">
                        <label class="form-label">Siswa</label>
                        <input type="text" class="form-control" :value="existing.student_name" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mata Pelajaran</label>
                        <input type="text" class="form-control" :value="existing.subject_name" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Semester</label>
                        <input type="text" class="form-control" :value="existing.semester_name" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tipe Nilai</label>
                        <input type="text" class="form-control"
                            :value="{ harian: 'Harian', uts: 'UTS', uas: 'UAS' }[existing.score_type] ?? existing.score_type"
                            disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- EDIT CARD --}}
        <div class="form-card">
            <div class="form-card__header">
                <div class="form-card__icon form-card__icon--blue">
                    <i class="ri-edit-line"></i>
                </div>
                <h2 class="form-card__title">Ubah Nilai</h2>
            </div>
            <div class="form-card__body">
                <div class="form-grid" style="gap:16px">

                    <div class="form-grid form-grid--2">
                        <div class="form-group">
                            <label class="form-label form-label--required">Nilai</label>
                            <input type="number" x-model="form.score" class="form-control"
                                placeholder="0" min="0" :max="form.max_score">
                            <span class="form-error" x-show="errors.score" x-text="errors.score"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label form-label--required">Nilai Maksimal</label>
                            <input type="number" x-model="form.max_score" class="form-control"
                                placeholder="100" min="1">
                            <span class="form-error" x-show="errors.max_score" x-text="errors.max_score"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label form-label--required">Tanggal</label>
                        <input type="date" x-model="form.date" class="form-control">
                        <span class="form-error" x-show="errors.date" x-text="errors.date"></span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                        <textarea x-model="form.description" class="form-control"
                            placeholder="Catatan tambahan..." rows="3"></textarea>
                    </div>

                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('teacher.scores.index') }}" class="btn btn--secondary">Batal</a>
            <button class="btn btn--primary" @click="submit()" :disabled="submitting">
                <span x-show="!submitting">Simpan Perubahan</span>
                <span x-show="submitting" class="btn-spinner">
                    <i class="ri-loader-4-line spin"></i> Menyimpan...
                </span>
            </button>
        </div>

    </div>
</div>
@endsection