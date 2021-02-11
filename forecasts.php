<?php

    $title = 'Alle Forecasts';
    include('head.php');
    include('nav.php');
    include('conn.php');

?>

<div class="body">

<?php
if (isset($_POST['forecastid'])) {

	$sql = "UPDATE forecasts SET deleted = '1' WHERE id='" . $_POST['forecastid'] . "'";

	if ($conn->query($sql) === TRUE) {
		echo "Record 'deleted' successfully<br>";
	} else {
		echo "Error deleting record: " . $conn->error;
	}

	$sql = "UPDATE orders SET deleted = '1' WHERE forecastlink = '" . $_POST['forecastid'] . "-1' and status = 'nieuw'";

	if ($conn->query($sql) === TRUE) {
		echo "Record 'deleted' successfully<br>";
	} else {
		echo "Error deleting record: " . $conn->error;
	}

	$conn->close();

} elseif (isset($_GET['delete'])) {

	echo '<h4>Ben je zeker om deze forecast en order te verwijderen?</h4>';

	$sql = "SELECT *, ( SELECT group_concat(id) FROM orders where forecastlink = '" . $_GET['forecastid'] . "-1' and status = 'nieuw' and deleted != 1) as orderids FROM forecasts WHERE id = " . $_GET['forecastid'] . " AND deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo $row["school"]. "<br>";
			echo $row["device1"]. " x " . $row["device1-SPSKU"]. "<br>";
			echo $row["device2"]. " x " . $row["device2-SPSKU"]. "<br>";
			echo $row["device3"]. " x " . $row["device3-SPSKU"]. "<br>";
			echo $row["device4"]. " x " . $row["device4-SPSKU"]. "<br>";
			echo 'en orders: ' . $row['orderids'];
		}
	} else {
		echo "0 results";
	}
	$conn->close();

	echo '<form action="forecasts.php" method="post">
		<input type="text" id="forecastid" name="forecastid" value="' . $_GET['forecastid'] . '" class="form-control" hidden><br>
		<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

} elseif (isset($_GET['forecastid'])) {

	echo "<h3>Details van forecast</h3><br>";
	if (isset($_GET['device']) == true) {
		$device = $_GET['device'];
	} else {
		$device = "0";
	}

	$sql = "SELECT * FROM forecasts where id = '" . $_GET['forecastid'] . "' AND deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			echo "<table class=\"table table-sm table-striped\">";
			echo "<tr><td>Synergy ID</td><td>" . $row['synergyid'] . "</td></tr>";
			echo "<tr><td>School</td><td>" . $row['school'] . "</td></tr>";
			echo "<tr><td>Verkoopkans</td><td>" . $row['salesorderid'] . "</td></tr>";
			echo "<tr><td>Beschrijving</td><td>" . $row['description'] . "</td></tr>";
			echo "<tr><td>Sales</td><td>" . $row['sales'] . "</td></tr>";

			echo "<tr><td></td><td></td></tr>";
			echo "<tr><td>Leveradres</td><td>" . $row['shipping_postcode'] . " " . $row['shipping_city'] . "<br>" . $row['shipping_street'] . "</td></tr>";
			echo "<tr><td>Contactpersoon voor levering</td><td>" . $row['delivery_contact_name'] . "<br>" . $row['delivery_contact_email'] . "<br>" . $row['delivery_contact_tel'] . "</td></tr>";
			echo "<tr><td>Contactpersoon voor facturatie</td><td>" . $row['financial_contact_name'] . "<br>" . $row['financial_contact_email'] . "<br>" . $row['financial_contact_tel'] . "</td></tr>";

			if ($device == "1" || ($device == "0" && $row['device1-SPSKU'] !== "")) {
				echo "<tr><td></td><td></td></tr>";
				echo "<tr><th>Toestel 1</th><th></th></tr>";
				echo "<tr><td>Aantal</td><td>" . $row['device1'] . "</td></tr>";
				echo "<tr><td>SPSKU</td><td>" . $row['device1-SPSKU'] . "</td></tr>";
				echo "<tr><td>Label</td><td>" . $row['label'] . "-0001</td></tr>";
				echo "<tr><td>Prijs</td><td>" . $row['device1-price'] . "</td></tr>";
				echo "<tr><td>Herstelkost</td><td>" . $row['device1-repaircost'] . "</td></tr>";
				echo "<tr><td>Financiering</td><td>" . $row['device1-finance'] . "</td></tr>";
				echo "<tr><td>Hoes</td><td>" . $row['device1-sleve'] . "</td></tr>";
				if($row['device1-licenses'] !== ''){
					echo "<tr><td>Chrome Licenties</td><td>" . $row['device1-licenses'] . "</td></tr>";
				} else {
					echo "<tr><td>Chrome Licenties</td><td>Geen</td></tr>";
				}
				echo "<tr><td>Consument</td><td>" . $row['device1-consumer'] . "</td></tr>";
				echo "<tr><td>Vrijblijvend</td><td>" . $row['device1-unobligated'] . "</td></tr>";
			}

			if ($device == "2" || ($device == "0" && $row['device2-SPSKU'] !== "")) {
				echo "<tr><td></td><td></td></tr>";
				echo "<tr><th>Toestel 2</th><th></th></tr>";
				echo "<tr><td>Aantal</td><td>" . $row['device2'] . "</td></tr>";
				echo "<tr><td>SPSKU</td><td>" . $row['device2-SPSKU'] . "</td></tr>";
				echo "<tr><td>Label</td><td>" . $row['label'] . "-0001</td></tr>";
				echo "<tr><td>Prijs</td><td>" . $row['device2-price'] . "</td></tr>";
				echo "<tr><td>Herstelkost</td><td>" . $row['device2-repaircost'] . "</td></tr>";
				echo "<tr><td>Financiering</td><td>" . $row['device2-finance'] . "</td></tr>";
				echo "<tr><td>Hoes</td><td>" . $row['device2-sleve'] . "</td></tr>";
				if($row['device2-licenses'] !== ''){
					echo "<tr><td>Chrome Licenties</td><td>" . $row['device2-licenses'] . "</td></tr>";
				} else {
					echo "<tr><td>Chrome Licenties</td><td>Geen</td></tr>";
				}
				echo "<tr><td>Consument</td><td>" . $row['device2-consumer'] . "</td></tr>";
				echo "<tr><td>Vrijblijvend</td><td>" . $row['device2-unobligated'] . "</td></tr>";
			}

			if ($device == "3" || ($device == "0" && $row['device3-SPSKU'] !== "")) {
				echo "<tr><td></td><td></td></tr>";
				echo "<tr><th>Toestel 3</th><th></th></tr>";
				echo "<tr><td>Aantal</td><td>" . $row['device3'] . "</td></tr>";
				echo "<tr><td>SPSKU</td><td>" . $row['device3-SPSKU'] . "</td></tr>";
				echo "<tr><td>Label</td><td>" . $row['label'] . "-0001</td></tr>";
				echo "<tr><td>Prijs</td><td>" . $row['device3-price'] . "</td></tr>";
				echo "<tr><td>Herstelkost</td><td>" . $row['device3-repaircost'] . "</td></tr>";
				echo "<tr><td>Financiering</td><td>" . $row['device3-finance'] . "</td></tr>";
				echo "<tr><td>Hoes</td><td>" . $row['device3-sleve'] . "</td></tr>";
				if($row['device3-licenses'] !== ''){
					echo "<tr><td>Chrome Licenties</td><td>" . $row['device3-licenses'] . "</td></tr>";
				} else {
					echo "<tr><td>Chrome Licenties</td><td>Geen</td></tr>";
				}
				echo "<tr><td>Consument</td><td>" . $row['device3-consumer'] . "</td></tr>";
				echo "<tr><td>Vrijblijvend</td><td>" . $row['device3-unobligated'] . "</td></tr>";
			}

			if ($device == "4" || ($device == "0" && $row['device4-SPSKU'] !== "")) {
				echo "<tr><td></td><td></td></tr>";
				echo "<tr><th>Toestel 4</th><th></th></tr>";
				echo "<tr><td>Aantal</td><td>" . $row['device4'] . "</td></tr>";
				echo "<tr><td>SPSKU</td><td>" . $row['device4-SPSKU'] . "</td></tr>";
				echo "<tr><td>Label</td><td>" . $row['label'] . "-0001</td></tr>";
				echo "<tr><td>Prijs</td><td>" . $row['device4-price'] . "</td></tr>";
				echo "<tr><td>Herstelkost</td><td>" . $row['device4-repaircost'] . "</td></tr>";
				echo "<tr><td>Financiering</td><td>" . $row['device4-finance'] . "</td></tr>";
				echo "<tr><td>Hoes</td><td>" . $row['device4-sleve'] . "</td></tr>";
				if($row['device4-licenses'] !== ''){
					echo "<tr><td>Chrome Licenties</td><td>" . $row['device4-licenses'] . "</td></tr>";
				} else {
					echo "<tr><td>Chrome Licenties</td><td>Geen</td></tr>";
				}
				echo "<tr><td>Consument</td><td>" . $row['device4-consumer'] . "</td></tr>";
				echo "<tr><td>Vrijblijvend</td><td>" . $row['device4-unobligated'] . "</td></tr>";
			}

			echo "<tr><td></td><td></td></tr>";
			echo "<tr><td>Opmerkingen</td><td>" . $row['remarks'] . "</td></tr>";
			echo "<tr><td>Referentie School</td><td>" . $row['refer'] . "</td></tr>";
			echo "<tr><td>Referentie voor op factuur</td><td>" . $row['refer_invoice'] . "</td></tr>";
			echo "<tr><td>Datum ingevuld</td><td>" . $row['date'] . "</td></tr>";
			echo "<tr><td>Start Datum</td><td>" . $row['startdate'] . "</td></tr>";
			echo "<tr><td>Campagne Jaar</td><td>" . $row['campagne'] . "</td></tr>";
			echo "</table>";
			echo "<br><br><br><br>";

		}
	} else {
		echo "0 results";
	}
	$conn->close();

} else {

?>


	<h3>Alle Forecasts</h3>

	<a href="<?php hasAccessForUrl('forecast-form.php'); ?>" class="btn btn-info">Nieuwe forecast doorgeven</a>
	<a href="<?php hasAccessForUrl('forecasts.php?0order=true'); ?>" class="btn btn-info">Bekijk forecasts zonder een order</a>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Sales</th>
				<th scope="col">Aantal</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Prijs</th>
				<th scope="col">Herstelkost</th>
				<th scope="col">Consument</th>
				<th scope="col">Financiering</th>
				<th scope="col">Vrijblijvend</th>
				<th scope="col">Aantal in order</th>
				<th scope="col">Order?</th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, q.id AS forecastid,
				IF(q.`device1-SPSKU`!='',IFNULL(( SELECT SUM(amount) FROM orders WHERE synergyid = q.synergyid AND SUBSTRING_INDEX(SUBSTRING_INDEX(SPSKU,';',1),'-O',1) = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device1-SPSKU`,';',1),'-O',1) AND finance_type = q.`device1-finance` AND consumer = q.`device1-consumer` AND unobligated = q.`device1-unobligated` and q.deleted != 1), '0'), 'geen SPSKU') AS totaalordered1,
				IF(q.`device1-SPSKU`!='',IFNULL(( SELECT SUM(amount) FROM orders WHERE synergyid = q.synergyid AND SUBSTRING_INDEX(SUBSTRING_INDEX(SPSKU,';',1),'-O',1) = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device2-SPSKU`,';',1),'-O',1) AND finance_type = q.`device2-finance` AND consumer = q.`device2-consumer` AND unobligated = q.`device2-unobligated` and q.deleted != 1), '0'), 'geen SPSKU') AS totaalordered2,
				IF(q.`device1-SPSKU`!='',IFNULL(( SELECT SUM(amount) FROM orders WHERE synergyid = q.synergyid AND SUBSTRING_INDEX(SUBSTRING_INDEX(SPSKU,';',1),'-O',1) = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device3-SPSKU`,';',1),'-O',1) AND finance_type = q.`device3-finance` AND consumer = q.`device3-consumer` AND unobligated = q.`device3-unobligated` and q.deleted != 1), '0'), 'geen SPSKU') AS totaalordered3,
				IF(q.`device1-SPSKU`!='',IFNULL(( SELECT SUM(amount) FROM orders WHERE synergyid = q.synergyid AND SUBSTRING_INDEX(SUBSTRING_INDEX(SPSKU,';',1),'-O',1) = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device4-SPSKU`,';',1),'-O',1) AND finance_type = q.`device4-finance` AND consumer = q.`device4-consumer` AND unobligated = q.`device4-unobligated` and q.deleted != 1), '0'), 'geen SPSKU') AS totaalordered4,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device1-SPSKU`, ';', 1), '-B1', 1), '-B2', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving1,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device2-SPSKU`, ';', 1), '-B1', 1), '-B2', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving2,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device3-SPSKU`, ';', 1), '-B1', 1), '-B2', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving3,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device4-SPSKU`, ';', 1), '-B1', 1), '-B2', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving4,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 1) and deleted != 1), 0) as orderid1,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 2) and deleted != 1), 0) as orderid2,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 3) and deleted != 1), 0) as orderid3,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 4) and deleted != 1), 0) as orderid4
				FROM `byod-orders`.forecasts q
				WHERE deleted != 1
				ORDER BY q.synergyid";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					$color = "";

					if(isset($_GET['0order']) == true && $row['totaalordered1'] != 0){
					} else {
						if (isset($row['device1']) == true && $row['device1'] !== "") {
							$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=1'";
							echo '<tr onclick="' . $url . '">
							<td scope="col"><a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '', false).'" style="color: white; text-decoration: underline;">' . $row['synergyid'] . '</a></td>
							<td>' . $row['school'] . '</td>
							<td>' . $row['sales'] . '</td>
							<td>' . $row['device1'] . '</td>
							<td>' . $row['devicebeschrijving1'] . '<br><span class="smalltext">' . $row['device1-SPSKU'] . '</span></td>
							<td>' . $row['device1-price'] . ' €</td>
							<td>' . $row['device1-repaircost'] . ' €</td>
							<td>' . $row['device1-consumer'] . '</td>
							<td>' . $row['device1-finance'] . '</td>
							<td>' . $row['device1-unobligated'] . '</td>
							<td>' . $row['totaalordered1'] . '</td>
							<td>';

							if (hasRole($role, ['management'])) {
								echo '<a href="'. hasAccessForUrl('order-form.php?forecastid=' . $row['forecastid'] . '&devicenr=1&synergyid=' . $row['synergyid'] . '&amount=' . $row['device1'] . '&spsku=' . $row['device1-SPSKU'] . '&finance=' . $row['device1-finance'] . '&sleve=' . $row['device1-sleve'] . '&consumer=' . $row['device1-consumer'] . '&unobligated=' . $row['device1-unobligated'] . '&sales=' . $row['sales'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:9px 0px;">Order Maken</button></a><br>';
							}

							if($row['orderid1'] !== '0'){
								$orderid1s = explode(',', $row['orderid1']);
								foreach($orderid1s as $o1s){
									echo '<a href="'. hasAccessForUrl('order.php?id=' . $o1s . '', false).'" class="btn">Ga naar het order ' . $o1s . '</a><br>';
								}
							}

							if(($row['totaalordered1'] == '0' || $row['totaalordered1'] == 'geen SPSKU') && hasRole($role, ['admin'])){
								echo '<a href="'. hasAccessForUrl('forecasts.php?forecastid=' . $row['forecastid'] . '&delete=true', false).'" class="btn btn-danger white">Verwijder forecast en order</a><br>';
							}

							echo '</td>
							</tr>';
						}
					}

					if(isset($_GET['0order']) == true && $row['totaalordered2'] != 0){
					} else {
						if (isset($row['device2']) == true && $row['device2'] !== "") {
							$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=2'";
							echo '<tr onclick="' . $url . '">
							<td scope="col"><a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '', false).'" style="color: white; text-decoration: underline;">' . $row['synergyid'] . '</a></td>
							<td>' . $row['school'] . '</td>
							<td>' . $row['sales'] . '</td>
							<td>' . $row['device2'] . '</td>
							<td>' . $row['devicebeschrijving2'] . '<br><span class="smalltext">' . $row['device2-SPSKU'] . '</span></td>
							<td>' . $row['device2-price'] . ' €</td>
							<td>' . $row['device2-repaircost'] . ' €</td>
							<td>' . $row['device2-consumer'] . '</td>
							<td>' . $row['device2-finance'] . '</td>
							<td>' . $row['device2-unobligated'] . '</td>
							<td>' . $row['totaalordered2'] . '</td>
							<td>';

							if (hasRole($role, ['management'])) {
								echo '<a href="'. hasAccessForUrl('order-form.php?forecastid=' . $row['forecastid'] . '&devicenr=2&synergyid=' . $row['synergyid'] . '&amount=' . $row['device2'] . '&spsku=' . $row['device2-SPSKU'] . '&finance=' . $row['device2-finance'] . '&sleve=' . $row['device2-sleve'] . '&consumer=' . $row['device2-consumer'] . '&unobligated=' . $row['device2-unobligated'] . '&sales=' . $row['sales'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:9px 0px;">Order Maken</button></a><br>';
							}

							if($row['orderid2'] !== '0'){
								$orderid2s = explode(',', $row['orderid2']);
								foreach($orderid2s as $o2s){
									echo '<a href="'. hasAccessForUrl('order.php?id=' . $o2s . '', false).'" class="btn">Ga naar het order ' . $o2s . '</a><br>';
								}
							}
							if(($row['totaalordered2'] == '0' || $row['totaalordered2'] == 'geen SPSKU') && hasRole($role, ['management'])){
								echo '<a href="'. hasAccessForUrl('forecasts.php?forecastid=' . $row['forecastid'] . '&delete=true', false).'" class="btn btn-danger white">Delete #' . $row['forecastid'] . '</a><br>';
							}
							echo '</td>
							</tr>';
						}
					}

					if(isset($_GET['0order']) == true && $row['totaalordered3'] != 0){
					} else {
						if (isset($row['device3']) == true && $row['device3'] !== "") {
							$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=3'";
							echo '<tr onclick="' . $url . '">
							<td scope="col"><a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '', false).'" style="color: white; text-decoration: underline;">' . $row['synergyid'] . '</a></td>
							<td>' . $row['school'] . '</td>
							<td>' . $row['sales'] . '</td>
							<td>' . $row['device3'] . '</td>
							<td>' . $row['devicebeschrijving3'] . '<br><span class="smalltext">' . $row['device3-SPSKU'] . '</span></td>
							<td>' . $row['device3-price'] . ' €</td>
							<td>' . $row['device3-repaircost'] . ' €</td>
							<td>' . $row['device3-consumer'] . '</td>
							<td>' . $row['device3-finance'] . '</td>
							<td>' . $row['device3-unobligated'] . '</td>
							<td>' . $row['totaalordered3'] . '</td>
							<td>';

							if (hasRole($role, ['management'])) {
								echo '<a href="'. hasAccessForUrl('order-form.php?forecastid=' . $row['forecastid'] . '&devicenr=3&synergyid=' . $row['synergyid'] . '&amount=' . $row['device3'] . '&spsku=' . $row['device3-SPSKU'] . '&finance=' . $row['device3-finance'] . '&sleve=' . $row['device3-sleve'] . '&consumer=' . $row['device3-consumer'] . '&unobligated=' . $row['device3-unobligated'] . '&sales=' . $row['sales'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:9px 0px;">Order Maken</button></a><br>';
							}

							if($row['orderid3'] !== '0'){
								$orderid3s = explode(',', $row['orderid3']);
								foreach($orderid3s as $o3s){
									echo '<a href="'. hasAccessForUrl('order.php?id=' . $o3s . '', false).'" class="btn">Ga naar het order ' . $o3s . '</a><br>';
								}
							}
							if(($row['totaalordered3'] == '0' || $row['totaalordered3'] == 'geen SPSKU') && hasRole($role, ['admin'])){
								echo '<a href="'. hasAccessForUrl('forecasts.php?forecastid=' . $row['forecastid'] . '&delete=true', false).'" class="btn btn-danger white">Delete #' . $row['forecastid'] . '</a><br>';
							}
							echo '</td>
							</tr>';
						}
					}

					if(isset($_GET['0order']) == true && $row['totaalordered4'] != 0){
					} else {
						if (isset($row['device4']) == true && $row['device4'] !== "") {
							$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=4'";
							echo '<tr onclick="' . $url . '">
							<td scope="col"><a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '', false).'" style="color: white; text-decoration: underline;">' . $row['synergyid'] . '</a></td>
							<td>' . $row['school'] . '</td>
							<td>' . $row['sales'] . '</td>
							<td>' . $row['device4'] . '</td>
							<td>' . $row['devicebeschrijving4'] . '<br><span class="smalltext">' . $row['device4-SPSKU'] . '</span></td>
							<td>' . $row['device4-price'] . ' €</td>
							<td>' . $row['device4-repaircost'] . ' €</td>
							<td>' . $row['device4-consumer'] . '</td>
							<td>' . $row['device4-finance'] . '</td>
							<td>' . $row['device4-unobligated'] . '</td>
							<td>' . $row['totaalordered4'] . '</td>
							<td>';

							if (hasRole($role, ['management'])) {
								echo '<a href="'. hasAccessForUrl('order-form.php?forecastid=' . $row['forecastid'] . '&devicenr=4&synergyid=' . $row['synergyid'] . '&amount=' . $row['device4'] . '&spsku=' . $row['device4-SPSKU'] . '&finance=' . $row['device4-finance'] . '&sleve=' . $row['device4-sleve'] . '&consumer=' . $row['device4-consumer'] . '&unobligated=' . $row['device4-unobligated'] . '&sales=' . $row['sales'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:9px 0px;">Order Maken</button></a><br>';
							}

							if($row['orderid4'] !== '0'){
								$orderid4s = explode(',', $row['orderid4']);
								foreach($orderid4s as $o4s){
									echo '<a href="'. hasAccessForUrl('order.php?id=' . $o4s . '', false).'" class="btn">Ga naar het order ' . $o4s . '</a><br>';
								}
							}
							if(($row['totaalordered4'] == '0' || $row['totaalordered4'] == 'geen SPSKU') && hasRole($role, ['admin'])){
								echo '<a href="'. hasAccessForUrl('forecasts.php?forecastid=' . $row['forecastid'] . '&delete=true', false).'" class="btn btn-danger white">Delete #' . $row['forecastid'] . '</a><br>';
							}
							echo '</td>
							</tr>';
						}
					}

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		?>

		</tbody>
	</table>
<?php
}
?>
</div>

<?php
include('footer.php');
?>
