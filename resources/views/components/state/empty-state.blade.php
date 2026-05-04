@props([
    'message' => 'Data tidak ditemukan',
])

<div x-show="results.length === 0" class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center" x-cloak>
    <i class="ri-information-line text-4xl sm:text-5xl text-yellow-600 mb-3"></i>
    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Tidak Ditemukan</h3>
    <p class="text-sm sm:text-base text-gray-600">
        {{ $message }} "<span class="font-medium" x-text="lastQuery"></span>".
    </p>
</div>
