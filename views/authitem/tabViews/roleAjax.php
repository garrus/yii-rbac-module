<?php
/**
 * roleAjax.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The assigning task to roles listboxes
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.tabViews
 * @since 1.0.0
 */
?>
<table width="100%">
    <tr>
        <th><?php echo SrbacHelper::translate('srbac', 'Assigned Tasks') ?></th>
        <th>&nbsp;</th>
        <th><?php echo SrbacHelper::translate('srbac', 'Not Assigned Tasks') ?></th>
    </tr>
    <tr>
        <td width="45%">
            <?php echo SrbacHtml::activeDropDownList($model, 'name[revoke]',
                SrbacHtml::listData(
                    $data["roleAssignedTasks"], 'name', 'name'),
                array('size' => $this->module->listBoxNumberOfLines, 'multiple' => 'multiple', 'class' => 'dropdown')) ?>
        </td>
        <td width="10%" align="center">
            <?php
            $ajax =
                array(
                    'type' => 'POST',
                    'update' => '#tasks',
                    'beforeSend' => 'function(){$("#loadMessRole").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#loadMessRole").removeClass("srbacLoading");}',
				);
            echo SrbacHtml::ajaxSubmitButton('<<', array('assign', 'assignTasks' => 1), $ajax, $data['assign']); ?>
            <?php
            $ajax =
                array(
                    'type' => 'POST',
                    'update' => '#tasks',
                    'beforeSend' => 'function(){$("#loadMessRole").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#loadMessRole").removeClass("srbacLoading");}',
				);
            echo SrbacHtml::ajaxSubmitButton('>>', array('assign', 'revokeTasks' => 1), $ajax, $data['revoke']); ?>
        </td>
        <td width="45%">
            <?php echo SrbacHtml::activeDropDownList($model, 'name[assign]',
                SrbacHtml::listData(
                    $data["roleNotAssignedTasks"], 'name', 'name'),
                array('size' => $this->module->listBoxNumberOfLines, 'multiple' => 'multiple', 'class' => 'dropdown')); ?>
        </td>
    </tr>
</table>
<div id="loadMessRole" class="message">
    <?php echo "&nbsp;" . $message ?>
</div>
