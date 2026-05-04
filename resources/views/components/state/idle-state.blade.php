@props([
    'title' => 'Mulai Pencarian',
    'loadingTitle' => 'Sedang Mencari',
    'description' => 'Masukkan kata kunci di kolom pencarian untuk melihat hasil.',
    'loadingDescription' => 'Sedang melakukan pencarian...',
])

<div x-show="!searched" class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-dashed border-blue-200 rounded-xl p-12 text-center">
    <div class="max-w-md mx-auto">
        <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="text-5xl text-blue-600" :class="loading ? 'ri-loader-4-line animate-spin' : 'ri-search-eye-line'"></i>
        </div>
        
        {{-- Progress Bar (only show when loading) --}}
        <div x-show="loading" x-cloak class="mb-4">
            <div class="w-full bg-blue-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${Math.round(progress)}%`"></div>
            </div>
            <p class="text-xs text-blue-600 mt-2 font-medium" x-text="`${Math.round(progress)}%`"></p>
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 mb-3" x-text="loading ? '{{ $loadingTitle }}' : '{{ $title }}'"></h3>
        <p class="text-base text-gray-600 leading-relaxed" x-text="loading ? '{{ $loadingDescription }}' : '{{ $description }}'"></p>
    </div>
</div>
