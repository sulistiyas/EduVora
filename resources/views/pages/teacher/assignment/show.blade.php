@extends('layouts.app')

@section('title', 'Detail Tugas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/teacher-score.css') }}">
    <style>
        .badge-pending { background: #FEF9C3; color: #CA8A04; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .badge-submitted { background: #DBEAFE; color: #2563EB; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        .badge-graded { background: #DCFCE7; color: #16A34A; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
    </style>
@endpush

@section('content')
<div x-data="teacherAssignmentShow()">

    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><a href="{{ route('teacher.assignments.index') }}">Tugas</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Detail</span></li>
        </ul>
    </div>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap">
        <div>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2">
                {{ $assignment->title }}
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                {{ $assignment->grade->grade_name }} - {{ $assignment->subject->subject_name }}
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('teacher.assignments.index') }}" class="sc-action" style="background:#fff;border:1px solid var(--border-color)">
                <i class="ri-arrow-left-line"></i>
                Kembali
            </a>
        </div>
    </div>

    <div style="background:#fff;border:1px solid var(--border-color);border-radius:12px;padding:20px;margin-bottom:24px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Deskripsi</div>
                <div style="font-size:14px;color:var(--text-primary);white-space:pre-wrap">{{ $assignment->description ?: '-' }}</div>
            </div>
            <div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Tenggat Waktu</div>
                <div style="font-size:14px;color:var(--text-primary)">{{ $assignment->due_date->format('d M Y') }}</div>
                @if($assignment->attachment)
                <div style="margin-top:12px">
                    <a href="{{ Storage::url($assignment->attachment) }}" target="_blank" style="font-size:13px;color:#2563EB;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                        <i class="ri-attachment-line"></i> Unduh Lampiran Tugas
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="dt-wrap">
        <div class="dt-toolbar">
            <div style="font-weight:600;font-size:16px;">Pengumpulan Siswa ({{ $assignment->submissions->count() }})</div>
        </div>

        <div class="dt-table-wrapper">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th style="width:25%">Nama Siswa</th>
                        <th>NIS</th>
                        <th>Waktu Submit</th>
                        <th>File</th>
                        <th>Status</th>
                        <th style="width:100px;text-align:right">Skor (0-100)</th>
                        <th style="width:200px">Feedback</th>
                        <th style="width:80px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignment->submissions as $sub)
                    <tr x-data="{ 
                            editing: false, 
                            score: '{{ $sub->score ?? '' }}',
                            feedback: '{{ $sub->feedback ?? '' }}',
                            saving: false,
                            async save() {
                                if(!this.score) { this.editing = false; return; }
                                this.saving = true;
                                try {
                                    const res = await fetch('{{ route('teacher.assignments.grade', [$assignment->id, $sub->id]) }}', {
                                        method: 'PATCH',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        },
                                        body: JSON.stringify({ score: this.score, feedback: this.feedback })
                                    });
                                    if(res.ok) {
                                        this.editing = false;
                                        window.location.reload();
                                    } else {
                                        alert('Gagal menyimpan nilai');
                                    }
                                } finally { this.saving = false; }
                            }
                        }">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="avatar" style="width:32px;height:32px;font-size:12px;background:#F1F5F9;color:#475569">
                                    {{ substr($sub->student->full_name ?? 'U', 0, 2) }}
                                </div>
                                <span style="font-weight:500;font-size:13px" title="{{ $sub->student->full_name ?? '-' }}">
                                    {{ Str::limit($sub->student->full_name ?? '-', 25) }}
                                </span>
                            </div>
                        </td>
                        <td style="font-size:13px">{{ $sub->student->nis ?? '-' }}</td>
                        <td style="font-size:13px">
                            @if($sub->submitted_at)
                                {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($sub->file_path)
                                <a href="{{ Storage::url($sub->file_path) }}" target="_blank" style="color:#2563EB;font-size:13px"><i class="ri-file-download-line"></i> Unduh</a>
                            @else
                                <span style="color:#94A3B8;font-size:13px">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-{{ $sub->status }}">{{ $sub->statusLabel() }}</span>
                        </td>
                        <td style="text-align:right">
                            <span x-show="!editing" style="font-weight:600;font-size:14px">{{ $sub->score ?? '-' }}</span>
                            <input x-show="editing" type="number" min="0" max="100" x-model="score" class="dt-search-input" style="width:60px;height:30px;padding:0 8px;text-align:right">
                        </td>
                        <td>
                            <span x-show="!editing" style="font-size:13px;color:var(--text-muted)">{{ $sub->feedback ?? '-' }}</span>
                            <input x-show="editing" type="text" x-model="feedback" class="dt-search-input" style="width:100%;height:30px;padding:0 8px" placeholder="Catatan...">
                        </td>
                        <td style="text-align:center">
                            <template x-if="!editing">
                                <button @click="editing = true" class="sc-action" style="padding:4px 8px;font-size:12px;height:auto">Nilai</button>
                            </template>
                            <template x-if="editing">
                                <div style="display:flex;gap:4px;justify-content:center">
                                    <button @click="save()" class="sc-action primary" style="padding:4px 8px;font-size:12px;height:auto"><i class="ri-check-line"></i></button>
                                    <button @click="editing = false" class="sc-action" style="padding:4px 8px;font-size:12px;height:auto;background:#fff;border:1px solid #ccc"><i class="ri-close-line"></i></button>
                                </div>
                            </template>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('teacherAssignmentShow', () => ({
        init() {}
    }));
});
</script>
@endpush
