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

<?php

if($_GET['type'] == 'webshop'){
	$title = 'Openstaande Webshop';
} else {
	$title = 'Openstaande Leermiddel';
}

if(isset($_GET['namen']) == true){
	$title .= ' Namen';
}

include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

if($_GET['type'] == 'leermiddel' && isset($_GET['namen']) == true){
?>
	
	<center>
	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">School</th>
				<th scope="col">Leerling</th>
				<th width=50 scope="col"><center>Jaar</th>
				<th width=110 scope="col"><center>Contract</th>
				<th scope="col">SKU</th>
				<th scope="col">Vendor SKU</th>
				<th width=100 scope="col"><center>Getekend</th>
				<th width=100 scope="col"><center>Betaald</th>
				<th width=80 scope="col"><center>Type</th>
			</tr>
		</thead>

		<tbody>

		<?php
	
		$sql = "SET lc_time_names = 'nl_BE'"; 
		$data = $conn -> query($sql);
			
		$sql= "SELECT *, `productnumber`
			FROM leermiddel.`tblcontractdetails`
			LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id`
			LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id LEFT JOIN `byod-orders`.`devices` ON `Leermiddel`.`tbltoestelcontractdefinitie`.`SKU`=`byod-orders`.`devices`.`SPSKU`
			WHERE leermiddel.`tblcontractdetails`.`deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum >= '2020-09-01' AND `lengte` = 0";

		$totaal = 0;
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {
				echo '<tr>';
				echo '<td>' . $row['SynergySchoolID'] . '</B> -';
				echo ' <font color=grey size=2><b>' . $row['SchoolNaam'] . '</td>';
				echo '<td width=100><font color=Cornsilk>' . $row['VoornaamLeerling'] . '';
				echo ' <font color=white><b>' . $row['NaamLeerling'] . '</td>';
				echo '<td><center>' . $row['Leerjaar'] . '</td>';
				echo '<td><B><center>' . $row['ContractVolgnummer'] . '</td>';
				echo '<td>' . $row['SKU'] . '</td>';
				echo '<td>' . $row['productnumber'] . '</td>';
				echo '<td data-sort="'. strtotime($row['DatumContractOntvangen']) .'"><center>' . date_format(date_create($row['DatumContractOntvangen']),"d M") . '</td>';
				echo '<td data-sort="'. strtotime($row['DatumVoorschotOntvangen']) .'"><center>' . date_format(date_create($row['DatumVoorschotOntvangen']),"d M") . '</td>';
				echo '<td><b> <font color=teal><center> H </td>';
				echo '</tr>';
				$totaal++;
							}

		} else {
			echo '0 results';
		}

		?>

		
			<h3 align=left>Openstaande leermiddel orders &nbsp&nbsp&nbsp<font size=3 color=grey>(totaal: <?php echo $totaal; ?>)
			
			
		

	</tbody>
</table>
<?php
} elseif($_GET['type'] == 'webshop' && isset($_GET['namen']) == true){
?>
	<h3>Openstaande webshop orders op naam</h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Exact Order Nr</th>
				<th scope="col">Klant</th>
				<th scope="col">Order datum</th>
				<th scope="col">Webshop Order Nr</th>
				<th scope="col">SKU</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">Voornaam leerling</th>
				<th scope="col">Naam leerling</th>
				<th scope="col">Beschrijving toestel</th>
				<th scope="col">School</th>
			</tr>
		</thead>

		<tbody>

		<?php

	$tsql= "select orkrg.ordernr, orkrg.inv_debtor_name, orddat, refer, artcode, orsrg.instruction,
		orkrg.freefield1, orkrg.refer1, orkrg.refer2, oms45,
		(select cmp_name from cicmpy where trim(cmp_code)=trim(freefield1)) as School
		from orkrg with (nolock)
		inner join orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
		where len(orkrg.freefield1)>0
		and (artcode like 'H%' or artcode like 'L%') and lengte=0 and ar_soort NOT LIKE 'P' order by orddat";
		$stmt = sqlsrv_query( $msconn, $tsql);
		if($stmt === false) {
			die( print_r( sqlsrv_errors(), true) );
		}
		$totaal = 0;
		while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
			echo '<tr>';
			echo '<td>' . $row['ordernr'] . '</td>';
			echo '<td>' . $row['inv_debtor_name'] . '</td>';
			echo '<td data-sort="'. strtotime(date_format($row['orddat'], 'd-m-Y')) .'">' . date_format($row['orddat'], 'd-m-Y') . '</td>';
			echo '<td>' . $row['refer'] . '</td>';
			echo '<td>' . $row['artcode'] . '</td>';
			echo '<td>' . $row['freefield1'] . '</td>';
			echo '<td>' . $row['refer1'] . '</td>';
			echo '<td>' . $row['refer2'] . '</td>';
			echo '<td>' . $row['oms45'] . '</td>';
			echo '<td>' . $row['School'] . '</td>';
			echo '</tr>';
			$totaal++;
		}

		?>

		<tr>
			<td scope="col"></th>
			<td scope="col"></th>
			<td scope="col"></th>
			<td scope="col"></th>
			<td scope="col"></th>
			<td scope="col"></th>
			<td scope="col"></th>
			<td scope="col">Totaal</th>
			<td scope="col"><?php echo $totaal; ?></th>
			<td scope="col"></th>
		</tr>

	</tbody>
