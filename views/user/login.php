<?php
/**
 * @var UserController $this
 * @var SrbacLoginForm $model
 * @var CActiveForm $form
 */
?>
<script type="text/javascript">
	if (window.top !== window) window.top.location.href = location.href;
</script>
<div class="row" style="
	margin: 150px auto;
	width: 400px;
	border-radius: 10px;
	border: 2px solid #333;
	box-shadow: 0 0 8px 2px gray;
	padding: 15px 20px;
	">

	<?php $this->renderFlash();?>

	<h2 class="text-center text-info">登录管理后台</h2>
	<hr>

	<?php $form = $this->beginWidget('CActiveForm', [
		'id' => 'srbac-login-form',
		'enableAjaxValidation' => false,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'text-danger field-desc',
		'htmlOptions' => [
			'class' => 'form-inline',
			'role' => 'form',
		],
	]);

	echo $form->errorSummary($model, '', null, ['class' => 'alert alert-danger']);?>

	<div class="form-group">
	<?php $loginType = 'name';
		$types = ['name', 'stuff_no', 'email', 'mobile'];
		foreach ($types as $type) {
			if (!empty($model->{$type})) {
				$loginType = $type;
				break;
			}
		}
		echo '<label for="login_type">登录方式</label>';
		echo CHtml::dropDownList('login_type', $loginType, [
			'name' => '登录名',
			'stuff_no' => '工号',
			'email' => '邮箱',
			'mobile' => '手机号',
		], ['class' => 'form-control']);
	?>
	</div>

	<?php foreach ($types as $type):?>
	<div class="form-group login-name-row <?php if ($type != $loginType) echo 'hide';?>" role="login-by-<?php echo $type;?>">
		<?php echo $form->labelEx($model, $type, ['class' => 'control-label']);?>
		<?php echo $form->textField($model, $type, ['class' => 'form-control', 'disabled' => $type == $loginType ? '' : 'disabled']);?>
		<?php echo $form->error($model, $type);?>
	</div>
	<?php endforeach;?>

	<div class="form-group">
		<?php echo $form->labelEx($model, 'password', ['class' => 'control-label']);?>
		<?php echo $form->passwordField($model, 'password', ['class' => 'form-control']);?>
		<?php echo $form->error($model, 'password', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>

	<div class="form-group">
		<?php echo CHtml::submitButton('登录', ['class' => 'btn btn-primary']);?>
		or <?php echo CHtml::link('注册', ['register'], ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>

	<?php $this->endWidget($form);?>
</div>

<script type="text/javascript">
	$(function(){
		var $form = $("#srbac-login-form");
		$form.delegate("#login_type", "change", function(e){
			var type = $(this).val();
			var $targetRow = $form.find(".login-name-row[role=login-by-" + type + "]");
			$targetRow.toggleClass("hide", false).find("input[type=text]").attr("disabled", false);
			$targetRow.siblings(".login-name-row").toggleClass("hide", true).find("input[type=text]").attr("disabled", "disabled");
		});
	});
</script>