<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// подключим файл для соединения с базой и объектом Product
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// получаем соединение с БД
//$database = new Database();
//$db = $database->getConnection();
// получаем id товара
$data = (object) $_POST;
// подготовка объекта
$product = Product::find(['id' => intval($data->id)]);

if(!$product) {
    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщим об этом пользователю
    echo json_encode(["message" => "Не удалось удалить товар."]);

    return;
}


// удаление товара
if ($product->delete()) {

    // код ответа - 200 ok
    http_response_code(200);

    // сообщение пользователю
    echo json_encode(["message" => "Товар был удалён."], JSON_UNESCAPED_UNICODE);
}

// если не удается удалить товар
else {

    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщим об этом пользователю
    echo json_encode(["message" => "Не удалось удалить товар."]);
}