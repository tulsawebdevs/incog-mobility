<h1>Request New <?php echo ucfirst($requestType)?> Project</h1>

<div class="messaging">
	<p class="no-js error"><img src="/img/icons/error.png" /> Your browser does not have JavaScript enabled. <a href="http://www.google.com/support/bin/answer.py?answer=23852">Learn how to enable JavaScript</a> before using this application.</p>
</div>

<p>Fields marked with an asterisk (<span class="required">*</span>) are required.</p>

<?php
echo $interform->create("Request",array("enctype"=>"multipart/form-data")); 
?>		
			
	<input type="hidden" class="text" name="data[Request][requestor_name]" value="<?php echo $logged_in_name?>" />
	<input type="hidden" class="text" name="data[Request][requestor_eml]" value="<?php echo $logged_in_as?>" />
	<input type="hidden" name="data[Request][status]" value="Pending" />
	
	<!-- Populated by JS on submit -->
	<input type="hidden" name="submissionTime" id="submissionTime" value="" />
			
	<h2 class="expanded">General Information</h2>
	<div class="section">
		<div class="field span-6">
			<label for="projectName">Project Name<span class="required">*</span></label>
			<input type="text" class="required text" name="data[Request][projectName]" id="projectName" value="" />
		</div>

		<div class="field span-6 append-10 last">
			<label for="shipReleaseDate">Ship/Release Date<span class="required">*</span></label>
			<input type="text" class="required date text" name="data[Request][shipReleaseDate]" id="shipReleaseDate" value="" />
			<span class="description">New item setup requires minimum 75 business days to ship</span>
		</div>

		<div class="field span-6">
			<label for="retailerName">Retailer Name</label>
			<input type="text" class="text" name="data[Request][retailerName]" id="retailerName" value="" />
		</div>

		<div class="field span-6 append-10 last">
			<label for="retailerTier">Retailer Tier</label>
			<input type="text" class="text" name="data[Request][retailerTier]" id="retailerTier" value="" />
		</div>
		
		<h4>Retailer Contact Information</h4>
		
		<div class="field span-6">
			<label for="retailerContactName">Name</label>
			<input type="text" class="text" name="data[Request][retailerContactName]" id="retailerContactName" value="" />
		</div>

		<div class="field span-6 append-10 last">
			<label for="retailerContactTitle">Title</label>
			<input type="text" class="text" name="data[Request][retailerContactTitle]" id="retailerContactTitle" value="" />
		</div>
		
		<div class="field span-6">
			<label for="retailerContactEmail">Email Address</label>
			<input type="text" class="text email" name="data[Request][retailerContactEmail]" id="retailerContactEmail" value="" />
		</div>

		<div class="field span-6 append-10 last">
			<label for="retailerContactPhone">Phone Number</label>
			<input type="text" class="text" name="data[Request][retailerContactPhone]" id="retailerContactPhone" value="" />
		</div>

		<button type="button" class="continueForm">
			<img src="/img/icons/accept.png" alt=""/> Continue
		</button>
		
		<a class="button negative" href="/requests/dashboard/operations">
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
		
		<a class="button negative" href="/requests/dashboard/operations">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->

	<h2 class="collapsed">Type of Request</h2>
	<div class="section">
		<dl class="projectTypes fields hasOther span-6 append-18 last">
			<dt>Please select a type of request<span class="required">*</span></dt>
			
			<dd>
				<label for="newProductSetup">
					<input type="radio" class="required" name="projectRequestType" value="newProductSetup" id="newProductSetup" /> New Product Setup
				</label>
			</dd>
			<dd>
				<label for="existingProductChange">
					<input type="radio" class="required" name="projectRequestType" value="existingProductChange" id="existingProductChange" /> Existing Product Change
				</label>
			</dd>
			<dd>
				<label for="marketingProgram">
					<input type="radio" class="required" name="projectRequestType" value="marketingProgram" id="marketingProgram" /> Marketing Program
				</label>
			</dd>
			<dd>
				<label for="retailerMeetingPreparation">
					<input type="radio" class="required" name="projectRequestType" value="retailerMeetingPreparation" id="retailerMeetingPreparation" /> Retailer Meeting Preparation
				</label>
			</dd>
			<dd>
				<label for="otherRequest">
					<input type="radio" class="required otherTrigger" name="projectRequestType" value="otherRequest" id="otherRequest" /> Other Request
				</label>
				<input type="text" class="text otherField" disabled="disabled" name="data[Request][project_type_other]" id="otherRequestText" />
				<span class="description">Please specify a type of project</span>
			</dd>
		</dl>

		<button type="button" class="continueForm">
			<img src="/img/icons/accept.png" alt=""/> Continue
		</button>
		
		<a class="button negative" href="/requests/dashboard/">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->

	<h2 class="collapsed">Request Details <span class="requestDetailsTitle"></span></h2>
	<div class="section">
		
		<div class="projectRequestTypeWrapper">
			<div id="newProductSetupForm" class="requestType">
				<dl class="horizontal hasProgressive fields span-12 append-12 last">
					<dt>Who is responsible for assigning UPC?<span class="required">*</span></dt>
					<dd>
						<label for="typeNewAssignUpcRetailer">
							<input type="radio" class="progressiveTrigger required radio" id="typeNewAssignUpcRetailer" name="data[Request][assignUpc]" value="Retailer" /> Retailer
						</label>
					</dd>
					<dd>
						<label for="typeNewAssignUpcOnecare">
							<input type="radio" class="required radio" id="typeNewAssignUpcOnecare" name="data[Request][assignUpc]" value="oneCare" /> OneCare
						</label>
					</dd>
				</dl>

				<div class="progressiveField">
					<div class="field span-6">
						<label>Product Name &amp; Size</label>
						<input type="text" class="text" name="original_copy_product" value="" />
					</div>

					<div class="field span-6 append-10 last">
						<label>UPC</label>
						<input type="text" class="text" name="original_copy_upc" value="" />
					</div>

					<div class="hidden src">
						<div class="field hidden span-6">
							<label>Product Name &amp; Size</label>
							<input type="text" class="text" name="" value="" />
						</div>

						<div class="field hidden span-6 append-10 last">
							<label>UPC</label>
							<input type="text" class="text" name="" value="" /> <a class="removeItem" data-remove="pairedfields" href="#"><img src="/img/icons/cross.png" width="16" height="16" alt="Remove this item"></a>
						</div>
					</div><!-- /.src -->

					<div class="clear"></div>

					<button type="button" class="addItem">
						<img src="/img/icons/tag_blue_add.png" alt=""/> Add Another Product
					</button>
				</div><!-- /.progressiveField -->

				<div class="clear">&nbsp;</div>

				<dl class="horizontal hasProgressive fields span-12 append-12 last">
					<dt>Is the new product replacing an existing product<span class="required">*</span></dt>
					<dd>
						<label for="replaceYes">
							<input type="radio" class="progressiveTrigger required radio" id="replaceYes" name="data[Request][replaceExisting]" value="Yes" />
							Yes
						</label>
					</dd>
					<dd>
						<label for="replaceNo">
							<input type="radio" class="required radio" id="replaceNo" name="data[Request][replaceExisting]" value="No" />
							No
						</label>
					</dd>
				</dl>

				<div class="progressiveField">
					<div class="field span-6">
						<label>Product Name &amp; Size<span class="required">*</span></label>
						<input type="text" class="text" name="original_copy_product" data-required="yes" value="" />
					</div>

					<div class="field span-6 append-10 last">
						<label>UPC<span class="required">*</span></label>
						<input type="text" class="text" name="original_copy_upc" data-required="yes" value="" />
					</div>
					
					<div class="clear"></div>

					<div class="hidden src">
						<div class="field hidden span-6">
							<label>Product Name &amp; Size<span class="required">*</span></label>
							<input type="text" class="text" name="" data-required="yes" value="" />
						</div>

						<div class="field hidden span-6 append-10 last">
							<label>UPC<span class="required">*</span></label>
							<input type="text" class="text" name="" data-required="yes" value="" /> <a class="removeItem" data-remove="pairedfields" href="#"><img src="/img/icons/cross.png" width="16" height="16" alt="Remove this item"></a>
						</div>
						
						<div class="clear"></div>
					</div><!-- /.src -->

					<button type="button" class="addItem">
						<img src="/img/icons/tag_blue_add.png" alt=""/> Add Another Product
					</button>
				</div><!-- /.progressiveField -->

				<div class="clear">&nbsp;</div>

				<dl class="horizontal hasProgressive fields span-12 append-12 last">
					<dt>Is this a copy of an existing SKU?<span class="required">*</span></dt>
					<dd>
						<label for="copyYes">
							<input type="radio" class="progressiveTrigger required radio" id="copyYes" name="data[Request][copy]" value="Yes" />
							Yes
						</label>
					</dd>
					<dd>
						<label for="copyNo">
							<input type="radio" class="required radio" id="copyNo" name="data[Request][copy]" value="No" />
							No
						</label>
					</dd>
				</dl>

				<div class="progressiveField">
					<div class="field span-6">
						<label>Product Name &amp; Size</label>
						<input type="text" class="text" name="original_copy_product" value="" />
					</div>

					<div class="field span-6 append-10 last">
						<label>UPC</label>
						<input type="text" class="text" name="original_copy_upc" value="" />
					</div>

					<div class="hidden src">
						<div class="field hidden span-6">
							<label>Product Name &amp; Size</label>
							<input type="text" class="text" name="" value="" />
						</div>

						<div class="field hidden span-6 append-10 last">
							<label>UPC</label>
							<input type="text" class="text" name="" value="" /> <a class="removeItem" data-remove="pairedfields" href="#"><img src="/img/icons/cross.png" width="16" height="16" alt="Remove this item"></a>
						</div>
					</div><!-- /.src -->

					<div class="clear"></div>

					<button type="button" class="addItem">
						<img src="/img/icons/tag_blue_add.png" alt=""/> Add Another Product
					</button>
				</div><!-- /.progressiveField -->

				<div class="clear">&nbsp;</div>

				<div class="span-10 colborder">
					<h3>Additional Details</h3>

					<p>Please provide any additional project details.</p>

					<div class="field span-5">
						<label for="productType">Product Type</label>
						<input type="text" class="text" name="data[Request][productType]" id="productType" value="" />
						<span class="description">Ex. Drain cleaner, candle, etc.</span>
					</div>

					<div class="field span-5 last">
						<label for="description">Description</label>
						<input type="text" class="text" name="data[Request][description]" id="description" value="" />
						<span class="description">Ex. Homelife 32oz DPO</span>
					</div>

					<div class="field span-5">
						<label for="productSize">Product Size</label>
						<input type="text" class="text" name="data[Request][productSize]" id="productSize" value="" />
					</div>

					<div class="field span-5 last">
						<label for="caseCount">Desired Case Count</label>
						<input type="text" class="text" name="data[Request][caseCount]" id="caseCount" value="" />
					</div>

					<div class="field span-5 append-5 last">
						<label for="componentsColors">Components &amp; Desired Colors</label>
						<input type="text" class="text" name="data[Request][componentsColors]" id="componentsColors" value="" />
						<span class="description">Ex. Black bottle, red cap</span>
					</div>

					<div class="field span-10 last">
						<label for="otherInfo">Other Information</label>
						<textarea name="data[Request][otherInfo]" id="otherInfo" cols="30" rows="10"></textarea>
					</div>
				</div>
				
				<div class="span-11 last">
					<h3>New Product Financial Feasibility</h3>

					<p>New products should have VCM > $40k<br />
						New versions of existing products should have VCM > $20k</p>

					<div class="field span-5">
						<label for="expectedAnnualVolume">Expected Annual Volume<span class="required">*</span></label>
						<input type="text" class="required text" name="data[Request][expectedAnnualVolume]" id="expectedAnnualVolume" value="" />
					</div>

					<div class="field span-6 last">
						<label for="expectedNumStores">Expected Number of Stores</label>
						<input type="text" class="text" name="data[Request][expectedNumStores]" id="expectedNumStores" value="" />
					</div>
					
					<div class="clear"></div>

					<div class="field span-5">
						<label for="expectedUnitsWeek">Expected Units/Store/Week</label>
						<input type="text" class="text" name="data[Request][expectedUnitsWeek]" id="expectedUnitsWeek" value="" />
					</div>

					<div class="field span-6 last">
						<label for="expectedACM">Expected Annual Contribution Margin<span class="required">*</span></label>
						<input type="text" class="required text" name="data[Request][expectedACM]" id="expectedACM" value="" />
					</div>
				</div>
				
				<div class="clear"></div>
			</div><!-- /#newProductSetupForm -->
			
			<div id="existingProductChangeForm" class="requestType">
				<dl class="horizontal hasOther fields span-22 last">
					<dt>Type of Change?<span class="required">*</span></dt>
					<dd>
						<label for="typeChangeTypeArt">
							<input type="radio" class="required radio" id="typeChangeTypeArt" name="data[Request][typeChangeType]" value="Artwork Change" /> Artwork Change (new labels)
						</label>
					</dd>
					<dd>
						<label for="typeChangeTypeComponent">
							<input type="radio" class="required radio" id="typeChangeTypeComponent" name="data[Request][typeChangeType]" value="Component Change" /> Component Change (new bottles)
						</label>
					</dd>
					<dd>
						<label for="typeChangeTypeOther" style="display: inline-block;">
							<input type="radio" class="required radio otherTrigger" id="typeChangeTypeOther" name="data[Request][typeChangeType]" value="Other Change" /> Other
						</label>&nbsp;
						<input type="text" class="text otherField" disabled="disabled" name="data[Request][project_type_other]" id="typeChangeTypeOtherDetail" style="display: inline-block;" />
						<span class="description">Please specify desired change</span>
					</dd>
				</dl>

				<dl class="horizontal fields span-12 append-12 last">
					<dt>Who owns the artwork files?<span class="required">*</span></dt>
					<dd>
						<label for="typeChangeOwnerRetailer">
							<input type="radio" class="required radio" id="typeChangeOwnerRetailer" name="data[Request][typeChangeOwner]" value="Retailer" /> Retailer
						</label>
					</dd>
					<dd>
						<label for="typeChangeOwnerOnecare">
							<input type="radio" class="required radio" id="typeChangeOwnerOnecare" name="data[Request][typeChangeOwner]" value="oneCARE" /> oneCARE
						</label>
					</dd>
				</dl>

				<h3>Details of each Impacted Product</h3>

				<div class="field span-6">
					<label>Product Name &amp; Size</label>
					<input type="text" class="text" name="original_copy_product" value="" />
				</div>

				<div class="field span-6 append-10 last">
					<label>UPC</label>
					<input type="text" class="text" name="original_copy_upc" value="" />
				</div>

				<div class="hidden src">
					<div class="field hidden span-6">
						<label>Product Name &amp; Size</label>
						<input type="text" class="text" name="" value="" />
					</div>

					<div class="field hidden span-6 append-10 last">
						<label>UPC</label>
						<input type="text" class="text" name="" value="" /> <a class="removeItem" data-remove="pairedfields" href="#"><img src="/img/icons/cross.png" width="16" height="16" alt="Remove this item"></a>
					</div>
				</div><!-- /.src -->
				
				<div class="clear"></div>

				<button type="button" class="addItem">
					<img src="/img/icons/tag_blue_add.png" alt=""/> Add Another Product
				</button>
				
				<div class="clear">&nbsp;</div>

				<div class="field span-12 append-10 last">
					<label for="typeChangeOtherInfo">Description of required changes</label>
					<textarea name="data[Request][typeChangeOtherInfo]" id="typeChangeOtherInfo" cols="30" rows="10"></textarea>
				</div>
			</div><!-- /#existingProductChangeForm -->
			
			<div id="marketingProgramForm" class="requestType">
				<div class="field span-6">
					<label for="typeProgramName">Program Name</label>
					<input type="text" class="text" name="data[Request][typeProgramName]" id="typeProgramName" value="" />
				</div>

				<div class="field span-6 append-10 last">
					<label for="typeProgramInMarket">Date Program in-market</label>
					<input type="text" class="date text" name="data[Request][typeProgramInMarket]" id="typeProgramInMarket" value="" />
				</div>

				<h3>Details of each Impacted Product</h3>

				<div class="field span-6">
					<label>Product Name &amp; Size</label>
					<input type="text" class="text" name="original_copy_product" value="" />
				</div>

				<div class="field span-6 append-10 last">
					<label>UPC</label>
					<input type="text" class="text" name="original_copy_upc" value="" />
				</div>

				<div class="hidden src">
					<div class="field hidden span-6">
						<label>Product Name &amp; Size</label>
						<input type="text" class="text" name="" value="" />
					</div>

					<div class="field hidden span-6 append-10 last">
						<label>UPC</label>
						<input type="text" class="text" name="" value="" /> <a class="removeItem" data-remove="pairedfields" href="#"><img src="/img/icons/cross.png" width="16" height="16" alt="Remove this item"></a>
					</div>
				</div><!-- /.src -->
				
				<div class="clear"></div>

				<button type="button" class="addItem">
					<img src="/img/icons/tag_blue_add.png" alt=""/> Add Another Product
				</button>
				
				<div class="clear">&nbsp;</div>

				<div class="field span-12 append-10 last">
					<label for="typeProgramDetails">Details of marketing program, including quantities</label>
					<textarea name="data[Request][typeProgramDetails]" id="typeProgramDetails" cols="30" rows="10"></textarea>
				</div>
			</div><!-- /#marketingProgramForm -->
			
			<div id="retailerMeetingPreparationForm" class="requestType">
				<div class="field span-6">
					<label for="typeMeetingDate">Meeting Date<span class="required">*</span></label>
					<input type="text" class="required date text" name="data[Request][typeMeetingDate]" id="typeMeetingDate" value="" />
				</div>
				
				<ul class="requestedMaterials fields span-10 append-6 last">
					<li>Please indicate any materials requested for the meeting</li>
					<li>
						<label for="typeMeetingRequestSamples">
							<input type="checkbox" class="checkbox" name="data[Request][typeMeetingRequestSamples]" id="typeMeetingRequestSamples" /> Product Samples
						</label>
					</li>
					<li>
						<label for="typeMeetingRequestMockups">
							<input type="checkbox" class="checkbox" name="data[Request][typeMeetingRequestMockups]" id="typeMeetingRequestMockups" /> Packaging Mockups
						</label>
					</li>
					<li>
						<label for="typeMeetingRequestRenderings">
							<input type="checkbox" class="checkbox" name="data[Request][typeMeetingRequestRenderings]" id="typeMeetingRequestRenderings" /> Renderings
						</label>
					</li>
				</ul>
				
				<div class="clear">&nbsp;</div>
				
				<div class="requestedMaterialsDetailWrapper">
					<div class="field span-12 append-10 last">
						<label for="typeMeetingRequestDetails">Please provide additional details about the requested materials</label>
						<textarea name="data[Request][typeMeetingRequestDetails]" id="typeMeetingRequestDetails" cols="30" rows="10"></textarea>
					</div>

					<h3>Ship-to Information for Requested Materials</h3>

					<div class="field span-6">
						<label for="typeMeetingshipToName">Name</label>
						<input type="text" class="text" name="data[Request][typeMeetingshipToName]" id="typeMeetingshipToName" value="" />
					</div>

					<div class="field span-6 append-10 last">
						<label for="typeMeetingshipToTitle">Title</label>
						<input type="text" class="text" name="data[Request][typeMeetingshipToTitle]" id="typeMeetingshipToTitle" value="" />
					</div>

					<div class="field span-6">
						<label for="typeMeetingshipToEmail">Email Address</label>
						<input type="text" class="text" name="data[Request][typeMeetingshipToEmail]" id="typeMeetingshipToEmail" value="" />
					</div>

					<div class="field span-6 append-10 last">
						<label for="typeMeetingshipToPhone">Phone Number</label>
						<input type="text" class="text" name="data[Request][typeMeetingshipToPhone]" id="typeMeetingshipToPhone" value="" />
					</div>

					<div class="field span-6 append-18 last">
						<label for="typeMeetingshipToAddress">Mailing Address</label>
						<input type="text" class="text" name="data[Request][typeMeetingshipToAddress]" id="typeMeetingshipToAddress" value="" />
					</div>
				</div><!-- /.requestedMaterialsDetailWrapper -->
			</div><!-- /#retailerMeetingPreparationForm -->
			
			<div id="otherRequestForm" class="requestType">
				<div class="field span-12 append-10 last">
					<label for="typeOtherDetails">Please provide detailed information regarding your request<span class="required">*</span></label>
					<textarea name="data[Request][typeOtherDetails]" id="typeOtherDetails" cols="30" rows="10"></textarea>
				</div>
			</div><!-- /#otherRequestForm -->
		</div><!-- /.projectRequestTypeWrapper -->

		<hr />

		<button type="submit" class="positive">
			<img src="/img/icons/add.png" alt=""/> Request New Operations Project
		</button>
		
		<a class="button negative" href="/requests/dashboard/operations">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->
</form>