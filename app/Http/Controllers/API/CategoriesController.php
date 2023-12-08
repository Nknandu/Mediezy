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
            'selecteddocters.dataList as selected_doctor_details',
            'categories.id as category_id',
            'categories.category_name',
            'categories.type',
        )
        ->first();

        $selectedDoctors->selected_doctor_details = json_decode($selectedDoctors->selected_doctor_details);
        foreach($selectedDoctors->selected_doctor_details as $key){
            $doctor = Docter::select('id','firstname','lastname','location','Services_at','specialization_id',DB::raw("CONCAT('" . asset('/DocterImages/images') . "/', docter_image) AS docter_image_path"))->where('id',$key->id)->first();
            $check = DB::table('specialize')->where('id', 3)->first();
            $key->firstname = $doctor->firstname;  
            $key->secondname = $doctor->lastname; 
            $key->location = $doctor->location; 
            $key->MainHospital = $doctor->Services_at; 
            $key->docter_image= $doctor->docter_image_path ;
            $key->Specialization = $check->specialization ;
        }
        $data = $selectedDoctors->selected_doctor_details ;
        if(!$data){
            return  response()->json(['status'=>true,'data' => null,'message'=>'Categories not found.']);
        }

    return  response()->json(['status'=>true,'data'=>$data,'message'=>'Category retrieved successfully.']);
    
} 
}