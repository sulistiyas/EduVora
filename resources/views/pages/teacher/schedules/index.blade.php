@extends('layouts.app')

@section('title', 'Jadwal Saya')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════════
   TEACHER SCHEDULE — JADWAL SAYA
   Design: Weekly calendar + list view, read-only teacher variant
   ═══════════════════════════════════════════════════════════════ */

/* ── Hero Banner ────────────────────────────────────────────── */
.ts-hero {
    background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 50%, #3B82F6 100%);
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    position: relative;
    overflow: hidden;
}
.ts-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.ts-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; right: 80px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}
.ts-hero-left { display: flex; align-items: center; gap: 16px; z-index: 1; }
.ts-hero-avatar {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: rgba(255,255,255,.2);
    display: grid; place-items: center;
    font-size: 20px; font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
    border: 1.5px solid rgba(255,255,255,.25);
}
.ts-hero-title {
    font-size: 20px; font-weight: 700;
    color: #fff; letter-spacing: -.4px;
    line-height: 1.2;
}
.ts-hero-sub {
    font-size: 13px; color: rgba(255,255,255,.75);
    margin-top: 4px; display: flex; align-items: center; gap: 12px;
}
.ts-hero-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 999px;
    padding: 3px 10px; font-size: 12px; color: #fff;
    backdrop-filter: blur(4px);
}
.ts-hero-date {
    text-align: right; z-index: 1; flex-shrink: 0;
}
.ts-hero-date-num {
    font-size: 42px; font-weight: 800;
    color: #fff; line-height: 1; letter-spacing: -2px;
}
.ts-hero-date-label {
    font-size: 13px; color: rgba(255,255,255,.7);
    text-align: center; margin-top: 2px;
}

/* ── Stat Cards ─────────────────────────────────────────────── */
.ts-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.ts-stat {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
}
.ts-stat-top {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.ts-stat-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: grid; place-items: center;
    font-size: 16px;
}
.ts-stat-badge {
    font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: 999px;
    letter-spacing: .3px;
}
.ts-stat-val {
    font-size: 26px; font-weight: 700;
    color: var(--text-primary); letter-spacing: -1px;
    line-height: 1;
}
.ts-stat-label {
    font-size: 12px; color: var(--text-muted);
    margin-top: 4px;
}
.ts-stat-accent {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 3px; border-radius: 0 0 14px 14px;
}

/* ── Tabs ───────────────────────────────────────────────────── */
.ts-tabs-wrap {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 16px; gap: 12px;
    flex-wrap: wrap;
}
.ts-tabs {
    display: flex; gap: 2px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px; padding: 3px;
}
.ts-tab {
    padding: 7px 18px;
    border-radius: 8px;
    font-size: 13px; font-weight: 500;
    cursor: pointer; border: none;
    background: transparent;
    color: var(--text-muted);
    transition: all .18s;
    display: flex; align-items: center; gap: 6px;
    font-family: var(--font);
}
.ts-tab.active {
    background: var(--card);
    color: var(--text-primary);
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
    border: 1px solid var(--border);
}
.ts-tab i { font-size: 15px; }

