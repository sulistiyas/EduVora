<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Teacher;
use App\Models\Academic\Schedule;
use App\Models\Academic\GradeSubject;
use App\Models\Academic\Grade;
use App\Models\Student\Student;
use App\Models\Academic\Subject;
use App\Models\Academic\Room;
use App\Models\Academic\Semester;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ─────────────────────────────────────────────────────────────────
        // 1. SCHOOL ID (SaaS multi-school: ambil school aktif si user)
        // ─────────────────────────────────────────────────────────────────
        $schoolId = DB::table('user_has_schools')
            ->where('user_id', $user->id)
            ->value('school_id');

        // ─────────────────────────────────────────────────────────────────
        // 2. TEACHER PROFILE
        // ─────────────────────────────────────────────────────────────────
        $teacherRecord = DB::table('teachers')
            ->where('user_id', $user->id)
            ->first();

        $teacher = (object) [
            'name'     => $teacherRecord?->full_name  ?? $user->name,
            'gender'   => $teacherRecord?->gender     ?? 'male',
            'nip'      => $teacherRecord?->nip        ?? '-',
            'photo'    => $user->profile_picture      ?? null,
            'semester' => 'Ganjil 2024/2025',   // bisa ambil dari semester aktif
            'mapel'    => [],                    // diisi dari query di bawah
        ];

        $teacherId = $teacherRecord?->teacher_id;

        // ─────────────────────────────────────────────────────────────────
        // 3. SEMESTER AKTIF
        // ─────────────────────────────────────────────────────────────────
        $activeSemester = DB::table('semesters')
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        if ($activeSemester) {
            $teacher->semester = $activeSemester->semester_name;
        }

        // ─────────────────────────────────────────────────────────────────
        // 4. MATA PELAJARAN yang diajar guru ini (di sekolah ini)
        //    grade_subjects → subjects
        // ─────────────────────────────────────────────────────────────────
        $mapelList = DB::table('grade_subjects as gs')
            ->join('subjects as s', 's.id', '=', 'gs.subject_id')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->distinct()
            ->pluck('s.subject_name')
            ->toArray();

        $teacher->mapel = $mapelList ?: ['—'];

        // ─────────────────────────────────────────────────────────────────
        // 5. GRADE-SUBJECT IDs milik guru ini (scope sekolah)
        // ─────────────────────────────────────────────────────────────────
        $gradeSubjectIds = DB::table('grade_subjects as gs')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->pluck('gs.id')  // grade_subjects.id (PK)
            ->toArray();

        // Grade IDs yang diajar guru
        $gradeIds = DB::table('grade_subjects as gs')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->distinct()
            ->pluck('gs.grade_id')
            ->toArray();

        // ─────────────────────────────────────────────────────────────────
        // 6. JADWAL HARI INI
        //    schedules → grade_subjects → subjects, grades, rooms
        // ─────────────────────────────────────────────────────────────────
        $today         = Carbon::now();
        $dayOfWeek     = strtolower($today->locale('en')->dayName); // 'monday', 'tuesday', dst.
        // Jika kolom day_of_week memakai format angka (1=Senin … 7=Minggu):
        $dayOfWeekNum  = $today->dayOfWeekIso; // 1=Mon … 7=Sun

        $todaySchedulesRaw = DB::table('schedules as sc')
            ->join('grade_subjects as gs',  'gs.id',        '=', 'sc.grade_subject_id')
            ->join('subjects as sub',       'sub.id',       '=', 'gs.subject_id')
            ->join('grades as g',           'g.grade_id',   '=', 'gs.grade_id')
            ->join('rooms as r',            'r.room_id',    '=', 'sc.room_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->where('sc.semester_id', $semesterId)
            ->where('sc.status', 'active')
            // Sesuaikan kondisi day_of_week dengan format di DB Anda:
            // ->where('sc.day_of_week', $dayOfWeek)       // jika string
            ->where('sc.day_of_week', $dayOfWeekNum)       // jika integer ISO
            ->orderBy('sc.start_time')
            ->select([
                'sc.schedule_id',
                'sc.start_time',
                'sc.end_time',
                'sc.session_type',
                'g.grade_name  as kelas',
                'sub.subject_name as mapel',
                'r.room_name   as ruangan',
                'r.code        as ruangan_kode',
            ])
            ->get();

        // Map ke format yang dipakai view
        // status_absensi & status_jurnal: tambahkan join ke tabel attendance/journal Anda
        $todaySchedules = $todaySchedulesRaw->map(function ($s) {
            return [
                'schedule_id'     => $s->schedule_id,
                'jam'             => substr($s->start_time, 0, 5) . '–' . substr($s->end_time, 0, 5),
                'kelas'           => $s->kelas,
                'mapel'           => $s->mapel,
                'ruangan'         => $s->ruangan_kode ?: $s->ruangan,
                // TODO: join ke tabel attendance & journal untuk status nyata
                'status_absensi'  => 'belum',
                'status_jurnal'   => 'belum',
            ];
        })->toArray();

        // ─────────────────────────────────────────────────────────────────
        // 7. STATISTICS
        // ─────────────────────────────────────────────────────────────────

        // 7a. Total kelas unik yang diajar guru (semester aktif)
        $totalKelas = DB::table('schedules as sc')
            ->join('grade_subjects as gs', 'gs.id', '=', 'sc.grade_subject_id')
            ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            ->where('gs.teacher_id', $teacherId)
            ->where('g.school_id', $schoolId)
            ->where('sc.semester_id', $semesterId)
            ->where('sc.status', 'active')
            ->distinct('gs.grade_id')
            ->count('gs.grade_id');

        // 7b. Total siswa yang diajar guru (dari grades yang diajar, bukan seluruh sekolah)
        //     Asumsi: students.grade_id merujuk ke grades.grade_id
        $totalSiswa = DB::table('students')
            ->whereIn('grade_id', $gradeIds)
            ->where('status', 'active')
            ->count();

        // 7c. Jadwal hari ini
        $jadwalHariIni = count($todaySchedules);

        // 7d. Tugas belum dinilai
        //     TODO: sesuaikan dengan tabel assignments & submissions di aplikasi Anda
        $tugasBelumDinilai = 0; // Assignment::where('teacher_id', $teacherId)->withPendingSubmissions()->count()

        // 7e. Absensi belum diisi (jadwal hari ini yang belum ada record attendance)
        //     TODO: sesuaikan dengan tabel attendances di aplikasi Anda
        $absensiBelumDiisi = 0; // hitung dari $todaySchedules yang status_absensi == 'belum'

        $statistics = [
            'total_kelas'          => $totalKelas,
            'total_siswa'          => $totalSiswa,
            'jadwal_hari_ini'      => $jadwalHariIni,
            'tugas_belum_dinilai'  => $tugasBelumDinilai,
            'absensi_belum_diisi'  => $absensiBelumDiisi,
        ];

        // ─────────────────────────────────────────────────────────────────
        // 8. PENDING ASSIGNMENTS (masih dummy — sesuaikan dengan model Anda)
        // ─────────────────────────────────────────────────────────────────
        $pendingAssignments = [
            // Contoh query jika ada tabel assignments & assignment_submissions:
            // DB::table('assignments as a')
            //     ->join('assignment_submissions as sub', 'sub.assignment_id', '=', 'a.id')
            //     ->join('grade_subjects as gs', 'gs.id', '=', 'a.grade_subject_id')
            //     ->join('grades as g', 'g.grade_id', '=', 'gs.grade_id')
            //     ->where('gs.teacher_id', $teacherId)
            //     ->where('g.school_id', $schoolId)
            //     ->whereNull('sub.grade')
            //     ->selectRaw('a.id, a.title as nama, g.grade_name as kelas, a.due_date as deadline, COUNT(sub.id) as pending')
            //     ->groupBy('a.id','a.title','g.grade_name','a.due_date')
            //     ->having('pending', '>', 0)
            //     ->get()
            //     ->map(fn($r) => [
            //         'nama'     => $r->nama,
            //         'kelas'    => $r->kelas,
            //         'deadline' => Carbon::parse($r->deadline)->translatedFormat('d M Y'),
            //         'pending'  => $r->pending,
            //     ])->toArray()
        ];

        // ─────────────────────────────────────────────────────────────────
        // 9. REKAP KEHADIRAN HARI INI (semua kelas yang diajar)
        //    TODO: sesuaikan dengan tabel attendances di aplikasi Anda
        // ─────────────────────────────────────────────────────────────────
        $attendanceSummary = [
            // Contoh:
            // DB::table('attendances')
            //     ->whereIn('schedule_id', collect($todaySchedules)->pluck('schedule_id'))
            //     ->selectRaw("
            //         SUM(CASE WHEN status='hadir'  THEN 1 ELSE 0 END) as hadir,
            //         SUM(CASE WHEN status='izin'   THEN 1 ELSE 0 END) as izin,
            //         SUM(CASE WHEN status='sakit'  THEN 1 ELSE 0 END) as sakit,
            //         SUM(CASE WHEN status='alpha'  THEN 1 ELSE 0 END) as alpha,
            //         COUNT(*) as total
            //     ")
            //     ->first()
            'hadir' => 0,
            'izin'  => 0,
            'sakit' => 0,
            'alpha' => 0,
            'total' => $totalSiswa ?: 1, // hindari division by zero di view
        ];

        // ─────────────────────────────────────────────────────────────────
        // 10. PENGUMUMAN SEKOLAH
        //     TODO: sesuaikan dengan tabel announcements di aplikasi Anda
        // ─────────────────────────────────────────────────────────────────
        $announcements = [
            // DB::table('announcements')
            //     ->where('school_id', $schoolId)
            //     ->where('target', 'guru')   // atau semua role
            //     ->where('status', 'active')
            //     ->orderByDesc('published_at')
            //     ->take(3)
            //     ->get()
            //     ->map(fn($a) => [
            //         'judul'   => $a->title,
            //         'tanggal' => Carbon::parse($a->published_at)->translatedFormat('d M Y'),
            //         'isi'     => $a->content,
            //     ])->toArray()
        ];

        // ─────────────────────────────────────────────────────────────────
        // 11. KALENDER AKADEMIK
        //     TODO: sesuaikan dengan tabel academic_calendars di aplikasi Anda
        // ─────────────────────────────────────────────────────────────────
        $academicEvents = [
            // DB::table('academic_calendars')
            //     ->where('school_id', $schoolId)
            //     ->where('date', '>=', today())
            //     ->orderBy('date')
            //     ->take(5)
            //     ->get()
            //     ->map(fn($e) => [
            //         'tanggal' => Carbon::parse($e->date)->translatedFormat('d M'),
            //         'label'   => $e->title,
            //         'tipe'    => $e->type, // 'deadline'|'ujian'|'agenda'|'libur'
            //     ])->toArray()
        ];

        // ─────────────────────────────────────────────────────────────────
        // 12. AKTIVITAS TERBARU
        //     TODO: sesuaikan dengan tabel activity_logs di aplikasi Anda
        //     (bisa pakai spatie/laravel-activitylog)
        // ─────────────────────────────────────────────────────────────────
        $recentActivities = [
            // DB::table('activity_log')
            //     ->where('causer_id', $user->id)
            //     ->where('causer_type', 'App\Models\User')
            //     ->latest()
            //     ->take(5)
            //     ->get()
            //     ->map(fn($a) => [
            //         'icon'  => $a->properties['icon']  ?? 'ri-information-line',
            //         'color' => $a->properties['color'] ?? 'blue',
            //         'teks'  => $a->description,
            //         'waktu' => Carbon::parse($a->created_at)->diffForHumans(),
            //     ])->toArray()
        ];

        // dd([
        //     'school_id' => $schoolId,
        //     'teacher_id' => $teacherId,
        //     'semester_id' => $semesterId,
        //     'grade_subject_ids' => $gradeSubjectIds,
        //     'grade_ids' => $gradeIds,
        //     'todaySchedulesRaw' => $todaySchedulesRaw,
        //     'statistics' => $statistics,
        // ]);

        // ─────────────────────────────────────────────────────────────────
        // RETURN VIEW
        // ─────────────────────────────────────────────────────────────────
        return view('pages.dash.teacher_dash', compact(
            'teacher',
            'statistics',
            'todaySchedules',
            'pendingAssignments',
            'attendanceSummary',
            'announcements',
            'academicEvents',
            'recentActivities'
        ));
    }
}