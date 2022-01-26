<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speciality extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     *
     * Получение связанного атрибута специальности
     *
     * @return HasMany
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, "specialty_id");
    }
}
