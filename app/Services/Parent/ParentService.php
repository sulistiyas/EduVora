<?php

namespace App\Services\Parent;

use App\Repositories\Parent\ParentRepository;
use Illuminate\Support\Facades\DB;

class ParentService
{
    public function __construct(
        protected ParentRepository $parentRepository
    ) {}

    /**
     * Get parent's linked student profile.
     */
    public function getChildData(int $userId): ?object
    {
        return $this->parentRepository->getChildDataByUserId($userId);
    }

    /**
     * Dashboard Summary for Parent
     */
    public function getDashboardSummary(int $userId): array
    {
        $childData = $this->getChildData($userId);

        if (! $childData) {
            return [
                'has_student' => false,
                'childData' => null,
            ];
        }

        $studentId = $childData->student_id;
        $gradeId = $childData->grade_id;
        $schoolId = $childData->school_id;
        $semesterId = $childData->active_semester?->semester_id;

        $attendanceSummary = $this->parentRepository->getAttendanceSummary($studentId, $semesterId);
        $todaySchedules = $this->parentRepository->getTodaySchedules($gradeId, $semesterId);
        $nilaiPerMapel = $this->parentRepository->getNilaiPerMapel($studentId, $gradeId, $semesterId);

        $rataRataNilai = count($nilaiPerMapel) > 0
            ? round(collect($nilaiPerMapel)->avg('avg'), 1)
            : 0;

        $feeSummary = [
            'total_tagihan' => 1500000,
            'terbayar' => 1000000,
            'sisa_tagihan' => 500000,
            'status' => 'Belum Lunas',
            'status_badge' => 'warning',
        ];

        $announcements = DB::table('academic_dates')
            ->where('school_id', $schoolId)
            ->where('start_date', '>=', now()->subDays(30))
            ->orderBy('start_date')
            ->take(4)
            ->get();

        return [
            'has_student' => true,
            'childData' => $childData,
            'attendanceSummary' => $attendanceSummary,
            'todaySchedules' => $todaySchedules,
            'nilaiPerMapel' => $nilaiPerMapel,
            'rataRataNilai' => $rataRataNilai,
            'feeSummary' => $feeSummary,
            'announcements' => $announcements,
        ];
    }

    /**
     * Attendance History for Child
     */
    public function getAttendanceHistory(int $userId, array $filters = []): array
    {
        $defaultSummary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'L' => 0, 'total' => 0, 'persen_hadir' => 0];

        $childData = $this->getChildData($userId);
        if (! $childData) {
            return ['has_student' => false, 'logs' => [], 'summary' => $defaultSummary];
        }

        $studentId = $childData->student_id;
        $semesterId = $filters['semester_id'] ?? ($childData->active_semester?->semester_id);

        $logs = $this->parentRepository->getAttendanceLogs($studentId, $semesterId, $filters);

        $summary = $defaultSummary;
        $summary['total'] = count($logs);

        foreach ($logs as $log) {
            $status = $log->status ?? 'H';
            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        $summary['persen_hadir'] = $summary['total'] > 0
            ? round(($summary['H'] / $summary['total']) * 100, 1)
            : 0;

        return [
            'has_student' => true,
            'childData' => $childData,
            'logs' => $logs,
            'summary' => $summary,
            'filters' => $filters,
        ];
    }

    /**
     * Score Summary for Child
     */
    public function getScoreSummary(int $userId, ?int $semesterId = null): array
    {
        $childData = $this->getChildData($userId);
        if (! $childData) {
            return ['has_student' => false, 'subjects' => [], 'sessions' => []];
        }

        $studentId = $childData->student_id;
        $gradeId = $childData->grade_id;
        $targetSemesterId = $semesterId ?: ($childData->active_semester?->semester_id);

        $subjects = $this->parentRepository->getScoreSubjects($studentId, $gradeId, $targetSemesterId);
        $sessions = $this->parentRepository->getScoreSessions($studentId, $gradeId, $targetSemesterId);

        $overallAverage = count($subjects) > 0 ? round(collect($subjects)->avg('avg_score'), 1) : 0;

        return [
            'has_student' => true,
            'childData' => $childData,
            'subjects' => $subjects,
            'sessions' => $sessions,
            'overallAverage' => $overallAverage,
        ];
    }

