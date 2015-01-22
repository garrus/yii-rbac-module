<?php
/**
 * clearObsolete.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * A view for deleting authItems of controllers that no longer exist
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.manage
 * @since 1.1.1
 */
?>
<?php if ($items) { ?>

    <div class="col-md-6" id="obsoleteList">
        <table class="srbacDataGrid" style="width:50%">
            <tr>
                <th>
                    <?php echo SrbacHelper::translate("srbac", "The following items doesn't seem to belong to a controller"); ?>
                </th>
            <tr>
            <tr>
                <td>
                    <div class="srbacForm">
                        <?php echo SrbacHtml::beginForm() ?>
                        <div>
                            <?php echo SrbacHtml::checkBoxList("items", "", $items, array("checkAll" => SrbacHelper::translate('srbac', 'Check All'))); ?>
                        </div>
                        <div class="action">
                            <?php echo SrbacHtml::ajaxButton(SrbacHelper::translate('srbac', 'Delete'),
                                array("deleteObsolete"),
                                array(
                                    'type' => 'POST',
                                    'update' => '#obsoleteList',
                                    'beforeSend' => 'function(){$("#wiobsoleteListzard").addClass("srbacLoading");}',
                                    'complete' => 'function(){$("#obsoleteList").removeClass("srbacLoading");}',
                                ),
                                array(
                                    'name' => 'buttonSave',
                                ));?>
                        </div>
                        <?php echo SrbacHtml::endForm() ?>
                    </div>
                </td>
            </tr>

        </table>
    </div>

<?php } else { ?>
	<div class="col-md-6">
		<table class="srbacDataGrid" style="width:50%">
			<tr>
				<th>
					<?php echo SrbacHelper::translate("srbac", "No authItems that don't belong to a controller were found"); ?>
				</th>
			</tr>
		</table>
	</div>
<?php } ?>
