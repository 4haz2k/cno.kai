<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Passport extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "series",
        "number",
        "date_of_issue",
        "issued",
        "division_code",
        "scan",
        "place_of_residence_id",
        "secondname",
        "firstname",
        "thirdname",
        "birthday",
        "sex"
    ];

    protected $hidden = [
        "id",
        "place_of_residence_id"
    ];

    /**
     *
     * Правила валидации
     *
     * @return string[]
     */
    public static function rules(): array{
        return [
            "serial" => "required|integer", // series
            "number" => "required|integer",
            "date_of_issue" => "required|date",
            "issued_by" => "required|string", // issued
            "department_code" => "required|integer", // division_code
            "scan" => "string",
            //"place_of_residence_id" => "required|integer|exists:addresses,id",
            "surname" => "required|string", // secondname
            "name" => "required|string", // firstname
            "patronymic" => "string", // thirdname
            "date_of_birth" => "required|date", // birthday
            "sex" => [
                "required",
                Rule::in(['M', 'W'])
            ],
        ];
    }

    public function placeOfResidence(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
