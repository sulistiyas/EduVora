/**
 * score/summary.js
 * Alpine component: scoreSummary()
 * Animates the KKM distribution bar on mount.
 */

export default function scoreSummary() {
    return {
        init() {
            this.$nextTick(() => {
                setTimeout(() => this._animateBars(), 150);
            });
        },

        _animateBars() {
            document.querySelectorAll('.sr-dist-segment[data-width]').forEach(seg => {
                seg.style.width = seg.getAttribute('data-width') + '%';
            });
        },
    };
}