</table>
<?php
} elseif(isset($_GET['type']) == 'webshop'){
?>
	<h3>Openstaande webshop orders</h3>
	<p style="color:red;">Mits de SPSKU in de webshop klopt</p>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">Magazijn</th>
				<th scope="col">Leverdatum</th>
				<th scope="col">Toestel</th>
				<th scope="col">Webshop Orders</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
<?php
	$totaal = 0;
	$sql = "SELECT id, CONCAT('SP-BYOD-', id) AS orderid, GROUP_CONCAT(id) AS alleorderids, synergyid, SPSKU, warehouse, shipping_date,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
		FROM orders q
		WHERE finance_type = 'Particulier' AND q.deleted != 1
		GROUP BY synergyid, SPSKU
		ORDER BY synergyid, SPSKU";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row['SPSKU']))));
			$tsql= "SELECT count(*) AS aantal
				FROM orkrg with (nolock)
				INNER JOIN orsrg with (nolock) ON orkrg.ordernr=orsrg.ordernr
				INNER JOIN cicmpy with (nolock) ON cicmpy.debnr=orkrg.debnr
				WHERE freefield1='" . $row['synergyid'] ."' AND artcode LIKE '" . $spsku . "%' and ar_soort NOT LIKE 'P' AND lengte=0";
			$stmt = sqlsrv_query( $msconn, $tsql);
			if($stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			$aantalwebshoporders = 0;
			while( $row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
				$aantalwebshoporders = $row2['aantal'];
			}

			if($aantalwebshoporders != '0'){
				echo '<tr>';
				echo '<td>' . $row['orderid'] . '</td>';
				echo '<td>' . $row['synergyid'] . '</td>';
				echo '<td>' . $row['warehouse'] . '</td>';
				echo '<td>' . $row['shipping_date'] . '</td>';
				echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
				echo '<td>' . $aantalwebshoporders . '</td>';

				echo '<td class="">';
				$orderids = explode(',', $row['alleorderids']);
				foreach($orderids as $id){
					echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $id . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
				}
				echo '</td>';
				echo '</tr>';
				}
				$totaal += $aantalwebshoporders;
			}
	} else {

		echo "0 results";

	}

	$conn->close();

	?>
			<tr>
				<td scope="col"></th>
				<td scope="col"></th>
				<td scope="col"></th>
				<td scope="col"></th>
				<td scope="col">Totaal</th>
				<td scope="col"><?php echo $totaal; ?></th>
				<td scope="col"></th>
			</tr>

		</tbody>
	</table>
<?php
} else {
?>
	<h3>Openstaande leermiddel orders</h3>
	<p style="color:red;">Mits de SPSKU in de leermiddel klopt</p>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">Magazijn</th>
				<th scope="col">Leverdatum</th>
				<th scope="col">Toestel</th>
				<th scope="col">Leermiddel Orders</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
<?php
	$totaal = 0;
	$sql = "SELECT CONCAT('SP-BYOD-', id) AS orderid, GROUP_CONCAT(id) AS alleorderids, synergyid, SPSKU, warehouse, shipping_date,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		(SELECT COUNT(*) FROM leermiddel.tblcontractdetails AS a
		LEFT JOIN leermiddel.tbltoestelcontractdefinitie AS b ON b.id = a.toestelcontractdefinitieid
		LEFT JOIN leermiddel.tblschool AS c ON c.id = a.schoolid
		WHERE c.synergyschoolid = q.synergyid AND b.sku = REPLACE(REPLACE(REPLACE(q.spsku, '-O', ''), '-B1', ''), '-B2', '')
		AND ContractOntvangen = '1' AND deleted = '0' AND VoorschotOntvangen IN ('1', '-1') AND lengte = '0' AND ContractVolgnummer LIKE '%2021___') AS leermiddelorders
		FROM orders q
		WHERE finance_type = 'Particulier' AND q.deleted != 1
		GROUP BY synergyid, spsku";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		if($row['leermiddelorders'] != '0'){
			echo '<tr>';
			echo '<td>' . $row['orderid'] . '</td>';
			echo '<td>' . $row['synergyid'] . '</td>';
			echo '<td>' . $row['warehouse'] . '</td>';
			echo '<td>' . $row['shipping_date'] . '</td>';
			echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
			echo '<td>' . $row['leermiddelorders'] . '</td>';


			echo '<td class="">';
			$orderids = explode(',', $row['alleorderids']);
			foreach($orderids as $id){
				echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $id . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
			}
			echo '</td>';
			echo '</tr>';
			$totaal += $row['leermiddelorders'];
		}
	}
} else {

	echo "0 results";

}

$conn->close();

?>

			<tr>
				<td scope="col"></th>
				<td scope="col"></th>
				<td scope="col"></th>
				<td scope="col"></th>
				<td scope="col">Totaal</th>
				<td scope="col"><?php echo $totaal; ?></th>
				<td scope="col"></th>
			</tr>
		</tbody>
	</table>
<?php
}

include('footer2.php');
?>
