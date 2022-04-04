<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Subject;
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

        $professors = Professor::with(["subjects", "position", "user.passport"])
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
                    "position"
                ])
                ->paginate(5);
        }
        else{
            $professors_array = Professor::whereHas("subjects")
                ->with([
                    "user.passport",
                    "position"
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
                "rank" => $item->position->rank,
                "subjects" => $item->subjects
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
       $professors = Professor::with(["user.passport"])->get();

       $professors_list = [];

       foreach ($professors as $professor) {
            array_push($professors_list, [
                "id" => $professor->id,
                "name" => $professor->user->passport->firstname,
                "secondname" => $professor->user->passport->secondname,
                "thirdname" => $professor->user->passport->thirdname,
                "fullname" => "{$professor->user->passport->secondname} {$professor->user->passport->firstname} {$professor->user->passport->thirdname}"
            ]);
       }

       return response()->json($professors_list);
    }
}
