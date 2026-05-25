/**
 * attendance/export.js
 * Alpine component untuk export handler di index & show blade.
 * Menangani: loading state, konfirmasi, trigger download.
 *
 * Register di app.js:
 *   import attendanceExport from './components/reports/attendance/export'
 *   Alpine.data('attendanceExport', attendanceExport)
 *
 * Usage di blade:
 *   <div x-data="attendanceExport()">
 *       <button @click="triggerExport" :disabled="loading">
 *           <span x-show="!loading">Export Excel</span>
 *           <span x-show="loading">Menyiapkan...</span>
 *       </button>
 *   </div>
 */

export default function attendanceExport(exportUrl = '') {
    return {
        // ── State ──────────────────────────────────────────────
        loading:  false,
        error:    null,
        url:      exportUrl,

        // ── Init ───────────────────────────────────────────────
        init() {
            // Ambil URL dari attribute kalau tidak di-pass lewat constructor
            // Contoh blade: x-data="attendanceExport($el.dataset.url)"
            if (!this.url && this.$el.dataset?.exportUrl) {
                this.url = this.$el.dataset.exportUrl;
            }
        },

        // ── Trigger export (download via hidden anchor) ────────
        async triggerExport() {
            if (this.loading || !this.url) return;

            this.loading = true;
            this.error   = null;

            try {
                const res = await fetch(this.url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content') ?? '',
                    },
                });

                if (!res.ok) {
                    throw new Error(`Server error: ${res.status}`);
                }

                // Cek content-type — kalau JSON berarti error dari controller
                const contentType = res.headers.get('content-type') ?? '';
                if (contentType.includes('application/json')) {
                    const json = await res.json();
                    throw new Error(json.message ?? 'Export gagal.');
                }

                // Download file
                const blob     = await res.blob();
                const blobUrl  = URL.createObjectURL(blob);
                const filename = this._getFilename(res) ?? 'attendance-report.xlsx';

                const a  = document.createElement('a');
                a.href   = blobUrl;
                a.download = filename;
                a.click();

                URL.revokeObjectURL(blobUrl);

            } catch (err) {
                this.error = err.message ?? 'Terjadi kesalahan saat export.';
                console.error('[attendanceExport]', err);
            } finally {
                this.loading = false;
            }
        },

        // ── Ambil filename dari Content-Disposition header ─────
        _getFilename(response) {
            const disposition = response.headers.get('content-disposition');
            if (!disposition) return null;

            const match = disposition.match(/filename[^;=\n]*=['"]?([^'"\n]+)['"]?/);
            return match ? match[1] : null;
        },
    };
}