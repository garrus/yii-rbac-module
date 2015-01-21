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
<?php echo SHtml::beginForm(); ?>
<div class="well well-sm">
        <?php
        echo SHtml::ajaxLink(
			'<strong>+</strong>'. Helper::translate('srbac', 'Create'),
//            SHtml::image($this->module->getIconsPath() . '/create.png',
//                Helper::translate('srbac', 'Create'),
//                array('border' => 0,
//                    'class' => 'icon', 'title' => Helper::translate('srbac', 'Create'),
//                )
//            ) . Helper::translate('srbac', 'Create'),
            array('create'),
            array(
                'type' => 'POST',
                'update' => '#preview',
                'beforeSend' => 'function(){$("#preview").addClass("srbacLoading");}',
                'complete' => 'function(){$("#preview").removeClass("srbacLoading");}',
            ),
			['class' => 'btn btn-success btn-sm', 'style' => 'color: white; font-size: 13px; padding: 3px 7px;']
        ); ?>
    <div class="form-inline pull-right">
        <div class="form-group-sm">
		<?php
		echo CHtml::label(Helper::translate('srbac', 'Search'). ' ', 'search-auth-item', ['style' => 'margin: 0 0.5em 0 1em;']);
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

        echo SHtml::imageButton($this->module->getIconsPath() . '/preview.png',
            array(
                'border' => 0,
                'title' => Helper::translate('srbac', 'Search'),
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
    </div>
</div>
<br/>

<?php
ob_start();
?>
<tr>
<th style="width: 80px;">
	<?php
	echo SHtml::dropDownList('selectedType', Yii::app()->user->getState("selectedType"),
		AuthItem::$TYPE_LABELS,
		array(
			'prompt' => Helper::translate('srbac', 'All'),
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
<th><?php echo Helper::translate('srbac', 'Name'); ?></th>
<th><?php echo Helper::translate('srbac', 'Description'); ?></th>
<th style="width: 100px;"></th>
</tr>
<?php $header = ob_get_clean();
$this->widget('zii.widgets.CListView', [
	'dataProvider' => $dataProvider,
	'itemView' => 'manage/_item_view',
	'tagName' => 'table',
	'template' => '<table class="table table-hover table-condensed table-striped" id="auth-item-list">
	<thead>'. $header. '</thead>
	<tbody>{items}</tbody>
</table>{pager}',
	'pagerCssClass' => '',
	'pager' => [
		'class' => 'CLinkPager',
		'htmlOptions' => ['class' => 'pagination'],
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
echo SHtml::endForm(); ?>
<br/>
