<?php

namespace App\Http\Controllers;

use App\Models\Docter;
use App\Models\DocterAvailability;
use App\Models\schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = schedule::select('schedule.id','docter.firstname AS DocterName','schedule.session_title','schedule.date','schedule.startingTime','schedule.endingTime','schedule.tokens','schedule.TokenCount')
            ->leftJoin('docter','schedule.docter_id','=','docter.id')->get();
            return DataTables::of($data)->addIndexColumn()
            ->addColumn('action', function($row){
                $actionBtn = '<div class="text-center actions text-nowrap">

                <button class="view btn btn_view me-2" value="'.$row["id"].'" title="View">
                <i class="ri-eye-line"></i>
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
        $ListDocter['Docter']=Docter::all();
        return view('Schedule.index',$ListDocter);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $getIdFromLogin = Auth::user()->id;
        $userRole = Auth::user()->user_role;
        $getDoctorData = Docter::select('id', 'firstname')->where('UserId', $getIdFromLogin)->get();

        if ($userRole == 2) {
            $doctorIds = $getDoctorData->pluck('id');
            $listDoctors = Docter::select('id', 'firstname')->whereIn('id', $doctorIds)->get();
        } else {
            $listDoctors = Docter::all();

        }

        return view('Schedule.ShowToken', ['Docter' => $listDoctors]);
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