.ts-filter-row {
    display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
}
.ts-chip {
    padding: 6px 14px; border-radius: 999px;
    font-size: 12px; font-weight: 500;
    border: 1px solid var(--border);
    background: var(--card);
    cursor: pointer; color: var(--text-muted);
    transition: all .15s;
    font-family: var(--font);
}
.ts-chip:hover { border-color: #93C5FD; color: #1D4ED8; background: #EFF6FF; }
.ts-chip.active { background: #1D4ED8; color: #fff; border-color: #1D4ED8; }

/* ── Week Navigator ─────────────────────────────────────────── */
.ts-week-nav {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 14px;
}
.ts-week-label {
    font-size: 14px; font-weight: 600;
    color: var(--text-primary);
    min-width: 190px; text-align: center;
}
.ts-nav-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--card);
    cursor: pointer; display: grid; place-items: center;
    color: var(--text-muted); font-size: 16px;
    transition: all .15s;
}
.ts-nav-btn:hover { background: var(--bg); border-color: #93C5FD; color: #1D4ED8; }
.ts-today-btn {
    padding: 6px 14px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--card);
    font-size: 12px; font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer; font-family: var(--font);
    transition: all .15s;
}
.ts-today-btn:hover { background: #EFF6FF; color: #1D4ED8; border-color: #93C5FD; }

/* ── Calendar Grid ──────────────────────────────────────────── */
.ts-calendar {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
}
.ts-cal-head {
    display: grid;
    grid-template-columns: 64px repeat(6, 1fr);
}
.ts-cal-head-cell {
    padding: 12px 8px;
    text-align: center;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
}
.ts-cal-head-cell:not(:first-child) {
    border-left: 1px solid var(--border);
}
.ts-cal-day-name {
    font-size: 10px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .08em;
}
.ts-cal-day-num {
    font-size: 18px; font-weight: 600;
    color: var(--text-primary);
    margin-top: 3px; line-height: 1;
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    margin: 4px auto 0;
    border-radius: 50%;
}
.ts-cal-day-num.is-today {
    background: #1D4ED8;
    color: #fff;
    font-size: 15px;
}

/* Time row */
.ts-cal-body {
    display: grid;
    grid-template-columns: 64px repeat(6, 1fr);
}
.ts-time-cell {
    padding: 8px 10px 0;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
    min-height: 80px;
    display: flex; align-items: flex-start;
}
.ts-time-label {
    font-size: 10px; font-weight: 600;
    color: var(--text-muted);
    white-space: nowrap; letter-spacing: .03em;
}
.ts-day-cell {
    border-left: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    min-height: 80px;
    padding: 5px;
    position: relative;
    transition: background .12s;
}
.ts-day-cell.is-today-col {
    background: rgba(37,99,235,.02);
}
.ts-day-cell:last-child {
    border-right: none;
}

/* Event cards */
.ts-event {
    border-radius: 8px;
    padding: 7px 9px;
    margin-bottom: 3px;
    cursor: pointer;
    transition: transform .12s, box-shadow .12s;
    border-left: 3px solid transparent;
}
.ts-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(0,0,0,.1);
}
.ts-event-subj {
    font-size: 12px; font-weight: 700;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.ts-event-detail {
    font-size: 10px; margin-top: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    opacity: .75;
}
.ts-event-time {
    font-size: 10px; margin-top: 3px;
    opacity: .65; font-weight: 600;
    letter-spacing: .02em;
}

/* Event color variants */
.ev-math   { background: #DBEAFE; color: #1E40AF; border-left-color: #3B82F6; }
.ev-eng    { background: #D1FAE5; color: #065F46; border-left-color: #10B981; }
.ev-ips    { background: #FEF9C3; color: #78350F; border-left-color: #F59E0B; }
.ev-science{ background: #EDE9FE; color: #4C1D95; border-left-color: #8B5CF6; }
.ev-other  { background: #FCE7F3; color: #831843; border-left-color: #EC4899; }

/* Session type dots */
.ts-session-dot {
    display: inline-block; width: 6px; height: 6px;
    border-radius: 50%; margin-right: 4px; flex-shrink: 0;
    vertical-align: middle;
}

/* ── List View ──────────────────────────────────────────────── */
.ts-list-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
}
.ts-list-header {
    display: grid;
    grid-template-columns: 100px 1fr 140px 120px 100px 100px;
    gap: 12px;
    padding: 11px 16px;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    font-size: 11px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .07em;
}
.ts-list-row {
    display: grid;
    grid-template-columns: 100px 1fr 140px 120px 100px 100px;
    gap: 12px;
    padding: 13px 16px;
    align-items: center;
    border-bottom: 1px solid var(--border);
    transition: background .1s;
    cursor: pointer;
}
.ts-list-row:last-child { border-bottom: none; }
.ts-list-row:hover { background: var(--bg); }
.ts-list-row.is-today-row { background: rgba(37,99,235,.03); }

.ts-row-day { font-size: 12px; color: var(--text-muted); font-weight: 500; }
.ts-row-subject {
    display: flex; align-items: center; gap: 10px;
}
.ts-row-av {
    width: 34px; height: 34px; border-radius: 9px;
    display: grid; place-items: center;
    font-size: 12px; font-weight: 700;
    flex-shrink: 0;
}
.ts-row-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
.ts-row-grade { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
.ts-row-room { font-size: 13px; color: var(--text-secondary); }
.ts-row-room-code { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
.ts-row-time {
    font-size: 13px; font-weight: 600;
    color: var(--text-primary);
    font-variant-numeric: tabular-nums;
}

/* ── Session Badge ──────────────────────────────────────────── */
.ts-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 600;
}

/* ── Status Badge ───────────────────────────────────────────── */
.ts-status-today {
    background: #DBEAFE; color: #1E40AF;
    border-radius: 999px; padding: 3px 10px;
    font-size: 11px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 4px;
}
.ts-status-done {
    background: #D1FAE5; color: #065F46;
    border-radius: 999px; padding: 3px 10px;
    font-size: 11px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 4px;
}
.ts-status-upcoming {
    background: var(--bg); color: var(--text-muted);
    border-radius: 999px; padding: 3px 10px;
    font-size: 11px; font-weight: 600; border: 1px solid var(--border);
    display: inline-flex; align-items: center; gap: 4px;
}

/* ── Detail Drawer/Modal ────────────────────────────────────── */
.ts-drawer-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.35);
    backdrop-filter: blur(2px);
    z-index: 1000;
    display: flex; align-items: flex-end;
    justify-content: center;
}
@media (min-width: 640px) {
    .ts-drawer-overlay { align-items: center; }
}
.ts-drawer {
    background: var(--card);
    border-radius: 20px 20px 0 0;
    width: 100%; max-width: 480px;
    padding: 0;
    overflow: hidden;
    max-height: 90vh;
    display: flex; flex-direction: column;
}
@media (min-width: 640px) {
    .ts-drawer {
        border-radius: 18px;
        margin: 0;
    }
}
.ts-drawer-handle {
    width: 36px; height: 4px;
    background: var(--border);
    border-radius: 2px;
    margin: 12px auto 0;
    flex-shrink: 0;
}
.ts-drawer-head {
    padding: 16px 20px 14px;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.ts-drawer-body {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
}
.ts-drawer-close {
    position: absolute; top: 14px; right: 16px;
    width: 30px; height: 30px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg);
    display: grid; place-items: center;
    cursor: pointer; color: var(--text-muted);
    font-size: 16px;
}

.ts-detail-row {
    display: flex; align-items: flex-start;
    gap: 12px; padding: 10px 0;
    border-bottom: 1px solid var(--border);
}
.ts-detail-row:last-child { border-bottom: none; }
.ts-detail-icon {
    width: 32px; height: 32px;
    border-radius: 8px; background: var(--bg);
    display: grid; place-items: center;
    font-size: 14px; color: var(--text-muted);
    flex-shrink: 0;
}
.ts-detail-label { font-size: 11px; color: var(--text-muted); margin-bottom: 2px; }
.ts-detail-val { font-size: 13px; font-weight: 600; color: var(--text-primary); }

/* ── Quick Action Buttons in Drawer ────────────────────────── */
.ts-quick-btn {
    flex: 1;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--bg);
    font-size: 12px; font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    font-family: var(--font);
    display: flex; flex-direction: column;
    align-items: center; gap: 6px;
    transition: all .15s;
}
.ts-quick-btn i { font-size: 18px; }
.ts-quick-btn:hover { border-color: #93C5FD; color: #1D4ED8; background: #EFF6FF; }
.ts-quick-btn.primary {
    background: #1D4ED8; color: #fff; border-color: #1D4ED8;
}
.ts-quick-btn.primary:hover { background: #1E40AF; }

/* ── Empty state ────────────────────────────────────────────── */
.ts-empty {
    padding: 48px 24px;
    text-align: center;
}
.ts-empty-icon {
    font-size: 40px; color: var(--border);
    margin-bottom: 12px;
}
.ts-empty-title {
    font-size: 15px; font-weight: 600;
    color: var(--text-secondary);
}
.ts-empty-sub {
    font-size: 13px; color: var(--text-muted);
    margin-top: 4px;
}

/* ── Skeleton ───────────────────────────────────────────────── */
.ts-skel {
    border-radius: 6px;
    background: linear-gradient(90deg, var(--border) 25%, var(--bg) 50%, var(--border) 75%);
    background-size: 200% 100%;
    animation: ts-shimmer 1.4s infinite;
    height: 12px;
}
@keyframes ts-shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* ── Daily View ─────────────────────────────────────────────── */
.ts-daily-slot {
    display: flex; gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background .1s;
}
.ts-daily-slot:last-child { border-bottom: none; }
.ts-daily-slot:hover { background: var(--bg); margin: 0 -4px; padding-left: 4px; padding-right: 4px; border-radius: 8px; }
.ts-daily-time {
    width: 80px; flex-shrink: 0;
    font-size: 12px; font-weight: 700;
    color: var(--text-muted);
    padding-top: 3px;
    text-align: right;
    font-variant-numeric: tabular-nums;
}
.ts-daily-bar {
    width: 3px; border-radius: 2px;
    flex-shrink: 0; align-self: stretch;
    min-height: 40px;
}
.ts-daily-content { flex: 1; min-width: 0; }
.ts-daily-subj {
    font-size: 14px; font-weight: 700;
    color: var(--text-primary);
}
.ts-daily-meta {
    font-size: 12px; color: var(--text-muted);
    margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap;
}
.ts-daily-meta span { display: flex; align-items: center; gap: 4px; }

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 768px) {
    .ts-stats { grid-template-columns: repeat(2, 1fr); }
    .ts-hero { flex-direction: column; align-items: flex-start; }
    .ts-hero-date { display: none; }
    .ts-list-header, .ts-list-row {
        grid-template-columns: 80px 1fr 100px 80px;
    }
    .ts-list-header > *:nth-child(5),
    .ts-list-header > *:nth-child(6),
    .ts-list-row > *:nth-child(5),
    .ts-list-row > *:nth-child(6) { display: none; }
}
@media (max-width: 640px) {
    .ts-stats { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
<div
    x-data="teacherSchedule({
        indexUrl:    '{{ route('teacher.schedules.index') }}',
        showUrl:     '{{ url('teacher/schedules') }}',
        semestersUrl:'{{ route('teacher.schedules.semesters') }}',
        teacherName: '{{ auth()->user()->name ?? 'Guru' }}',
        teacherInitials: '{{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) . strtoupper(substr(strrchr(auth()->user()->name ?? '', ' '), 1, 1) ?: substr(auth()->user()->name ?? 'X', 1, 1)) }}',
        todayDay:    {{ now()->dayOfWeekIso }},
        todayDate:   {{ now()->day }},
        todayMonth:  '{{ now()->translatedFormat('F Y') }}',
        todayDayName:'{{ now()->translatedFormat('l') }}',
    })"
    x-init="init()"
>

    {{-- ── BREADCRUMB ──────────────────────────────────────────── --}}
    <div style="margin-bottom:16px">
        <ul class="breadcrumb-list">
            <li>
                <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                    <i class="ri-home-4-line"></i> Dashboard
                </a>
            </li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Jadwal Saya</span></li>
        </ul>
    </div>

    {{-- ── HERO BANNER ──────────────────────────────────────────── --}}
    <div class="ts-hero">
        <div class="ts-hero-left">
            <div class="ts-hero-avatar" x-text="teacherInitials"></div>
            <div>
                <div class="ts-hero-title">Selamat pagi, <span x-text="teacherName.split(' ')[0]"></span>! 👋</div>
                <div class="ts-hero-sub">
                    <span>
                        <i class="ri-calendar-line" style="vertical-align:-1px;margin-right:3px"></i>
                        <span x-text="todayDayName + ', ' + todayDate + ' ' + todayMonth"></span>
                    </span>
                    <span x-show="activeSemesterName">
                        <span class="ts-hero-pill">
                            <i class="ri-book-open-line"></i>
                            <span x-text="activeSemesterName"></span>
                        </span>
                    </span>
                </div>
                <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
                    <div class="ts-hero-pill">
                        <i class="ri-calendar-schedule-line"></i>
                        <span><strong x-text="todaySchedules.length"></strong> jadwal hari ini</span>
                    </div>
                    <div class="ts-hero-pill">
                        <i class="ri-door-open-line"></i>
                        <span><strong x-text="uniqueSubjects"></strong> mata pelajaran</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="ts-hero-date">
            <div class="ts-hero-date-num" x-text="todayDate"></div>
            <div class="ts-hero-date-label" x-text="todayMonth.split(' ')[0].toUpperCase()"></div>
            <div class="ts-hero-date-label" x-text="todayDayName"></div>
        </div>
    </div>

    {{-- ── STAT STRIP ───────────────────────────────────────────── --}}
    <div class="ts-stats">
        <div class="ts-stat">
            <div class="ts-stat-top">
                <div class="ts-stat-icon" style="background:#EFF6FF;color:#2563EB">
                    <i class="ri-layout-grid-line"></i>
                </div>
                <span class="ts-stat-badge" style="background:#EFF6FF;color:#1D4ED8">Aktif</span>
            </div>
            <div class="ts-stat-val" x-text="meta.total || '—'"></div>
            <div class="ts-stat-label">Total Jadwal</div>
            <div class="ts-stat-accent" style="background:#3B82F6"></div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-top">
                <div class="ts-stat-icon" style="background:#F0FDF4;color:#16A34A">
                    <i class="ri-time-line"></i>
                </div>
                <span class="ts-stat-badge" style="background:#F0FDF4;color:#15803D">Hari ini</span>
            </div>
            <div class="ts-stat-val" x-text="todaySchedules.length || '0'"></div>
            <div class="ts-stat-label">Jadwal Hari Ini</div>
            <div class="ts-stat-accent" style="background:#22C55E"></div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-top">
                <div class="ts-stat-icon" style="background:#FEF9C3;color:#CA8A04">
                    <i class="ri-book-2-line"></i>
                </div>
                <span class="ts-stat-badge" style="background:#FEF9C3;color:#A16207">Mapel</span>
            </div>
            <div class="ts-stat-val" x-text="uniqueSubjects || '—'"></div>
            <div class="ts-stat-label">Mata Pelajaran</div>
            <div class="ts-stat-accent" style="background:#EAB308"></div>
        </div>
        <div class="ts-stat">
            <div class="ts-stat-top">
                <div class="ts-stat-icon" style="background:#F5F3FF;color:#7C3AED">
                    <i class="ri-team-line"></i>
                </div>
                <span class="ts-stat-badge" style="background:#F5F3FF;color:#6D28D9">Kelas</span>
            </div>
            <div class="ts-stat-val" x-text="uniqueGrades || '—'"></div>
            <div class="ts-stat-label">Kelas Diajar</div>
            <div class="ts-stat-accent" style="background:#8B5CF6"></div>
        </div>
    </div>

    {{-- ── TOOLBAR ───────────────────────────────────────────────── --}}
    <div class="ts-tabs-wrap">
        <div class="ts-tabs">
            <button class="ts-tab" :class="view === 'weekly' && 'active'" @click="view = 'weekly'">
                <i class="ri-calendar-2-line"></i> Mingguan
            </button>
            <button class="ts-tab" :class="view === 'daily' && 'active'" @click="view = 'daily'">
                <i class="ri-sun-line"></i> Harian
            </button>
            <button class="ts-tab" :class="view === 'list' && 'active'" @click="view = 'list'">
                <i class="ri-list-check-2"></i> Daftar
            </button>
        </div>
        <div class="ts-filter-row">
            <select
                x-model="semesterFilter"
                @change="fetchSchedules()"
                style="height:34px;padding:0 10px;border-radius:8px;border:1px solid var(--border);background:var(--card);font-family:var(--font);font-size:12px;color:var(--text-primary);cursor:pointer;min-width:160px"
            >
                <option value="">Semua Semester</option>
                <template x-for="sem in semesters" :key="sem.semester_id">
                    <option :value="sem.semester_id" x-text="sem.semester_name"></option>
                </template>
            </select>
            <button
                class="ts-nav-btn"
                title="Refresh"
                @click="fetchSchedules()"
                style="width:34px;height:34px"
            >
                <i class="ri-refresh-line" style="font-size:14px"></i>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         VIEW: WEEKLY CALENDAR
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="view === 'weekly'">

        {{-- Week nav --}}
        <div class="ts-week-nav">
            <button class="ts-nav-btn" @click="prevWeek()">
                <i class="ri-arrow-left-s-line"></i>
            </button>
            <span class="ts-week-label" x-text="weekRangeLabel"></span>
            <button class="ts-nav-btn" @click="nextWeek()">
                <i class="ri-arrow-right-s-line"></i>
            </button>
            <button class="ts-today-btn" @click="goToToday()">Hari Ini</button>

            {{-- Day filter chips --}}
            <div style="margin-left:8px;display:flex;gap:6px;flex-wrap:wrap">
                <template x-for="day in calDays" :key="day.value">
                    <button
                        class="ts-chip"
                        :class="dayFilter === day.value && 'active'"
                        @click="dayFilter = dayFilter === day.value ? '' : day.value; fetchSchedules()"
                        x-text="day.short"
                    ></button>
                </template>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="ts-calendar">
            {{-- Header row --}}
            <div class="ts-cal-head">
                <div class="ts-cal-head-cell"></div>
                <template x-for="(day, idx) in weekDays" :key="idx">
                    <div class="ts-cal-head-cell">
                        <div class="ts-cal-day-name" x-text="day.name"></div>
                        <div class="ts-cal-day-num" :class="day.isToday && 'is-today'" x-text="day.date"></div>
                    </div>
                </template>
            </div>

            {{-- Skeleton --}}
            <template x-if="loading">
                <div style="padding:32px 20px;display:grid;grid-template-columns:64px repeat(6,1fr);gap:8px">
                    <template x-for="i in 42" :key="i">
                        <div class="ts-skel" :style="Math.random() > .7 ? 'height:56px' : 'height:12px;opacity:.4'"></div>
                    </template>
                </div>
            </template>

            {{-- Time slot rows --}}
            <template x-if="!loading">
                <template x-for="slot in timeSlots" :key="slot.label">
                    <div class="ts-cal-body">
                        <div class="ts-time-cell">
                            <span class="ts-time-label" x-text="slot.label"></span>
                        </div>
                        <template x-for="(day, idx) in weekDays" :key="idx">
                            <div class="ts-day-cell" :class="day.isToday && 'is-today-col'">
                                <template x-for="ev in getEventsForSlot(day.dayNum, slot)" :key="ev.schedule_id">
                                    <div
                                        class="ts-event"
                                        :class="subjectColorClass(ev.subject_name)"
                                        @click="openDetail(ev)"
                                    >
                                        <div class="ts-event-subj" x-text="ev.subject_name"></div>
                                        <div class="ts-event-detail" x-text="ev.grade_name + ' · ' + (ev.room_code || ev.room_name)"></div>
                                        <div class="ts-event-time" x-text="ev.time_range"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         VIEW: DAILY
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="view === 'daily'">
        {{-- Day selector --}}
        <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap">
            <template x-for="day in calDays" :key="day.value">
                <button
                    class="ts-chip"
                    :class="selectedDay === day.value && 'active'"
                    @click="selectedDay = day.value"
                    x-text="day.label"
                ></button>
            </template>
        </div>

        <div class="ts-list-card">
            <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                <i class="ri-sun-line" style="color:#F59E0B;font-size:16px"></i>
                <span style="font-size:14px;font-weight:700;color:var(--text-primary)" x-text="selectedDayLabel"></span>
                <span style="font-size:12px;color:var(--text-muted)" x-text="'(' + dailySchedules.length + ' sesi)'"></span>
            </div>

            <template x-if="loading">
                <div style="padding:20px">
                    <template x-for="i in 3" :key="i">
                        <div style="display:flex;gap:14px;padding:14px 0;border-bottom:1px solid var(--border)">
                            <div class="ts-skel" style="width:80px;height:16px;flex-shrink:0"></div>
                            <div style="flex:1">
                                <div class="ts-skel" style="width:60%;height:16px;margin-bottom:8px"></div>
                                <div class="ts-skel" style="width:40%;height:12px"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="!loading && dailySchedules.length > 0" style="padding:0 18px">
                <template x-for="ev in dailySchedules" :key="ev.schedule_id">
                    <div class="ts-daily-slot" @click="openDetail(ev)">
                        <div class="ts-daily-time" x-text="ev.time_range"></div>
                        <div class="ts-daily-bar" :style="'background:' + subjectColor(ev.subject_name)"></div>
                        <div class="ts-daily-content">
                            <div class="ts-daily-subj" x-text="ev.subject_name"></div>
                            <div class="ts-daily-meta">
                                <span><i class="ri-user-3-line"></i> <span x-text="ev.grade_name"></span></span>
                                <span><i class="ri-door-open-line"></i> <span x-text="ev.room_name + (ev.room_code ? ' (' + ev.room_code + ')' : '')"></span></span>
                                <span>
                                    <span
                                        class="ts-badge"
                                        :style="sessionBadgeStyle(ev.session_type)"
                                        x-text="sessionLabel(ev.session_type)"
                                    ></span>
                                </span>
                            </div>
                        </div>
                        <div style="flex-shrink:0;align-self:center">
                            <i class="ri-arrow-right-s-line" style="font-size:18px;color:var(--text-muted)"></i>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="!loading && dailySchedules.length === 0" class="ts-empty">
                <div class="ts-empty-icon"><i class="ri-calendar-check-line"></i></div>
                <div class="ts-empty-title">Tidak ada jadwal</div>
                <div class="ts-empty-sub">Tidak ada sesi mengajar di hari ini</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         VIEW: LIST
    ══════════════════════════════════════════════════════════════ --}}
    <div x-show="view === 'list'">

        {{-- Search & filter toolbar --}}
        <div style="display:flex;gap:8px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
            <div style="position:relative;flex:1;min-width:220px">
                <i class="ri-search-line" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:14px;color:var(--text-muted)"></i>
                <input
                    x-model="search"
                    @input.debounce.350ms="fetchSchedules()"
                    type="text"
                    placeholder="Cari mata pelajaran, kelas, ruangan..."
                    style="width:100%;height:36px;padding:0 10px 0 32px;border:1px solid var(--border);border-radius:8px;background:var(--card);font-family:var(--font);font-size:13px;color:var(--text-primary);outline:none"
                />
            </div>

            {{-- Day filter --}}
            <select
                x-model="dayFilter"
                @change="fetchSchedules()"
                style="height:36px;padding:0 10px;border-radius:8px;border:1px solid var(--border);background:var(--card);font-family:var(--font);font-size:12px;color:var(--text-primary);cursor:pointer"
            >
                <option value="">Semua Hari</option>
                <template x-for="day in calDays" :key="day.value">
                    <option :value="day.value" x-text="day.label"></option>
                </template>
            </select>

            {{-- Session filter --}}
            <select
                x-model="sessionFilter"
                @change="fetchSchedules()"
                style="height:36px;padding:0 10px;border-radius:8px;border:1px solid var(--border);background:var(--card);font-family:var(--font);font-size:12px;color:var(--text-primary);cursor:pointer"
            >
                <option value="">Semua Tipe Sesi</option>
                <template x-for="st in sessionTypes" :key="st.value">
                    <option :value="st.value" x-text="st.label"></option>
                </template>
            </select>

            <select
                x-model="perPage"
                @change="fetchSchedules()"
                style="height:36px;padding:0 10px;border-radius:8px;border:1px solid var(--border);background:var(--card);font-family:var(--font);font-size:12px;color:var(--text-primary);cursor:pointer"
            >
                <option value="10">10 / hal</option>
                <option value="25">25 / hal</option>
                <option value="50">50 / hal</option>
            </select>
        </div>

        <div class="ts-list-card">
            {{-- Header --}}
            <div class="ts-list-header">
                <div>Hari</div>
                <div>Mata Pelajaran</div>
                <div>Ruangan</div>
                <div>Waktu</div>
                <div>Tipe Sesi</div>
                <div>Status</div>
            </div>

            {{-- Skeleton --}}
            <template x-if="loading">
                <template x-for="i in 5" :key="i">
                    <div class="ts-list-row" style="cursor:default">
                        <div class="ts-skel" style="width:60px;height:12px"></div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="ts-skel" style="width:34px;height:34px;border-radius:9px;flex-shrink:0"></div>
                            <div style="flex:1">
                                <div class="ts-skel" style="width:70%;height:12px;margin-bottom:6px"></div>
                                <div class="ts-skel" style="width:50%;height:10px"></div>
                            </div>
                        </div>
                        <div class="ts-skel" style="width:80px;height:12px"></div>
                        <div class="ts-skel" style="width:90px;height:12px"></div>
                        <div class="ts-skel" style="width:70px;height:20px;border-radius:999px"></div>
                        <div class="ts-skel" style="width:70px;height:20px;border-radius:999px"></div>
                    </div>
                </template>
            </template>

            {{-- Rows --}}
            <template x-if="!loading && schedules.length > 0">
                <template x-for="(ev, i) in schedules" :key="ev.schedule_id">
                    <div
                        class="ts-list-row"
                        :class="ev.day_of_week === todayDay && 'is-today-row'"
                        @click="openDetail(ev)"
                    >
                        <div>
                            <div class="ts-row-day" x-text="ev.day_name"></div>
                        </div>
                        <div class="ts-row-subject">
                            <div
                                class="ts-row-av"
                                :style="'background:' + subjectBgColor(ev.subject_name) + ';color:' + subjectColor(ev.subject_name)"
                                x-text="initials(ev.subject_name)"
                            ></div>
                            <div>
                                <div class="ts-row-name" x-text="ev.subject_name"></div>
                                <div class="ts-row-grade" x-text="ev.grade_name"></div>
                            </div>
                        </div>
                        <div>
                            <div class="ts-row-room" x-text="ev.room_name"></div>
                            <div class="ts-row-room-code" x-text="ev.room_code"></div>
                        </div>
                        <div class="ts-row-time" x-text="ev.time_range"></div>
                        <div>
                            <span
                                class="ts-badge"
                                :style="sessionBadgeStyle(ev.session_type)"
                                x-text="sessionLabel(ev.session_type)"
                            ></span>
                        </div>
                        <div>
                            <span
                                :class="ev.day_of_week === todayDay ? 'ts-status-today' : 'ts-status-done'"
                            >
                                <i :class="ev.day_of_week === todayDay ? 'ri-time-line' : 'ri-check-line'"></i>
                                <span x-text="ev.day_of_week === todayDay ? 'Hari ini' : 'Terjadwal'"></span>
                            </span>
                        </div>
                    </div>
                </template>
            </template>

            {{-- Empty --}}
            <div x-show="!loading && schedules.length === 0" class="ts-empty">
                <div class="ts-empty-icon"><i class="ri-calendar-schedule-line"></i></div>
                <div class="ts-empty-title">Tidak ada data jadwal</div>
                <div class="ts-empty-sub">Coba ubah filter atau hubungi admin</div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="dt-footer" x-show="!loading && meta.total > 0">
            <div class="dt-info">
                Menampilkan
                <strong x-text="schedules.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + schedules.length"
                ></strong>
                dari <strong x-text="meta.total"></strong> data
            </div>
            <div class="dt-pagination">
                <button class="dt-page" @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <template x-for="page in meta.last_page" :key="page">
                    <button class="dt-page" :class="page === meta.current_page ? 'is-active' : ''"
                        @click="changePage(page)" x-text="page"></button>
                </template>
                <button class="dt-page" @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DETAIL DRAWER
    ══════════════════════════════════════════════════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="showDetail"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="ts-drawer-overlay"
            @click.self="closeDetail()"
            @keydown.escape.window="closeDetail()"
        >
            <div
                x-show="showDetail"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="ts-drawer"
            >
                <div class="ts-drawer-handle"></div>

                {{-- Drawer Header --}}
                <div class="ts-drawer-head" style="position:relative">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div
                            style="width:44px;height:44px;border-radius:12px;display:grid;place-items:center;font-size:16px;font-weight:700;flex-shrink:0"
                            :style="'background:' + subjectBgColor(selectedEvent?.subject_name) + ';color:' + subjectColor(selectedEvent?.subject_name)"
                            x-text="initials(selectedEvent?.subject_name ?? '')"
                        ></div>
                        <div>
                            <div style="font-size:16px;font-weight:700;color:var(--text-primary)" x-text="selectedEvent?.subject_name"></div>
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px" x-text="selectedEvent?.grade_name"></div>
                        </div>
                    </div>
                    <button class="ts-drawer-close" @click="closeDetail()">
                        <i class="ri-close-line"></i>
                    </button>
                </div>

                {{-- Drawer Body --}}
                <div class="ts-drawer-body">

                    {{-- Session type badge --}}
                    <div style="margin-bottom:16px">
                        <span
                            class="ts-badge"
                            :style="sessionBadgeStyle(selectedEvent?.session_type)"
                            x-text="sessionLabel(selectedEvent?.session_type)"
                        ></span>
                    </div>

                    {{-- Detail rows --}}
                    <div>
                        <div class="ts-detail-row">
                            <div class="ts-detail-icon"><i class="ri-calendar-event-line"></i></div>
                            <div>
                                <div class="ts-detail-label">Hari</div>
                                <div class="ts-detail-val" x-text="selectedEvent?.day_name"></div>
                            </div>
                        </div>
                        <div class="ts-detail-row">
                            <div class="ts-detail-icon"><i class="ri-time-line"></i></div>
                            <div>
                                <div class="ts-detail-label">Waktu</div>
                                <div class="ts-detail-val" x-text="selectedEvent?.time_range"></div>
                            </div>
                        </div>
                        <div class="ts-detail-row">
                            <div class="ts-detail-icon"><i class="ri-door-open-line"></i></div>
                            <div>
                                <div class="ts-detail-label">Ruangan</div>
                                <div class="ts-detail-val">
                                    <span x-text="selectedEvent?.room_name"></span>
                                    <span x-show="selectedEvent?.room_code" style="font-size:11px;color:var(--text-muted);margin-left:6px" x-text="'(' + selectedEvent?.room_code + ')'"></span>
                                </div>
                            </div>
                        </div>
                        <div class="ts-detail-row">
                            <div class="ts-detail-icon"><i class="ri-user-3-line"></i></div>
                            <div>
                                <div class="ts-detail-label">Kelas</div>
                                <div class="ts-detail-val" x-text="selectedEvent?.grade_name"></div>
                            </div>
                        </div>
                        <div class="ts-detail-row">
                            <div class="ts-detail-icon"><i class="ri-book-open-line"></i></div>
                            <div>
                                <div class="ts-detail-label">Semester</div>
                                <div class="ts-detail-val" x-text="selectedEvent?.semester_name ?? '—'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick actions --}}
                    <div style="margin-top:20px">
                        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">
                            Aksi Cepat
                        </div>
                        <div style="display:flex;gap:8px">
                            
                            <a  :href="selectedEvent
                                    ? '{{ route('teacher.attendance.start') }}?schedule_id=' + selectedEvent.schedule_id
                                    : '#'"
                                class="ts-quick-btn primary"
                            >
                                <i class="ri-checkbox-circle-line"></i>
                                <span>Presensi</span>
                            </a>
                            
                            <a :href="selectedEvent
                                    ? '{{ route('teacher.scores.index') }}?schedule_id=' + selectedEvent.schedule_id
                                    : '#'"
                                class="ts-quick-btn">
                                <i class="ri-bar-chart-line"></i>
                                <span>Input Nilai</span>
                            </a>
                            
                            <a  :href="selectedEvent ? '/teacher/notes?schedule_id=' + selectedEvent.schedule_id : '#'"
                                class="ts-quick-btn"
                            >
                                <i class="ri-sticky-note-line"></i>
                                <span>Catatan</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>{{-- /x-data --}}
@endsection

@push('scripts')
<script>
function teacherSchedule(config = {}) {
    return {
        // ─── Config ────────────────────────────────────────────────
        indexUrl:     config.indexUrl     ?? '/teacher/schedules',
        showUrl:      config.showUrl      ?? '/teacher/schedules',
        semestersUrl: config.semestersUrl ?? '/teacher/schedules/semesters',
        teacherName:  config.teacherName  ?? 'Guru',
        teacherInitials: config.teacherInitials ?? 'GU',
        todayDay:     config.todayDay     ?? 1,
        todayDate:    config.todayDate    ?? 1,
        todayMonth:   config.todayMonth   ?? '',
        todayDayName: config.todayDayName ?? '',

        // ─── State ─────────────────────────────────────────────────
        view:           'weekly',  // 'weekly' | 'daily' | 'list'
        schedules:      [],
        meta:           { current_page: 1, per_page: 50, total: 0, last_page: 1 },
        loading:        false,
        search:         '',
        perPage:        50,
        semesterFilter: '',
        dayFilter:      '',
        sessionFilter:  '',
        selectedDay:    config.todayDay ?? 1,

        // ─── Dropdown data ──────────────────────────────────────────
        semesters:          [],
        activeSemesterName: '',

        // ─── Week nav ───────────────────────────────────────────────
        weekOffset: 0,

        // ─── Detail drawer ──────────────────────────────────────────
        showDetail:    false,
        selectedEvent: null,

        // ─── Constants ──────────────────────────────────────────────
        calDays: [
            { value: 1, label: 'Senin',  short: 'Sen' },
            { value: 2, label: 'Selasa', short: 'Sel' },
            { value: 3, label: 'Rabu',   short: 'Rab' },
            { value: 4, label: 'Kamis',  short: 'Kam' },
            { value: 5, label: 'Jumat',  short: 'Jum' },
            { value: 6, label: 'Sabtu',  short: 'Sab' },
        ],
        timeSlots: [
            { label: '07.00', hour: 7 },
            { label: '08.30', hour: 8 },
            { label: '10.00', hour: 10 },
            { label: '11.30', hour: 11 },
            { label: '13.00', hour: 13 },
            { label: '14.30', hour: 14 },
        ],
        sessionTypes: [
            { value: 'regular',         label: 'Reguler' },
            { value: 'lab',             label: 'Lab' },
            { value: 'exam',            label: 'Ujian' },
            { value: 'extracurricular', label: 'Ekstrakurikuler' },
            { value: 'remedial',        label: 'Remedial' },
        ],

        // ─── Subject color map ──────────────────────────────────────
        _colorPalette: [
            { bg: '#DBEAFE', text: '#1E40AF' },
            { bg: '#D1FAE5', text: '#065F46' },
            { bg: '#FEF9C3', text: '#78350F' },
            { bg: '#EDE9FE', text: '#4C1D95' },
            { bg: '#FCE7F3', text: '#831843' },
            { bg: '#FEF3C7', text: '#92400E' },
            { bg: '#CFFAFE', text: '#164E63' },
        ],
        _subjectColorCache: {},
        _colorIdx: 0,

        _getSubjectPalette(name) {
            if (!name) return this._colorPalette[0];
            if (!this._subjectColorCache[name]) {
                this._subjectColorCache[name] = this._colorPalette[this._colorIdx % this._colorPalette.length];
                this._colorIdx++;
            }
            return this._subjectColorCache[name];
        },
        subjectColor(name)    { return this._getSubjectPalette(name).text; },
        subjectBgColor(name)  { return this._getSubjectPalette(name).bg; },
        subjectColorClass(name) {
            const classMap = {
                0: 'ev-math', 1: 'ev-eng', 2: 'ev-ips',
                3: 'ev-science', 4: 'ev-other',
            };
            if (!name) return 'ev-math';
            const palette = this._getSubjectPalette(name);
            const idx = this._colorPalette.indexOf(palette) % 5;
            return classMap[idx] ?? 'ev-math';
        },

        // ─── Computed ───────────────────────────────────────────────
        get todaySchedules() {
            return this.schedules.filter(s => s.day_of_week === this.todayDay);
        },
        get uniqueSubjects() {
            return [...new Set(this.schedules.map(s => s.subject_name))].length;
        },
        get uniqueGrades() {
            return [...new Set(this.schedules.map(s => s.grade_name))].length;
        },
        get selectedDayLabel() {
            return this.calDays.find(d => d.value === this.selectedDay)?.label ?? '—';
        },
        get dailySchedules() {
            return this.schedules.filter(s => s.day_of_week === this.selectedDay);
        },

        // ─── Week calendar ──────────────────────────────────────────
        get weekDays() {
            // Build Mon-Sat labels with dates relative to current week offset
            const today   = new Date();
            const dayOfWeek = today.getDay() === 0 ? 7 : today.getDay(); // 1=Mon
            const monday    = new Date(today);
            monday.setDate(today.getDate() - dayOfWeek + 1 + (this.weekOffset * 7));

            return this.calDays.map((d, i) => {
                const date = new Date(monday);
                date.setDate(monday.getDate() + i);
                const isToday = date.toDateString() === today.toDateString();
                return {
                    name:   d.short,
                    date:   date.getDate(),
                    dayNum: d.value,
                    isToday,
                };
            });
        },
        get weekRangeLabel() {
            const days  = this.weekDays;
            const first = days[0];
            const last  = days[days.length - 1];
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            const today  = new Date();
            const yr     = new Date(today);
            yr.setDate(today.getDate() - (today.getDay() === 0 ? 7 : today.getDay()) + 1 + this.weekOffset * 7);
            return `${first.date} – ${last.date} ${months[yr.getMonth()]} ${yr.getFullYear()}`;
        },

        getEventsForSlot(dayNum, slot) {
            return this.schedules.filter(s => {
                if (s.day_of_week !== dayNum) return false;
                const startH = parseInt((s.start_time ?? '00:00').split(':')[0]);
                return startH === slot.hour || (startH >= slot.hour && startH < (slot.hour + 2));
            });
        },

        prevWeek()  { this.weekOffset--; },
        nextWeek()  { this.weekOffset++; },
        goToToday() { this.weekOffset = 0; },

        // ─── Init ───────────────────────────────────────────────────
        async init() {
            await this.fetchSemesters();
            await this.fetchSchedules();
        },

        // ─── Fetch semesters ────────────────────────────────────────
        async fetchSemesters() {
            try {
                const res = await fetch(this.semestersUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) return;
                this.semesters = await res.json();
                const active   = this.semesters.find(s => s.status === 'active');
                if (active) {
                    this.activeSemesterName = active.semester_name;
                    this.semesterFilter     = active.semester_id;
                }
            } catch { this.semesters = []; }
        },

        // ─── Fetch schedules ────────────────────────────────────────
        async fetchSchedules(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page,
                    per_page: this.view === 'list' ? parseInt(this.perPage) : 200, // fetch all for calendar
                    ...(this.search.trim()  ? { search:       this.search.trim()  } : {}),
                    ...(this.semesterFilter ? { semester_id:  this.semesterFilter } : {}),
                    ...(this.dayFilter      ? { day_of_week:  this.dayFilter      } : {}),
                    ...(this.sessionFilter  ? { session_type: this.sessionFilter  } : {}),
                });

                const res  = await fetch(`${this.indexUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!res.ok) throw new Error('Gagal memuat jadwal.');
                const json     = await res.json();
                this.schedules = json.data;
                this.meta      = json.meta;
            } catch (err) {
                this.showToast('error', err.message);
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchSchedules(page);
        },

        // ─── Detail drawer ──────────────────────────────────────────
        openDetail(ev) {
            this.selectedEvent = ev;
            this.showDetail    = true;
        },
        closeDetail() {
            this.showDetail    = false;
            this.selectedEvent = null;
        },

        // ─── Badge helpers ───────────────────────────────────────────
        sessionLabel(type) {
            return this.sessionTypes.find(s => s.value === type)?.label ?? type;
        },
        sessionBadgeStyle(type) {
            const map = {
                regular:         'background:#EFF6FF;color:#1D4ED8',
                lab:             'background:#F0FDF4;color:#15803D',
                exam:            'background:#FEF9C3;color:#854D0E',
                extracurricular: 'background:#F5F3FF;color:#6D28D9',
                remedial:        'background:#FFF7ED;color:#C2410C',
            };
            return map[type] ?? 'background:#F1F5F9;color:#475569';
        },

        // ─── Helpers ────────────────────────────────────────────────
        initials(name) {
            return (name ?? '').split(/[-_ ]/).map(w => w[0]?.toUpperCase() ?? '').join('').slice(0, 2);
        },
        showToast(icon, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true, position: 'top-end',
                    icon, title: message,
                    showConfirmButton: false,
                    timer: 3500, timerProgressBar: true,
                });
            }
        },
    };
}
</script>
@endpush