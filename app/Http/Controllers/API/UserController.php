<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\DocterAvailability;
use App\Models\Favouritestatus;
use App\Models\Patient;
use App\Models\Symtoms;
use App\Models\User;
use App\Models\Specialize;
use App\Models\Specification;
use App\Models\Subspecification;
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

    public function UserEdit($userId)
    {
        $userDetails = Patient::where('UserId', $userId)->get();

        if ($userDetails->isEmpty()) {
            $response = ['message' => 'User not found with the given UserId'];
            return response()->json($response, 404);
        }

        return $this->sendResponse('Userdetails', $userDetails, '1', 'User retrieved successfully.');
    }


    public function updateUserDetails(Request $request, $userId)
    {
        try {
            DB::beginTransaction();

            // Validate input
            $request->validate([
                'firstname' => 'required|string',
                'secondname' => 'required|string',
                'email' => 'required|email',
                'mobileNo' => 'required|string',
                'location' => 'required|string',
                'gender' => 'required|string',
                'user_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Check if the user exists
            $user = User::find($userId);

            if (!$user) {
                return $this->sendResponse(null, null, '2', 'User not found.');
            }

            // Update user details
            $user->firstname = $request->input('firstname');
            $user->secondname = $request->input('secondname');
            $user->email = $request->input('email');
            $user->mobileNo = $request->input('mobileNo');
            $user->save();

            // Update patient details
            $patient = Patient::where('UserId', $userId)->first();

            if (!$patient) {
                return $this->sendResponse(null, null, '3', 'Patient not found.');
            }

            $patient->firstname = $request->input('firstname');
            $patient->lastname = $request->input('secondname');
            $patient->mobileNo = $request->input('mobileNo');
            $patient->email = $request->input('email');
            $patient->location = $request->input('location');
            $patient->gender = $request->input('gender');

            if ($request->hasFile('user_image')) {
                $imageFile = $request->file('user_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('UserImages'), $imageName);

                    $patient->user_image = $imageName;
                }
            }

            $patient->save();

            DB::commit();

            return $this->sendResponse("users", $user, '1', 'User details updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }


    // public function UserLogin(Request $req)
    // {
    //     // validate inputs
    //     $rules = [
    //         'email' => 'required',
    //         'password' => 'required|string'
    //     ];
    //     $req->validate($rules);
    //     // find user email in users table
    //     $user = User::where('email', $req->email)->first();

    //     // if user email found and password is correct
    //     if ($user && Hash::check($req->password, $user->password)) {
    //         $token = $user->createToken('Personal Access Token')->plainTextToken;
    //         $response = ['user' => $user, 'token' => $token];
    //         return response()->json($response, 200);
    //     }
    //     $response = ['message' => 'Incorrect email or password'];
    //     return response()->json($response, 400);
    // }


    private function getClinics($doctorId)
    {
        // Replace this with your actual logic to retrieve clinic details from the database
        // You may use Eloquent queries or another method based on your application structure
        $clinics = DocterAvailability::where('docter_id', $doctorId)->get(['id', 'hospital_Name', 'availability']);

        return $clinics;
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



    public function favouritestatus(Request $request)
    {
        $userId = $request->user_id;
        $docterId = $request->docter_id;

        $docter = Docter::find($docterId);

        if (!$docter) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Check if the user has already added the doctor to favorites
        $existingFavourite = Favouritestatus::where('UserId', $userId)
            ->where('doctor_id', $docterId)
            ->first();

        if ($existingFavourite) {
            Favouritestatus::where('doctor_id', $docterId)->where('UserId', $userId)->delete();
            return response()->json(['status' => true, 'message' => 'favourite Removed successfully .']);
        } else {
            // If not, create a new entry in the addfavourites table
            $addfav = new Favouritestatus();
            $addfav->UserId = $userId;
            $addfav->doctor_id = $docterId;
            $addfav->save();
        }

        return response()->json(['status' => true, 'message' => 'favourite added successfully .']);
    }


    public function getallfavourites($id)
    {
        $specializeArray['specialize'] = Specialize::all();
        $specificationArray['specification'] = Specification::all();
        $subspecificationArray['subspecification'] = Subspecification::all();

        // Get all favorite doctors for the given user ID
        $favoriteDoctors = Favouritestatus::where('UserId', $id)->get();

        $favoriteDoctorsWithSpecifications = [];

        foreach ($favoriteDoctors as $favoriteDoctor) {
            // Fetch details for each favorite doctor
            $doctor = Docter::Leftjoin('docteravaliblity', 'docter.id', '=', 'docteravaliblity.docter_id')
                ->where('docter.UserId', $favoriteDoctor->doctor_id)
                ->first();
            $specialize = $specializeArray['specialize']->firstWhere('id', $doctor['specialization_id']);
            if ($doctor) {
                $id = $doctor->id;

                // Initialize doctor details if not already present
                if (!isset($favoriteDoctorsWithSpecifications[$id])) {
                    $favoriteDoctorsWithSpecifications[$id] = [
                        'id' => $id,
                        'UserId' => $doctor->UserId,
                        'firstname' => $doctor->firstname,
                        'secondname' => $doctor->lastname,
                        'Specialization' => $specialize ? $specialize['specialization'] : null,
                        'DocterImage' => asset("DocterImages/images/{$doctor->docter_image}"),
                        'Location' => $doctor->location,
                        'MainHospital' => $doctor->Services_at,

                    ];
                }
            }
        }

        // Format the output to match the expected structure
        $formattedOutput = array_values($favoriteDoctorsWithSpecifications);

        return $this->sendResponse('Favorite Doctors', $formattedOutput, '1', 'Favorite doctors retrieved successfully.');
    }
}
