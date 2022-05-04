<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Subject;
use App\Models\SubjectsOfProfessor;
use App\Models\TimeTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfessorsController extends Controller
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
     * Возвращает список преподавателей и предметы
     *
     * @return JsonResponse
     */
    public function getProfessors(): JsonResponse
    {
        $subjects = Subject::all()->toArray();

        $professors = Professor::with(["subjects", "user.passport"])
            ->whereHas("subjects", function ($query){
                $query->where("subject_id", "!=", null);
            })
            ->paginate(\request("page_size") ? : 10)
            ->toArray();

        unset(
            $professors["links"],
            $professors["to"],
            $professors["prev_page_url"],
            $professors["path"],
            $professors["next_page_url"],
            $professors["last_page_url"],
            $professors["first_page_url"],
            $professors["from"],
            $professors["last_page"]
        );

        return response()->json($professors + ["subjects" => $subjects]);
    }

    /**
     *
     * Получение преподавателей по id предмета
     *
     * @return JsonResponse
     */
    public function getProfessorsById(): JsonResponse
    {
        if($subject_id = \request('subject_id')) {
            $professors_array = Professor::whereHas("subjects", function ($query) use ($subject_id) { $query->where("subjects.id", "=", $subject_id);})
                ->with([
                    "user.passport",
                ])
                ->paginate(5);
        }
        else{
            $professors_array = Professor::whereHas("subjects")
                ->with([
                    "user.passport",
                ])
                ->paginate(5);
        }

        $result = [];

        foreach ($professors_array as $item){
            $result["data"][] = [
                "id" => $item->id,
                "firstname" => $item->user->passport->firstname,
                "secondname" => $item->user->passport->secondname,
                "thirdname" => $item->user->passport->thirdname,
                "rank" => $item->position,
                "subjects" => $item->subjects,
                "price" => $item->price
            ];
        }

        $result["total_pages"] = ceil($professors_array->total() / 5);

        return response()->json($result);
    }

    /**
     *
     * Получение фио преподов и их id
     *
     * @return JsonResponse
     */
    public function getShortProfessors(): JsonResponse
    {
        $professors = Professor::with([
            "user.passport"
        ]);

        if(\request("subject_id"))
            $professors = $professors->whereHas("subjects", function ($q) { $q->where("subject_id", \request("subject_id")); });

        $professors = $professors->get();

        $professors_list = [];

        foreach ($professors as $professor) {
             array_push($professors_list, [
                 "id" => $professor->id,
                 "name" => $professor->user->passport->firstname,
                 "secondname" => $professor->user->passport->secondname,
                 "thirdname" => $professor->user->passport->thirdname,
                 "fullname" => "{$professor->user->passport->secondname} {$professor->user->passport->firstname} {$professor->user->passport->thirdname}",
             ]);
        }

        return response()->json($professors_list);
    }

    /**
     *
     * Получение пропода по его ID
     *
     * @return JsonResponse
     */
    public function getSingle(): JsonResponse
    {
        if(!\request("professor_id")){
            return response()->json(["error" => "parameter professor_id empty"]);
        }

        $professor = Professor::where("id", \request("professor_id"))
        ->with([
            "user" => function($q){ $q->select(["id", "login", "phone"]); },
            "user.passport" => function($q){ $q->select(["id", "place_of_residence_id", "series", "number", "date_of_issue", "issued", "division_code", "ITN", "INILA", "secondname", "firstname", "thirdname", "birthday"]); },
            "user.passport.placeOfResidence",
        ])->first();

        if(!$professor){
            return response()->json(["error" => "professor not found"], 404);
        }

        return response()->json($professor);
    }

    /**
     * Получение списка преподавателей для страницы преподаватели
     */
    public function pageProfessorsList(): JsonResponse
    {
        $professors = SubjectsOfProfessor::with([
            "subject" => function ($q){ $q->select(["id", "title"]); },
            "professor" => function($q) { $q->select(["id", "position"]);},
            "professor.user" => function($q) { $q->select(["id"]);},
            "professor.user.passport" => function ($q) { $q->select(["id", "firstname", "secondname", "thirdname"]);}
        ]);

        if(\request("subject_id"))
            $professors = $professors->whereHas("subject", function ($q) { $q->where("id", \request("subject_id")); });

        if(\request("position"))
            $professors = $professors->whereHas("professor", function ($q) { $q->where("position", \request("position")); });

        $professors = $professors
            ->paginate(\request("page_size") ? : 10)
            ->toArray();

        unset(
            $professors["links"],
            $professors["to"],
            $professors["prev_page_url"],
            $professors["path"],
            $professors["next_page_url"],
            $professors["last_page_url"],
            $professors["first_page_url"],
            $professors["from"],
            $professors["last_page"]
        );

        return response()->json($professors);
    }

    /**
     *
     * Должности преподавателей
     *
     * @return JsonResponse
     */
    public function positionsList(): JsonResponse
    {
        return response()->json(config("statics.positions"));
    }

    /**
     *
     * Получение расписания
     *
     * @return JsonResponse
     */
    public function getProfessorTimeTable(): JsonResponse
    {
        if(!\request("subject_id") and !\request("professor_id")){
            return response()->json([]);
        }

        $timetable = TimeTable::whereHas("subjectOfProfessor", function ($q){ $q->where("subject_id", \request("subject_id")); })
            ->whereHas("subjectOfProfessor", function ($q){ $q->where("professor_id", \request("professor_id")); })
            ->get();

        foreach ($timetable as $item){
            $item->date = date("m.d.Y", strtotime($item->date));
        }

        return response()->json($timetable);
    }
}
