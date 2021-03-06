<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language;?>">
<?php $assetsUrl = Yii::app()->getModule('srbac')->getAssetsUrl();?>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<?php if (!$this->checkIpAndMac()):?>
		<applet width="0px" height="0px" code="MacAddress.class" archive="<?php echo $assetsUrl; ?>/MacAddress.jar">
			<param name="callback" value="<?php
			$recAddrUrl = $this->createAbsoluteUrl('/srbac/user/recAddr');
			if (strpos($recAddrUrl, '?') === false) {
				$recAddrUrl .= '?_n_=1';
			}
			echo $recAddrUrl;
			?>">
		</applet>
	<?php endif;?>

	<meta content="ie=7" http-equiv="x-ua-compatible">

	<meta name="GENERATOR" content="MSHTML 8.00.6001.19154">

	<!-- 新 Bootstrap 核心 CSS 文件 -->
	<link rel="stylesheet" href="<?php echo $assetsUrl;?>/css/bootstrap.min.css">
	<!-- 可选的Bootstrap主题文件（一般不用引入） -->
	<link rel="stylesheet" href="<?php echo $assetsUrl;?>/css/bootstrap-theme.min.css">

	<link rel="stylesheet" type="text/css" href="<?php echo $assetsUrl;?>/css/admin.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $assetsUrl;?>/css/srbac.css">

	<script type="text/javascript" src="<?php echo $assetsUrl;?>/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $assetsUrl;?>/js/jquery-migrate-1.2.1.js"></script>

	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script type="text/javascript" src="<?php echo $assetsUrl;?>/js/bootstrap.min.js"></script>
	<!--
	<script type=text/javascript src="/js/jquery.validate.js"></SCRIPT>
	<script type=text/javascript src="/js/jquery.metadata.js"></SCRIPT>
	<script type=text/javascript src="/js/jquery.validate.message.cn.js"></SCRIPT>
	<script type=text/javascript src="/js/common.js?2541"></SCRIPT>
	<script type=text/javascript src="/js/admincp.js"></SCRIPT>
	-->
	<script type="text/javascript" src="<?php echo $assetsUrl;?>/js/jquery.noty.packaged.min.js"></SCRIPT>

	<title><?php echo CHtml::encode($this->pageTitle ?: Yii::app()->name, ' - 管理后台');?> </title>
</head>

<body>
<!-- 页面操作提示信息使用,必须在body下 -->
<div id="feedback_info" style="display:none;position:absolute;z-index:9999;"></div>
<div id="cpcontainer" class="container-fluid" style="margin-bottom: 20px;">
	<?php echo $content;?>
</div>

<script type="text/JavaScript">
	parent.document.title = <?php echo json_encode($this->pageTitle);?>;
	function saveform(form){
		$("#"+form).submit();
	}
</script>

</body>
</html>
