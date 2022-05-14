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
            "title" => [
                "required",
                "unique:specialty,specialty_title",
                "regex: /^[\dа-яёА-ЯЁA-Za-z]([\dа-яёА-ЯЁA-Za-z.]|(\s[\dа-яёА-ЯЁA-Za-z]))+$/u"
            ],
            "faculty" => [
                "required",
                "regex:/^[\dа-яёА-ЯЁA-Za-z]([\dа-яёА-ЯЁA-Za-z.]|(\s[\dа-яёА-ЯЁA-Za-z]))+$/u"
            ]
        ];
    }
}
