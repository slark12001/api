<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require_once $_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// подготовка объекта
$product = new Product($db);

// получаем id товара для редактирования
$data = json_decode(file_get_contents("php://input"));

// установим id свойства товара для редактирования
$product->id = intval($data->id);

// установим значения свойств товара
$product->name = $data->name ?? '';
$product->announce = $data->announce ?? '';
$product->description = $data->description ?? '';
$product->is_enabled = (isset($data->is_enabled) && $data->is_enabled === true) ? 1 : 0;

// обновление товара
if ($product->id > 0 && $product->update()) {

    // установим код ответа - 200 ok
    http_response_code(200);

    // сообщим пользователю
    echo json_encode(["message" => "Товар был обновлён."], JSON_UNESCAPED_UNICODE);
}

// если не удается обновить товар, сообщим пользователю
else {

    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщение пользователю
    echo json_encode(["message" => "Невозможно обновить товар."], JSON_UNESCAPED_UNICODE);
}