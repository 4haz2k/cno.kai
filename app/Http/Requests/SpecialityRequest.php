<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialityRequest extends FormRequest
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
            "title" => "required|unique:specialty,specialty_title|regex:/^([а-яА-Я]+ ?)+$/g",
            "faculty" => "required|regex:/^([а-яА-Я]+ ?)+$/g"
        ];
    }
}
