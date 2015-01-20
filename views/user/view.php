<?php
/* @var $this UserController */
/* @var $model SrbacUser */
$this->renderPartial('/authitem/frontpage');
?>

<h1>用户 <?php echo $model->name;?> #<?php echo $model->id; ?></h1>
<?php echo CHtml::link('<< 返回用户列表', ['/'. $this->module->id. '/'. $this->id. '/index']);?>
<hr>

<p>
<?php echo CHtml::link('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);?>
 <?php echo CHtml::button('删除', [
	'class' => 'btn btn-danger',
	'submit' => ['/'. $this->module->id. '/'. $this->id. '/delete','id'=>$model->id],
	'confirm' =>'确定要删除用户 '. $model->name. ' ('. $model->displayName. ') 吗？'
]);?>
</p>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'displayName',
		'stuff_no',
		'email',
		'mobile',
		'create_time',
		'update_time',
	),
)); ?>
