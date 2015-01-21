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
<?php if (Yii::app()->user->hasFlash('updateName')): ?>
    <div id="messageUpd"
         style="color:red;font-weight:bold;font-size:14px;text-align:center
       ;position:relative;border:solid black 2px;background-color:#DDDDDD"
        >
        <?php echo Yii::app()->user->getFlash('updateName'); ?>
        <?php
        Yii::app()->clientScript->registerScript(
            'myHideEffect',
            '$("#messageUpd").animate({opacity: 0}, 2000).fadeOut(500);',
            CClientScript::POS_READY
        );
        ?>
    </div>
<?php endif; ?>
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
    <div class="form-inline" style="margin: 0; display: inline-block;">
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
	<th><?php echo Helper::translate('srbac', 'Name'); ?></th>
<th>
	<?php
	echo SHtml::dropDownList('selectedType', Yii::app()->user->getState("selectedType"),
		AuthItem::$TYPES,
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
<th class="text-center"><?php echo Helper::translate('srbac', 'Actions') ?></th>
</tr>
<?php $header = ob_get_clean();
$this->widget('zii.widgets.CListView', [
	'dataProvider' => $dataProvider,
	'itemView' => 'manage/_item_view',
	'tagName' => 'table',
	'template' => '<table class="table table-hover table-condensed table-striped" id="auth-item-list">
	<thead>'. $header. '</thead>
	<tbody>{items}</tbody>
</table>',
]);
?>
<div class="simple">
<?php $this->widget('system.web.widgets.pagers.CLinkPager', [
	'pages' => $dataProvider->pagination,
	'htmlOptions' => [
		'class' => 'pagination',
	],
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
]);?>
</div>
<?php echo SHtml::endForm(); ?>
<br/>
