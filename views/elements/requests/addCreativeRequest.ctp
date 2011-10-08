<h1>Request New Creative Project</h1>

<div class="messaging">
	<p class="no-js error"><img src="/img/icons/error.png" /> Your browser does not have JavaScript enabled. <a href="http://www.google.com/support/bin/answer.py?answer=23852">Learn how to enable JavaScript</a> before using this application.</p>
</div>

<p>Fields marked with an asterisk (<span class="required">*</span>) are required.</p>

<?php
echo $interform->create("Request",array("enctype"=>"multipart/form-data")); 
?>		
			
	<input type="hidden" class="text" name="data[Request][requestor_name]" value="<?php echo $logged_in_name?>" />
	<input type="hidden" class="text" name="data[Request][requestor_eml]" value="<?php echo $logged_in_as?>" />
			
	<h2 class="expanded">General Information</h2>
	<div class="section">
		<div class="field span-6">
			<label for="projectName">Project Name<span class="required">*</span></label>
			<input type="text" class="required text" name="data[Request][project_name]" id="projectName" value="" />
		</div>

		<div class="field span-6 append-10 last">
			<label for="dueDate">Due Date<span class="required">*</span></label>
			<input type="text" class="required date text" name="data[Request][due_date]" id="dueDate" value="" />
		</div>

		<div class="clear"></div>

		<div class="field span-6">
			<label for="customer">Customer</label>
			<input type="text" class="text" name="data[Request][customer]" id="customer" value="" />
			<span class="description">End retailer such as Wal-Mart</span>
		</div>

		<div class="field span-6 append-10 last">
			<label for="shipTo">Ship To</label>
			<input type="text" class="text" name="data[Request][ship_to]" id="shipTo" value="" />
			<span class="description">To who and where this project<br />is delivered</span>
		</div>
		
		<div class="clear"></div>
		
		<dl class="horizontal fields span-12 append-12 last">
			<dt>Division<span class="required">*</span></dt>
			<dd>
				<label for="divHC">
					<input type="radio" class="required radio" id="divHC" name="data[Request][division]" value="HC" />
					Home Cleaning
				</label>
			</dd>
			<dd>
				<label for="divSC">
					<input type="radio" class="required radio" id="divSC" name="data[Request][division]" value="S" />
					Specialty Care
				</label>
			</dd>
		</dl>
				
		<div class="clear"></div>

		<button type="button" class="continueForm">
			<img src="/img/icons/accept.png" alt=""/> Continue
		</button>
		
		<a class="button negative" href="/requests/dashboard/">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->

	<h2 class="collapsed">Select a Brand</h2>
	<div class="section">
		<p>Please select a brand logo<span class="required">*</span></p>
			
		<div class="field">
			<input type="hidden" class="hidden required" name="data[Request][brand]" value="" />
		</div>
		<?php
		$brandsArr = array("","Bounce","Clorox","Downy","Dreft","Dryel","Evercare",
		"Febreze","Michael Graves","Purina","Roto-Rooter","Tide","Private Label");
		foreach($brandsArr as $id=>$brandName) {
			if(!$id) continue;
			$bn = str_replace(" ","-",$brandName);
			$gif = strtolower($bn);
		
			$class="span-4";
			if($id==4 || $id==8 || $id==12) $class .= " last";
		?>
		<a href="#" class="brandImg"><span class="<?php echo $class?>"><img src="/img/brand-logos/<?php echo strtolower($gif)?>.gif" width="109" height="66" alt="<?php echo $brandName?>" /></span></a>
		<?php
			if ($id==4 || $id==8) echo "<div class=\"clear\"></div>";
		}
		?>
		<div class="clear"></div>
	
		<button type="button" class="continueForm">
			<img src="/img/icons/accept.png" alt=""/> Continue
		</button>
		
		<a class="button negative" href="/requests/dashboard/">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->

	<h2 class="collapsed">Project Details</h2>
	<div class="section">
		<div class="field span-6">
			<label for="projectCategory">Project Category<span class="required">*</span></label>
			<select name="data[Request][category]" id="projectCategory" class="required">
				<option value="">Select a category</option>
			<?php
			$lstCategories=array(
			"Soft Surface","Cleaning","Corporate","Laundry","Pet","Europe/Canada","Fabric Care","Private Label"
			);
			foreach($lstCategories as $id=>$category) {
				?>
				<option value="<?php echo $category?>"><?php echo $category?></option>
			<?php
			}
			?>
				<option value="other">Other</option>
			</select>
		</div>

		<div class="hidden field span-6 append-10 last">
			<label for="categoryOther">Other Category<span class="required">*</span></label>
			<input type="text" class="text" name="data[Request][category_other]" id="categoryOther" value="" />
			<span class="description">Please specify a project category</span>
		</div>
	
		<div class="clear"></div>

		<h3>Type of Project<span class="required">*</span></h3>
		<ul class="projectTypes fields">
			<li class="span-5">
			<?php
			foreach ($lstProjectTypes as $i=>$projectType) {
				$label="type".str_replace(" ","",$projectType['ProjectType']['name']);
				?>
				<label for="<?php echo $label?>">
				<?php 
				echo $form->checkbox("ProjectType.id.".$projectType['ProjectType']['id'],
				 array('id'=>$label,'value' => $projectType['ProjectType']['id']));
				 ?>
			
				<?php
				echo $projectType['ProjectType']['name'];
				?>
				</label>
				<?php
//				pr("i $i: PT ");
//				pr($projectType);
				
				if(($i%4)==3 && $i!=(sizeof($lstProjectTypes)-1)) {
					echo "</li>\n<li class=\"span-5\">";
				}
			}
			?>
				<label for="typeOther">
					<input type="checkbox" class="checkbox" name="data[Request][project_type_other_chk]" id="typeOther" /> Other Type
				</label>
				<input type="text" class="text" disabled="disabled" name="data[Request][project_type_other]" id="typeOtherText" />
				<span class="description">Please specify a type of project</span>
			</li>
		</ul>

		<button type="button" class="continueForm">
			<img src="/img/icons/accept.png" alt=""/> Continue
		</button>
		
		<a class="button negative" href="/requests/dashboard/">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->

	<h2 class="collapsed">Additional Details &amp; Submit Request</h2>
	<div class="section">
		<p>Providing some additional details about your project may allow the art department to fulfill your request more efficiently. All additional fields are optional. Disabled fields are not needed for your type of project.</p>

		<button type="button" class="showAdditionalFields">
			<img src="/img/icons/application_view_detail.png" alt=""/> Provide Additional Details
		</button>
	
		<div class="additionalFieldWrapper">
			<div class="disabled field span-7">
				<label for="aUpcCode">UPC Code</label>
				<input type="text" class="text" name="data[Request][upc_code]" id="aUpcCode" value="" disabled="disabled" />
			</div>

			<div class="disabled field span-7">
				<label for="aItemNumber">Item Number</label>
				<input type="text" class="text" name="data[Request][item_number]" id="aItemNumber" value="" disabled="disabled" />
			</div>

			<div class="disabled field span-7 last">
				<label for="aPartNumber">Part Number</label>
				<input type="text" class="text" name="data[Request][part_number]" id="aPartNumber" value="" disabled="disabled" />
			</div>

			<div class="disabled field span-7">
				<label for="aPackageDimensions">Package Dimensions</label>
				<input type="text" class="text" name="data[Request][package_dimensions]" id="aPackageDimensions" value="" disabled="disabled" />
			</div>

			<div class="disabled field span-7">
				<label for="aPrinter">Printer</label>
				<input type="text" class="text" name="data[Request][printer]" id="aPrinter" value="" disabled="disabled" />
				<span class="description">Name of company/supplier who<br />will print the artwork</span>
			</div>

			<div class="disabled field span-7 last">
				<label for="aContactInformation">Contact Information</label>
				<input type="text" class="text" name="data[Request][contact_information]" id="aContactInformation" value="" disabled="disabled" />
				<span class="description">Name of contact person at<br />the printer</span>
			</div>

			<div class="disabled field span-7">
				<label for="aStock">Stock/Substrate</label>
				<select name="data[Request][stock]" id="aStock" disabled="disabled">
					<option value="">Please select a stock/substrate</option>
					<option value="Card Weight (thickness in points)">Card Weight (thickness in points)</option>
					<option value="Polypropylene Bag">Polypropylene Bag</option>
					<option value="Polyethylene Bag">Polyethylene Bag</option>
					<option value="Pressure-sensitive Adhesive Label">Pressure-sensitive Adhesive Label</option>
					<option value="Text Weight Insert">Text Weight Insert</option>
					<option value="Cover Weight Insert">Cover Weight Insert</option>
					<option value="Corrugated (thickness in flutes)">Corrugated (thickness in flutes)</option>
					<option value="Other">Other</option>
				</select>
				<!-- <input type="text" class="text" name="aStock" id="aStock" value="" disabled="disabled" /> -->
			</div>

			<div class="disabled field span-7">
				<label for="aVarnish">Varnish/Coatings</label>
				<select name="data[Request][varnish]" id="aVarnish" disabled="disabled">
					<option value="">Please select a varnish/coating</option>
					<option value="Aqueous">Aqueous</option>
					<option value="UV">UV</option>
					<option value="Matte">Matte</option>
					<option value="Uncoated">Uncoated</option>
					<option value="Other">Other</option>
				</select>
				<!-- <input type="text" class="text" name="aVarnish" id="aVarnish" value="" disabled="disabled" /> -->
			</div>

			<div class="disabled field span-7 last">
				<label for="aDieReference">Dieline Reference</label>
				<input type="text" class="text" name="data[Request][die_reference]" id="aDieReference" value="" disabled="disabled" />
				<span class="description">Indicate whether dieline exists<br />or a new one will be supplied</span>
			</div>

			<div class="disabled field span-7">
				<label for="aCount">Count</label>
				<input type="text" class="text" name="data[Request][count]" id="aCount" value="" disabled="disabled" />
			</div>

			<div class="disabled field span-7">
				<label for="aItemDimensions">Item Dimensions</label>
				<input type="text" class="text" name="data[Request][item_dimensions]" id="aItemDimensions" value="" disabled="disabled" />
			</div>

			<div class="disabled field span-7 last">
				<label for="aColorLimit">Color Limit/Preference</label>
				<input type="text" class="text" name="data[Request][color_limit]" id="aColorLimit" value="" disabled="disabled" />
				<span class="description">Number of color stations<br />supplier has available</span>
			</div>

			<div class="field span-12 append-10 last">
				<label for="comments">Project Comments or Background</label>
				<textarea name="data[Request][comments]" id="comments" cols="30" rows="10"></textarea>
			</div>
		</div><!-- /.additionalFieldWrapper -->
	
		<div class="clear">&nbsp;</div>
	
		<h3>File Attachments</h3>

		<p>If you have any additional documentation to provide the art department, such as a Design Brief Form, please provide them below. You may package all documents into a single ZIP file or upload all documents individually.</p>
	
		<div class="field span-8 append-14 last">
			<input type="file" class="file" name="original_filename" value="testing" />
		</div>
	
		<div class="hidden src">
			<div class="field hidden span-8 append-14 last">
				<a class="removeItem" href="#"><img src="/img/icons/cross.png" width="16" height="16" alt="Remove this item"></a> <input type="file" class="file" name="" />
			</div>
		</div>
	
		<button type="button" class="addItem">
			<img src="/img/icons/page_white_add.png" alt=""/> Add Another File
		</button>
	
		<hr />
	
		<input type="hidden" name="username" id="username" value="" />
		<input type="hidden" name="data[Request][status]" value="Pending" />
		
		<!-- Populated by JS on submit -->
		<input type="hidden" name="submissionTime" id="submissionTime" value="" />

		<button type="submit" class="positive">
			<img src="/img/icons/add.png" alt=""/> Request New Creative Project
		</button>
		
		<a class="button negative" href="/requests/dashboard/">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->
</form>