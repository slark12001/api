<?php


class Product
{

    // подключение к базе данных и таблице 'shop_product'
    private $conn;
    private string $table_name = "shop_product";
    private string $relations_table = 'shop_product_category';

    // свойства объекта
    public int $id;
    public bool $is_enabled;
    public string|null $description;
    public string|null $name;
    public string|null $announce;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($category_id = false)
    {
        $category_id = intval($category_id);
        // выбираем все записи
        $query = "SELECT
               id, name, announce, description
            FROM
                " . $this->table_name . " 
                WHERE is_enabled = 1";

        if($category_id) {
            $query .= " and id IN (select product_id from " . $this->relations_table . " where category_id = $category_id)";
        }
        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // выполняем запрос
        $stmt->execute();

        return $stmt;
    }

    public function create(): bool
    {

        // запрос для вставки (создания) записей
        $query = "INSERT INTO
                " . $this->table_name . "
            SET
                name=:name, is_enabled = :is_enabled, description = :description, announce = :announce";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->announce = htmlspecialchars(strip_tags($this->announce));

        // привязка значений
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":is_enabled", $this->is_enabled);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":announce", $this->announce);

        // выполняем запрос
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete(): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id ";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $this->id = htmlspecialchars(strip_tags($this->id));

        // привязываем id записи для удаления
        $stmt->bindParam(':id', $this->id);

        // выполняем запрос
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function update(): bool
    {
        // запрос для обновления записи (товара)
        $query = "UPDATE
                " . $this->table_name . "
            SET
                name = :name,
                announce = :announce,
                description = :description,
                is_enabled = :is_enabled
            WHERE
                id = :id";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->announce = htmlspecialchars(strip_tags($this->announce));

        // привязываем значения
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':announce', $this->announce);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':is_enabled', $this->is_enabled);
        $stmt->bindParam(':id', $this->id);

        // выполняем запрос
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}