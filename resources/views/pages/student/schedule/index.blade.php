@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Jadwal Pelajaran</span></li>
    </ol>
</nav>
@endsection

@section('content')

{{-- Header Banner --}}
<div style="
    background: linear-gradient(135deg, #1E3A8A 0%, #4F46E5 50%, #7C3AED 100%);
    border-radius: var(--radius);
    padding: 22px 28px;
    margin-bottom: 24px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.2);
    min-height: 88px;
    box-sizing: border-box;
">
    <div style="display: flex; align-items: center; gap: 16px;">
        <div style="
            width: 48px;
            height: 48px;
            min-width: 48px;
            min-height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            color: #fff;
        ">
            <i class="ri-calendar-schedule-line"></i>
        </div>
        <div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0; line-height: 1.3;">
                    Jadwal Pelajaran
                </h2>
                @if($gradeName)
                    <span style="
                        font-size: 0.8rem;
                        background: rgba(255, 255, 255, 0.22);
                        color: #fff;
                        padding: 3px 12px;
                        border-radius: 99px;
                        font-weight: 700;
                        white-space: nowrap;
                    ">
                        {{ $gradeName }}
                    </span>
                @endif
            </div>
            <div style="font-size: 0.84rem; color: rgba(255, 255, 255, 0.85); margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                <i class="ri-bookmark-3-line"></i>
                <span>{{ $activeSemester?->semester_name ?? 'Semester Aktif' }}</span>
            </div>
        </div>
    </div>

    @if(!empty($schedules))
        <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
            <div style="
                background: rgba(255, 255, 255, 0.16);
                border: 1px solid rgba(255, 255, 255, 0.25);
                border-radius: 12px;
                padding: 10px 18px;
                text-align: center;
                min-width: 110px;
            ">
                <div style="font-size: 1.2rem; font-weight: 800; line-height: 1; color: #fff;">{{ $stats['total_subjects'] ?? 0 }}</div>
                <div style="font-size: 0.68rem; color: rgba(255, 255, 255, 0.85); text-transform: uppercase; margin-top: 4px; font-weight: 700; letter-spacing: 0.5px;">Mata Pelajaran</div>
            </div>
            <div style="
                background: rgba(255, 255, 255, 0.16);
                border: 1px solid rgba(255, 255, 255, 0.25);
                border-radius: 12px;
                padding: 10px 18px;
                text-align: center;
                min-width: 110px;
            ">
                <div style="font-size: 1.2rem; font-weight: 800; line-height: 1; color: #fff;">{{ $stats['total_sessions'] ?? 0 }}</div>
                <div style="font-size: 0.68rem; color: rgba(255, 255, 255, 0.85); text-transform: uppercase; margin-top: 4px; font-weight: 700; letter-spacing: 0.5px;">Jam Sesi/Minggu</div>
            </div>
        </div>
    @endif
</div>

@if(empty($schedules))
    <div class="card" style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
        <div style="
            width: 72px; height: 72px; margin: 0 auto 16px;
            background: #EFF6FF; border-radius: 20px;
            display: grid; place-items: center; font-size: 36px; color: var(--primary);
        ">
            <i class="ri-calendar-event-line"></i>
        </div>
        <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
            Belum Ada Jadwal Pelajaran
        </div>
        <div style="font-size: 0.88rem; max-width: 420px; margin: 0 auto; color: var(--text-muted);">
            Jadwal pelajaran belum tersedia untuk kelas Anda saat ini. Silakan hubungi wali kelas atau pihak akademik.
        </div>
    </div>
