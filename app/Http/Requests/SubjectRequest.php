<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "title" => "required|unique:subjects,title|regex:/^([а-яА-Яa-zA-Z]+ ?)+$/g",
            "description" => "max:200|regex:/^([а-яА-Яa-zA-Z-_.!;0-9]+ ?)+$/g"
        ];
    }
}
