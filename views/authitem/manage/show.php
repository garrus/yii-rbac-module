<?php
/**
 * show.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The auth items information view. Also this view is used for deleting
 * confirmation.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.manage
 * @since 1.0.0
 */
?>
<?php if ($updateList) : ?>
    <script type="text/javascript">
        <?php echo SHtml::ajax(array(
          'type'=>'POST',
          'url'=>array('manage'),
          'update'=>'#list',
          )); ?>
    </script>
<?php else : ?>
    <h2><?php echo $model->name; ?></h2>

	<?php
	$this->widget('zii.widgets.CDetailView', [
		'data' => $model,
		'attributes' => [
			[
				'name' => 'type',
				'value' => AuthItem::$TYPES[$model->type],
			],
			'description',
			'bizrule',
			'data'
		]
	]);
	?>

    <div class="simple">
        <?php if ($delete) : ?>
            <?php echo Helper::translate('srbac', 'Really delete') ?> <?php echo $model->name; ?> ?
            <?php echo SHtml::ajaxButton(Helper::translate('srbac', 'Yes'),
                array('delete', 'id' => $model->name),
                array(
                    'type' => 'POST',
                    'update' => '#preview'
                ), array('id' => 'deleteButton', 'class' => 'btn btn-danger btn-sm')); ?>
        <?php endif; ?>
    </div>
<?php endif;