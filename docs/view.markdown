#Nadir Framework

Yet another MVC PHP microframework.

##Представление

Представление содержит HTML-разметку страницы и минимум логики, необходимой только
лишь для оперирования полученными из контроллера переменными. Представление, в 
общем случае, является композитным и состоит из Макета (экземпляр класса 
`\core\Layout`) и Представления в узком смысле (объект класса `\core\View`). 
Макет - это специальное представление для обрамления других представлений, обычно 
он содержит части пользовательского интерфейса, общие для нескольких представлений.

Передача пользовательских переменных из контроллера в представление осуществляется
присваиванием нужных значений этим переменным как свойствам связанного с контроллером
объекта `$this->view` (аналогично для макета - `$this->layout`). Например:
````php
\\...
public function actionFoo() {
	\\...
	$this->setView('test', 'foo');
	$this->setLayout('main');
	$this->layout->isUserOnline	 = TRUE;
	$this->view->foo			 = 'bar';
	$this->view->bar			 = array(42, 'baz');
	\\...
}
\\...
````
В файле с разметкой этого Представления `..\test\foo.php` переменные доступны для
чтения вызовом `$this->foo` и `$this->bar`. Например:
````php
<!-- ... -->
<div>
	<h1><?= $this->foo; ?></h1>
	<?php if (is_array($this->bar) && !empty($this->bar)): ?>
		<ul>
		<?php foreach ($this->bar as $elem): ?>
			<li><?= $elem; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<!-- ... -->
````
Аналогично, в файле `..\main.php`, содержащем разметку Макета, переменная доступна 
для чтения вызовом `$this->isUserOnline`.
````php
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<title>Nadir Framework</title>
	</head>
	<body>
		<h1>User <?= $this->isUserOnline ? 'online':  'offline'; ?></h1>
		<?php $this->view->render(); ?>
	</body>
</html>
````
Обратите внимание, что место размещения разметки View в разметке Layout
определяется местом вызова метода `$this->view->render()`.