<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\schedule;
use App\Models\TodaySchedule;
use App\Models\TokenBooking;
use App\Models\TokenHistory;
use Carbon\Carbon;
use Faker\Core\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateInterval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'docter_id' => ['required', 'max:25'],
                'session_title' => ['max:250'],
                'date' => ['required', 'max:25'],
                'startingTime' => ['max:250'],
                'endingTime' => ['required', 'max:25'],
                'TokenCount' => ['max:250'],
                'timeduration' => ['required', 'max:25'],
                'format' => ['max:250'],
                'section'=>['required', 'max:25']
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            // Use a transaction to ensure atomicity (all-or-nothing)
            DB::beginTransaction();

            $existingSchedule = Schedule::where('docter_id', $request->docter_id)
            ->where('hospital_Id', $request->hospital_Id)
            ->first();

        // Delete the existing schedule if found
        if ($existingSchedule) {
            $existingSchedule->delete();
        }

            $tokens = [];
            $counter = 1; // Initialize the counter before the loop

            $startDateTime = $request->startingTime;
            $endDateTime = $request->endingTime;
            $duration = $request->timeduration;

            // Use Carbon to parse input times
            $startTime = Carbon::createFromFormat('H:i', $startDateTime);
            $endTime = Carbon::createFromFormat('H:i', $endDateTime);

            // Calculate the time interval based on the duration
            $timeInterval = new DateInterval('PT' . $duration . 'M');

            // Generate tokens at regular intervals
            $currentTime = $startTime;

            while ($currentTime <= $endTime) {
                $tokens[] = [
                    'Number' => $counter, // Use the counter for auto-incrementing 'Number'
                    'Time' => $currentTime->format('H:i'),
                    'Tokens' => $currentTime->add($timeInterval)->format('H:i')
                ];

                $counter++; // Increment the counter for the next card
            }

            $inputDate = Carbon::parse($request->date);
            $oneYearLater = $inputDate->addYear();
            $oneYearLaterString = $oneYearLater->toDateString();

            $tokensJson = json_encode($tokens);
            $selectdays = json_encode($request->selecteddays);

            // Create a new schedule record
            $schedule = new Schedule;
            $schedule->docter_id = $request->docter_id;
            $schedule->session_title = $request->session_title;
            $schedule->date = $request->date;
            $schedule->startingTime = $request->startingTime;
            $schedule->endingTime = $request->endingTime;
            $schedule->TokenCount = $request->TokenCount;
            $schedule->timeduration = $request->timeduration;
            $schedule->format = $request->format;
            $schedule->scheduleupto = $oneYearLaterString;
            $schedule->selecteddays = $selectdays;
            $schedule->tokens = $tokensJson;
            $schedule->hospital_Id = $request->hospital_Id;
            $schedule->section = $request->section;
            // Save the schedule record
            $schedule->save();

            // Create a new token_history record
            $tokenHistory = new TokenHistory();
            $tokenHistory->docter_id = $request->docter_id;
            $tokenHistory->session_title = $request->session_title;
            $tokenHistory->TokenUpdateddate = $request->date;
            $tokenHistory->startingTime = $request->startingTime;
            $tokenHistory->endingTime = $request->endingTime;
            $tokenHistory->TokenCount = $request->TokenCount;
            $tokenHistory->timeduration = $request->timeduration;
            $tokenHistory->format = $request->format;
            $tokenHistory->scheduleupto = $oneYearLaterString;
            $tokenHistory->selecteddays = $selectdays;
            $tokenHistory->tokens = $tokensJson;
            $tokenHistory->hospital_Id = $request->hospital_Id;

            // Save the token_history record
            $tokenHistory->save();

            // Commit the transaction
            DB::commit();

            return $this->sendResponse("schedulemanager", $schedule, '1', 'Schedule Manager created successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Handle the exception
            return $this->sendError('Error', $e->getMessage(), 500);
        }
    }



    /**
     * Display the specified resource.
     */

    // public function show($date)
    // {
    //     // Check if the user is authenticated
    //     if (Auth::check()) {
    //         // Get the logged-in user
    //         $user = Auth::user();

    //         // Find the schedule for the logged-in user based on the given date
    //         $schedule = Schedule::where('docter_id', $user->id)
    //                             ->where('date', '<=', $date)
    //                             ->where('scheduleupto', '>=', $date)
    //                             ->first();

    //         if (is_null($schedule)) {
    //             return $this->sendError('Schedule not found for the given date.');
    //         }

    //         // Convert the selecteddays string to an array after removing quotes and extra spaces
    //         $selectedDaysArray = array_map('trim', explode(',', str_replace(['[', ']', '"'], '', $schedule->selecteddays)));

    //         // Set the transformed selecteddays array back to the schedule
    //         $schedule->selecteddays = $selectedDaysArray;

    //         // Decode the tokens JSON string
    //         $tokensArray = json_decode($schedule->tokens, true);

    //         // Transform the tokens array structure
    //         $transformedTokens = array_map(function($token) {
    //             return [
    //                 'Number' => $token['Number'],
    //                 'StartingTime' => $token['Time'],
    //                 'EndingTime' => $token['Tokens'],
    //             ];
    //         }, $tokensArray);

    //         // Set the transformed tokens array back to the schedule
    //         $schedule->tokens = $transformedTokens;

    //         // Return the modified response
    //         return $this->sendResponse("schedule", $schedule, '1', 'Schedule retrieved successfully.');
    //     } else {
    //         // Handle the case where the user is not authenticated
    //         return $this->sendError('User not authenticated.', [], 401);
    //     }
    // }

    // date-22/11/2023
    // public function show($date)
    // {
    //     // Check if the user is authenticated
    //     if (Auth::check()) {
    //         // Get the logged-in user
    //         $user = Auth::user();

    //         // Find the schedule for the logged-in user based on the given date
    //         $schedule = Schedule::where('docter_id', $user->id)
    //                             ->where('date', '<=', $date)
    //                             ->where('scheduleupto', '>=', $date)
    //                             ->first();

    //         if (is_null($schedule)) {
    //             return $this->sendError('Schedule not found for the given date.');
    //         }

    //         // Convert the selecteddays string to an array after removing quotes and extra spaces
    //         $selectedDaysArray = array_map('trim', explode(',', str_replace(['[', ']', '"'], '', $schedule->selecteddays)));

    //         // Check if the given date falls on a selected day
    //         $givenDateDay = date('l', strtotime($date));
    //         if (!in_array(strtolower($givenDateDay), $selectedDaysArray)) {
    //             return $this->sendError('Schedule not available for the given date.');
    //         }

    //         // Decode the tokens JSON string
    //         $tokensArray = json_decode($schedule->tokens, true);

    //         // Transform the tokens array structure
    //         $transformedTokens = array_map(function($token) {
    //             return [
    //                 'Number' => $token['Number'],
    //                 'StartingTime' => $token['Time'],
    //                 'EndingTime' => $token['Tokens'],
    //             ];
    //         }, $tokensArray);

    //         // Set the transformed tokens array back to the schedule
    //         $schedule->tokens = $transformedTokens;

    //         // Return the modified response
    //         return $this->sendResponse("schedule", $schedule, '1', 'Schedule retrieved successfully.');
    //     } else {
    //         // Handle the case where the user is not authenticated
    //         return $this->sendError('User not authenticated.', [], 401);
    //     }
    // }
    // public function show($date)
    // {
    //     // Check if the user is authenticated
    //     if (Auth::check()) {
    //         // Get the logged-in user
    //         $user = Auth::user();

    //         // Find the schedule for the logged-in user based on the given date
    //         $schedule = Schedule::where('docter_id', $user->id)
    //                             ->where('date', '<=', $date)
    //                             ->where('scheduleupto', '>=', $date)
    //                             ->first();

    //         if (is_null($schedule)) {
    //             return $this->sendError('Schedule not found for the given date.');
    //         }

    //         // Decode the selecteddays JSON string
    //         $selectedDaysArray = json_decode($schedule->selecteddays, true);

    //         // Check if the given date falls on a selected day
    //         $givenDateDay = date('l', strtotime($date));
    //         if (!in_array($givenDateDay, $selectedDaysArray)) {
    //             return $this->sendResponse("schedule", null, '1', 'Schedule not available for the given date.');
    //         }

    //         // Decode the tokens JSON string
    //         $tokensArray = json_decode($schedule->tokens, true);

    //         // Transform the tokens array structure
    //         $transformedTokens = array_map(function($token) {
    //             $isBooked = isset($token['is_booked']) ? $token['is_booked'] : null;
    //             $iscancelled = isset($token['is_cancelled']) ? $token['is_cancelled'] : null;
    //             return [
    //                 'Number' => $token['Number'],
    //                 'StartingTime' => $token['Time'],
    //                 'EndingTime' => $token['Tokens'],
    //                 'Is_booked' => $isBooked,
    //                 'is_cancelled' => $iscancelled
    //             ];
    //         }, $tokensArray);

    //         // Set the transformed tokens array back to the schedule
    //         $schedule->tokens = $transformedTokens;

    //         // Return the modified response
    //         return $this->sendResponse("schedule", $schedule, '1', 'Schedule retrieved successfully.');
    //     } else {
    //         // Handle the case where the user is not authenticated
    //         return $this->sendError('User not authenticated.', [], 40g

    public function show($date, $clinic_id)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the logged-in user
            $user = Auth::user();

            // Find the schedule for the logged-in user based on the given date
            $schedule = Schedule::where('docter_id', $user->id)
                ->where('date', '<=', $date)
                ->where('hospital_Id', '<=', $clinic_id)
                ->where('scheduleupto', '>=', $date)
                ->first();

            if (is_null($schedule)) {
                return $this->sendError('Schedule not found for the given date.');
            }

            // Decode the selecteddays JSON string
            $selectedDaysArray = json_decode($schedule->selecteddays, true);

            // Check if the given date falls on a selected day
            $givenDateDay = date('l', strtotime($date));
            if (!in_array($givenDateDay, $selectedDaysArray)) {
                return $this->sendResponse("schedule", null, '1', 'Schedule not available for the given date.');
            }

            // Decode the tokens JSON string
            $tokensArray = json_decode($schedule->tokens, true);

            // Get doctor appointments from the token_booking table for the given date
            $appointments = TokenBooking::where('doctor_id', $user->id)
                ->whereDate('date', $date)
                ->get();


            $today_schedule = TodaySchedule::select('id', 'tokens', 'date', 'hospital_Id')
                ->where('docter_id',  $user->id)
                ->where('hospital_Id', $clinic_id)
                ->where('date', $date)
                ->first();

            if ($today_schedule) {
                $tokensArray = json_decode($today_schedule->tokens, true);
            }


            $transformedTokens = array_map(function ($token) use ($appointments) {
                $matchingAppointment = $appointments->first(function ($appointment) use ($token) {
                    return $appointment->TokenNumber == $token['Number'] && $appointment->TokenTime == $token['Time'];
                });

                // Add debug information
                if ($matchingAppointment) {
                    info("Matching appointment found for TokenNumber: {$token['Number']}, TokenTime: {$token['Time']}");
                }
                return [
                    'Number' => $token['Number'],
                    'StartingTime' => $token['Time'],
                    'EndingTime' => $token['Tokens'],
                    'Is_booked' => $matchingAppointment ? 1 : 0,
                    'is_cancelled' => isset($token['is_cancelled']) ? $token['is_cancelled'] : null,
                ];
            }, $tokensArray);

            // Set the transformed tokens array back to the schedule
            $schedule->tokens = $transformedTokens;

            // Return the modified response
            return $this->sendResponse("schedule", $schedule, '1', 'Schedule retrieved successfully.');
        } else {
            // Handle the case where the user is not authenticated
            return $this->sendError('User not authenticated.', [], 401);
        }
    }







    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $specialization = schedule::find($id);

        $input = $request->all();

        $validator = Validator::make($input, [
            'docter_id' => ['required', 'max:25'],
            'session_title' => ['max:250'],
            'date' => ['required', 'max:25'],
            'startingTime' => ['max:250'],
            'endingTime' => ['required', 'max:25'],
            'token' => ['max:250'],
            'timeduration' => ['required', 'max:25'],
            'format' => ['max:250'],

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        } else {
            $specialization->specialization = $input['specialization'];

            $specialization->save();
            return $this->sendResponse("specialization", $specialization, '1', 'specialization Updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $schedule = schedule::find($id);

        if (is_null($schedule)) {
            return $this->sendError('specialization not found.');
        }

        $schedule->delete();
        return $this->sendResponse("schedule", $schedule, '1', 'schedule Deleted successfully');
    }






    public function calculateMaxTokens(Request $request)
    {
        try {
            $startDateTime = $request->input('startingTime');
            $endDateTime = $request->input('endingTime');
            $duration = $request->input('timeduration');

            $startTime = Carbon::createFromFormat('H:i', $startDateTime);
            $endTime = Carbon::createFromFormat('H:i', $endDateTime);

            // Calculate the time interval based on the duration
            $timeInterval = new DateInterval('PT' . $duration . 'M');

            $maxTokenCount = 0;
            $currentTime = $startTime;

            while ($currentTime <= $endTime) {
                $maxTokenCount++;
                $currentTime->add($timeInterval);
            }

            return response()->json(['max_token_count' => $maxTokenCount], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
