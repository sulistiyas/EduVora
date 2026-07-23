@extends('layouts.app')

@section('title', 'Pengaturan Platform')

@section('content')

<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
    <div>
        <ul class="breadcrumb-list">
            <li>
                <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                    <i class="ri-home-4-line"></i> Dashboard
                </a>
            </li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li>Platform</li>
            <li><i class="ri-arrow-right-s-line"></i></li>
            <li><span>Pengaturan</span></li>
        </ul>
        <h2 style="font-size:22px;font-weight:700;color:var(--text-primary);letter-spacing:-.5px;line-height:1.2;margin-top:4px">
            Pengaturan Platform
        </h2>
        <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
            Konfigurasi sistem EduVora secara global
        </p>
    </div>
    <button @click="saveAll()" class="dt-btn dt-btn-primary" :disabled="saving">
        <i class="ri-save-line" x-show="!saving"></i>
        <i class="ri-loader-4-line" x-show="saving" style="animation:spin 1s linear infinite"></i>
        <span x-text="saving ? 'Menyimpan...' : 'Simpan Semua'"></span>
    </button>
</div>

<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .setting-group { margin-bottom: 24px; }
    .setting-group-title {
        font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: var(--text-muted); margin-bottom: 12px;
        padding-bottom: 8px; border-bottom: 1px solid var(--border);
    }
    .setting-row {
        display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
        padding: 14px 0; border-bottom: 1px solid var(--border);
        align-items: start;
    }
    .setting-row:last-child { border-bottom: none; }
    .setting-label { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }
    .setting-desc { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }
    .setting-input { width: 100%; }
    .setting-input input[type="text"],
    .setting-input input[type="email"],
    .setting-input input[type="number"],
    .setting-input textarea,
    .setting-input select {
        width: 100%; padding: 8px 12px; border: 1px solid var(--border);
        border-radius: 8px; font-size: 0.85rem; background: var(--bg);
        color: var(--text-primary); transition: border-color 0.2s;
    }
    .setting-input input:focus,
    .setting-input textarea:focus,
    .setting-input select:focus {
        outline: none; border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    }
    .setting-input textarea { min-height: 80px; resize: vertical; }
    .toggle-switch {
        position: relative; width: 44px; height: 24px; display: inline-block;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0; background: #CBD5E1; border-radius: 99px;
        cursor: pointer; transition: background 0.3s;
    }
    .toggle-slider::before {
        content: ''; position: absolute; width: 18px; height: 18px;
        border-radius: 50%; background: #fff; top: 3px; left: 3px;
        transition: transform 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,.15);
    }
    .toggle-switch input:checked + .toggle-slider { background: var(--primary); }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }
</style>

<div x-data="settingsPage()" x-init="init()">

    @foreach($grouped as $group => $items)
        @php
            $groupLabels = [
                'general'       => ['icon' => 'ri-settings-3-line',  'label' => 'Umum',           'color' => '#3B82F6'],
                'access'        => ['icon' => 'ri-shield-keyhole-line', 'label' => 'Akses & Keamanan', 'color' => '#EF4444'],
                'academic'      => ['icon' => 'ri-book-open-line',   'label' => 'Akademik',        'color' => '#F59E0B'],
                'notification'  => ['icon' => 'ri-notification-3-line', 'label' => 'Notifikasi',    'color' => '#10B981'],
            ];
            $g = $groupLabels[$group] ?? ['icon' => 'ri-tools-line', 'label' => ucfirst($group), 'color' => '#6B7280'];
        @endphp
        <div class="card setting-group">
            <div class="card-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:8px;background:{{ $g['color'] }}15;color:{{ $g['color'] }};display:grid;place-items:center;font-size:16px;">
                        <i class="{{ $g['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="card-title" style="margin:0;">{{ $g['label'] }}</div>
                        <div class="card-subtitle" style="margin:0;">{{ count($items) }} pengaturan</div>
                    </div>
                </div>
            </div>
            <div style="padding:4px 20px 12px;">
                @foreach($items as $item)
                    <div class="setting-row">
                        <div>
                            <div class="setting-label">{{ $item->label }}</div>
                            <div class="setting-desc">{{ $item->description }}</div>
                        </div>
                        <div class="setting-input">
                            @if($item->type === 'boolean')
                                <label class="toggle-switch">
                                    <input type="checkbox"
                                        :checked="formData['{{ $item->key }}'] === 'true'"
                                        @change="formData['{{ $item->key }}'] = $event.target.checked ? 'true' : 'false'">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span style="font-size:0.78rem;color:var(--text-muted);margin-left:8px;"
                                      x-text="formData['{{ $item->key }}'] === 'true' ? 'Aktif' : 'Nonaktif'"></span>

                            @elseif($item->type === 'textarea')
                                <textarea x-model="formData['{{ $item->key }}']"
                                    placeholder="{{ $item->label }}"></textarea>

                            @elseif($item->type === 'select')
                                <select x-model="formData['{{ $item->key }}']">
                                    <option value="SMA/SMK">SMA/SMK</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SD">SD</option>
                                </select>

                            @elseif($item->type === 'email')
                                <input type="email" x-model="formData['{{ $item->key }}']"
                                    placeholder="{{ $item->label }}">

                            @elseif($item->type === 'number')
                                <input type="number" x-model="formData['{{ $item->key }}']"
                                    placeholder="{{ $item->label }}">

                            @else
                                <input type="text" x-model="formData['{{ $item->key }}']"
                                    placeholder="{{ $item->label }}">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</div>

@endsection

@push('scripts')
<script>
function settingsPage() {
    return {
        formData: {},
        saving: false,

        init() {
            @foreach($grouped as $items)
                @foreach($items as $item)
                    @if($item->type === 'image')
                        this.formData['{{ $item->key }}'] = '{{ $item->value }}';
                    @else
                        this.formData['{{ $item->key }}'] = '{{ addslashes($item->value ?? '') }}';
                    @endif
                @endforeach
            @endforeach
        },

        async saveAll() {
            this.saving = true;
            try {
                const res = await fetch('{{ route("platform-settings.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ settings: this.formData }),
                });

                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Gagal menyimpan pengaturan.', 'error');
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endpush
