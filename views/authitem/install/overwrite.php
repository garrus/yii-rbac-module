<?php
/**
 * overwrite.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The ovewrite installation warning view.
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.install
 * @since 1.0.0
 */
?>
<h3><?php echo SrbacHelper::translate('srbac', 'Install Srbac') ?></h3>
<div class="srbac">
    <?php echo SrbacHtml::beginForm(); ?>
    <div>
        <?php echo SrbacHelper::translate('srbac', 'Srbac is already Installed.<br />Overwrite it?<br />'); ?>
    </div>
    <div>
        <?php echo SrbacHtml::hiddenField("action", "Overwrite"); ?>
        <?php echo SrbacHtml::hiddenField("demo", $demo); ?>
        <?php echo SrbacHtml::submitButton(SrbacHelper::translate('srbac', 'Overwrite')); ?>
    </div>
    <?php echo SrbacHtml::endForm(); ?>
</div>
