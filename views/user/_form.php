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
)); ?>
	<?php echo $form->errorSummary($model); ?>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php if ($model->isNewRecord):
			echo $form->textField($model,'name',array('size'=>15, 'maxlength'=>15, 'required' => 'required', 'class' => 'form-control'));
			echo '<div class="field-desc text-info">必填</div>';
		else:
			echo '<span class="text-muted">', $model->name, '</span>';
		endif;?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'password_plain'); ?>
		<?php echo $form->passwordField($model,'password_plain',array('size'=>15, 'required' => 'required', 'class' => 'form-control')); ?>
		<div class="field-desc text-info">必填</div>
		<?php echo $form->error($model,'password_plain'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'password_plain_confirm'); ?>
		<?php echo $form->passwordField($model,'password_plain_confirm',array('size'=>15, 'required' => 'required', 'class' => 'form-control')); ?>
		<div class="field-desc text-info">必填</div>
		<?php echo $form->error($model,'password_plain_confirm'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'displayName'); ?>
		<?php echo $form->textField($model,'displayName',array('size'=>15,'maxlength'=>32, 'required' => 'required', 'class' => 'form-control')); ?>
		<div class="field-desc text-info">必填</div>
		<?php echo $form->error($model,'displayName'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>15,'maxlength'=>63, 'class' => 'form-control')); ?>
		<div class="field-desc text-info">可作为登录名使用</div>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>15,'maxlength'=>15, 'class' => 'form-control')); ?>
		<div class="field-desc text-info">可作为登录名使用</div>
		<?php echo $form->error($model,'mobile'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo $form->labelEx($model,'stuff_no'); ?>
		<?php echo $form->textField($model,'stuff_no',array('size'=>15,'maxlength'=>15, 'class' => 'form-control')); ?>
		<div class="field-desc text-info">可作为登录名使用</div>
		<?php echo $form->error($model,'stuff_no'); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo CHtml::submitButton('提交', ['class' => 'btn btn-primary']);?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->