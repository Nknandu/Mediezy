<?php

use App\Http\Controllers\API\AppoinmentsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CategoriesController;
use App\Http\Controllers\API\DocterController;
use App\Http\Controllers\API\GetTokenController;
use App\Http\Controllers\API\HospitalController;
use App\Http\Controllers\API\LabController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\MedicalshopController;
use App\Http\Controllers\API\MedicineController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\specializeController;
use App\Http\Controllers\API\SpecificationController;
use App\Http\Controllers\API\SubspecificationController;
use App\Http\Controllers\API\TokenBookingController;
use App\Http\Controllers\API\TokenGenerationController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//Specialization
Route::get('/specialization', [SpecificationController::class, 'index']);
Route::get('/specialization/{id}', [SpecificationController::class, 'show']);

Route::post('/patient_history', [UserController::class, 'PatientHistory']);


Route::post('/specialization', [SpecificationController::class, 'store']);
Route::put('/specialization/{id}', [SpecificationController::class, 'update']);
Route::delete('/specialization/{id}', [SpecificationController::class, 'destroy']);

//Subpecialization
Route::get('/subspecialization', [SubspecificationController::class, 'index']);
Route::get('/subspecialization/{id}', [SubspecificationController::class, 'show']);

Route::post('/subspecialization', [SubspecificationController::class, 'store']);
Route::put('/subspecialization/{id}', [SubspecificationController::class, 'update']);
Route::delete('/subspecialization/{id}', [SubspecificationController::class, 'destroy']);


Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/docter', [DocterController::class, 'index']);
});
//Docter
Route::get('/getalldocters', [DocterController::class, 'getallDocters']);
Route::get('/docter/{userId}', [DocterController::class, 'show']);
Route::post('/docter', [DocterController::class, 'store']);
Route::put('/docter/{userId}', [DocterController::class, 'update']);
Route::delete('/docter/{id}', [DocterController::class, 'destroy']);
Route::get('/symptoms/{specializationId}', [DocterController::class, 'getSymptomsBySpecialization']);
Route::get('/docter/docterByspecialization/{id}', [DocterController::class, 'getDoctorsBySpecialization']);

//specialize
Route::get('/specialize', [specializeController::class, 'index']);
Route::get('/specialize/{id}', [specializeController::class, 'show']);

Route::post('/specialize', [specializeController::class, 'store']);
Route::put('/specialize/{id}', [specializeController::class, 'update']);
Route::delete('/specialize/{id}', [specializeController::class, 'destroy']);

Route::get('/schedule', [ScheduleController::class, 'index']);
Route::get('/schedule/{date}/{clinicId}', [ScheduleController::class, 'show']);

Route::post('/schedule', [ScheduleController::class, 'store']);
Route::put('/schedule/{id}', [ScheduleController::class, 'update']);
Route::delete('/schedule/{id}', [ScheduleController::class, 'destroy']);
Route::post('/getTokenCount', [ScheduleController::class, 'calculateMaxTokens']);

Route::post('/banner', [BannerController::class, 'store']);
Route::post('/set-first-image/{id}', [BannerController::class, 'setFirstImage']);
Route::put('/update-footer/{id}', [BannerController::class, 'updateFooterImages']);
//Medicine
Route::get('/Medicine', [MedicineController::class, 'index']);
Route::get('/Medicine/{id}', [MedicineController::class, 'show']);
Route::post('/Medicine', [MedicineController::class, 'store']);
Route::put('/Medicine/{id}', [MedicineController::class, 'update']);
Route::delete('/Medicine/{id}', [MedicineController::class, 'destroy']);
// Docter Registration
Route::post('/register', [RegisterController::class, 'register']);
// User Registration
Route::post('/Userregister', [UserController::class, 'UserRegister']);
Route::get('/Useredit/{userId}', [UserController::class, 'UserEdit']);
Route::put('/Userupdate/{userId}', [UserController::class, 'updateUserDetails']);
//  Login
Route::post('/login', [LoginController::class, 'login']);
Route::post('/generate-cards', [TokenGenerationController::class, 'generateTokenCards']);
Route::get('/generate-cards', [TokenGenerationController::class, 'generateTokenCards']);
Route::middleware('auth:api')->get('/today-schedule', [TokenGenerationController::class, 'getTodayTokens']);
Route::get('/get-hospital-name/{doctor_id}', [DocterController::class, 'getHospitalName']);
Route::post('/approveorreject', [DocterController::class, 'ApproveOrReject']);
//login with token
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/TokenBooking', [TokenBookingController::class, 'bookToken']);
Route::get('/getallappointments/{userId}/{date}', [TokenBookingController::class, 'GetallAppointmentOfDocter']);
Route::get('/getallcompletedappointments/{userId}/{date}', [TokenBookingController::class, 'GetallAppointmentOfDocterCompleted']);

