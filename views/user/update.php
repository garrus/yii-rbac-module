<?php
/* @var $this UserController */
/* @var $model SrbacUser */
$this->renderPartial('/authitem/frontpage');
?>

<h1>编辑用户 <?php echo $model->displayName, ' [', $model->name, '#', $model->id, ']'; ?></h1>
<?php echo CHtml::link('<< 返回用户列表', ['/'. $this->module->id. '/'. $this->id. '/index']);?>
<hr>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>