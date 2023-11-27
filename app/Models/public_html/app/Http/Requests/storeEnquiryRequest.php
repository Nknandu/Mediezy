<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeEnquiryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'max:20'],
            'dob' => 'required',
            'remarks' => 'required',
            'religion_id' => 'required',
            'caste_id' => 'required',
            'education' => 'required',
            'streem' => 'required',
            'colg_schl' => 'required',
            'photo' => 'required',
            'country' => 'required',
            'state' => 'required',
            'location'=>'required',
            'address'=>'required',
            'pincode'=>'required',
            'mob_no'=>'required',
            'email' => ['required', 'string', 'email', 'max:120'],
            'enq_date'=>'required',
            'enq_taken_by'=>'required',
            'course'=>'required',
            'discount'=>'required',
            'enq_source'=>'required',
            'enq_stage'=>'required',
            'next_folow_up'=>'required',
            'created_by'=>'required',
            'updated_by'=>'required'
        ];
    }
}
