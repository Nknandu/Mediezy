<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Banner;
use App\Models\DocterAvailability;
use App\Models\Patient;
use App\Models\Symtoms;
use Illuminate\Http\Request;

class AppoinmentsController extends BaseController
{

    private function getClinics($doctorId)
    {
        // Replace this with your actual logic to retrieve clinic details from the database
        // You may use Eloquent queries or another method based on your application structure
        $clinics = DocterAvailability::where('docter_id', $doctorId)->get(['id', 'hospital_Name', 'availability']);

        return $clinics;
    }
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
}
