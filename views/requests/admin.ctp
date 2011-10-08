     <table cellpadding="0" cellspacing="0">
                <tr>
                        <td>
                                <h1 style="font-size: 20px; font-weight: normal; color: #9c3482;">INCOG Mobility Center Ride Requests</h1>
                                <p>Ride requests pending<br/>Oldest requests are listed first</p>
<table width=70% border="1">
<tr><td>Status</td><td>Rider</td><td>ZIP</td><td>Phone</td><td>Received At</td><td>Description</td><td>Details</td></tr>
<?php

foreach($lstRequests as $Request) {
                $friendlyDate = strftime("%D %H:%M",strtotime($Request["Request"]["created_at"]));
$riderName= $Request["Rider"]["name"];
$riderPhone= $Request["Rider"]["phone"];

$requestZip = $Request["Request"]["zip"];

$requestNote = substr($Request["Request"]["detail"],0,65)."... ";
if ($Request["Request"]["audio_url"]) {
  $requestNote .= "<p>Please view details for audio request.</p>";
}

$requestID = $Request["Request"]["id"];
?>
<tr>
<td><?php echo $Request["Request"]["status"]?></td>
<td><?php echo $riderName?></td>
<td><?php echo $requestZip?></td>
<td><?php echo $riderPhone?></td>
<td><?php echo $friendlyDate?></td>
<td style="width:300px"><?php echo $requestNote?></td><td>
<a href="<?php echo FULL_BASE_URL?>/requests/detail/<?php echo $Request["Request"]["id"]?>">Details</a></td>
</tr>
<?php

}
?>
</table>
