<?php 

 class MailComponent
 {
    var $controller=true;
	  
	function startup(&$controller)
    {
        // This method takes a reference to the controller which is loading it.
        // Perform controller initialization here.
    }
	/*
	list of To addresses
	@var	array
	*/
	var $sendto = array();
	/*
	@var	array
	*/
	var $acc = array();
	/*
	@var	array
	*/
	var $abcc = array();
	/*
	paths of attached files
	@var array
	*/
	var $aattach = array();
	/*
	list of message headers
	@var array
	*/
	var $xheaders = array();
	/*
	message priorities referential
	@var array
	*/
	var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
	/*
	character set of message
	@var string
	*/
	
	var $charset = "iso-8859-1";
	var $ctencoding = "8bit";
	var $receipt = 0;
	var $checkAddress = true;
	var $body;
	var $boundary;
	
	

/*

	Mail contructor
	
*/

function getMail()
{
	$this->autoCheck( true );
	$this->boundary= "--" . md5( uniqid("myboundary") );
}


/*		

activate or desactivate the email addresses validator
ex: autoCheck( true ) turn the validator on
by default autoCheck feature is on

@param boolean	$bool set to true to turn on the auto validation
@access public
*/
function autoCheck( $bool )
{
	if( $bool )
		$this->checkAddress = true;
	else
		$this->checkAddress = false;
}


/*

Define the subject line of the email
@param string $subject any monoline string

*/
function Subject( $subject )
{
	$this->xheaders['Subject'] = strtr( $subject, "\r\n" , "  " );
}


/*

set the sender of the mail
@param string $from should be an email address

*/
 
function From( $from )
{

	if( ! is_string($from) ) {
		echo "Class Mail: error, From is not a string";
		exit;
	}
	$this->xheaders['From'] = $from;
}

/*
 set the Reply-to header 
 @param string $email should be an email address

*/ 
function ReplyTo( $address )
{

	if( ! is_string($address) ) 
		return false;
	
	$this->xheaders["Reply-To"] = $address;
		
}


/*
add a receipt to the mail ie.  a confirmation is returned to the "From" address (or "ReplyTo" if defined) 
when the receiver opens the message.

@warning this functionality is *not* a standard, thus only some mail clients are compliants.

*/
 
function Receipt()
{
	$this->receipt = 1;
}


/*
set the mail recipient
@param string $to email address, accept both a single address or an array of addresses

*/

function To( $to )
{

	// TODO : test validit sur to
	if( is_array( $to ) )
		$this->sendto= $to;
	else 
		$this->sendto[] = $to;

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->sendto );

}


/*		Cc()
 *		set the CC headers ( carbon copy )
 *		$cc : email address(es), accept both array and string
 */

function Cc( $cc )
{
	if( is_array($cc) )
		$this->acc= $cc;
	else 
		$this->acc[]= $cc;
		
	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->acc );
	
}



/*		Bcc()
 *		set the Bcc headers ( blank carbon copy ). 
 *		$bcc : email address(es), accept both array and string
 */

function Bcc( $bcc )
{
	if( is_array($bcc) ) {
		$this->abcc = $bcc;
	} else {
		$this->abcc[]= $bcc;
	}

	if( $this->checkAddress == true )
		$this->CheckAdresses( $this->abcc );
}


/*		Body( text [, charset] )
 *		set the body (message) of the mail
 *		define the charset if the message contains extended characters (accents)
 *		default to us-ascii
 *		$mail->Body( "ml en franais avec des accents", "iso-8859-1" );
 */
function Body( $body, $charset="" )
{
	$this->body = $body;
	
	if( $charset != "" ) {
		$this->charset = strtolower($charset);
		if( $this->charset != "us-ascii" )
			$this->ctencoding = "8bit";
	}
}


/*		Organization( $org )
 *		set the Organization header
 */
 
function Organization( $org )
{
	if( trim( $org != "" )  )
		$this->xheaders['Organization'] = $org;
}


/*		Priority( $priority )
 *		set the mail priority 
 *		$priority : integer taken between 1 (highest) and 5 ( lowest )
 *		ex: $mail->Priority(1) ; => Highest
 */
 
function Priority( $priority )
{
	if( ! intval( $priority ) )
		return false;
		
	if( ! isset( $this->priorities[$priority-1]) )
		return false;

	$this->xheaders["X-Priority"] = $this->priorities[$priority-1];
	
	return true;
	
}


/*	
 Attach a file to the mail
 
 @param string $filename : path of the file to attach
 @param string $filetype : MIME-type of the file. default to 'application/x-unknown-content-type'
 @param string $disposition : instruct the Mailclient to display the file if possible ("inline") or always as a link ("attachment") possible values are "inline", "attachment"
 */

