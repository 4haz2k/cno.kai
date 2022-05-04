<?php

namespace App\Models;

use Carbon\Traits\Date;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\Rule;

/**
 * @property array|Application|mixed|string|null series
 * @property array|Application|mixed|string|null number
 * @property array|Application|mixed|string|null date_of_issue
 * @property array|Application|mixed|string|null issued
 * @property array|Application|mixed|string|null division_code
 * @property array|Application|mixed|string|null ITN
 * @property array|Application|mixed|string|null INILA
 * @property array|Application|mixed|integer|null place_of_residence_id
 * @property array|Application|mixed|string|null secondname
 * @property array|Application|mixed|string|null firstname
 * @property array|Application|mixed|string|null thirdname
 * @property array|Application|mixed|Date|null birthday
 * @property array|Application|mixed|string|null sex
 * @property mixed id
 */
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

    /**
     *
     * Получение связанного атрибута адреса прописки
     *
     * @return BelongsTo
     */
    public function placeOfResidence(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     *
     * Получение связанного атрибута пользователя
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, "id");
    }
}
