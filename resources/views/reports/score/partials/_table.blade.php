{{-- ─────────────────────────────────────────────────────────────────
     resources/views/teacher/reports/score/partials/_table.blade.php
     Score Report — Tabs: Rekap Per Siswa | Daftar Sesi + Modal Detail
────────────────────────────────────────────────────────────────── --}}

@php
    $studentRows ??= collect();
    $sessions    ??= collect();
    $kkm         ??= 75;
    $sortCol     ??= 'name';
    $sortDir     ??= 'asc';
    $scoreType   ??= 'harian';

    // Badge color map for score type
    $typeBadge = [
        'harian' => ['bg' => '#EEF2FF', 'color' => '#4338CA', 'label' => 'Harian'],
        'uts'    => ['bg' => '#FFF7ED', 'color' => '#C2410C', 'label' => 'UTS'],
        'uas'    => ['bg' => '#F0FDF4', 'color' => '#15803D', 'label' => 'UAS'],
    ];
@endphp

<div x-data="scoreTable()"
     x-init="init()"
     @keydown.escape.window="closeSession()">

    {{-- ── Tab Toggle ────────────────────────────────────────────────── --}}
    <div class="ar-tabs">
        <button class="ar-tab" :class="{ active: tab === 'rekap' }"
                @click="setTab('rekap')" type="button">
            <i class="ri-group-line"></i>
            Rekap Per Siswa
            <span class="sr-tab-default">DEFAULT</span>
        </button>
        <button class="ar-tab" :class="{ active: tab === 'sesi' }"
                @click="setTab('sesi')" type="button">
            <i class="ri-list-check"></i>
            Daftar Sesi
            <span class="ar-tab-count">{{ $sessions->count() }}</span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         TAB: REKAP PER SISWA
    ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'rekap'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div class="ar-table-wrap">
            <div class="ar-table-header">
                <div class="ar-table-title">
                    <i class="ri-group-line"></i>
                    Rekap Per Siswa
                    <span class="ar-table-count">{{ $studentRows->count() }} siswa</span>
                </div>
                <div style="font-size:12px;color:var(--text-muted)">
                    Rata-rata kelas:
                    <strong style="color:var(--primary)">
                        {{ number_format($studentRows->avg('final_score') ?? 0, 1) }}
                    </strong>
                    &nbsp;·&nbsp; KKM: <strong>{{ $kkm }}</strong>
                </div>
            </div>

            @if($studentRows->count() > 0)
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th class="col-no">#</th>

                            {{-- Nama Siswa --}}
                            <th class="sort {{ $sortCol === 'name' ? 'sort-'.$sortDir : '' }}"
                                onclick="window.location.href='{{ route('teacher.reports.score.index', array_merge(request()->query(), ['sort_col' => 'name', 'sort_dir' => ($sortCol === 'name' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}'">
                                <div class="dt-th-inner">
                                    Nama Siswa
                                    <span class="sort-icon">
                                        <i class="ri-arrow-up-s-line up"></i>
                                        <i class="ri-arrow-down-s-line down"></i>
                                    </span>
                                </div>
                            </th>

                            {{-- NIS --}}
                            <th>NIS</th>

                            {{-- Harian (avg) --}}
                            <th style="text-align:center"
                                class="sort {{ $sortCol === 'harian_avg' ? 'sort-'.$sortDir : '' }}"
                                onclick="window.location.href='{{ route('teacher.reports.score.index', array_merge(request()->query(), ['sort_col' => 'harian_avg', 'sort_dir' => ($sortCol === 'harian_avg' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}'">
                                <div class="dt-th-inner" style="justify-content:center">
                                    Harian (avg)
                                    <span class="sort-icon">
                                        <i class="ri-arrow-up-s-line up"></i>
                                        <i class="ri-arrow-down-s-line down"></i>
                                    </span>
                                </div>
                            </th>

                            {{-- UTS --}}
                            <th style="text-align:center"
                                class="sort {{ $sortCol === 'avg_uts' ? 'sort-'.$sortDir : '' }}"
                                onclick="window.location.href='{{ route('teacher.reports.score.index', array_merge(request()->query(), ['sort_col' => 'avg_uts', 'sort_dir' => ($sortCol === 'avg_uts' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}'">
                                <div class="dt-th-inner" style="justify-content:center">
                                    UTS
                                    <span class="sort-icon">
                                        <i class="ri-arrow-up-s-line up"></i>
                                        <i class="ri-arrow-down-s-line down"></i>
                                    </span>
                                </div>
                            </th>

                            {{-- UAS --}}
                            <th style="text-align:center"
                                class="sort {{ $sortCol === 'avg_uas' ? 'sort-'.$sortDir : '' }}"
                                onclick="window.location.href='{{ route('teacher.reports.score.index', array_merge(request()->query(), ['sort_col' => 'avg_uas', 'sort_dir' => ($sortCol === 'avg_uas' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}'">
                                <div class="dt-th-inner" style="justify-content:center">
                                    UAS
                                    <span class="sort-icon">
                                        <i class="ri-arrow-up-s-line up"></i>
                                        <i class="ri-arrow-down-s-line down"></i>
                                    </span>
                                </div>
                            </th>

                            {{-- Rata-rata --}}
                            <th style="text-align:center"
                                class="sort {{ $sortCol === 'total' ? 'sort-'.$sortDir : '' }}"
                                onclick="window.location.href='{{ route('teacher.reports.score.index', array_merge(request()->query(), ['sort_col' => 'total', 'sort_dir' => ($sortCol === 'total' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}'">
                                <div class="dt-th-inner" style="justify-content:center">
                                    Rata-rata
                                    <span class="sort-icon">
                                        <i class="ri-arrow-up-s-line up"></i>
                                        <i class="ri-arrow-down-s-line down"></i>
                                    </span>
                                </div>
                            </th>

                            {{-- Status KKM --}}
                            <th>Status KKM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentRows as $idx => $row)
                        @php
                            $belowKkm = ($row->final_score ?? 0) < $kkm;

                            // Score pill helper
                            $pill = fn($val) => [
                                'bg'    => $val < $kkm ? '#FEE2E2' : '#DCFCE7',
                                'color' => $val < $kkm ? '#DC2626' : '#16A34A',
                            ];
                        @endphp
                        <tr style="{{ $belowKkm ? 'background:#FFF5F5' : '' }}">

                            <td class="col-no dt-muted">{{ $loop->iteration }}</td>

                            {{-- Nama --}}
                            <td>
                                <div class="dt-user">
                                    <div class="dt-av {{ $belowKkm ? 'av-red' : 'av-blue' }}"
                                         style="font-size:12px">
                                        {{ strtoupper(substr($row->full_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="dt-user-name">{{ $row->full_name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- NIS --}}
                            <td class="dt-mono">{{ $row->nis ?? '-' }}</td>

                            {{-- Harian avg --}}
                            <td style="text-align:center">
                                @php $p = $pill($row->avg_harian ?? 0); @endphp
                                <span class="sr-score-pill" style="background:{{ $p['bg'] }};color:{{ $p['color'] }}">
                                    {{ number_format($row->avg_harian ?? 0, 1) }}
                                </span>
                            </td>

                            {{-- UTS --}}
                            <td style="text-align:center">
                                @php $p = $pill($row->avg_uts ?? 0); @endphp
                                <span class="sr-score-pill" style="background:{{ $p['bg'] }};color:{{ $p['color'] }}">
                                    {{ number_format($row->avg_uts ?? 0, 1) }}
                                </span>
                            </td>

                            {{-- UAS --}}
                            <td style="text-align:center">
                                @php $p = $pill($row->avg_uas ?? 0); @endphp
                                <span class="sr-score-pill" style="background:{{ $p['bg'] }};color:{{ $p['color'] }}">
                                    {{ number_format($row->avg_uas ?? 0, 1) }}
                                </span>
                            </td>

                            {{-- Rata-rata --}}
                            <td style="text-align:center">
                                @php $p = $pill($row->final_score ?? 0); @endphp
                                <span class="sr-score-pill sr-score-pill--lg" style="background:{{ $p['bg'] }};color:{{ $p['color'] }}">
                                    {{ number_format($row->final_score ?? 0, 1) }}
                                </span>
                            </td>

                            {{-- Status KKM --}}
                            <td>
                                @if($belowKkm)
                                    <span class="ar-status alpha">⚠ Di bawah KKM</span>
                                @else
                                    <span class="ar-status hadir">✓ Lulus KKM</span>
                                @endif
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="dt-footer">
                <div class="dt-info">
                    Menampilkan <strong>{{ $studentRows->count() }}</strong> siswa ·
                    KKM: <strong>{{ $kkm }}</strong>
                </div>
                <div class="dt-info">
                    Rata-rata kelas:
                    <strong style="color:var(--primary)">
                        {{ number_format($studentRows->avg('final_score') ?? 0, 1) }}
                    </strong>
                </div>
            </div>

            @else
            <div class="ar-empty">
                <div class="ar-empty-icon"><i class="ri-file-list-3-line"></i></div>
                <div class="ar-empty-title">Tidak ada data siswa</div>
                <p class="ar-empty-sub">Pilih kelas dan semester untuk melihat rekap nilai.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         TAB: DAFTAR SESI
    ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'sesi'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         style="display:none">

        <div class="ar-table-wrap">
            <div class="ar-table-header">
                <div class="ar-table-title">
                    <i class="ri-list-check"></i>
                    Daftar Sesi Penilaian
                    <span class="ar-table-count">{{ $sessions->count() }} sesi</span>
                </div>
            </div>

            @if($sessions->count() > 0)
            <div class="dt-table-wrap">
                <table class="dt-table">
                    <thead>
                        <tr>
                            <th class="col-no">#</th>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th style="text-align:center">Rata-rata</th>
                            <th style="text-align:center">Tertinggi</th>
                            <th style="text-align:center">Terendah</th>
                            <th style="text-align:center">Di Bawah KKM</th>
                            <th class="col-act">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $i => $session)
                        @php
                            $sAvg   = $session->avg_score       ?? 0;
                            $sHigh  = $session->highest_score   ?? 0;
                            $sLow   = $session->lowest_score    ?? 0;
                            $sBelKkm = $session->below_kkm_count ?? 0;
                            $badge  = $typeBadge[$session->score_type] ?? $typeBadge['harian'];

                            // Session data JSON untuk modal (Alpine)
                            $sessionJson = json_encode([
                                'title'      => $session->title,
                                'type'       => $session->score_type,
                                'date'       => $session->score_date?->format('Y-m-d'),
                                'avg'        => round($sAvg, 1),
                                'highest'    => $sHigh,
                                'lowest'     => $sLow,
                                'below_kkm'  => $sBelKkm,
                                'detail_url' => route('teacher.reports.score.show', $session->score_session_id),
                            ]);
                        @endphp
                        <tr>
                            <td class="col-no dt-muted">{{ $loop->iteration }}</td>

                            <td>
                                <div class="dt-user-name">
                                    {{ $session->score_date?->format('d M Y') ?? '-' }}
                                </div>
                                <div class="dt-user-email">
                                    {{ $session->score_date?->translatedFormat('l') ?? '' }}
                                </div>
                            </td>

                            <td class="dt-user-name">{{ $session->title ?? '-' }}</td>

                            <td>
                                <span class="sr-type-badge"
                                      style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>

                            <td style="text-align:center">
                                <span style="font-weight:700;color:{{ $sAvg < $kkm ? '#DC2626' : 'var(--primary)' }}">
                                    {{ number_format($sAvg, 1) }}
                                </span>
                            </td>

                            <td style="text-align:center">
                                <span style="font-weight:700;color:#16A34A">{{ $sHigh }}</span>
                            </td>

                            <td style="text-align:center">
                                <span style="font-weight:700;color:#DC2626">{{ $sLow }}</span>
                            </td>

                            <td style="text-align:center">
                                @if($sBelKkm > 0)
                                    <span class="ar-status alpha"
                                          style="font-size:11px;padding:2px 8px">
                                        {{ $sBelKkm }} siswa
                                    </span>
                                @else
                                    <span class="ar-status hadir"
                                          style="font-size:11px;padding:2px 8px">Semua lulus</span>
                                @endif
                            </td>

                            <td class="col-act">
                                <div class="dt-actions" style="opacity:1">
                                    {{-- Buka modal detail --}}
                                    <button type="button"
                                            class="dt-act"
                                            title="Lihat Detail"
                                            @click="openSession({{ $sessionJson }})">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    {{-- Link ke halaman show --}}
                                    <a href="{{ route('teacher.reports.score.show', $session->score_session_id) }}"
                                       class="dt-act" title="Buka Halaman Detail">
                                        <i class="ri-external-link-line"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="dt-footer">
                <div class="dt-info">
                    Menampilkan <strong>{{ $sessions->count() }}</strong> sesi
                    tipe <strong>{{ strtoupper($scoreType) }}</strong>
                </div>
            </div>

            @else
            <div class="ar-empty">
                <div class="ar-empty-icon"><i class="ri-calendar-2-line"></i></div>
                <div class="ar-empty-title">Belum ada sesi untuk tipe ini</div>
                <p class="ar-empty-sub">Coba ganti tipe nilai atau pilih kelas dan semester yang berbeda.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SESSION DETAIL MODAL
    ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="selectedSession !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-overlay"
         style="display:none"
         @click.self="closeSession()">

        <div class="modal-box sr-modal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             style="max-width:520px">

            {{-- Modal Header --}}
            <div class="modal-header" style="display:flex;align-items:flex-start;justify-content:space-between">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <span class="sr-type-badge"
                              x-bind:style="selectedSession
                                ? 'background:' + {harian:'#EEF2FF',uts:'#FFF7ED',uas:'#F0FDF4'}[selectedSession.type] + ';color:' + {harian:'#4338CA',uts:'#C2410C',uas:'#15803D'}[selectedSession.type]
                                : ''"
                              x-text="selectedSession ? { harian:'Harian', uts:'UTS', uas:'UAS' }[selectedSession.type] : ''">
                        </span>
                        <span style="font-size:12px;color:var(--text-muted)"
                              x-text="selectedSession ? selectedSession.date : ''"></span>
                    </div>
                    <div style="font-size:16px;font-weight:700;color:var(--text-primary)"
                         x-text="selectedSession ? selectedSession.title : ''"></div>
                </div>
                <button @click="closeSession()"
                        style="background:var(--bg);border:none;border-radius:99px;width:32px;height:32px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);flex-shrink:0">
                    <i class="ri-close-line" style="font-size:16px"></i>
                </button>
            </div>

            {{-- Modal Stats --}}
            <div style="display:flex;gap:10px;padding:14px 20px;border-bottom:1px solid var(--border)">
                <template x-if="selectedSession">
                    <template x-for="(stat, i) in [
                        { label: 'Rata-rata',    value: selectedSession.avg,       color: '#4338CA' },
                        { label: 'Tertinggi',    value: selectedSession.highest,   color: '#16A34A' },
                        { label: 'Terendah',     value: selectedSession.lowest,    color: '#DC2626' },
                        { label: 'Di Bawah KKM', value: selectedSession.below_kkm, color: '#EA580C' },
                    ]" :key="i">
                        <div style="flex:1;text-align:center;padding:8px 4px;background:var(--bg);border-radius:10px;border:1px solid var(--border)">
                            <div style="font-size:18px;font-weight:800;line-height:1"
                                 :style="'color:' + stat.color"
                                 x-text="stat.value"></div>
                            <div style="font-size:10px;color:var(--text-muted);font-weight:600;letter-spacing:.04em;margin-top:3px;text-transform:uppercase"
                                 x-text="stat.label"></div>
                        </div>
                    </template>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer" style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                <button @click="closeSession()"
                        class="ar-btn ar-btn-outline" style="height:36px;font-size:13px">
                    Tutup
                </button>
                <template x-if="selectedSession">
                    <a :href="selectedSession.detail_url"
                       class="ar-btn ar-btn-primary" style="height:36px;font-size:13px">
                        <i class="ri-external-link-line"></i>
                        Lihat Detail Lengkap
                    </a>
                </template>
            </div>

        </div>
    </div>

</div>
{{-- end x-data scoreTable --}}