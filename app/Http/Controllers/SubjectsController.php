<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectOfProfessorRequest;
use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use App\Models\SubjectsOfProfessor;
use Illuminate\Http\JsonResponse;

class SubjectsController extends Controller
{
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
            $subjects = Subject::all();

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
        if(!\request("subject_id") or !\request("professor_id"))
            return response()->json(["error" => "subject_id or professor_id is empty"]);

        $subject_of_professor = SubjectsOfProfessor::where("subject_id", \request("subject_id"))->where("professor_id", \request("professor_id"))->first();

        if($subject_of_professor)
            $subject_of_professor->delete();
        else
            return response()->json(["error" => "subject of professor not found by subject_id ".\request("subject_id")." and professor_id ".\request("professor_id")]);

        return response()->json(["message" => "data deleted success"]);
    }
}
