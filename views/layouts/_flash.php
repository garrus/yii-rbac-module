<?php
/**
 * @var array $flashes
 */
$types = ['success', 'error', 'warning'];
foreach ($types as $type):
	if (isset($flashes[$type])):?>
		<div class="alert alert-<?php echo $type;?> alert-dismissible fade in" role="alert">
			<span class="btn btn-link pull-right" data-dismiss="alert">&times;</span>
			<h5><?php echo $flashes[$type];?></h5>
		</div>
	<?php endif;
endforeach;
