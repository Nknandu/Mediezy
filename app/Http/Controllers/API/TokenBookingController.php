<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\DocterAvailability;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Symtoms;
use App\Models\TokenBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TokenBookingController extends BaseController
{




    public function bookToken(Request $request)
    {
        try {
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
                'clinic_id'=> 'required',
                'Bookingtype'=> 'sometimes|in:1,2,3' //1 for self,2 for familymember ,3 for others
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

            // Check if the patient already exists
            $existingPatient = Patient::where('firstname', $request->input('PatientName'))
                ->where('mobileNo', $request->input('MobileNo'))
                ->first();

            if ($existingPatient) {
                // If patient exists, use existing patient ID
                $patientId = $existingPatient->id;
            } else {
                // If patient doesn't exist, create a new patient
                $userId = DB::table('users')->insertGetId([
                    'firstname' => $request->input('PatientName'),
                    'mobileNo' => $request->input('MobileNo'),
                    'user_role' => 3,
                ]);

                $patientId = DB::table('patient')->insertGetId([
                    'firstname' => $request->input('PatientName'),
                    'mobileNo' => $request->input('MobileNo'),
                    'user_type'=>$request->input('Bookingtype'),
                    'UserId' => $userId,
                ]);
            }

            // Create a new token booking with the current time
            $tokenBooking = DB::transaction(function () use ($request, $isDoctor, $symptomIds1, $symptomIds2, $patientId) {
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
                    'whenitstart' => $request->input('whenitstart'),
                    'whenitcomes' => $request->input('whenitcomes'),
                    'regularmedicine' => $request->input('regularmedicine'),
                    'Bookingtime' => now(),
                    'patient_id' => $patientId,
                    'clinic_id' => $request->input('clinic_id')
                ];

                return TokenBooking::create($bookingData);
            });

            // Return a success response
            return $this->sendResponse("TokenBooking", $tokenBooking, '1', 'Token Booked successfully.');
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



    private function getClinics($doctorId)
    {
        // Replace this with your actual logic to retrieve clinic details from the database
        // You may use Eloquent queries or another method based on your application structure
        $clinics = DocterAvailability::where('docter_id', $doctorId)->get(['id', 'hospital_Name', 'startingTime','endingTime','address','location']);

        return $clinics;
    }

    public function GetallAppointmentOfDocter($userId, $date)//datewise where completed is 0
    {
        try {
            // Get the currently authenticated doctor
            $doctor = Docter::where('UserId', $userId)->first();

            if (!$doctor) {
                return response()->json(['message' => 'Patient not found.'], 404);
            }

            // Validate the date format (if needed)

            // Get all appointments for the doctor on the selected date
            $appointments = Docter::join('token_booking', 'token_booking.doctor_id', '=', 'docter.UserId')
            ->where('docter.UserId', $doctor->UserId)
            ->whereDate('token_booking.date', $date)
            ->orderByRaw('CAST(token_booking.TokenNumber AS SIGNED) ASC')
            ->where('Is_completed',0)
            ->get(['token_booking.*']);


            // Initialize an array to store appointments along with doctor details
            $appointmentsWithDetails = [];

            // Iterate through each appointment and add symptoms information
            foreach ($appointments as $appointment) {
                $symptoms = json_decode($appointment->Appoinmentfor_id, true);

                // Extract appointment details
                $appointmentDetails = [
                    'id'=>$appointment->id,
                    'TokenNumber' => $appointment->TokenNumber,
                    'Date' => $appointment->date,
                    'Startingtime' => $appointment->TokenTime,
                    'PatientName' => $appointment->PatientName,
                    'Age' => $appointment->age,
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



    public function GetallAppointmentOfDocterCompleted($userId, $date)//datewise where completed is 1
    {
        try {
            // Get the currently authenticated doctor
            $doctor = Docter::where('UserId', $userId)->first();

            if (!$doctor) {
                return response()->json(['message' => 'Patient not found.'], 404);
            }

            // Validate the date format (if needed)

            // Get all appointments for the doctor on the selected date
            $appointments = Docter::join('token_booking', 'token_booking.doctor_id', '=', 'docter.UserId')
            ->where('docter.UserId', $doctor->UserId)
            ->whereDate('token_booking.date', $date)
            ->orderByRaw('CAST(token_booking.TokenNumber AS SIGNED) ASC')
            ->where('Is_completed',1)
            ->get(['token_booking.*']);


            // Initialize an array to store appointments along with doctor details
            $appointmentsWithDetails = [];

            // Iterate through each appointment and add symptoms information
            foreach ($appointments as $appointment) {
                $symptoms = json_decode($appointment->Appoinmentfor_id, true);

                // Extract appointment details
                $appointmentDetails = [
                    'id'=>$appointment->id,
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
            $mainSymptoms = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray();
            $otherSymptoms = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray();
            $booking['main_symptoms'] = array_merge($mainSymptoms, $otherSymptoms);
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
