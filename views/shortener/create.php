<form action="" method="post" class="urlshortener-form">
	<?php echo Form::label("url", "URL to shorten"); ?>
	<br />
	<?php echo Form::input("url", 'http://', array("class"=>"text")); ?>
	<?php echo Form::submit("submit", "Shorten", array("class"=>"submit")); ?>
	<br class="clear" />
</form>
<?php if (isset($error)): ?>
	<br />
	<div class="url">
		Could not generate short code. Please enter a valid URL and try again.
	</div>
<?php endif; ?>
<?php if (isset($url)): ?>
	<br />
	<div class="url">
		<a href="<?php echo $url;?>"><?php echo $url;?></a>
	</div>
<?php endif; ?>