    /**
     * Fee / SPP Invoices & Transactions
     */
    public function getFeeInvoices(int $userId): array
    {
        $childData = $this->getChildData($userId);
        if (! $childData) {
            return ['has_student' => false, 'invoices' => [], 'summary' => []];
        }

        $invoices = [
            [
                'id' => 1,
                'title' => 'SPP Bulan Juli 2026',
                'category' => 'SPP Bulanan',
                'amount' => 500000,
                'paid_amount' => 500000,
                'due_date' => '2026-07-10',
                'status' => 'Lunas',
                'status_badge' => 'success',
                'paid_at' => '2026-07-05 10:15',
            ],
            [
                'id' => 2,
                'title' => 'SPP Bulan Agustus 2026',
                'category' => 'SPP Bulanan',
                'amount' => 500000,
                'paid_amount' => 500000,
                'due_date' => '2026-08-10',
                'status' => 'Lunas',
                'status_badge' => 'success',
                'paid_at' => '2026-07-25 14:30',
            ],
            [
                'id' => 3,
                'title' => 'SPP Bulan September 2026',
                'category' => 'SPP Bulanan',
                'amount' => 500000,
                'paid_amount' => 0,
                'due_date' => '2026-09-10',
                'status' => 'Belum Dibayar',
                'status_badge' => 'warning',
                'paid_at' => null,
            ],
            [
                'id' => 4,
                'title' => 'Uang Kegiatan Extrakurikuler',
                'category' => 'Kegiatan',
                'amount' => 250000,
                'paid_amount' => 0,
                'due_date' => '2026-08-30',
                'status' => 'Belum Dibayar',
                'status_badge' => 'warning',
                'paid_at' => null,
            ],
        ];

        $totalTagihan = collect($invoices)->sum('amount');
        $totalTerbayar = collect($invoices)->sum('paid_amount');
        $sisaTagihan = $totalTagihan - $totalTerbayar;

        return [
            'has_student' => true,
            'childData' => $childData,
            'invoices' => $invoices,
            'summary' => [
                'total_tagihan' => $totalTagihan,
                'total_terbayar' => $totalTerbayar,
                'sisa_tagihan' => $sisaTagihan,
            ],
        ];
    }

    /**
     * Teacher contacts for parent communication
     */
    public function getTeacherContacts(int $userId): array
    {
        $childData = $this->getChildData($userId);
        if (! $childData) {
            return ['has_student' => false, 'teachers' => []];
        }

        $teachers = $this->parentRepository->getTeacherContacts($childData->grade_id);

        return [
            'has_student' => true,
            'childData' => $childData,
            'teachers' => $teachers,
        ];
    }

    /**
     * Report Card summary
     */
    public function getReportCardSummary(int $userId, ?int $semesterId = null): array
    {
        $childData = $this->getChildData($userId);
        if (! $childData) {
            return ['has_student' => false, 'subjects' => [], 'comments' => null];
        }

        $studentId = $childData->student_id;
        $gradeId = $childData->grade_id;
        $targetSemesterId = $semesterId ?: ($childData->active_semester?->semester_id);

        $rawScores = $this->parentRepository->getScoreSubjects($studentId, $gradeId, $targetSemesterId);

        $reportScores = collect($rawScores)->map(function ($item) {
            $score = $item->avg_score;
            if ($score >= 90) {
                $grade = 'A';
                $predikat = 'Sangat Baik';
            } elseif ($score >= 80) {
                $grade = 'B';
                $predikat = 'Baik';
            } elseif ($score >= 70) {
                $grade = 'C';
                $predikat = 'Cukup';
            } else {
                $grade = 'D';
                $predikat = 'Perlu Bimbingan';
            }

            return [
                'subject' => $item->subject_name,
                'code' => $item->subject_code,
                'score' => $score,
                'grade' => $grade,
                'predikat' => $predikat,
            ];
        });

        return [
            'has_student' => true,
            'childData' => $childData,
            'reportScores' => $reportScores,
            'academicYear' => $childData->active_semester?->year_name ?? '2025/2026',
            'semesterName' => $childData->active_semester?->semester_name ?? 'Semester Ganjil',
            'notes' => 'Siswa menunjukkan perkembangan positif yang konsisten dalam pembelajaran.',
        ];
    }

    /**
     * Academic History
     */
    public function getAcademicHistory(int $userId): array
    {
        $childData = $this->getChildData($userId);
        if (! $childData) {
            return ['has_student' => false, 'histories' => []];
        }

        $histories = [
            [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'Ganjil',
                'kelas' => $childData->grade_name,
                'rata_rata' => 84.5,
                'kehadiran' => '96%',
                'status_promosi' => 'Aktif',
            ],
            [
                'tahun_ajaran' => '2024/2025',
                'semester' => 'Genap',
                'kelas' => 'Kelas Sebelumnya',
                'rata_rata' => 82.0,
                'kehadiran' => '94%',
                'status_promosi' => 'Naik Kelas',
            ],
        ];

        return [
            'has_student' => true,
            'childData' => $childData,
            'histories' => $histories,
        ];
    }
}
