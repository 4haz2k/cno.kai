<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfessorRequest extends FormRequest
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
            "surname" => "required|",
            "name" => "required|",
            "patronymic" => "",
            "telephone" => "required|",
            "faculty" => "required|",
            "email" => "required|",
            "exp" => "",
            "date_of_birth" => "",
            "position" => "",
            "serial" => "",
            "number" => "",
            "date_of_issue" => "",
            "issued_by" => "",
            "department_code" => "",
            "country" => "",
            "state" => "",
            "city" => "",
            "district" => "",
            "street" => "",
            "house" => "",
            "entrance" => "",
            "apt" => "",
            "INN" => "",
            "SNILS" => "",
            "personal_number" => "",
            "description" => "",
        ];
    }
}
