<?php
/**
 * _form.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The create new auth item form.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.manage
 * @since 1.0.0
 *
 * @var CActiveForm $form
 * @var AuthItem $model
 */
?>
<div class="srbacForm row">

    <?php $form = $this->beginWidget('CActiveForm', [
		'htmlOptions' => [
			'class' => 'form-inline col-md-12',
			'role' => 'form',
		],
	]); ?>

    <?php echo $form->errorSummary($model, '', null, ['class' => 'alert alert-danger']); ?>

    <div class="form-group col-md-12">
        <?php echo $form->labelEx($model, 'name', ['class' => 'control-label col-md-2']); ?>
        <?php echo $form->textField($model, 'name',
            $model->name == SrbacHelper::findModule('srbac')->superUser ?
                array('size' => 20, 'disabled' => "disabled", 'class' => 'form-control input-sm') : array('size' => 20, 'class' => 'form-control input-sm')); ?>
    </div>
    <div class="form-group col-md-12">
        <?php echo $form->labelEx($model, 'type', ['class' => 'control-label col-md-2']); ?>
        <?php echo $form->dropDownList($model, 'type', AuthItem::$TYPE_LABELS,
            $model->name == SrbacHelper::findModule('srbac')->superUser || $update
                ? array('disabled' => "disabled", 'class' => 'form-control input-sm') : array('class' => 'form-control input-sm')); ?>
    </div>
    <div class="form-group col-md-12">
        <?php echo $form->labelEx($model, 'description', ['class' => 'control-label col-md-2']); ?>
        <?php echo $form->textArea($model, 'description', array('rows' => 3, 'cols' => 20, 'class' => 'form-control input-sm')); ?>
    </div>
    <?php if (Yii::app()->user->hasFlash('updateSuccess')):
		Yii::app()->clientScript->registerScript(
			'myHideEffect',
			'noty({type: "success", "timeout": 5000, "layout": "topCenter", "text": '. json_encode(Yii::app()->user->getFlash('updateSuccess')).'})',
			CClientScript::POS_READY
		);
    elseif (Yii::app()->user->hasFlash('updateError')):
		Yii::app()->clientScript->registerScript(
			'myHideEffect',
			'noty({type: "error", "timeout": 6000, "layout": "topCenter", "text": '. json_encode(Yii::app()->user->getFlash('updateError')).'})',
			CClientScript::POS_READY
		);
    endif; ?>
    <div class="form-group col-md-12">
        <?php echo $form->labelEx($model, 'bizrule', ['class' => 'control-label col-md-2']); ?>
        <?php echo $form->textArea($model, 'bizrule',
            $model->name == SrbacHelper::findModule('srbac')->superUser ?
                array('rows' => 3, 'cols' => 20, 'disabled' => 'disabled', 'class' => 'form-control input-sm') : array('rows' => 3, 'class' => 'form-control input-sm', 'cols' => 20)); ?>
    </div>
    <div class="form-group col-md-12">
        <?php echo $form->labelEx($model, 'data', ['class' => 'control-label col-md-2']); ?>
        <?php echo $form->textField($model, 'data',
            $model->name == SrbacHelper::findModule('srbac')->superUser ?
                array('disabled' => 'disabled', 'size' => 30, 'class' => 'form-control input-sm') : array('size' => 30, 'class' => 'form-control input-sm')); ?>
    </div>
    <?php echo CHtml::hiddenField('oldName', $model->name); ?>
    <div class="form-group col-md-12">
		<label class="control-label col-md-2"> </label>
        <?php echo SrbacHtml::ajaxSubmitButton(
            $update ? SrbacHelper::translate('srbac', 'Save') :
                SrbacHelper::translate('srbac', 'Create'),
            $update ? array('update', 'id' => urlencode($model->name)) : array('create'),
            array(
                'type' => 'POST',
                'update' => '#preview'
            ), array('name' => 'saveButton2', 'class' => 'btn btn-primary btn-sm'));
        ?>
    </div>
    <?php $this->endWidget($form); ?>

</div><!-- srbacForm -->

