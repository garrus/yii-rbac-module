<?php
/**
 * list.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */
/**
 * The auth items list view
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.manage
 * @since 1.0.0
 *
 * @var CActiveDataProvider $dataProvider
 */
?>
<?php if (Yii::app()->user->hasFlash('updateName')):
	Yii::app()->clientScript->registerScript(
		'myHideEffect',
		'noty({type: "info", "timeout": 5000, "layout": "topCenter", "text": '. json_encode(Yii::app()->user->getFlash('updateName')).'})',
		CClientScript::POS_READY
	);
endif; ?>
<?php echo SrbacHtml::beginForm(); ?>
<div class="well well-sm form-inline text-right">
	<div class="form-group-sm pull-left">
		<?php
		echo CHtml::label(SrbacHelper::translate('srbac', 'Search'). ' ', 'search-auth-item', ['style' => 'margin: 0 10px;']);
		$this->widget('CAutoComplete',
			array(
				'name' => 'name',
				'max' => 10,
				'delay' => 300,
				'matchCase' => false,
				'url' => array('autocomplete'),
				'minChars' => 2,
				'htmlOptions' => ['class' => 'form-control input-sm'],
				'id' => 'search-auth-item',
			)
		);

		echo SrbacHtml::imageButton($this->module->getIconsPath() . '/preview.png',
			array(
				'border' => 0,
				'title' => SrbacHelper::translate('srbac', 'Search'),
				'live' => false,
				'ajax' => array(
					'type' => 'POST', 'url' => array('list'), 'update' => '#list',
					'beforeSend' => 'function(){$("#list").addClass("srbacLoading");}',
					'complete' => 'function(){$("#list").removeClass("srbacLoading");}',
				),
				'style' => 'vertical-align: middle;margin-left: 10px;',
			)
		);
		?>
	</div>
		<?php
		echo SrbacHtml::ajaxLink(
			'<strong>+</strong>'. SrbacHelper::translate('srbac', 'Create'),
			array('create'),
			array(
				'type' => 'POST',
				'update' => '#preview',
				'beforeSend' => 'function(){$("#preview").addClass("srbacLoading");}',
				'complete' => 'function(){$("#preview").removeClass("srbacLoading");}',
			),
			['class' => 'btn btn-success btn-sm', 'style' => 'color: white; font-size: 13px; padding: 3px 7px; margin-right: 10px;']
		); ?>
</div>
<br/>

<?php
ob_start();
?>
<tr>
<th style="width: 80px;">
	<?php
	echo SrbacHtml::dropDownList('selectedType', Yii::app()->user->getState("selectedType"),
		AuthItem::$TYPE_LABELS,
		array(
			'prompt' => SrbacHelper::translate('srbac', 'All'),
			'live' => false,
			'ajax' => array(
				'type' => 'POST',
				'url' => array('list'),
				'update' => '#list',
				'beforeSend' => 'function(){$("#list").addClass("srbacLoading");}',
				'complete' => 'function(){$("#list").removeClass("srbacLoading");}',
			),
			'class' => 'form-control input-sm',
		)
	);
	?>
</th>
<th><?php echo SrbacHelper::translate('srbac', 'Name'); ?></th>
<th><?php echo SrbacHelper::translate('srbac', 'Description'); ?></th>
<th style="width: 100px;"></th>
</tr>
<?php $header = ob_get_clean();
$this->widget('zii.widgets.CListView', [
	'dataProvider' => $dataProvider,
	'itemView' => 'manage/_item_view',
	'itemsTagName' => 'tbody',
	'id' => 'auth-item-list',
	'tagName' => 'table',
	'htmlOptions' => ['class' => 'table table-hover table-condensed table-striped'],
	'template' => '<thead>'. $header. '</thead>	{items} <tr><td colspan="4">{pager}</td></tr>',
	'pagerCssClass' => '',
	'pager' => [
		'class' => 'CLinkPager',
		'htmlOptions' => ['class' => 'pagination', 'style' => 'margin: 10px 15px;'],
		'header' => '<nav>',
		'footer' => '</nav>',
		'firstPageCssClass' => '',
		'lastPageCssClass' => '',
		'previousPageCssClass' => '',
		'nextPageCssClass' => '',
		'internalPageCssClass' => '',
		'hiddenPageCssClass' => 'disabled',
		'selectedPageCssClass' => 'active',
		'prevPageLabel' => '上一页',
		'nextPageLabel' => '下一页',
		'firstPageLabel' => '&laquo;',
		'lastPageLabel' => '&raquo;',
	]
]);
echo SrbacHtml::endForm(); ?>
<br/>
