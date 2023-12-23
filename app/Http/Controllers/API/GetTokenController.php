<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\TokenBooking;
use Carbon\Carbon;
use App\Models\Symtoms;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetTokenController extends BaseController
{


    public function getCurrentDateTokens()
    {
        // Get the current date
        $currentDate = now()->toDateString();

        // Retrieve tokens for the current date using Eloquent
        $tokens = TokenBooking::whereDate('date', $currentDate)
        ->orderByRaw('CAST(token_booking.TokenNumber AS SIGNED) ASC')->get();

        if ($tokens->isEmpty()) {
            // No tokens found for the current date
            return response()->json(['message' => 'No tokens available for the current date.', 'tokens' => null], 200);
        }

        $filteredTokens = $tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'TokenNumber' => $token->TokenNumber,
                'TokenTime' => $token->TokenTime,
            ];
        });

        // Return the tokens as JSON
        return response()->json(['message' => 'Currendate Tokens retrieved successfully.', 'tokens' => $filteredTokens], 200);
    }


    // public function getTokensForCheckInAndComplete(Request $request)
    // {
    //     $rules = [
    //         'userId' => 'required',
    //         'TokenNumber' => 'required',
    //         'Is_checkIn' => 'sometimes',
    //         'Is_completed' => 'sometimes',
    //     ];

    //     $validation = Validator::make($request->all(), $rules);

    //     if ($validation->fails()) {
    //         return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
    //     }

    //     try {
    //         // Get current date
    //         $currentDate = Carbon::now()->toDateString();
    //         $userId = $request->userId;
    //         $tokenNumber = $request->TokenNumber;

    //         // Fetch appointments for the current date
    //         $appointments = DB::table('token_booking')
    //             ->whereDate('date', $currentDate)
    //             ->where('doctor_id', $userId)
    //             ->where('TokenNumber', $tokenNumber)
    //             ->get();
    //         if ($appointments->isEmpty()) {
    //             return response()->json(['message' => 'No appointments for the current date.'], 200);
    //         }

    //         $updatedTokens = [];

    //         foreach ($appointments as $appointment) {
    //             $tokenBooking = TokenBooking::find($appointment->id);

    //             if ($request->Is_checkIn) {
    //                 $tokenBooking->Is_checkIn = $request->Is_checkIn;
    //                 $tokenBooking->checkinTime = now();
    //             }

    //             if ($request->Is_completed) {
    //                 $tokenBooking->Is_completed = $request->Is_completed;
    //                 $tokenBooking->checkoutTime = now();
    //             }

    //             $tokenBooking->save();

    //             // Add the updated token details to the response
    //             $updatedTokens[] = $tokenBooking;
    //             $symptoms = json_decode($tokenBooking->Appoinmentfor_id, true);
    //             $tokenBooking['main_symptoms'] = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray();
    //             $tokenBooking['other_symptoms'] = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray();
    //         }

    //         return response()->json(['message' => 'Check-in and/or completion status updated successfully.', 'tokens' => $updatedTokens], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

    public function getTokensForCheckInAndComplete(Request $request)
{
    $rules = [
        'TokenNumber' => 'required',
        'Is_checkIn' => 'sometimes',
        'Is_completed' => 'sometimes',
    ];

    $validation = Validator::make($request->all(), $rules);

    if ($validation->fails()) {
        return response()->json(['status' => false, 'response' => $validation->errors()->first()]);
    }

    try {
        // Get the authenticated user's ID (assuming you have authentication in place)
        $userId = auth()->user()->id;

        // Get current date
        $currentDate = Carbon::now()->toDateString();
        $tokenNumber = $request->TokenNumber;

        // Fetch appointments for the current date and the logged-in doctor
        $appointments = DB::table('token_booking')
            ->whereDate('date', $currentDate)
            ->where('doctor_id', $userId)
            ->where('TokenNumber', $tokenNumber)
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments for the current date.'], 200);
        }

        $updatedTokens = [];

        foreach ($appointments as $appointment) {
            $tokenBooking = TokenBooking::find($appointment->id);

            if ($request->Is_checkIn) {
                $tokenBooking->Is_checkIn = $request->Is_checkIn;
                $tokenBooking->checkinTime = now();
            }

            if ($request->Is_completed) {
                $tokenBooking->Is_completed = $request->Is_completed;
                $tokenBooking->checkoutTime = now();
            }

            $tokenBooking->save();

            // Add the updated token details to the response
            $updatedTokens[] = $tokenBooking;
            $symptoms = json_decode($tokenBooking->Appoinmentfor_id, true);
            $tokenBooking['main_symptoms'] = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor1'])->get()->toArray();
            $tokenBooking['other_symptoms'] = Symtoms::select('id', 'symtoms')->whereIn('id', $symptoms['Appoinmentfor2'])->get()->toArray();
        }

        return response()->json(['message' => 'Check-in and/or completion status updated successfully.', 'tokens' => $updatedTokens], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

}
