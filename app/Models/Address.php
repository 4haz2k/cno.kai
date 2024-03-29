<?php

namespace App\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array|Application|mixed|integer|null id
 * @property array|Application|mixed|string|null country
 * @property array|Application|mixed|string|null region
 * @property array|Application|mixed|string|null locality
 * @property array|Application|mixed|string|null district
 * @property array|Application|mixed|string|null street
 * @property array|Application|mixed|string|null house
 * @property array|Application|mixed|string|null frame
 * @property array|Application|mixed|string|null apartment
 */
class Address extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "country",
        "region",
        "locality",
        "district",
        "street",
        "house",
        "frame",
        "apartment"
    ];

    /**
     *
     * Правила валидации
     *
     * @return string[]
     */
    public static function rules(): array{
        return [
            "country" => "required|string",
            "state" => "string", // region
            "city" => "required|string", // locality
            "district" => "string",
            "street" => "required|string",
            "house" => "required|string",
            "entrance" => "string", // frame
            "apt" => "string", // apartment
        ];
    }

    /**
     *
     * Получение связанного атрибута паспорта
     *
     * @return HasMany
     */
    public function passports(): HasMany
    {
        return $this->hasMany(Passport::class, "place_of_residence_id");
    }

    /**
     *
     * Получение связанного атрибута пользователей
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, "actual_place_of_residence_id");
    }
}
