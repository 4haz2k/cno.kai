<?php

namespace App\Models;

use Carbon\Traits\Date;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property array|Application|int|mixed|null id
 * @property array|Application|mixed|string|null position
 * @property array|Application|mixed|string|null personal_number
 * @property false|mixed|Date date_of_commencement_of_teaching_activity
 * @property array|Application|mixed|string|null department
 * @property array|Application|double|null price
 */
class Professor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = [];

    /**
     *
     * Получение связанного атрибута пользователь
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, "id");
    }

    /**
     *
     * Получение связанного атрибута предметы
     *
     * @return BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, "subjects_of_professor");
    }
}
