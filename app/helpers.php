<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('getAuthSchoolId')) {
    function getAuthSchoolId(): int
    {
        $schoolId = Auth::user()?->schools?->first()?->school_id;

        if (! $schoolId) {
            abort(403, 'Akun ini tidak terkait dengan sekolah manapun.');
        }

        return (int) $schoolId;
    }
}
