<?php
/* @var $this UserController */
/* @var $model SrbacUser */
/* @var $dataProvider CActiveDataProvider */
$this->renderPartial('/authitem/frontpage');
?>

<h1>用户列表</h1>
<hr>

<p>
	<?php echo CHtml::link('<strong>+</strong> 创建新用户', ['create'], ['class' => 'btn btn-success']);?>
	<?php if (Yii::app()->user->name == SrbacUser::SA_NAME):?>
	<?php echo CHtml::link('修改管理员密码', ['/srbac/user/changePassword'], ['class' => 'btn btn-warning', 'target' => '__blank']);?>
	<?php endif;?>
</p>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'filter' => $model,
	'htmlOptions' => ['class' => ''],
	'template' => '{summary} {items} {pager}',
	'itemsCssClass' => 'table table-hover table-condensed table-striped',
	'columns' => [
		[
			'name' => 'id',
			'headerHtmlOptions' => ['width' => '30px'],
		],
		'displayName',
		'name',
		[
			'name' => 'email',
			'value' => 'CHtml::link($data->email, "mailto://". $data->email)',
			'type' => 'raw',
		],
		'stuff_no',
		'mobile',
		[
			'header' => '角色',
			'value' => 'implode("<br>", $data->getRoleNames())',
			'type' => 'raw',
		],
		[
			'name' => 'create_time',
			'filter' => false,
		],
		[
			'class' => 'CButtonColumn',
			'template' => '{update} {delete}',
			'updateButtonOptions' => ['class' => 'btn btn-xs btn-primary'],
			'updateButtonImageUrl' => false,
			'updateButtonLabel' => '编辑',

			'deleteButtonOptions' => ['class' => 'btn btn-xs btn-danger'],
			'deleteButtonLabel' => '删除',
			'deleteButtonImageUrl' => false,

			'buttons' => [
				'update' => [
					'visible' => '!$data->isAdmin',
				],
				'delete' => [
					'visible' => '!$data->isAdmin',
				],
			],


		]
	]
));
