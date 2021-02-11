<?php
$title = $_GET['synergyid'] . ' details';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');
?>

<div class="body">
<?php

if (isset($_POST['labelchange']) !== false) {

	$synergyid = mysqli_real_escape_string($conn, $_POST['synergyid']);
	$signpost_label = strtoupper(mysqli_real_escape_string($conn, $_POST['signpost_label']));

	$sql = "UPDATE schools
			SET signpost_label = '" . $signpost_label . "', schoolhistory =
			CASE WHEN schoolhistory IS NULL
				THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Signpost Label aangepast naar \"" . $signpost_label . "\"')
				ELSE concat(schoolhistory,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Signpost Label aangepast naar \"" . $signpost_label . "\"')
			END
			WHERE synergyid = '" . $synergyid . "'";

	if ($conn->query($sql) === TRUE) {

		echo "Record updated successfully";

		// redirect
		$URL = "school.php?synergyid=" . $synergyid;
		if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
		else { header("Location: $URL"); }
		exit;

	} else {
		echo "Error updating record: " . $conn->error;
	}

	die();

} elseif (isset($_GET['labelchange']) !== false) {

	echo '<h1>Signpost Label van school ' . $_GET['synergyid'] . '</h1>
		<form action="school.php" method="post">
			<label for="id">School:</label><br>
			<input type="text" id="statuschange" name="labelchange" value="true" hidden>
			<input type="text" id="synergyid" name="synergyid" value="' . $_GET['synergyid'] . '" readonly class="form-control"><br>
			<label for="signpost_label">Signpost Label:</label><br>';

		echo '<div class="row">
				<div class="col"><input type="text" title="Voer het label in zonder 20-0001" class="form-control" placeholder="" Name="signpost_label" required></div>
				<div class="col"><input type="text" value="20-0001" readonly class="form-control"></div>
			</div><br>
			<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

	die();

} elseif (isset($_POST['schoolid']) !== false) {

	$asignee = mysqli_real_escape_string($conn, $_POST['asignee']);
	$status_notes = mysqli_real_escape_string($conn, $_POST['status_notes']);

	$sql = "UPDATE `byod-orders`.`schools` SET schoolasignee='" . $asignee . "', schoolstatus='" . $status_notes . "', schoolhistory =
	CASE WHEN schoolhistory IS NULL
		THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Toegewezen op " . $asignee . " met status " . $status_notes . "')
		ELSE concat(schoolhistory,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Toegewezen op " . $asignee . " met status " . $status_notes . "')
	END
	WHERE id='" . $_POST['schoolid'] . "'";

	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo $_POST['schoolid'] . " is aangepast.<br>
			Dit order is nu toegewezen op <em>" . $asignee . "</em> met status '" . $status_notes . "'.<br>";

		echo '<a href="'. hasAccessForUrl('school.php?synergyid=' . $_POST['synergyid'] . '', false).'"><button class="btn btn-dark">Terug naar het overzicht</button></a>';
		echo '</div>';
	} else {
		echo "Error updating record: " . $conn->error;
	}

	die();

}


echo '<h4>School info</h4>';
$sql = "SELECT * FROM schools
	WHERE synergyid = " . $_GET['synergyid'] . "";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		echo '
		<strong>Synergy ID:</strong> ' . $row['synergyid'] . '<br>
		<strong>School:</strong> ' . $row['school_name'] . '<br>
		<strong>Sales:</strong> ' . $row['contact'] . '<br>
		<strong>Image intakes:</strong> <a href="'. hasAccessForUrl('mail-verzenden.php?q=' . $row['guid'] . '', false).'">Klik hier</a><br><br>';

		echo '<strong>Signpost Label: </strong> ' . $row['signpost_label'];
		if(hasRole($role, ['management'])){
			echo '<br><a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '&labelchange', false).'">( Klik hier het signpost label aan te passen )</a>';
		}
		echo '<br><br>';

		$schoolid = $row['id'];
		$schoolasignee = $row['schoolasignee'];
		$schoolhistory = $row['schoolhistory'];
		$schoolstatus = $row['schoolstatus'];

		if (hasRole($role, ['management', 'software', 'webshop'])) {
			echo '
			<form action="school.php" method="post">
				<input type="text" name="synergyid" id="synergyid" value="' . $row['synergyid'] . '" hidden>
				<input type="text" name="schoolid" id="schoolid" value="' . $schoolid . '" hidden>
				<input type="text" name="user" id="user" value="' . $loginname . '" hidden>
				<label for="asignee"><b>Toegewezen op:</b></label><br>
				<select name="asignee" class="form-control" id="asignee">';

			if ($schoolasignee !== '') {
				echo '<option value="' . $schoolasignee . '">' . $schoolasignee . '</option>';
			}

			echo '<option value="">Niet toegewezen</option>
				<option value="Alain.leuregans">Alain</option>
				<option value="Bart">Bart C</option>
				<option value="Ismail">Ismail</option>
				<option value="Jelle">Jelle</option>
				<option value="Jens">Jens</option>
				<option value="Joe.specker">Joe</option>
				<option value="Jordy">Jordy</option>
				<option value="Mike">Mike</option>
				<option value="Nathalie.desmaele">Nathalie</option>
				<option value="Quinten">Quinten</option>
				<option value="Thomas">Thomas</option>
				</select>
				<br>
				<label for="status_notes"><b>Status beschrijving:</b></label><br>
				<textarea id="status_notes" name="status_notes" rows="4" class="form-control">' . $schoolstatus . '</textarea><br>

				<input type="submit" value="Opslaan" class="btn btn-danger"><br>
				</form><br>
				';
		}

		echo '<h4>Geschiedenis</h4>';

		if($schoolhistory !== ''){
			echo $schoolhistory . '<br>';
		} else {
			echo 'Nog geen geschiedenis';
		}
		echo '<br><br>';

	}
}

