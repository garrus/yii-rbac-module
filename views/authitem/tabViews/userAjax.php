<?php
/**
 * userAjax.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The assigning roles to users listboxes
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.tabViews
 * @since 1.0.0
 */
?>
<table width="100%">
    <tr>
        <th><?php echo SrbacHelper::translate('srbac', 'Assigned Roles') ?></th>
        <th>&nbsp;</th>
        <th><?php echo SrbacHelper::translate('srbac', 'Not Assigned Roles') ?></th>
    </tr>
    <tr>
        <td width="45%">
            <?php echo SrbacHtml::activeDropDownList($model, 'name[revoke]',
                SrbacHtml::listData(
                    $data['userAssignedRoles'], 'name', 'name'),
                array('size' => $this->module->listBoxNumberOfLines, 'multiple' => 'multiple', 'class' => 'dropdown')) ?>
        </td>
        <td width="10%" align="center">
            <?php
            $ajax =
                array(
                    'type' => 'POST',
                    'update' => '#roles',
                    'beforeSend' => 'function(){$("#loadMess").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#loadMess").removeClass("srbacLoading");}',
				);
            echo SrbacHtml::ajaxSubmitButton('<<', array('assign', 'assignRoles' => 1), $ajax, $data['assign']); ?>
            <?php
            $ajax =
                array(
                    'type' => 'POST',
                    'update' => '#roles',
                    'beforeSend' => 'function(){$("#loadMess").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#loadMess").removeClass("srbacLoading");}',
				);
            echo SrbacHtml::ajaxSubmitButton('>>', array('assign', 'revokeRoles' => 1), $ajax, $data['revoke']); ?>
        </td>
        <td width="45%">
            <?php echo SrbacHtml::activeDropDownList($model, 'name[assign]',
                SrbacHtml::listData(
                    $data['userNotAssignedRoles'], 'name', 'name'),
                array('size' => $this->module->listBoxNumberOfLines, 'multiple' => 'multiple', 'class' => 'dropdown')); ?>
        </td>
    </tr>
</table>
<div class="message" id="loadMess">
    <?php echo "&nbsp;" . $message ?>
</div>
