			<h1>Requests Dashboard</h1>
			
			<div class="messaging">
				<p class="no-js error"><img src="/img/icons/error.png" /> Your browser does not have JavaScript enabled. <a href="http://www.google.com/support/bin/answer.py?answer=23852">Learn how to enable JavaScript</a> before using this application.</p>
			</div>
			
			<div class="clear">&nbsp;</div>
							
				<div id="tablecontainer">
					<table class="projectTable">
						<thead>
							<tr> 
					  			<th>Received</th>
								<th>ZIP</th>
								<th>Details</th>
								<th>Actions</th>
							</tr>
						</thead>

						<tbody>

<?php
foreach($lstRequests as $Request) {
?>
							<tr><td><?php echo $Request["Request"]["created_at"]?></td>
							<td><?php echo $Request["Request"]["zip"]?></td>
							<td><?php echo $Request["Request"]["detail"]?></td>
							<td><a href="/requests/claim/<?php echo $Request["Request"]["id"]?>">Ownage</a></td></tr>
		 			<?php
}
?>
						</tbody>
		 			</table>
				</div><!-- /#tablecontainer -->
			
