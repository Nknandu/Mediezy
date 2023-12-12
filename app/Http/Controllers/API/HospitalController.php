<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HospitalController extends BaseController
{

    public function HospitalRegister(Request $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();

            $validator = Validator::make($input, [
                'firstname' => 'required',
                'email' => 'required',
                'password' => 'required',
                'mobileNo' => 'required',
                'address' => 'required',
                'Type' => 'sometimes|in:1,2' //1 for hospital 2 for clinic

            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }


            $emailExists = Hospital::where('email', $input['email'])->count();
            $emailExistsinUser = User::where('email', $input['email'])->count();

            if ($emailExists && $emailExistsinUser) {
                return $this->sendResponse("Hospital", null, '3', 'Email already exists.');
            }

            $input['password'] = Hash::make($input['password']);

            $userId = DB::table('users')->insertGetId([
                'firstname' => $input['firstname'],
                'secondname' => 'Hospital',
                'email' => $input['email'],
                'password' => $input['password'],
                'user_role' => 6, //6 for hospital
            ]);

            $HospitalData = [

                'firstname' => $input['firstname'],
                'mobileNo' => $input['mobileNo'],
                'email' => $input['email'],
                'location' => $input['location'],
                'address' => $input['address'],
                'Type' => $input['Type'],
                'UserId' => $userId,
            ];

            if ($request->hasFile('hospital_image')) {
                $imageFile = $request->file('hospital_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('HospitalImages/images'), $imageName);

                    $DocterData['hospital_image'] = $imageName;
                }
            }

            $Hospital = new Hospital($HospitalData);
            $Hospital->save();
            DB::commit();

            return $this->sendResponse("Hospital", $Hospital, '1', 'Hospital created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }





    public function AddDocter(Request $request)
    {

        try {
            DB::beginTransaction();

            $input = $request->all();

            $validator = Validator::make($input, [
                'HospitalId' => 'required',
                'firstname' => 'required',
                'secondname' => 'required',
                'email' => 'required',
                'password' => 'required',
                'mobileNo' => 'required',


            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }
            $emailExists = Docter::where('email', $input['email'])->count();
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
                'user_role' => 2,
            ]);

            $DocterData = [
                'HospitalId' => $input['HospitalId'],
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
            DB::commit();

            return $this->sendResponse("Docters", $Docter, '1', 'Docter created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }





    public function GetAllDoctorsbyHospitalId($hospitalId)
    {
        $doctors = DB::table('docter')
            ->join('Hosptal', 'docter.HospitalId', '=', 'Hosptal.UserId')
            ->where('Hosptal.UserId', $hospitalId)
            ->select('docter.*')
            ->get();

        return response()->json(['status' => true, 'doctors' => $doctors]);
    }





    public function GetCountOfDocter($hospitalId)
    {
        $doctorCount = DB::table('docter')
            ->join('Hosptal', 'docter.HospitalId', '=', 'Hosptal.UserId')
            ->where('Hosptal.UserId', $hospitalId)
            ->count();

        return response()->json(['status' => true, 'doctorCount' => $doctorCount]);
    }


    public function getAppointmentCountByHospitalId($hospitalId)
    {
        $appointmentCount = DB::table('token_booking')
            ->join('Hosptal', 'token_booking.clinic_id', '=', 'Hosptal.id')
            ->where('token_booking.clinic_id', $hospitalId)
            ->count();

        return response()->json(['status' => true, 'appointmentCount' => $appointmentCount]);
    }

}
