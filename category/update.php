<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require_once $_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php';
if ($_SERVER['REQUEST_METHOD'] != 'POST')
    return;
// получаем соединение с базой данных
//$database = new Database();
//$db = $database->getConnection();
$data = (object) $_POST;
// подготовка объекта
$category = Category::find(['id' => intval($data->id)]);
if(!$category) {
    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщение пользователю
    echo json_encode(["message" => "Невозможно обновить категорию."], JSON_UNESCAPED_UNICODE);

    return false;
}

if (isset($data->name))
    $category->name = $data->name;
if (isset($data->description))
    $category->description = $data->description;
if (isset($data->is_enabled))
    $category->is_enabled = $data->is_enabled;

// обновление товара
if ($category->update()) {

    // установим код ответа - 200 ok
    http_response_code(200);

    // сообщим пользователю
    echo json_encode(["message" => "Категория была обновлена."], JSON_UNESCAPED_UNICODE);
}

// если не удается обновить товар, сообщим пользователю
else {

    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщение пользователю
    echo json_encode(["message" => "Невозможно обновить категорию."], JSON_UNESCAPED_UNICODE);
}