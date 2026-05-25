/**
 * attendance/table.js
 * Alpine component untuk tab toggle di index.blade.php
 *
 * Register di app.js:
 *   import attendanceTable from './components/reports/attendance/table'
 *   Alpine.data('attendanceTable', attendanceTable)
 */

export default function attendanceTable() {
    return {
        // ── State ──────────────────────────────────────────────
        tab: 'sessions', // 'sessions' | 'students'

        // ── Init ───────────────────────────────────────────────
        init() {
            // Restore tab dari URL hash kalau ada
            // e.g. /attendance#students → langsung buka tab siswa
            const hash = window.location.hash.replace('#', '');
            if (['sessions', 'students'].includes(hash)) {
                this.tab = hash;
            }

            // Animate progress bar saat tab students pertama kali dibuka
            this.$watch('tab', (val) => {
                if (val === 'students') {
                    this._animateStudentBars();
                }
            });
        },

        // ── Switch tab + update hash ───────────────────────────
        setTab(val) {
            this.tab = val;
            history.replaceState(null, '', `#${val}`);
        },

        // ── Animate progress bar di tab per-siswa ─────────────
        _animateStudentBars() {
            this.$nextTick(() => {
                document.querySelectorAll('.ar-student-bar-fill').forEach(bar => {
                    // Reset dulu ke 0, baru animate ke nilai asli
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = (bar.dataset.pct ?? 0) + '%';
                    }, 80);
                });
            });
        },
    };
}