<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\DocterAvailability;
use App\Models\schedule;
use App\Models\Specialize;
use App\Models\Specification;
use App\Models\Subspecification;
use App\Models\Symtoms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DocterController extends BaseController
{



    public function index()
    {
        $specializeArray['specialize']=Specialize::all();
        $specificationArray['specification'] = Specification::all();
        $subspecificationArray['subspecification'] = Subspecification::all();

        $docters = Docter::join('docteravaliblity', 'docter.id', '=', 'docteravaliblity.docter_id')
            ->select('docter.UserId', 'docter.id', 'docter.docter_image', 'docter.firstname', 'docter.lastname', 'docter.specialization_id', 'docter.subspecification_id', 'docter.specification_id', 'docter.about', 'docter.location', 'docter.gender', 'docter.email', 'docter.mobileNo', 'docter.Services_at', 'docteravaliblity.id as avaliblityId','docteravaliblity.hospital_Name', 'docteravaliblity.availability')
            ->get();

        $doctersWithSpecifications = [];

        foreach ($docters as $doctor) {
            $id = $doctor['id'];

            if (!isset($doctersWithSpecifications[$id])) {

                $specialize = $specializeArray['specialize']->firstWhere('id', $doctor['specialization_id']);

                $doctersWithSpecifications[$id] = [
                    'id' => $id,
                    'UserId' => $doctor['UserId'],
                    'firstname' => $doctor['firstname'],
                    'secondname' => $doctor['lastname'],
                    'Specialization' => $specialize ? $specialize['specialization'] : null,
                    'DocterImage' => asset("DocterImages/images/{$doctor['docter_image']}"),
                    'About' => $doctor['about'],
                    'Location' => $doctor['location'],
                    'Gender' => $doctor['gender'],
                    'emailID' => $doctor['email'],
                    'Mobile Number' => $doctor['mobileNo'],
                    'MainHospital' => $doctor['Services_at'],
                    'subspecification_id' => $doctor['subspecification_id'],
                    'specification_id' => $doctor['specification_id'],
                    'specifications' => [],
                    'subspecifications' => [],
                    'clincs' => [],
                ];
            }

            $specificationIds = explode(',', $doctor['specification_id']);
            $subspecificationIds = explode(',', $doctor['subspecification_id']);

            $doctersWithSpecifications[$id]['specifications'] = array_merge(
                $doctersWithSpecifications[$id]['specifications'],
                array_map(function ($id) use ($specificationArray) {
                    return $specificationArray['specification']->firstWhere('id', $id)['specification'];
                }, $specificationIds)
            );

            $doctersWithSpecifications[$id]['subspecifications'] = array_merge(
                $doctersWithSpecifications[$id]['subspecifications'],
                array_map(function ($id) use ($subspecificationArray) {
                    return $subspecificationArray['subspecification']->firstWhere('id', $id)['subspecification'];
                }, $subspecificationIds)
            );

            $doctersWithSpecifications[$id]['clincs'][] = [
                'id'   => $doctor['avaliblityId'],
                'name' => $doctor['hospital_Name'],
                'availability' => $doctor['availability'],
            ];
        }

        // Format the output to match the expected structure
        $formattedOutput = array_values($doctersWithSpecifications);

        return $this->sendResponse("Docters", $formattedOutput, '1', 'Docters retrieved successfully.');
    }



    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->all();

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



            $hospitalData = json_decode($input['hospitals'], true); // Decode the JSON string

            // Create DocterAvailability records
            if (is_array($hospitalData)) {
                foreach ($hospitalData as $hospital) {
                    $availabilityData = [
                        'docter_id' => $Docter->id,
                        'hospital_Name' => $hospital['hospitalName'],
                        'availability' => $hospital['availability'],
                    ];

                    // Create and save DocterAvailability records
                    $docterAvailability = new DocterAvailability($availabilityData);
                    $docterAvailability->save();
                }
            }




            DB::commit();

            return $this->sendResponse("Docters", $Docter, '1', 'Docter created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }
    // public function show($id)
    // {
    //     $Docter = Docter::find($id);

    //     if (is_null($Docter)) {
    //         return $this->sendError('Docter not found.');
    //     }

    //     return $this->sendResponse("Docter", $Docter, '1', 'Docter retrieved successfully.');
    // }

    public function show($userId)
    {
        $specificationArray['specification'] = Specification::all();
        $subspecificationArray['subspecification'] = Subspecification::all();


        $docters = Docter::join('docteravaliblity', 'docter.id', '=', 'docteravaliblity.docter_id')
            ->join('users', 'docter.UserId', '=', 'users.id') // Assuming 'UserId' is the foreign key in the 'Docter' table
            ->select('docter.id', 'docter.UserId', 'docter.firstname', 'docter.lastname', 'docter.docter_image', 'docter.subspecification_id', 'docter.specification_id', 'docter.about', 'docter.location', 'docter.gender', 'docter.email', 'docter.mobileNo', 'docter.Services_at','docteravaliblity.id as avaliblityId', 'docteravaliblity.hospital_Name', 'docteravaliblity.availability')
            ->where('users.id', $userId) // Filtering by UserId from the User table
            ->get();

        $doctersWithSpecifications = [];

        foreach ($docters as $doctor) {
            $id = $doctor['id'];

            if (!isset($doctersWithSpecifications[$id])) {
                $doctersWithSpecifications[$id] = [
                    'id' => $id,
                    'UserId' => $doctor['UserId'],
                    'firstname' => $doctor['firstname'],
                    'secondname' => $doctor['lastname'],
                    'DocterImage' => asset("DocterImages/images/{$doctor['docter_image']}"),
                    'About' => $doctor['about'],
                    'Location' => $doctor['location'],
                    'Gender' => $doctor['gender'],
                    'emailID' => $doctor['email'],
                    'Mobile Number' => $doctor['mobileNo'],
                    'MainHospital' => $doctor['Services_at'],
                    'subspecification_id' => $doctor['subspecification_id'],
                    'specification_id' => $doctor['specification_id'],
                    'specifications' => [],
                    'subspecifications' => [],
                    'clincs' => [],
                ];
            }

            $specificationIds = explode(',', $doctor['specification_id']);
            $subspecificationIds = explode(',', $doctor['subspecification_id']);

            $doctersWithSpecifications[$id]['specifications'] = array_merge(
                $doctersWithSpecifications[$id]['specifications'],
                array_map(function ($id) use ($specificationArray) {
                    return $specificationArray['specification']->firstWhere('id', $id)['specification'];
                }, $specificationIds)
            );

            $doctersWithSpecifications[$id]['subspecifications'] = array_merge(
                $doctersWithSpecifications[$id]['subspecifications'],
                array_map(function ($id) use ($subspecificationArray) {
                    return $subspecificationArray['subspecification']->firstWhere('id', $id)['subspecification'];
                }, $subspecificationIds)
            );

            $doctersWithSpecifications[$id]['clincs'][] = [
                'id'  => $doctor['avaliblityId'] ,
                'name' => $doctor['hospital_Name'],
                'availability' => $doctor['availability'],
            ];
        }

        // Format the output to match the expected structure
        $formattedOutput = array_values($doctersWithSpecifications);

        return $this->sendResponse("Docter", $formattedOutput, '1', 'Docter retrieved successfully.');
    }

    // public function update(Request $request, $userId)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $docter = Docter::find($userId);

    //         if (is_null($docter)) {
    //             return $this->sendError('Docter not found.');
    //         }

    //         $input = $request->all();

    //         // Update fields as needed
    //         $docter->firstname = $input['firstname'] ?? $docter->firstname;
    //         $docter->lastname = $input['lastname'] ?? $docter->lastname;
    //         $docter->mobileNo = $input['mobileNo'] ?? $docter->mobileNo;
    //         $docter->email = $input['email'] ?? $docter->email;
    //         $docter->location = $input['location'] ?? $docter->location;

    //         // Handle image upload if a new image is provided
    //         if ($request->hasFile('docter_image')) {
    //             $imageFile = $request->file('docter_image');

    //             if ($imageFile->isValid()) {
    //                 $imageName = $imageFile->getClientOriginalName();
    //                 $imageFile->move(public_path('DocterImages/images'), $imageName);

    //                 $docter->docter_image = $imageName;
    //             }
    //         }

    //         $docter->save();

    //         $user = User::find($docter->UserId);

    //     if (!is_null($user)) {
    //         $user->firstname = $input['firstname'] ?? $user->firstname;
    //         $user->secondname = $input['lastname'] ?? $user->secondname;
    //         $user->mobileNo = $input['mobileNo'] ?? $user->mobileNo;
    //         $user->email = $input['email'] ?? $user->email;
    //         $user->save();
    //     }

    //         DB::commit();

    //         return $this->sendResponse("Docter", $docter, '1', 'Docter updated successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
    //     }
    // }


    public function update(Request $request, $userId)
    {
        try {
            DB::beginTransaction();


            $docter = Docter::where('UserId', $userId)->first();

            if (is_null($docter)) {
                return $this->sendError('Docter not found.');
            }

            $input = $request->all();

            // Update fields as needed
            $docter->firstname = $input['firstname'] ?? $docter->firstname;
            $docter->lastname = $input['lastname'] ?? $docter->lastname;
            $docter->mobileNo = $input['mobileNo'] ?? $docter->mobileNo;
            $docter->email = $input['email'] ?? $docter->email;
            $docter->location = $input['location'] ?? $docter->location;

            // Handle image upload if a new image is provided
            if ($request->hasFile('docter_image')) {
                $imageFile = $request->file('docter_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('DocterImages/images'), $imageName);

                    $docter->docter_image = $imageName;
                }
            }

            $docter->save();

            $user = User::find($docter->UserId);

            if (!is_null($user)) {
                $user->firstname = $input['firstname'] ?? $user->firstname;
                $user->secondname = $input['lastname'] ?? $user->secondname;
                $user->mobileNo = $input['mobileNo'] ?? $user->mobileNo;
                $user->email = $input['email'] ?? $user->email;
                $user->save();
            }

            DB::commit();

            // Include UserId in the response
            $response = [
                'success' => true,
                'UserId' => $user->id,
                'Docter' => $docter,
                'code' => '1',
                'message' => 'Docter updated successfully.'
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $errorMessages = [], $code = 404);
        }
    }



    //get the docter by specialization
    public function getDoctorsBySpecialization($specializationId)
    {
        $specialization = Specialize::findOrFail($specializationId);
        $doctors = $specialization->doctors;

        return response()->json($doctors);
    }




    public function getHospitalName($userId)
    {
        // Query the Docter table to get the doctor's details based on the provided UserId
        $doctor = Docter::where('UserId', $userId)->first();

        if (is_null($doctor)) {
            return response()->json(['error' => 'Doctor not found for the given UserId'], 404);
        }

        // Retrieve the doctor id associated with the doctor
        $doctorId = $doctor->id;

        // Query the DocterAvailability table to get all hospital details for the doctor
        $hospitalDetails = DocterAvailability::where('docter_id', $doctorId)->get();

        if ($hospitalDetails->isEmpty()) {
            return response()->json(['error' => 'Hospital details not found for the selected doctor'], 404);
        }

        // Combine doctor details with hospital details
        $result = [

            'hospital_details' => $hospitalDetails,
        ];

        return response()->json($result);
    }

    public function ApproveOrReject(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $action = $request->input('action'); // 'approve' or 'reject'

        $doctor = Docter::find($doctorId);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        // Update the is_approve column based on the action
        if ($action == 'approve') {
            $doctor->is_approve = 1;
        } elseif ($action == 'reject') {
            $doctor->is_approve = 2;
        }

        $doctor->save();

        return response()->json(['message' => 'Doctor ' . ucfirst($action) . 'd successfully']);
    }
    public function getSymptomsBySpecialization($userId)
    {
        // Find the doctor by user ID
        $doctor = Docter::where('UserId', $userId)->first();

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found.'], 404);
        }

        // Use a join to fetch symptoms based on the doctor's specialization and specialization_id in symtoms table
        $symptoms = Symtoms::join('docter', 'symtoms.specialization_id', '=', 'docter.specialization_id')
            ->where('docter.UserId', $doctor->UserId) // Assuming 'UserId' is the correct column name in 'docter' table
            ->get(['symtoms.*']); // Select only the columns from the 'symtoms' table

        return response()->json(['symptoms' => $symptoms], 200);
    }

    public function getTokens(Request $request)
    {
        $rules = [
            'doctor_id'     => 'required',
            'hospital_id'   => 'required',
            'date'          => 'required',
        ];
        $messages = [
            'date.required' => 'Date is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $docter = Docter::where('id', $request->doctor_id)->first();
            if (!$docter) {
                return response()->json(['status' => false, 'message' => 'Doctor not found']);
            }
            $shedulded_tokens =  schedule::where('docter_id', $request->doctor_id)->where('hospital_Id', $request->hospital_id)->first();
            $requestDate = Carbon::parse($request->date);
            $startDate = Carbon::parse($shedulded_tokens->date);
            $scheduledUptoDate = Carbon::parse($shedulded_tokens->scheduleupto);
            // Get the day of the week
            $dayOfWeek = $requestDate->format('l'); // 'l' format gives the full name of the day
            $allowedDaysArray = json_decode($shedulded_tokens->selecteddays);


            if (!$requestDate->between($startDate, $scheduledUptoDate)) {
                return response()->json(['status' => false, 'message' => 'Token not found on this date']);
            }

            if (!in_array($dayOfWeek, $allowedDaysArray)) {
                return response()->json(['status' => false, 'message' => 'Token not found on this day']);
            }
            $shedulded_tokens =  schedule::select('id','tokens','date','hospital_Id','startingTime','endingTime')->where('docter_id', $request->doctor_id)->where('hospital_Id', $request->hospital_id)->first();
            $shedulded_tokens['tokens'] = json_decode($shedulded_tokens->tokens);

            return response()->json(['status' => true, 'token_data' => $shedulded_tokens]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => "Internal Server Error"]);
        }
    }
}
