<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $trialDays = SystemSetting::get('trial_days', '14');
        $enableTrial = SystemSetting::get('enable_free_trial', '1');

        return view('super-admin.settings.index', compact('trialDays', 'enableTrial'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'trial_days' => 'required|integer|min:0|max:365',
            'enable_free_trial' => 'nullable|boolean',
        ]);

        $trialDays = $request->input('trial_days', 14);
        $enableTrial = $request->has('enable_free_trial') ? '1' : '0';

        if ($enableTrial === '0') {
            $trialDays = 0;
        }

        SystemSetting::set('trial_days', (string) $trialDays);
        SystemSetting::set('enable_free_trial', (string) $enableTrial);

        return back()->with('success', 'System settings updated successfully.');
    }
}
