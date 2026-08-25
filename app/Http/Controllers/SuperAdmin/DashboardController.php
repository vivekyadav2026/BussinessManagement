<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $orgCount = Organization::count();
        $userCount = User::count();
        $activeOrgs = Organization::where('is_active', true)->count();
        
        return view('super-admin.dashboard', compact('orgCount', 'userCount', 'activeOrgs'));
    }
}
