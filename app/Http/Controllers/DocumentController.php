<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\Word2007\Element\Text;

class DocumentController extends Controller
{
    public function getDocument()
    {
        $order = Order::find(1)->with([
            "student.user.passport.placeOfResidence",
            "student.group",
            "service",
            "timeTable.subjectOfProfessor.subject",
            "timeTable.subjectOfProfessor.professor.user.passport"
        ])->first();

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
                "word" => $order->price,
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
        ];

        $document = $this->saveDocument($tags);

        if($document === true){
            return response()->json(["info" => "Document saved success"]);
        }
        else{
            return response()->json($document);
        }
    }

    private function encryptData($value): string
    {
        return Crypt::encrypt(Crypt::encrypt($value) . "$" . env("SECRET_DOCUMENT_CODE"));
    }

    private function decryptData($value): string
    {
        return Crypt::decrypt(explode("$", Crypt::decrypt($value))[0]);
    }

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

    private function saveDocument(array $tags)
    {
        $path = public_path()."/example/dogovor.docx";

        try {
            $document = new TemplateProcessor($path);
        } catch (CopyFileException | CreateTemporaryFileException $e) {
            return ["info" => "error: ".$e->getMessage()];
        }

        $document = $this->replaceAllTags($tags, $document);

        $document->saveAs(public_path("documents/test.docx"));

        return true;
    }
}
