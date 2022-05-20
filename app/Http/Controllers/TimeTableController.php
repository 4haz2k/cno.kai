<?php

namespace App\Http\Controllers;

use App\Models\TimeTable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $timetable = $timetable->paginate(\request("page_size") ? : 10)->toArray();

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
}
