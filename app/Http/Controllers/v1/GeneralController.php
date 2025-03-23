<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Responser\JsonResponser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GeneralController extends Controller
{
    // Create customer
    public function createCustomer(Request $request)
    {
        try {
            DB::beginTransaction();

            $validateRequest = $this->validateCustomerRequest($request);

            if ($validateRequest->fails()) {
                return JsonResponser::send(true, $validateRequest->errors()->first(), $validateRequest->errors()->all(), 400);
            }

            $createRecord = Customer::firstOrCreate([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phoneno,
                'status' => 'Active' //$request->status
            ]);

            DB::commit();
            return JsonResponser::send(false, "Customer created successfully", $createRecord, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }

    // All services
    public function allServices()
    {
        try {
            $reocrd = Service::orderBy('created_at', 'desc')->get();
            if ($reocrd->isEmpty()) {
                return JsonResponser::send(true, 'Record Not Found', null, 404);
            }
            return JsonResponser::send(false, 'Record found! successfully', $reocrd, 200);
        } catch (\Throwable $e) {
            return JsonResponser::send(true, $e->getMessage(), null, 500);
        }
    }

    // Create appointment
    public function createAppointment(Request $request)
    {
        try {
            DB::beginTransaction();

            $validateRequest = $this->validateAppointmentRequest($request);

            if ($validateRequest->fails()) {
                return JsonResponser::send(true, $validateRequest->errors()->first(), $validateRequest->errors()->all(), 400);
            }

            $appointmentId = time() . strtoupper(Str::random(6)); // Generates appointmentId

            $createRecord = Appointment::create([
                'customer_id' => $request->customer_id,
                'service_id' => $request->service_id,
                'appointmentId' => $appointmentId,
                'date' => $request->date,
                'time' => $request->time,
                'additional_details' => $request->additional_details
            ]);

            DB::commit();
            return JsonResponser::send(false, "Appointment created successfully", $createRecord, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }

    private function validateCustomerRequest($request)
    {
        $rules = [
            'full_name' => 'required',
            'email' => 'required',
            'phoneno' => 'required'
        ];

        $validate = Validator::make($request->all(), $rules);
        return $validate;
    }

    private function validateAppointmentRequest($request)
    {
        $rules = [
            'customer_id' => 'required',
            'service_id' => 'required',
            'date' => 'required',
            'time' => 'required'
        ];

        $validate = Validator::make($request->all(), $rules);
        return $validate;
    }
}
