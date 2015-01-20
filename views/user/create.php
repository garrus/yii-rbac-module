<?php
/* @var $this UserController */
/* @var $model SrbacUser */
$this->renderPartial('/authitem/frontpage');
?>

<h1>创建新用户</h1>
<?php echo CHtml::link('<< 返回用户列表', ['/'. $this->module->id. '/'. $this->id. '/index']);?>
<hr>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>