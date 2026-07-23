@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div
    x-data="auditLogSearch({
        indexUrl: '{{ route('audit-logs.index') }}',
        tables: {{ json_encode($tables) }}
    })"
    x-init="init()"
>

    {{-- ── PAGE HEADER ─────────────────────────────────────────── --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
        <div>
            <ul class="breadcrumb-list">
                <li>
                    <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                        <i class="ri-home-4-line"></i> Dashboard
                    </a>
                </li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li>Sistem</li>
                <li><i class="ri-arrow-right-s-line"></i></li>
                <li><span>Audit Logs</span></li>
            </ul>
            <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
                Audit Logs
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Pantau seluruh aktivitas perubahan data di seluruh platform
            </p>
        </div>
    </div>

    {{-- ── DATATABLE ────────────────────────────────────────────── --}}
    <div class="dt-wrap">

        {{-- Toolbar --}}
        <div class="dt-toolbar">
            <div class="dt-toolbar-left">
                <div class="dt-search" :class="search ? 'has-value' : ''">
                    <i class="ri-search-line dt-search-icon"></i>
                    <input
                        x-model="search"
                        @input.debounce.400ms="fetchLogs()"
                        type="text"
                        placeholder="Cari aksi, tabel, atau pengguna..."
                        class="dt-search-input"
                    />
                    <button x-show="search" @click="search=''; fetchLogs()" class="dt-search-clear">×</button>
                </div>
                <div style="position:relative" @click.outside="tableFilterOpen=false">
                    <div
                        @click="tableFilterOpen = !tableFilterOpen"
                        class="dt-select"
                        style="display:flex;align-items:center;justify-content:space-between;min-width:160px;cursor:pointer">
                        <span x-text="tableFilterLabel()"></span>
                        <i class="ri-arrow-down-s-line" style="font-size:16px;transition:.2s"
                            :style="tableFilterOpen ? 'transform:rotate(180deg)' : ''"></i>
                    </div>
                    <div
                        x-show="tableFilterOpen"
                        x-transition
                        style="position:absolute;top:calc(100% + 4px);left:0;width:100%;background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius-sm);box-shadow:0 6px 16px rgba(0,0,0,.08);overflow:hidden;z-index:50">
                        <div
                            @click="setTableFilter(''); tableFilterOpen=false"
                            style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                            @mouseenter="$el.style.background='var(--bg)'"
                            @mouseleave="$el.style.background='transparent'"
                            :style="tableFilter === '' ? 'background:var(--bg);font-weight:600' : ''">
                            <span>Semua Tabel</span>
                            <i x-show="tableFilter === ''" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                        </div>
                        <template x-for="t in tables" :key="t">
                            <div
                                @click="setTableFilter(t); tableFilterOpen=false"
                                style="padding:9px 12px;font-size:13px;cursor:pointer;display:flex;justify-content:space-between;align-items:center"
                                @mouseenter="$el.style.background='var(--bg)'"
                                @mouseleave="$el.style.background='transparent'"
                                :style="tableFilter === t ? 'background:var(--bg);font-weight:600' : ''">
                                <span x-text="t"></span>
                                <i x-show="tableFilter === t" class="ri-check-line" style="font-size:14px;color:var(--primary)"></i>
                            </div>
                        </template>
                    </div>
                </div>
                <input x-model="dateFrom" @change="fetchLogs()" type="date" class="dt-select" style="min-width:140px" placeholder="Dari tanggal" />
                <input x-model="dateTo" @change="fetchLogs()" type="date" class="dt-select" style="min-width:140px" placeholder="Sampai tanggal" />
                <select x-model="perPage" @change="fetchLogs()" class="dt-select">
                    <option value="10">10 / halaman</option>
                    <option value="15">15 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>
            <div class="dt-divider"></div>
            <div class="dt-toolbar-right">
                <button class="dt-icon-btn" title="Refresh" @click="fetchLogs(meta.current_page)">
                    <i class="ri-refresh-line"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="dt-table-wrap">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th class="col-no">#</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th class="hide-sm">Tabel</th>
                        <th class="hide-sm">Record ID</th>
                        <th>IP Address</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Skeleton --}}
                    <template x-if="loading">
                        <template x-for="i in 5" :key="i">
                            <tr>
                                <td class="col-no"><div class="dt-skel w-24" style="margin:0 auto"></div></td>
                                <td><div class="dt-skel w-3/4"></div></td>
                                <td><div class="dt-skel w-3/4"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-24"></div></td>
                                <td class="hide-sm"><div class="dt-skel w-16"></div></td>
                                <td><div class="dt-skel w-24"></div></td>
                                <td><div class="dt-skel w-24"></div></td>
                            </tr>
                        </template>
                    </template>

                    {{-- Data rows --}}
                    <template x-if="!loading && logs.length > 0">
                        <template x-for="(log, i) in logs" :key="log.id">
                            <tr>
                                <td class="col-no dt-mono" x-text="(meta.current_page - 1) * meta.per_page + i + 1"></td>
                                <td>
                                    <div class="dt-user">
                                        <div class="dt-av" :class="avatarColor(i)" x-text="initials(log.user?.name ?? 'S')"></div>
                                        <div>
                                            <div class="dt-user-name" x-text="log.user?.name ?? '-'"></div>
                                            <div style="font-size:11px;color:var(--text-muted)" x-text="log.user?.email ?? ''"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="dt-badge" :class="actionBadge(log.action)" x-text="log.action"></span>
                                </td>
                                <td class="hide-sm dt-muted" x-text="log.table_name ?? '—'"></td>
                                <td class="hide-sm dt-mono" style="font-size:12px" x-text="log.record_id ?? '—'"></td>
                                <td class="dt-muted" style="font-size:12px" x-text="log.ip_address ?? '—'"></td>
                                <td>
                                    <span style="font-size:12px;color:var(--text-muted)" x-text="formatDate(log.created_at)"></span>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>

            {{-- Empty --}}
            <div x-show="!loading && logs.length === 0" class="dt-empty">
                <div class="dt-empty-icon"><i class="ri-file-list-3-line"></i></div>
                <div class="dt-empty-title">Tidak ada log aktivitas</div>
                <div class="dt-empty-sub">Belum ada perubahan data yang tercatat</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="dt-footer">
            <div class="dt-info">
                Menampilkan
                <strong x-text="logs.length > 0 ? (meta.current_page - 1) * meta.per_page + 1 : 0"></strong>–<strong
                    x-text="(meta.current_page - 1) * meta.per_page + logs.length"
                ></strong>
                dari <strong x-text="meta.total"></strong> data
            </div>
            <div class="dt-pagination">
                <button class="dt-page" @click="changePage(meta.current_page - 1)" :disabled="meta.current_page <= 1">
                    <i class="ri-arrow-left-s-line"></i>
                </button>
                <template x-for="page in visiblePages()" :key="page">
                    <button class="dt-page" :class="page === meta.current_page ? 'is-active' : ''"
                        @click="changePage(page)" x-text="page"></button>
                </template>
                <button class="dt-page" @click="changePage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page">
                    <i class="ri-arrow-right-s-line"></i>
                </button>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function auditLogSearch({ indexUrl, tables }) {
    return {
        logs: [],
        meta: { current_page: 1, per_page: 15, total: 0, last_page: 1 },
        loading: false,
        search: '',
        tableFilter: '',
        tableFilterOpen: false,
        dateFrom: '',
        dateTo: '',
        perPage: 15,

        init() {
            this.fetchLogs();
        },

        async fetchLogs(page = 1) {
            this.loading = true;
            const params = new URLSearchParams({
                search: this.search,
                table_name: this.tableFilter,
                date_from: this.dateFrom,
                date_to: this.dateTo,
                per_page: this.perPage,
                page: page,
            });
            try {
                const res = await fetch(`${indexUrl}?${params}`, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                this.logs = json.data;
                this.meta = json.meta;
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page < 1 || page > this.meta.last_page) return;
            this.fetchLogs(page);
        },

        visiblePages() {
            const pages = [];
            const last = this.meta.last_page;
            const curr = this.meta.current_page;
            let start = Math.max(1, curr - 2);
            let end = Math.min(last, curr + 2);
            if (end - start < 4) { start = Math.max(1, end - 4); end = Math.min(last, start + 4); }
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },

        setTableFilter(val) {
            this.tableFilter = val;
            this.fetchLogs();
        },

        tableFilterLabel() {
            return this.tableFilter || 'Semua Tabel';
        },

        formatDate(dt) {
            if (!dt) return '—';
            const d = new Date(dt);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },

        initials(name) {
            return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
        },

        avatarColor(i) {
            const colors = ['ua-blue','ua-green','ua-violet','ua-orange','ua-pink'];
            return colors[i % colors.length];
        },

        actionBadge(action) {
            if (!action) return '';
            const a = action.toLowerCase();
            if (a.includes('create') || a.includes('store') || a.includes('tambah')) return 'badge-success';
            if (a.includes('update') || a.includes('edit') || a.includes('ubah')) return 'badge-info';
            if (a.includes('delete') || a.includes('hapus')) return 'badge-danger';
            if (a.includes('toggle') || a.includes('status')) return 'badge-warning';
            return 'badge-info';
        },
    };
}
</script>
@endpush