@else
    {{-- Interactive Alpine Container --}}
    <div x-data="{ activeDay: {{ ($todayNum >= 1 && $todayNum <= 5 && isset($schedules[$todayNum])) ? $todayNum : 0 }} }">

        {{-- Day Filter Tabs Navigation --}}
        <div class="card" style="margin-bottom: 20px; padding: 12px 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">

                <div style="display: flex; align-items: center; gap: 6px; overflow-x: auto; padding-bottom: 2px; width: 100%;">
                    <button
                        type="button"
                        @click="activeDay = 0"
                        style="
                            padding: 8px 16px; border-radius: 10px; font-size: 0.84rem; font-weight: 600;
                            border: none; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 6px;
                            white-space: nowrap;
                        "
                        :style="activeDay === 0 ? 'background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.3);' : 'background: var(--bg); color: var(--text-secondary);'"
                    >
                        <i class="ri-calendar-2-line"></i> Semua Hari
                    </button>

                    @foreach($dayNames as $dayNum => $dayName)
                        @php
                            $hasSchedule = isset($schedules[$dayNum]);
                            $isToday = ($dayNum === $todayNum);
                        @endphp
                        <button
                            type="button"
                            @click="activeDay = {{ $dayNum }}"
                            style="
                                padding: 8px 16px; border-radius: 10px; font-size: 0.84rem; font-weight: 600;
                                border: none; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 6px;
                                position: relative; white-space: nowrap;
                            "
                            :style="activeDay === {{ $dayNum }} ? 'background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,0.3);' : 'background: {{ $hasSchedule ? "var(--bg)" : "#F8FAFC" }}; color: {{ $hasSchedule ? "var(--text-primary)" : "var(--text-muted)" }};'"
                        >
                            <span>{{ $dayName }}</span>

                            @if($hasSchedule)
                                <span style="
                                    font-size: 0.72rem; padding: 1px 6px; border-radius: 99px;
                                    font-weight: 700;
                                "
                                :style="activeDay === {{ $dayNum }} ? 'background: rgba(255,255,255,0.25); color: #fff;' : 'background: #DBEAFE; color: #1D4ED8;'"
                                >
                                    {{ count($schedules[$dayNum]) }}
                                </span>
                            @endif

                            @if($isToday)
                                <span style="
                                    width: 6px; height: 6px; border-radius: 50%; background: #10B981;
                                    display: inline-block; box-shadow: 0 0 0 2px rgba(16,185,129,0.3);
                                "></span>
                            @endif
                        </button>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- Schedule Cards by Day --}}
        @foreach($dayNames as $dayNum => $dayName)
            @php
                $hasSchedule = isset($schedules[$dayNum]);
                $isToday = ($dayNum === $todayNum);
                $dayColor = match($dayNum) {
                    1 => '#3B82F6', // Senin - Blue
                    2 => '#8B5CF6', // Selasa - Purple
                    3 => '#10B981', // Rabu - Emerald
                    4 => '#F59E0B', // Kamis - Amber
                    5 => '#EF4444', // Jumat - Red
                    6 => '#EC4899', // Sabtu - Pink
                    default => '#64748B',
                };
            @endphp

            @if($hasSchedule)
                <div
                    x-show="activeDay === 0 || activeDay === {{ $dayNum }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="card"
                    style="margin-bottom: 20px;"
                >
                    {{-- Day Header --}}
                    <div class="card-header" style="
                        padding: 16px 20px;
                        background: linear-gradient(to right, {{ $dayColor }}0D, transparent);
                        border-bottom: 1px solid var(--border);
                    ">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="
                                width: 40px; height: 40px; border-radius: 12px;
                                background: {{ $dayColor }}; color: #fff;
                                display: grid; place-items: center; font-size: 15px; font-weight: 800;
                                box-shadow: 0 4px 10px {{ $dayColor }}40;
                            ">
                                {{ substr($dayName, 0, 2) }}
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="card-title" style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-primary);">
                                        Hari {{ $dayName }}
                                    </div>
                                    @if($isToday)
                                        <span class="badge" style="
                                            background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0;
                                            font-size: 0.72rem; padding: 2px 8px; border-radius: 99px; font-weight: 700;
                                        ">
                                            <i class="ri-checkbox-circle-fill" style="font-size: 11px;"></i> Hari Ini
                                        </span>
                                    @endif
                                </div>
                                <div class="card-subtitle" style="margin-top: 2px; font-size: 0.8rem; color: var(--text-muted);">
                                    {{ count($schedules[$dayNum]) }} mata pelajaran terjadwal
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Table View --}}
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 170px;">Jam Pelajaran</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru Pengampu</th>
                                    <th style="width: 140px;">Ruangan</th>
                                    <th style="width: 130px;">Tipe Sesi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules[$dayNum] as $s)
                                    <tr>
                                        <td>
                                            <div style="
                                                display: inline-flex; align-items: center; gap: 6px;
                                                background: var(--bg); padding: 6px 12px; border-radius: 8px;
                                                border: 1px solid var(--border);
                                            ">
                                                <i class="ri-time-line" style="color: var(--primary); font-size: 14px;"></i>
                                                <span style="font-family: var(--font-mono); font-size: 0.83rem; font-weight: 600; color: var(--text-primary);">
                                                    {{ $s['jam'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <div style="
                                                    width: 32px; height: 32px; border-radius: 8px;
                                                    background: #EFF6FF; color: var(--primary);
                                                    display: grid; place-items: center; font-size: 16px; flex-shrink: 0;
                                                ">
                                                    <i class="ri-book-open-line"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-primary);">
                                                        {{ $s['mapel'] }}
                                                    </div>
                                                    @if(!empty($s['mapel_kode']))
                                                        <div style="font-size: 0.74rem; color: var(--text-muted); font-family: var(--font-mono);">
                                                            {{ $s['mapel_kode'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="
                                                    width: 28px; height: 28px; border-radius: 50%;
                                                    background: linear-gradient(135deg, #3B82F6, #6366F1);
                                                    color: #fff; display: grid; place-items: center;
                                                    font-size: 11px; font-weight: 700; flex-shrink: 0;
                                                ">
                                                    {{ strtoupper(substr($s['guru'], 0, 1)) }}
                                                </div>
                                                <span style="font-size: 0.88rem; font-weight: 500; color: var(--text-secondary);">
                                                    {{ $s['guru'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="
                                                display: inline-flex; align-items: center; gap: 4px;
                                                background: #F1F5F9; color: #334155; border: 1px solid #E2E8F0;
                                                padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;
                                            ">
                                                <i class="ri-map-pin-line" style="color: var(--primary);"></i>
                                                {{ $s['ruangan'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $tipeMap = [
                                                    'regular' => ['label' => 'Reguler', 'bg' => '#EFF6FF', 'color' => '#1D4ED8', 'border' => '#BFDBFE', 'icon' => 'ri-article-line'],
                                                    'lab' => ['label' => 'Praktikum Lab', 'bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0', 'icon' => 'ri-computer-line'],
                                                    'exam' => ['label' => 'Ujian', 'bg' => '#FFFBEB', 'color' => '#B45309', 'border' => '#FDE68A', 'icon' => 'ri-file-list-3-line'],
                                                    'extracurricular' => ['label' => 'Ekstrakurikuler', 'bg' => '#F3E8FF', 'color' => '#7E22CE', 'border' => '#DDD6FE', 'icon' => 'ri-basketball-line'],
                                                    'remedial' => ['label' => 'Remedial', 'bg' => '#FEF2F2', 'color' => '#B91C1C', 'border' => '#FECACA', 'icon' => 'ri-tools-line'],
                                                ];
                                                $t = $tipeMap[$s['tipe']] ?? ['label' => ucfirst($s['tipe']), 'bg' => '#F1F5F9', 'color' => '#475569', 'border' => '#E2E8F0', 'icon' => 'ri-information-line'];
                                            @endphp
                                            <span style="
                                                display: inline-flex; align-items: center; gap: 4px;
                                                background: {{ $t['bg'] }}; color: {{ $t['color'] }};
                                                border: 1px solid {{ $t['border'] }};
                                                padding: 4px 10px; border-radius: 99px; font-size: 0.76rem; font-weight: 600;
                                            ">
                                                <i class="{{ $t['icon'] }}"></i>
                                                {{ $t['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Single Day Empty State (if a specific day tab is selected and has no schedule) --}}
        @foreach($dayNames as $dayNum => $dayName)
            @if(!isset($schedules[$dayNum]))
                <div
                    x-show="activeDay === {{ $dayNum }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="card"
                    style="text-align: center; padding: 48px 20px; color: var(--text-muted);"
                >
                    <div style="
                        width: 56px; height: 56px; margin: 0 auto 12px;
                        background: #F8FAFC; border-radius: 16px; border: 1px solid var(--border);
                        display: grid; place-items: center; font-size: 28px; color: var(--text-muted);
                    ">
                        <i class="ri-cup-line"></i>
                    </div>
                    <div style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">
                        Tidak Ada Jadwal Hari {{ $dayName }}
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        Tidak ada jam mata pelajaran yang terjadwal untuk hari ini. Selamat beristirahat!
                    </div>
                </div>
            @endif
        @endforeach

    </div>
@endif

@endsection
