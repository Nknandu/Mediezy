<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function UserRegister(Request $request){

        try {
            DB::beginTransaction();

            $input = $request->all();

            $emailExists = Patient::where('email', $input['email'])->count();
            $emailExistsinUser = User::where('email', $input['email'])->count();

            if ($emailExists && $emailExistsinUser) {
                return $this->sendResponse("Docters", null, '3', 'Email already exists.');
            }

            $input['password'] = Hash::make($input['password']);

            $userId = DB::table('users')->insertGetId([
                'firstname' => $input['firstname'],
                'secondname' => $input['secondname'],
                'email' => $input['email'],
                'password' => $input['password'],
                'mobileNo' => $input['mobileNo'],
                'user_role' => 3,
            ]);

            $PatientData = [

                'firstname' => $input['firstname'],
                'lastname' => $input['secondname'],
                'mobileNo' => $input['mobileNo'],
                'email' => $input['email'],
                'location' => $input['location'],
                'gender' => $input['gender'],
                'UserId' => $userId,
            ];

            if ($request->hasFile('user_image')) {
                $imageFile = $request->file('user_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('UserImages'), $imageName);

                    $PatientData['user_image'] = $imageName;
                }
            }

            $patient = new Patient($PatientData);
            $patient->save();




            DB::commit();

            return $this->sendResponse("users", $patient, '1', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }



    public function UserLogin(Request $req)
    {
        // validate inputs
        $rules = [
            'email' => 'required',
            'password' => 'required|string'
        ];
        $req->validate($rules);
        // find user email in users table
        $user = User::where('email', $req->email)->first();

        // if user email found and password is correct
        if ($user && Hash::check($req->password, $user->password)) {
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = ['user' => $user, 'token' => $token];
            return response()->json($response, 200);
        }
        $response = ['message' => 'Incorrect email or password'];
        return response()->json($response, 400);
    }
}
