<?php

namespace System\Database;
use Exception;

class SelectBuilder{
	public string $table;
	protected array $fields = ['*']; // Названия столбцов
	// Дополнения: изначально null, потом передаем строку запроса
	protected array $addons = [
		'join' => null,
		'where' => null,
		'group_by' => null,
		'having' => null,
		'order_by' => null,
		'limit' => null
	];

	public function __construct(string $table){
		$this->table = $table;
	}

	// Названия столбцов
	public function fields(array $fields) {
		$this->fields = $fields;
		return $this;
	}

	// для WHERE ...
	public function addWhere(string $where) {
		$this->addons['where'] .= ' ' . $where;
		return $this;
	}

	// Для вывода в строку
	public function __toString(){
		$activeCommands = [];

		foreach($this->addons as $command => $setting) {
			if ($setting !== null) {
				$sqlKey = str_replace('_', ' ', strtoupper($command)); // strtoupper — Преобразует строку в верхний регистр
				$activeCommands[] = "$sqlKey $setting";
			}
		}

		$fields = implode(', ', $this->fields); // название столбцов
		$addon = implode(' ', $activeCommands); // дополнения в определенном порядке
		return trim("SELECT $fields FROM {$this->table} $addon"); // возвращает готовый SQL-запрос
	}

	// запускается при вызове недоступных методов
	public function __call($name, $args) {
		// если метода нет в списке $this->addons, то вызывается исключение
		if (!array_key_exists($name, $this->addons)) {
			throw new Exception('sql error unknown');
		}

		$this->addons[$name] = $args[0]; // записывает параметры SQL-запроса
		return $this;
	}
}