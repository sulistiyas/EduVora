@extends('layouts.app')

@section('title', 'Tambah Nilai')

@section('content')
<div
    x-data="teacherScoreForm({
        storeUrl:        '{{ route('teacher.scores.store') }}',
        indexUrl:        '{{ route('teacher.scores.index') }}',
        semestersUrl:    '{{ route('teacher.schedules.semesters') }}',
        gradeSubjectsUrl:'{{ route('teacher.grade-subjects') }}',
        studentsUrl:     '{{ route('teacher.students') }}',
        isEdit: false,
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
            <li><span>Tambah Nilai</span></li>
        </ul>
    </div>

    {{-- PAGE HEADER --}}
    <div class="page-header" style="margin-bottom:20px">
        <div class="page-header__left">
            <a href="{{ route('teacher.scores.index') }}" class="btn-back">
                <i class="ri-arrow-left-line"></i>
            </a>
            <div>
                <h1 class="page-title">Tambah Nilai</h1>
                <p class="page-subtitle">Isi form di bawah untuk menambah nilai siswa</p>
            </div>
        </div>
    </div>

    <div class="form-layout" style="max-width:720px">

        {{-- CARD: Data Nilai --}}
        <div class="form-card">
            <div class="form-card__header">
                <div class="form-card__icon form-card__icon--blue">
                    <i class="ri-bar-chart-line"></i>
                </div>
                <h2 class="form-card__title">Data Nilai</h2>
            </div>
            <div class="form-card__body">
                <div class="form-grid" style="gap:16px">

                    {{-- Semester --}}
                    <div class="form-group">
                        <label class="form-label form-label--required">Semester</label>
                        <select x-model="form.semester_id" class="form-control" @change="onSemesterChange()">
                            <option value="">Pilih Semester</option>
                            <template x-for="sem in semesters" :key="sem.semester_id">
                                <option :value="sem.semester_id" x-text="sem.semester_name"></option>
                            </template>
                        </select>
                        <span class="form-error" x-show="errors.semester_id" x-text="errors.semester_id"></span>
                    </div>

                    {{-- Mata Pelajaran --}}
                    <div class="form-group">
                        <label class="form-label form-label--required">Mata Pelajaran</label>
                        <select x-model="form.grade_subject_id" class="form-control"
                            @change="onSubjectChange()" :disabled="!form.semester_id || loadingSubjects">
                            <option value="">
                                <span x-text="loadingSubjects ? 'Memuat...' : 'Pilih Mata Pelajaran'"></span>
                            </option>
                            <template x-for="gs in gradeSubjects" :key="gs.id">
                                <option :value="gs.id"
                                    x-text="gs.subject?.subject_name + ' — ' + (gs.teacher?.full_name ?? 'Tanpa Guru')">
                                </option>
                            </template>
                        </select>
                        <span class="form-error" x-show="errors.grade_subject_id" x-text="errors.grade_subject_id"></span>
                    </div>

                    {{-- Siswa --}}
                    <div class="form-group">
                        <label class="form-label form-label--required">Siswa</label>
                        <select x-model="form.student_id" class="form-control"
                            :disabled="!form.grade_subject_id || loadingStudents">
                            <option value="">
                                <span x-text="loadingStudents ? 'Memuat...' : 'Pilih Siswa'"></span>
                            </option>
                            <template x-for="s in students" :key="s.id">
                                <option :value="s.id" x-text="s.full_name + ' (' + s.nis + ')'"></option>
                            </template>
                        </select>
                        <span class="form-error" x-show="errors.student_id" x-text="errors.student_id"></span>
                    </div>

                    {{-- Tipe Nilai --}}
                    <div class="form-group">
                        <label class="form-label form-label--required">Tipe Nilai</label>
                        <div class="radio-group">
                            <template x-for="type in scoreTypes" :key="type.value">
                                <label class="radio-card" :class="form.score_type === type.value && 'active'">
                                    <input type="radio" x-model="form.score_type" :value="type.value" style="display:none">
                                    <span class="radio-card__dot"
                                        :class="form.score_type === type.value ? 'radio-card__dot--active' : 'radio-card__dot--inactive'">
                                    </span>
                                    <span x-text="type.label"></span>
                                </label>
                            </template>
                        </div>
                        <span class="form-error" x-show="errors.score_type" x-text="errors.score_type"></span>
                    </div>

                    {{-- Nilai & Nilai Maksimal --}}
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

                    {{-- Tanggal --}}
                    <div class="form-group">
                        <label class="form-label form-label--required">Tanggal</label>
                        <input type="date" x-model="form.date" class="form-control">
                        <span class="form-error" x-show="errors.date" x-text="errors.date"></span>
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group">
                        <label class="form-label">Keterangan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                        <textarea x-model="form.description" class="form-control"
                            placeholder="Catatan tambahan..." rows="3"></textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="form-actions">
            <a href="{{ route('teacher.scores.index') }}" class="btn btn--secondary">Batal</a>
            <button class="btn btn--primary" @click="submit()" :disabled="submitting">
                <span x-show="!submitting">Simpan Nilai</span>
                <span x-show="submitting" class="btn-spinner">
                    <i class="ri-loader-4-line spin"></i> Menyimpan...
                </span>
            </button>
        </div>

    </div>
</div>
@endsection