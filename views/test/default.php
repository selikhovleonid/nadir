<!-- ... -->
<div>
        <h1>User <?= $this->isUserOnline ? 'online':  'offline'; ?></h1>
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