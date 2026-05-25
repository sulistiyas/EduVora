/**
 * attendance/summary.js
 * Alpine component untuk summary cards
 * Saat ini ringan — hanya format angka & tooltip.
 * Dipisah agar mudah dikembangkan (misal: realtime refresh via fetch).
 *
 * Register di app.js:
 *   import attendanceSummary from './components/reports/attendance/summary'
 *   Alpine.data('attendanceSummary', attendanceSummary)
 */

export default function attendanceSummary(initialData = {}) {
    return {
        // ── State ──────────────────────────────────────────────
        data: {
            total_sessions:   initialData.total_sessions   ?? 0,
            total_present:    initialData.total_present    ?? 0,
            total_permission: initialData.total_permission ?? 0,
            total_sick:       initialData.total_sick       ?? 0,
            total_absent:     initialData.total_absent     ?? 0,
            total_late:       initialData.total_late       ?? 0,
            total_detail:     initialData.total_detail     ?? 0,
            attendance_rate:  initialData.attendance_rate  ?? 0,
            absence_rate:     initialData.absence_rate     ?? 0,
        },

        // ── Init ───────────────────────────────────────────────
        init() {
            // Kosong untuk sekarang.
            // Nanti bisa ditambah: fetch('/api/attendance/summary?...')
            // lalu update this.data secara reaktif tanpa reload halaman.
        },

        // ── Helpers ────────────────────────────────────────────

        /**
         * Hitung persentase dari total_detail
         */
        pct(count) {
            if (!this.data.total_detail) return '0.0';
            return ((count / this.data.total_detail) * 100).toFixed(1);
        },

        /**
         * CSS class untuk warna rate (good/warn/bad)
         */
        rateClass(rate) {
            if (rate >= 80) return 'ar-rate-good';
            if (rate >= 60) return 'ar-rate-warn';
            return 'ar-rate-bad';
        },

        /**
         * Format angka dengan separator ribuan
         */
        fmt(num) {
            return Number(num).toLocaleString('id-ID');
        },
    };
}