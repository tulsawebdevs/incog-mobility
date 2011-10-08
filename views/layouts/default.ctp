<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>	   <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>	   <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<?php
	if(isset($folderName)) {
?>
	<title><?php echo $folderName?>INCOG - Ride Request</title>
<?php
	} else {
?>
	<title><?php echo $pageTitle?>INCOG - Ride Request</title>
<?php
	}
?>
	
	<link rel="stylesheet" href="/css/grid.css" />
	<link rel="stylesheet" href="/css/global.css" />
	<link rel="stylesheet" href="/vendor/DataTables-1.7.6/media/css/demo_table.css" />

	<!--[if IE]>
		<link rel="stylesheet" href="/css/ie.css" />
	<![endif]-->

	<script src="/vendor/modernizr-1.7.min.js"></script>
</head>

<?php
if(!isset($bodyClassName)) {
	$bodyClassName = Inflector::singularize($controllerName).ucfirst($actionName);
	$bodyClassName = "directory";
	$bodyClassName = strtolower($controllerName);
}
?>

<body class="<?php echo $bodyClassName?>">
<input type="hidden" id="baseUrl" value="<?php echo FULL_BASE_URL?>">
	
<div id="page">
	<header>
		<a href="/" class="logo"><img src="/img/logo.png" alt="oneCARE Logo" width="191" height="128" /></a>
		
		<?php
		$activeLi = ($controllerName == "Documents")
		?$controllerName
		:ucfirst($actionName);
		$LIs = array();
		if (isset($logged_in_as) && $logged_in_as) {
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

		</div><!-- /#primaryNavAndSearchWrapper -->
	</header>

	<div id="bd">
		<div class="main container">
			<div class="prepend-1 span-22 append-1">
				<?php echo $content_for_layout ?>
			</div>
		</div><!-- /.main -->
	</div><!-- /#bd -->
</div><!-- /#page -->

<footer>
	<div class="wrapper">
	</div>
</footer>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.js"></script>
<script>!window.jQuery && document.write(unescape('%3Cscript src="/js/libs/jquery-1.5.2.js"%3E%3C/script%3E'))</script>

<script src="/vendor/DataTables-1.7.6/media/js/jquery.dataTables.min.js"></script>
<script src="/vendor/ckeditor/ckeditor.js"></script>

<script src="/js/utils.js"></script>
<script src="/js/global.js"></script>

<!--[if lt IE 7 ]>
	<script src="/vendor/dd_belatedpng.js"></script>
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


<?php echo $this->element('sql_dump'); ?>
</body>
</html>
