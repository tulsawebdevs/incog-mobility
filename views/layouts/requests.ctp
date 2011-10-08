<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>	   <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>	   <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1" />

<?php
if (!isset($pageTitle)) $pageTitle = "INCOG Ride Request";
?>
	<title><?php echo $pageTitle?></title>

	
	<link rel="stylesheet" href="/css/grid.css" />
<?php 
switch($additionalJS) {
	case "Dashboard":
	?>
	<link rel="stylesheet" href="/vendor/jquery-ui-1.8.13.custom/css/redmond/jquery-ui-1.8.13.custom.css" />
	<link rel="stylesheet" href="/vendor/DataTables-1.7.6/media/css/demo_page.css" />
	<link rel="stylesheet" href="/vendor/DataTables-1.7.6/media/css/demo_table_jui.css" />
	<?php
	break;
	
	default:
	?>
	<link rel="stylesheet" href="/vendor/jquery-ui-1.8.13.custom/css/redmond/jquery-ui-1.8.13.custom.css" />
	<?php
	break;
}
?>
	<link rel="stylesheet" href="/css/global.css" />
	
	<!--[if IE]>
		<link rel="stylesheet" href="/css/ie.css" />
	<![endif]-->

	<script src="/vendor/modernizr-1.7.min.js"></script>
</head>

<?php 
if(!isset($bodyClass)) {
	$bodyClassAttr="class=\"requestDetail\"";
}else{
	$bodyClassAttr="class=\"$bodyClass\"";
}

?>
<body <?php echo $bodyClassAttr?>>
<div id="page">
	<header id="hd">
		<a href="/" class="logo"><img src="/img/logo.png" alt="INCOG" width="191" height="128" /></a>
		
		<?php
		$activeLi = ($controllerName == "Documents")
		?$controllerName
		:ucfirst($actionName);
		$LIs = array();
		if ($logged_in_as) {
			pr("logged in as ".$logged_in_as);
			$LIs["Dashboard"] ="/pages/dashboard";
			$LIs["Documents"] ="/documents/index";
			$LIs["Calendar"] ="/pages/calendar";
			$LIs["Directory"] ="/pages/directory";
			$LIs["Locations"] ="/pages/locations";
		}
		?>

		<div id="primaryNavAndSearchWrapper">
			<nav>
				<ul>
					<?php 
						if (isset($logged_in_as) && $logged_in_as) {
					?>
					<li class="hasChildren">
						<a href="/pages/dashboard">Pages</a>
						<div class="sub">
							<div class="sub-inner">
								<ul>
									<?php
									foreach($LIs as $label=>$href) {
										$active ="";
										if($label==$activeLi) $active = "class=\"active\"";
										?>
										<li <?php echo $active?>>
										<a href="<?php echo $href?>"><?php echo $label?></a>
										</li>
									<?php
									}
									?>
								</ul>
							</div>
						</div>
					</li>
					<li>
						<a href="http://admin.onecaredev.com">Upload Documents</a>
					</li>
					<li>
						<a href="/requests/dashboard">Creative Requests</a>
					</li>
					<li>
						<a href="/users/logout">Log Out</a>
					</li>
					<?php } ?>
				</ul>
			</nav>

			<form id="searchForm" action="/documents/search" method="post">

<?php
if(isset($_GET["d"]) && $_GET["d"]) {
	?><input name="formDebug" value="<?php echo $_GET["d"]?>" type="hidden"><?php
}
?>
				<input type="text" class="text text" name="data[Document][keyword]" placeholder="Search for&hellip;" />
			</form>
		</div><!-- /#primaryNavAndSearchWrapper -->
	</header>

	<div id="bd">		
		<div class="main container" role="main">
			<div class="prepend-1 span-22 append-1">
				<?php echo $content_for_layout?> 
			</div>
		</div><!-- /.main -->
	</div><!-- /#bd -->
</div><!-- /#page -->

<footer id="ft">
	<div class="wrapper">
		<p>&copy; Copyright 2010 &mdash; <?php echo date('Y'); ?> The oneCARE Co. All Rights Reserved. All copy and claims valid only in the U.S.</p>
		<p><a href="http://bouncecare.com">BounceCare.com</a> | <a href="http://downywrinklereleaser.com">DownyWrinkleReleaser.com</a> | <a href="http://dryel.com">Dryel.com</a> | <a href="http://dreftbabycare.com">DreftBabyCare.com</a> | <a href="http://newfebreze.com">NewFebreze.com</a> | <a href="http://tidecare.com">TideCare.com</a></p>
	</div>
</footer>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/vendor/jquery-1.5.2.js">\x3C/script>')</script>

<?php
switch($additionalJS) {
	case "Add":
	?>
	<script src="/vendor/jquery-ui-1.8.13.custom/js/jquery-ui-1.8.13.custom.min.js"></script>
	<script src="/vendor/jquery.scrollTo-1.4.2/jquery.scrollTo-min.js"></script>
	<script src="/vendor/jquery-validation-1.8.0/jquery.validate.js"></script>
	
	<?php
	break;
	case "Dashboard":
	?>
	<script src="/vendor/jquery-ui-1.8.13.custom/js/jquery-ui-1.8.13.custom.min.js"></script>
	<script src="/vendor/DataTables-1.7.6/media/js/jquery.dataTables.js"></script>
	<script src="/vendor/jquery.cookie.js"></script>
	<?php
	break;
	case "Detail":
	?>
	<script src="/vendor/jquery-ui-1.8.13.custom/js/jquery-ui-1.8.13.custom.min.js"></script>
	<script src="/vendor/jquery.scrollTo-1.4.2/jquery.scrollTo-min.js"></script>
	
	<?php
	default:
	break;
}
?>

<script src="/js/utils.js"></script>
<script src="/js/configs.js"></script>
<script src="/js/global.js"></script>

<!--[if lt IE 7 ]>
	<script src="/vendor/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg, h2");</script>
<![endif]-->

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20102347-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</body>
</html>
