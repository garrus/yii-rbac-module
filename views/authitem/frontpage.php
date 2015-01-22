<?php
/**
 * frontpage.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * Srbac main administration page
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem
 * @since 1.0.2
 */
?>
<div class="marginBottom">
    <div class="iconSet">
        <div class="iconBox">
            <?php echo SrbacHtml::link(
                SrbacHtml::image($this->module->getIconsPath() . '/manageAuth.png',
                    SrbacHelper::translate('srbac', 'Managing auth items'),
                    array('class' => 'icon',
                        'title' => SrbacHelper::translate('srbac', 'Managing auth items'),
                        'border' => 0
                    )
                ) . " " .
                ($this->module->iconText ?
                    SrbacHelper::translate('srbac', 'Managing auth items') :
                    ""),
                array('authitem/manage'))
            ?>
        </div>
        <div class="iconBox">
            <?php echo SrbacHtml::link(
                SrbacHtml::image($this->module->getIconsPath() . '/usersAssign.png',
                    SrbacHelper::translate('srbac', 'Assign to users'),
                    array('class' => 'icon',
                        'title' => SrbacHelper::translate('srbac', 'Assign to users'),
                        'border' => 0,
                    )
                ) . " " .
                ($this->module->iconText ?
                    SrbacHelper::translate('srbac', 'Assign to users') :
                    ""),
                array('authitem/assign'));?>
        </div>
        <div class="iconBox">
            <?php echo SrbacHtml::link(
                SrbacHtml::image($this->module->getIconsPath() . '/users.png',
                    SrbacHelper::translate('srbac', 'User\'s assignments'),
                    array('class' => 'icon',
                        'title' => SrbacHelper::translate('srbac', 'User\'s assignments'),
                        'border' => 0
                    )
                ) . " " .
                ($this->module->iconText ?
                    SrbacHelper::translate('srbac', 'User\'s assignments') :
                    ""),
                array('authitem/assignments'));?>
        </div>

		<div class="iconBox">
			<?php echo SrbacHtml::link(
				SrbacHtml::image($this->module->getIconsPath() . '/user.png',
					SrbacHelper::translate('srbac', 'User Management'),
					array('class' => 'icon',
						'title' => SrbacHelper::translate('srbac', 'User Management'),
						'border' => 0
					)
				) . " " .
				($this->module->iconText ?
					SrbacHelper::translate('srbac', 'User Management') :
					""),
				array('user/index'));?>
		</div>
		<div class="iconBox pull-right">
		<?php echo CHtml::link('注销 '.Yii::app()->user->name, ['/srbac/user/logout'], [
			'class' => 'pull-right',
			'style' => 'line-height: 32px; padding: 0 10px; background-color: lightsteelblue;'
		]);?>
		</div>
    </div>
    <div class="reset"></div>
</div>