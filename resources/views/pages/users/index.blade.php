@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumb')
    <li class="flex items-center">
        <i class="ri-arrow-right-s-line"></i>
        <span class="text-gray-900 font-medium">User Management</span>
    </li>
@endsection

@section('content')

    

@endsection

@push('scripts')
<script>
    // Animasi role bars saat halaman load
    document.addEventListener('DOMContentLoaded', function () {
        const bars = document.querySelectorAll('.role-bar-fill');
        bars.forEach((bar, i) => {
            const target = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.transition = 'width .8s cubic-bezier(.4,0,.2,1)';
                bar.style.width = target;
            }, 300 + i * 100);
        });
    });
</script>
@endpush