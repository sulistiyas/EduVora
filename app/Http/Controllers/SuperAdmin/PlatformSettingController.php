<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\System\PlatformSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformSettingController extends Controller
{
    public function index(): View
    {
        $settings = PlatformSetting::orderBy('group')->orderBy('key')->get();
        $grouped = $settings->groupBy('group');

        return view('pages.settings.index', compact('grouped'));
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        foreach ($request->input('settings') as $key => $value) {
            PlatformSetting::where('key', $key)->update(['value' => $value]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan berhasil disimpan.',
        ]);
    }
}
