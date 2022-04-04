<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectsOfProfessor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "subjects_of_professor";

    protected $hidden = [
        "subject_id",
        "professor_id"
    ];

    /**
     *
     * Получение связанного атрибута предмет
     *
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     *
     * Получение связанного атрибута преподаватель
     *
     * @return BelongsTo
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    /**
     *
     * Получение связанного атрибута расписание
     *
     * @return HasMany
     */
    public function timeTable(): HasMany
    {
        return $this->hasMany(TimeTable::class, "subject_of_professor_id");
    }

}
