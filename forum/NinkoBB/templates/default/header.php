<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo lang('encoding'); ?>" />
	<title><?php echo $config['site_name']; ?> - Open Source Forum Script</title>
	<link href="<?php echo $config['template_url']; ?>assets/css/ninko.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo $config['template_url']; ?>assets/js/jquery.js"></script>
	<script src="<?php echo $config['template_url']; ?>assets/js/jquery.scrollTo-min.js"></script>
<?php load_hook('page_head'); ?>
</head>
<body>
<?php load_hook('page_start'); ?>
<div id="wrapper">
	<div id="header">
		<div class="right">
			<?php load_hook('header_right'); ?>
		</div>

		<h1 class="logo">
			<a href="<?php echo $config['url_path']; ?>"><?php echo $config['site_name']; ?></a>
		</h1>
	</div>