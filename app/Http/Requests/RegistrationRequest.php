<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            "email" => "required|email|unique:users,login",
            "telephone" => [
                "required",
                "size:11",
                "regex:/^([+]?\d{1,2}[-\s]?|)\d{3}[-\s]?\d{3}[-\s]?\d{4}$/"
            ],
            "password" => [
                "required",
                "min:6",
                "regex:/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}/"
            ],
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
                //"regex:/^([а-яА-Яa-zA-Z]+ ?)+$/u"
            ],
            "passport.date_of_issue" => "required|date_format:d.m.Y",
            "passport.department_code" => [
                "required",
                //"regex:/^\d{4}$/"
            ],
            "passport.INN" => "required|digits:12|integer|numeric|unique:passports,ITN",
            "passport.SNILS" => "required|digits:11|integer|numeric|unique:passports,INILA",
            "passport.place_of_residence.country" => [
                "required",
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.region" => [
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.locality" => [
                "required",
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.district" => [
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.street" => [
                "required",
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "passport.place_of_residence.house" => [
                "required",
                "regex:/(^[А-Я]?[а-я0-9]+$)|(^[A-Z]?[a-z0-9]+$)/u"
            ],
            "passport.place_of_residence.frame" => ["regex:/(^[А-Я]?[а-я0-9]+$)|(^[A-Z]?[a-z0-9]+$)/u"],
            "passport.place_of_residence.apartment" => ["regex:/(^[А-Я]?[а-я0-9]+$)|(^[A-Z]?[a-z0-9]+$)/u"],
            "the_same_address" => "required|boolean",
            "place_of_residence.country" => [
                "required_if:the_same_address,0,false",
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "place_of_residence.region" => ["regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"],
            "place_of_residence.locality" => [
                "required_if:the_same_address,0,false",
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "place_of_residence.district" => ["regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"],
            "place_of_residence.street" => [
                "required_if:the_same_address,0,false",
                "regex:/(^([А-Я]?[а-я]+\s?){0,5}\S$)|(^([A-Z]?[a-z]+\s?){0,5}\S$)/u"
            ],
            "place_of_residence.house" => [
                "required_if:the_same_address,0,false",
                "regex:/(^[А-Я]?[а-я0-9]+$)|(^[A-Z]?[a-z0-9]+$)/u"
            ],
            "place_of_residence.frame" => ["regex:/(^[А-Я]?[а-я0-9]+$)|(^[A-Z]?[a-z0-9]+$)/u"],
            "place_of_residence.apartment" => ["regex:/(^[А-Я]?[а-я0-9]+$)|(^[A-Z]?[a-z0-9]+$)/u"],
            "role" => "required|in:PREPOD,ADMIN,STUDENT",
            "position" => "required_if:role,0,PREPOD|in:$positions",
            "faculty" => [
                "required_if:role,0,PREPOD",
                "regex:/^([а-яА-Я]+ ?)+$/"
            ],
            "personal_number" => "required_if:role,0,PREPOD|integer|numeric",
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
