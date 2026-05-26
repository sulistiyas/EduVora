/**
 * public/js/alpine/score-report.js
 *
 * Alpine component untuk halaman Laporan Nilai (Score Report).
 * Di-import via app.js -> window.scoreReport
 */

export default function scoreReport (config = {}) {
    return {
        /*
        |------------------------------------------------------------------
        | STATE
        |------------------------------------------------------------------
        */
        activeTab: config.activeTab ?? 'rekap',
        sessions:  config.sessions  ?? [],

        /*
        |------------------------------------------------------------------
        | INIT
        |------------------------------------------------------------------
        */
        init() {
            // Restore active tab dari URL hash jika ada
            const hash = window.location.hash.replace('#', '');
            if (['rekap', 'sesi'].includes(hash)) {
                this.activeTab = hash;
            }

            // Sync hash saat tab berubah
            this.$watch('activeTab', (val) => {
                history.replaceState(null, '', `#${val}`);
            });
        },

        /*
        |------------------------------------------------------------------
        | HELPERS
        |------------------------------------------------------------------
        */

        /**
         * Format angka ke 1 desimal.
         */
        fmt(val) {
            if (val === null || val === undefined || val === '-') return '-';
            return Number(val).toFixed(1);
        },
    };
};