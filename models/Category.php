<?php


class Category extends Base
{
    // подключение к базе данных и таблице 'shop_product'
    private string $table_name = "shop_category";
    private string $relations_table = 'shop_product_category';

    // свойства объекта
    public  $id;
    public  $is_enabled;
    public  $parent;
    public  $name;

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        parent::__construct($db);
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
                $this->table_name
            SET
                name=:name, is_enabled = :is_enabled, parent = :parent";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $this->clear($this->name);

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

    public static function find(array $condition = [])
    {
        $database = new Database();
        $model = new static($database->getConnection());
        $table = $model->table_name;
        $query = "Select * from $table where ";
        $first_column = false;
        foreach ($condition as $column => $value) {
            if (!$first_column) {
                $query .= $column;
                $first_column = true;
            } else {
                $query .= " AND $column";
            }
            if (is_array($value))
                $query .= " IN (" . implode(", ", $value) . ")";
            else
                $query .= " = $value";
        }

        $stmt = $model->conn->prepare($query);
        $stmt->execute();

        if($stmt->rowCount() > 0)
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $model->id = $id;
                $model->parent = $parent;
                $model->name = $name;
                $model->is_enabled = $is_enabled;
            }

            return $model;
        } else
            return false;
    }
}