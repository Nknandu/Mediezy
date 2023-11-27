<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Banner::select('id','bannerImage','firstImage','footerImage')->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('firstImage', function ($row) {
                    $isActiveBtn = '<label class="toggle-btn">';
                    $isActiveBtn .= '<input type="checkbox" class="toggle-checkbox" name="firstImage" data-id="' . $row["id"] . '" onclick="setAsFirstImage(this)" ' . ($row["firstImage"] ? 'checked' : '') . '>';
                    $isActiveBtn .= '<span class="toggle-slider"></span>';
                    $isActiveBtn .= '</label>';
                    return $isActiveBtn;
                })
                ->addColumn('footerImage', function ($row) {
                    $isDefaultBtn = '<label class="toggle-btn">';
                    $isDefaultBtn .= '<input type="checkbox" class="toggle-checkbox" name="footerImage" data-id="' . $row["id"] . '" onclick="setAsFooterImage(this)" ' . ($row["footerImage"] ? 'checked' : '') . '>';
                    $isDefaultBtn .= '<span class="toggle-slider"></span>';
                    $isDefaultBtn .= '</label>';
                    return $isDefaultBtn;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="text-center actions text-nowrap">
                    <button class="edit btn btn_edit me-2" value="' . $row["id"] . '"><i class="ri-pencil-line"></i></button>
                    <button class="delete btn btn_delete" value="' . $row["id"] . '"><i class="ri-delete-bin-6-line"></i></button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['firstImage', 'footerImage', 'action'])
                ->make(true);
        }

        return view('Banner.index');
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
