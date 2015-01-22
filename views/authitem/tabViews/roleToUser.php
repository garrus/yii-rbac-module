<?php
/**
 * roleToUser.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The tab view for assigning roles to users
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.tabViews
 * @since 1.0.0
 */
?>
<!-- USER -> ROLES -->
<div class="srbac">
    <?php echo SrbacHtml::beginForm(); ?>
    <?php echo SrbacHtml::errorSummary($model); ?>
    <table width="100%">
        <tr>
            <th colspan="2"><?php echo SrbacHelper::translate('srbac', 'Assign Roles to Users') ?></th>
        </tr>
        <tr>
            <th width="50%">
                <?php echo SrbacHtml::label(SrbacHelper::translate('srbac', "User"), 'user'); ?></th>
            <td width="50%" rowspan="2">
                <div id="roles">
                    <?php
                    $this->renderPartial(
                        'tabViews/userAjax',
                        array('model' => $model, 'userid' => $userid, 'data' => $data, 'message' => $message)
                    );
                    ?>
                </div>
            </td>
        </tr>
        <tr valign="top">
            <td><?php
                $criteria = new CDbCriteria();
                $criteria->order = $this->module->username;
                echo SrbacHtml::activeDropDownList($this->module->getUserModel(), $this->module->userid,
                    SrbacHtml::listData($this->module->getUserModel()->findAll($criteria), $this->module->userid, $this->module->username),
                    array('size' => $this->module->listBoxNumberOfLines, 'class' => 'dropdown', 'ajax' => array(
                        'type' => 'POST',
                        'url' => array('getRoles'),
                        'update' => '#roles',
                        'beforeSend' => 'function(){$("#loadMess").addClass("srbacLoading");}',
                        'complete' => 'function(){$("#loadMess").removeClass("srbacLoading");}'
                    ),
                    )); ?>
            </td>
        </tr>
    </table>
    <br/>
    <?php echo SrbacHtml::endForm(); ?>
</div>
