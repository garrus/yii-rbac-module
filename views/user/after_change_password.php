<?php
/** @var string $url */
?>
<script type="text/javascript">
	setTimeout(function(){
		(window.top===window?location:window.top.location).href = <?php echo json_encode($url);?>;
	}, 3000);
</script>

<div class="row" style="
	margin: 150px auto;
	width: 400px;
	border-radius: 10px;
	border: 2px solid #333;
	box-shadow: 0 0 8px 2px gray;
	padding: 15px 20px;
	">
	<h2 class="text-success">密码修改成功！</h2>
	<p class="text-warning">将在 3 秒后跳转到<a href="<?php echo $url;?>">登录界面</a>，请使用新密码重新登录</p>
</div>