<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScoreController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $schoolId = getAuthSchoolId();

        $studentRecord = DB::table('students')
            ->where('user_id', $user->id)
            ->first();

        $studentId = $studentRecord?->id;
        $gradeId = $studentRecord?->grade_id;

        $activeSemester = DB::table('semesters')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'active')
            ->where('academic_years.school_id', $schoolId)
            ->orderByDesc('semesters.created_at')
            ->select('semesters.*')
            ->first();

        $semesterId = $activeSemester?->semester_id;

        $scoresPerSubject = [];
        $overallAvg = 0;

        if ($studentId && $gradeId && $semesterId) {
            $raw = DB::table('score_details as sd')
                ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
                ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
                ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
                ->where('sd.student_id', $studentId)
                ->where('gs.grade_id', $gradeId)
                ->where('ss.semester_id', $semesterId)
                ->where('ss.is_published', true)
                ->groupBy('sub.id', 'sub.subject_name')
                ->selectRaw('
                    sub.id as subject_id,
                    sub.subject_name,
                    ROUND(AVG(sd.score), 1) as avg_score,
                    MAX(sd.score) as max_score,
                    MIN(sd.score) as min_score,
                    COUNT(sd.score_detail_id) as total_entries
                ')
                ->orderByDesc('sub.subject_name')
                ->get();

            foreach ($raw as $r) {
                $scoresPerSubject[] = [
                    'subject' => $r->subject_name,
                    'avg' => (float) $r->avg_score,
                    'max' => (float) $r->max_score,
                    'min' => (float) $r->min_score,
                    'total' => (int) $r->total_entries,
                    'color' => $this->scoreColor((float) $r->avg_score),
                ];
            }

            $overallAvg = count($scoresPerSubject) > 0
                ? round(collect($scoresPerSubject)->avg('avg'), 1)
                : 0;
        }

        $detailScores = [];
        if ($studentId && $gradeId && $semesterId) {
            $detailRaw = DB::table('score_details as sd')
                ->join('score_sessions as ss', 'ss.score_session_id', '=', 'sd.score_session_id')
                ->join('grade_subjects as gs', 'gs.id', '=', 'ss.grade_subject_id')
                ->join('subjects as sub', 'sub.id', '=', 'gs.subject_id')
                ->where('sd.student_id', $studentId)
                ->where('gs.grade_id', $gradeId)
                ->where('ss.semester_id', $semesterId)
                ->where('ss.is_published', true)
                ->orderBy('sub.subject_name')
                ->orderBy('ss.score_date')
                ->select([
                    'sub.subject_name as mapel',
                    'ss.title',
                    'ss.score_type',
                    'ss.score_date',
                    'sd.score',
                    'sd.max_score',
                    'sd.notes',
                ])
                ->get();

            $typeLabel = ['daily' => 'Ulangan Harian', 'mid_exam' => 'UTS', 'final_exam' => 'UAS', 'assignment' => 'Tugas'];

            foreach ($detailRaw as $d) {
                $detailScores[] = [
                    'mapel' => $d->mapel,
                    'title' => $d->title,
                    'type' => $typeLabel[$d->score_type] ?? $d->score_type,
                    'date' => $d->score_date ? Carbon::parse($d->score_date)->translatedFormat('d M Y') : '-',
                    'score' => $d->score !== null ? (float) $d->score : null,
                    'max_score' => (float) ($d->max_score ?? 100),
                    'percentage' => $d->score !== null && ($d->max_score ?? 100) > 0
                        ? round(($d->score / ($d->max_score ?? 100)) * 100, 1)
                        : null,
                    'notes' => $d->notes,
                ];
            }
        }

        $gradeName = null;
        if ($gradeId) {
            $gradeRecord = DB::table('grades')->where('grade_id', $gradeId)->first();
            $gradeName = $gradeRecord?->grade_name;
        }

        return view('pages.student.scores.index', compact('scoresPerSubject', 'detailScores', 'overallAvg', 'activeSemester', 'gradeName'));
    }

    private function scoreColor(float $score): string
    {
        if ($score >= 85) {
            return '#10B981';
        }
        if ($score >= 75) {
            return '#3B82F6';
        }
        if ($score >= 65) {
            return '#F59E0B';
        }

        return '#EF4444';
    }
}
