<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParentInfoRequest extends FormRequest
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
                'father_name' => ['required', 'string', 'max:255'],
                'mother_name' => ['required','max:255'],
                'primary_mobile_no' => ['required', 'max:25'],
                'secondary_mobile_no' => ['required', 'max:25'],
                'primary_email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'secondary_email' => ['required', 'max:120'],
                'permanent_address' => [ 'max:500'],
                'permanent_mobile_no' => [ 'max:25'],
                'permanent_lan_line_no' => [ 'max:25'],
                'permanent_email' => [ 'max:120'],
                'permanent_post_office' => [ 'max:50'],
                'permanent_lan_mark' => [ 'max:120'],
                'communication_address' => ['required', 'max:500'],
                'communication_mobile_no' => ['required', 'max:25'],
                'communication_lan_line_no' => ['required', 'max:25'],
                'communication_email' => ['required', 'max:120'],
                'communication_post_office' => ['required', 'max:50'],
                'communication_lan_mark' => ['required', 'max:120'],
                'father_occupation' => [ 'max:70'],
                'mother_occupation' => [ 'max:70'],
                'country' => ['required', 'max:50'],
                'state' => ['required', 'max:50'],
                'location' => [ 'max:50'],   
                'user_name' => ['required', 'string', 'max:255'],
                'password' => 'nullable',
                'created_by' => ['max:10'],
                'updated_by' => ['max:10'],

        ];
    }
}
