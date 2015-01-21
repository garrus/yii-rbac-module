<?php
/**
 * manage.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * The auth items main administration page
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem.manage
 * @since 1.0.0
 *
 * @var boolean $full
 */
?>
<?php if ($this->module->getMessage() != "") : ?>
     <div id="srbacError">
          <?php echo $this->module->getMessage(); ?>
     </div>
<?php endif; ?>
<?php if (!$full) :
     if ($this->module->getShowHeader()) :
          $this->renderPartial($this->module->header);
     endif;
     $this->renderPartial("frontpage");
     ?>
     <div id="wizardButton" style="text-align:left" class="controlPanel marginBottom">
          <?php
		  echo SHtml::ajaxLink(
               SHtml::image($this->module->getIconsPath() . '/admin.png',
                    Helper::translate('srbac', 'Manage AuthItem'),
                    array('class' => 'icon',
                         'title' => Helper::translate('srbac', 'Manage AuthItem'),
                         'border' => 0
                    )
               ) . " " .
               ($this->module->iconText ? Helper::translate('srbac', 'Manage AuthItem') : ""),
               array('manage', 'full' => true),
               array(
                    'type' => 'POST',
                    'update' => '#wizard',
                    'beforeSend' => 'function(){$("#wizard").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#wizard").removeClass("srbacLoading");}',
               ),
               array(
                    'name' => 'buttonManage',
                    'onclick' => "$(this).css('font-weight', 'bold');$(this).siblings().css('font-weight', 'normal');",
               )
          );

		  echo SHtml::ajaxLink(
               SHtml::image($this->module->getIconsPath() . '/wizard.png',
                    Helper::translate('srbac', 'Autocreate Auth Items'),
                    array('class' => 'icon',
                         'title' => Helper::translate('srbac', 'Autocreate Auth Items'),
                         'border' => 0
                    )
               ) . " " .
               ($this->module->iconText ?
                    Helper::translate('srbac', 'Autocreate Auth Items') :
                    ""),
               array('auto'),
               array(
                    'type' => 'POST',
                    'update' => '#wizard',
                    'beforeSend' => 'function(){$("#wizard").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#wizard").removeClass("srbacLoading");}',
               ),
               array(
                    'name' => 'buttonAuto',
                    'onclick' => "$(this).css('font-weight', 'bold');$(this).siblings().css('font-weight', 'normal');",
               )
          );

		  echo SHtml::ajaxLink(
               SHtml::image($this->module->getIconsPath() . '/allow.png',
                    Helper::translate('srbac', 'Edit always allowed list'),
                    array('class' => 'icon',
                         'title' => Helper::translate('srbac', 'Edit always allowed list'),
                         'border' => 0
                    )
               ) . " " .
               ($this->module->iconText ?
                    Helper::translate('srbac', 'Edit always allowed list') :
                    ""),
               array('editAllowed'),
               array(
                    'type' => 'POST',
                    'update' => '#wizard',
                    'beforeSend' => 'function(){$("#wizard").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#wizard").removeClass("srbacLoading");}',
               ),
               array(
                    'name' => 'buttonAllowed',
                    'onclick' => "$(this).css('font-weight', 'bold');$(this).siblings().css('font-weight', 'normal');",
               )
          );

		  echo SHtml::ajaxLink(
               SHtml::image($this->module->getIconsPath() . '/eraser.png',
                    Helper::translate('srbac', 'Clear obsolete authItems'),
                    array('class' => 'icon',
                         'title' => Helper::translate('srbac', 'Clear obsolete authItems'),
                         'border' => 0
                    )
               ) . " " .
               ($this->module->iconText ?
                    Helper::translate('srbac', 'Clear obsolete authItems') :
                    ""),
               array('clearObsolete'),
               array(
                    'type' => 'POST',
                    'update' => '#wizard',
                    'beforeSend' => 'function(){$("#wizard").addClass("srbacLoading");}',
                    'complete' => 'function(){$("#wizard").removeClass("srbacLoading");}',
               ),
               array(
                    'name' => 'buttonClear',
                    'onclick' => "$(this).css('font-weight', 'bold');$(this).siblings().css('font-weight', 'normal');",
               )
          );
          ?>
     </div>
     <br/>
<div id="wizard" class="row">
	<?php endif; ?>
	<div class="col-md-6">
		<h3 class="text-center" style="margin-top: 0;"><?php echo Helper::translate("srbac", "Auth items"); ?></h3>
		<div id="list">
			<?php echo $this->renderPartial('manage/list', array(
				'dataProvider' => $dataProvider,
			)); ?>
		</div>
	</div>

	<div class="col-md-6">
		<h3 class="text-center" style="margin-top: 0;"><?php echo Helper::translate('srbac', 'Actions') ?></h3>
		<div id="preview"></div>
	</div>
<?php if (!$full) :?>
</div>

     <?php if ($this->module->getShowFooter()) :
          $this->renderPartial($this->module->footer);
     endif;
endif;
