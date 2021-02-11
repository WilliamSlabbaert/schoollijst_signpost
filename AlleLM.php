<?php

$title = 'PVT - alle leermiddel contracten';

include('head.php');
include('nav.php');
include('conn.php');

?>
<style>.dataTables_paginate a {
    color: #00ADBD;
		padding: 8px 15px;
    text-decoration: none;
    transition: background-color .7s;
		margin-top:0.5em;
}

.dataTables_paginate{margin-top:0.5em}

.dataTables_paginate a.current {
    color: white;
		font-size: 16;
		border-style: solid;
		border-width: 1 ;
		border-color: white;
		background-color: #00ADBD;
}

.dataTables_paginate a:hover {background-color: #00ADBD; color:white}

tr:hover{background-color: #00ADBD;}

.dataTables_length label {
    color: #00ADBD;
	padding: 0px 40px;
}
</style>
<div class="body">


<?php

	$sql = "SET lc_time_names = 'nl_BE'"; 
	$result = $conn -> query($sql);

	$sql = "SELECT CONCAT(`SynergySchoolID`,'<br><font color=grey size=1>',`SchoolNaam`) AS School, 
	 CONCAT('<font color=lime><center>',(DATE_FORMAT(CASE WHEN `DatumVoorschotOntvangen` IS NULL OR `DatumContractopgemaakt` IS NULL THEN NULL WHEN `DatumVoorschotOntvangen` > `DatumContractopgemaakt`  THEN `DatumVoorschotOntvangen` ELSE `DatumContractopgemaakt` END,'%d %b ') )) AS OK, 
	 CONCAT('<B><font color=#eb6421>',`ContractVolgnummer`,'<BR> </B> <I><font size=2><font color=white> ',`VoornaamLeerling`,'</I> <B>',`NaamLeerling`) AS Leerling,
     DATE_FORMAT(`DatumContractopgemaakt`,'%d %b %Y') AS Opgemaakt, 
	 CASE WHEN `ContractOntvangen` THEN CONCAT ('<font color=green><center>',DATE_FORMAT(`DatumContractOntvangen`,'%d %b ')) ELSE '<center><font color=red>X' END AS Ontvangen,
	 CONCAT('<center>',`tblcontractdetails`.`Waarborg`,' €') AS Borg,
	 CASE WHEN `DatumVoorschotOntvangen` IS NOT NULL THEN (CASE WHEN `tblcontractdetails`.`BedragVoorschotOntvangen`=`tblcontractdetails`.`Waarborg` THEN CONCAT ('<center><font color=green>',`BedragVoorschotOntvangen`,' €') ELSE CONCAT ('<center><font color=red>',`BedragVoorschotOntvangen`,' €<br><font size=2><b>incorrect') END) ELSE IF(`tblcontractdetails`.`Waarborg`=0,'<font color=green><center>OK','<center><font color=red>X') END AS Voorshot,
     CASE WHEN `DatumVoorschotOntvangen` IS NOT NULL THEN CONCAT ('<font color=green><center>',DATE_FORMAT(`DatumVoorschotOntvangen`,'%d %b')) ELSE '<center><font color=red>X' END AS Op, 
	 CASE WHEN  `lengte` = 0 THEN '<font color=red><center><B>X' ELSE CONCAT('<font color=green><center>',`lengte`) END AS Geleverd,	
	 DATE_FORMAT(`tblcontractdetails`.`StartDatum`,'%d %b') AS START,
	 
	 CONCAT('<center>',`tblcontractdetails`.`Huurprijs`,' €') AS Huur
			FROM leermiddel.`tblcontractdetails`
			LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id`
			LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
			WHERE `deleted` = 0 
			ORDER BY `tblcontractdetails`.`StartDatum` DESC,`SynergyHID`";
		
	/* , CONCAT ('<TR><td colspan=13><B>',`instruction`,'<center></B>','---',`SKU`,'<br><font color=grey>',`OmschrijvingToestel`,'</TD></TR>') AS Toestel */ 
			
	$result = $conn->query($sql);
	echo "<h4 align=left><font color=#00ADBD>&nbsp&nbsp&nbspAlle leermiddel contracten</H4>";
	echo createTable($result, 'mysql');


?>

</div>

<?php
include('footer2.php');
?>
