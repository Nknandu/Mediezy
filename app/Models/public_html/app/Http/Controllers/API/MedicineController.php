<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicineController extends BaseController
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
        $input = $request->all();

        $validator = Validator::make($input, [
            'docter_id' => [ 'max:25'],
            'user_id' => ['max:25'],
            'medicineName' => ['max:250'],
            'Dosage' => ['required', 'max:25'],
            'NoOfDays' => ['max:250'],
            'MorningBF' => ['required', 'max:25'],
            'MorningAF' => ['max:250'],
            'Noon' => ['required', 'max:25'],
            'night' => ['max:250'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }





        // Create a new schedule record
        $medicine = new Medicine;
        $medicine->user_id = $request->user_id;
        $medicine->docter_id = $request->docter_id;
        $medicine->medicineName = $request->medicineName;
        $medicine->Dosage = $request->Dosage;
        $medicine->NoOfDays = $request->NoOfDays;
        $medicine->MorningBF = $request->MorningBF;
        $medicine->MorningAF = $request->MorningAF;
        $medicine->Noon = $request->Noon;
        $medicine->night = $request->night;


        // Save the schedule record
        $medicine->save();

        return $this->sendResponse("medicine", $medicine, '1', 'Medicine Added successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $medicines = Medicine::find($id);

        if (is_null($medicines)) {
            return $this->sendError('medicines not found.');
        }

        return $this->sendResponse("medicines", $medicines, '1', 'medicines retrieved successfully.');
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
        $medicines = Medicine::find($id);

        $input = $request->all();

        $validator = Validator::make($input, [
            'docter_id' => [ 'max:25'],
            'user_id' => ['max:25'],
            'medicineName' => ['max:250'],
            'Dosage' => ['required', 'max:25'],
            'NoOfDays' => ['max:250'],
            'MorningBF' => ['required', 'max:25'],
            'MorningAF' => ['max:250'],
            'Noon' => ['required', 'max:25'],
            'night' => ['max:250'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        } else {

            $medicines->user_id = $input['user_id'];
        $medicines->docter_id = $input['user_id'];
        $medicines->medicineName = $input['medicineName'];
        $medicines->Dosage = $input['Dosage'];
        $medicines->NoOfDays = $input['NoOfDays'];
        $medicines->MorningBF = $input['MorningBF'];
        $medicines->MorningAF = $input['MorningAF'];
        $medicines->Noon = $input['Noon'];
        $medicines->night = $input['night'];

            $medicines->save();
            return $this->sendResponse("medicines", $medicines, '1', 'medicines Updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $medicines = Medicine::find($id);

        if (is_null($medicines)) {
            return $this->sendError('medicines not found.');
        }

        $medicines->delete();
        return $this->sendResponse("medicines", $medicines, '1', 'medicines Deleted successfully');
    }


}
