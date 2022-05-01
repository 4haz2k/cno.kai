<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            "timetable_id" => "required|exists:timetable,id",
            "number_of_hours" => "required",
            "service_id" => "required|exists:services,id",
            "student_id" => "required|exists:students,id",
        ];
    }
}
