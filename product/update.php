<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
;
// получаем соединение с базой данных
//$database = new Database();
//$db = $database->getConnection();
$data = (object) $_POST;
$product = Product::find(['id' => intval($data->id)]);
if(!$product) {
    // код ответа - 503 Сервис не доступен
    http_response_code(503);
    echo json_encode(['message' => 'Нет товара с данным id']);
    return false;
}
// подготовка объекта
//$product = new Product($db);

// получаем id товара для редактирования





// установим id свойства товара для редактирования
//$product->id = intval($data->id);

// установим значения свойств товара
if (isset($data->name))
    $product->name = $data->name;
if (isset($data->announce))
    $product->announce = $data->announce;
if (isset($data->description))
    $product->description = $data->description;
if (isset($data->is_enabled))
    $product->is_enabled = $data->is_enabled;

// обновление товара
if ($product->update()) {

    // установим код ответа - 200 ok
    http_response_code(200);

    // сообщим пользователю
    echo json_encode(["message" => "Товар был обновлён."], JSON_UNESCAPED_UNICODE);
} // если не удается обновить товар, сообщим пользователю
else {

    // код ответа - 503 Сервис не доступен
    http_response_code(503);

    // сообщение пользователю
    echo json_encode(["message" => "Невозможно обновить товар."], JSON_UNESCAPED_UNICODE);
}