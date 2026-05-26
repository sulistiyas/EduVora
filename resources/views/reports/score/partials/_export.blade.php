{{-- ─────────────────────────────────────────────────────────────────
     resources/views/teacher/reports/score/partials/_export.blade.php
     Score Report — Export Button (inline, used in page header)
────────────────────────────────────────────────────────────────── --}}

<div x-data="scoreExport('{{ route('teacher.reports.score.export') }}')">
    <button @click="triggerExport()"
            :disabled="loading"
            class="ar-btn ar-btn-export">
        <span x-show="!loading">
            <i class="ri-download-2-line"></i> Export Excel
        </span>
        <span x-show="loading" style="display:none">
            <i class="ri-loader-4-line spin"></i> Menyiapkan...
        </span>
    </button>
    <p x-show="error" x-text="error"
       style="color:var(--danger);font-size:12px;margin-top:6px;display:none"></p>
</div>