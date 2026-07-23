<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasSchoolScope
{
    private function getAuthSchoolId(): int
    {
        $schoolId = Auth::user()->schools->first()->school_id ?? null;

        if (! $schoolId) {
            throw new \Exception('Admin tidak terkait dengan sekolah manapun.');
        }

        return $schoolId;
    }
}
