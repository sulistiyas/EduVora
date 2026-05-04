@props(['indexRoute' => '#'])

<div x-show="searched" x-cloak>

    {{-- Error state --}}
    <x-state.error-state />

    {{-- Results Card --}}
    <div x-show="!error" class="rounded-xl border border-blue-100 bg-white shadow-sm shadow-blue-50 overflow-hidden">

        {{-- Header: total + loading bar --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-blue-100">
            <div>
                <h3 class="text-sm font-semibold text-blue-900">Daftar Roles</h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Total <span class="font-bold text-blue-900" x-text="total"></span> Roles
                    <template x-if="total > 0">
                        <span x-text="`(menampilkan ${fromRecord}–${toRecord})`"></span>
                    </template>
                </p>
            </div>
            <div x-show="loading" x-cloak class="flex items-center gap-2">
                <div class="w-28 bg-blue-100 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${Math.round(progress)}%`"></div>
                </div>
                <span class="text-xs text-slate-400" x-text="`${Math.round(progress)}%`"></span>
            </div>
        </div>

        {{-- Empty state --}}
        <div x-show="!loading && results.length === 0" class="flex flex-col items-center justify-center px-6 py-16 text-center gap-3">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center">
                <i class="ri-user-search-line text-3xl text-slate-400"></i>
            </div>
            <p class="text-sm font-bold text-blue-900">Tidak ada Roles</p>
            <p class="text-xs text-slate-400">Coba ubah filter pencarian Anda</p>
        </div>

        {{-- Table --}}
        <div x-show="results.length > 0" class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-blue-50 border-b border-blue-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Nama Role</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-400">Deskripsi</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-400 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="role in results" :key="role.role_id">
                        <tr class="border-b border-slate-100 hover:bg-blue-50/50 transition-colors">

                            {{-- Nama Role --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-sm"
                                        x-text="(role.role_name ?? '?').charAt(0).toUpperCase()"
                                    ></div>
                                    <span class="font-semibold text-blue-900" x-text="role.role_name ?? '-'"></span>
                                </div>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="px-5 py-3.5 text-slate-500 text-sm" x-text="role.role_description ?? '-'"></td>

                            {{-- Aksi --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    
                                        :href="`{{ $indexRoute }}/${role.role_id}`"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-100 bg-white text-slate-500 hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                        title="Detail">
                                        <i class="ri-eye-line text-sm"></i>
                                    </a>
                                    
                                        :href="`{{ $indexRoute }}/${role.role_id}/edit`"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-orange-200 bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors"
                                        title="Edit">
                                        <i class="ri-edit-line text-sm"></i>
                                    </a>
                                    <button
                                        type="button"
                                        @click="confirmDelete(role.role_id, role.nama, '{{ csrf_token() }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-200 bg-red-50 text-red-500 hover:bg-red-100 transition-colors"
                                        title="Hapus">
                                        <i class="ri-delete-bin-line text-sm"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="lastPage > 1" x-cloak class="flex items-center justify-between px-5 py-3.5 border-t border-blue-100 bg-blue-50/40">
            <p class="text-xs text-slate-400 font-medium">
                Menampilkan <strong class="text-blue-900" x-text="fromRecord"></strong>–<strong class="text-blue-900" x-text="toRecord"></strong>
                dari <strong class="text-blue-900" x-text="total"></strong> data
            </p>
            <div class="flex items-center gap-1">
                <button
                    @click="goToPage(page - 1)"
                    :disabled="page <= 1"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-100 bg-white text-sm text-slate-500 hover:border-blue-300 hover:text-blue-600 disabled:cursor-not-allowed disabled:opacity-40 transition-colors"
                >
                    <i class="ri-arrow-left-s-line"></i>
                </button>

                <template x-for="p in pageNumbers" :key="p">
                    <template x-if="typeof p === 'number'">
                        <button
                            @click="goToPage(p)"
                            :class="p === page
                                ? 'bg-blue-500 border-blue-500 text-white font-bold shadow-sm shadow-blue-200'
                                : 'bg-white border-blue-100 text-slate-600 hover:border-blue-300 hover:text-blue-600'"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border text-sm transition-colors"
                            x-text="p"
                        ></button>
                    </template>
                    <template x-if="typeof p === 'string'">
                        <span class="inline-flex h-8 w-8 items-center justify-center text-sm text-slate-400">…</span>
                    </template>
                </template>

                <button
                    @click="goToPage(page + 1)"
                    :disabled="page >= lastPage"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-100 bg-white text-sm text-slate-500 hover:border-blue-300 hover:text-blue-600 disabled:cursor-not-allowed disabled:opacity-40 transition-colors"
                >
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
        </div>

    </div>
</div>