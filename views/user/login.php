<?php
/**
 * @var UserController $this
 * @var SrbacLoginForm $model
 * @var CActiveForm $form
 * @var string $dynamicPassword
 * @var boolean $showRegisterLink
 * @var boolean $showDynamicPassword
 */
?>
<script type="text/javascript">
	if (window.top !== window) window.top.location.href = location.href;
</script>



<div class="row" style="
	margin: 100px auto;
	width: 500px;
	border-radius: 10px;
	border: 2px solid #333;
	box-shadow: 0 0 8px 2px gray;
	padding: 15px 20px;
	">

	<?php $this->renderFlash();?>

	<h2 class="text-center text-info">登录管理后台</h2>
	<hr>

	<?php $form = $this->beginWidget('CActiveForm', [
		'id' => 'srbac-login-form',
		'enableAjaxValidation' => false,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'text-danger field-desc',
		'htmlOptions' => [
			'class' => 'form-inline',
			'role' => 'form',
		],
	]);

	echo $form->errorSummary($model, '', null, ['class' => 'alert alert-danger']);?>

	<div class="form-group">
	<?php $loginType = 'name';
		$types = [
			'name',
			'stuff_no',
			'email',
			'mobile'
		];
		foreach ($types as $type) {
			if (!empty($model->{$type})) {
				$loginType = $type;
				break;
			}
		}
		echo '<label for="login_type" class="control-label">登录方式</label>';
		echo CHtml::dropDownList('login_type', $loginType, [
			'name' => '登录名',
			'stuff_no' => '工号',
			'email' => '邮箱',
			'mobile' => '手机号',
		], ['class' => 'form-control']);
	?>
	</div>

	<?php foreach ($types as $type):?>
	<div class="form-group login-name-row <?php if ($type != $loginType) echo 'hide';?>" role="login-by-<?php echo $type;?>">
		<?php echo $form->labelEx($model, $type, ['class' => 'control-label']);?>
		<?php echo $form->textField($model, $type, ['class' => 'form-control', 'disabled' => $type == $loginType ? '' : 'disabled']);?>
		<?php if ($type == 'mobile'): ?>
			<p class="field-desc text-info">只有当你的个人资料中填写了手机号才可使用手机号登录</p>
		<?php elseif ($type == 'stuff_no'): ?>
			<p class="field-desc text-info">只有当你的个人资料中填写了工号才可使用工号登录</p>
		<?php endif;?>
		<?php echo $form->error($model, $type);?>
	</div>
	<?php endforeach;?>

	<div class="form-group">
		<?php echo $form->labelEx($model, 'password', ['class' => 'control-label']);?>
		<?php echo $form->passwordField($model, 'password', ['class' => 'form-control']);?>
		<?php echo $form->error($model, 'password', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>

	<?php if ($showDynamicPassword): ?>
	<div class="form-group">
		<?php echo CHtml::label('动态密码', 'dynamic_password', ['class' => 'control-label']);?>
		<?php echo CHtml::textField('dynamic_password', $dynamicPassword, ['class' => 'form-control', 'autocomplete' => 'off']);?>
		<?php echo $form->error($model, 'dynamic_password', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
		<p class="field-desc" style="margin-top: 7px;">
			<?php echo CHtml::link('获取动态密码', '#', ['class' => 'btn btn-default btn-sm', 'id' => 'resent-dynamic-password']);?>
		</p>
	</div>
	<?php endif;?>

	<div class="form-group">
		<?php echo CHtml::submitButton('登录', ['class' => 'btn btn-primary']);?>
		<?php if (!empty($showRegisterLink)): ?>or <?php echo CHtml::link('注册', ['register']); endif;?>
	</div>

	<?php $this->endWidget($form);?>
</div>

<div class="row well" style="
margin: 100px auto;
width: 500px;
padding: 15px 20px;">
	<h4 style="color:red;">如何启用IP地址插件？</h4>
	<ol>
		<li>根据弹出的提示安装或升级Java 8运行环境（若机器上已安装Java 8，则这一步会跳过）</li>
		<li>打开 <i>控制面板->Java（32 位）</i> 选项（如果控制面板里的设置项是按类别显示的，则按此路径寻找：<i>控制面板->程序->Java（32 位）</i>）</li>
		<li>在弹出的Java控制面板中，选择“安全”选项卡，在“例外站点列表”中添加 <strong><?php echo Yii::app()->request->hostInfo;?></strong>，然后点击确定。</li>
		<li>重新启动chrome浏览器并打开此页面后，浏览器可能还会进一步阻拦插件的运行，此时需要点击地址栏里靠右的那个提示按钮，选择始终允许使用此站点上的插件即可。</li>
	</ol>

</div>


<script type="text/javascript">
	$(function(){
		var $form = $("#srbac-login-form");
		var sendDpInterval = 0;
		$form.delegate("#login_type", "change", function(e){
			var type = $(this).val();
			var $targetRow = $form.find(".login-name-row[role=login-by-" + type + "]");
			$targetRow.toggleClass("hide", false).find("input[type=text]").attr("disabled", false);
			$targetRow.siblings(".login-name-row").toggleClass("hide", true).find("input[type=text]").attr("disabled", "disabled");
		})<?php if ($showDynamicPassword):?>
			.delegate("#resent-dynamic-password", "click", function(e){
			var $link = $(this);
			if ($link.hasClass("disabled")) return false;

			if ($form.find(".login-name-row").not(".hide").find("input").val().length === 0 ||
				$form.find("input[type=password]").length === 0) {
				e.preventDefault();
				noty({"text": "请先填写用户名和密码", "type": "warning", "timeout": 2000, "layout": "topCenter"});
				return false;
			}

			$link.addClass("disabled");
			$link.data("ori-text", $link.text());
			$link.text("发送中……");
			clearInterval(sendDpInterval);

			$form.find("#dynamic_password").val("");
			var xhr = $.post($form.attr("action"), $form.serialize(), function(json){
				if (json.ret == 1) {
					$link.removeClass("disabled");
					$link.text($link.data("ori-text"));
					noty({"text": json.msg, "type": "error", "timeout": 7000, "layout": "topCenter"});
				} else {
					startCounting();
					noty({"text": json.msg, "type": "success", "timeout": 7000, "layout": "topCenter"});
				}
			}, "json").error(function(xhr, settings, error){
				$link.removeClass("disabled");
				$link.text($link.data("ori-text"));
				noty({"text": xhr.responseText, "type": "error", "timeout": 7000, "layout": "topCenter"});
			});

			function startCounting(){
				$link.data("ori-text", $link.text());
				$link.text("30 秒后可重新发送");

				var sec = 30;
				sendDpInterval = setInterval(function(){
					if (--sec) {
						$link.text(sec + " 秒后可重新发送");
					} else {
						clearInterval(sendDpInterval);
						$link.text("重新发送");
						$link.removeClass("disabled");
						xhr.abort();
					}
				}, 1000);
			}

			return false;
		})<?php endif;?>;

		<?php if ($showDynamicPassword):?>
		$form.on("submit", function(e){
			var $btn = $form.find("input[type=submit]");
			if ($btn.hasClass("disabled")) return false;

			var $dp = $form.find("#dynamic_password");
			if (!$dp.val()) {
				$btn.attr("disabled", true).addClass("disabled");
				$.post($form.attr("action"), $form.serialize(), function(json){
					$btn.attr("disabled", false).removeClass("disabled");
					if (json.ret == 1) {
						noty({"text": json.msg, "type": "error", "timeout": 7000, "layout": "topCenter"});
					} else {
						noty({"text": json.msg, "type": "success", "timeout": 7000, "layout": "topCenter"});
					}
				}, "json").error(function(xhr, settings, error){
					$btn.attr("disabled", false).removeClass("disabled");;
					noty({"text": xhr.responseText, "type": "error", "timeout": 7000, "layout": "topCenter"});
				});
				e.preventDefault();
			}
		});
		<?php endif;?>
	});
</script>