<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     *
     * Получение связанного атрибута преподаватель
     *
     * @return HasMany
     */
    public function professors(): HasMany
    {
        return $this->hasMany(Professor::class, "position_id");
    }

    /**
     *
     * Получение связанного атрибута тарифы
     *
     * @return HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class, "position_id");
    }

    /**
     *
     * Получение связанного атрибута услуги
     *
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, "rates");
    }
}
