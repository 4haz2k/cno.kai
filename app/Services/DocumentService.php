<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentService
{
    private $security;

    public function __construct()
    {
        $this->security = new SecurityService();
    }

    public function getDocument($order_id): array
    {
        $order = Order::where("id", $order_id)->with([
            "student.user.passport.placeOfResidence",
            "student.group",
            "service",
            "timeTable.subjectOfProfessor.subject",
            "timeTable.subjectOfProfessor.professor.user.passport"
        ])->first();

        if(!$order){
            return ["status" => false, "info" => "error: order_id not found."];
        }

        if($order->treaty){
            return ["status" => true, "info" => "Success found", "link" => $order->treaty];
        }

        $address = $order->student->user->passport->placeOfResidence;

        $student_address = $address->country.", ".$address->region.", ".$address->locality.", ".$address->street.", д. ".$address->house.", ".$address->apartment;

        $tags = [
            [
                "tag" => "order-date",
                "word" => date("d.m.Y", strtotime($order->create_date)),
                "bold" => false,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "order-date-n",
                "word" => date("d.m.Y", strtotime($order->create_date)),
                "bold" => false,
                "underline" => false,
                "size" => 9
            ],
            [
                "tag" => "student-name",
                "word" => $order->student->user->passport->secondname." ".$order->student->user->passport->firstname." ".$order->student->user->passport->thirdname,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "student-group",
                "word" => $order->student->group->group_code,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "subject-type",
                "word" => $order->service->title,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "discipline",
                "word" => $order->timeTable->subjectOfProfessor->subject->title,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "professor-name",
                "word" => $order->timeTable->subjectOfProfessor->professor->user->passport->secondname." ".$order->timeTable->subjectOfProfessor->professor->user->passport->firstname." ".$order->timeTable->subjectOfProfessor->professor->user->passport->thirdname,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "professor-qualifier",
                "word" => $order->timeTable->subjectOfProfessor->professor->position,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "professor-faculty",
                "word" => $order->timeTable->subjectOfProfessor->professor->department,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "order-price",
                "word" => $this->numToString($order->price),
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "order-time",
                "word" => $order->number_of_lessons * 2,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "seventee-date",
                "word" => date('d.m.Y', strtotime($order->create_date. ' + 17 days')),
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "student-address",
                "word" => $student_address,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "passport-s-n",
                "word" => $order->student->user->passport->series." ".$order->student->user->passport->number,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "passport-issued",
                "word" => $order->student->user->passport->issued,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "passport-date-of-issue",
                "word" => date("d.m.Y", strtotime($order->student->user->passport->date_of_issue)),
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "order_id",
                "word" => $order->id,
                "bold" => true,
                "underline" => 'single',
                "size" => 9
            ],
            [
                "tag" => "order_id_n",
                "word" => $order->id,
                "bold" => false,
                "underline" => false,
                "size" => 9
            ],
        ];

        $document = $this->saveDocument($tags, $order_id);

        if($document["info"] == "Document saved success"){
            $order = Order::where("id", $order_id)->first();
            $order->treaty = $document["link"];
            $order->update();

            return $document;
        }
        else{
            return $document;
        }
    }

    /**
     *
     * Замена плейс-холдеров в документе
     *
     * @param array $data
     * @param $document
     * @return mixed
     */
    private function replaceAllTags(array $data, $document){
        foreach ($data as $datum) {
            $word = new TextRun();

            $word->addText($datum["word"], [
                "bold" => $datum["bold"],
                "underline" => $datum["underline"],
                "size" => $datum["size"]
            ]);

            $i = 1;
            $count = $document->getVariableCount()[$datum["tag"]];

            while ($i <  $count + 1){
                $document->setComplexValue($datum["tag"], $word);
                $i++;
            }
        }

        return $document;
    }

    /**
     *
     * Сохранение документа
     *
     * @param array $tags
     * @param $order_id
     * @return string[]
     */
    private function saveDocument(array $tags, $order_id): array
    {
        $path = public_path()."/example/dogovor.docx";

        try {
            $document = new TemplateProcessor($path);
        } catch (CopyFileException | CreateTemporaryFileException $e) {
            Log::error("FILE STORE ERROR: ".$e->getMessage());
            return ["status" => false, "info" => "error: ".$e->getMessage()];
        }

        $document = $this->replaceAllTags($tags, $document);

        $document->saveAs(public_path("documents/order_num_{$order_id}.docx"));

        return ["status" => true, "info" => "Document saved success", "link" => "order_num_{$order_id}.docx"];
    }

    /**
     *
     * Конвертация числа в строку
     *
     * @param $num
     * @return string
     */
    private function numToString($num): string
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять')
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array(
            array('копейка' , 'копейки',   'копеек',     1),
            array('',    '',     '',     0),
            array('тысяча',   'тысячи',    'тысяч',      1),
            array('миллион',  'миллиона',  'миллионов',  0),
            array('миллиард', 'миллиарда', 'миллиардов', 0),
        );

        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) {
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1;
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; // 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; // 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; // 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            }
        } else {
            $out[] = $nul;
        }
        $out[] = $this->morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub

        return intval($rub)." (".trim(preg_replace('/ {2,}/', ' ', join(' ', $out))).") рублей (я) ". $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]).".";
    }

    /**
     * Склоняем словоформу
     * @param $n
     * @param $f1
     * @param $f2
     * @param $f5
     * @return mixed
     */
    private function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }
}
