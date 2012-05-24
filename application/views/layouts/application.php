<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv attribute="X-XRDS-Location" content="http://bookmarks.ariven.com/auth/yadis">

	<title><?php if (isset($meta['title']))
	{
		echo $meta['title'];
	} ?></title>
	<?php
	if (isset($meta['description']))
	{
		printf('<meta name="description" content="%s">' . "\n", $meta['description']);
	}

	?>
	<?php
	if (isset($meta['keywords']))
	{
		printf('<meta name="keywords" content="%s">' . "\n", $meta['keywords']);
	}

	?>
	<meta name="author" content="<?php if (isset($meta['author']))
	{
		echo $meta['author'];
	} ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- CSS ================================================== -->
	<link rel="stylesheet" href="/assets/css/bootstrap.css">
	<link rel="stylesheet" href="/assets/css/bootstrap-responsive.css">
	<link rel="stylesheet" href="/assets/css/style.css">

	<?php
	if (isset($stylesheets))
	{
		$format = '<link rel="stylesheet" href="/assets/css/%s">' . "\n";
		if (is_array($stylesheets))
		{
			foreach ($stylesheets as $sheet)
			{
				echo sprintf($format, $sheet);
			}
		} else
		{
			echo sprintf($format, $stylesheets);
		}
	}
	?>

</head>
<body>
<!-- menu -->
<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="/">ErisMarks</a>
			<ul class="nav">
				<li><a href="/">home</a></li>
				<?php if (isset($yield_main_menu))
			{
				echo $yield_main_menu;
			} ?>
			</ul>
			<?php
			if (isset($member_menu))
			{
				echo $member_menu;
			}
			?>
		</div>
	</div>
</div>

<div class="container">
	<!-- menu end -->
	<div id="header" class="row">
		<?php echo isset($yield_header) ? $yield_header : ''; ?>
	</div>
	<!-- content -->
	<div id="content" class="row">
		<div class="span9">
			<?php echo $yield; ?>
		</div>
	</div>
	<!-- content end -->

	<!-- footer -->
	<div id="footer" class="row">
		<?php echo isset($yield_footer) ? $yield_footer : ''; ?>
		<?php echo isset($menus['alt']) ? $menus['alt'] : ''; ?>
	</div>
	<!-- footer end -->
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/assets/js/libs/jquery-1.7.2.min.js"><\/script>')</script>

<script src="/assets/js/bootstrap.min.js"></script>
<?php
	$script_template = '<script src="/assets/js/%s"></script>' . "\n";
	if (isset($include_scripts))
	{
		foreach ($include_scripts as $script)
		{
			echo sprintf($script_template, $script);
		}
	}
	$script_template = '<script src="%s"></script>' . "\n";
	if (isset($external_scripts))
	{
		foreach ($external_scripts as $script)
		{
			echo sprintf($script_template, $script);
		}
	}
?>

<script>
	$(document).ready(function () {
		$(".alert").alert();
	<?php
		if (isset($ready_script))
		{
			if (is_array($ready_script))
			{
				foreach ($ready_script as $script)
				{
					echo $script;
				}
			} else
			{
				echo $ready_script;
			}
		}
	?>

	});
</script>

<!--[if lt IE 7 ]>
<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
<script>window.attachEvent("onload", function () {
	CFInstall.check({mode:"overlay"})
})</script>
<![endif]-->

</body>
</html>
