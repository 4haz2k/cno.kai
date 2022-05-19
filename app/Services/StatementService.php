<?php
namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;
use PhpOffice\PhpWord\TemplateProcessor;

class StatementService
{
    private $professor_id;
    private $firstRange;
    private $lastRange;

    /**
     * StatementService constructor.
     * @param null $_professor_id
     */
    public function __construct($_professor_id = null)
    {
        $this->professor_id = $_professor_id;
        $date = Carbon::now("Europe/Moscow");
        $this->firstRange =  $date->subMonth()->firstOfMonth()->toDateTimeString();
        $this->lastRange = $date->lastOfMonth()->toDateTimeString();
    }

    /**
     *
     * Получение списка заказов
     *
     * @return Builder[]|Collection
     */
    private function getOrdersByRange(){

        if(!$this->professor_id){
            return Order::with([
                "service" => function($q){ $q->select(["id", "title"]); },
                "timeTable" => function($q){ $q->select(["id", "subject_of_professor_id"]); },
                "timeTable.subjectOfProfessor.professor" => function($q){ $q->select(["id", "position", "personal_number", "department"]); },
                "timeTable.subjectOfProfessor.professor.user.passport" => function($q){ $q->select(["id", "secondname", "firstname", "thirdname"]); },
                "student" => function($q){ $q->select(["id", "group_id"]); },
                "student.group" => function($q){ $q->select(["id", "group_code"]); },
            ])
                ->where("create_date", ">=", $this->firstRange)
                ->where("create_date", "<=", $this->lastRange)
                ->select(["id", "student_id", "service_id", "timetable_id", "create_date"])
                ->get();
        }
        else{
            return Order::with([
                "service" => function($q){ $q->select(["id", "title"]); },
                "timeTable" => function($q){ $q->select(["id", "subject_of_professor_id"]); },
                "timeTable.subjectOfProfessor.professor" => function($q){ $q->select(["id", "position", "personal_number", "department"]); },
                "timeTable.subjectOfProfessor.professor.user.passport" => function($q){ $q->select(["id", "secondname", "firstname", "thirdname"]); },
                "student" => function($q){ $q->select(["id", "group_id"]); },
                "student.group" => function($q){ $q->select(["id", "group_code"]); },
            ])
                ->where("create_date", ">=", $this->firstRange)
                ->where("create_date", "<=", $this->lastRange)
                ->whereHas("timeTable.subjectOfProfessor.professor", function ($q){ $q->where("id", $this->professor_id); })
                ->get();
        }
    }

    /**
     *
     * Сортировка заказов по преподавателям и получение списка услуг
     *
     * @return array
     */
    private function dataSortAndFilter(): array
    {
        $orders = $this->getOrdersByRange();

        // услуги
        $services = ["service" => $services = $this->arrayUniqueKey($orders->pluck("service")->toArray(), "id"), "count" => count($services)];

        $orders_list = $orders->toArray();
        $list = [];

        foreach ($orders as $key => $order) { // перебор заказов
            if(array_key_exists($key, $orders_list)){ // если найден такой ключ и не был ранее удален, то проходим дальше
                if(array_search($order->timeTable->subjectOfProfessor->professor->id, array_column($list, "professor_id")) !== false){ // если найден ключ уже в готовом списке, то добавляем элементы в существующий элемент
                    // добавляем данные
                    array_push($list[array_search($order->timeTable->subjectOfProfessor->professor->id, array_column($list, "professor_id"))]["data"], [
                        "service" => $order->service,
                        "date" => $order->create_date,
                        "group" => $order->student->group
                    ]);

                    unset($orders_list[$key]);
                }
                else{ // если не найден, то добавляем новое значение препода
                    array_push($list, [
                        "professor_id" => $order->timeTable->subjectOfProfessor->professor->id,
                        "fio" => $order->timeTable->subjectOfProfessor->professor->user->passport->secondname." ".$order->timeTable->subjectOfProfessor->professor->user->passport->firstname." ".$order->timeTable->subjectOfProfessor->professor->user->passport->thirdname,
                        "department" => $order->timeTable->subjectOfProfessor->professor->department,
                        "personal_number" => $order->timeTable->subjectOfProfessor->professor->personal_number,
                        "position" => $order->timeTable->subjectOfProfessor->professor->position,
                        "data" => [[
                            "service" => $order->service,
                            "date" => $order->create_date,
                            "group" => $order->student->group
                        ]]
                    ]);
                }
            }
        }

        return ["professors" => $list, "services" => $services];
    }

