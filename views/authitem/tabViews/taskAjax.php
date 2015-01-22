<?php
/**
 * taskAjax.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The assigning operations to tasks listboxes
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.tabViews
 * @since 1.0.0
 */
?>
<table width="100%">
    <tr>
        <th><?php echo SrbacHelper::translate('srbac', 'Assigned Operations') ?></th>
        <th>&nbsp;</th>
        <th><?php echo SrbacHelper::translate('srbac', 'Not Assigned Operations') ?></th>
    </tr>
    <tr>
        <td width="45%">
            <?php echo SrbacHtml::activeDropDownList($model, 'name[revoke]',
                SrbacHtml::listData(
                    $data['taskAssignedOpers'], 'name', 'name'),
                array('size' => $this->module->listBoxNumberOfLines, 'multiple' => 'multiple', 'class' => 'dropdown')) ?>
        </td>
        <td width="10%" align="center">
            <?php
            $ajax =
                array(
                    'type' => 'POST',
                    'update' => '#operations',
                    'beforeSend' => 'function(){$("#loadMessTask").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#loadMessTask").removeClass("srbacLoading");}',
				);
            echo SrbacHtml::ajaxSubmitButton('<<', array('assign', 'assignOpers' => 1), $ajax, $data['assign']); ?>
            <?php
            $ajax =
                array(
                    'type' => 'POST',
                    'update' => '#operations',
                    'beforeSend' => 'function(){$("#loadMessTask").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#loadMessTask").removeClass("srbacLoading");}',
				);
            echo SrbacHtml::ajaxSubmitButton('>>', array('assign', 'revokeOpers' => 1), $ajax, $data['revoke']); ?>
        </td>
        <td width="45%">
            <?php echo SrbacHtml::activeDropDownList($model, 'name[assign]',
                SrbacHtml::listData(
                    $data['taskNotAssignedOpers'], 'name', 'name'),
                array('size' => $this->module->listBoxNumberOfLines, 'multiple' => 'multiple', 'class' => 'dropdown')); ?>
        </td>
    </tr>
</table>
<div id="loadMessTask" class="message">
    <?php echo "&nbsp;" . $message ?>
</div>