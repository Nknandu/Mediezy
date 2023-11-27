<?php

namespace App\Http\Controllers;


use App\Models\Subspecification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class SubspecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subspecification::select( 'id','subspecification','remark')->get();
            return DataTables::of($data)->addIndexColumn()
            ->addColumn('action', function($row){
                $actionBtn = '<div class="text-center actions text-nowrap">

                <button class="edit btn btn_edit me-2" value="'.$row["id"].'" title="Edit">
                  <i class="ri-pencil-line"></i>
                </button>
                <button class="delete btn btn_delete" value="'.$row["id"].'" title="Delete">
                  <i class="ri-delete-bin-6-line"></i>
                </button>
              </div>';
            return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }


       return view('subspecification.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
