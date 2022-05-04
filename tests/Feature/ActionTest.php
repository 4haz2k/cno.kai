<?php

namespace Tests\Feature;

use App\Services\DocumentService;
use App\Services\SecurityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ActionTest extends TestCase
{
    /**
     * Проверка работоспособности модуля авторизации
     * @test
     */
    public function test_user_login()
    {
        $parameters = [
            "email" => "vitalij95@fokin.ru",
            "password" => "password"
        ];

        $response = $this->call("POST", "/api/auth/login", $parameters);

        $response->assertStatus(200);
    }

    /**
     * Проверка работоспособности модуля создания документов
     * @test
     */
    public function test_document_create(){
        $order_id = 2;
        $expected = true;

        $document = new DocumentService();
        $result = $document->getDocument($order_id)["status"];
        $this->assertEquals($expected, $result);
    }

    /**
     * Проверка работоспособниости модуля безопасности
     * @test
     */
    public function test_order_hash_check(){
        $expected = true;
        $value = '$2y$10$LMIXsUKvIDhllTcm4obEfus1k/hz2ANqlj.pSW3RUMdP60K5XJFuS';
        $order_id = 1;

        $security = new SecurityService();
        $result = $security->checkHash($order_id, $value);

        $this->assertEquals($expected, $result);
    }

    /**
     * Проверка модуля конвертации числа формата int в строку русского вида
     * @test
     */
    public function test_num_convert_to_string(){
        $num = 15425.16;
        $expected = "15425 (пятнадцать тысяч четыреста двадцать пять) рублей (я) 16 копеек.";

        $document = new DocumentService();
        $result = $document->numToString($num);

        $this->assertEquals($expected, $result);
    }

    /**
     * Получение информации о преподавателе
     */
    public function test_get_professor_info(){
        $parameters = [
            "professor_id" => 34
        ];

        $response = $this->call("POST", "/api/professors/getSingle", $parameters);
        $response->assertStatus(200);
    }

    /**
     *
     * Тестирование регистрации препода / админа
     *
     * @test
     */
    public function test_registration(){
        $data = [
            "email" => "fsafas@mail.ru",
            "telephone" => "88005553535",
            "password" => "Test810!",
            "passport" => [
                "name" => "Евгений",
                "surname" => "Иванов",
                "patronymic" => "Викторович",
                "date_of_birth" => "07.05.2005",
                "sex" => "M",
                "serial" => "1234",
                "number" => "123456",
                "issued_by" => "Отделом УФМС",
                "date_of_issue" => "10.06.2016",
                "department_code" => "004-004",
                "INN" => 123456789402,
                "SNILS" => 12345671901,
                "place_of_residence" => [
                    "country" => "Российская Федерация",
                    "region" => "республика Татарстан",
                    "locality" => "город Москва",
                    "district" => "Вахитовский район",
                    "street" => "Пушкина",
                    "house" => "1",
                    "frame" => "2",
                    "apartment" => "50",
                ],
            ],
            "the_same_address" => true,
//            "place_of_residence" => [
//                "country" => "",
//                "region" => "",
//                "locality" => "",
//                "district" => "",
//                "street" => "",
//                "house" => "",
//                "frame" => "",
//                "apartment" => "",
//            ],
            "role" => "STUDENT",
//            "position" => "",
//            "faculty" => "",
//            "personal_number" => 123456789012,
//            "INN" => 123456789012,
//            "SNILS" => 12345678901,
//            "exp" => "",
//            "price" => 1
            "group_id" => 1,
            "receipt_date" => "01.09.2018",
        ];

        $this->json('post', 'api/auth/registration', $data)
            ->assertStatus(200);
    }

    /**
     *
     * Тестирование редактирования пользователя
     *
     * @test
     */
    public function test_profile_edit(){
        $data = [
            "user_id" => 69,
            "address_id" => 70,
            "email" => "testing@mail.ru",
            "telephone" => "88005553535",
            "passport" => [
                "name" => "Евгений",
                "surname" => "Иванов",
                "patronymic" => "Викторович",
                "date_of_birth" => "07.05.2005",
                "sex" => "M",
                "serial" => "1234",
                "number" => "123456",
                "issued_by" => "Отделом УФМС",
                "date_of_issue" => "10.06.2016",
                "department_code" => "004-004",
                "INN" => 123456789402,
                "SNILS" => 12345671901,
                "place_of_residence" => [
                    "country" => "Российская Федерация",
                    "region" => "республика Татарстан",
                    "locality" => "город Москва",
                    "district" => "Вахитовский район",
                    "street" => "Пушкина",
                    "house" => "1",
                    "frame" => "2",
                    "apartment" => "50",
                ],
            ],
            "the_same_address" => true,
//            "place_of_residence" => [
//                "country" => "",
//                "region" => "",
//                "locality" => "",
//                "district" => "",
//                "street" => "",
//                "house" => "",
//                "frame" => "",
//                "apartment" => "",
//            ],
            "role" => "STUDENT",
//            "position" => "",
//            "faculty" => "",
//            "personal_number" => 123456789012,
//            "INN" => 123456789012,
//            "SNILS" => 12345678901,
//            "exp" => "",
//            "price" => 1
            "group_id" => 1,
            "receipt_date" => "01.09.2018",
        ];

        $this->json('post', 'api/auth/profileEdit', $data)
            ->assertStatus(200);
    }

}
