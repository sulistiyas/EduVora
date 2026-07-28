@extends('layouts.app')

@section('title', 'Profil Anak')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Profil Anak</span></li>
    </ol>
</nav>
@endsection

@section('content')

@if(!$has_student || !$childData)
<div class="card" style="padding: 40px; text-align: center;">
    <p>Belum ada data anak terhubung.</p>
</div>
@else

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    {{-- FOTO & IDENTITAS UTAMA --}}
    <div class="card" style="padding: 24px; text-align: center;">
        <div style="
            width: 100px; height: 100px; border-radius: 24px;
            background: linear-gradient(135deg, #4F46E5, #818CF8);
            display: grid; place-items: center;
            font-size: 36px; font-weight: 800; color: #fff;
            margin: 0 auto 16px auto;
            box-shadow: 0 8px 24px rgba(79,70,229,.25);
        ">
            {{ strtoupper(substr($childData->student->full_name, 0, 2)) }}
        </div>
        <h3 style="font-size: 18px; font-weight: 700; margin: 0;">{{ $childData->student->full_name }}</h3>
        <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">NIS: {{ $childData->student->nis ?? '-' }}</p>
        <span class="badge badge-primary" style="margin-top: 8px;">{{ $childData->grade_name }}</span>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid var(--border-color);">

        <div style="text-align: left; font-size: 13px; display: flex; flex-direction: column; gap: 10px;">
            <div><strong>Wali Kelas:</strong> {{ $childData->homeroom_teacher }}</div>
            <div><strong>Kontak Wali:</strong> {{ $childData->homeroom_phone }}</div>
            <div><strong>Hubungan Orang Tua:</strong> {{ $childData->relationship }}</div>
        </div>
    </div>

    {{-- DETAIL DATA DIRI --}}
    <div class="card" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 20px 0;"><i class="ri-user-line"></i> Detail Data Diri Siswa</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary);">Nama Panggilan</label>
                <div style="font-size: 14px; font-weight: 600;">{{ $childData->student->nick_name ?? '-' }}</div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary);">Jenis Kelamin</label>
                <div style="font-size: 14px; font-weight: 600;">{{ $childData->student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary);">Tanggal Lahir</label>
                <div style="font-size: 14px; font-weight: 600;">{{ $childData->student->birth_date ? \Carbon\Carbon::parse($childData->student->birth_date)->translatedFormat('d M Y') : '-' }}</div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary);">Email Siswa</label>
                <div style="font-size: 14px; font-weight: 600;">{{ $childData->student->email ?? '-' }}</div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary);">Nomor Telepon</label>
                <div style="font-size: 14px; font-weight: 600;">{{ $childData->student->phone_number ?? '-' }}</div>
            </div>
            <div>
                <label style="font-size: 12px; color: var(--text-tertiary);">Kota / Kabupaten</label>
                <div style="font-size: 14px; font-weight: 600;">{{ $childData->student->city ?? '-' }}</div>
            </div>
        </div>

        <div style="margin-top: 16px;">
            <label style="font-size: 12px; color: var(--text-tertiary);">Alamat Rumah</label>
            <div style="font-size: 14px; font-weight: 500;">{{ $childData->student->address ?? '-' }}</div>
        </div>
    </div>
</div>

@endif

@endsection
