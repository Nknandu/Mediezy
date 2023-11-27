<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\Medicine;
use App\Models\Symtoms;
use App\Models\TokenBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TokenBookingController extends BaseController
{


    // public function bookToken(Request $request)
    // {
    //     // Validate request data
    //     $this->validate($request, [
    //         'BookedPerson_id' => 'required',
    //         'PatientName' => 'required',
    //         'gender' => 'required',
    //         'age' => 'required',
    //         'MobileNo' => 'required',
    //         'Appoinmentfor' => 'required|array',
    //         'date' => 'required|date_format:Y-m-d',
    //         'TokenNumber' => 'required',
    //         'TokenTime' => 'required',
    //         'whenitstart' => 'required',
    //         'whenitcomes' => 'required',
    //         'regularmedicine' => 'required',
    //         'doctor_id'=> 'required',
    //     ]);

    //     // Check if the user is a doctor
    //     $isDoctor = $request->has('doctor_id');
    //     $specializationId = null;

    //     if ($isDoctor) {
    //         // If the user is a doctor, get the specialization_id from the doctor's record
    //         $specializationId = Docter::where('id', $request->input('doctor_id'))->value('specialization_id');
    //     }
    //     // Find or create symptoms
    //     $symptomIds = [];
    //     foreach ($request->input('Appoinmentfor') as $symptomName) {
    //         $symptom = Symtoms::firstOrNew(['symtoms' => $symptomName]);

    //         if (!$symptom->exists) {
    //             $symptom->specialization_id = $specializationId;
    //             $symptom->save();
    //         }

    //         $symptomIds[] = $symptom->id;
    //     }
    //     // Update the request with the symptoms IDs as a comma-separated string
    //     $request->merge(['Appoinmentfor_id' => implode(',', $symptomIds)]);

    //     // Create a new token booking with the current time
    //     $tokenBooking = DB::transaction(function () use ($request, $isDoctor) {
    //         $bookingData = [
    //             'BookedPerson_id' => $request->input('BookedPerson_id'),
    //             'PatientName' => $request->input('PatientName'),
    //             'gender' => $request->input('gender'),
    //             'age' => $request->input('age'),
    //             'MobileNo' => $request->input('MobileNo'),
    //             'Appoinmentfor_id' => $request->input('Appoinmentfor_id'),
    //             'date' => $request->input('date'),
    //             'TokenNumber' => $request->input('TokenNumber'),
    //             'TokenTime' => $request->input('TokenTime'),
    //             'doctor_id' =>$request->input('doctor_id'),
    //             'Bookingtime' => now(),
    //         ];


    //         return TokenBooking::create($bookingData);
    //     });

    //     // Return a success response
    //     return $this->sendResponse("TokenBooking", $tokenBooking, '1', 'Token Booked successfully.');
    // }


    public function bookToken(Request $request)
    {
        // Validate request data
        $this->validate($request, [
            'BookedPerson_id' => 'required',
            'PatientName' => 'required',
            'gender' => 'required',
            'age' => 'required',
            'MobileNo' => 'required',
            'date' => 'required|date_format:Y-m-d',
            'TokenNumber' => 'required',
            'TokenTime' => 'required',
            'whenitstart' => 'required',
            'whenitcomes' => 'required',
            'regularmedicine' => 'required',
            'doctor_id' => 'required',
            'Appoinmentfor1' => 'required|array',
            'Appoinmentfor2' => 'required|array',
        ]);


        $isDoctor = $request->has('doctor_id');
        $specializationId = null;

        if ($isDoctor) {

            $specializationId = Docter::where('id', $request->input('doctor_id'))->value('specialization_id');
        }


        $symptomIds1 = [];

        foreach ($request->input('Appoinmentfor1') as $symptomName) {
            $symptom = Symtoms::firstOrNew(['symtoms' => $symptomName]);

            if (!$symptom->exists) {
                $symptom->specialization_id = $specializationId;
                $symptom->save();
            }

            $symptomIds1[] = $symptom->id;
        }


        $symptomIds2 = array_map('intval', $request->input('Appoinmentfor2'));

        foreach ($symptomIds2 as $symptomId) {
            $symptom = Symtoms::find($symptomId);
            if (!$symptom) {
                // Display a message or take appropriate action
                return $this->sendError('Invalid Appoinmentfor2 ID', 'The specified Appoinmentfor2 ID does not exist in the symptoms table.', 400);
            }
        }

        $existingSymptoms2 = Symtoms::whereIn('id', $symptomIds2)->get();



        // Create a new token booking with the current time
        $tokenBooking = DB::transaction(function () use ($request, $isDoctor, $symptomIds1, $symptomIds2) {
            $bookingData = [
                'BookedPerson_id' => $request->input('BookedPerson_id'),
                'PatientName' => $request->input('PatientName'),
                'gender' => $request->input('gender'),
                'age' => $request->input('age'),
                'MobileNo' => $request->input('MobileNo'),
                'Appoinmentfor_id' => json_encode(['Appoinmentfor1' => $symptomIds1, 'Appoinmentfor2' => $symptomIds2]),
                'date' => $request->input('date'),
                'TokenNumber' => $request->input('TokenNumber'),
                'TokenTime' => $request->input('TokenTime'),
                'doctor_id' => $request->input('doctor_id'),
                'Bookingtime' => now(),
            ];

            return TokenBooking::create($bookingData);
        });

        // Return a success response
        return $this->sendResponse("TokenBooking", $tokenBooking, '1', 'Token Booked successfully.');
    }



    public function GetallAppointmentOfDocter()
    {
    }

    public function appointmentDetails(Request $request)
    {
        $rules = [
            'token_id'   => 'required',
        ];
        $messages = [
            'token_id.required' => 'Token is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $tokenId = $request->token_id;
            $booking = TokenBooking::select('id', 'date', 'TokenTime', 'Appoinmentfor_id', 'whenitstart', 'whenitcomes', 'attachment', 'notes')->where('id', $tokenId)->first();
            if (!$booking) {
                return response()->json(['status' => false, 'response' => "Booking not found"]);
            }
            $symptoms = json_decode($booking->Appoinmentfor_id, true);
            $booking['main_symptoms'] = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray();
            $booking['other_symptoms'] = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray();
            $booking['medicine']       = Medicine::where('token_id', $tokenId)->get();
            return response()->json(['status' => true, 'booking_data' => $booking, 'message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'response' => "Internal Server Error"]);
        }
    }

    public function addPrescription(Request $request)
    {
        $rules = [
            'token_id' => 'required',
            'medicine_name' => 'sometimes', // This makes medicine_name not required
            'dosage' => 'required_with:medicine_name',
            'no_of_days' => 'required_with:medicine_name',
            'type' => 'required_with:medicine_name|in:1,2',
            'night' => 'required_with:medicine_name|in:0,1',
            'morning' => 'required_with:medicine_name|in:0,1',
            'noon' => 'required_with:medicine_name|in:0,1',
            // Other validation rules for other fields, if needed
        ];
        $messages = [
            'token_id.required' => 'Token is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $tokenPrescription  = TokenBooking::where('id', $request->token_id)->first();

            if ($request->medicine_name) {
                $medicine  = new Medicine();
                $medicine->token_id     = $request->token_id;
                $medicine->medicineName = $request->medicine_name;
                $medicine->Dosage       = $request->dosage;
                $medicine->NoOfDays     = $request->no_of_days;
                $medicine->Noon         = $request->noon;
                $medicine->morning      = $request->morning;
                $medicine->night        = $request->night;
                $medicine->type         = $request->type;
                $medicine->save();
            }
            if ($request->notes) {
                $medicine->notes         = $request->notes;
                $medicine->save();
            }
            if ($request->hasFile('attachment')) {
                $imageFile = $request->file('attachment');
                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('bookings/attachments'), $imageName);
                    $tokenPrescription->attachment = $imageName;
                    $tokenPrescription->save();
                }
            }
            return response()->json(['status' => true, 'message' => 'Medicine added .']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'response' => "Internal Server Error"]);
        }
    }








}
