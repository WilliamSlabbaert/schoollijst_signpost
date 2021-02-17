<?php

if ($_GET['type'] == 'webshop') {
	$title = 'Openstaande Webshop';
} else {
	$title = 'Openstaande Leermiddel';
}

if (isset($_GET['namen']) == true) {
	$title .= ' Namen';
}

include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

if ($_GET['type'] == 'leermiddel' && isset($_GET['namen']) == true) {
?>
	<h3 class="container-fluid">Openstaande leermiddel orders op naam</h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Naam</th>
				<th scope="col">Voornaam</th>
				<th scope="col">Leerjaar</th>
				<th scope="col">ContractVolgnummer</th>
				<th scope="col">SynergyHID</th>
				<th scope="col">ExactKlantnummer</th>
				<th scope="col">SKU</th>
				<th scope="col">Datum Ondertekend</th>
				<th scope="col">Datum Betaald</th>
				<th scope="col">School</th>
				<th scope="col">School Synergy ID</th>
			</tr>
		</thead>

		<tbody>
			<?php
			$sql = "SELECT * FROM leermiddel.`tblcontractdetails`
			LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id`
			LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
			WHERE `deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum >= '2020-09-01' AND `lengte` = 0";
			$result = $conn->query($sql);
			$ordersCount = 0;
			$data = array();
			while ($row = $result->fetch_assoc()) {
				$temp = array();
				array_push($temp,$row['NaamLeerling'],
				    $row['VoornaamLeerling'],
				    $row['Leerjaar'],
	                $row['ContractVolgnummer'],
	 				$row['SynergyHID'],
					$row['ExactKlantnummer'],
					$row['SKU'],
					'<span Style= visibility:collapse;>' . date("Y-m-d",strtotime($row['DatumContractOntvangen'])).'</span><br>'.date("d-m-Y",strtotime($row['DatumContractOntvangen'])),
					'<span Style= visibility:collapse;>' . date("Y-m-d",strtotime($row['DatumVoorschotOntvangen'])).'</span><br>'.date("d-m-Y",strtotime($row['DatumVoorschotOntvangen'])),
					$row['SchoolNaam'],
					$row['SynergySchoolID']
				);
				array_push($data, $temp);
			}
			?>
			<script>
				let itemArray = <?php echo json_encode($data); ?>
			</script>

		</tbody>
	</table>
	>
<?php
} elseif ($_GET['type'] == 'webshop' && isset($_GET['namen']) == true) {
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
			$tsql = "SELECT orkrg.ordernr, orkrg.inv_debtor_name, orddat, refer, artcode, orsrg.instruction,orkrg.freefield1, orkrg.refer1, orkrg.refer2, oms45,
		(SELECT cmp_name FROM cicmpy WHERE trim(cmp_code)=trim(freefield1)) AS School
		FROM orkrg WITH (NOLOCK)
		INNER JOIN orsrg WITH (NOLOCK) ON orkrg.ordernr=orsrg.ordernr
		WHERE len(orkrg.freefield1)>0
		AND (artcode LIKE 'H%' OR artcode LIKE 'L%') AND lengte=0 AND ar_soort NOT LIKE 'P' ORDER BY orddat";
			$stmt = sqlsrv_query($msconn, $tsql);
			if ($stmt === false) {
				die(print_r(sqlsrv_errors(), true));
			}
			$totaal = 0;
			$data = array();
			while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
				$temp = array();
				array_push($temp, $row['ordernr'], $row['inv_debtor_name'], date_format($row['orddat'], 'Y-m-d'), $row['refer'], $row['artcode'], $row['freefield1'], $row['refer1'], $row['refer2'], $row['oms45'], $row['School']);
				array_push($data, $temp);
			}
			?>
			<script>
				let itemArray = <?php echo json_encode($data); ?>
			</script>
		</tbody>
	</table>
<?php
} elseif (isset($_GET['type']) == 'webshop') {
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

				$data = array();
				while ($row = $result->fetch_assoc()) {

					$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row['SPSKU']))));
					$tsql = "SELECT count(*) AS aantal
				FROM orkrg with (nolock)
				INNER JOIN orsrg with (nolock) ON orkrg.ordernr=orsrg.ordernr
				INNER JOIN cicmpy with (nolock) ON cicmpy.debnr=orkrg.debnr
				WHERE freefield1='" . $row['synergyid'] . "' AND artcode LIKE '" . $spsku . "%' and ar_soort NOT LIKE 'P' AND lengte=0";
					$stmt = sqlsrv_query($msconn, $tsql);
					if ($stmt === false) {
						die(print_r(sqlsrv_errors(), true));
					}
					$aantalwebshoporders = 0;
					while ($row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
						$aantalwebshoporders = $row2['aantal'];
					}
					if ($aantalwebshoporders != '0') {
						$temp = array();
						$orderClicks = '';
						$orderids = explode(',', $row['alleorderids']);
						foreach ($orderids as $id) {
							$orderClicks .= '<a href="' . hasAccessForUrl('delivery.php?orderid=' . $id . '', false) . '" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
						}
						array_push($temp, $row['orderid'], $row['synergyid'], $row['warehouse'], $row['shipping_date'], $row['devicebeschrijving'] . " <br><span class=smalltext>" . $row['SPSKU'] . "</span>", $aantalwebshoporders, $orderClicks);
						array_push($data, $temp);
					}
				}
			} else {
				echo "0 results";
			}
			$conn->close();
			?>
			<script>
				let itemArray = <?php echo json_encode($data); ?>
			</script>
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

				$data = array();
				while ($row = $result->fetch_assoc()) {

					if ($row['leermiddelorders'] != '0') {
						$temp = array();
						$orderids = explode(',', $row['alleorderids']);
						foreach ($orderids as $id) {
							echo '<a href="' . hasAccessForUrl('delivery.php?orderid=' . $id . '', false) . '" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
						}
						/*
						echo '<tr>';
						echo '<td>' . $row['orderid'] . '</td>';
						echo '<td>' . $row['synergyid'] . '</td>';
						echo '<td>' . $row['warehouse'] . '</td>';
						echo '<td>' . $row['shipping_date'] . '</td>';
						echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
						echo '<td>' . $row['leermiddelorders'] . '</td>';


						echo '<td class="">';
						
						echo '</td>';
						echo '</tr>';*/
						$totaal += $row['leermiddelorders'];
					}
				}
			} else {

				echo "0 results";
			}

			$conn->close();

			?>

			<tr>
				<td scope="col">
					</th>
				<td scope="col">
					</th>
				<td scope="col">
					</th>
				<td scope="col">
					</th>
				<td scope="col">Totaal</th>
				<td scope="col"><?php echo $totaal; ?></th>
				<td scope="col">
					</th>
			</tr>
		</tbody>
	</table>
<?php
}
include('footer.php');
?>