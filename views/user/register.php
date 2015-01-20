<?php
/* @var $this UserController */
/* @var $model SrbacUser */
?>

<div class="row" style="
	margin: 100px auto;
	width: 400px;
	border-radius: 10px;
	border: 2px solid #333;
	box-shadow: 0 0 8px 2px gray;
	padding: 15px 20px;
	">

	<h2 class="text-center text-info">注册</h2>
	<hr>
<?php $this->renderPartial('_form', ['model' => $model]);?>
	<div class="text-right"><?php echo CHtml::link('已有帐号？点此登录', ['login']);?></div>
</div>