<?php


class Category
{
    // подключение к базе данных и таблице 'shop_product'
    private $conn;
    private string $table_name = "shop_category";
    private string $relations_table = 'shop_product_category';

    // свойства объекта
    public int $id;
    public bool $is_enabled;
    public int $parent;
    public string|null $name;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {

        // выбираем все записи
        $query = "SELECT
               id, name, parent,
       (select count(1) from ". $this->relations_table ." where " . $this->relations_table. ".category_id = ". $this->table_name .".id) as count_products
            FROM
                " . $this->table_name . " 
                WHERE is_enabled = 1;";

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
                name=:name, is_enabled = :is_enabled, parent = :parent";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $this->name = htmlspecialchars(strip_tags($this->name));

        // привязка значений
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":is_enabled", $this->is_enabled);
        $stmt->bindParam(":parent", $this->parent);

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
                parent = :parent,
                is_enabled = :is_enabled
            WHERE
                id = :id";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $this->name = htmlspecialchars(strip_tags($this->name));

        // привязываем значения
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':parent', $this->parent);
        $stmt->bindParam(':is_enabled', $this->is_enabled);
        $stmt->bindParam(':id', $this->id);

        // выполняем запрос
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}