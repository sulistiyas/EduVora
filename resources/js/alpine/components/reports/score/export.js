/**
 * score/export.js
 * Alpine component: scoreExport(exportUrl)
 * Handles Excel export with loading state + error display.
 */

export default function scoreExport(exportUrl) {
    return {
        // ── State ─────────────────────────────────────────────────────────
        loading: false,
        error:   null,

        // ── Trigger export ────────────────────────────────────────────────
        triggerExport() {
            if (this.loading) return;

            this.loading = true;
            this.error   = null;

            // Append current filter params to exportUrl
            const currentParams = new URLSearchParams(window.location.search);
            const url = new URL(exportUrl, window.location.origin);

            currentParams.forEach((val, key) => url.searchParams.set(key, val));

            // Open in new tab — browser handles file download
            const link = document.createElement('a');
            link.href  = url.toString();
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Reset loading after short delay
            setTimeout(() => { this.loading = false; }, 1500);
        },
    };
}