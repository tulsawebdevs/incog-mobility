<?php



if ($Request[0]["Request"]["status"] == "dispatched") {
  $claimlink = '<a href="'.FULL_BASE_URL.'/requests/claim/'.$Request[0]["Request"]["id"].'">Claim this Ride</a>';
} else {
  $claimlink = 'This ride has been claimed.';
}

echo '<div class="wrapper">
        <div class="label">Name:</div>
        <div class="name">'.$Request[0]["Rider"]["name"].'</div>
        <br />
        <div class="label">Zip:</div>
        <div class="zip">'.$Request[0]["Request"]["zip"].'</div>
        <br />
        <div class="label">Phone:</div>
        <div class="phone">'.$Request[0]["Rider"]["phone"].'</div>
        <br />
        <div class="label">Detail:</div>
        <div class="notes">'.$Request[0]["Request"]["detail"].'</div>
        <br />
        <div class="label">Notes:</div>
        <div class="notes">'.$Request[0]["Rider"]["notes"].'</div>
        <br />
        <div class="label">Status:</div>
        <div class="notes">'.$Request[0]["Request"]["status"].'</div>
        <br />
        <div class="claimButton">
        '.$claimlink.'</div>
      </div>
      <br />
      <div class="label">Recording:</div>
      <div class="recording">'.$detailAudio.'</div>';

?>