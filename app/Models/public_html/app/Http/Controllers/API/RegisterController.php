<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends BaseController
{
    public function Docterregister(Request $request)
    {
        $input = $request->all();
        //validate incoming inputs
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $userId = DB::table('users')->insertGetId([
            'name'=>$request->name,
            'email'=>$request->email,
            'user_role'=>'2',
            'password'=>Hash::make($request->password),
        ]);
        $DocterData = [
            'firstname' => $input['firstname'],
            'lastname' => $input['secondname'],
            'mobileNo' => $input['mobileNo'],
            'email' => $input['email'],
            'location' => $input['location'],
            'specification_id' => $input['specification_id'],
            'subspecification_id' => $input['subspecification_id'],
            'specialization_id' => $input['specialization_id'],
            'about' => $input['about'],
            'Services_at' => $input['service_at'],
            'gender' => $input['gender'],
            'UserId' => $userId,
        ];

        if ($request->hasFile('docter_image')) {
            $imageFile = $request->file('docter_image');

            if ($imageFile->isValid()) {
                $imageName = $imageFile->getClientOriginalName();
                $imageFile->move(public_path('DocterImages/images'), $imageName);

                $DocterData['docter_image'] = $imageName;
            }
        }

        $Docter = new Docter($DocterData);
        $Docter->save();


        return $this->sendResponse("Docters", $Docter, '1', 'Docter created successfully.');


    }


    public function Userregister(Request $request)
    {
        //validate incoming inputs
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'user_role'=>'3',
            'password'=>Hash::make($request->password),
        ]);



        return $user;


    }

    public function LabRegister(Request $request)
    {
        //validate incoming inputs
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'user_role'=>'4',
            'password'=>Hash::make($request->password),
        ]);



        return $user;


    }

}
