<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CategoriesController extends Controller
{
   public function index(Request $request){
    if ($request->ajax()) {
        $data = Category::select( 'id','category_name',
        'type',
        'description',
        'image',)->get();
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
   return view('categories');
}

   }

