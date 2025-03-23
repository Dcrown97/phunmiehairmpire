<?php

namespace App\Http\Controllers\v1\Admin;

use App\Exports\AppointmentExport;
use App\Helpers\ProcessAuditLog;
use App\Helpers\UserMgtHelper;
use App\Http\Controllers\Controller;
use App\Mail\Messages;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Responser\JsonResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $searchParam = $request->search_param;

        (!is_null($request->start_date) && !is_null($request->end_date)) ? $dateSearchParams = true : $dateSearchParams = false;

        try {

            $records = Appointment::when($searchParam, function ($query) use ($searchParam) {
                return $query->whereRelation('customer', 'full_name', 'like', '%' . $searchParam . '%')
                    ->orWhere('time', 'like', '%' . $searchParam . '%');
            })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($dateSearchParams, function ($query) use ($request) {
                    $startDate = Carbon::parse($request->start_date);
                    $endDate = Carbon::parse($request->end_date);

                    // Filter events that fall between the provided start_date and end_date
                    return $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    });
                })->with('customer');

            if ($request->export == true) {
                $records = $records->orderBy('created_at', 'desc')->get();
                return Excel::download(new AppointmentExport($records), 'appointment.xlsx');
            } else {
                $records = $records->orderBy('created_at', 'desc')->paginate(10);
                return JsonResponser::send(false, 'Record found successfully!', $records, 200);
            }
        } catch (\Throwable $e) {
            return JsonResponser::send(true, $e->getMessage(), null, 500);
        }
    }

    public function show($id)
    {
        try {

            $record = Appointment::where('id', $id)->with('customer', 'service')->first();
            if (is_null($record)) {
                return JsonResponser::send(true, 'Record not found', [], 400);
            }

            return JsonResponser::send(false, 'Record found successfully', $record, 200);
        } catch (\Throwable $error) {
            logger($error);
            return JsonResponser::send(true, $error->getMessage(), [], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $currentUserInstance = UserMgtHelper::userInstance();
            $currentUserInstanceId = $currentUserInstance->id;

            $record = Appointment::where('id', $id)->with('customer', 'service')->first();
            if (is_null($record)) {
                return JsonResponser::send(true, 'Record not found', [], 400);
            }

            $previousDate = $record->date;
            $previousTime = $record->time;

            $record->update($request->only(array_keys($request->all())));

            $mailData = [
                'name' => $record->customer->full_name,
                'phoneno' => $record->customer->phone_number,
                'service' => $record->service->name,
                'price' => $record->service->price,
                'date' => $record->date,
                'time' => $record->time,
                'previousDate' => $previousDate,
                'previousTime' => $previousTime,
                'status' => $record->status,
                'email' => $record->customer->email
            ];
            Mail::to($record->email)->send(new Messages($mailData));

            $dataToLog = [
                'causer_id' => $currentUserInstanceId,
                'action_id' => $record->id,
                'action_type' => "Models\Appointment",
                'action' => "Update",
                'log_name' => "Appointment {$request->status} successfully",
                'description' => "Appointment {$request->status} successfully by {$currentUserInstance->name}",
            ];
            ProcessAuditLog::storeAuditLog($dataToLog);

            $newAppointment = Appointment::where('id', $request->id)->with('customer', 'service')->first();

            DB::commit();
            return JsonResponser::send(false, "Appointment has been {$request->status} successfully", $newAppointment, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }
}
