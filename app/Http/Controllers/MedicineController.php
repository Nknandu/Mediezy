<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class MedicineController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Medicine::select('id','user_id','docter_id','medicineName','Dosage','NoOfDays','MorningBF','MorningAF','Noon','night')->get();
                return DataTables::of($data)->addIndexColumn()
                ->addColumn('approve_status', function ($row) {

                       $StatusBtn=  '<button  class="btn statusButton approve-button badge bg-success" value="' . $row["id"] . '">Approve</button>

                       <button class="statusButton reject-button badge bg-danger" value="' . $row["id"] . '">Reject</button>';
                         return $StatusBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="text-center actions text-nowrap">
                        <button class="edit btn btn_edit me-2" value="' . $row["id"] . '" title="Edit">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <button class="delete btn btn_delete" value="' . $row["id"] . '" title="Delete">
                            <i class="ri-delete-bin-6-line"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'approve_status'])
                ->make(true);

        }

        return view('medicalprescription.index');
    }
}
