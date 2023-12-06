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
    public function index()
{
    $categories = Category::all();

    $responseCategories = $categories->map(function ($category) {
        $category->image = $category->image ? url("/img/{$category->image}") : null;
        return $category;
    });

    return $this->sendResponse('categories', $responseCategories, '1', 'Categories retrieved successfully.');
}
    public function store(Request $request)
    {

        $request->validate([
            'category_name' => 'required|string|max:255',
            'type' => 'required|in:doctor,medicine',
            'description' => 'nullable|string',
            'docter_id' => [
                'sometimes:type,doctor',
                'exists:docter,id',
            ],

        ]);


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
            'selecteddocters.id as selected_doctor_id',
            'selecteddocters.dataList as selected_doctor_details',
            'categories.id as category_id',
            'categories.category_name',
            'categories.type'
        )
        ->get();

    if ($selectedDoctors->isEmpty()) {
        return $this->sendError('category', null, 404, 'Selected doctors not found for the category');
    }

    // Extract relevant information for response
    $data = [
        'id' => $category->id,
        'category_name' => $category->category_name,
        'type' => $category->type,
        'selectedDoctors' => $selectedDoctors->map(function ($selectedDoctor) {
            $doctorDetails = json_decode($selectedDoctor->selected_doctor_details, true);

            // Modify the mapping to include only specific details of each doctor
            $doctors = collect($doctorDetails)->map(function ($doctor) {
                // Retrieve additional details of the doctor from the "doctor" table using the "UserId"
                $additionalDoctorDetails = Docter::where('UserId', $doctor['userid'])->first();

                // Retrieve specialization details without id, created_at, and updated_at
                $specializationDetails = Specialize::find($additionalDoctorDetails->specialization_id, ['specialization']);

                return [
                    'id' => $additionalDoctorDetails->id,
                    'UserId' => $additionalDoctorDetails->UserId,
                    'firstname' => $additionalDoctorDetails->firstname,
                    'lastname' => $additionalDoctorDetails->lastname,
                    'location' => $additionalDoctorDetails->location,
                    'docter_image' => url("DocterImages/images/{$additionalDoctorDetails->docter_image}"),
                    'specialization' => $specializationDetails->specialization, // Include specialization details directly
                    'MainHospital' => $additionalDoctorDetails->Services_at,
                ];
            });

            return [
                'selected_doctor_id' => $selectedDoctor->selected_doctor_id,
                'doctor_details' => $doctors,
            ];
        }),
    ];

    return $this->sendResponse('category', $data, 200, 'Category retrieved successfully.');
}
}
