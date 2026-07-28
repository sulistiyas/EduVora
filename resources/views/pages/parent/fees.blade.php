@extends('layouts.app')

@section('title', 'Pembayaran SPP & Tagihan')

@section('breadcrumb')
<nav class="breadcrumb-nav">
    <ol class="breadcrumb-list">
        <li>
            <a href="{{ route('dashboard') }}" class="breadcrumb-home">
                <i class="ri-home-4-line"></i> Beranda
            </a>
        </li>
        <li><i class="ri-arrow-right-s-line"></i><span>Pembayaran SPP</span></li>
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

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 20px;">
        <div style="font-size: 13px; color: var(--text-secondary);">Total Tagihan</div>
        <div style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-top: 4px;">Rp {{ number_format($summary['total_tagihan'] ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="card" style="padding: 20px;">
        <div style="font-size: 13px; color: var(--text-secondary);">Total Terbayar</div>
        <div style="font-size: 24px; font-weight: 700; color: #10B981; margin-top: 4px;">Rp {{ number_format($summary['total_terbayar'] ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="card" style="padding: 20px;">
        <div style="font-size: 13px; color: var(--text-secondary);">Sisa Tagihan</div>
        <div style="font-size: 24px; font-weight: 700; color: #F59E0B; margin-top: 4px;">Rp {{ number_format($summary['sisa_tagihan'] ?? 0, 0, ',', '.') }}</div>
    </div>
</div>

<div class="card" style="padding: 24px;">
    <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 16px 0;"><i class="ri-wallet-3-line"></i> Daftar Tagihan & Histori Pembayaran</h3>

    <div class="table-responsive">
        <table class="table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Tagihan / Deskripsi</th>
                    <th>Kategori</th>
                    <th>Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td style="font-weight: 600;">{{ $inv['title'] }}</td>
                    <td><span class="badge badge-secondary">{{ $inv['category'] }}</span></td>
                    <td style="font-size: 13px;">{{ \Carbon\Carbon::parse($inv['due_date'])->translatedFormat('d M Y') }}</td>
                    <td style="font-weight: 700;">Rp {{ number_format($inv['amount'], 0, ',', '.') }}</td>
                    <td>
                        @if($inv['status'] === 'Lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-warning">Belum Dibayar</span>
                        @endif
                    </td>
                    <td style="font-size: 13px; color: var(--text-secondary);">{{ $inv['paid_at'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

@endsection
