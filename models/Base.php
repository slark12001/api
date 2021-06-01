<?php


abstract class Base
{
    protected $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function clear (&$field): string
    {
        return htmlspecialchars(strip_tags($field));
    }

    abstract static protected function find(array $condition = []);
    abstract protected function read();
    abstract protected function create();
    abstract protected function delete();
    abstract protected function update();
}