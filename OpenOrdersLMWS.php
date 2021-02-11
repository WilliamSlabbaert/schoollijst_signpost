<?php

$title = 'PVT - alle openstaande Leermiddel en Webshop orders';

include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

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
		function createTable2($result, $type) {
				if($type == 'mysql' || $type == 'leermiddel' || $type == 'byod') {
					
					$table = '';
					for ($x=0;$x<mysqli_num_fields($result);$x++) $table .= '<th>'.mysqli_field_name($result,$x).'</th>';
					$table .= '</thead>';
					while ($rows = mysqli_fetch_assoc($result)) {
						$table .= '<tr>';
						foreach ($rows as $row) $table .= '<td>'.$row.'</td>';
						$table .= '</tr>';
					}
					//mysql_data_seek($result,0); //if we need to reset the mysql result pointer to 0
					return $table;

				} else if($type == 'mssql' || $type == 'exact' || $type == 'synergy') {

					$table = '';
					while ($rows = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
						$table .= '<tr>';
						foreach ($rows as $row) {
							$table .= '<td>'.$row.'</td>';
						}
						$table .= '</tr>';
						
					}
					return $table;

				}
			}
	$sql = "SET lc_time_names = 'nl_BE'"; 
	$result = $conn -> query($sql);

	$sql = "SELECT 
			CONCAT('<B><font color=#eb6421>',`ContractVolgnummer`,' - </B> <I><font size=2><font color=#818380> ',`VoornaamLeerling`,'</I> <B>',`NaamLeerling`) AS Leerling,
			CONCAT(`SynergySchoolID`,' <font color=grey size=2>',`SchoolNaam`) AS School, 
			CONCAT('<B><font color=green><center>',(DATE_FORMAT(CASE WHEN `DatumVoorschotOntvangen` IS NULL OR `DatumContractopgemaakt` IS NULL THEN NULL WHEN `DatumVoorschotOntvangen` > `DatumContractopgemaakt`  THEN `DatumVoorschotOntvangen` ELSE `DatumContractopgemaakt` END,'%d %b %Y') )) AS OK, 
			'H' AS type,
			`SKU` as Toestel
				FROM leermiddel.`tblcontractdetails`
				LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id`
				LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
				WHERE `deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum >= '2020-09-01' AND `lengte` = 0 
				ORDER BY `tblcontractdetails`.`StartDatum` DESC,`SynergyHID`";
		
	$result = $conn->query($sql);

	$tsql= "SET LANGUAGE DUTCH";
	$tsql=	"select 
			CONCAT('<B><font color=#eb6421>',refer,' - </B> <I><font size=2><font color=#818380> ',orkrg.refer1,'</I> <B>',orkrg.refer2) AS Leerling,
			CONCAT(orkrg.freefield1,' <font color=grey size=2>',(select cmp_name from cicmpy where trim(cmp_code)=trim(freefield1))) AS School,
			CONCAT('<B><font color=green><center>',FORMAT(orddat,'dd MMM yyyy')) as OK,
			'W' AS type,
			artcode as Toestel
				from orkrg with (nolock)
				inner join orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
				where len(orkrg.freefield1)>0
				and (artcode like 'H%' or artcode like 'L%') and lengte=0 and ar_soort NOT LIKE 'P' order by orddat";

	$stmt = sqlsrv_query( $msconn, $tsql);
		
	echo "<h4 align=left><font color=#00ADBD>&nbsp&nbsp&nbspAlle openstaande orders (Leermiddel en Webshop)</H4>";
	echo '<table class="table" id="table"><thead class="thead-dark">'.createTable2($result, 'mysql');
	echo createTable2($stmt, 'exact');	
	echo '</table>';

?>

</div>

<?php
include('footer.php');
?>
