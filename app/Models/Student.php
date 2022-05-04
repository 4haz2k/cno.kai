<?php

namespace App\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property array|Application|int|mixed|null id
 * @property array|Application|mixed|string|null group_id
 * @property false|mixed|string receipt_date
 */
class Student extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = [
      "group_id"
    ];

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

    /**
     *
     * Получение связанного атрибута группа
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     *
     * Получение связанного атрибута заказов
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, "student_id");
    }
}