Route::group(['prefix' => 'user'], function () {
    Route::any('/get_docter_tokens', [DocterController::class, 'getTokens']);
    Route::get('/userCompletedAppoinments/{userId}',[UserController::class,'GetUserCompletedAppoinments']);
    Route::post('/addtofavourites',[UserController::class,'favouritestatus']);
    Route::get('/getallfavourites/{id}',[UserController::class,'getallfavourites']);
    Route::post('/upload_document',[UserController::class,'uploadDocument']);
    Route::post('/update_document',[UserController::class,'updateDocument']);
    Route::post('/get_uploaded_documents',[UserController::class,'getUploadedDocuments']);
    Route::post('/reports_time_line',[UserController::class,'ReportsTimeLine']);
    Route::post('/get_prescriptions',[UserController::class,'getPrescriptions']);
    Route::post('/manage_member',[UserController::class,'manageMembers']);
    Route::post('/manage_address',[UserController::class,'manageAddress']);
    Route::post('/get_address',[UserController::class,'getUserAddresses']);
    Route::post('/get_patients',[UserController::class,'getPatients']);
});

//code for add_prescription
Route::group(['prefix' => 'docter'], function () {
    Route::post('/get_appointment_details', [TokenBookingController::class, 'appointmentDetails']);
    Route::post('/today_token_schedule',[TokenGenerationController::class,'todayTokenSchedule']);
    Route::post('/add_prescription', [TokenBookingController::class, 'addPrescription']);
    Route::post('/delete_tokens', [TokenGenerationController::class, 'deleteToken']);
    Route::post('/leave_update',[DocterController::class,'leaveUpdate']);
    Route::post('/leaves',[DocterController::class,'getDoctorLeaveList']);
    Route::post('/check_pincode_available',[DocterController::class,'checkPincodeAvailable']);
});

Route::group(['prefix' => 'Tokens'], function () {
    Route::post('/getTokendetails', [GetTokenController::class, 'getTokensForCheckInAndComplete']);
    Route::get('/getcurrentTokens', [GetTokenController::class, 'getCurrentDateTokens']);
});
Route::get('/user/userAppoinments/{userId}',[AppoinmentsController::class,'GetUserAppointments']);

//Workfrom athira
Route::get('/Showcategories', [CategoriesController::class, 'index']);
Route::get('/ShowCategoriesdocter/{id}', [CategoriesController::class, 'show']);

Route::post('/Categories', [CategoriesController::class, 'store']);
Route::get('/searchdoctor', [DocterController::class, 'searchDoctor']);


//medicalshop
Route::group(['prefix' => 'medicalshop'], function () {
    Route::post('/Register', [MedicalshopController::class, 'MedicalshopRegister']);
    Route::post('/medicine',[MedicalshopController::class, 'MedicineProduct']);
    Route::get('/getallmedicalshop',[MedicalshopController::class, 'GetMedicalShopForDoctors']);
    Route::get('/getmedicalshops',[MedicalshopController::class, 'GetAllMedicalShops']);





    });
//Laboratory
 Route::group(['prefix' => 'Lab'], function () {
    Route::post('/LabRegister', [LabController::class, 'LabRegister']);
    
    Route::post('/Test',[LabController::class, 'LabTest']);
    Route::get('/getalllab',[LabController::class, 'GetLabForDoctors']);
    Route::get('/getallScanningCenter',[LabController::class, 'GetScanningForDoctors']);
    Route::post('/addfavLab',[LabController::class, 'addFavouirtesLab']);
    Route::post('/RemovefavLab',[LabController::class, 'RemoveFavouirtesLab']);
    Route::get('/getfavlab',[LabController::class, 'getFavlab']);
    Route::get('/getLabs',[LabController::class, 'GetAllLabs']);

 });

 Route::group(['prefix' => 'Hospital'], function () {
    Route::post('/Register', [HospitalController::class, 'HospitalRegister']);

});



