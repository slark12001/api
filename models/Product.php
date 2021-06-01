<?php


class Product extends Base
{

    private string $table_name = "shop_product";
    private string $relations_table = 'shop_product_category';

    // свойства объекта
    public  $id;
    public  $is_enabled;
    public  $description;
    public  $name;
    public  $announce;

    public function __construct($db)
    {
        parent::__construct($db);
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

        if ($category_id) {
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
        if (!empty($this->name))
            $this->clear($this->name);
        if (!empty($this->description))
            $this->clear($this->description);
        if (!empty($this->announce))
           $this->clear($this);

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
                {$this->table_name}
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
        if (!empty($this->name))
            $this->clear($this->name);
        if (!empty($this->description))
           $this->clear($this->description);
        if (!empty($this->announce))
            $this->clear($this->announce);


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



    public static function find(array $condition = [])
    {
        $database = new Database();
        $model = new Product($database->getConnection());
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
                $model->name = $name;
                $model->description = $description;
                $model->is_enabled = $is_enabled;
                $model->announce = $announce;
            }

            return $model;
        } else
            return false;

    }

    public function addInCategory (int $category): bool
    {
        $last_id = $this->conn->lastInsertId();
        $query = "INSERT INTO {$this->relations_table} ( category_id, product_id ) VALUES ( $category , $last_id )";

        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            return true;
        }

        return false;

    }

}