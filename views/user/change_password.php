<?php
/**
 * @var SrbacUser $model
 * @var CActiveForm $form
 * @var boolean $needValidateOriPass
 */
?>
<div class="row" style="
	margin: 150px auto;
	width: 400px;
	border-radius: 10px;
	border: 2px solid #333;
	box-shadow: 0 0 8px 2px gray;
	padding: 15px 20px;
	">

	<h3 class="text-center text-info">修改用户 <strong><?php echo $model->name;?></strong> 的密码</h3>
	<hr>
	<?php $form = $this->beginWidget('CActiveForm', [
			'id' => 'change-password-form',
			'htmlOptions' => [
				'class' => 'form-inline',
				'role' => 'form',
			],
			'enableAjaxValidation' => false,
			'enableClientValidation' => false,
			'errorMessageCssClass' => 'text-danger field-desc',
		]);
	echo $form->errorSummary($model, '', null, ['class' => 'alert alert-danger']);?>

	<?php if ($needValidateOriPass):?>
	<div class="form-group" style="margin-bottom: 35px;">
		<?php echo CHtml::label('原密码', 'ori_pass', ['class' => 'control-label']);?>
		<?php echo CHtml::passwordField('ori_pass', '', ['class' => 'form-control']);?>
		<?php echo $form->error($model, 'ori_pass', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>
	<?php endif;?>

	<div class="form-group">
		<?php echo $form->labelEx($model, 'password_plain', ['class' => 'control-label']);?>
		<?php echo $form->passwordField($model, 'password_plain', ['class' => 'form-control']);?>
		<?php echo $form->error($model, 'password_plain', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model, 'password_plain_confirm', ['class' => 'control-label']);?>
		<?php echo $form->passwordField($model, 'password_plain_confirm', ['class' => 'form-control']);?>
		<?php echo $form->error($model, 'password_plain_confirm', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>

	<div class="form-group">
		<?php echo CHtml::submitButton('提交', ['class' => 'btn btn-primary']);?>
	</div>

	<?php $this->endWidget($form);?>
</div>