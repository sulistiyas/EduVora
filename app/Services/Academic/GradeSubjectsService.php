<?php

namespace App\Services\Academic;

use App\Concerns\HasSchoolScope;
use App\Models\Academic\Grade;
use App\Models\Academic\GradeSubject;
use Illuminate\Database\Eloquent\Collection;

class GradeSubjectsService
{
    use HasSchoolScope;

    /**
     * Ambil semua mapel yang di-assign ke kelas tertentu
     * Eager load subject + teacher
     */
    public function getByGrade($gradeId): Collection
    {
        $grade = Grade::where('grade_id', $gradeId)
            ->where('school_id', $this->getAuthSchoolId())
            ->first();

        if (! $grade) {
            throw new \Exception('Kelas tidak ditemukan atau bukan milik sekolah ini.');
        }

        return GradeSubject::with([
            'subject:id,subject_name,subject_code',
            'teacher:teacher_id,full_name,nip',
        ])
            ->where('grade_id', $gradeId)
            ->orderBy('id')
            ->get();
    }

    /**
     * Assign mapel ke kelas
     */
    public function assignSubject($gradeId, array $data): GradeSubject
    {
        $grade = Grade::where('grade_id', $gradeId)
            ->where('school_id', $this->getAuthSchoolId())
            ->first();

        if (! $grade) {
            throw new \Exception('Kelas tidak ditemukan atau bukan milik sekolah ini.');
        }

        // Cegah duplikat
        $exists = GradeSubject::where('grade_id', $gradeId)
            ->where('id', $data['id'])
            ->exists();

        if ($exists) {
            throw new \Exception('Mata pelajaran ini sudah ada di kelas.');
        }

        $gs = GradeSubject::create([
            'grade_id' => $gradeId,
            'subject_id' => $data['id'],
            'teacher_id' => $data['teacher_id'] ?? null,
            'kkm' => $data['kkm'] ?? null,
            'weight_harian' => $data['weight_harian'] ?? 40,
            'weight_uts' => $data['weight_uts'] ?? 30,
            'weight_uas' => $data['weight_uas'] ?? 30,
            'status' => 'active',
        ]);

        return $gs->load([
            'subject:id,subject_name,subject_code',
            'teacher:teacher_id,full_name,nip',
        ]);
    }

    /**
     * Update satu kolom (teacher / kkm / bobot) pada grade_subject
     */
    public function updateSubject($gradeId, $id, array $data): ?GradeSubject
    {
        $grade = Grade::where('grade_id', $gradeId)
            ->where('school_id', $this->getAuthSchoolId())
            ->first();

        if (! $grade) {
            throw new \Exception('Kelas tidak ditemukan atau bukan milik sekolah ini.');
        }

        $gs = GradeSubject::where('grade_id', $gradeId)->find($id);
        if (! $gs) {
            return null;
        }

        $allowed = ['teacher_id', 'kkm', 'weight_harian', 'weight_uts', 'weight_uas', 'status'];
        $gs->update(array_intersect_key($data, array_flip($allowed)));

        return $gs->load([
            'subject:id,subject_name,subject_code',
            'teacher:teacher_id,full_name,nip',
        ]);
    }

    /**
     * Hapus mapel dari kelas
     */
    public function removeSubject($gradeId, $id): bool
    {
        $grade = Grade::where('grade_id', $gradeId)
            ->where('school_id', $this->getAuthSchoolId())
            ->first();

        if (! $grade) {
            throw new \Exception('Kelas tidak ditemukan atau bukan milik sekolah ini.');
        }

        $gs = GradeSubject::where('grade_id', $gradeId)->find($id);
        if (! $gs) {
            return false;
        }
        $gs->delete();

        return true;
    }
}
