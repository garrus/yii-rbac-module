<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */
$this->renderPartial('/authitem/frontpage');
?>

<h1>用户列表</h1>
<hr>

<p>
	<?php echo CHtml::link('创建新用户', ['create'], ['class' => 'btn btn-primary']);?>
</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'htmlOptions' => ['class' => ''],
	'template' => '{summary} {items} {pager}',
	'itemsCssClass' => 'table table-hover table-condensed table-striped',
	'columns' => [
		'id',
		'displayName',
		'name',
		'stuff_no',
		'mobile',
		[
			'name' => 'email',
			'value' => 'CHtml::link($data->email, "mailto://". $data->email)',
			'type' => 'raw',
		],
		'create_time',
		[
			'name' => 'update_time',
			'value' => '$data->update_time == "0000-00-00 00:00:00" ? "-" : $data->update_time',
		],
		[
			'class' => 'CButtonColumn',
			'template' => '{update}',
			'updateButtonOptions' => ['class' => 'btn btn-xs btn-success'],
			'updateButtonImageUrl' => false,
			'updateButtonLabel' => '编辑',

//			'deleteButtonOptions' => ['class' => 'btn btn-xs btn-danger'],
//			'deleteButtonImageUrl' => false,
		]
	]
)); ?>
