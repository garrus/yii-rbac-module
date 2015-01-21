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
            <?php echo SHtml::link(
                SHtml::image($this->module->getIconsPath() . '/manageAuth.png',
                    Helper::translate('srbac', 'Managing auth items'),
                    array('class' => 'icon',
                        'title' => Helper::translate('srbac', 'Managing auth items'),
                        'border' => 0
                    )
                ) . " " .
                ($this->module->iconText ?
                    Helper::translate('srbac', 'Managing auth items') :
                    ""),
                array('authitem/manage'))
            ?>
        </div>
        <div class="iconBox">
            <?php echo SHtml::link(
                SHtml::image($this->module->getIconsPath() . '/usersAssign.png',
                    Helper::translate('srbac', 'Assign to users'),
                    array('class' => 'icon',
                        'title' => Helper::translate('srbac', 'Assign to users'),
                        'border' => 0,
                    )
                ) . " " .
                ($this->module->iconText ?
                    Helper::translate('srbac', 'Assign to users') :
                    ""),
                array('authitem/assign'));?>
        </div>
        <div class="iconBox">
            <?php echo SHtml::link(
                SHtml::image($this->module->getIconsPath() . '/users.png',
                    Helper::translate('srbac', 'User\'s assignments'),
                    array('class' => 'icon',
                        'title' => Helper::translate('srbac', 'User\'s assignments'),
                        'border' => 0
                    )
                ) . " " .
                ($this->module->iconText ?
                    Helper::translate('srbac', 'User\'s assignments') :
                    ""),
                array('authitem/assignments'));?>
        </div>

		<div class="iconBox">
			<?php echo SHtml::link(
				SHtml::image($this->module->getIconsPath() . '/user.png',
					Helper::translate('srbac', 'User Management'),
					array('class' => 'icon',
						'title' => Helper::translate('srbac', 'User Management'),
						'border' => 0
					)
				) . " " .
				($this->module->iconText ?
					Helper::translate('srbac', 'User Management') :
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