function Attach( $filename, $filetype = "", $disposition = "inline" )
{
	// TODO : si filetype="", alors chercher dans un tablo de MT connus / extension du fichier
	if( $filetype == "" )
		$filetype = "application/x-unknown-content-type";
		
	$this->aattach[] = $filename;
	$this->actype[] = $filetype;
	$this->adispo[] = $disposition;
}

/*

Build the email message

@access protected

*/
function BuildMail()
{

	// build the headers
	$this->headers = "";
//	$this->xheaders['To'] = implode( ", ", $this->sendto );
	
	if( count($this->acc) > 0 )
		$this->xheaders['CC'] = implode( ", ", $this->acc );
	
	if( count($this->abcc) > 0 ) 
		$this->xheaders['BCC'] = implode( ", ", $this->abcc );
	

	if( $this->receipt ) {
		if( isset($this->xheaders["Reply-To"] ) )
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders["Reply-To"];
		else 
			$this->xheaders["Disposition-Notification-To"] = $this->xheaders['From'];
	}
	
	if( $this->charset != "" ) {
		$this->xheaders["Mime-Version"] = "1.0";
		$this->xheaders["Content-Type"] = "text/html; charset=$this->charset";
		$this->xheaders["Content-Transfer-Encoding"] = $this->ctencoding;
	}

	$this->xheaders["X-Mailer"] = "Php/libMailv1.3";
	
	// include attached files
	if( count( $this->aattach ) > 0 ) {
		$this->_build_attachement();
	} else {
		$this->fullBody = $this->body;
	}

	reset($this->xheaders);
	while( list( $hdr,$value ) = each( $this->xheaders )  ) {
		if( $hdr != "Subject" )
			$this->headers .= "$hdr: $value\n";
	}
	

}

/*		
	fornat and send the mail
	@access public
	
*/ 
function Send()
{
	$this->BuildMail();
	if (is_array($this->sendto)) {
		$this->sendto = array_unique($this->sendto);
	}
	if (!strstr($_SERVER['SERVER_NAME'],"onecare")) {
		$removeAddress = "webmaster@onecaredev.com";
		Configure::write("debug",2);
		pr("no mail for $removeAddress .. dev machine");
		Configure::write("debug",0);
		//don't send to TM and friends except @ prod
		foreach($this->sendto as $i=>$addr) {
			if (strtolower($addr) == $removeAddress) {
				unset($this->sendto[$i]);
			}
		}
	}
	$this->strTo = implode( ", ", $this->sendto );
//	pr("mail going to ".$this->strTo);
	// envoie du mail
	$res = @mail( $this->strTo, $this->xheaders['Subject'], $this->fullBody, $this->headers );

   return $res;

}



/*
 *		return the whole e-mail , headers + message
 *		can be used for displaying the message in plain text or logging it
 */

function Get()
{
	$this->BuildMail();
	$mail = "To: " . $this->strTo . "\n";
	$mail .= $this->headers . "\n";
	$mail .= $this->fullBody;
	return $mail;
}


/*
	check an email address validity
	@access public
	@param string $address : email address to check
	@return true if email adress is ok
 */
 
function ValidEmail($address)
{
	if( ereg( ".*<(.+)>", $address, $regs ) ) {
		$address = $regs[1];
	}
 	if(ereg( "^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|COM|com|COM|GOV|gov|MIL|mil|ORG|org|EDU|edu|INT|int)\$",$address) ) 
 		return true;
 	else
 		return false;
}


/*

	check validity of email addresses 
	@param	array $aad - 
	@return if unvalid, output an error message and exit, this may -should- be customized

 */
 
function CheckAdresses( $aad )
{
	for($i=0;$i< count( $aad); $i++ ) {
//		echo "checking add ".$aad[$i];
		if( ! $this->ValidEmail( $aad[$i]) ) {
			pr("Class Mail, method Mail : invalid address '$aad[$i]' <br/> 
				automatically appending onecareco.com in case this is a KT user name");
			$aad[$i] = $aad[$i]."@onecareco.com";
			if(	! $this->ValidEmail( $aad[$i]) ) {
			trigger_error("Class still invalid address '$aad[$i]' automatically appending onecare.com");
			}
			//
//			exit;
		}
	}
}


/*
 check and encode attach file(s) . internal use only
 @access private
*/

function _build_attachement()
{

if (!isset($this->boundary)) {
//	$this->boundary= "--" . md5( uniqid("myboundary") );
}
	$this->xheaders["Content-Type"] = "multipart/mixed;\n boundary=\"".$this->boundary."\"";

	$this->fullBody = "This is a multi-part message in MIME format.\n--".$this->boundary."\n";
	$this->fullBody .= "Content-Type: text/html; charset=$this->charset\nContent-Transfer-Encoding: $this->ctencoding\n\n" . $this->body ."\n";
	
	$sep= chr(13) . chr(10);
	
	$ata= array();
	$k=0;
	
	// for each attached file, do...
	for( $i=0; $i < count( $this->aattach); $i++ ) {
		
		$filename = $this->aattach[$i];
		$basename = basename($filename);
		$ctype = $this->actype[$i];	// content-type
		$disposition = $this->adispo[$i];
		
		if( ! file_exists( $filename) ) {
			echo "Class Mail, method attach : file $filename can't be found"; exit;
		}
		$subhdr= "--$this->boundary\nContent-type: $ctype;\n name=\"$basename\"\nContent-Transfer-Encoding: base64\nContent-Disposition: $disposition;\n  filename=\"$basename\"\n";
		$ata[$k++] = $subhdr;
		// non encoded line length
		$linesz= filesize( $filename)+1;
		$fp= fopen( $filename, 'r' );
		$ata[$k++] = chunk_split(base64_encode(fread( $fp, $linesz)));
		fclose($fp);
	}
	$this->fullBody .= implode($sep, $ata);
 
 
 
 }


}

