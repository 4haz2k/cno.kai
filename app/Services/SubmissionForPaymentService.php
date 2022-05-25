<?php
namespace App\Services;


use App\Models\Order;
use App\Models\Professor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\TemplateProcessor;

class SubmissionForPaymentService
{
    private $firstRange;
    private $lastRange;

    public function __construct()
    {
        $date = Carbon::now("Europe/Moscow");
        $this->firstRange =  $date->subMonth()->firstOfMonth()->toDateTimeString();
        $this->lastRange = $date->lastOfMonth()->toDateTimeString();
    }

    /**
     *
     * Получение ссылки документа
     *
     * @return string|null
     */
    public function getSubmission(): ?string
    {
        $professors = $this->sortProfessors();

        if($professors == null)
            return null;

        return $this->createDocument($professors);
    }

    /**
     *
     * Получение преподавателей для заполнения документа
     *
     * @return array
     */
    private function getProfessors(): array
    {
        return Professor::with([
            "subjectOfProfessor.timeTable.order",
            "user" => function ($q) { $q->select(["id"]); },
            "user.passport" => function ($q) { $q->select(["id", "firstname", "secondname", "thirdname"]); }
            ])
            ->whereHas("subjectOfProfessor.timeTable.order", function ($q) { $q->where("orders.id", "!=", null); })
            ->get()
            ->makeHidden(["date_of_commencement_of_teaching_activity", "description", "price"])
            ->toArray();
    }

    /**
     *
     * Удаление преподаватлей, у которых нет заказов
     *
     * @return array|null
     */
    private function sortProfessors(): ?array
    {
        $professors = $this->getProfessors();

        if(empty($professors))
            return null;

        foreach ($professors as $professor_key => &$professor){
            foreach ($professor["subject_of_professor"] as $subject_of_professor_key => &$subject_of_professor){
                foreach ($subject_of_professor["time_table"] as $timetable_key => $timetable) {
                    if($timetable["order"] == null){
                        unset($professors[$professor_key]["subject_of_professor"][$subject_of_professor_key]["time_table"][$timetable_key]);
                    }
                }
            }
        }

        unset($professor, $subject_of_professor);

        foreach ($professors as $professor_key => $professor) {
            foreach($professor["subject_of_professor"] as $subject_of_professor_key => $subject_of_professor){
                if(empty($subject_of_professor["time_table"])){
                    unset($professors[$professor_key]["subject_of_professor"][$subject_of_professor_key]);
                }
            }
        }

        unset($professor, $subject_of_professor);

        foreach ($professors as &$professor) {
            $order = [
                "number_of_lessons" => 0,
                "price" => 0
            ];

            foreach ($professor["subject_of_professor"] as $subject_of_professor){
                foreach ($subject_of_professor["time_table"] as $timetable){
                    $order["number_of_lessons"] += $timetable["order"]["number_of_lessons"];
                    $order["price"] += $timetable["order"]["price"];
                }
            }

            $professor["order"] = $order;

            unset($professor["subject_of_professor"]);
        }

        return $professors;
    }

    /**
     *
     * Создание документа
     *
     * @param $professors
     * @return string|null
     */
    private function createDocument($professors): ?string
    {
        $document = $this->openDocument();

        if(!$document["status"]){
            return null;
        }

        $document = $this->placeHolders($professors, $document["doc"]);

        $document->saveAs(public_path("documents/submission.docx"));

        return public_path("documents/submission.docx");
    }

    /**
     *
     * Открытие документа
     *
     * @return array
     */
    private function openDocument(): array
    {
        $path = public_path()."/example/Predstavlenia_na_vyplaty.docx";

        try {
            $document = new TemplateProcessor($path);
        } catch (CopyFileException | CreateTemporaryFileException $e) {
            Log::error("FILE Predstavlenia_na_vyplaty.docx OPEN ERROR: ".$e->getMessage());
            return ["status" => false, "info" => "error: ".$e->getMessage()];
        }

        return ["status" => true, "doc" => $document];
    }

    /**
     *
     * Замена заглушек на данные
     *
     * @param $professors
     * @param $document
     * @return mixed
     */
    private function placeHolders($professors, $document){
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale("ru");
        $date = Carbon::now("Europe/Moscow");

        $word = new TextRun();

        $word->addText($date->subMonth()->isoFormat('MMMM Y'), [
            "bold" => true,
            "size" => 14,
            "name" => "Times New Roman"
        ]);

        $document->setComplexValue("date", $word);

        $document->setComplexBlock("table", $this->createTable($professors));

        return $document;
    }

    /**
     *
     * Создание таблицы
     *
     * @param $professors
     * @return Table
     */
    private function createTable($professors): Table
    {
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999', "alignment" => "center");

        $table = new Table($styleTable);

        $styleCell12 = [
            'name' => 'Times New Roman',
            'size' => 12,
        ];

        $styleCell14 = [
            'name' => 'Times New Roman',
            'size' => 14,
            "bold" => true
        ];

        $style1 = [
            'lineHeight' => 1.0,
            'spaceBefore' => Converter::cmToTwip(0),
            'spaceAfter' => Converter::cmToTwip(0),
            'align' => 'center',
            'valign' => 'center'
        ];

        $style2 = [
            'lineHeight' => 1.0,
            'spaceBefore' => Converter::cmToTwip(0),
            'spaceAfter' => Converter::cmToTwip(0),
            'valign' => 'center'
        ];

        $cellCenter = [
            'valign' => 'center'
        ];

        $table->addRow(Converter::cmToTwip(2.67));
        $table->addCell(Converter::cmToTwip(1.02), $cellCenter)->addText("№", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(3.54), $cellCenter)->addText("Ф.И.О. сотрудника", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(1.45), $cellCenter)->addText("Таб. №", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(3.19), $cellCenter)->addText("Должность", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(2.37), $cellCenter)->addText("Кафедра", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(1.72), $cellCenter)->addText("Кол-во часов", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(2.5), $cellCenter)->addText("Период", $styleCell14, $style1);
        $table->addCell(Converter::cmToTwip(1.9), $cellCenter)->addText("Сумма, руб.", $styleCell14, $style2);

        $count = 1;
        $time = $this->getTimeRange();
        foreach ($professors as $professor){
            $fio = $professor["user"]["passport"]["secondname"]." ".$professor["user"]["passport"]["firstname"]." ".$professor["user"]["passport"]["thirdname"];
            $table->addRow(Converter::cmToTwip(2.15));
            $table->addCell(Converter::cmToTwip(1.02), $cellCenter)->addText("{$count}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(3.54), $cellCenter)->addText("{$fio}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(1.45), $cellCenter)->addText("{$professor["personal_number"]}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(3.19), $cellCenter)->addText("{$professor["position"]}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(2.37), $cellCenter)->addText("{$professor["department"]}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(1.72), $cellCenter)->addText("{$professor["order"]["number_of_lessons"]}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(2.5), $cellCenter)->addText("{$time}", $styleCell12, $style1);
            $table->addCell(Converter::cmToTwip(1.9), $cellCenter)->addText("{$professor["order"]["price"]}", $styleCell12, $style1);
            $count++;
        }

        return $table;
    }

    /**
     *
     * Получение отрезка времени документа
     *
     * @return string
     */
    private function getTimeRange(): string
    {
        $date = Carbon::now("Europe/Moscow");
        return $date->subMonth()->firstOfMonth()->format("d.m.Y")." - ".$date->lastOfMonth()->format("d.m.Y");
    }

}
