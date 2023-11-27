<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\schedule;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\Request;

class TokenGenerationController extends BaseController
{

    public function generateTokenCards(Request $request)
    {
        try {

            $cards = [];
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
                $cards[] = [
                    'Number' => $counter, // Use the counter for auto-incrementing 'Number'
                    'Time' => $currentTime->format('H:i'),
                    'Tokens' => $currentTime->add($timeInterval)->format('H:i'),
                    'is_booked'=>0,
                    'is_cancelled'=>0
                ];

                $counter++; // Increment the counter for the next card
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









}
