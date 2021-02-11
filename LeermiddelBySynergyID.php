<?php

$title = 'PVT - Leermiddel contracten';

include('head.php');
include('nav.php');
include('LMconn.php');

?>

<div class="body">

	<form class="Search" action="Leermiddelbysynergyid.php" method="post">
	<center> <B>SynergyID</B> for the Leermiddel contacts you want to see :
	  <input type="SynergyID" placeholder="SynergyID..." name="SynergyID">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</center>
	</form>

<?php

if(!empty($_POST))
{
	$a =$_POST['SynergyID'];

	$sql = "SELECT CONCAT(`SynergySchoolID`,' <BR><B> ',`SchoolNaam`) AS School,tblcontractdetails.`ContractVolgnummer` AS LMContract,tblcontractdetails.`StartDatum`,`DatumVoorschotOntvangen` AS Voorschot,`DatumContractOntvangen` AS contract,CONCAT(`NaamLeerling`,' ',`VoornaamLeerling`) AS Leerling,`NaamToestel` AS omschrijving ,concat(`instruction`,'<font color=grey><I> (',`lengte`,')') AS toestel  FROM `tblcontractdetails` INNER JOIN `tblschool` ON `SchoolID`=`tblschool`.`id` INNER JOIN `tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id` WHERE deleted=0 AND SynergySchoolID = $a ORDER BY School, startdatum";
	$result = $conn->query($sql);

	echo createTable($result, 'mysql');
}

?>

</div>

<?php
include('footer2.php');
?>
