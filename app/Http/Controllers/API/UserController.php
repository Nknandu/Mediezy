<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\DocterAvailability;
use App\Models\Patient;
use App\Models\Symtoms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function UserRegister(Request $request)
    {

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


    private function getClinics($doctorId)
    {
        // Replace this with your actual logic to retrieve clinic details from the database
        // You may use Eloquent queries or another method based on your application structure
        $clinics = DocterAvailability::where('docter_id', $doctorId)->get(['id', 'hospital_Name', 'availability']);

        return $clinics;
    }
    // public  function GetUserAppoinments(Request $request, $userId)
    // {
    //     try {
    //         // Get the currently authenticated doctor
    //         $doctor = Patient::where('UserId', $userId)->first();


    //         if (!$doctor) {
    //             return response()->json(['message' => 'patient not found.'], 404);
    //         }

    //         // Validate the date format
    //         try {

    //         } catch (\Exception $e) {
    //             return response()->json(['message' => 'Invalid date format. Use Y-m-d.'], 400);
    //         }

    //         // Get all appointments for the doctor on the selected date
    //         $appointments = Patient::join('token_booking', 'token_booking.BookedPerson_id', '=', 'patient.UserId')
    //         ->join('docter', 'docter.UserId', '=', 'token_booking.doctor_id') // Join the doctor table
    //         ->where('patient.UserId', $doctor->UserId)
    //         ->orderByRaw('CAST(token_booking.TokenNumber AS SIGNED) ASC')
    //         ->where('Is_completed', 0)
    //         ->get(['token_booking.*', 'docter.*']);


    //         // Iterate through each appointment and add symptoms information
    //         foreach ($appointments as $appointment) {
    //             $symptoms = json_decode($appointment->Appoinmentfor_id, true);
    //             $appointment->main_symptoms = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray();
    //             $appointment->other_symptoms = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray();
    //         }

    //         // Return a success response
    //         return $this->sendResponse('Appointments', $appointments, '1', 'Appointments retrieved successfully.');
    //     } catch (\Exception $e) {
    //         // Handle unexpected errors
    //         return $this->sendError('Error', $e->getMessage(), 500);
    //     }
    // }
    public function GetUserAppointments(Request $request, $userId)
    {
        try {
            // Get the currently authenticated doctor
            $doctor = Patient::where('UserId', $userId)->first();

            if (!$doctor) {
                return response()->json(['message' => 'Patient not found.'], 404);
            }

            // Validate the date format (if needed)

            // Get all appointments for the doctor on the selected date
            $appointments = Patient::join('token_booking', 'token_booking.BookedPerson_id', '=', 'patient.UserId')
                ->join('docter', 'docter.UserId', '=', 'token_booking.doctor_id') // Join the doctor table
                ->where('patient.UserId', $doctor->UserId)
                ->orderByRaw('CAST(token_booking.TokenNumber AS SIGNED) ASC')
                ->where('Is_completed', 0)
                ->get(['token_booking.*', 'docter.*']);

            // Initialize an array to store appointments along with doctor details
            $appointmentsWithDetails = [];

            // Iterate through each appointment and add symptoms information
            foreach ($appointments as $appointment) {
                $symptoms = json_decode($appointment->Appoinmentfor_id, true);

                // Extract appointment details
                $appointmentDetails = [
                    'TokenNumber' => $appointment->TokenNumber,
                    'Date' => $appointment->date,
                    'Startingtime' => $appointment->TokenTime,
                    'PatientName' => $appointment->PatientName,
                    'main_symptoms' => Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray(),
                    'other_symptoms' => Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray(),
                ];

                // Extract doctor details from the first appointment (assuming all appointments have the same doctor details)
                $doctorDetails = [
                    'firstname' => $appointment->firstname,
                    'secondname' => $appointment->lastname,
                    'Specialization' => $appointment->specialization,
                    'DocterImage' => asset("DocterImages/images/{$appointment->docter_image}"),
                    'Mobile Number' => $appointment->mobileNo,
                    'MainHospital' => $appointment->Services_at,
                    'subspecification_id' => $appointment->subspecification_id,
                    'specification_id' => $appointment->specification_id,
                    'specifications' => explode(',', $appointment->specifications),
                    'subspecifications' => explode(',', $appointment->subspecifications),
                    'clincs' => [],
                ];

                // Assuming you have a way to retrieve and append clinic details
                // You need to implement a function like getClinics() based on your database structure
                $doctorDetails['clincs'] = $this->getClinics($appointment->clinic_id);

                // Combine appointment and doctor details
                $combinedDetails = array_merge($appointmentDetails, $doctorDetails);

                // Add to the array
                $appointmentsWithDetails[] = $combinedDetails;
            }

            // Return a success response with the appointments and doctor details
            return $this->sendResponse('Appointments', $appointmentsWithDetails, '1', 'Appointments retrieved successfully.');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }




    public  function GetUserCompletedAppoinments(Request $request, $userId)
    {
        try {
            // Get the currently authenticated doctor
            $doctor = Patient::where('UserId', $userId)->first();

            if (!$doctor) {
                return response()->json(['message' => 'Patient not found.'], 404);
            }

            // Validate the date format (if needed)

            // Get all appointments for the doctor on the selected date
            $appointments = Patient::join('token_booking', 'token_booking.BookedPerson_id', '=', 'patient.UserId')
                ->join('docter', 'docter.UserId', '=', 'token_booking.doctor_id') // Join the doctor table
                ->where('patient.UserId', $doctor->UserId)
                ->orderByRaw('CAST(token_booking.TokenNumber AS SIGNED) ASC')
                ->where('Is_completed', 1)
                ->get(['token_booking.*', 'docter.*']);

            // Initialize an array to store appointments along with doctor details
            $appointmentsWithDetails = [];

            // Iterate through each appointment and add symptoms information
            foreach ($appointments as $appointment) {
                $symptoms = json_decode($appointment->Appoinmentfor_id, true);

                // Extract appointment details
                $appointmentDetails = [
                    'TokenNumber' => $appointment->TokenNumber,
                    'Date' => $appointment->date,
                    'Startingtime' => $appointment->TokenTime,
                    'PatientName' => $appointment->PatientName,
                    'main_symptoms' => Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray(),
                    'other_symptoms' => Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray(),
                ];

                // Extract doctor details from the first appointment (assuming all appointments have the same doctor details)
                $doctorDetails = [
                    'firstname' => $appointment->firstname,
                    'secondname' => $appointment->lastname,
                    'Specialization' => $appointment->specialization,
                    'DocterImage' => asset("DocterImages/images/{$appointment->docter_image}"),
                    'Mobile Number' => $appointment->mobileNo,
                    'MainHospital' => $appointment->Services_at,
                    'subspecification_id' => $appointment->subspecification_id,
                    'specification_id' => $appointment->specification_id,
                    'specifications' => explode(',', $appointment->specifications),
                    'subspecifications' => explode(',', $appointment->subspecifications),
                    'clincs' => [],
                ];

                // Assuming you have a way to retrieve and append clinic details
                // You need to implement a function like getClinics() based on your database structure
                $doctorDetails['clincs'] = $this->getClinics($appointment->clinic_id);

                // Combine appointment and doctor details
                $combinedDetails = array_merge($appointmentDetails, $doctorDetails);

                // Add to the array
                $appointmentsWithDetails[] = $combinedDetails;
            }

            // Return a success response with the appointments and doctor details
            return $this->sendResponse('Appointments', $appointmentsWithDetails, '1', 'Appointments retrieved successfully.');
        } catch (\Exception $e) {
            // Handle unexpected errors
            return $this->sendError('Error', $e->getMessage(), 500);
        }
}
}
