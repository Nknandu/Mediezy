<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeStreamRequest extends FormRequest
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
            'qualification' => [ 'max:25'],
            'stream' => ['required','max:250'],
            'created_by' => ['max:10'],
        ];
    }
}
