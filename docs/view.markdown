# Nadir Framework

Yet another MVC PHP microframework.

## Представление

### Композиты представления

Представление содержит HTML-разметку страницы и минимум логики, необходимой только
лишь для оперирования полученными из контроллера переменными. Представление, в 
общем случае, является композитным и состоит из Макета (экземпляр класса 
`\core\Layout`) и Представления в узком смысле (объект класса `\core\View`). 
Макет - это специальное представление для обрамления других представлений, обычно 
он содержит части пользовательского интерфейса, общие для нескольких представлений.
Каждое из композитов представления может, в свою очередь, содержать сниппеты (объекты
класса `\core\Snippet`) - фрагменты часто встречающихся в проекте элементов интерфейса - 
навигационных панелей, различных информационных блоков и пр.

Передача пользовательских переменных из контроллера в представление осуществляется
присваиванием нужных значений этим переменным как свойствам связанного с контроллером
объекта View (аналогично для макета - Layout). Например:
````php
\\...
public function actionFoo() {
	\\...
	$this->setView('test', 'foo');
	$this->setLayout('main');
	$this->getLayout()->isUserOnline = FALSE;
	$this->getView()->foo			 = 'bar';
	$this->getView()->bar			 = array(42, 'baz');
	\\...
}
\\...
````
Для массового присваивания пользовательских переменных предусмотрен более лаконичный 
метод `setVariables()`. Пример вызова:
````php
$this->getView()->setVariables(array(
    'foo' => 'bar',
    'bar' => array(42, 'baz'),
));
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
		<?php $this->getView()->render(); ?>
	</body>
</html>
````
Обратите внимание, что место размещения разметки View в разметке Layout
определяется местом вызова метода `$this->getView()->render()`.

### Сниппеты
Работа со сниппетами, в целом, аналогична работе с композитами View и Layout. 
Класс сниппета также является наследником класса `\core\AView` и процесс передачи 
и вызова  пользовательских переменных осуществляется аналогично таковому в Макете 
и Представлении. Композиты представления могут содержать более одного сниппета. 
Сниппет не может включать в себя другой сниппет.

Вынесем часть разметки из предыдущего примера в отдельный сниппет `topbar`. Файл 
`..\topbar.php` будет содержать следующий код:
````php
<h1>User <?= $this->isUserOnline ? 'online' : 'offline'; ?></h1>
````
Добавим объект сниппета объекту View и передадим в него флаг `isUserOnline`:
````php
\\...
public function actionFoo() {
	\\...
	$this->setView('test', 'foo');
	$this->setLayout('main');
	$this->getView()->addSnippet('topbar');
	$this->getView()
	    ->getSnippet('topbar')
	    ->isUserOnline    = TRUE;
	$this->getView()->foo = 'bar';
	$this->getView()->bar = array(42, 'baz');
	\\...
}
\\...
````
Место рендеринга сниппета `topbar` определяется местом вызова метода 
`$this->getSnippet('topbar')->render()` в файле с разметкой View:
````php
<!-- ... -->
<div>
    <?php $this->getSnippet('topbar')->render(); ?>
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