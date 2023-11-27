<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeFeedbackRequest extends FormRequest
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
            'enquiry_id' => ['required', 'max:25'],
            'feedback' => ['max:250'],
            'created_by' => ['max:10'],
        ];
    }
}
