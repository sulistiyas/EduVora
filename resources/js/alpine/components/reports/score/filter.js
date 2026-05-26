/**
 * score/filter.js
 * Alpine component: scoreFilter()
 * Handles grade, semester, score-type selects + auto-submit.
 */

export default function scoreFilter() {
    return {
        // ── Init ──────────────────────────────────────────────────────────
        init() {
            // nothing async needed; selects handle themselves
        },

        // ── Auto-submit when any select changes ───────────────────────────
        submitForm() {
            this.$nextTick(() => {
                document.getElementById('filter-form')?.submit();
            });
        },

        // ── Clear a specific filter param ─────────────────────────────────
        clearParam(param) {
            const url  = new URL(window.location.href);
            const list = Array.isArray(param) ? param : [param];
            list.forEach(p => url.searchParams.delete(p));
            window.location.href = url.toString();
        },

        // ── Reset all filters ─────────────────────────────────────────────
        resetAll() {
            window.location.href = window.location.pathname;
        },
    };
}