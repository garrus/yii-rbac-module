<?php
/**
 * @var UserController $this
 * @var SrbacLoginForm $model
 * @var CActiveForm $form
 * @var string $dynamicPassword
 */
?>
<script type="text/javascript">
	if (window.top !== window) window.top.location.href = location.href;
</script>
<div class="row" style="
	margin: 150px auto;
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
		$types = ['name', 'stuff_no', 'email', 'mobile'];
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
		<?php echo $form->error($model, $type);?>
	</div>
	<?php endforeach;?>

	<div class="form-group">
		<?php echo $form->labelEx($model, 'password', ['class' => 'control-label']);?>
		<?php echo $form->passwordField($model, 'password', ['class' => 'form-control']);?>
		<?php echo $form->error($model, 'password', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
	</div>

	<div class="form-group">
		<?php echo CHtml::label('动态密码', 'dynamic_password', ['class' => 'control-label']);?>
		<?php echo CHtml::textField('dynamic_password', $dynamicPassword, ['class' => 'form-control', 'autocomplete' => 'off']);?>
		<?php echo $form->error($model, 'dynamic_password', ['errorCssClass' => 'has-error', 'successCssClass' => 'has-success']);?>
		<p class="field-desc" style="margin-top: 7px;">
			<?php echo CHtml::link('获取动态密码', '#', ['class' => 'btn btn-default btn-sm', 'id' => 'resent-dynamic-password']);?>
		</p>
	</div>

	<div class="form-group">

		<?php echo CHtml::submitButton('登录', ['class' => 'btn btn-primary']);?>
		or <?php echo CHtml::link('注册', ['register']);?>
	</div>

	<?php $this->endWidget($form);?>
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
		}).delegate("#resent-dynamic-password", "click", function(e){
			var $link = $(this);
			if ($link.hasClass("disabled")) return false;

			if ($form.find("input").eq(0).val().length==0 || $form.find("input").eq(1).length==0) {
				noty({"text": "请先填写用户名和密码", "type": "warning", "timeout": 2000, "layout": "topCenter"});
				return false;
			}

			$link.addClass("disabled");
			$link.text("发送中……");
			clearInterval(sendDpInterval);

			$form.find("#dynamic_password").val("");
			var xhr = $.post($form.attr("action"), $form.serialize(), function(json){
				if (json.ret == 1) {
					noty({"text": json.msg, "type": "error", "timeout": 7000, "layout": "topCenter"});
					$link.removeClass("disabled");
				} else {
					noty({"text": json.msg, "type": "success", "timeout": 7000, "layout": "topCenter"});
					startCounting();
				}
			}, "json").error(function(xhr, settings, error){
				$link.removeClass("disabled");
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
		});

		$form.on("submit", function(e){
			var $btn = $form.find("input[type=submit]");
			if ($btn.hasClass("disabled")) return false;

			var $dp = $form.find("#dynamic_password");
			if (!$dp.val()) {
				$btn.attr("disabled", true).addClass("disabled");
				$.post($form.attr("action"), $form.serialize(), function(json){
					if (json.ret == 1) {
						noty({"text": json.msg, "type": "error", "timeout": 7000, "layout": "topCenter"});
					} else {
						noty({"text": json.msg, "type": "success", "timeout": 7000, "layout": "topCenter"});
					}
					$btn.attr("disabled", false).removeClass("disabled");
				}, "json").error(function(xhr, settings, error){
					$btn.attr("disabled", false).removeClass("disabled");;
					noty({"text": xhr.responseText, "type": "error", "timeout": 7000, "layout": "topCenter"});
				});
				e.preventDefault();
			}
		});
	});
</script>