<?php
/* @var $this UserController */
/* @var $model SrbacUser */
/* @var $form CActiveForm */
?>
<div class="row">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => ['class' => 'form-inline col-md-12', 'role' => 'form'],
	'errorMessageCssClass' => 'text-danger field-desc',
)); ?>
	<?php echo $form->errorSummary($model, '', null, ['class' => 'alert alert-danger']); ?>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'name',['class' => 'control-label col-md-2']); ?>
		<?php if ($model->isNewRecord):
			echo $form->textField($model,'name',array('size'=>25, 'maxlength'=>15, 'required' => 'required', 'class' => 'form-control'));
			echo '<div class="field-desc text-info">必填</div>';
		else:
			echo '<span class="text-muted">', $model->name, '</span>';
		endif;?>
		<?php echo $form->error($model,'name', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'displayName',['class' => 'control-label col-md-2']); ?>
		<?php echo $form->textField($model,'displayName',array('size'=>25,'maxlength'=>32, 'required' => 'required', 'class' => 'form-control')); ?>
		<div class="field-desc text-info">必填</div>
		<?php echo $form->error($model,'displayName', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'email',['class' => 'control-label col-md-2']); ?>
		<?php echo $form->textField($model,'email',array('size'=>25, 'maxlength'=>63, 'class' => 'form-control', 'required' => 'required')); ?>
		<div class="field-desc text-info">必填</div>
		<?php echo $form->error($model,'email', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'password_plain',['class' => 'control-label col-md-2']); ?>
		<?php echo $form->passwordField($model,'password_plain',array('size'=>25, 'class' => 'form-control')); ?>
		<?php if (!$model->isNewRecord):?>
			<div class="field-desc text-info">若无须修改，请留空</div>
		<?php endif;?>
		<?php echo $form->error($model,'password_plain', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'password_plain_confirm',['class' => 'control-label col-md-2']); ?>
		<?php echo $form->passwordField($model,'password_plain_confirm',array('size'=>25, 'class' => 'form-control')); ?>
		<?php echo $form->error($model,'password_plain_confirm', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'mobile',['class' => 'control-label col-md-2']); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>25,'maxlength'=>15, 'class' => 'form-control')); ?>
		<div class="field-desc text-info">可选，可作为登录名使用</div>
		<?php echo $form->error($model,'mobile', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'stuff_no',['class' => 'control-label col-md-2']); ?>
		<?php echo $form->textField($model,'stuff_no',array('size'=>25,'maxlength'=>15, 'class' => 'form-control')); ?>
		<div class="field-desc text-info">可选，可作为登录名使用</div>
		<?php echo $form->error($model,'stuff_no', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo CHtml::submitButton('提交', ['class' => 'btn btn-primary']);?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->