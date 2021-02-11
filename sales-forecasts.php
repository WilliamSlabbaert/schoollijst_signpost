<?php

$title = 'Sales Mijn Forecasts';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Mijn Forecasts</h3>

	<a href="<?php hasAccessForUrl('forecast-form.php'); ?>" class="btn btn-info">Nieuwe forecast doorgeven</a>

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
				<th scope="col">Vrijblijvend</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, q.id as forecastid,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device1-SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving1,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device2-SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving2,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device3-SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving3,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`device4-SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving4,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 1) and deleted != 1), 0) as orderid1,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 2) and deleted != 1), 0) as orderid2,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 3) and deleted != 1), 0) as orderid3,
				IFNULL(( SELECT group_concat(id) FROM orders WHERE forecastlink = concat(q.id, '-', 4) and deleted != 1), 0) as orderid4,
				IFNULL(( SELECT status FROM orders WHERE forecastlink = concat(q.id, '-', 1) and deleted != 1 LIMIT 1), 0) as orderid1status,
				IFNULL(( SELECT status FROM orders WHERE forecastlink = concat(q.id, '-', 2) and deleted != 1 LIMIT 1), 0) as orderid2status,
				IFNULL(( SELECT status FROM orders WHERE forecastlink = concat(q.id, '-', 3) and deleted != 1 LIMIT 1), 0) as orderid3status,
				IFNULL(( SELECT status FROM orders WHERE forecastlink = concat(q.id, '-', 4) and deleted != 1 LIMIT 1), 0) as orderid4status
				FROM `byod-orders`.forecasts q
				WHERE sales = '" . $loginname . "' AND deleted != 1
				ORDER BY q.synergyid";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					$color = "";

					$url = "document.location = 'forecasts.php?id=" . $row['id'] . "'";

					if (isset($row['device1']) == true && $row['device1'] !== "") {
						$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=1'";
						echo '<tr onclick="' . $url . '">
						<td>' . $row['synergyid'] . '</td>
						<td>' . $row['school'] . '</td>
						<td>' . $row['sales'] . '</td>
						<td>' . $row['device1'] . '</td>
						<td>' . $row['devicebeschrijving1'] . '<br><span class="smalltext">' . $row['device1-SPSKU'] . '</span></td>
						<td>' . $row['device1-price'] . ' €</td>
						<td>' . $row['device1-repaircost'] . ' €</td>
						<td>' . $row['device1-consumer'] . '</td>
						<td>' . $row['device1-unobligated'] . '</td>
						<td>';
							if($row['orderid1'] !== '0'){
								$orderid1s = explode(',', $row['orderid1']);
								foreach($orderid1s as $o1s){
									echo '<a href="'. hasAccessForUrl('order.php?id=' . $o1s . '', false).'" class="btn">Ga naar het order ' . $o1s . '</a><br>';
								}
							}
							if($row['orderid1status'] == 'nieuw' || ($row['forecastid'] >= 900 && $row['orderid1'] == '0')) {
								echo '<a href="'. hasAccessForUrl('forecasts.php?forecastid=' . $row['forecastid'] . '&delete=true', false).'" class="btn btn-danger white">Verwijder forecast en order</a><br>';
							}
						echo '</td>
						</tr>';
					}

					if (isset($row['device2']) == true && $row['device2'] !== "") {
						$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=2'";
						echo '<tr onclick="' . $url . '">
						<td>' . $row['synergyid'] . '</td>
						<td>' . $row['school'] . '</td>
						<td>' . $row['sales'] . '</td>
						<td>' . $row['device2'] . '</td>
						<td>' . $row['devicebeschrijving2'] . '<br><span class="smalltext">' . $row['device2-SPSKU'] . '</span></td>
						<td>' . $row['device2-price'] . ' €</td>
						<td>' . $row['device2-repaircost'] . ' €</td>
						<td>' . $row['device2-consumer'] . '</td>
						<td>' . $row['device2-unobligated'] . '</td>
						<td></td>
						</tr>';
					}

					if (isset($row['device3']) == true && $row['device3'] !== "") {
						$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=3'";
						echo '<tr onclick="' . $url . '">
						<td>' . $row['synergyid'] . '</td>
						<td>' . $row['school'] . '</td>
						<td>' . $row['sales'] . '</td>
						<td>' . $row['device3'] . '</td>
						<td>' . $row['devicebeschrijving3'] . '<br><span class="smalltext">' . $row['device3-SPSKU'] . '</span></td>
						<td>' . $row['device3-price'] . ' €</td>
						<td>' . $row['device3-repaircost'] . ' €</td>
						<td>' . $row['device3-consumer'] . '</td>
						<td>' . $row['device3-unobligated'] . '</td>
						<td></td>
						</tr>';
					}

					if (isset($row['device4']) == true && $row['device4'] !== "") {
						$url = "document.location = 'forecasts.php?forecastid=" . $row['id'] . "&device=4'";
						echo '<tr onclick="' . $url . '">
						<td>' . $row['synergyid'] . '</td>
						<td>' . $row['school'] . '</td>
						<td>' . $row['sales'] . '</td>
						<td>' . $row['device4'] . '</td>
						<td>' . $row['devicebeschrijving4'] . '<br><span class="smalltext">' . $row['device4-SPSKU'] . '</span></td>
						<td>' . $row['device4-price'] . ' €</td>
						<td>' . $row['device4-repaircost'] . ' €</td>
						<td>' . $row['device4-consumer'] . '</td>
						<td>' . $row['device4-unobligated'] . '</td>
						<td></td>
						</tr>';
					}

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		?>

		</tbody>
	</table>
</div>

<?php
include('footer.php');
?>
