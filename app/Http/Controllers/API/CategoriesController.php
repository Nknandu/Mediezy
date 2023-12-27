<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController;
use App\Models\Category;
use App\Models\Docter;
use App\Models\Specialize;
use App\Models\SelectedDocters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CategoriesController extends BaseController
{
    public function index()   //doctor
    {
        $categories = Category::where('type', 'doctor')->get();
        $responseCategories = $categories->map(function ($category) {
            $category->image = $category->image ? url("/img/{$category->image}") : null;
            return $category;
        });
        return $this->sendResponse('categories', $responseCategories, '1', 'Doctor categories retrieved successfully.');
    }
    public function indexs()  //symptoms
{
    $categories = Category::where('type', 'symptoms')->get();
    $responseCategories = $categories->map(function ($category) {
        $category->image = $category->image ? url("/img/{$category->image}") : null;
        return $category;
    });
    return $this->sendResponse('categories', $responseCategories, '1', 'Symptoms categories retrieved successfully.');
}
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'type' => 'required|in:doctor,medicine,symptoms',
            'description' => 'nullable|string',
            'docter_id' => [
                'sometimes:type,doctor',
                'exists:docter,id',
            ],
        ]);
        //Already Exist
        $existingCategory = DB::table('categories')
        ->where('category_name', $request['category_name'])
        ->where('type', $request['type'])
        ->first();
        if ($existingCategory) {
            return $this->sendResponse("category", 'Exists', '0', 'Category name already exists');
        }
        // image uploading
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $imageName);
        }
        if ($request->type == 'doctor') {
            $docter = Docter::all();
        }
        $categoryId = DB::table('categories')->insertGetId([
            'category_name' => $request['category_name'],
            'type' => $request['type'],
            'description' => $request['description'],
            'image' => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $DoctersList = json_decode($request['doctorsList'], true);
        $selectedDocters=new SelectedDocters();
        $selectedDocters->cat_id=$categoryId;
        $selectedDocters->dataList=$DoctersList;
        $selectedDocters->save();
        return $this->sendResponse('category',$selectedDocters, '1', 'Category created successfully.');
    }
//category doctor
public function show($id)
{
    $category = Category::find($id);

    if (!$category) {
        return $this->sendResponse('category', null, 404, 'Category not found.');
    }
    $selectedDoctors = DB::table('selecteddocters')
        ->join('categories', 'selecteddocters.cat_id', '=', 'categories.id')
        ->where('selecteddocters.cat_id', $category->id)
        ->select(
            'selecteddocters.dataList as selected_doctor_details',
            'categories.id as category_id',
            'categories.category_name',
            'categories.type',
        )
        ->first();
    if (!$selectedDoctors) {
        return response()->json(['status' => true, 'data' => null, 'message' => 'Selected doctors not found.']);
    }
    $selectedDoctors->selected_doctor_details = json_decode($selectedDoctors->selected_doctor_details);

    if ($category->type === 'doctor') {
        foreach ($selectedDoctors->selected_doctor_details as $key) {
            $doctor = Docter::select('UserId', 'firstname', 'lastname', 'location', 'Services_at', 'specialization_id', DB::raw("CONCAT('" . asset('/DocterImages/images') . "/', docter_image) AS docter_image_path"))->where('id', $key->id)->first();
            if ($doctor) {
                $check = DB::table('specialize')->where('id', 3)->first();
                $key->UserId = $doctor->UserId;
                $key->firstname = $doctor->firstname;
                $key->secondname = $doctor->lastname;
                $key->location = $doctor->location;
                $key->MainHospital = $doctor->Services_at;
                $key->docter_image = $doctor->docter_image_path;
                $key->Specialization = $check->specialization;
                unset($key->userid);
            }
        }
        $data = $selectedDoctors->selected_doctor_details;

        if (!$data) {
            return response()->json(['status' => true, 'data' => null, 'message' => 'Doctors not found.']);
        }
        return response()->json(['status' => true, 'data' => $data, 'message' => 'Doctors retrieved successfully.']);
    }
    else {
        return response()->json(['status' => true, 'data' => null, 'message' => 'Category is not of type doctor.']);
    }
}


//symptoms category
public function shows($id)
{
    $category = Category::find($id);
    if (!$category) {
        return $this->sendResponse('category', null, 404, 'Category not found.');
    }
    $selectedDoctors = DB::table('selecteddocters')
        ->join('categories', 'selecteddocters.cat_id', '=', 'categories.id')
        ->where('selecteddocters.cat_id', $category->id)
        ->select(
            'selecteddocters.dataList as selected_doctor_details',
            'categories.id as category_id',
            'categories.category_name',
            'categories.type',
        )
        ->first();
    if (!$selectedDoctors) {
        return response()->json(['status' => true, 'data' => null, 'message' => 'Selected doctors not found.']);
    }
    $selectedDoctors->selected_doctor_details = json_decode($selectedDoctors->selected_doctor_details);
    if ($category->type === 'symptoms') {
        foreach ($selectedDoctors->selected_doctor_details as $key) {
            $doctor = Docter::select('id', 'firstname', 'lastname', 'location', 'Services_at', 'specialization_id', DB::raw("CONCAT('" . asset('/DocterImages/images') . "/', docter_image) AS docter_image_path"))->where('id', $key->id)->first();
            if ($doctor) {
                $check = DB::table('specialize')->where('id', 3)->first();
                $key->firstname = $doctor->firstname;
                $key->secondname = $doctor->lastname;
                $key->location = $doctor->location;
                $key->MainHospital = $doctor->Services_at;
                $key->docter_image = $doctor->docter_image_path;
                $key->Specialization = $check->specialization;
            }
        }
        $data = $selectedDoctors->selected_doctor_details;
        if (!$data) {
            return response()->json(['status' => true, 'data' => null, 'message' => 'Doctors not found.']);
        }
        return response()->json(['status' => true, 'data' => $data, 'message' => 'Doctors retrieved successfully.']);
    } else {
        return response()->json(['status' => true, 'data' => null, 'message' => 'Category is not of type Symptoms.']);
    }
}
//delete
public function destroy($id)
{
    $category = Category::find($id);
    if (!$category) {
        return $this->sendResponse('category', null, 404, 'Category not found.');
    }
    $selectedDoctors = SelectedDocters::where('cat_id', $category->id)->first();
    if ($selectedDoctors) {
        $selectedDoctors->delete();
    }
    $category->delete();
    return $this->sendResponse('category', null, '1', 'Category deleted successfully.');
}
//update
public function update(Request $request, $id)
{
    $request->validate([
        'category_name' => 'sometimes|required|string|max:255',
        'type' => 'sometimes|required|in:doctor,medicine',
        'description' => 'nullable|string',
        'docter_id' => [
            'sometimes',
            'required_if:type,doctor',
            'exists:docter,id',
        ],
    ]);
    $category = Category::find($id);
    if (!$category) {
        return $this->sendResponse('category', null, 404, 'Category not found.');
    }
    $category->category_name = $request->input('category_name', $category->category_name);
    $category->type = $request->input('type', $category->type);
    $category->description = $request->input('description', $category->description);
     //$category->image = $request->input('image', $category->image);
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('img'), $imageName);
        $category->image = $imageName;
    }
    $category->save();
    $selectedDocters = SelectedDocters::where('cat_id', $category->id)->first();
    if ($selectedDocters) {
        $DoctersList = json_decode($request->input('doctorsList','[]'), true);
        $selectedDocters->dataList=$DoctersList;
        $selectedDocters->save();
    }
    return $this->sendResponse('category', $category, 200, 'Category updated successfully.');
}
//edit
public function edit($id)
{
    $category = Category::findOrFail($id);
    $selectedDocters = DB::table('selecteddocters')
        ->where('cat_id', $id)
        ->first();
    $doctorData = [];
    if ($selectedDocters && property_exists($selectedDocters, 'dataList') && $selectedDocters->dataList) {
        $doctorIds = json_decode($selectedDocters->dataList, true);
        // Flatten the array
        $doctorIds = array_flatten($doctorIds);
        if (is_array($doctorIds) && count($doctorIds) > 0) {
            $doctors = DB::table('docter')
                ->whereIn('id', $doctorIds)
                ->get();
            foreach ($doctors as $doctor) {
                $doctorData[] = [
                    'id' => $doctor->id,
                    'firstname' => $doctor->firstname,
                ];
     }  } }
    $imagePath = $category->image;
    return response()->json([
        'success' => true,
        'category' => $category,
        'selectedDocters' => [
            'id' => optional($selectedDocters)->id,
            'cat_id' => optional($selectedDocters)->cat_id,
            'dataList' => $doctorData,
            'created_at' => optional($selectedDocters)->created_at,
            'updated_at' => optional($selectedDocters)->updated_at,
        ],
        'imagePath' => $imagePath,
    ]);
}
}
