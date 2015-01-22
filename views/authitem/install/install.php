<?php
/**
 * Install.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The install view.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.install
 * @since 1.0.0
 */
?>
<?php
$script = "
jQuery('#help_handle').click(function(){
$('#help').toggle('1000');
});";

Yii::app()->clientScript->registerScript("cb", $script, CClientScript::POS_READY);
?>
<?php $error = false;
$disabled = array(); ?>
<h3><?php echo SrbacHelper::translate('srbac', 'Install Srbac') ?></h3>
<div class="srbac">
    <div id="help_handle" class="iconBox" style="float:right">
        <?php echo
            SrbacHtml::image($this->module->getIconsPath() . '/help.png',
                SrbacHelper::translate('srbac', 'Help'),
                array('class' => 'icon',
                    'title' => SrbacHelper::translate('srbac', 'Help'),
                    'border' => 0
                )) . " " .
            ($this->module->iconText ?
                SrbacHelper::translate('srbac', 'Help') :
                "");
        ?>
    </div>
    <br/>
    <?php echo SrbacHtml::beginForm(); ?>
    <div id="help" style="display:none">
        <?php $this->renderPartial(Yii::app()->findLocalizedFile('install/installText')) ?>
    </div>
    <div>
        <?php echo SrbacHelper::translate('srbac', 'Your Database, AuthManager and srbac settings:'); ?>
        <table class="srbacDataGrid" width="'100%">
            <?php if (Yii::app()->authManager instanceof CDbAuthManager) { ?>

                <?php try { ?>
                    <tr>
                        <th colspan="2"><?php echo SrbacHelper::translate('srbac', 'Database'); ?></th>
                    <tr>
                        <td><?php echo SrbacHelper::translate('srbac', 'Driver'); ?></td>
                        <td><?php echo Yii::app()->authManager->db->getDriverName() ?></td>
                    </tr>
                    <tr>
                        <td><?php echo SrbacHelper::translate('srbac', 'Connection'); ?></td>
                        <td><?php echo Yii::app()->authManager->db->connectionString ?></td>
                    </tr>
                <?php } catch (CException $e) { ?>
                    <tr>
                        <td colspan="2">
                            <div class="error">
                                <?php echo SrbacHelper::translate('srbac', 'Database is not Configured'); ?>
                                <?php echo "<pre>" . $e->getMessage() . "</pre>"; ?>
                            </div>
                        </td>
                    </tr>
                    <?php $error = true; ?>
                <?php } ?>
                <?php try { ?>
                    <tr>
                        <th colspan="2"><?php echo SrbacHelper::translate('srbac', 'AuthManager'); ?></th>
                    <tr>
                        <td><?php echo SrbacHelper::translate('srbac', 'Item Table'); ?></td>
                        <td><?php echo Yii::app()->authManager->itemTable ?></td>
                    </tr>
                    <tr>
                        <td><?php echo SrbacHelper::translate('srbac', 'Assignment Table'); ?></td>
                        <td><?php echo Yii::app()->authManager->assignmentTable ?></td>
                    </tr>
                    <tr>
                        <td><?php echo SrbacHelper::translate('srbac', 'Item child table'); ?></td>
                        <td><?php echo Yii::app()->authManager->itemChildTable ?></td>
                    </tr>
                <?php } catch (CException $e) { ?>
                    <tr>
                        <td colspan="2">
                            <div class="error">
                                <?php echo SrbacHelper::translate('srbac', 'AuthManager is not Configured'); ?>
                                <?php echo "<pre>" . $e->getMessage() . "</pre>"; ?>
                            </div>
                        </td>
                    </tr>
                    <?php $error = true; ?>
                <?php } ?>
            <?php } ?>
            <?php try { ?>
                <tr>
                    <th colspan="2"><?php echo SrbacHelper::translate('srbac', 'srbac'); ?></th>
                </tr>
                <?php foreach ($this->module->getAttributes() as $key => $value) { ?>
                    <?php $check = SrbacHelper::checkInstall($key, $value); ?>
                    <?php echo $check[0]; ?>
                    <?php if ($check[1] == SrbacHelper::ERROR) $error = true; ?>
                <?php } ?>
            <?php } catch (CException $e) { ?>
                <tr>
                    <td colspan="2">
                        <div class="error">
                            <?php echo SrbacHelper::translate('srbac', 'srbac is not Configured'); ?>
                            <?php echo "<pre>" . $e->getMessage() . "</pre>"; ?>
                        </div>
                    </td>
                </tr>
                <?php $error = true; ?>
            <?php } ?>
            <tr>
                <th colspan="2">Yii</th>
            </tr>
            <tr>
                <td>
                    <?php echo SrbacHelper::translate("srbac", "Yii version") . " :"; ?>
                </td>
                <?php if (SrbacHelper::checkYiiVersion(SrbacHelper::findModule("srbac")->getSupportedYiiVersion())) { ?>
                    <td><?php echo Yii::getVersion() ?></td>
                <?php } else { ?>
                    <td style="color:red;font-weight:bold"><?php echo Yii::getVersion() .
                            "  <br /> " .
                            SrbacHelper::translate("srbac", "Wrong Yii version, lower required version is") . " " . SrbacHelper::findModule("srbac")->getSupportedYiiVersion(); ?></td>
                    <?php
                    $error = true;
                } ?>
            </tr>
        </table>
    </div>
    <div>
        <?php if ($error) { ?>
            <div>
                <?php echo SrbacHelper::translate('srbac', 'There is an error in your configuration') ?>
                <?php $disabled = array('disabled' => true) ?>
            </div>
        <?php } ?>
        <?php echo SrbacHtml::hiddenField("action", "Install"); ?>
        <?php echo SrbacHtml::checkBox("demo", false, $disabled);
        echo SrbacHelper::translate('srbac', 'Create demo authItems?')
        ?><br/>
        <?php echo SrbacHtml::submitButton(SrbacHelper::translate('srbac', 'Install'), $disabled); ?>
    </div>

    <?php echo SrbacHtml::endForm(); ?>
</div>
