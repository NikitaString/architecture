<?php

namespace Modules\Articles\Controllers;

use Modules\_base\Controller as BaseController;
use Modules\Articles\Models\Index as ModelsIndex;
use System\Exceptions\ExcValidation;
use System\Template;

class Index extends BaseController{
	protected ModelsIndex $model;

	// экземпляр класса Model
	public function __construct(){
		$this->model = ModelsIndex::getInstance();
	}

	public function index(){
		$articles = $this->model->all();

		$this->title = 'Home page';
		$this->content = Template::render(__DIR__ . '/../Views/v_all.php', [
			'articles' => $articles
		]);
	}

	public function item(){
		$this->title = 'Article page';
		$id = (int)$this->env[1];
		$article = $this->model->get($id);

		$this->content = Template::render(__DIR__ . '/../Views/v_item.php', [
			'article' => $article
		]);
	}

	public function add() {
		$this->title = 'Article add';
		$this->content = '111';

		try{
			$this->model->add(['title' => '', 'content' => '13']);
		}
		catch(ExcValidation $e) {
			$this->content = 'cant add article';
		}
	}

	public function remove() {
		var_dump($this->model->remove(1));
	}
}