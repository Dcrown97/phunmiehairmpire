<?php

namespace App\Http\Controllers\v1\Admin;

use App\Helpers\ProcessAuditLog;
use App\Helpers\UserMgtHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Responser\JsonResponser;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            $reocrd = Service::orderBy('created_at', 'desc')->paginate(10);
            if ($reocrd->isEmpty()) {
                return JsonResponser::send(true, 'Record Not Found', null, 404);
            }
            return JsonResponser::send(false, 'Record found! successfully', $reocrd, 200);
        } catch (\Throwable $e) {
            return JsonResponser::send(true, $e->getMessage(), null, 500);
        }
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();

            $validateRequest = $this->validateServiceRequest($request);

            if ($validateRequest->fails()) {
                return JsonResponser::send(true, $validateRequest->errors()->first(), $validateRequest->errors()->all(), 400);
            }

            $currentUserInstance = UserMgtHelper::userInstance();
            $currentUserInstanceId = $currentUserInstance->id;

            $createRecord = Service::create([
                'name' => $request->name,
                'price' => $request->price,
                'duration' => $request->duration,
                'description' => $request->description
            ]);

            $dataToLog = [
                'causer_id' => $currentUserInstanceId,
                'action_id' => $createRecord->id,
                'action_type' => "Models\Service",
                'action' => "Create",
                'log_name' => "Service created successfully",
                'description' => "Service created successfully by {$currentUserInstance->name}",
            ];
            ProcessAuditLog::storeAuditLog($dataToLog);

            DB::commit();
            return JsonResponser::send(false, "Service created successfully", $createRecord, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }

    public function show($id)
    {
        try {

            $record = Service::find($id);
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

            $validateRequest = $this->validateServiceRequest($request);

            if ($validateRequest->fails()) {
                return JsonResponser::send(true, $validateRequest->errors()->first(), $validateRequest->errors()->all(), 400);
            }

            $currentUserInstance = UserMgtHelper::userInstance();
            $currentUserInstanceId = $currentUserInstance->id;

            $record = Service::find($id);
            if (is_null($record)) {
                return JsonResponser::send(true, 'Record not found', [], 400);
            }

            $record->update([
                'name' => $request->name,
                'price' => $request->price,
                'duration' => $request->duration,
                'description' => $request->description
            ]);

            $dataToLog = [
                'causer_id' => $currentUserInstanceId,
                'action_id' => $record->id,
                'action_type' => "Models\Service",
                'action' => "Create",
                'log_name' => "Service updated successfully",
                'description' => "Service updated successfully by {$currentUserInstance->name}",
            ];
            ProcessAuditLog::storeAuditLog($dataToLog);

            $newService = Service::where('id', $request->id)->first();

            DB::commit();
            return JsonResponser::send(false, "Service updated successfully", $newService, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $user = UserMgtHelper::userInstance();
            $userId = $user->id;

            $record = Service::find($id);
            if (is_null($record)) {
                return JsonResponser::send(true, 'Record not found', [], 400);
            }

            $dataToLog = [
                'causer_id' => $userId,
                'action_id' => $record->id,
                'action_type' => "Models\Service",
                'action' => "Delete",
                'log_name' => "Service deleted successfully",
                'description' => "Service deleted successfully by {$user->name}",
            ];
            ProcessAuditLog::storeAuditLog($dataToLog);

            $record->delete();

            DB::commit();
            return JsonResponser::send(false, "Service deleted successfully", 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }

    private function validateServiceRequest($request)
    {
        $rules = [
            'name' => 'required',
            'price' => 'required',
            'duration' => 'required'
        ];

        $validate = Validator::make($request->all(), $rules);
        return $validate;
    }
}
