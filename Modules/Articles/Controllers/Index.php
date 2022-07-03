<?php

namespace Modules\Articles\Controllers;

use System\Contracts\IStorage;
use Modules\_base\Controller as BaseController;

use System\FileStorage;
use System\Template;

class Index extends BaseController{
	protected IStorage $storage;

	public function __construct(){
		$this->storage = FileStorage::getInstance('db/articles.txt'); // yes-yes, without DI it is trash
	}

	public function index(){
		$this->title = 'Home page';
		$this->content = 'Articles list';
	}

	public function item(){
		$this->title = 'Article page';
		$id = (int)$this->env[1];
		$article = $this->storage->get($id);

		$this->content = Template::render(__DIR__ . '/../Views/v_item.php', [
			'article' => $article
		]);
	}
}