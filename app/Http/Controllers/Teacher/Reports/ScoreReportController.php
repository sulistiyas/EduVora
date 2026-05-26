<?php

namespace App\Http\Controllers\Teacher\Reports;

use App\Exports\Teacher\Reports\ScoreReportExport;
use App\Http\Controllers\Controller;
use App\Models\Academic\GradeSubject;
use App\Models\Academic\Semester;
use App\Models\Student\ScoreSession;
use App\Services\Reports\Teacher\ScoreReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ScoreReportController extends Controller
{
    public function __construct(
        protected ScoreReportService $service,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $teacher  = Auth::user()->teacher;
        $schoolId = Auth::user()->schools->first()->school_id;

        $filters = $this->service->buildFilters(
            input: $request->only([
                'semester_id',
                'grade_id',
                'subject_id',
                'grade_subject_id',
                'score_type',
                'is_published',
            ]),
            teacherId: $teacher->teacher_id,
            schoolId:  $schoolId,
        );

        $data = $this->service->getIndexData($filters);

        $exportGsId = $filters['grade_subject_id'];
        if (!$exportGsId && !empty($filters['grade_id']) && !empty($filters['subject_id'])) {
            $exportGsId = GradeSubject::query()
                ->where('grade_id',   $filters['grade_id'])
                ->where('subject_id', $filters['subject_id'])
                ->where('teacher_id', $teacher->teacher_id)
                ->value('id');
        }
        if (!$exportGsId) {
            $exportGsId = GradeSubject::query()
                ->where('teacher_id', $teacher->teacher_id)
                ->where('status', 'active')
                ->value('id');
        }

        $data['filters']['grade_subject_id'] = $exportGsId;
        return view('reports.score.index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — detail 1 sesi (modal / halaman detail)
    |--------------------------------------------------------------------------
    */

    public function show(int $sessionId)
    {
        $session = ScoreSession::with([
            'gradeSubject.grade:grade_id,grade_name,level',
            'gradeSubject.subject:id,subject_name,subject_code',
            'semester:semester_id,semester_name',
        ])->findOrFail($sessionId);

        // Pastikan sesi milik guru yang login
        $teacher = Auth::user()->teacher;
        abort_if(
            $session->gradeSubject?->teacher_id !== $teacher->teacher_id,
            403,
            'Anda tidak memiliki akses ke sesi ini.'
        );

        $detail = $this->service->getSessionDetail($sessionId);

        return view('reports.score.show', compact('detail', 'session'));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT — dipanggil dari index (student summary) atau show (per sesi)
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        $teacher  = Auth::user()->teacher;
        $schoolId = Auth::user()->schools->first()->school_id;

        $exportType = $request->query('type', 'summary'); // 'summary' | 'sessions'

        $filters = $this->service->buildFilters(
            input: $request->only([
                'semester_id',
                'grade_id',
                'subject_id',
                'grade_subject_id',
                'score_type',
            ]),
            teacherId: $teacher->teacher_id,
            schoolId:  $schoolId,
        );
        if (empty($filters['grade_subject_id'])) {
            $filters['grade_subject_id'] = GradeSubject::query()
                ->where('teacher_id', $teacher->teacher_id)
                ->where('status', 'active')
                ->value('id');
        }

    //     dd([
    //     'filters'     => $filters,
    //     'exportType'  => $exportType,
    //     'rowsCount'   => $this->service->getExportStudentSummary($filters)->count(),
    //     'rawSummary'  => $this->service->getExportStudentSummary($filters)->first(),
    // ]);

        // Meta untuk header Excel
        $meta = $this->buildExportMeta($filters, $teacher, $exportType);

        // Rows dari service
        $rows = match ($exportType) {
            'sessions' => $this->service->getExportSessionsByType($filters),
            default    => $this->service->getExportStudentSummary($filters),
        };

        // Nama file
        $filename = implode('-', array_filter([
            'nilai',
            str($meta['grade'])->slug(),
            str($meta['subject'])->slug(),
            $meta['semester'] ? str($meta['semester'])->slug() : null,
            $exportType,
            now()->format('Ymd'),
        ])) . '.xlsx';

        return Excel::download(
            new ScoreReportExport($rows, $meta, $exportType),
            $filename,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Build meta array untuk header Excel dari active filters.
     */
    private function buildExportMeta(array $filters, object $teacher, string $exportType): array
    {
        // Ambil grade_subject untuk mendapat label grade & subject
        $gradeSubjectId = $filters['grade_subject_id'] ?? null;

        $gradeSubject = $gradeSubjectId
            ? GradeSubject::with([
                'grade:grade_id,grade_name',
                'subject:id,subject_name',
              ])->find($gradeSubjectId)
            : null;

        $semester = !empty($filters['semester_id'])
            ? Semester::find($filters['semester_id'])
            : null;

        $scoreTypeLabel = match ($filters['score_type'] ?? null) {
            'harian' => 'Harian',
            'uts'    => 'UTS',
            'uas'    => 'UAS',
            default  => 'Semua Tipe',
        };

        return [
            'title'       => 'Laporan Nilai — '
                             . ($gradeSubject?->grade?->grade_name   ?? '')
                             . ' · '
                             . ($gradeSubject?->subject?->subject_name ?? ''),
            'grade'       => $gradeSubject?->grade?->grade_name       ?? '-',
            'subject'     => $gradeSubject?->subject?->subject_name   ?? '-',
            'semester'    => $semester?->semester_name                 ?? '-',
            'score_type'  => $scoreTypeLabel,
            'kkm'         => $gradeSubject?->kkm                      ?? '-',
            'teacher'     => $teacher->full_name                      ?? '',
            'export_type' => $exportType,
            'generated_at'=> now()->format('d/m/Y H:i'),
        ];
    }
}