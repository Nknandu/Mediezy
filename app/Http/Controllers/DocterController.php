<?php

namespace App\Http\Controllers;

use App\Models\Docter;
use App\Models\Specialize;
use App\Models\Specification;
use App\Models\Subspecification;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class DocterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Docter::select('id',
                'firstname',
                'lastname',
                'docter_image',
                'mobileNo',
                'gender',
                'location',
                'email',
                'specialization_id',
                'specification_id',
                'subspecification_id',
                'about',
                'Services_at',
                'UserId',
                'is_approve',
                'created_at',
                'updated_at')->get();
                return DataTables::of($data)->addIndexColumn()
                ->addColumn('approve_status', function ($row) {

                       $StatusBtn=  '<button  class="btn statusButton approve-button badge bg-success" value="' . $row["id"] . '">Approve</button>

                       <button class="statusButton reject-button badge bg-danger" value="' . $row["id"] . '">Reject</button>';
                         return $StatusBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = "<div class='text-center actions text-nowrap'>
                <a class='edit btn update_hover me-2' href='Docteredit/" . $row["UserId"] . "' title='Edit'>
                  <i class='ri-pencil-line'></i>
                </a>
                <button class='delete btn btn_delete' value='" . $row["UserId"] . "' title='Delete'>
                  <i class='ri-delete-bin-6-line'></i>
                </button>
              </div>";
                    return $actionBtn;
                })
                ->rawColumns(['action', 'approve_status'])
                ->make(true);

        }

        return view('Docter.index');
    }



   public function create(){

    $ListSpecification['Specification']=Specification::all();
    $ListSubspecification['subspecification']=Subspecification::all();
    $ListSpecialization['specialization']=Specialize::all();
    $data=array_merge($ListSpecification,$ListSubspecification,$ListSpecialization);
    return view('Docter.create',$data);
   }


   public function edit(Request $request, $userId)
   {
    $ListSpecification['Specification']=Specification::all();
    $ListSubspecification['subspecification']=Subspecification::all();
    $ListSpecialization['specialization']=Specialize::all();
    $data=array_merge($ListSpecification,$ListSubspecification,$ListSpecialization);
       // Fetch the Docter object
       $doctor = Docter::where('userId', $userId)->first();

       // Use an associative array to pass multiple variables to the view
       return view('Docter.edit', compact('doctor'),$data);
   }

}