    /**
     * Создание ведомости одного преподавателя
     */
    public function createStatements(){
//        $document = $this->openTemplate();
//
//        if(!$document["status"]){
//            return $document["info"];
//        }

        $list = $this->dataSortAndFilter();

        $services = $list["services"];
        $professors = $list["professors"];

        //$docs_path = [];

        $c = 1;
        foreach ($professors as $professor){
            $document = $this->openTemplate()["doc"];
            $table = $this->createSingleStatement($professor, $services);
            $document->setComplexBlock("table", $table);
            $document->saveAs(public_path("documents/statements/statement_num_{$c}.docx"));
            $c++;
        }

        return true;
    }

    /**
     *
     * Создание одного экземпляра документа
     *
     * @param $professor
     * @param $services
     */
    private function createSingleStatement($professor, $services){
        $dataToInsert = $this->sortTimeService($professor);
        $converter = new Converter();
        $flags = [];

        $styleTable = array('borderSize' => 6, 'borderColor' => '999999', "alignment" => "center");
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => $services["count"] + 1, 'valign' => 'center');
        $cellColSpan2 = array('gridSpan' => 2, 'valign' => 'center');

        $table = new Table($styleTable);

        $styleCell12 = [
            'name' => 'Times New Roman',
            'size' => 12,
        ];
        $styleCell10 = [
            'name' => 'Times New Roman',
            'size' => 10,
        ];
        $styleCell9 = [
            'name' => 'Times New Roman',
            'size' => 9,
        ];

        $style = [
            'lineHeight' => 1.0,
            'spaceBefore' => Converter::cmToTwip(0.2),
            'spaceAfter' => Converter::cmToTwip(0.2),
            'align' => 'center',
            'valign' => 'end'
        ];

        $style2 = [
            'lineHeight' => 1.0,
            'spaceBefore' => Converter::cmToTwip(0),
            'spaceAfter' => Converter::cmToTwip(0),
            'align' => 'center',
            'valign' => 'center'
        ];

        $style3 = [
            'lineHeight' => 1.0,
            'spaceBefore' => Converter::cmToTwip(0),
            'spaceAfter' => Converter::cmToTwip(0),
            'align' => 'center',
            'valign' => 'center'
        ];

        // создаем шапку таблички
        $table->addRow();
        $table->addCell($converter::cmToTwip(1.32), ['textDirection'=> Cell::TEXT_DIR_BTLR, 'vMerge' => 'restart', 'valign' => 'center'])->addText("Число месяца", $styleCell12, $style);
        $table->addCell($converter::cmToTwip(1.32), ['textDirection'=> Cell::TEXT_DIR_BTLR, 'vMerge' => 'restart', 'valign' => 'center'])->addText("№ группы", $styleCell12, $style);
        $table->addCell($converter::cmToTwip(14.49), $cellColSpan)->addText("Фактически выполнено часов по видам занятий", $styleCell9, $style2);
        $table->addCell($converter::cmToTwip(1.32), ['textDirection'=> Cell::TEXT_DIR_BTLR, 'vMerge' => 'restart', 'valign' => 'center'])->addText("Примечание", $styleCell12, $style);

        // создание и вывод услуг
        $table->addRow($converter::cmToTwip(1.8));
        $table->addCell(null, $cellRowContinue);
        $table->addCell(null, $cellRowContinue);

        $pos = 0;

