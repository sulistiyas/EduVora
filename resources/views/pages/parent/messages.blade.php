@extends('layouts.app')

@section('title', 'Pesan ke Guru')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Pesan ke Guru</span></li>
    </ol>
</nav>
@endsection

@section('content')

@if(!$has_student || !$childData)
<div class="card" style="padding: 40px; text-align: center;">
    <i class="ri-user-unfollow-line" style="font-size: 48px; color: var(--text-tertiary);"></i>
    <h3 style="margin-top: 16px;">Belum Ada Data Anak Terhubung</h3>
    <p style="color: var(--text-secondary); margin-top: 8px;">Akun Anda belum terhubung dengan data siswa di sekolah ini.</p>
</div>
@else

<div class="card" style="padding: 24px; margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 700; margin: 0;">Kontak Pengajar & Wali Kelas</h2>
    <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Daftar guru pengajar dan wali kelas yang dapat Anda hubungi untuk konsultasi mengenai anak Anda.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
    @if(empty($teachers))
        <div class="card" style="padding: 30px; text-align: center; grid-column: 1/-1;">
            <p style="color: var(--text-secondary);">Belum ada data guru pengajar terdaftar di kelas anak Anda.</p>
        </div>
    @else
        @foreach($teachers as $t)
        @php
            $guruObj = (object) $t;
        @endphp
        <div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 12px;">
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #4F46E5, #6366F1); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 18px;">
                        {{ strtoupper(substr($guruObj->nama_guru ?? 'G', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 15px;">{{ $guruObj->nama_guru ?? '-' }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">Pengajar: <strong>{{ $guruObj->mapel ?? '-' }}</strong></div>
                    </div>
                </div>
                <div style="font-size: 12px; color: var(--text-tertiary); display: flex; flex-direction: column; gap: 4px;">
                    <span><i class="ri-mail-line"></i> {{ $guruObj->email ?? 'Tidak ada email' }}</span>
                    <span><i class="ri-phone-line"></i> {{ $guruObj->hp ?? 'Tidak ada telepon' }}</span>
                </div>
            </div>

            <div style="margin-top: 16px; display: flex; gap: 8px;">
                @if(!empty($guruObj->hp))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guruObj->hp) }}" target="_blank" class="btn btn-success" style="flex:1; justify-content: center; font-size: 12px;">
                    <i class="ri-whatsapp-line"></i> WhatsApp
                </a>
                @endif
                <a href="mailto:{{ $guruObj->email ?? '' }}" class="btn btn-secondary" style="flex:1; justify-content: center; font-size: 12px;">
                    <i class="ri-mail-line"></i> Email
                </a>
            </div>
        </div>
        @endforeach
    @endif
</div>

@endif

@endsection
