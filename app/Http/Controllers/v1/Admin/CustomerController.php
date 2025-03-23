<?php

namespace App\Http\Controllers\v1\Admin;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Responser\JsonResponser;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {

            $records = Customer::with(['appointment' => function ($query) {
                $query->latest('date'); // Get the latest appointment first
            }]);

            if ($request->export == true) {
                $records = $records->orderBy('created_at', 'desc')->get()->map(function ($record) {
                    $record->last_visit = optional($record->appointment->first())->date; // Get latest confirmed appointment date
                    return $record;
                });
                return Excel::download(new CustomerExport($records), 'customer.xlsx');
            } else {
                $records = $records->orderBy('created_at', 'desc')->paginate(10);
                // Append last_visit_date to each customer record
                $records->getCollection()->transform(function ($record) {
                    $record->last_visit_date = optional($record->appointment->first())->date;
                    return $record;
                });
                return JsonResponser::send(false, 'Record found successfully!', $records, 200);
            }
        } catch (\Throwable $e) {
            return JsonResponser::send(true, $e->getMessage(), null, 500);
        }
    }

    public function show($id)
    {
        try {

            $record = Customer::where('id', $id)->with('appointment.service')->first();
            if (is_null($record)) {
                return JsonResponser::send(true, 'Record not found', [], 400);
            }

            return JsonResponser::send(false, 'Record found successfully', $record, 200);
        } catch (\Throwable $error) {
            logger($error);
            return JsonResponser::send(true, $error->getMessage(), [], 500);
        }
    }
}
