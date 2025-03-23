<?php

namespace App\Http\Controllers\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Responser\JsonResponser;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {


        try {
            $totalAppoint = Appointment::count();
            $weeklyAppointment = Appointment::whereBetween('date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();
            $records = Appointment::with('customer')->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'totalAppoint' => $totalAppoint,
                'totalEarnings' => 0,
                'weeklyAppointment' => $weeklyAppointment,
                'appointments' => $records
            ];
            return JsonResponser::send(false, 'Record found successfully!', $data, 200);
        } catch (\Throwable $e) {
            return JsonResponser::send(true, $e->getMessage(), null, 500);
        }
    }
}
