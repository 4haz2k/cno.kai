<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Получение преподавателей
     *
     * @return JsonResponse
     */
    public function getProfessors()
    {
        if (\request('name'))
            $name = explode(' ', \request('name'));

        $name = \request('name');

        if(\request('subject'))
            $subject = \request('subject');

        // сортировка с помощью предмета
        return response()->json([
            "professors" => Subject::with(["professors"])
                ->where("subjects.title", "LIKE", "%{$subject}%")
                ->get()
                ->pluck("professors")
                ->filter()
                ->flatten()
                ->all()
        ]);

//        return response()->json([
//            "professors" => Subject::with(["professors" => function($q) use ($name){
//                $q->where("professors.ITN", "LIKE", "%".$name[0]."%");
//            }])
////                ->where("subjects.title", "LIKE", "%{$subject}%")
////                ->get()
////                ->pluck("professors")
////                ->filter()
////                ->flatten()
////                ->all()
//        ]);
    }

    /**
     *
     *
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
}
