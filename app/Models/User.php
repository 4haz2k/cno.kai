<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property array|Application|mixed|integer|null id
 * @property array|Application|mixed|string|null login
 * @property array|Application|mixed|string|null password
 * @property array|Application|mixed|string|null phone
 * @property array|Application|mixed|string|null role
 * @property array|Application|int|mixed|null actual_place_of_residence_id
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "login",
        'password',
        "phone",
        "role"
    ];

    /**
     *
     * Правила валидации
     *
     * @return string[]
     */
    public static function rules(): array{
        return [
            //"actual_place_of_residence_id" => "integer|exists:addresses,id",
            "email" => "required|string|unique:users,login", // login
            'password' => "required|string",
            "telephone" => "required|string", // phone
            "role" => "required|string",
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     *
     * Получение связанного атрибута паспорта
     *
     * @return HasOne
     */
    public function passport(): HasOne
    {
        return $this->hasOne(Passport::class, "id");
    }

    /**
     *
     * Получение связанного атрибута студента
     *
     * @return HasOne
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, "id");
    }

    /**
     *
     * Получение связанного атрибута преподаватель
     *
     * @return HasOne
     */
    public function professor(): HasOne
    {
        return $this->hasOne(Professor::class, "id");
    }

    /**
     *
     * Получение связанного атрибута фактического адреса проживания
     *
     * @return BelongsTo
     */
    public function actualPlaceOfResidence(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
