<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Speciality;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth:api', ['except' => ['login', 'registration']]);
//    }

    /**
     *
     * Возвращает список студентов с параметрами group_id и speciality_id
     *
     * @return JsonResponse
     */
    public function getStudents(): JsonResponse
    {
        $students = Student::with(["user.passport", "group.speciality"]);

        if(\request("group_id")){ // если есть id группы
            $students = $students->whereHas("group", function ($query) { $query->where("id", \request("group_id")); });
        }

        if(\request("speciality_id")){ // если есть id специальности
            $students = $students->whereHas("group.speciality", function ($query) { $query->where("id", \request("speciality_id")); });
        }

        $students = $students->paginate(\request("page_size") ? : 10)->toArray();

        unset(
            $students["links"],
            $students["to"],
            $students["prev_page_url"],
            $students["path"],
            $students["next_page_url"],
            $students["last_page_url"],
            $students["first_page_url"],
            $students["from"],
            $students["last_page"]
        );

        return response()->json($students);
    }
}
