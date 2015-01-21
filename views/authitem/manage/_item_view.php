<?php
/**
 * @var AuthItem $data
 * @var int $index
 */
?>
<tr class="<?php echo $index % 2 ? 'even' : 'odd'; ?>">
	<td><?php
		echo SHtml::ajaxLink($data->name,
			array('show', 'id' => $data->name),
			array('type' => 'POST', 'update' => '#preview',
				'beforeSend' => 'function(){$("#preview").addClass("srbacLoading");}',
				'complete' => 'function(){$("#preview").removeClass("srbacLoading");}',
			), array("title" => $data->description ? $data->description : $data->name)
		);
		?></td>
	<td><?php echo SHtml::encode(AuthItem::$TYPES[$data->type]); ?></td>
	<td class="text-center">
		<?php
		echo SHtml::ajaxLink(
			Helper::translate('srbac', 'Update'),
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
			['class' => 'btn btn-primary btn-xs']
		);
		if ($data->name != Helper::findModule('srbac')->superUser):
			echo SHtml::ajaxLink(
				Helper::translate('srbac', 'Delete'),
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
				['class' => 'btn btn-danger btn-xs', 'style' => 'margin-left: 10px;']
			);
		endif; ?>
	</td>
</tr>