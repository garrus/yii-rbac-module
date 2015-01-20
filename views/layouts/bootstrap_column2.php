<?php $this->beginContent('/layouts/bootstrap');?>

<script type="text/javascript">
	$(function(){
		$(".toggle-dropdown-nav").bind("click", function(){
			var $this = $(this);
			$this.siblings(".dropdown-nav").slideToggle("fast");
		});
	});
</script>
<div class="row">
	<div class="col-md-2">
		<div class="affix">
		<?php $this->widget('zii.widgets.CMenu', array(
			'items' => $this->menu,
			'htmlOptions' => array(
				'class' => 'nav nav-pills nav-stacked',
			)
		));?>
		</div>
	</div>
	<div class="col-md-10">
		<?php echo $content;?>
	</div>
</div>
<?php $this->endContent();
