<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
