<?php

namespace System;

use System\Database\Connection;
use System\Database\QuerySelect;
use System\Database\SelectBuilder;
use System\Exceptions\ExcValidation;

abstract class Model{
    protected static $instance; // экземпляр класса для singleton'a
    protected Connection $db; // переменая подключенния к базе данных
    protected string $table; // название таблицы
    protected string $pk; // первичный ключ (название столбца)
    protected array $validationRules; // правила валидации
    protected Validator $validator; // экземпляр класса валидатора

    // реализация паттерна(анти-паттерна) singleton'a
    public static function getInstance() : static {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct() {
        $this->db = Connection::getInstance(); // подключение к БД
        $this->validator = new Validator($this->validationRules); // создание класса валидатора
    }

    // получени всех полей из БД
    public function all() : array {
        return $this->selector()->get();
    }

    public function get(int $id) : ?array{
        $res = $this->selector()->where("{$this->pk} = :pk", ['pk' => $id])->get();
        return $res[0] ?? null;
    }

    // создает и возвращает экземпляр класса QuerySelect
    public function selector() : QuerySelect {
        $builder = new SelectBuilder($this->table);
        return new QuerySelect($this->db, $builder);
    }

    // добавление в базу данных
    public function add(array $fields) : int {
        $isValid = $this->validator->run($fields);

        if (!$isValid) {
            throw new ExcValidation();
        }

        $names = [];
        $masks = [];
        
        foreach($fields as $field => $val) {
            $names[] = $field;
            $masks[] = ":$field";
        }

        $namesStr = implode(', ', $names);
        $masksStr = implode(', ', $masks);

        $query = "INSERT INTO {$this->table} {$namesStr} VALUES {$masksStr}";
        $this->db->query($query, $fields); // $fields - это buinds для SQL-запроса
        return $this->db->lastInsertId();
    }

    // Удаление из БД 
    public function remove(int $id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->pk} = :pk";
        $query = $this->db->query($query, ['pk' => $id]);
        return $query->rowCount(); // rowCount — Возвращает количество строк, затронутых последним SQL-запросом
    }

    /* 
    UPDATE [LOW_PRIORITY] [IGNORE] table_reference
    SET assignment_list
    [WHERE where_condition]
    [ORDER BY ...]
    [LIMIT row_count]

    UPDATE table_name
    SET column1 = value1, column2 = value2, ...
    WHERE condition;
    */

    /*
    public function edit(array $fields) {
        $isValid = $this->validator->run($fields);

        if (!$isValid) {
            throw new ExcValidation();
        }
        $names = [];
        foreach($fields as $field => $value) {
            $names[] = "$field=:$field";
        }

        $namesStr = implode(', ', $names);

        $query = "UPDATE {$this->table} SET {$namesStr}";
        $this->db->query($query, $fields); // $fields - это buinds для SQL-запроса
        return $this->db->lastInsertId();
    }
    */

    // На вход постпует условие
    // public function remove(array $fields) {
    //     $isValid = $this->validator->run($fields);

    //     if (!$isValid) {
    //         throw new ExcValidation();
    //     }

    //     if (!empty($fields)) {
    //         foreach($fields as $field => $value) {
    //             $names[] = "$field=:$field";
    //         }

    //         $namesStr = implode(', ', $names);

    //         $query = "DELETE FROM {$this->table} WHERE $namesStr";
    //         $this->db->query($query, $fields); // $fields - это buinds для SQL-запроса
    //         return;
    //     }
    //     // Иначе удалить всё
    //     $query = "DELETE FROM {$this->table}";
    //     $this->db->query($query, $fields); // $fields - это buinds для SQL-запроса
    //     return;

    // }
}