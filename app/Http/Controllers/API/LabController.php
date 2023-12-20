<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\FavouriteLab;
use App\Models\Laboratory;
use App\Models\LabTest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LabController extends BaseController
{

    public function LabRegister(Request $request)
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
                'Type' => 'sometimes|in:1,2' // 1 for lab, 2 for scanning
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $emailExists = Laboratory::where('email', $input['email'])->count();
            $emailExistsinUser = User::where('email', $input['email'])->count();

            if ($emailExists && $emailExistsinUser) {
                return $this->sendResponse("Laboratory", null, '3', 'Email already exists.');
            }

            $input['password'] = Hash::make($input['password']);

            $userId = DB::table('users')->insertGetId([
                'firstname' => $input['firstname'],
                'secondname' => $input['Type'] == 1 ? 'Laboratory' : 'ScanningCenter',
                'email' => $input['email'],
                'mobileNo' => $input['mobileNo'],
                'password' => $input['password'],
                'user_role' => 4,
            ]);

            $DocterData = [
                'firstname' => $input['firstname'],
                'mobileNo' => $input['mobileNo'],
                'email' => $input['email'],
                'location' => $input['location'],
                'address' => $input['address'],
                'Type' => $input['Type'],
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



    public function LabTest(Request $request)
    {
        try {
            // Validate request data
            $this->validate($request, [
                'lab_id' => 'required',
                'TestName' => 'required',
                'TestDescription' => 'sometimes',
                'Test_price' => 'required',
                'discount' => 'sometimes',
            ]);

            // Extract data from the request
            $lab_id = $request->input('lab_id');
            $TestName = $request->input('TestName');
            $TestDescription = $request->input('TestDescription');
            $Test_price = $request->input('Test_price');
            $discount = $request->input('discount');

            // Check if discount is provided
            if ($discount !== null) {
                $Total_price = $Test_price - ($Test_price * $discount / 100);
            } else {
                $Total_price = $Test_price;
            }

            $MedicineData = [
                'lab_id' => $lab_id,
                'TestName' => $TestName,
                'TestDescription' => $TestDescription,
                'Test_price' => $Test_price,
                'discount' => $discount,
                'Total_price' => $Total_price,
            ];

            // Upload and save the image if provided
            if ($request->hasFile('test_image')) {
                $imageFile = $request->file('test_image');

                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('LabImages/Test'), $imageName);

                    $MedicineData['test_image'] = $imageName;
                }
            }

            // Save the data to the database
            $Medicine = new LabTest($MedicineData);
            $Medicine->save();

            // Return success response
            return $this->sendResponse('MedicineProduct', $MedicineData, '1', 'Medicine added successfully.');
        } catch (ValidationException $e) {
            // Handle validation errors
            return $this->sendError('Validation Error', $e->errors(), 422);
        } catch (QueryException $e) {
            // Handle database query errors
            return $this->sendError('Database Error', $e->getMessage(), 500);
        } catch (\Exception $e) {
            // Handle other unexpected errors
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }
    //get all the lab for docter App
    public function GetLabForDoctors()
    {
        if (Auth::check()) {
            $loggedInDoctorId = Auth::user()->id;

            $Laboratories = Laboratory::where('Type', 1)->get();
            $LaboratoryDetails = [];

            foreach ($Laboratories as $Laboratory) {
                // Check favorite status for each laboratory
                $favoriteStatus = DB::table('favouriteslab')
                    ->where('doctor_id', $loggedInDoctorId)
                    ->where('lab_id', $Laboratory->id)
                    ->exists();

                $LaboratoryDetails[] = [
                    'id' => $Laboratory->id,
                    'UserId' => $Laboratory->UserId,
                    'Laboratory' => $Laboratory->firstname,
                    'Laboratoryimage' => asset("LabImages/{$Laboratory->lab_image}"),
                    'mobileNo' => $Laboratory->mobileNo,
                    'location' => $Laboratory->location,
                    'favoriteStatus' => $favoriteStatus ? 1 : 0,
                ];
            }

            return $this->sendResponse("Laboratory", $LaboratoryDetails, '1', 'Laboratory retrieved successfully');
        }
    }

    public function GetScanningForDoctors()
    {
        if (Auth::check()) {
            $loggedInDoctorId = Auth::user()->id;

            $Laboratories = Laboratory::where('Type', 2)->get();
            $LaboratoryDetails = [];

            foreach ($Laboratories as $Laboratory) {
                // Check favorite status for each laboratory
                $favoriteStatus = DB::table('favouriteslab')
                    ->where('doctor_id', $loggedInDoctorId)
                    ->where('lab_id', $Laboratory->id)
                    ->exists();

                $LaboratoryDetails[] = [
                    'id' => $Laboratory->id,
                    'UserId' => $Laboratory->UserId,
                    'Laboratory' => $Laboratory->firstname,
                    'Laboratoryimage' => asset("LabImages/{$Laboratory->lab_image}"),
                    'mobileNo' => $Laboratory->mobileNo,
                    'location' => $Laboratory->location,
                    'favoriteStatus' => $favoriteStatus ? 1 : 0,
                ];
            }

            return $this->sendResponse("Laboratory", $LaboratoryDetails, '1', 'Laboratory retrieved successfully');
        }
    }



    public function GetAllLabs()
    {
        $Laboratories = Laboratory::where('Type', 1)->get();
        $LaboratoryDetails = [];

        foreach ($Laboratories as $Laboratory) {

            $LaboratoryDetails[] = [
                'id' => $Laboratory->id,
                'UserId' => $Laboratory->UserId,
                'Laboratory' => $Laboratory->firstname,
                'Laboratoryimage' => asset("LabImages/{$Laboratory->lab_image}"),
                'mobileNo' => $Laboratory->mobileNo,
                'location' => $Laboratory->location,

            ];
        }

        return $this->sendResponse("Laboratory", $LaboratoryDetails, '1', 'Laboratory retrieved successfully');
    }


    public function addFavouirtesLab(Request $request)
    {
        $doctorId = $request->doctor_id;
        $labId = $request->lab_id;

        $laboratory = Laboratory::find($labId);

        if (!$laboratory) {
            return response()->json(['error' => 'Laboratory not found'], 404);
        }

        // Check if the laboratory is already a favorite for the doctor
        $existingFavorite = FavouriteLab::where('lab_id', $labId)
            ->where('doctor_id', $doctorId)
            ->first();

        if ($existingFavorite) {
            // Laboratory is already a favorite for the doctor
            return response()->json(['status' => false, 'message' => 'Laboratory is already saved as a favorite.']);
        }

        // If not already a favorite, add it to the favorites list
        $addFavorite = new FavouriteLab();
        $addFavorite->lab_id = $labId;
        $addFavorite->doctor_id = $doctorId;
        $addFavorite->save();

        return response()->json(['status' => true, 'message' => 'Laboratory added to favorites successfully.']);
    }

    public function RemoveFavouirtesLab(Request $request)
    {
        $docterId = $request->doctor_id;
        $LabId = $request->lab_id;
        $Laboratory = Laboratory::find($LabId);

        if (!$Laboratory) {
            return response()->json(['error' => 'Laboratory not found'], 404);
        }
        $existingFavourite = FavouriteLab::where('lab_id', $LabId)
            ->where('doctor_id', $docterId)
            ->first();

        if ($existingFavourite) {
            FavouriteLab::where('doctor_id', $docterId)->where('lab_id', $LabId)->delete();
            return response()->json(['status' => true, 'message' => 'favourite Removed successfully .']);
        }
    }

    public function getFavlab()
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $loggedInDoctorId = Auth::user()->id;

            $favoriteLabs = FavouriteLab::leftJoin('laboratory', 'laboratory.id', '=', 'favouriteslab.lab_id')
                ->where('doctor_id', $loggedInDoctorId)
                ->select('laboratory.*')
                ->get();

            $LaboratoryDetails = [];

            foreach ($favoriteLabs as $Laboratory) {
                $LaboratoryDetails[] = [
                    'id' => $Laboratory->id,
                    'UserId' => $Laboratory->UserId,
                    'Laboratory' => $Laboratory->firstname,
                    'Laboratoryimage' => asset("LabImages/{$Laboratory->lab_image}"),
                    'mobileNo' => $Laboratory->mobileNo,
                    'location' => $Laboratory->location,
                ];
            }

            return response()->json(['status' => true, 'message' => 'Favorite labs retrieved successfully.', 'favoriteLabs' => $LaboratoryDetails]);
        } else {
            return response()->json(['status' => false, 'message' => 'User not authenticated.']);
        }
    }
}
