<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeTableStoreRequest;
use App\Models\SubjectsOfProfessor;
use App\Models\TimeTable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class TimeTableController extends Controller
{
    public function getTimeTable(): JsonResponse
    {
        $timetable = TimeTable::with("subjectOfProfessor.subject");

        if(\request("professor_id")){
            $timetable = $timetable->whereHas("subjectOfProfessor", function ($q){
                $q->where("professor_id", \request("professor_id"));
            });
        }

        if(\request("subject_id")){
            $timetable = $timetable->whereHas("subjectOfProfessor", function ($q){
                $q->where("subject_id", \request("subject_id"));
            });
        }

        if(\request("date")){
            $timetable = $timetable->where("date",
                Carbon::parse(\request("date"))->format(Carbon::DEFAULT_TO_STRING_FORMAT)
            );
        }

        if(\request("building")){
            $timetable = $timetable->where("building", \request("building"));
        }

        if (\request("actual")){
            $timetable = $timetable->where("date", ">=", Carbon::now("Europe/Moscow")->format("Y-m-d"));
        }

        $timetable = $timetable->orderBy("date")->paginate(\request("page_size") ? : 10)->toArray();

        unset(
            $timetable["links"],
            $timetable["to"],
            $timetable["prev_page_url"],
            $timetable["path"],
            $timetable["next_page_url"],
            $timetable["last_page_url"],
            $timetable["first_page_url"],
            $timetable["from"],
            $timetable["last_page"]
        );

        return response()->json($timetable);
    }

    /**
     *
     * Добавление нового расписания
     *
     * @param TimeTableStoreRequest $request
     * @return JsonResponse
     */
    public function addTimeTable(TimeTableStoreRequest $request): JsonResponse
    {
        $subject_of_professor_id = SubjectsOfProfessor::where("professor_id", $request->professor_id)
            ->where("subject_id", $request->subject_id)
            ->pluck("id")
            ->first();

        if(empty($subject_of_professor_id)){
            return response()->json(["message" => "subject_of_professor_id not found by subject_id and professor_id"], 422);
        }

        $timetable = new TimeTable([
            "subject_of_professor_id" => $subject_of_professor_id,
            "building" => $request->building,
            "date" => Carbon::createFromFormat("d.m.Y", $request->date)->format("Y-m-d"),
            "classroom" => $request->classroom
        ]);

        return $timetable->save() ?
            response()->json(["message" => "timeTable saved"]) :
            response()->json(["message" => "timeTable save error"], 500);
    }
}
