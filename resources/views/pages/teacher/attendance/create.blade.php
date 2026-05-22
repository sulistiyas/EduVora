@extends('layouts.app')

@section('title', 'Buat Sesi Absensi')

@section('content')
<div style="max-width:480px;margin:0 auto">

    {{-- Breadcrumb --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li><a href="{{ route('dashboard') }}" class="breadcrumb-home"><i class="ri-home-4-line"></i> Dashboard</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><a href="{{ route('teacher.attendance.index') }}" style="color:var(--primary);text-decoration:none">Presensi</a></li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li>Buat Sesi</li>
        </ul>
    </div>

    {{-- Card --}}
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px 24px">

        {{-- Header --}}
        <div style="margin-bottom:24px">
            <div style="font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:4px">
                <i class="ri-calendar-check-line" style="color:var(--primary);margin-right:6px"></i>
                Buat Sesi Absensi
            </div>
            <div style="font-size:13px;color:var(--text-muted)">Pilih tanggal untuk sesi absensi ini.</div>
        </div>

        {{-- Schedule Info --}}
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:20px">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:8px">
                Info Jadwal
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
                <div style="display:flex;gap:8px;align-items:center;font-size:13px">
                    <i class="ri-book-2-line" style="color:var(--primary);width:16px"></i>
                    <span style="color:var(--text-secondary)">Mata Pelajaran:</span>
                    <strong>{{ $schedule->gradeSubject?->subject?->subject_name ?? '-' }}</strong>
                </div>
                <div style="display:flex;gap:8px;align-items:center;font-size:13px">
                    <i class="ri-group-line" style="color:var(--primary);width:16px"></i>
                    <span style="color:var(--text-secondary)">Kelas:</span>
                    <strong>{{ $schedule->gradeSubject?->grade?->grade_name ?? '-' }}</strong>
                </div>
                <div style="display:flex;gap:8px;align-items:center;font-size:13px">
                    <i class="ri-book-open-line" style="color:var(--primary);width:16px"></i>
                    <span style="color:var(--text-secondary)">Semester:</span>
                    <strong>{{ $schedule->semester?->semester_name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('teacher.attendance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->schedule_id }}">

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">
                    <i class="ri-calendar-line" style="margin-right:4px"></i>
                    Tanggal Absensi <span style="color:#EF4444">*</span>
                </label>
                <input
                    type="date"
                    name="attendance_date"
                    value="{{ old('attendance_date', now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}"
                    required
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;
                           font-size:14px;color:var(--text-primary);background:var(--bg);
                           outline:none;box-sizing:border-box"
                    onfocus="this.style.borderColor='var(--primary)'"
                    onblur="this.style.borderColor='var(--border)'"
                >
                @error('attendance_date')
                    <div style="margin-top:6px;font-size:12px;color:#EF4444">{{ $message }}</div>
                @enderror
                <div style="margin-top:6px;font-size:11px;color:var(--text-muted)">
                    <i class="ri-information-line"></i>
                    Tanggal tidak bisa lebih dari hari ini.
                </div>
            </div>

            <div style="display:flex;gap:10px">
                <a href="{{ route('teacher.attendance.index') }}"
                   style="flex:1;text-align:center;padding:10px 16px;border:1px solid var(--border);
                          border-radius:10px;font-size:13px;font-weight:600;color:var(--text-secondary);
                          text-decoration:none;background:var(--bg)">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
                <button type="submit"
                        style="flex:2;padding:10px 16px;background:var(--primary);color:#fff;
                               border:none;border-radius:10px;font-size:13px;font-weight:600;
                               cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px">
                    <i class="ri-check-line"></i> Buat Sesi Absensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection