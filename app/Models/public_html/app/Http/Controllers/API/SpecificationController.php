<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecificationController extends BaseController
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
            'specification' => ['required', 'max:25' ],
            'remark' => ['max:250'],

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }
        $CheckExists = Specification::select('specification')->where(['specification' => $input['specification']])->get();
        if (count($CheckExists) > 0) {
            return $this->sendResponse("Specification", 'Exists' , '0', 'Specification Already Exists');
        } else {
            $Specification = Specification::create($input);
            return $this->sendResponse("Specification",$Specification ,'1', 'Specification created successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $Specification = Specification::find($id);

        if (is_null($Specification)) {
            return $this->sendError('Specification not found.');
        }

        return $this->sendResponse("Specification", $Specification, '1', 'Specification retrieved successfully.');
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
        $Specification = Specification::find($id);

        $input = $request->all();

        $validator = Validator::make($input, [
            'specification' => ['required', 'max:25' ],
            'remark' => ['max:250'],

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());

        }
        else {
            $Specification->specification = $input['specification'];
            $Specification->remark = $input['remark'];
            $Specification->save();
            return $this->sendResponse("Specification", $Specification, '1', 'Specification Updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $Specification = Specification::find($id);

        if (is_null($Specification)) {
            return $this->sendError('Specification not found.');
        }

            $Specification->delete();
            return $this->sendResponse("Specification", $Specification, '1', 'Specification Deleted successfully');


    }
}
