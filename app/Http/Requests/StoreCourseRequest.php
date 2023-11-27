<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'course_provider_id'=>['required','max:30'],
            'code'=>['max:20'],
            'course_name'=>['required','max:50'],
            'printable_name'=>['max:50'],
            'batch_course'=>['max:50'],
            'department_id'=>['required','max:50'],
            'course_category_id'=>['required','max:50'],
            'course_type_id'=>['required','max:50'],
            'zonal_discount'=>['max:50'],
            'requirment_id' => ['required','max:50'],
            'Requirement' => ['required','max:50'],
            'course_id' => ['required','max:50'],
            'created_by' => ['max:10'],
        ];
    }
}