        foreach ($services["service"] as $service){
            $pos++;
            $table->addCell($converter::cmToTwip(1.32), ['textDirection'=> Cell::TEXT_DIR_BTLR])->addText($service["title"], $styleCell10, $style);
            array_push($flags, ["service_id" => $service["id"], "pos" => $pos]);
        }

        $table->addCell($converter::cmToTwip(1.32), ['textDirection'=> Cell::TEXT_DIR_BTLR])->addText("Всего", $styleCell10, $style);
        $table->addCell(null, $cellRowContinue);

        // вывод данных
        for($month = 1; $month <= 31; $month++){
            $table->addRow();
            $table->addCell($converter::cmToTwip(1.32))->addText("$month", $styleCell10, $style3);

            foreach ($flags as $flag){
                $flag_found = true;

                foreach ($dataToInsert as $datum){
                    if($flag["service_id"] == $datum["service"]["id"] and $month == $datum["date"]){
                        $table->addCell($converter::cmToTwip(1.32))->addText("{$datum["count"]}", $styleCell10, $style3);
                        $flag_found = false;
                        break;
                    }
                }

                if($flag_found){
                    $table->addCell($converter::cmToTwip(1.32))->addText("", $styleCell10, $style3);
                }
            }
            $table->addCell($converter::cmToTwip(1.32), ['textDirection'=> Cell::TEXT_DIR_BTLR])->addText("Всего", $styleCell10, $style);
        }

        $table->addRow();
        $table->addCell($converter::cmToTwip(2.64), $cellColSpan2)->addText("Итого", $styleCell10, $style3);

        for ($i = 1; $i <= $services["count"] + 2; $i++){
            $table->addCell($converter::cmToTwip(1.32))->addText("", $styleCell10, $style3);
        }

        return $table;
    }

    /**
     *
     * Получение сортированного списка данных 1 преподавателя
     *
     * @param $list
     * @return array
     */
    private function sortTimeService($list): array
    {
        $array = $list["data"];

        // подсчитываем дубликаты по услуге, дню месяца и группе
        for($i = 0; $i <= count($array); $i++){
            for($j = 0; $j <= count($array) - 1; $j++){
                if($array[$i]["service"]["id"] == $array[$j]["service"]["id"] and
                    date("d",strtotime($array[$i]["date"])) == date("d",strtotime($array[$j]["date"])) and
                    $array[$i]["group"]["id"] == $array[$j]["group"]["id"])
                {
                   if(array_key_exists("count", $array[$i]) === false){
                       $array[$i] + ["count" => 1];
                   }
                   else{
                       $array[$i]["count"] += 1;
                   }
                }
            }
        }

        // конвертируем дату в день месяца
        foreach ($array as &$value){
            $value["date"] =  (int)date("d",strtotime($value["date"]));
        }

        // убираем дубликаты
        $filtered = [];
        foreach ($array as $item) {
            $filtered[$item['service']["id"].$item["date"].$item['group']["id"]] = $item;
        }

        $array = array_values($filtered);

        // соединяем заказы в один день месяца
        for($i = 0; $i <= count($array); $i++){
            for($j = 1; $j <= count($array); $j++){
                if($array[$i]["service"]["id"] == $array[$j]["service"]["id"] and $array[$i]["date"] == $array[$j]["date"] and $array[$i]["group"]["id"] != $array[$j]["group"]["id"])
                {
                    $array[$i]["count"] += (int)$array[$j]["count"];
                    $array[$i]["group"]["group_code"] .= ", ".$array[$j]["group"]["group_code"];
                    unset($array[$j]);
                }
            }
        }

        return $array;
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
     * Открытие документа
     *
     * @return array|TemplateProcessor
     */
    private function openTemplate() {
        $path = public_path()."/example/VEDOMOST_example.docx";

        try {
            $document = new TemplateProcessor($path);
        } catch (CopyFileException | CreateTemporaryFileException $e) {
            Log::error("FILE VEDOMOST_example.docx OPEN ERROR: ".$e->getMessage());
            return ["status" => false, "info" => "error: ".$e->getMessage()];
        }

        return ["status" => true, "doc" => $document];
    }
}
