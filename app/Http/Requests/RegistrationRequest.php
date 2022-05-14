<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed the_same_address
 * @property mixed role
 */
class RegistrationRequest extends FormRequest
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
        $positions = implode(",", config("statics.positions"));

        return [
            // user data
            "email" => "required|email|unique:users,login",
            "telephone" => [
                "required",
                "size:11",
                "regex:^[+]?\d{1,2}[-\s]?\(?\d{3}\)?[-\s]?\d{3}[-\s]?((\d{4})|(\d{2}[-\s]?\d{2}))$"
            ],
            "password" => [
                "required",
                "min:6",
                "regex:/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}/"
            ],
            // passport
            "passport.name" => [
                "required",
                "regex:/(^[А-Я]?[а-я]+$)|(^[A-Z]?[a-z]+$)/u"
            ],
            "passport.surname" => [
                "required",
                "regex:/(^[А-Я]?[а-я]+$)|(^[A-Z]?[a-z]+$)/u"
            ],
            "passport.patronymic" => ["regex:/(^[А-Я]?[а-я]+$)|(^[A-Z]?[a-z]+$)/u"],
            "passport.date_of_birth" => "required|date_format:d.m.Y",
            "passport.sex" => "required|in:M,W",
            "passport.serial" => [
                "required",
                "regex:/^\d{4}$/"
            ],
            "passport.number" => [
                "required",
                "regex:/^\d{6}$/"
            ],
            "passport.issued_by" => [
                "required",
                "regex:/^(([а-яёА-ЯЁ]|\s[а-яёА-ЯЁ]|\.)+|([a-zA-Z]|\s\b|\.)+)$/u"
            ],
            "passport.date_of_issue" => "required|date_format:d.m.Y",
            "passport.department_code" => [
                "required",
                "regex:/^\d{3}-\d{3}$/"
            ],
            "passport.INN" => "required|size:12|unique:passports,ITN|regex:/^\d{12}$/",
            "passport.SNILS" => "required|size:11|unique:passports,INILA|regex:/^\d{11}$/",
            "passport.place_of_residence.country" => [
                "required",
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.region" => [
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.locality" => [
                "required",
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.district" => [
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.street" => [
                "required",
                "regex: /(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.house" => [
                "required",
                "regex:/^\d+(\s?[А-ЯA-Za-zа-я]*)$/u"
            ],
            "passport.place_of_residence.frame" => [
                "regex:/^\d+(\s?[А-ЯA-Za-zа-я]*)$/u"
            ],
            "passport.place_of_residence.apartment" => [
                "regex:/^\d*[А-Яа-яA-Za-z]*$/u"
            ],
            "the_same_address" => "required|boolean",
            // address
            "place_of_residence.country" => [
                "required_if:the_same_address,0,false",
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "place_of_residence.region" => [
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "place_of_residence.locality" => [
                "required_if:the_same_address,0,false",
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "place_of_residence.district" => [
                "regex:/(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "place_of_residence.street" => [
                "required_if:the_same_address,0,false",
                "regex: /(^([А-Я]?[а-я]+\s?-?){0,5}\S$)|(^([A-Z]?[a-z]+\s?-?){0,5}\S$)/u"
            ],
            "place_of_residence.house" => [
                "required_if:the_same_address,0,false",
                "regex:/^\d+(\s?[А-ЯA-Za-zа-я]*)$/u"
            ],
            "place_of_residence.frame" => [
                "regex:/^\d+(\s?[А-ЯA-Za-zа-я]*)$/u"
            ],
            "place_of_residence.apartment" => [
                "regex:/^\d*[А-Яа-яA-Za-z]*$/u"
            ],
            "role" => "required|in:PREPOD,ADMIN,STUDENT",
            "position" => "required_if:role,0,PREPOD|in:$positions",
            "faculty" => [
                "required_if:role,0,PREPOD",
                "regex:/^[\dа-яёА-ЯЁA-Za-z]([\dа-яёА-ЯЁA-Za-z.]|(\s[\dа-яёА-ЯЁA-Za-z]))+$/u"
            ],
            "personal_number" => [
                "required_if:role,0,PREPOD",
                "integer",
                "numeric",
                "regex:/^\d+$/"
            ],
            "exp" => "required_if:role,0,PREPOD|date_format:d.m.Y",
            "price" => [
                "required_if:role,0,PREPOD",
                "regex:/^\d+(\.\d{1,2})?$/"
            ],
            "group_id" => "required_if:role,0,STUDENT|exists:groups,id",
            "receipt_date" => "required_if:role,0,STUDENT|date_format:d.m.Y",
        ];
    }
}
