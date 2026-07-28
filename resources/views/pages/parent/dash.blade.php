@extends('layouts.app')

@section('title', 'Dashboard Orang Tua')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Dashboard Orang Tua</span></li>
    </ol>
</nav>
@endsection

@section('content')

@if(!$has_student || !$childData)
<div class="card" style="padding: 40px; text-align: center;">
    <i class="ri-user-unfollow-line" style="font-size: 48px; color: var(--text-tertiary);"></i>
    <h3 style="margin-top: 16px;">Belum Ada Data Anak Terhubung</h3>
    <p style="color: var(--text-secondary); margin-top: 8px;">Akun Anda belum terhubung dengan data siswa di sekolah ini. Silakan hubungi pihak tata usaha sekolah.</p>
</div>
@else

{{-- HERO CARD --}}
<div style="
    background: linear-gradient(135deg, #1E1B4B 0%, #312E81 50%, #4338CA 100%);
    border-radius: 20px;
    padding: 28px 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    position: relative;
    overflow: hidden;
    color: #fff;
    box-shadow: 0 10px 30px rgba(49, 46, 129, 0.25);
    margin-bottom: 24px;
">
    <div style="position:absolute;top:-40px;right:-40px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>

    <div style="
        width: 76px; height: 76px; border-radius: 20px;
        background: linear-gradient(135deg, #6366F1, #818CF8);
        display: grid; place-items: center;
        font-size: 28px; font-weight: 800; color: #fff;
        flex-shrink: 0;
        border: 3px solid rgba(255,255,255,.2);
        box-shadow: 0 8px 24px rgba(0,0,0,.2);
    ">
        {{ strtoupper(substr($childData->student->full_name ?? 'A', 0, 2)) }}
    </div>

    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
            <span style="font-size:11px;font-weight:600;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:1px;">
                MODUL PANTAU ANAK
            </span>
            <span style="padding: 2px 10px; border-radius: 20px; background: rgba(255,255,255,.15); font-size: 11px; font-weight:600;">
                {{ $childData->relationship }}
            </span>
        </div>
        <h2 style="font-size: 24px; font-weight: 700; color: #fff; margin:0; line-height: 1.2;">
            {{ $childData->student->full_name }}
        </h2>
        <div style="display: flex; gap: 16px; margin-top: 8px; font-size: 13px; color: rgba(255,255,255,.8); flex-wrap: wrap;">
            <span><i class="ri-id-card-line"></i> NIS: {{ $childData->student->nis ?? '-' }}</span>
            <span><i class="ri-school-line"></i> Kelas: {{ $childData->grade_name }}</span>
            <span><i class="ri-user-star-line"></i> Wali Kelas: {{ $childData->homeroom_teacher }}</span>
        </div>
    </div>
</div>

{{-- STATS GRID --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 20px; border-left: 4px solid #10B981;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size: 13px; color: var(--text-secondary); font-weight: 500;">Kehadiran Harian</span>
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16,185,129,.1); color: #10B981; display:grid; place-items:center;">
                <i class="ri-checkbox-circle-line" style="font-size:18px;"></i>
            </div>
        </div>
        <div style="font-size: 26px; font-weight: 700; margin-top: 8px;">{{ $attendanceSummary['persen_hadir'] }}%</div>
        <span style="font-size: 12px; color: var(--text-tertiary);">{{ $attendanceSummary['H'] }} dari {{ $attendanceSummary['total'] }} Sesi Hadir</span>
    </div>

    <div class="card" style="padding: 20px; border-left: 4px solid #3B82F6;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size: 13px; color: var(--text-secondary); font-weight: 500;">Rata-rata Nilai</span>
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59,130,246,.1); color: #3B82F6; display:grid; place-items:center;">
                <i class="ri-bar-chart-2-line" style="font-size:18px;"></i>
            </div>
        </div>
        <div style="font-size: 26px; font-weight: 700; margin-top: 8px;">{{ $rataRataNilai }}</div>
        <span style="font-size: 12px; color: var(--text-tertiary);">Nilai Akademik Semester Ini</span>
    </div>

    <div class="card" style="padding: 20px; border-left: 4px solid #F59E0B;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size: 13px; color: var(--text-secondary); font-weight: 500;">Status Tagihan SPP</span>
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245,158,11,.1); color: #F59E0B; display:grid; place-items:center;">
                <i class="ri-wallet-3-line" style="font-size:18px;"></i>
            </div>
        </div>
        <div style="font-size: 22px; font-weight: 700; margin-top: 8px; color: #F59E0B;">Rp {{ number_format($feeSummary['sisa_tagihan'], 0, ',', '.') }}</div>
        <span style="font-size: 12px; color: var(--text-tertiary);">Sisa Tagihan Belum Terbayar</span>
    </div>

    <div class="card" style="padding: 20px; border-left: 4px solid #8B5CF6;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size: 13px; color: var(--text-secondary); font-weight: 500;">Semester Aktif</span>
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(139,92,246,.1); color: #8B5CF6; display:grid; place-items:center;">
                <i class="ri-calendar-event-line" style="font-size:18px;"></i>
            </div>
        </div>
        <div style="font-size: 18px; font-weight: 700; margin-top: 12px;">{{ $childData->active_semester?->semester_name ?? 'Ganjil' }}</div>
        <span style="font-size: 12px; color: var(--text-tertiary);">Tahun Ajaran {{ $childData->active_semester?->year_name ?? '2025/2026' }}</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    {{-- JADWAL HARI INI --}}
    <div class="card" style="padding: 24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 600; margin:0;"><i class="ri-time-line"></i> Jadwal Pelajaran Anak Hari Ini</h3>
            <a href="{{ route('parent.attendance') }}" style="font-size: 13px; color: #3B82F6; text-decoration:none;">Lihat Presensi <i class="ri-arrow-right-line"></i></a>
        </div>

        @if(empty($todaySchedules))
            <p style="color: var(--text-secondary); font-size: 14px; text-align:center; padding: 20px 0;">Tidak ada jadwal pelajaran untuk hari ini.</p>
        @else
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($todaySchedules as $schedule)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: var(--bg-hover); border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #EEF2FF; color: #4F46E5; display:grid; place-items:center; font-weight: 700;">
                            <i class="ri-book-open-line"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 14px;">{{ $schedule['mapel'] }}</div>
                            <div style="font-size: 12px; color: var(--text-secondary);">Guru: {{ $schedule['guru'] }} | Ruang: {{ $schedule['ruangan'] }}</div>
                        </div>
                    </div>
                    <span class="badge badge-info" style="font-size: 12px; padding: 4px 10px;">{{ $schedule['jam'] }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- QUICK CONTACT & ACTION --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="card" style="padding: 20px;">
            <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 12px 0;"><i class="ri-contacts-line"></i> Wali Kelas Anak</h4>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #4F46E5; color:#fff; display:grid; place-items:center; font-weight: 700;">
                    {{ strtoupper(substr($childData->homeroom_teacher, 0, 2)) }}
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">{{ $childData->homeroom_teacher }}</div>
                    <div style="font-size: 12px; color: var(--text-secondary);"><i class="ri-phone-line"></i> {{ $childData->homeroom_phone }}</div>
                </div>
            </div>
            <a href="{{ route('parent.messages') }}" class="btn btn-primary" style="width: 100%; margin-top: 16px; justify-content: center; font-size: 13px;">
                <i class="ri-chat-3-line"></i> Kirim Pesan Konsultasi
            </a>
        </div>

        <div class="card" style="padding: 20px; background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border: 1px solid #BFDBFE;">
            <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 8px 0; color: #1E40AF;"><i class="ri-information-line"></i> Catatan Sekolah</h4>
            <p style="font-size: 12px; color: #1E3A8A; line-height: 1.5; margin: 0;">
                Orang tua dapat memantau presensi dan rekapitulasi nilai secara berkala melalui portal ini.
            </p>
        </div>
    </div>
</div>

@endif

@endsection
