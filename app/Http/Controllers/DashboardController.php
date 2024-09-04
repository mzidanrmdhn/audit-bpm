<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usersOnline = User::whereHas('activities', function ($query) {
            $query->where('timestamp', '>=', Carbon::now()->subMinutes(5)); // Contoh: pengguna online dalam 5 menit terakhir
        })->get();

        $activities = ActivityLog::latest()->limit(10)->get();

        return view('dashboard.index', compact('usersOnline', 'activities'));
    }
}
