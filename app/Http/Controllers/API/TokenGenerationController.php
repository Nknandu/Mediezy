<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Docter;
use App\Models\schedule;
use App\Models\TodaySchedule;
use App\Models\TodayShedule;
use App\Models\TokenBooking;
use Carbon\Carbon;

use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TokenGenerationController extends BaseController
{

    // public function generateTokenCards(Request $request)
    // {
    //     try {

    //         $cards = [];
    //         $counter = 1; // Initialize the counter before the loop


    //         $startDateTime = $request->startingTime;
    //         $endDateTime = $request->endingTime;
    //         $duration = $request->timeduration;

    //         // Use Carbon to parse input times
    //         $startTime = Carbon::createFromFormat('H:i', $startDateTime);
    //         $endTime = Carbon::createFromFormat('H:i', $endDateTime);

    //         // Calculate the time interval based on the duration
    //         $timeInterval = new DateInterval('PT' . $duration . 'M');

    //         // Generate tokens at regular intervals
    //         $currentTime = $startTime;

    //         while ($currentTime <= $endTime) {
    //             $cards[] = [
    //                 'Number' => $counter, // Use the counter for auto-incrementing 'Number'
    //                 'Time' => $currentTime->format('H:i'),
    //                 'Tokens' => $currentTime->add($timeInterval)->format('H:i'),
    //                 'is_booked' => 0,
    //                 'is_cancelled' => 0
    //             ];

    //             $counter++; // Increment the counter for the next card
    //         }


    //         return response()->json(['cards' => $cards], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

    public function generateTokenCards(Request $request)
    {
        try {
            $cards = [];
            $counter = 1; // Initialize the counter before the loop

            $startMorningTime = $request->startingMorningTime;
            $endMorningTime = $request->endingMorningTime;
            $durationMorning = $request->morningTimeDuration;
            $section=$request->section;

            // Use Carbon to parse input times for morning section
            $startTimeMorning = Carbon::createFromFormat('H:i', $startMorningTime);
            $endTimeMorning = Carbon::createFromFormat('H:i', $endMorningTime);

            // Calculate the time interval based on the duration for morning section
            $timeIntervalMorning = new DateInterval('PT' . $durationMorning . 'M');

            // Generate tokens for morning section at regular intervals
            $currentTimeMorning = $startTimeMorning;

            while ($currentTimeMorning <= $endTimeMorning) {
                $cards[] = [
                    'Section'=>$section,
                    'Number' => $counter, // Use the counter for auto-incrementing 'Number'
                    'Time' => $currentTimeMorning->format('H:i'),
                    'Tokens' => $currentTimeMorning->add($timeIntervalMorning)->format('H:i'),
                    'is_booked' => 0,
                    'is_cancelled' => 0
                ];

                $counter++; // Increment the counter for the next card
            }

            // Check if evening section is present
            if ($request->has('startingEveningTime') && $request->has('endingEveningTime') && $request->has('eveningTimeDuration')) {
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

            return response()->json(['cards' => $cards], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }





    public function getTodayTokens(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        if (!$user) {
            return $this->sendError('Authentication Error', 'User not authenticated', 401);
        }
        $today = now()->toDateString();

        $schedules = Schedule::whereHas('docter', function ($query) use ($user) {
            $query->where('UserId', $user->id);
        })->where('date', $today)->get();


        $tokensByClinic = [];

        foreach ($schedules as $schedule) {
            $doctor = $schedule->doctor;

            // Assuming you have a relationship set up for the clinics in Doctor model
            $clinics = $doctor->clinics;

            foreach ($clinics as $clinic) {
                $clinicId = $clinic->hospital_Id;

                if (!isset($tokensByClinic[$clinicId])) {
                    $tokensByClinic[$clinicId] = [];
                }

                // Decode the JSON data
                $tokens = json_decode($schedule->tokens);

                $tokensByClinic[$clinicId][] = [
                    'clinic' => $clinic,
                    'tokens' => $tokens,
                ];
            }
        }


        if (!empty($tokensByClinic)) {
            return $this->sendResponse('todaytokens', $tokensByClinic, '1', 'Today\'s tokens retrieved successfully');
        } else {
            return $this->sendError('No Schedule Found', 'No schedule for today found for the logged-in user');
        }
    }

    public function todayTokenSchedule(Request $request)
    {
        $rules = [
            'doctor_id'     => 'required',
            'hospital_id'   => 'required',
            'date'          => 'required',
            'delay_type'    => 'required|in:1,2,3', //2 for late , 1 for earliy
            'custome_time'  => 'required_if:delay_type,1,2',
            'start_time'    => 'required_if:delay_type,3',
            'end_time'      => 'required_if:delay_type,3|after:start_time',
        ];
        $messages = [
            'date.required' => 'Date is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $token_booked = TokenBooking::where('date', $request->date)->where('doctor_id', $request->doctor_id)->where('clinic_id', $request->hospital_id)->first();
            if ($token_booked) {
                return response()->json(['status' => false, 'message' => 'Already bookings in this date']);
            }
            $doctor  = Docter::where('id', $request->doctor_id)->first();
            if (!$doctor) {
                return response()->json(['status' => false, 'message' => 'Doctor not found']);
            }
            $schedule = schedule::where('docter_id', $doctor->id)->where('hospital_Id', $request->hospital_id)->first();

            $requestDate = Carbon::parse($request->date);
            $startDate = Carbon::parse($schedule->date);
            $scheduledUptoDate = Carbon::parse($schedule->scheduleupto);
            // Get the day of the week
            $dayOfWeek = $requestDate->format('l'); // 'l' format gives the full name of the day

            $jsonString = str_replace("'", "\"", $schedule->selecteddays);

            $allowedDaysArray = json_decode($jsonString);

            if (!$requestDate->between($startDate, $scheduledUptoDate)) {
                return response()->json(['status' => false, 'message' => 'Date is this not found in year']);
            }
            if (!in_array($dayOfWeek, $allowedDaysArray)) {
                return response()->json(['status' => false, 'message' => 'Selected day not found on your scheduled days']);
            }
            // Set the start time and end time
            $startTime = Carbon::parse($schedule->startingTime);
            $endTime = Carbon::parse($schedule->endingTime);

            // Generate time slots in JSON format
            $timeSlots = [];
            $count = 0;

            if ($request->delay_type == '2') {
                // Add 30 minutes to the start time
                $startTime->addMinutes($request->custome_time);
            }
            if ($request->delay_type == '1') { // Subtract 30 minutes from the start time
                $startTime->subMinutes($request->custome_time);
            }

            if ($request->delay_type == 3) {
                $excludeStartTime = Carbon::parse($request->start_time);
                $excludeEndTime = Carbon::parse($request->end_time);
                while ($startTime < $endTime) {
                    $slotStartTime = $startTime->format('h:i');
                    $startTime->addMinutes($schedule->timeduration);

                    // Skip slots that fall within the excluded time range
                    if ($startTime >= $excludeStartTime && $startTime <= $excludeEndTime) {
                        continue;
                    }
                    $slotEndTime = $startTime->format('h:i');
                    $timeSlots[] = [
                        "Number" => $count = 0 ? 1 : $count + 1,
                        'Time' => $slotStartTime,
                        'Tokens' => $slotEndTime,
                        "is_booked" => 0,
                        "is_cancelled" => 0
                    ];
                }
                $count++;
            } else {
                // Calculate the number of slots
                $totalSlots = $startTime->diffInMinutes($endTime) / $schedule->timeduration;
                for ($i = 1; $i <= $totalSlots; $i++) {
                    $slot = [
                        'Number' => $i,
                        'Time' => $startTime->format('h:i'),
                        'Tokens' => $startTime->addMinutes($schedule->timeduration)->format('h:i'),
                        "is_booked" => 0,
                        "is_cancelled" => 0
                    ];
                    $timeSlots[] = $slot;
                }
            }

            $checkIfexist = TodaySchedule::where('docter_id', $doctor->id)->whereDate('date', $request->date)->where('hospital_Id', $request->hospital_id)->first();
            if ($checkIfexist) {
                $schedule = $checkIfexist;
            } else {
                $schedule = new TodaySchedule();
            }
            $schedule->docter_id = $request->doctor_id;
            $schedule->hospital_id = $request->hospital_id;
            $schedule->date = $request->date;
            $schedule->delay_time = $request->custome_time;
            $schedule->delay_type = $request->delay_type;
            $schedule->tokens = json_encode($timeSlots);
            $schedule->save();

            return response()->json(['status' => true, 'message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => "Internal Server Error"]);
        }
    }

    public function deleteToken(Request $request)
    {
        $rules = [
            'doctor_id'     => 'required',
            'hospital_id'   => 'required',
            'date'          => 'required',
            'token_numbers' => 'required'
        ];
        $messages = [
            'date.required' => 'Date is required',
        ];
        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
        }
        try {
            $token_booked = TokenBooking::where('date', $request->date)->where('doctor_id', $request->doctor_id)->where('clinic_id', $request->hospital_id)->first();
            if ($token_booked) {
                return response()->json(['status' => false, 'message' => 'Already bookings in this date']);
            }
            $request_tokens = json_decode($request->token_numbers);
            $doctor  = Docter::where('id', $request->doctor_id)->first();
            if (!$doctor) {
                return response()->json(['status' => false, 'message' => 'Doctor not found']);
            }
            $schedule = schedule::where('docter_id', $doctor->id)->where('hospital_Id', $request->hospital_id)->first();

            $requestDate = Carbon::parse($request->date);
            $startDate = Carbon::parse($schedule->date);
            $scheduledUptoDate = Carbon::parse($schedule->scheduleupto);
            // Get the day of the week
            $dayOfWeek = $requestDate->format('l'); // 'l' format gives the full name of the day

            $jsonString = str_replace("'", "\"", $schedule->selecteddays);

            $allowedDaysArray = json_decode($jsonString);

            if (!$requestDate->between($startDate, $scheduledUptoDate)) {
                return response()->json(['status' => false, 'message' => 'Date is this not found in year']);
            }
            if (!in_array($dayOfWeek, $allowedDaysArray)) {
                return response()->json(['status' => false, 'message' => 'Selected day not found on your scheduled days']);
            }
            $shedulded_tokens =  schedule::where('docter_id', $request->doctor_id)->where('hospital_Id', $request->hospital_id)->first();

            $today_schedule = TodaySchedule::select('id', 'tokens', 'date', 'hospital_Id')->where('docter_id', $request->doctor_id)->where('hospital_Id', $request->hospital_id)->where('date', $request->date)->first();
            if ($today_schedule) {
                $shedulded_tokens = $today_schedule;
            }
            // Filter the array
            $checking_token =  json_decode($shedulded_tokens->tokens);
            $filteredData = array_filter($checking_token, function ($item) use ($request_tokens) {
                return !in_array($item->Number, $request_tokens);
            });
            // Reset the keys if needed
            $filteredData = array_values($filteredData);

            if ($today_schedule) {
                $schedule = $today_schedule;
            } else {
                $schedule = new TodaySchedule();
            }
            $schedule->docter_id = $request->doctor_id;
            $schedule->hospital_id = $request->hospital_id;
            $schedule->date = $request->date;
            $schedule->tokens = json_encode($filteredData);
            $schedule->save();

            return response()->json(['status' => true, 'message' => 'Successfully Updated']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => "Internal Server Error"]);
        }
    }
}
