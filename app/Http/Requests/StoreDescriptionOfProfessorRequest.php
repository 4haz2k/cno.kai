<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed id
 * @property mixed value
 */
class StoreDescriptionOfProfessorRequest extends FormRequest
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
            "id" => "required|exists:professors,id",
            "value" => ["required", "regex:/(^[A-Za-zА-ЯЁа-яё\d!?.,:;\"'%]+\s?-?){0,250}\S$/u"]
        ];
    }
}
