<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Medicalshop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MedicalshopController extends BaseController
{


    public function LabRegister(Request $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();

            $emailExists = Medicalshop::where('email', $input['email'])->count();
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
                'user_role' => 5,
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

            if ($request->hasFile('shop_image')) {
                $imageFile = $request->file('shop_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('shopImages/images'), $imageName);

                    $DocterData['shop_image'] = $imageName;
                }
            }

            $Medicalshop = new Medicalshop($DocterData);
            $Medicalshop->save();
            DB::commit();

            return $this->sendResponse("Medicalshop", $Medicalshop, '1', 'Medicalshop created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }
}
