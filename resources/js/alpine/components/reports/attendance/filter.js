/**
 * attendance/filter.js
 * Alpine component untuk filter bar di index.blade.php
 *
 * Register di app.js:
 *   import attendanceFilter from './components/reports/attendance/filter'
 *   Alpine.data('attendanceFilter', attendanceFilter)
 */

export default function attendanceFilter() {
    return {
        // ── State ──────────────────────────────────────────────
        hasActiveFilter: false,

        // ── Init ───────────────────────────────────────────────
        init() {
            this.checkActiveFilter();
        },

        // ── Cek apakah ada filter aktif (untuk styling form) ──
        checkActiveFilter() {
            const params = new URLSearchParams(window.location.search);
            const filterKeys = ['date_from', 'date_to', 'grade_id', 'subject_id', 'status'];
            this.hasActiveFilter = filterKeys.some(k => params.get(k));
        },

        // ── Reset semua filter ─────────────────────────────────
        resetFilters() {
            window.location.href = window.location.pathname;
        },

        // ── Submit form filter ─────────────────────────────────
        submitFilter() {
            document.getElementById('filter-form').submit();
        },
    };
}