<?php

namespace App\Http\Controllers\v1\Admin;

use App\Helpers\ProcessAuditLog;
use App\Helpers\UserMgtHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Responser\JsonResponser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function show($id)
    {
        try {
            $record = User::where('id', $id)->first();
            if (is_null($record)) {
                return JsonResponser::send(true, 'Record not found', [], 400);
            }
            return JsonResponser::send(false, 'Record found successfully', $record, 200);
        } catch (\Throwable $error) {
            logger($error);
            return JsonResponser::send(true, $error->getMessage(), [], 500);
        }
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $currentUserInstance = UserMgtHelper::userInstance();
            $currentUserInstanceId = $currentUserInstance->id;

            // Check if resource exists
            $record = User::find($id);
            if (is_null($record)) {
                return JsonResponser::send(true, "User not found", [], 400);
            }

            $record->update($request->only(array_keys($request->all())));

            // Log the update action
            $dataToLog = [
                'causer_id' => $currentUserInstanceId,
                'action_id' => $record->id,
                'action_type' => "Models\User",
                'action' => "Update",
                'log_name' => "Profile updated successfully",
                'description' => "Profile updated successfully by {$currentUserInstance->name}",
            ];
            ProcessAuditLog::storeAuditLog($dataToLog);

            DB::commit();

            // Check if resource exists
            $record = User::find($id);

            // Return the updated resource
            return JsonResponser::send(false, "Profile updated successfully", $record, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponser::send(true, $th->getMessage(), [], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $rules = [
                'password' =>  [
                    'required',
                    'string',
                    'min:8',             // must be at least 8 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                "user_id" => "required|exists:users,id",
            ];

            $validateRequest = Validator::make($request->all(), $rules);

            if ($validateRequest->fails()) {
                return JsonResponser::send(true, $validateRequest->errors()->first(), $validateRequest->errors()->all(), 400);
            }
            DB::beginTransaction();
            $userdata = User::find($request->user_id);

            $password = $request->password;
            $hashedPasword = $userdata->password;
            // check if new password is not the same with old password
            if (Hash::check($password, $hashedPasword)) {
                return JsonResponser::send(true, "New password cannot be the same as old password", [], 400);
            }

            $updatePassword = $userdata->update([
                'password' => Hash::make($password),
                'can_login' => true,
                'is_active' => true,
            ]);

            if (!$updatePassword) {
                return JsonResponser::send(true, "Error occured! Please refresh and try again", [], 400);
            }

            DB::commit();
            return JsonResponser::send(false, "Password created successfully! Please login with your new password", [], 200);
        } catch (\Throwable $error) {
            DB::rollBack();
            return JsonResponser::send(true, $error->getMessage(), [], 500);
        }
    }
}
