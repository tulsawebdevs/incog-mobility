			<h1><?php echo ucwords($requestType)?> Requests Dashboard</h1>
			
			<div class="messaging">
				<p class="no-js error"><img src="/img/icons/error.png" /> Your browser does not have JavaScript enabled. <a href="http://www.google.com/support/bin/answer.py?answer=23852">Learn how to enable JavaScript</a> before using this application.</p>
			</div>
			
			<div class="clear">&nbsp;</div>
			
			<div id="tabs">
				
				<div id="tablecontainer">
					<table class="projectTable">
						<thead>
							<tr> 
					  			<th>Received</th>
								<th>ZIP</th>
								<th>Details</th>
							</tr>
						</thead>

						<tbody>

<?php
foreach($lstRequests as $Request) {
							<tr><td><?php echo $Request["Request"]?></td>
							<td><?php echo $HCRequest["Request"]?></td>
							<td><?php echo $HCRequest["Request"]?></td></tr>
		 			<?php
}
?>
						</tbody>
		 			</table>
				</div><!-- /#homeCleaning -->
			</div><!-- /#tabs -->
			
			
