<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// получаем отправленные данные
$data = (object) $_POST;


// убеждаемся, что данные не пусты
if (
    !empty($data->name)
) {

    // устанавливаем значения свойств товара
    $product->name = $data->name ?? null;
    $product->is_enabled = $data->is_enabled ?? 0;
    $product->description = $data->description ?? null;
    $product->announce = $data->announce ?? null;

    // создание товара
    if ($product->create()) {

        // установим код ответа - 201 создано
        http_response_code(201);

        // сообщим пользователю
        echo json_encode(["message" => "Товар был создан."], JSON_UNESCAPED_UNICODE);

        if($category_id = intval($data->category_id))
            $product->addInCategory($category_id);
    } // если не удается создать товар, сообщим пользователю
    else {

        // установим код ответа - 503 сервис недоступен
        http_response_code(503);

        // сообщим пользователю
        echo json_encode(["message" => "Невозможно создать товар."], JSON_UNESCAPED_UNICODE);
    }
} // сообщим пользователю что данные неполные
else {

    // установим код ответа - 400 неверный запрос
    http_response_code(400);

    // сообщим пользователю
    echo json_encode(["message" => "Невозможно создать товар. Данные неполные."], JSON_UNESCAPED_UNICODE);
}