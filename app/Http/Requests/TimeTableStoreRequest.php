<?php

namespace App\Http\Requests;

use App\Models\SubjectsOfProfessor;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed subject_of_professor_id
 * @property mixed building
 * @property mixed date
 * @property mixed classroom
 */
class TimeTableStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            "professor_id" => "exists:professors,id",
            "subject_id" => "exists:subjects,id",
            "date" => "required|date_format:d.m.Y",
            "classroom" => "required",
            "building" => "required"
        ];
    }
}
