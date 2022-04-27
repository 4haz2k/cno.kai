<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectOfProfessorRequest;
use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use App\Models\SubjectsOfProfessor;
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

    /**
     * Добавление нового предмета
     * @param SubjectRequest $request
     * @return JsonResponse
     */
    public function addSubjects(SubjectRequest $request): JsonResponse
    {
        $subject = new Subject();

        $subject->title = \request("title");
        $subject->description = \request("description");

        $subject->save();

        return response()->json(["message" => "data saved success"]);
    }

    /**
     *
     * Добавление предмета преподавателю
     *
     * @param SubjectOfProfessorRequest $request
     * @return JsonResponse
     */
    public function addSubjectsProfessor(SubjectOfProfessorRequest $request): JsonResponse
    {
        $subject_of_professor = new SubjectsOfProfessor();

        $subject_of_professor->subject_id = $request->subject_id;
        $subject_of_professor->professor_id = $request->professor_id;

        $subject_of_professor->save();

        return response()->json(["message" => "data saved success"]);
    }

    /**
     *
     * Удаление предмета преподавателя
     *
     * @return JsonResponse
     */
    public function deleteSubjectsProfessor(): JsonResponse
    {
        if(!\request("subject_of_professor_id"))
            return response()->json(["error" => "subject of professor id empty"]);

        $subject_of_professor = SubjectsOfProfessor::find(\request("subject_of_professor_id"));

        if($subject_of_professor)
            $subject_of_professor->delete();
        else
            return response()->json(["error" => "subject of professor not found by id ".\request("subject_of_professor_id")]);

        return response()->json(["message" => "data deleted success"]);
    }
}
