<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     *
     * Получение связанного атрибута преподаватели
     *
     * @return BelongsToMany
     */
    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class, "subjects_of_professor");
    }

    /**
     *
     * Получение связанного атрибута предметы преподавателей
     *
     * @return HasMany
     */
    public function subjectsOfProfessor(): HasMany
    {
        return $this->hasMany(SubjectsOfProfessor::class, "subject_id", "id");
    }
}
