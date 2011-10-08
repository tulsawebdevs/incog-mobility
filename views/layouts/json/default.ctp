<?php
//header("Pragma: no-cache");
//header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
//header('Content-Type: text/x-json');
//no! 
//header("X-JSON: ".$content_for_layout);
echo $content_for_layout;
Configure::write("debug",0);
?>