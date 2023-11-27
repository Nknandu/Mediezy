<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
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
            'organization_name' => ['required', 'max:35'],
            'code' => ['required', 'max:50'],
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
            'gst_no' => ['required', 'max:30'],
            'pan_no' => ['required', 'max:30'],
            'website' => [ 'max:100'],
            'country' => ['required', 'max:50'],
            'state' => ['required', 'max:50'],
            'location' => [ 'max:50'],
            'headding' => ['required', 'max:150'],    
            'subheading' => ['required', 'max:200'],          
            'created_by' => ['max:10'],
            'updated_by' => ['max:10'],

        ];
    }
}
