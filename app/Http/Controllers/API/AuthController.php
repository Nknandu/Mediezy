<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{

    public function login(Request $req)
{
    // validate inputs
    $rules = [
        'email' => 'required',
        'password' => 'required|string'
    ];
    $req->validate($rules);

    // find user email in users table
    $user = User::where('email', $req->email)
        ->where('user_role', 3) // 3 represents regular user
        ->first();

    // find user email in doctors table
    $doctor = User::where('email', $req->email)
        ->where('user_role', 2) // 2 represents doctor
        ->first();

    // find user email in labs table
    $lab = User::where('email', $req->email)
        ->where('user_role', 4) // 4 represents lab
        ->first();

    // find user email in medicalshops table
    $medicalShop = User::where('email', $req->email)
        ->where('user_role', 5) // 5 represents medical shop
        ->first();

        $hospital=User::where('email', $req->email)
        ->where('user_role', 6) // 5 represents medical shop
        ->first();
    // if user email found and password is correct
    if ($user && Hash::check($req->password, $user->password)) {
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $response = ['user' => $user, 'token' => $token, 'role' => 'user'];
        return response()->json($response, 200);
    } elseif ($doctor && Hash::check($req->password, $doctor->password)) {
        $token = $doctor->createToken('Personal Access Token')->plainTextToken;
        $response = ['doctor' => $doctor, 'token' => $token, 'role' => 'doctor'];
        return response()->json($response, 200);
    } elseif ($lab && Hash::check($req->password, $lab->password)) {
        $token = $lab->createToken('Personal Access Token')->plainTextToken;
        $response = [
            'lab' => [
                'id' => $lab->id,
                'firstname' => $lab->firstname,
                "mobileNo"=>$lab->mobileNo,
                'email' => $lab->email,
                'user_role' => $lab->user_role,
            ],
            'token' => $token,
            'role' => 'lab',
        ];
        return response()->json($response, 200);
    } elseif ($medicalShop && Hash::check($req->password, $medicalShop->password)) {
        $token = $medicalShop->createToken('Personal Access Token')->plainTextToken;
        $response = [
            'medical_shop' => [
                'id' => $medicalShop->id,
                'firstname' => $medicalShop->firstname,
                "mobileNo"=>$medicalShop->mobileNo,
                'email' => $medicalShop->email,
                'user_role' => $medicalShop->user_role,
            ],
            'token' => $token,
            'role' => 'medicalShop',
        ];
        return response()->json($response, 200);
    }

    $response = ['message' => 'Incorrect email or password'];
    return response()->json($response, 400);
}

}
