<?php

namespace System;

// Возвращает шаблон с помощью буферизации
class Template{
	public static function render($pathToTemplate, $vars = []) : string {
		extract($vars); // функция extract() вернет значения переменных для шаблона
		ob_start();
		include($pathToTemplate);
		return ob_get_clean();
	}
}