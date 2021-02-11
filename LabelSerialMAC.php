<?php

$title = 'PVT - label with MAC address';

include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<form class="Search" action="LabelSerialMAC.php" method="post">
	<center> <B>SynergyID</B> for the labels/MAC addresses :
	  <input type="SynergyID" placeholder="SynergyID..." name="SynergyID">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</center>
	</form>

<?php

if(!empty($_POST))
{
	$a =$_POST['SynergyID'];

	$sql = "SELECT labels.SynergyID, schools.school_name as School, labels.Signpost_label,labels.label as Lavel, labels.serialnumber as Serial,devicedata.MacWifi, devicedata.MacEthernet  FROM labels INNER JOIN devicedata ON labels.serialnumber = devicedata.SerieNummer INNER JOIN Schools ON schools.SynergyID=labels.synergyID WHERE labels.SynergyID = '$a'";
	$result = $conn->query($sql);

	echo createTable($result, 'mysql');
}

?>

</div>

<?php
include('footer.php');
?>
