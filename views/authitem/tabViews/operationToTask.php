<?php
/**
 * operationToTask.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The tab view for assigning operations to tasks
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.tabViews
 * @since 1.0.0
 */
?>
<?php
$criteria = new CDbCriteria();
$criteria->condition = "type=1";
$criteria->order = "name";
?>
    <!-- TASKS -> OPERATIONS -->
    <div class="srbac">
        <?php echo SrbacHtml::beginForm(); ?>
        <?php echo SrbacHtml::errorSummary($model); ?>
        <table width="100%">
            <tr>
                <th colspan="2"><?php echo SrbacHelper::translate('srbac', 'Assign Operations to Tasks') ?></th>
            </tr>
            <tr>
                <th width="50%">
                    <?php echo SrbacHtml::label(SrbacHelper::translate('srbac', "Task"), 'task'); ?></th>
                <td width="50%" rowspan="2">
                    <div id="operations">
                        <?php
                        $this->renderPartial('tabViews/taskAjax',
                            array('model' => $model, 'userid' => $userid, 'data' => $data, 'message' => $message));
                        ?>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <td><?php echo SrbacHtml::activeDropDownList(Assignments::model(), 'itemname',
                        SrbacHtml::listData(AuthItem::model()->findAll($criteria), 'name', 'name'),
                        array('size' => $this->module->listBoxNumberOfLines, 'class' => 'dropdown', 'ajax' => array(
                            'type' => 'POST',
                            'url' => array('getOpers'),
                            'update' => '#operations',
                            'beforeSend' => 'function(){$("#loadMessTask").addClass("srbacLoading");}',
                            'complete' => 'function(){$("#loadMessTask").removeClass("srbacLoading");}'
                        ),
                        )); ?>
                    <div>
                        <?php echo SrbacHelper::translate("srbac", "Clever Assigning"); ?>:
                        <?php echo SrbacHtml::checkBox("clever", Yii::app()->getGlobalState("cleverAssigning")); ?>
                    </div>
                </td>
            </tr>
        </table>
        <br/>

        <div class="message" id="loadMessTask">
            <?php echo $message ?>
        </div>
        <?php echo SrbacHtml::endForm(); ?>
    </div>
<?php
$urlManager = Yii::app()->getUrlManager();
$parent = $this->module->parentModule ? $this->module->parentModule->name . "/" : "";
$url = $urlManager->createUrl($parent . "srbac/authitem/getCleverOpers");
?>
<?php
$script = "jQuery('#clever').click(function(){
  var checked = $('#clever').attr('checked');
  var name = $('#Assignments_itemname').attr('value');
  $.ajax({
   type: 'POST',
   url: '{$url}',
   data: 'checked='+checked+'&name='+name,
   beforeSend: function(){
     $('#loadMessTask').addClass('srbacLoading');
   },
   complete: function(){
     $('#loadMessTask').removeClass('srbacLoading');
   },
  success: function(data){
     $('#operations').html(data);
   }
 });

});";

Yii::app()->clientScript->registerScript("cb", $script, CClientScript::POS_READY);
?>