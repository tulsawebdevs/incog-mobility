
<h1>Request Ride</h1>

<div class="messaging">
	<p class="no-js error"><img src="/img/icons/error.png" /> Your browser does not have JavaScript enabled. <a href="http://www.google.com/support/bin/answer.py?answer=23852">Learn how to enable JavaScript</a> before using this application.</p>
</div>

<p>Fields marked with an asterisk (<span class="required">*</span>) are required.</p>

<?php
echo $interform->create("Request"); 
?>		
	<h2 class="expanded">General Information</h2>
	<div class="section">
		<div class="field span-6">
			<label for="ZIP">ZIP<span class="required">*</span></label>
			<input type="text" class="required text" name="data[Request][zip]" id="zip" value="" />
		</div>
		<div class="field span-6">
			<label for="detail">Detailed Description<span class="required">*</span></label>
			<textarea name="data[Request][detail]" id="detail"></textarea>
		</div>

		<button type="submit" class="positive">
			<img src="/img/icons/add.png" alt=""/> Request Ride
		</button>
		
		<a class="button negative" href="/requests/dashboard/">
			<img src="/img/icons/cross.png" alt=""/> Cancel
		</a>
	</div><!-- /.section -->
</form>
