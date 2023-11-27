<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends BaseController
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
        if ($request->hasFile('profile_img')) {
            foreach ($request->file('profile_img') as $file) {
                $imageName = $file->getClientOriginalName();
                $file->move(public_path('BannerImages/images'), $imageName);


                $banner = new Banner();
                $banner->bannerImage = $imageName;



                $banner->save();
            }

            return $this->sendResponse("Banner", $banner, '1', 'Images Uploaded successfully');
        } else {
            return $this->sendError("No images were uploaded.", [], 400);
        }
    }


    /**
     * Display the specified resource.
     */
    public function setFirstImage(Request $request, $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }


        Banner::where('id', '!=', $id)->update(['firstImage' => false]);
        $banner->firstImage = true;
        $banner->save();

        return response()->json(['message' => 'Image set as first image'], 200);
    }



    public function updateFooterImages(Request $request, $id)
{
    $banner = Banner::find($id);

    // Ensure there are no more than 3 images with footerImage set to 1
    $footerImagesCount = Banner::where('footerImage', true)->count();

    if ($footerImagesCount >= 3) {
        // If more than 3 images have footerImage set to 1, reset all to 0
        Banner::where('footerImage', true)->update(['footerImage' => false]);
    }

    // Set the `footerImage` of the specified image to true
    $banner->footerImage = true;
    $banner->save();

    return response()->json(['message' => 'Footer images updated successfully']);
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
