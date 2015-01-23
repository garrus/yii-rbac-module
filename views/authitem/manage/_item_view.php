<?php
/**
 * @var AuthItem $data
 * @var int $index
 */
$itemHash = substr(md5($data->name), 3, 8);

if (Yii::app()->request->isAjaxRequest):
	Yii::app()->clientScript->registerScript('unbind-event-'. $itemHash, <<<SCRIPT
	jQuery("body").off("click", "#show-auth-item-{$itemHash}");
	jQuery("body").off("click", "#update-auth-item-{$itemHash}");
	jQuery("body").off("click", "#delete-auth-item-{$itemHash}");
SCRIPT
	);
endif;
?>
<tr class="<?php echo $index % 2 ? 'even' : 'odd'; ?>">
	<td class="text-center"><?php echo SrbacHtml::encode(AuthItem::$TYPE_LABELS[$data->type]); ?></td>
	<td><?php
		echo SrbacHtml::ajaxLink($data->name,
			array('show', 'id' => $data->name),
			array('type' => 'POST', 'update' => '#preview',
				'beforeSend' => 'function(){$("#preview").addClass("srbacLoading");}',
				'complete' => 'function(){$("#preview").removeClass("srbacLoading");}',
			), array('title' => $data->description ? $data->description : $data->name, 'id' => 'show-auth-item-'. $itemHash)
		);
		?></td>
	<td><?php echo CHtml::encode($data->description);?></td>

	<td class="text-right">
		<?php
		echo SrbacHtml::ajaxLink(
			SrbacHelper::translate('srbac', 'Update'),
//			SHtml::image($this->module->getIconsPath() . '/update.png',
//				Helper::translate('srbac', 'Update'),
//				array('border' => 0, 'title' => Helper::translate('srbac', 'Update'))),
			array('update', 'id' => $data->name),
			array(
				'type' => 'POST',
				'update' => '#preview',
				'beforeSend' => 'function(){$("#preview").addClass("srbacLoading");}',
				'complete' => 'function(){$("#preview").removeClass("srbacLoading");}',
			),
			['class' => 'btn btn-primary btn-xs', 'id' => 'update-auth-item-'. $itemHash]
		);
		if ($data->name != SrbacHelper::findModule('srbac')->superUser):
			echo SrbacHtml::ajaxLink(
				SrbacHelper::translate('srbac', 'Delete'),
//				SHtml::image($this->module->getIconsPath() . '/delete.png'
//					, Helper::translate('srbac', 'Delete'),
//					array('border' => 0, 'title' => Helper::translate('srbac', 'Delete'))),
				array('confirm', 'id' => $data->name),
				array(
					'type' => 'POST',
					'update' => '#preview',
					'beforeSend' => 'function(){$("#preview").addClass("srbacLoading");}',
					'complete' => 'function(){$("#preview").removeClass("srbacLoading");}',
				),
				['class' => 'btn btn-danger btn-xs', 'style' => 'margin-left: 10px;', 'id' => 'delete-auth-item-'. $itemHash]
			);
		endif; ?>
	</td>
</tr>
