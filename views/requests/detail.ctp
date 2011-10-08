<?php

echo '<div class="wrapper">
        <div class="label">Name:</div>
        <div class="name">'.$res["Rider"]["name"].'</div>
      </div>';


echo "<p>Recording:</p>";
echo $detailAudio;


echo "<pre>";
print_r($Request);
echo "</pre>";

?>

requests/claim/id