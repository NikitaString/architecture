<?php

namespace System\Database;

// Класс прослойки БД и SelectBuilder для SQL-запросов, паттерн - посредник
class QuerySelect{
	protected Connection $db; // подключение к БД
	protected SelectBuilder $builder; // экземпляр класса для построения SQL-запросов
	protected array $binds = []; // бинды для подготовленных запросов

	public function __construct(Connection $db, SelectBuilder $builder){
		$this->db = $db; // коннект к базе данных
		$this->builder = $builder; // класс для построения sql-запросов
	}

	// для WHERE и параметров binds
	public function where(string $where, array $binds = []) {
		$this->builder->addWhere($where);
		$this->binds = $binds + $this->binds; // тот же array_merge()...
		return $this;
	}

	// для limit
	public function limit(int $shift, ?int $cnt = null) {
		$this->builder->limit($shift . (($cnt !== null) ? ",$cnt" : ''));
		return $this;
	}

	// для получения все полез из БД
	public function get() : array {
		return $this->db->select($this->builder, $this->binds);
	}

	// работает с объектом, как-будто объект - это функция
	// public function __invoke() {
	// 	return $this->db->select($this->builder, $this->binds);
	// }
}