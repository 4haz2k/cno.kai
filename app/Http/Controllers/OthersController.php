<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Http\Requests\StatementRequest;
use App\Models\Order;
use App\Models\Service;
use App\Models\Speciality;
use App\Services\StatementService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OthersController extends Controller
{
    /**
     * Возвращает статистику по предметам и их заказам за месяц + топ 10 преподавателей и количество их заказов
     */
    public function getStatistic(): JsonResponse
    {
        // получение данных
        $orders = $this->getOrders(Carbon::now("Europe/Moscow")->toDateTimeString(), Carbon::now("Europe/Moscow")->subMonth()->toDateTimeString());

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
        $subjects = $this->arrayUniqueKey($subjects, "id"); // удаление повторяющихся значений предметов

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
     * Удаление дубликатов массива по одному ключу
     *
     * @param $array
     * @param $key
     * @return array
     */
    private function arrayUniqueKey($array, $key): array
    {
        $tmp = $key_array = array();
        $i = 0;

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $tmp[$i] = $val;
            }
            $i++;
        }
        return $tmp;
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
        return Order::where("create_date", ">=", $previous_time)
            ->where("create_date", "<=", $current_time)
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

    /**
     * Получение файлов ведомости
     * @param StatementRequest $request
     * @return BinaryFileResponse
     */
    public function getStatement(StatementRequest $request): BinaryFileResponse
    {
        $data = new StatementService(request("professor_id"));
        return response()->download($data->createStatements())->deleteFileAfterSend(true);
    }
}
