<?php
/* @var $this UserController */
/* @var $model SrbacUser */
/* @var $form CActiveForm */
?>

<div class="row">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions' => ['class' => 'form-inline col-md-12', 'role' => 'form'],
)); ?>

	<div class="form-group col-md-4">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>11,'maxlength'=>11,'class' => 'form-control')); ?>
	</div>

	<div class="form-group col-md-4">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>15,'maxlength'=>15,'class' => 'form-control')); ?>
	</div>

	<div class="form-group col-md-4">
		<?php echo $form->label($model,'displayName'); ?>
		<?php echo $form->textField($model,'displayName',array('size'=>15,'maxlength'=>32,'class' => 'form-control')); ?>
	</div>

	<div class="form-group col-md-4">
		<?php echo $form->label($model,'stuff_no'); ?>
		<?php echo $form->textField($model,'stuff_no',array('size'=>15,'maxlength'=>15,'class' => 'form-control')); ?>
	</div>

	<div class="form-group col-md-4">
		<?php echo $form->label($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>15,'maxlength'=>63,'class' => 'form-control')); ?>
	</div>

	<div class="form-group col-md-4">
		<?php echo $form->label($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>15,'maxlength'=>15,'class' => 'form-control')); ?>
	</div>

	<div class="form-group col-md-12">
		<?php echo CHtml::submitButton('Search', ['class' => 'btn btn-primary']); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->