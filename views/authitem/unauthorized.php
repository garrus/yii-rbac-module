<?php
/**
 * unauthorized.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * Default page shown when a not authorized user tries to access a page
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem
 * @since 1.0.2
 *
 * @var int $code
 * @var string $title
 * @var string $message
 */
?>
<?php if (Yii::app()->request->isAjaxRequest):?>
	<h2 style="color: #a94442;"><?php echo $code;?>: <?php echo $title;?></h2>
	<p style="color: #8a6d3b;"><?php echo $message;?></p>
<?php else: ?>
<div class="row">
	<div class="col-md-6 col-md-push-3">
		<div unselectable="on" style="height: 280px; position: relative; top: 30px; font-size: 150pt; font-weight: bold; color: #efefef; font-family: Verdana, 'Times New Roman', sans-serif;">
			<?php echo $code;?>
		</div>

		<h2 class="text-danger"><?php echo $title;?></h2>
		<p class="text-warning"><?php echo $message; ?></p>

		<ul style="font-size: 0.9em; padding-left: 1.5em; margin-top: 2em; line-height: 20px; list-style-type: square;">
			<li><?php echo CHtml::link('返回上一页', 'javascript:history.go(-1)');?></li>
			<li><?php echo CHtml::link('回到首页', Yii::app()->getModule('srbac')->backendHomeUrl);?></li>
			<li><?php echo CHtml::link('注销登录 ('. Yii::app()->user->name. ')', ['/srbac/user/logout']);?></li>
		</ul>
	</div>
</div>
<?php endif;?>
