<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
// получаем отправленные данные
$data = json_decode(file_get_contents("php://input"));

$category_id = $data->category_id ?? false;

$stmt = $product->read($category_id);

$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив товаров
    $products_arr = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $product_item = [
            "id" => $id,
            "description" => html_entity_decode($description),
            "name" => $name,
            "announce" => $announce,
        ];

        $products_arr[] = $product_item;
    }

    // устанавливаем код ответа - 200 OK
    http_response_code(200);

    // выводим данные о товаре в формате JSON
    echo json_encode($products_arr);
} else {

    http_response_code(404);

    // сообщаем пользователю, что товары не найдены
    echo json_encode(["message" => "Товары не найдены."], JSON_UNESCAPED_UNICODE);
}
