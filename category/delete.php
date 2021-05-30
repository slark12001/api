<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// подключим файл для соединения с базой и объектом Product
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php';

// получаем соединение с БД
$database = new Database();
$db = $database->getConnection();

// подготовка объекта
$category = new Category($db);

$data = json_decode(file_get_contents("php://input"));

$category->id = $data->id ?? 0;


if ($category->id > 0 && $category->delete()) {

    // код ответа - 200 ok
    http_response_code(200);

    // сообщение пользователю
    echo json_encode(["message" => "Категория была удалена."], JSON_UNESCAPED_UNICODE);
}

else {

    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщим об этом пользователю
    echo json_encode(["message" => "Не удалось удалить категорию."]);
}