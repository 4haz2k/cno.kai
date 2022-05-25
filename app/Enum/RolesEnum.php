<?php
namespace App\Enum;

/**
 *
 * Class RolesEnum
 * Класс с ролями в системе
 *
 * @package App\Enum
 */
abstract class RolesEnum
{
    /**
     * Преподаватель
     */
    const professor = "PREPOD";

    /**
     * Студент
     */
    const student = "STUDENT";

    /**
     * Администратор
     */
    const admin = "ADMIN";
}
