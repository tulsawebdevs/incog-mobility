<ul>
	<?php 
	foreach($requestHistory as $historyItem) {
	$createdFriendly=strftime("%D %H:%M",strtotime($historyItem["status_history"]["created_on"]));
	?>
		<li><?php echo $createdFriendly ?> - <?php echo $historyItem["status_history"]["note"]?></li>
	<?php
	}
	//pr($requestHistory);
	?>
</ul>