<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TimeTable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = "timetable";

    protected $hidden = [
        "subject_of_professor_id"
    ];

    protected $fillable = [
        "subject_of_professor_id",
        "building",
        "date",
        "classroom"
    ];

    /**
     *
     * Получение связанного атрибута предмет преподавателя
     *
     * @return BelongsTo
     */
    public function subjectOfProfessor(): BelongsTo
    {
        return $this->belongsTo(SubjectsOfProfessor::class);
    }

    /**
     *
     * Получение связанного атрибута заказ
     *
     * @return HasOne
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class, "timetable_id");
    }
}
