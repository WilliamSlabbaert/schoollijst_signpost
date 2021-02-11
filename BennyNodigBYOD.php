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

.dataTables_paginate a:hover {background-color: #00ADBD; color:white;}

tr:hover{background-color: #00ADBD40;}

.dataTables_length label {
    color: #00ADBD;
	padding: 0px 40px;
}
</style>

<?php


	$title = 'Openstaande orders';






include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');


?>
	
	<center>
	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th width=80 scope="col"><center>Type</th>
				<th scope="col">SKU</th>
				<th scope="col">Vendor SKU</th>
				<th scope="col">Aantal</th>
			</tr>
		</thead>

		<tbody>

		<?php
	
		$sql = "SET lc_time_names = 'nl_BE'"; 
		$data = $conn -> query($sql);
			
		$sql= "SELECT SKU,productnumber, count(*) as aantal
			FROM leermiddel.`tblcontractdetails`
			LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id`
			LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id LEFT JOIN `byod-orders`.`devices` ON `Leermiddel`.`tbltoestelcontractdefinitie`.`SKU`=`byod-orders`.`devices`.`SPSKU`
			WHERE leermiddel.`tblcontractdetails`.`deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum >= '2020-09-01' AND `lengte` = 0 group by SKU,productnumber";

		$totaal = 0;
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {
				echo '<tr>';
				echo '<td><b> <font color=teal><center> H </td>';
				echo '<td>' . $row['SKU'] . '</td>';
				echo '<td>' . $row['productnumber'] . '</td>';
				echo '<td><b> <font color=teal><center>' . $row['aantal'] .' </td>';
				echo '</tr>';
				$totaal+=$row['aantal'];
			}
		}

		$sql= "SELECT orders.SPSKU,`productnumber`,SUM(`amount`) AS aantal FROM orders LEFT JOIN devices ON orders.`SPSKU`=devices.`SPSKU` LEFT JOIN schools ON orders.synergyid = schools.synergyid WHERE (`finance_type` = 'School' or `finance_type` LIKE 'School%') AND `status`<>'uitgeleverd' GROUP BY  `SPSKU`";
		
		$totaal2 = 0;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					echo '<tr>';
					echo '<td><b> <font color=teal><center> S </td>';
					echo '<td>' . $row['SPSKU'] . '</td>';
					echo '<td>' . $row['productnumber'] . '</td>';
					echo '<td><b> <font color=teal><center>'. $row['aantal'].'</td>';
					echo '</tr>';
					$totaal2 +=$row['aantal'];
				}
		

		} 
		
		$tsql= "select count(orkrg.ordernr) as aantal, artcode
		from orkrg with (nolock)
		inner join orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
		where len(orkrg.freefield1)>0
		and (artcode like 'H%' or artcode like 'L%') and lengte=0 and ar_soort NOT LIKE 'P' group by artcode";
		$stmt = sqlsrv_query( $msconn, $tsql);
		if($stmt === false) {
			die( print_r( sqlsrv_errors(), true) );
		}
		$totaal3 = 0;
		while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
			echo '<tr>';
			echo '<td><b> <font color=teal><center> W </td>';
			echo '<td>' . $row['artcode'] . '</td>';
			echo '<td>' . $row['artcode'] . '</td>';
			echo '<td><b> <font color=teal><center>' . $row['aantal'] . '</td>';
			echo '</tr>';
			$totaal3+=$row['aantal'];
		}
		
		/* else {
			echo '0 results';
		}*/
		$alles=$totaal+$totaal2+$totaal3
		?>

		
		<h4 align=left><font color=#00ADBD>&nbsp&nbsp&nbsp # Openstaande orders (LM, WS en SCHOOL) &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<font size=3 color=grey>(leermiddel: <?php echo "$totaal, schoolorder: $totaal2, webshoporders: $totaal3, <b>grand total $alles)";?>
				
	</tbody>
</table>

<?php

include('footer2.php');
?>
