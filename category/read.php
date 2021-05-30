<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php';

$database = new Database();
$db = $database->getConnection();

$category = new Category($db);

$stmt = $category->read();
$num = $stmt->rowCount();

// проверка, найдено ли больше 0 записей
if ($num > 0) {

    // массив товаров
    $categories = array();

    // получаем содержимое нашей таблицы
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // извлекаем строку
        extract($row);

        $category = [
            "id" => $id,
            "name" => $name,
            "parent" => $parent,
            'count_products' => $count_products
        ];

        $categories[] = $category;
    }

    // устанавливаем код ответа - 200 OK
    http_response_code(200);

    // выводим данные о товаре в формате JSON
    echo json_encode($categories);
} else {

    http_response_code(404);

    // сообщаем пользователю, что товары не найдены
    echo json_encode(["message" => "Товары не найдены."], JSON_UNESCAPED_UNICODE);
}
