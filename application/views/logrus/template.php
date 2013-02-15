<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo (isset($title)) ? $title : ''; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/css/bootstrap.css" rel="stylesheet">
	<style type="text/css">
		body {
			padding-top: 20px;
			padding-bottom: 40px;
		}

	</style>

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>

<body>

<div class="container">

	<div class="row">
		<?php echo (isset($message)) ? $message : ''; ?>
	</div>
	<hr>
	<div class="footer">
		<p>&copy; <?php echo date('Y'); ?></p>
	</div>

</div> <!-- /container -->

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.2.2/bootstrap.min.js"></script>
<?php
	if (isset($scripts))
	{
		$template = '<script src="%s"></script>' . "\r\n";
		if (is_array($scripts))
		{
			foreach ($scripts as $script)
			{
				printf($template, $script);
			}
		}
		else
		{
			printf($template, $scripts);
		}

	}
?>
<script>
	$(document).ready(function () {
		$(".alert").alert();
<?php
	if (isset($ready_script))
	{
		$template = '%s' . "\r\n";
		if (is_array($ready_script))
		{
			foreach ($ready_script as $script)
			{
				printf($template, $script);
			}
		}
		else
		{
			printf($template, $ready_script);
		}
	}
?>
	});
</script>
</body>
</html>
