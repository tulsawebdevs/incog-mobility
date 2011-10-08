<!--
	TODO:
	
	Missing:
		(from auth)
		Requestor Name
		Requestor Email
-->

<h1>Project Details</h1>

<div class="messaging">
	<p class="no-js error"><img src="/img/icons/error.png" /> Your browser does not have JavaScript enabled. <a href="http://www.google.com/support/bin/answer.py?answer=23852">Learn how to enable JavaScript</a> before using this application.</p>
</div>

<div class="clear">&nbsp;</div>

<div class="span-6 colborder">
	<a class="button" href="/requests/dashboard" style="margin-top: 0; margin-bottom: 0;">
		<img src="/img/icons/table_go.png" /> Return to Dashboard
	</a>
	
	<hr />
	
	<h4>Update Project Details</h4>
	
	<p>Your changes will automatically save as you make them.</p>
	<input type="hidden" id="requestId" value="<?php echo $Request["Request"]["id"]?>" />
	
	<img style="display: none;" class="loader" src="/img/icons/ajax-loader.gif" />
		
	<?php
			$friendlyDueDate = strftime("%m/%d/%Y",strtotime($Request["Request"]["due_date"]));
	?>
	
	<div class="field">
		<label for="dueDate">Due Date</label>
		<input type="text" class="date text" name="data[Request][due_date]" id="dueDate" value="<?php echo $friendlyDueDate?>" />
		<span class="description">MM/DD/YYYY</span>
	</div>
	
	<div class="field">
		<label for="assignedTo">Assigned To</label>
		<?php
		$currentlyAssignedTo = $Request["Request"]["assignment"];
		$selected = "selected=\"selected\"";
		
		
		?>
		<select id="assignedTo">
			<option >(not assigned)</option>
			<option <?php if($currentlyAssignedTo == "Rich") echo $selected?>>Rich</option>
			<option <?php if($currentlyAssignedTo == "Cara") echo $selected?>>Cara</option>
			<option <?php if($currentlyAssignedTo == "Lisa") echo $selected?>>Lisa</option>
			<option <?php if($currentlyAssignedTo == "Lyn") echo $selected?>>Lyn</option>
			<option <?php if($currentlyAssignedTo == "Outsourced vendor") echo $selected?>>Outsourced vendor</option>
		</select>
	</div>

	<div class="field">
		<label for="projectDivision">Division</label>
		<?php
		$divisions = array("HC"=>"Home Cleaning","S"=>"Specialty Care");
		?>
		<select id="projectDivision">
		<?php
		foreach($divisions as $abbrev=>$thisDivision) {
		$selected='';
			if($Request["Request"]["division"]==$abbrev) $selected="selected=\"selected\"";
		?>
			<option value="<?php echo $abbrev?>" <?php echo $selected?> ><?php echo $thisDivision?></option>
		<?php
		}
		?>
		</select>
	</div>

	<div class="field">
		<label for="projectStatus">Project Status</label>
		<select id="projectStatus">
		<?php
		$stati = array("Pending","Need More Information","Active","Completed","Abandoned");
		foreach($stati as $thisStatus) {
		$selected='';
			if($Request["Request"]["status"]==$thisStatus) $selected="selected=\"selected\"";
		?>
			<option value="<?php echo $thisStatus?>" <?php echo $selected?>><?php echo $thisStatus?></option>
			<?php
		}
		?>
		</select>
	</div>
	
	<div id="needMoreInfoModal" class="modal" title="Need more information">
		<div class="enterMessage">
			<p>You&rsquo;ve indicated this request is lacking some information. Please specify what additional information is needed:</p>
		
			<textarea class="needMoreInfoMessage"></textarea>
		
			<p>An email notification will be sent to <strong><?php echo $Request["Request"]["requestor_name"]?></strong> (<?php echo $Request["Request"]["requestor_eml"]?>) with your message appended.</p>
		
			<button type="button" class="sendEmail">
				<img src="/img/icons/email_go.png" alt=""/> Send Email Notification
			</button>
		</div>
		
		<div class="emailSent">
			<p>Your email requesting additional information was successfully sent.</p>
			
			<button type="button" class="close">
				<img src="/img/icons/cross.png" alt=""/> Close this Window
			</button>
		</div>
	</div><!-- /#needMoreInfoModal -->
	
	<hr />
	
	<h4>Add a Note to this Project</h4>
	
	<p>Your note will be appended to the &ldquo;Notes&rdquo; section of this request.</p>
	
	<textarea class="newNote" maxlength="255"></textarea>
	<p><em>Limit 255 characters.</em></p>
	
	<button type="button" class="addNote" style="margin-bottom: 0;">
		<img src="/img/icons/add.png" alt=""/> Add Note
	</button>
	
	<hr />
	
	<p><a href="#" class="viewHistory"><strong>View project history</strong></a></p>
	
	<div id="historyModal" class="modal" title="Project history">
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
	</div><!-- /#historyModal -->
