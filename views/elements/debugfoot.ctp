<?php
if (count($debugMsgs)) {
	?>
	<?php 
	foreach($debugMsgs as $msg) {
//		echo $msg[0]."  -- ";
		if (isset($msg[0]) &&  $msg[0] == "State Info:") {
			?>
			<div style="background:yellow"><p>State</p> 
				<?php foreach($msg[1] as $key=>$value) {
					if (is_object($value)) continue;
					$v = is_array($value) ? print_r($value,1) : $value;
					echo "$key: $v";
					echo "<br/>";
				}
				?>
				</div>
		<?php
		}else{
			pr($msg);
		}
		?>
	<?php
	}
}
?>
