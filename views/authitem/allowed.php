<?php
/**
 * allowed.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The view for the editing of the alwaysAllowed list
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem
 * @since 1.1.0
 */
?>
<?php

//CVarDumper::dump($controllers, 3, true);
foreach ($controllers as $n => $controller) {
    $title = $controller["title"];
    $data = array();
    foreach ($controller["actions"] as $key => $val) {
        $data[$val] = $val;
    }
    if (sizeof($data) > 0) {
        $select = $controller["allowed"];
        // It seems that this tabview conflicts with assign tabview so I raise the tab number by 3
        //$cont[$n+3]["title"] = str_replace("Controller", "", $title);
        //$cont[$n+3]["content"] = SHtml::checkBoxList($title, $select, $data);


        $cont["tab_" . $n] = array(
            "title" => str_replace("Controller", "", $title),
            "content" => SrbacHtml::checkBoxList($title, $select, $data));
    }
}
?>
<?php echo SrbacHtml::form(); ?>
<div class="col-md-6">
	<div class="vertTab">
		<?php
		$this->widget('system.web.widgets.CTabView',
			array(
				'tabs' => $cont,
				//'cssFile' => $this->module->getCssUrl(),
			));
		?>
	</div>
	<div class="action">
		<?php echo SrbacHtml::ajaxSubmitButton(SrbacHelper::translate("srbac", "Save"),
			array('saveAllowed'),
			array(
				'type' => 'POST',
				'update' => '#wizard',
				'beforeSend' => 'function(){$("#wizard").addClass("srbacLoading");}',
				'complete' => 'function(){$("#wizard").removeClass("srbacLoading");}',
			),
			array(
				'name' => 'buttonSave',
			)
		)
		?>
	</div>
</div>
<?php echo SrbacHtml::endForm(); ?>
<!--Adjust tabview height--->
<script type="text/javascript">
    var tabsHeight = $(".tabs").height();
    if (tabsHeight > 260) {
        $(".view").height(tabsHeight - 16);
    } else {
        $(".view").height(260);
        $(".tabs").attr("style", "border-bottom:none");

    }
</script>
