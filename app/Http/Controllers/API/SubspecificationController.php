<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use App\Models\Subspecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubspecificationController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'subspecification' => ['required', 'max:25' ],
            'remark' => ['max:250'],

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }
        $CheckExists = Subspecification::select('subspecification')->where(['subspecification' => $input['subspecification']])->get();
        if (count($CheckExists) > 0) {
            return $this->sendResponse("Subspecification", 'Exists' , '0', 'Subspecification Already Exists');
        } else {
            $Subspecification = Subspecification::create($input);
            return $this->sendResponse("Subspecification",$Subspecification ,'1', 'Subspecification created successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $Subspecification = Subspecification::find($id);

        if (is_null($Subspecification)) {
            return $this->sendError('Subspecification not found.');
        }

        return $this->sendResponse("Subspecification", $Subspecification, '1', 'Subspecification retrieved successfully.');
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
        $Subspecification = Subspecification::find($id);

        $input = $request->all();

        $validator = Validator::make($input, [
            'subspecification' => ['required', 'max:25' ],
            'remark' => ['max:250'],

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());

        }
         else {
            $Subspecification->subspecification = $input['subspecification'];
            $Subspecification->remark = $input['remark'];
            $Subspecification->save();
            return $this->sendResponse("Subspecification", $Subspecification, '1', 'Subspecification Updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $Subspecification = Subspecification::find($id);

        if (is_null($Subspecification)) {
            return $this->sendError('Subspecification not found.');
        }

            $Subspecification->delete();
            return $this->sendResponse("Subspecification", $Subspecification, '1', 'Subspecification Deleted successfully');


    }
}
