<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeStaffRequest extends FormRequest
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
    { return [

        'name' => ['required', 'max:50'],
        'email' => ['required', 'max:25'],
        'mobile_no' => ['required', 'max:15'],
        'department_id' => ['required'],
        'remarks' => ['max:250'],
        'password' => ['required'],
        'confirm_password' => ['required'],
    ];
    }
}
