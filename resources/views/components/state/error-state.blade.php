@props([])

<div x-show="error" x-cloak class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6 text-center">
    <i class="ri-error-warning-line text-3xl text-red-500 mb-2"></i>
    <p class="text-sm text-red-700" x-text="error"></p>
</div>
