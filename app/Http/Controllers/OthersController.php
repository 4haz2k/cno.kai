<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Order;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OthersController extends Controller
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
     * Возвращает статистику по предметам и их заказам за месяц + топ 10 преподавателей и количество их заказов
     */
    public function getStatistic(): JsonResponse
    {
        // получение данных
        $orders = $this->getOrders(Carbon::now()->toDateString(), Carbon::now()->subMonth()->toDateString());

        // извлечение данных
        $subjects = $orders->pluck("timeTable.subjectOfProfessor.subject");
        $professors = $orders->pluck("timeTable.subjectOfProfessor.professor");
        $orders_count = $orders->count();

        // сортировка
        $subjects_list = $this->sortSubjects($subjects, $orders->toArray());
        $professors_list = $this->sortProfessors($professors);

        // ответ
        return response()->json([
            "subjects" => $subjects_list,
            "professors" => $professors_list,
            "orders_count" => $orders_count
        ]);
    }

    /**
     *
     * Сортировка предметов
     *
     * @param $subjects
     * @param $orders
     * @return array
     */
    private function sortSubjects($subjects, $orders): array
    {
        $subjects_list = [];

        foreach ($subjects as $subject){
            foreach ($orders as $order){
                if($subject->id == $order["time_table"]["subject_of_professor"]["subject"]["id"]){ // если совпадает id предмета и заказа
                    array_push($subjects_list, ["subject" => $subject, "orders_count" => 1]);
                }
            }
        }

        $output = [];

        foreach ($subjects_list as $item) { // подсчитываем одинаковые значения
            $id = $item['subject']['id'];

            if (isset($output[$id])) {
                $output[$id]['orders_count']++;
            } else {
                $output[$id] = $item;
            }
        }

        $subjects_list = [];

        foreach ($output as $value){
            array_push($subjects_list, $value);
        }

        return $subjects_list;
    }

    /**
     *
     * Сортировка преподавателей, ТОП 10
     *
     * @param $professors
     * @return array
     */
    private function sortProfessors($professors): array
    {
        $professors_list = [];

        foreach ($professors as $professor){
            array_push($professors_list, ["professor" => $professor, "orders_count" => 1]);
        }

        $output = [];

        foreach ($professors_list as $item) { // подсчитываем одинаковые значения
            $id = $item['professor']['id'];

            if (isset($output[$id])) {
                $output[$id]['orders_count']++;
            } else {
                $output[$id] = $item;
            }
        }

        $professors_list = [];

        foreach ($output as $value){
            array_push($professors_list, $value);
        }

        usort($professors_list, function($a, $b){
            return ($b['orders_count'] - $a['orders_count']);
        });

        $professors_list = array_slice($professors_list, 0, 10);

        return $professors_list;
    }

    /**
     *
     * Получение списка заказов за месяц
     *
     * @param $current_time
     * @param $previous_time
     * @return mixed
     */
    private function getOrders($current_time, $previous_time){
        return Order::where("create_date", ">=", "2013-01-13 23:20:52")
            ->where("create_date", "<=", "2018-08-30 17:45:07")
            ->with("timeTable.subjectOfProfessor.subject")
            ->with("timeTable.subjectOfProfessor.professor.user.passport")
            ->get()
            ->makeHidden(["timeTable.subjectOfProfessor.professor.user.passport.number"]);
    }

    /**
     *
     * Получение списка услуг
     *
     * @return JsonResponse
     */
    public function getServices(): JsonResponse
    {
        return response()->json(Service::all()->makeHidden(["description"]));
    }

    /**
     *
     * Добавление услуги
     *
     * @param ServiceRequest $request
     * @return JsonResponse
     */
    public function addService(ServiceRequest $request): JsonResponse
    {
        $service = new Service();
        $service->title = $request->title;
        $service->save();

        return response()->json(["message" => "data saved success"]);
    }

    /**
     *
     * Получение факультетов
     *
     */
    public function getFaculties(): JsonResponse
    {
        $faculties = Speciality::select("faculty")->distinct()->pluck("faculty");

        return response()->json($faculties);
    }
}
