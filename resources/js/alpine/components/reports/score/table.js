/**
 * score/table.js
 * Alpine component: scoreTable()
 * Manages:
 *   - tab switching: 'rekap' (per-student) | 'sesi' (session list)
 *   - column sort state (communicated via URL params, server-side sort)
 *   - session detail modal open/close
 */

export default function scoreTable() {
    return {
        // ── State ─────────────────────────────────────────────────────────
        tab:             'rekap',       // 'rekap' | 'sesi'
        sortCol:         'name',
        sortDir:         'asc',
        selectedSession: null,          // session object for detail modal

        // ── Init ──────────────────────────────────────────────────────────
        init() {
            // Restore tab from URL hash if present
            const hash = window.location.hash.replace('#', '');
            if (['rekap', 'sesi'].includes(hash)) this.tab = hash;

            // Restore sort from URL params
            const params = new URLSearchParams(window.location.search);
            if (params.get('sort_col')) this.sortCol = params.get('sort_col');
            if (params.get('sort_dir')) this.sortDir = params.get('sort_dir');
        },

        // ── Tab switch ────────────────────────────────────────────────────
        setTab(tab) {
            this.tab = tab;
            window.location.hash = tab;
        },

        // ── Server-side sort (navigate with params) ───────────────────────
        handleSort(col) {
            if (this.sortCol === col) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortCol = col;
                this.sortDir = 'asc';
            }

            const url = new URL(window.location.href);
            url.searchParams.set('sort_col', this.sortCol);
            url.searchParams.set('sort_dir', this.sortDir);
            window.location.href = url.toString();
        },

        // ── Sort icon helper ──────────────────────────────────────────────
        sortIcon(col) {
            if (this.sortCol !== col) return '↕';
            return this.sortDir === 'asc' ? '↑' : '↓';
        },

        // ── Session detail modal ──────────────────────────────────────────
        openSession(sessionData) {
            this.selectedSession = sessionData;
            document.body.style.overflow = 'hidden';
        },

        closeSession() {
            this.selectedSession = null;
            document.body.style.overflow = '';
        },

        // ── Keyboard close (Escape) ───────────────────────────────────────
        handleKeydown(e) {
            if (e.key === 'Escape' && this.selectedSession) this.closeSession();
        },
    };
}