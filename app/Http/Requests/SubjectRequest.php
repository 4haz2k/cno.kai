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
            "title" => [
                "required",
                "unique:subjects,title",
                "regex:/^[а-яёА-ЯЁA-Za-z]([а-яёА-ЯЁA-Za-z.]|(\s[а-яёА-ЯЁA-Za-z]))+$/u"
            ],
            "description" => [
                "max:200",
                "regex:/^[а-яёА-ЯЁA-Za-z]([\dа-яёА-ЯЁA-Za-z.-]|(\s[\dа-яёА-ЯЁA-Za-z]))+$/u"
            ]
        ];
    }
}