echo '<h4>Alle orders</h4>';
?>
	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Aantal</th>
				<th scope="col">Status</th>
				<th scope="col">Toegewezen op</th>
				<th scope="col">Sales</th>
				<th scope="col">Plaats</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
<?php

$ordersql = "SELECT *, q.id AS orderid,
	(SELECT SUM(`device1`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts1,
	(SELECT SUM(`device2`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts2,
	(SELECT SUM(`device3`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts3,
	(SELECT SUM(`device4`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts4,
	((SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON images2019.synergyid = schools.synergyidold WHERE schools.synergyid = q.synergyid AND okvoor2020 = '1')+
	(SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND confirmed = '1')) AS allimages,
	((SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON images2019.synergyid = schools.synergyidold WHERE schools.synergyid = q.synergyid AND status = 'done')+
	(SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND status = 'done')) AS imagesdone,
	( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
	FROM `byod-orders`.orders q LEFT JOIN `byod-orders`.schools ON q.synergyid = schools.synergyid WHERE q.synergyid = '" . $_GET['synergyid'] . "' ORDER BY q.synergyid";
$orderresult = $conn->query($ordersql);
$schools = "";
$subtotaal = 0;
$totaal = 0;
$totaalforecasts = 0;

if ($orderresult->num_rows > 0) {

	while($row = $orderresult->fetch_assoc()) {

		if ($row['status'] == "nieuw") {
			$color = 'btn-outline-danger';
		} elseif ($row['status'] == "ombouw"){
			$color = 'btn-outline-warning';
		} elseif($row['status'] == "wachten op image"){
			$color = 'btn-outline-info';
		}elseif($row['status'] == "imaging") {
			$color = 'btn-outline-primary';
		} elseif ($row['status'] == "levering") {
			$color = 'btn-outline-secondary';
		} elseif ($row['status'] == "uitgeleverd") {
			$color = 'btn-outline-success';
		} else {
			$color = "";
		}

		$url = "document.location = 'order.php?id=" . $row['orderid'] . "'";

		echo '<tr onclick="' . $url . '" class="' . $color . '">';
		echo '<td scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';
		echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
		echo '<td>' . $row['amount'] . '</td>';
		echo '<td>' . $row['status'] . '<br>' . $row['status_notes'] . '</td>';
		echo '<td>' . $row['asignee'] . '</td>';
		echo '<td>' . $row['sales'] . '</td>';
		echo '<td>' . $row['warehouse'] . '</td>';

		echo '<td class="">';
		if ($row['status'] == "nieuw") {
			echo '<a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&edit=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Order Starten</button></a>';
		}
		echo '<a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:5px 0px;">Order bekijken</button></a>
			<a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&synergyid=' . $row['synergyid'] . '&duplicate=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Order dupliceren</button></a>
		</td>';

		echo '</tr>';

	}

} else {
	echo "0 results";
}

?>

		</tbody>
	</table>
	<br><br>
<?php

echo '<h4>Openstaande webshop orders van dit Synergy ID</h4>';
echo '<table class="table">';
$leermiddelorders = 0;
$webshoporders = 0;

$sql = "SELECT CONCAT('SP-BYOD-', id) AS orderid, GROUP_CONCAT(id) AS alleorderids, synergyid, SPSKU, warehouse, shipping_date,
	( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
	(SELECT COUNT(*) FROM leermiddel.tblcontractdetails AS a
	LEFT JOIN leermiddel.tbltoestelcontractdefinitie AS b ON b.id = a.toestelcontractdefinitieid
	LEFT JOIN leermiddel.tblschool AS c ON c.id = a.schoolid
	WHERE c.synergyschoolid = q.synergyid AND b.sku = REPLACE(REPLACE(REPLACE(q.spsku, '-O', ''), '-B1', ''), '-B2', '')
	AND ContractOntvangen = '1' AND deleted = '0' AND VoorschotOntvangen IN ('1', '-1') AND lengte = '0' AND ContractVolgnummer LIKE '%2021___') AS leermiddelorders
	FROM orders q
	WHERE finance_type = 'Particulier' AND q.synergyid = '" . $_GET['synergyid'] . "' and q.deleted != 1
	GROUP BY synergyid, spsku";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		if($row['leermiddelorders'] != '0'){
			echo '<tr>';
				echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
				echo '<td>Leermiddel: ' . $row['leermiddelorders'] . '</td>';

				echo '<td class="">';
				$orderids = explode(',', $row['alleorderids']);
				foreach($orderids as $id){
					echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $id . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
				}
				echo '</td>';
			echo '</tr>';
			$leermiddelorders = 1;
		}
	}
} else {
	echo "Geen leermiddel orders <br>";
}

if($leermiddelorders == '0'){
	echo "Geen leermiddel orders <br>";
}

$sql = "SELECT id, CONCAT('SP-BYOD-', id) AS orderid, GROUP_CONCAT(id) AS alleorderids, synergyid, SPSKU, warehouse, shipping_date,
	( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
	FROM orders q
	WHERE finance_type = 'Particulier' AND synergyid = '" . $_GET['synergyid'] . "' and q.deleted != 1
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
			WHERE freefield1='" . $row['synergyid'] . "' AND artcode LIKE '" . $spsku . "%' AND lengte=0";
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
			echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
			echo '<td>Webshop: ' . $aantalwebshoporders . '</td>';

			echo '<td class="">';
			$orderids = explode(',', $row['alleorderids']);
			foreach($orderids as $id){
				echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $id . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
			}
			echo '</td>';
			echo '</tr>';
			$webshoporders = 1;
		}
	}
} else {
	echo "Geen webshop ( magento ) orders <br>";
}

if($webshoporders == '0'){
	echo "Geen webshop ( magento ) orders <br>";
}

echo '</table><br><br><br><br>';

$conn->close();

?>
</div>

<?php
include('footer.php');
?>