class OnecareMail extends MailComponent {
	var $defaultRecipient = "innerechotest@gmail.com";
	var $defaultCC;



	function __construct($reply_to = "noreply@onecareco.com" )  {
		$this->Subject("oneCARE Creative Request");
		$this->From("oneCARE Request System <$reply_to>");
		$this->defaultCC = "innerechotest@gmail.com";
		$this->charset = "iso-8859-15";
		$this->ReplyTo($reply_to);
		$this->boundary= "--" . md5( uniqid("myboundary") );

	}

	function buildFromRequest($Request,$recipient) {
//		foreach($Request as $field=>$val) {
//			//creates vars named $project_name, etc. from keys of the model
//			if(!isset($$field)) $$field == $val;
//		}
		$friendlyDate = strftime("%D",strtotime($Request["created_on"]));
		$DOMAIN = FULL_BASE_URL;
		if($recipient == "requestor") {
			$body =<<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>oneCARE</title>
</head>

<body style="font: 13px sans-serif; line-height: 1.22; background-color: #fff; color: #444;">
	<table width="600" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<h1 style="font-size: 20px; font-weight: normal; color: #9c3482;">Request New Creative Project</h1>

				<h2 style="font-size: 18px; font-weight: normal; color: #133d6f;">Your request was successfully submitted.</h2>

				<p>{$Request["requestor_name"]},

				<p>Your new project request, <strong>{$Request["project_name"]}</strong>, was successfully submitted on 
				<strong>{$friendlyDate}</strong>. We will contact you at this email address for any necessary follow-up questions or feedback. 
				Please keep this email for your records.</p>
				
				<p><a href="{$DOMAIN}/requests/detail/{$Request["id"]}">View the details for this project</a>.</p>
				
				<p>The project ID for this request is <strong>{$Request["id"]}</strong>.</p>

				<p style="color: #999;">This notification was automatically generated by the oneCARE Intranet.</p>
				
				<img src="{$DOMAIN}/img/logo-small.png" width="62" height="48" alt="oneCARE logo" />
			</td>
		</tr>
	</table>
</body>
</html>			
EOF;
		}else if ($recipient=="artDept") {
			$body =<<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>oneCARE</title>
</head>

<body style="font: 13px sans-serif; line-height: 1.22; background-color: #fff; color: #444;">
	<table width="600" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<h1 style="font-size: 20px; font-weight: normal; color: #9c3482;">New Project Request</h1>
				
				<p>A new {$Request["category"]} project for <strong>{$Request["brand"]}</strong> has been requested by {$Request["requestor_name"]} ({$Request["requestor_eml"]}).</p>
				
				<p><a href="{$DOMAIN}/requests/detail/{$Request["id"]}">View the details for this project</a>.</p>
				
				<p>The project ID for this request is <strong>{$Request["id"]}</strong>.</p>

				<p style="color: #999;">This notification was automatically generated by the oneCARE Intranet.</p>
				
				<img src="{$DOMAIN}/img/logo-small.png" width="62" height="48" alt="oneCARE logo" />
			</td>
		</tr>
	</table>
</body>
</html>			
EOF;
		}else{
			trigger_error("buildFromRequest does not have a tpl for recipT: ");
			pr($recipient);
			exit;
			
		}
		$this->Body($body);
	}
	function buildFromTemplate($file,$vars) {
		if (file_exists("files/$file")) {
			$content = file_get_contents("files/".$file);
		}else{
			$content = file_get_contents($file);
		}
		foreach($vars as $marker=>$replacement) {
			$content = str_replace(
				"@$marker@", $replacement, $content);
			
		}
		// In case any of our lines are larger than 70 characters, we should use wordwrap()
		$content = wordwrap($content, 70);
		$this->Body($content);
		return $content;
	}
}
?>