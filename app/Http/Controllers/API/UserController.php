<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\DocterAvailability;
use App\Models\Favouritestatus;
use App\Models\LabReport;
use App\Models\Patient;
use App\Models\PatientDocument;
use App\Models\PatientPrescriptions;
use App\Models\Symtoms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use League\CommonMark\Node\Block\Document;

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
            // If the doctor is already in favorites, toggle the status
            $existingFavourite->favouritestatus = !$existingFavourite->favouritestatus;
            $existingFavourite->save();
        } else {
            // If not, create a new entry in the addfavourites table
            $addfav = new Favouritestatus();
            $addfav->UserId = $userId;
            $addfav->doctor_id = $docterId;
            $addfav->favouritestatus = true; // Assuming you want to set the initial status to 1
            $addfav->save();
        }

        return response()->json(['status' => 'success', 'favouritesstatus' => $existingFavourite ? $existingFavourite->favouritesstatus : true]);
    }


    public function getallfavourites($id)
    {

        $GetallFav = Favouritestatus::where('UserId', $id)->get();
        return $this->sendResponse('favourites', $GetallFav, '1', 'favourite retrieved successfully.');
    }
    public function uploadDocument(Request $request)
    {
        $rules = [
            'user_id'     => 'required',
            'document'    => 'required|mimes:doc,docx,pdf,jpeg,png,jpg|max:2048'
        ];
        $messages = [
            'document.required' => 'Document is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $patient_doc = new PatientDocument();
            $patient_doc->user_id = $request->user_id;
            if ($request->hasFile('document')) {
                $imageFile = $request->file('document');
                if ($imageFile->isValid()) {
                    $imageName = $imageFile->getClientOriginalName();
                    $imageFile->move(public_path('user/documents'), $imageName);
                    $patient_doc->document = $imageName;
                }
            }
            $patient_doc->save();
            return response()->json(['status' => true, 'response' => "File Added"]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'response' => "Internal Server Error"]);
        }
    }

    public function updateDocument(Request $request)
    {
        $rules = [
            'user_id'        => 'required',
            'document_id'    => 'required',
            'type'           => 'required|in:1,2',
            'test_name'      => 'required_if:type,1',
            'lab_name'       => 'required_if:type,1',
            'doctor_name'    => 'required_if:type,1,2',
            'date'           => 'required_if:type,1,2',
        ];
        $messages = [
            'document_id.required' => 'DocumentId is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            DB::beginTransaction();
            $document = PatientDocument::where('user_id', $request->user_id)->where('id', $request->document_id)->first();
            if (!$document) {
                return response()->json(['status' => false, 'message' => 'Document not found']);
            }
            $this->updateDocumentType($request, $document);
            DB::commit();
            return response()->json(['status' => true, 'response' => "File Updated"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'response' => "Internal Server Error"]);
        }
    }

    private function updateDocumentType(Request $request, PatientDocument $document)
    {
        $type = $request->type;

        if ($type == '1' || $type == '2') {
            $model = ($type == '1') ? LabReport::class : PatientPrescriptions::class;
            $record = $model::where('user_id', $request->user_id)->where('document_id', $request->document_id)->first();

            if (!$record) {
                $record = new $model();
            }
            $record->user_id = $request->user_id;
            $record->document_id = $request->document_id;
            $record->date = $request->date;
            $record->doctor_name = $request->doctor_name;

            if ($request->notes) {
                $record->notes = $request->notes;
            }

            if ($request->file_name) {
                $this->updateDocumentFile($request, $document, $record);
            }
            $record->save();
            $document->status = 1 ;
            $document->type = $type;
            $document->save();
        }
    }

    private function updateDocumentFile(Request $request, PatientDocument $document, $record)
    {
        $oldFilePath = public_path('user/documents/' . $document->document);

        if (!File::exists($oldFilePath)) {
            return response()->json(['status' => false, 'response' => 'File not found']);
        }

        $newFileName = $request->file_name;
        $newFileNameWithExtension = $newFileName . '.' . pathinfo($oldFilePath, PATHINFO_EXTENSION);
        $newFilePath = public_path('user/documents/' . $newFileNameWithExtension);

        // Move the file to the new name
        File::move($oldFilePath, $newFilePath);

        // Update the file name in the database
        $record->file_name = $newFileName;
        $document->document = $newFileNameWithExtension;
        $document->save();
    }

    public function getUploadedDocuments(Request $request)
    {
        $rules = [
            'user_id'     => 'required',
        ];
        $messages = [
            'user_id.required' => 'UserId is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $patient_doc = PatientDocument::select('id', 'user_id', 'status', 'created_at', DB::raw("CONCAT('" . asset('user/documents') . "', '/', document) AS document_path"))->where('user_id', $request->user_id)->get();
            if (!$patient_doc) {
                $patient_doc = null;
            }
            return response()->json(['status' => true, 'document_data' => $patient_doc]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'response' => "Internal Server Error"]);
        }
    }
}
