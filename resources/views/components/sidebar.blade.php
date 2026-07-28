<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
            </svg>
        </div>
        <div class="sidebar-logo-text">
            <div class="sidebar-logo-name">EduSaaS</div>
            <div class="sidebar-logo-sub">School Management</div>
        </div>
    </div>

    @php $role = auth()->user()->role_name; @endphp
    <nav class="sidebar-nav">

        <div class="nav-section-label" style="margin-top:8px;">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
            class="nav-item {{ request()->routeIs(
                '*.dashboard',
                'dashboard'
            ) ? 'active' : '' }}">
                <i class="ri-dashboard-3-line"></i>
                Dashboard
        </a>

        {{-- ══════════════════════════════════
            SUPER-ADMIN
            Platform level — tidak ada school context
        ══════════════════════════════════ --}}
        @if ($role === 'super-admin')

            <div class="nav-section-label" style="margin-top:8px;">Platform Management</div>

            <a href="{{ route('school-management.index') }}"
            class="nav-item {{ request()->routeIs('school-management.*') ? 'active' : '' }}">
                <i class="ri-community-line"></i>
                School Management
            </a>

            <a href="{{ route('users.index') }}"
            class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="ri-user-line"></i>
                User Management
            </a>

            <a href="{{ route('roles.index') }}"
            class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="ri-shield-line"></i>
                Role & Permission
            </a>

            <a href="{{ route('audit-logs.index') }}"
            class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                <i class="ri-file-list-3-line"></i>
                Audit Logs
            </a>

            <a href="{{ route('platform-settings.index') }}"
            class="nav-item {{ request()->routeIs('platform-settings.*') ? 'active' : '' }}">
                <i class="ri-settings-3-line"></i>
                Pengaturan Platform
            </a>

        @endif

        {{-- ══════════════════════════════════
            SCHOOL-ADMIN
            Scoped ke sekolah milik user
        ══════════════════════════════════ --}}
        @if ($role === 'school-admin')

            <div class="nav-section-label">Sekolah Aktif</div>
            <div class="sidebar-school-badge">
                <i class="ri-building-4-line"></i>
                <span>{{ auth()->user()->schools->first()->school_name ?? '-' }}</span>
            </div>

            <div class="nav-section-label" style="margin-top:8px;">Data Sekolah</div>

            <a href="{{ route('school-admin.students.index') }}"
            class="nav-item {{ request()->routeIs('school-admin.students.*') ? 'active' : '' }}">
                <i class="ri-user-3-line"></i>
                Siswa
                <span class="nav-badge">1.2k</span>
            </a>

            <a href="{{ route('school-admin.teachers.index') }}"
            class="nav-item {{ request()->routeIs('school-admin.teachers.*') ? 'active' : '' }}">
                <i class="ri-team-line"></i>
                Guru & Staff
            </a>

            <a href="{{ route('schedules.index') }}"
            class="nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                <i class="ri-calendar-schedule-line"></i>
                Jadwal
            </a>

            <a href="{{ route('school-admin.attendance.index') }}" class="nav-item {{ request()->routeIs('school-admin.attendance.*') ? 'active' : '' }}">
                <i class="ri-checkbox-circle-line"></i>
                Presensi
            </a>

            <a href="{{ route('student-scores.index') }}" class="nav-item {{ request()->routeIs('student-scores.*') ? 'active' : '' }}">
                <i class="ri-bar-chart-2-line"></i>
                Nilai
            </a>

            <div class="nav-section-label" style="margin-top:8px;">Akademik</div>

            {{-- Tahun Ajaran (collapsible) --}}
            <div class="nav-item-group {{ request()->routeIs('academic-year.*', 'semesters.*', 'academic-dates.*') ? 'open' : '' }}">
                <a href="#"
                class="nav-item nav-item--has-children {{ request()->routeIs('academic-year.*', 'semesters.*', 'academic-dates.*') ? 'active' : '' }}"
                onclick="toggleNavGroup(this); return false;">
                    <i class="ri-calendar-2-line"></i>
                    Tahun Ajaran
                    <i class="ri-arrow-down-s-line nav-arrow" style="margin-left:auto;"></i>
                </a>
                <div class="nav-sub-menu">
                    <a href="{{ route('academic-year.index') }}"
                    class="nav-item nav-item--sub {{ request()->routeIs('academic-year.*') ? 'active' : '' }}">
                        <i class="ri-calendar-check-line"></i>
                        Daftar Tahun Ajaran
                    </a>
                    <a href="{{ route('semesters.index') }}"
                    class="nav-item nav-item--sub {{ request()->routeIs('semesters.*') ? 'active' : '' }}">
                        <i class="ri-split-cells-horizontal"></i>
                        Semester
                    </a>
                    <a href="{{ route('academic-dates.index') }}"
                    class="nav-item nav-item--sub {{ request()->routeIs('academic-dates.*') ? 'active' : '' }}">
                        <i class="ri-calendar-event-line"></i>
                        Tanggal Penting
                    </a>
                </div>
            </div>

            {{-- Struktur Akademik (collapsible) --}}
            <div class="nav-item-group {{ request()->routeIs('subjects.*', 'rooms.*', 'grades.*') ? 'open' : '' }}">
                <a href="#"
                class="nav-item nav-item--has-children {{ request()->routeIs('subjects.*', 'rooms.*', 'grades.*') ? 'active' : '' }}"
                onclick="toggleNavGroup(this); return false;">
                    <i class="ri-layout-grid-line"></i>
                    Struktur Akademik
                    <i class="ri-arrow-down-s-line nav-arrow" style="margin-left:auto;"></i>
                </a>
                <div class="nav-sub-menu">
                    <a href="{{ route('subjects.index') }}"
                    class="nav-item nav-item--sub {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                        <i class="ri-book-open-line"></i>
                        Mata Pelajaran
                    </a>
                    <a href="{{ route('rooms.index') }}"
                    class="nav-item nav-item--sub {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                        <i class="ri-door-open-line"></i>
                        Ruangan
                    </a>
                    <a href="{{ route('grades.index') }}"
                    class="nav-item nav-item--sub {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                        <i class="ri-school-line"></i>
                        Kelas
                    </a>
                </div>
            </div>

            <div class="nav-section-label" style="margin-top:8px;">Keuangan</div>

            <a href="#" class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                <i class="ri-money-dollar-circle-line"></i>
                Pembayaran SPP
            </a>

            <a href="#" class="nav-item {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                <i class="ri-wallet-3-line"></i>
                Penggajian
            </a>

        @endif

        {{-- ══════════════════════════════════
            TEACHER
        ══════════════════════════════════ --}}
        @if ($role === 'teacher')

            <div class="nav-section-label">Sekolah Aktif</div>
            <div class="sidebar-school-badge">
                <i class="ri-building-4-line"></i>
                <span>{{ auth()->user()->schools->first()->school_name ?? '-' }}</span>
            </div>

            <div class="nav-section-label" style="margin-top:8px;">Mengajar</div>

            <a href="{{ route('teacher.schedules.index') }}" class="nav-item {{ request()->routeIs('teacher.schedules.*') ? 'active' : '' }}">
                <i class="ri-calendar-schedule-line"></i>
                Jadwal Saya
            </a>

            <a href="{{ route('teacher.attendance.index') }}" class="nav-item {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
                <i class="ri-checkbox-circle-line"></i>
                Presensi
            </a>

            <a href="{{ route('teacher.scores.index') }}" class="nav-item {{ request()->routeIs('teacher.scores.*') ? 'active' : '' }}">
                <i class="ri-bar-chart-2-line"></i>
                Input Nilai
            </a>

            <a href="{{ route('teacher.assignments.index') }}" 
               class="nav-item {{ request()->routeIs('teacher.assignments.*') ? 'active' : '' }}">
                <i class="ri-file-list-2-line"></i>
                Tugas
            </a>

            <div class="nav-section-label" style="margin-top:8px;">Reports</div>

            <a href="{{ route('teacher.reports.attendance.index') }}"
            class="nav-item {{ request()->routeIs('teacher.reports.attendance.*') ? 'active' : '' }}">
                <i class="ri-file-chart-line"></i>
                Attendance Reports
            </a>

            <a href="{{ route('teacher.reports.score.index') }}"
            class="nav-item {{ request()->routeIs('teacher.reports.score.*') ? 'active' : '' }}">
                <i class="ri-bar-chart-box-line"></i>
                Score Reports
            </a>

            <a href="#"
            class="nav-item {{ request()->routeIs('teacher.reports.student-progress.*') ? 'active' : '' }}">
                <i class="ri-user-star-line"></i>
                Student Progress
            </a>

            <a href="#"
            class="nav-item {{ request()->routeIs('teacher.reports.class-statistics.*') ? 'active' : '' }}">
                <i class="ri-pie-chart-2-line"></i>
                Class Statistics
            </a>

            <a href="#"
            class="nav-item {{ request()->routeIs('teacher.reports.learning-completion.*') ? 'active' : '' }}">
                <i class="ri-checkbox-multiple-line"></i>
                Learning Completion
            </a>

            <a href="#"
            class="nav-item {{ request()->routeIs('teacher.reports.export.*') ? 'active' : '' }}">
                <i class="ri-download-2-line"></i>
                Export Reports
            </a>

            

        @endif

        {{-- ══════════════════════════════════
            STUDENT
        ══════════════════════════════════ --}}
        @if ($role === 'student')

            <div class="nav-section-label">Sekolah Aktif</div>
            <div class="sidebar-school-badge">
                <i class="ri-building-4-line"></i>
                <span>{{ auth()->user()->schools->first()->school_name ?? '-' }}</span>
            </div>

            <div class="nav-section-label" style="margin-top:8px;">Akademik Saya</div>

            <a href="{{ route('student.schedule') }}" class="nav-item {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
                <i class="ri-calendar-schedule-line"></i>
                Jadwal Pelajaran
            </a>

            <a href="{{ route('student.attendance') }}" class="nav-item {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
                <i class="ri-checkbox-circle-line"></i>
                Presensi Saya
            </a>

            <a href="{{ route('student.scores') }}" class="nav-item {{ request()->routeIs('student.scores') ? 'active' : '' }}">
                <i class="ri-bar-chart-2-line"></i>
                Nilai Saya
            </a>

            <a href="{{ route('student.assignments.index') }}" 
               class="nav-item {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                <i class="ri-file-list-2-line"></i>
                Tugas
            </a>

        @endif

        {{-- ══════════════════════════════════
            STUDENT-PARENT
        ══════════════════════════════════ --}}
        @if ($role === 'student-parent')

            <div class="nav-section-label">Sekolah Aktif</div>
            <div class="sidebar-school-badge">
                <i class="ri-building-4-line"></i>
                <span>{{ auth()->user()->schools->first()->school_name ?? '-' }}</span>
            </div>

            <div class="nav-section-label" style="margin-top:8px;">Pantau Anak</div>

            <a href="{{ route('parent.attendance') }}" class="nav-item {{ request()->routeIs('parent.attendance') ? 'active' : '' }}">
                <i class="ri-checkbox-circle-line"></i>
                Presensi Anak
            </a>

            <a href="{{ route('parent.scores') }}" class="nav-item {{ request()->routeIs('parent.scores') ? 'active' : '' }}">
                <i class="ri-bar-chart-2-line"></i>
                Nilai Anak
            </a>

            <a href="{{ route('parent.fees') }}" class="nav-item {{ request()->routeIs('parent.fees') ? 'active' : '' }}">
                <i class="ri-money-dollar-circle-line"></i>
                Pembayaran SPP
            </a>

            <a href="{{ route('parent.reports') }}" class="nav-item {{ request()->routeIs('parent.reports') ? 'active' : '' }}">
                <i class="ri-file-chart-line"></i>
                Rapor Anak
            </a>

            <a href="{{ route('parent.profile') }}" class="nav-item {{ request()->routeIs('parent.profile') ? 'active' : '' }}">
                <i class="ri-user-smile-line"></i>
                Profil Anak
            </a>

            <a href="{{ route('parent.messages') }}" class="nav-item {{ request()->routeIs('parent.messages') ? 'active' : '' }}">
                <i class="ri-chat-3-line"></i>
                Pesan ke Guru
            </a>

            <a href="{{ route('parent.academic-history') }}" class="nav-item {{ request()->routeIs('parent.academic-history') ? 'active' : '' }}">
                <i class="ri-history-line"></i>
                Histori Akademik
            </a>

        @endif

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="sidebar-user-role">{{ ucfirst(str_replace('-', ' ', $role ?? 'User')) }}</div>
            </div>
            <i class="ri-more-2-fill" style="color:rgba(255,255,255,.3);font-size:16px;"></i>
        </div>
    </div>

</aside>

{{-- =============================================
     CSS tambahan untuk nav collapsible group
     Tambahkan ke file CSS/layout Anda
============================================== --}}

<style>
.nav-item-group .nav-sub-menu {
    display: none;
    padding-left: 12px;
}
.nav-item-group.open .nav-sub-menu {
    display: block;
}
.nav-item-group.open .nav-arrow {
    transform: rotate(180deg);
}
.nav-arrow {
    transition: transform 0.2s ease;
}
.nav-item--sub {
    font-size: 0.8rem;
    padding-top: 6px;
    padding-bottom: 6px;
}
</style>


{{-- =============================================
     JS untuk toggle collapsible group
     Tambahkan ke file JS/layout Anda
============================================== --}}

{{-- <script>
    function toggleNavGroup(el) {
        const group = el.closest('.nav-item-group');
        group.classList.toggle('open');
    }
</script> --}}
<script>
function toggleNavGroup(el) {
    const currentGroup = el.closest('.nav-item-group');

    // Tutup semua group lain (optional accordion behavior)
    document.querySelectorAll('.nav-item-group').forEach(group => {
        if (group !== currentGroup) {
            group.classList.remove('open');
        }
    });

    // Toggle current group
    currentGroup.classList.toggle('open');
}
</script>