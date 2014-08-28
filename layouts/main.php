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