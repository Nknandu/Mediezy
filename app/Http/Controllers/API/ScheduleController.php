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
    // public function generateTokenCards(Request $request)
    // {
    //     try {
    //         $cards = [];
    //         $counter = 1; // Initialize the counter before the loop
    //         if ($request->has('startingMorningTime') && $request->has('endingMorningTime') && $request->has('morningTimeDuration')) {
    //         $startMorningTime = $request->startingMorningTime;
    //         $endMorningTime = $request->endingMorningTime;
    //         $durationMorning = $request->morningTimeDuration;


    //         // Use Carbon to parse input times for morning section
    //         $startTimeMorning = Carbon::createFromFormat('H:i', $startMorningTime);
    //         $endTimeMorning = Carbon::createFromFormat('H:i', $endMorningTime);

    //         // Calculate the time interval based on the duration for morning section
    //         $timeIntervalMorning = new DateInterval('PT' . $durationMorning . 'M');

    //         // Generate tokens for morning section at regular intervals
    //         $currentTimeMorning = $startTimeMorning;

    //         while ($currentTimeMorning <= $endTimeMorning) {
    //             $cards[] = [
    //                 'Number' => $counter, // Use the counter for auto-incrementing 'Number'
    //                 'Time' => $currentTimeMorning->format('H:i'),
    //                 'Tokens' => $currentTimeMorning->add($timeIntervalMorning)->format('H:i'),
    //                 'is_booked' => 0,
    //                 'is_cancelled' => 0
    //             ];

    //             $counter++; // Increment the counter for the next card
    //         }
    //     }
    //         // Check if evening section is present
    //         if ($request->has('startingEveningTime') && $request->has('endingEveningTime') && $request->has('eveningTimeDuration')) {
    //             $startingNumberEvening = ($counter == 1) ? 1 : $counter;

    //             // Reset the counter for the evening section
    //             $counter = $startingNumberEvening;
    //             $startEveningTime = $request->startingEveningTime;
    //             $endEveningTime = $request->endingEveningTime;
    //             $durationEvening = $request->eveningTimeDuration;

    //             // Use Carbon to parse input times for evening section
    //             $startTimeEvening = Carbon::createFromFormat('H:i', $startEveningTime);
    //             $endTimeEvening = Carbon::createFromFormat('H:i', $endEveningTime);

    //             // Calculate the time interval based on the duration for evening section
    //             $timeIntervalEvening = new DateInterval('PT' . $durationEvening . 'M');

    //             // Generate tokens for evening section at regular intervals
    //             $currentTimeEvening = $startTimeEvening;

    //             while ($currentTimeEvening <= $endTimeEvening) {
    //                 $cards[] = [
    //                     'Number' => $counter, // Use the counter for auto-incrementing 'Number'
    //                     'Time' => $currentTimeEvening->format('H:i'),
    //                     'Tokens' => $currentTimeEvening->add($timeIntervalEvening)->format('H:i'),
    //                     'is_booked' => 0,
    //                     'is_cancelled' => 0
    //                 ];

    //                 $counter++; // Increment the counter for the next card
    //             }
    //         }
    //         return $cards;
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }



    public function generateTokenCards(Request $request)
    {
        try {
            $cards = [];
            $counter = 1; // Initialize the counter before the loop
            if (
                $request->has('startingMorningTime') &&
                $request->has('endingMorningTime') &&
                $request->has('morningTimeDuration') &&
                $request->morningTimeDuration !== null
            ) {
                $startMorningTime = $request->startingMorningTime;
                $endMorningTime = $request->endingMorningTime;
                $durationMorning = $request->morningTimeDuration;

                // Use Carbon to parse input times for morning section
                $startTimeMorning = Carbon::createFromFormat('H:i', $startMorningTime);
                $endTimeMorning = Carbon::createFromFormat('H:i', $endMorningTime);

                // Calculate the time interval based on the duration for morning section
                $timeIntervalMorning = new DateInterval('PT' . $durationMorning . 'M');

                // Generate tokens for morning section at regular intervals
                $currentTimeMorning = $startTimeMorning;

                while ($currentTimeMorning <= $endTimeMorning) {
                    $cards[] = [
                        'Number' => $counter, // Use the counter for auto-incrementing 'Number'
                        'Time' => $currentTimeMorning->format('H:i'),
                        'Tokens' => $currentTimeMorning->add($timeIntervalMorning)->format('H:i'),
                        'is_booked' => 0,
                        'is_cancelled' => 0
                    ];

                    $counter++; // Increment the counter for the next card
                }
            }
            // Check if evening section is present
            if (
                $request->has('startingEveningTime') &&
                $request->has('endingEveningTime') &&
                $request->has('eveningTimeDuration') &&
                $request->eveningTimeDuration !== null
            ) {
                $startingNumberEvening = ($counter == 1) ? 1 : $counter;

                // Reset the counter for the evening section
                $counter = $startingNumberEvening;
                $startEveningTime = $request->startingEveningTime;
                $endEveningTime = $request->endingEveningTime;
                $durationEvening = $request->eveningTimeDuration;

                // Use Carbon to parse input times for evening section
                $startTimeEvening = Carbon::createFromFormat('H:i', $startEveningTime);
                $endTimeEvening = Carbon::createFromFormat('H:i', $endEveningTime);

                // Calculate the time interval based on the duration for evening section
                $timeIntervalEvening = new DateInterval('PT' . $durationEvening . 'M');

                // Generate tokens for evening section at regular intervals
                $currentTimeEvening = $startTimeEvening;

                while ($currentTimeEvening <= $endTimeEvening) {
                    $cards[] = [
                        'Number' => $counter, // Use the counter for auto-incrementing 'Number'
                        'Time' => $currentTimeEvening->format('H:i'),
                        'Tokens' => $currentTimeEvening->add($timeIntervalEvening)->format('H:i'),
                        'is_booked' => 0,
                        'is_cancelled' => 0
                    ];

                    $counter++; // Increment the counter for the next card
                }
            }
            return $cards;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
                'startingMorningTime' => ['max:250'],
                'endingMorningTime' => ['max:25'],
                'startingEveningTime' => ['max:250'],
                'endingEveningTime' => ['max:25'],
                'TokenCount' => ['max:250'],
                'format' => ['max:250'],

            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }


            DB::beginTransaction();

            $existingSchedule = Schedule::where('docter_id', $request->docter_id)
                ->where('hospital_Id', $request->hospital_Id)
                ->first();

            // Delete the existing schedule if found
            if ($existingSchedule) {
                $existingSchedule->delete();
            }



            $inputDate = Carbon::parse($request->date);
            $oneYearLater = $inputDate->addYear();
            $oneYearLaterString = $oneYearLater->toDateString();


            $selectdays = json_encode($request->selecteddays);

            $tokens = $this->generateTokenCards($request);

            // Encode the cards to JSON
            $tokensJson = json_encode($tokens);

            // Create a new schedule record
            $schedule = new Schedule;
            $schedule->docter_id = $request->docter_id;
            $schedule->session_title = $request->session_title;
            $schedule->date = $request->date;
            $schedule->startingTime = $request->startingMorningTime ?? null;
            $schedule->endingTime = $request->endingMorningTime ?? null;
            $schedule->eveningstartingTime = $request->startingEveningTime ?? null;
            $schedule->eveningendingTime = $request->endingEveningTime ?? null;
            $schedule->TokenCount = $request->TokenCount;
            $schedule->timeduration = $request->morningTimeDuration ?? null;
            $schedule->eveningTimeDuration = $request->eveningTimeDuration ?? null;
            $schedule->format = $request->format;
            $schedule->scheduleupto = $oneYearLaterString;
            $schedule->selecteddays = $selectdays;
            $schedule->tokens = $tokensJson; // Use the JSON-encoded tokens here
            $schedule->hospital_Id = $request->hospital_Id;

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





    public function show($date, $clinic_id)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the logged-in user
            $user = Auth::user();

            // Find the schedule for the logged-in user based on the given date
            $schedule = Schedule::where('docter_id', $user->id)
                ->where('hospital_Id', $clinic_id)
                ->where('date', '<=', $date)
                ->where('scheduleupto', '>=', $date)
                ->first();

            if (is_null($schedule)) {
                return $this->sendError('Schedule not found for the given date.');
            }

            $selectedDaysArray = json_decode($schedule->selecteddays, true);

            // Check if the given date falls on a selected day
            $givenDateDay = date('l', strtotime($date));
            if (!in_array($givenDateDay, $selectedDaysArray)) {
                return $this->sendResponse("schedule", null, '1', 'Schedule not available for the given date.');
            }

            $schedule['tokens'] = json_decode($schedule->tokens);

            $today_schedule = TodaySchedule::select('id', 'tokens', 'date', 'hospital_Id')
                ->where('docter_id',  $user->id)
                ->where('hospital_Id', $clinic_id)
                ->where('date', $date)
                ->first();

            if ($today_schedule) {
                $schedule = $today_schedule;
                $schedule['tokens'] = json_decode($today_schedule->tokens);
            }

            $morningTokens = [];
            $eveningTokens = [];

            foreach ($schedule['tokens'] as $token) {
                // Set is_booked to 1 (or any other value you want)
                $tokenBooking = TokenBooking::where('date', $date)
                    ->where('clinic_id', $clinic_id)
                    ->where('TokenTime', $token->Time)
                    ->where('TokenNumber', $token->Number)
                    ->first();

                $token->is_booked = $tokenBooking ? 1 : 0;

                // Categorize tokens into morning and evening
                if (Carbon::parse($token->Time) < Carbon::parse('13:00:00')) {
                    $morningTokens[] = $token;
                } else {
                    $eveningTokens[] = $token;
                }
            }

            $tokenData = new \stdClass(); // Create a new object to store token data
            $tokenData->morning_tokens = $morningTokens;
            $tokenData->evening_tokens = $eveningTokens;
            $tokenData->hospitalId = $clinic_id; // Add hospital_id to the token data

            // Return the modified response
            return response()->json([
                'status' => true,
                'schedule' => $tokenData,
                'message' => 'Schedule retrieved successfully.',
            ]);
        } else {
            // Handle the case where the user is not authenticated
            return $this->sendError('User not authenticated.', [], 401);
        }
    }



    // Helper function to transform tokens
    private function transformTokens($tokensArray, $appointments)
    {
        return array_map(function ($token) use ($appointments) {
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
