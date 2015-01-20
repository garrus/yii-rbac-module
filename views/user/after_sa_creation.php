<?php
/**
 * @var SrbacUser $user
 * @var string $password
 */
?>
<div class="row" style="
	margin: 150px auto;
	width: 400px;
	border-radius: 10px;
	border: 2px solid #333;
	box-shadow: 0 0 8px 2px gray;
	padding: 15px 20px;
	">

	<h2 class="text-center text-info">管理员帐号已创建</h2>
	<hr>
	<p>登录名：<?php echo $user->name;?></p>
	<p>密码： <?php echo $password;?></p>
	<?php echo CHtml::link('修改密码', ['changePassword']);?>
</div>