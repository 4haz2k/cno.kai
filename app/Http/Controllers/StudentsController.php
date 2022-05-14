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

    /**
     *
     * Получение студента по его ID
     *
     * @return JsonResponse
     */
    public function getSingle(): JsonResponse
    {
        if(!\request("student_id"))
            return response()->json(["error" => "parameter student_id empty"]);

        $student = Student::where("id", \request("student_id"))
            ->with([
                "user" => function($q){ $q->select(["id", "login", "phone", "actual_place_of_residence_id"]); },
                "user.actualPlaceOfResidence",
                "user.passport" => function($q){ $q->select(["id", "place_of_residence_id", "series", "number", "date_of_issue", "issued", "division_code", "ITN", "INILA", "secondname", "firstname", "thirdname", "birthday"]); },
                "user.passport.placeOfResidence",
                "group" => function ($q) { $q->select(["id", "specialty_id",  "group_code"]); },
                "group.speciality" => function ($q) { $q->select(["id", "faculty"]); },
            ])
            ->first();

        if(!$student){
            return response()->json(["error" => "student not found"]);
        }

        if($student->user->actual_place_of_residence_id == $student->user->passport->place_of_residence_id){
            $flag = ["is_actual" => true];
        }
        else{
            $flag = ["is_actual" => false];
        }

        return response()->json($student->toArray() + $flag);
    }
}
