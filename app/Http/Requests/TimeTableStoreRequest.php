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
     * Добавление параметра subject_of_professor_id
     */
    protected function prepareForValidation()
    {
        // Добавление параметра subject_of_professor_id, который я получаю с помощью professor_id и subject_id.
        // Нужно для того, чтобы в валидации проверить, найден ли предмет преподавателя по данным параметрам, чтобы можно было
        // добавить новую запись расписания
        $this->request->add([
            "subject_of_professor_id" => SubjectsOfProfessor::where("professor_id", $this->professor_id)
                ->where("subject_id", $this->subject_id)
                ->pluck("id")
                ->first(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "subject_of_professor_id" => "exists:subjects_of_professor,id",
            "date" => "required|date_format:d.m.Y",
            "classroom" => "required",
            "building" => "required"
        ];
    }

    /**
     *
     * Специфичное сообщение на subject_of_professor_id.exists
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            "subject_of_professor_id.exists" => "not found subject of professor by specified subject_id and professor_id"
        ];
    }
}
