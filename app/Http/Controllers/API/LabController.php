<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LabController extends BaseController
{

    public function LabRegister(Request $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();

            $emailExists = Laboratory::where('email', $input['email'])->count();
            $emailExistsinUser = User::where('email', $input['email'])->count();

            if ($emailExists && $emailExistsinUser) {
                return $this->sendResponse("Laboratory", null, '3', 'Email already exists.');
            }

            $input['password'] = Hash::make($input['password']);

            $userId = DB::table('users')->insertGetId([
                'firstname' => $input['firstname'],
                'secondname' => $input['secondname'],
                'email' => $input['email'],
                'password' => $input['password'],
                'user_role' => 4,
            ]);

            $DocterData = [

                'firstname' => $input['firstname'],
                'lastname' => $input['secondname'],
                'mobileNo' => $input['mobileNo'],
                'email' => $input['email'],
                'location' => $input['location'],
                'address' => $input['address'],
                'UserId' => $userId,
            ];

            if ($request->hasFile('lab_image')) {
                $imageFile = $request->file('lab_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('LabImages/images'), $imageName);

                    $DocterData['lab_image'] = $imageName;
                }
            }

            $Laboratory = new Laboratory($DocterData);
            $Laboratory->save();
            DB::commit();

            return $this->sendResponse("Laboratory", $Laboratory, '1', 'Laboratory created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }


}