</div><!-- /.span-6 -->

<div class="span-15 last">
	
	<div id="tabs">
		<ul class="tabBar">
			<li><a href="#projectOverview">Project Overview</a></li>
			<li><a href="#projectNotes">Notes</a></li>
			<li><a href="#marketingInput">Marketing Input</a></li>
		</ul>
		
		<div id="projectOverview">
			<h3>Current Status: <span class="currentStatus"><?php echo $Request["Request"]["status"]?></span></h3>

			<?php
			$friendlyDate = strftime("%m/%d/%Y %I:%M %p",strtotime($Request["Request"]["created_on"]));
			?><p>This project was submitted by <strong><?php echo $Request["Request"]["requestor_name"]?></strong> (<?php echo $Request["Request"]["requestor_eml"]?>) on <strong><?php echo $friendlyDate ?></strong>.</p>

			<h2>General Information</h2>

			<p><strong>Project Name:</strong> <?php echo $Request["Request"]["project_name"]?></p>
			<p><strong>Brand:</strong> <?php echo $Request["Request"]["brand"]?></p>

			<p><strong>Due Date:</strong> <span class="currentDueDate"><?php echo $friendlyDueDate?></span></p>

			<?php
			if($Request["Request"]["customer"]) {
			?>			
				<p><strong>Customer:</strong> <?php echo $Request["Request"]["customer"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["ship_to"]) {
			?>			
				<p><strong>Ship To:</strong> <?php echo $Request["Request"]["ship_to"]?></p>
			<?php
			}
			?>

			<p><strong>Division:</strong> <span class="currentDivision"><?php echo $divisions[$Request["Request"]["division"]]?></span></p>

			<h2>Project Details</h2>

			<p><strong>Project Category:</strong> <?php 
			if ($Request["Request"]["category_other"]) {
				echo $Request["Request"]["category_other"];
			}else if ($Request["Request"]["category"]){
				echo $Request["Request"]["category"];
			}else{
				trigger_error("neither cat or cat_other evald true. req:");
				pr($Request["Request"]);
			}?></p>

			<?php
			$cdlist = "";
			foreach($Request["ProjectType"] as $PT) {
				$cdlist .= $PT["name"].", ";
			}
			if ($Request["Request"]["project_type_other"]) {
			$cdlist.= $Request["Request"]["project_type_other"].", ";
			}
			if($cdlist){
				$cdlist = substr($cdlist,0,(strlen($cdlist)-2));
			?>
			<p><strong>Project Type(s):</strong> <?php echo $cdlist ?></p>

			<div class="clear">&nbsp;</div>

			<?php
			}
			if($Request["Request"]["upc_code"]) {
			?>			
				<p><strong>UPC Code:</strong> <?php echo $Request["Request"]["upc_code"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["item_number"]) {
			?>			
				<p><strong>Item Number:</strong> <?php echo $Request["Request"]["item_number"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["part_number"]) {
			?>			
				<p><strong>Part Number:</strong> <?php echo $Request["Request"]["part_number"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["package_dimensions"]) {
			?>			
				<p><strong>Package Dimensions:</strong> <?php echo $Request["Request"]["package_dimensions"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["printer"]) {
			?>			
				<p><strong>Printer:</strong> <?php echo $Request["Request"]["printer"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["contact_information"]) {
			?>			
				<p><strong>Contact Information:</strong> <?php echo $Request["Request"]["contact_information"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["stock"]) {
			?>			
				<p><strong>Stock/Substrate:</strong> <?php echo $Request["Request"]["stock"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["varnish"]) {
			?>			
				<p><strong>Varnish/Coatings:</strong> <?php echo $Request["Request"]["varnish"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["die_reference"]) {
			?>			
				<p><strong>Dieline Reference:</strong> <?php echo $Request["Request"]["die_reference"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["count"]) {
			?>			
				<p><strong>Count:</strong> <?php echo $Request["Request"]["count"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["item_dimensions"]) {
			?>			
				<p><strong>Item Dimensions:</strong> <?php echo $Request["Request"]["item_dimensions"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["color_limit"]) {
			?>			
				<p><strong>Color Limit/Preferences:</strong> <?php echo $Request["Request"]["color_limit"]?></p>
			<?php
			}
			?>

			<?php
			if($Request["Request"]["comments"]) {
			?>			
				<p><strong>Project Comments:</strong> <?php echo $Request["Request"]["comments"]?></p>
			<?php
			}
			?>

			<h2>File Attachments</h2>

			<?php
			if(!sizeof($Request["RequestFile"])) {
				?>
			<!-- If (no files found) -->
			<p>No files attached to this request.</p>

			<?php
			}else{
			//	pr($Request["RequestFile"]);
				?>
			<ul>
			<?php
				foreach($Request["RequestFile"] as $RF) {
					$dlPath = str_replace("./","/",$RF["local_path"]);
					?>
					<li><a href="<?php echo $dlPath?>"><?php echo $RF["original_filename"]?></a></li>
				<?php
				}
				?>
			</ul>
			<?php
			}
			?>
		</div><!-- /#projectOverview -->

		<div id="projectNotes">
			<h3>Notes</h3>
			<?php
			foreach($Request["Note"] as $Note) {
				?>
			<p><?php echo stripslashes($Note["note_text"])?><br />
				<span class="noteMeta">&mdash; Added by <?php echo $Note["created_by"]?> on <?php echo $Note["created_on"]?></span></p>
			<?php
			}
			?>
		</div><!-- /#projectNotes -->
		
		<div id="marketingInput" class="clearfix">
			<dl class="horizontal fields first span-5">
				<dt>New part number(s) needed</dt>
				<dd>
					<label for="newPartNumberYes">
						<input type="radio" class="radio" id="newPartNumberYes" name="data[][]" value="Yes" /> Yes
					</label>
				</dd>
				<dd>
					<label for="newPartNumberNo">
						<input type="radio" class="radio" id="newPartNumberNo" name="data[][]" value="No" /> No
					</label>
				</dd>
			</dl>
			
			<dl class="horizontal fields span-4">
				<dt>Dielines needed</dt>
				<dd>
					<label for="dielinesYes">
						<input type="radio" class="radio" id="dielinesYes" name="data[][]" value="Yes" /> Yes
					</label>
				</dd>
				<dd>
					<label for="dielinesNo">
						<input type="radio" class="radio" id="dielinesNo" name="data[][]" value="No" /> No
					</label>
				</dd>
			</dl>
			
			<dl class="horizontal fields span-5 last">
				<dt>Coupon code(s) needed</dt>
				<dd>
					<label for="couponsYes">
						<input type="radio" class="radio" id="couponsYes" name="data[][]" value="Yes" /> Yes
					</label>
				</dd>
				<dd>
					<label for="couponsNo">
						<input type="radio" class="radio" id="couponsNo" name="data[][]" value="No" /> No
					</label>
				</dd>
			</dl>
			
			<div class="clear"></div>
			
			<div class="field first span-14 last">
				<label for="additionalDetails">Additional Details (number of part numbers, what for, types of dielines, etc.)</label>
				<textarea name="data[][]" id="additionalDetails" cols="30" rows="10"></textarea>
			</div>
			
			<div class="clear"></div>
			
			<h3 class="first span-14 last">Printer Contact for Product</h3>
			
			<div class="clear">&nbsp;</div>
			
			<div class="field first span-7">
				<label for="printerName">Name</label>
				<input type="text" class="text" name="data[][]" id="printerName" value="" />
			</div>
			
			<div class="field last span-7">
				<label for="printerTitle">Title</label>
				<input type="text" class="text" name="data[][]" id="printerTitle" value="" />
			</div>
			
			<div class="field first span-7">
				<label for="printerEmail">Email Address</label>
				<input type="text" class="text" name="data[][]" id="printerEmail" value="" />
			</div>
			
			<div class="field last span-7">
				<label for="printerPhone">Phone Number</label>
				<input type="text" class="text" name="data[][]" id="printerPhone" value="" />
			</div>
			
			<div class="field first span-7 append-7 last">
				<label for="printerMailing">Mailing Address</label>
				<input type="text" class="text" name="data[][]" id="printerMailing" value="" />
			</div>
			
			<div class="span-14 first last">
				<button type="submit">
					<img src="/img/icons/disk.png" alt=""/> Save Project Details
				</button>

				<a class="button negative" href="#">
					<img src="/img/icons/cross.png" alt=""/> Cancel
				</a>
				
				<hr />
			</div>
			
			<h3 class="span-14 first last">Routing Information</h3>
			
			<div class="clear">&nbsp;</div>
			
			<div class="field first span-14 last">
				<label for="recipients">Forward the project details to the following recipients</label>
				<textarea name="data[][]" id="recipients" cols="30" rows="10"></textarea>
				<p><em>Please comma-separate all email addresses</em></p>
			</div>
			
			<div class="span-14 first last">
				<button type="submit">
					<img src="/img/icons/email_go.png" alt=""/> Forward Project Details
				</button>
			</div>
		</div><!-- /#marketingInput -->
	</div><!-- /#tabs -->	
</div><!-- /.span-15.last -->
	
<?php
//pr($Request);
?>