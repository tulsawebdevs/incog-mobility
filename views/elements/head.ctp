<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<title><?php echo $siteTitle?></title>
	
	<?php
	echo $javascript->link('/vendor/modernizr-1.1.min');
	
	echo $html->css('base');
	echo $html->css('common');
	echo $html->css('forms');
	echo $html->css('tables');
	echo $html->css('/vendor/dataTables-1.6/media/css/demo_table_jui');
	echo $html->css('jquery-ui-1.7.2.custom');
	echo $html->css('/vendor/colorbox/css/colorbox');
	
	// TODO: Replace all CSS with blended global-min.css
	// echo $html->css("global-min");
	?>
	
	<!-- TODO: get icon -->
	<link type="image/x-icon" href="favicon.ico" rel="shortcut icon" />
	<link type="image/x-icon" href="favicon.ico" rel="icon" />
</head>