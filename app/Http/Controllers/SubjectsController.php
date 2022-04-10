<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectsController extends Controller
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
     * Получение всех предметов
     *
     * @return JsonResponse
     */
    public function getSubjects(): JsonResponse
    {
        if(\request("professor_id"))
            $subjects = Subject::whereHas("professors", function ($q){ $q->where("professors.id", \request("professor_id")); })->get()->makeHidden(["description"]);
        else
            $subjects = Subject::all()->makeHidden(["description"]);

        return response()->json([
            "subjects" => $subjects
        ]);
    }
}
