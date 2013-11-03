<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>OAuth-Configuration</title>
		<link href="/pimcore/static/js/lib/ext/resources/css/ext-all.css" media="screen" rel="Stylesheet" type="text/css"/>
		<link href="/plugins/OAuth/static/css/admin.css" media="screen" rel="Stylesheet" type="text/css"/>
		<?php
		$conf = Zend_Registry::get("pimcore_config_system");

		$themeUrl = "/pimcore/static/js/lib/ext/resources/css/xtheme-blue.css";
		if($conf->general->theme) {
			$themeUrl = $conf->general->theme;
		}
		?>
		<link href="<?php print $themeUrl ?>" media="screen" rel="Stylesheet" type="text/css"/>
		<link href="/pimcore/static/css/admin.css" media="screen" rel="Stylesheet" type="text/css" />
		
		<script type="text/javascript" src="/pimcore/static/js/lib/jquery-1.4.2.min.js"></script>
		<script type="text/javascript">
		$(function() {

			function getConsumer(element) {
				var form = $('form', element).first().attr('id');
				var consumer = $('#'+form+'-consumer').val();
				return consumer;
			}

			$('input.connect').click(function(event) {
				event.preventDefault();

				var consumer = getConsumer($(this).parent());
				
				var _this = this;
				$.ajax({
					url : '/plugin/OAuth/admin/has-keys',
					data : {consumer : consumer},
					dataType : 'json',
					success : function(data) {
						if(data.hasKeys) {
							$($(_this).parent()).slideUp(700);
							window.setTimeout(function() {document.location.href = '/plugin/OAuth/admin/get-access/consumer/'+consumer;}, 800);
						} else {
							$(_this).parent().prepend('<div class="error">Add your credentials first.</div>');
						}
					}
				});
			});
			
			$('input.disconnect').click(function(event) {
				event.preventDefault();
				var consumer = getConsumer($(this).parent());
				var _this = this;
				$.ajax({
					url : '/plugin/OAuth/admin/disconnect/consumer/'+consumer,
					dataType : 'json',
					success : function(data) {
						if(data.success) {
							$($(_this).parent()).slideUp(800);
							window.setTimeout(function() { document.location.href = '/plugin/OAuth/admin/config' ; }, 800);
						} else {
							$(_this).parent().prepend('<div class="error">'+data.error+'</div>');
						}
					}
				});
			});

			$('.consumer form').submit(function(event) {
				event.preventDefault();		
				var _this = this;
				var id = $(this).attr('id');
				$.ajax({
					url : '/plugin/OAuth/admin/add-keys/',
					dataType : 'json',
					data : {
						consumer : id,
						consumerKey : $('#'+id+'-consumerKey').val(),
						consumerSecret : $('#'+id+'-consumerSecret').val()
					},
					type : 'post',
					success : function(data) {						
						if(data.success) {
							$('#'+id).parent().parent().children('.form-credentials-message').html('<div>'+data.success+'</div>');
							$(_this).find('.error').removeClass('error');
						} else if(data.errors) {
							var length = data.errors.length;
							$('#'+id).parent().parent().children('.form-credentials-message').html('<div class="error">Please check your entered keys.</div>');
							for(var i = 0; i < length; i++) {																
								$('#'+id+'-'+data.errors[i]).addClass('error');
							}
						}
					}
				});
			});		
		});
		</script>
	</head>
	<body>
		<div id="main">
			<div class="left block">
				<img src="/plugins/OAuth/static/img/oauth.png" alt="oauth-logo" />
			</div>
			<div class="left block-title">
				<h1 style="margin-top:30px;margin-left: 10px;">OAuth Configuration</h1>
			</div>
			<div class="clear"></div>
			<br />
			<br />			
			<?php /* @var $consumer OAuth_Consumer */ ?>
			<?php foreach($this->consumers as $consumer) : ?>
			<div class="consumer">
				<h2><?php print $consumer->getName(); ?></h2>
				<div class="form-credentials-message">&nbsp;</div>
				<div><?php print $consumer->getForm(); ?></div>

				<?php if($consumer->getHasAccess()) : ?>
				<input class="disconnect" type="button" value="DISCONNECT" name="disconnect" />
				<?php else : ?>
				<input class="connect" type="button" value="CONNECT <?php print $consumer->getName() ?>" name="connect" />
				<?php endif; ?>

			</div>
			<?php endforeach; ?>
			<div class="clear"></div>
		</div>
	</body>
</html>
