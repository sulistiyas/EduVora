{{--
    Reusable filter card for Data pages.

    Props:
        $cols   – Tailwind grid-cols classes for the filter inputs (default: sm:grid-cols-2 lg:grid-cols-4)

    Named slots:
        $slot          – filter inputs (rendered inside the grid)
        $activeFilters – (optional) active filter badge row; only shown when hasActiveFilter is truthy

    Usage:
        <x-filter-card cols="sm:grid-cols-2 lg:grid-cols-3">
            <x-slot:activeFilters>
                <template x-if="query.trim()">...</template>
                <button @click="clearFilters()">Reset</button>
            </x-slot:activeFilters>

            <div>... filter input 1 ...</div>
            <div>... filter input 2 ...</div>
        </x-filter-card>
--}}

@props([
    'cols' => 'sm:grid-cols-2 lg:grid-cols-4',
])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-1 gap-4 {{ $cols }}">
        {{ $slot }}
    </div>

    @isset($activeFilters)
        <div x-show="hasActiveFilter" x-cloak class="mt-3 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500">Filter aktif:</span>
            {{ $activeFilters }}
        </div>
    @endisset
</div>
