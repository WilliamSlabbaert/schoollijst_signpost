<?php

$title = 'Copaco Pre-Orders';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Copaco Pre-Orders</h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Aantal</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Beschrijving</th>
				<th scope="col">SKU</th>
				<th scope="col">Hoes</th>
				<th scope="col">Status</th>
			</tr>
		</thead>

		<tbody>
		<?php

			if (isset($_GET['sort']) == true) {
				$sql = "SELECT *, q.id AS orderid,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) as SPSKU_beschrijving
					FROM devices WHERE SPSKU = q.SPSKU LIMIT 1) as SPSKU_beschrijving,
					(SELECT productnumber as TDSKU FROM devices WHERE SPSKU = q.SPSKU LIMIT 1 ) as TDSKU
					FROM `byod-orders`.orders q
					LEFT JOIN `byod-orders`.schools ON q.synergyid = schools.synergyid
					WHERE warehouse = 'Copaco' ORDER BY q.synergyid";
			} else {
				$sql = "SELECT *, q.id AS orderid,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) as SPSKU_beschrijving FROM devices WHERE SPSKU = q.SPSKU LIMIT 1) as SPSKU_beschrijving,
					(SELECT productnumber as TDSKU FROM devices WHERE SPSKU = q.SPSKU LIMIT 1 ) as TDSKU
					FROM `byod-orders`.orders q
					LEFT JOIN `byod-orders`.schools ON q.synergyid = schools.synergyid
					WHERE warehouse = 'Copaco' ORDER BY q.synergyid";
			}

			$result = $conn->query($sql);
			$schools = "";

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					if ($row['status'] == "tdconfigadmin") {
						$color = 'btn-outline-danger';
					} elseif ($row['status'] == "tdgeenvoorraad") {
						$color = 'btn-outline-warning';
					} elseif ($row['status'] == "tdimaging") {
						$color = 'btn-outline-info';
					} elseif ($row['status'] == "tdafgewerkttemp") {
						$color = 'btn-outline-primary';
					} elseif ($row['status'] == "tdafgewerkt") {
						$color = 'btn-outline-success';
					} else {
						$color = "";
					}

					$url = "document.location = 'order.php?id=" . $row['orderid'] . "'";

					echo '<tr onclick="' . $url . '" class="' . $color . '">';
						echo '<th scope="row">SP-BYOD20-' . $row['orderid'] . '</th>';
						echo '<td>' . $row['synergyid'] . '</td>';
						echo '<td>' . $row['school_name'] . '</td>';
						echo '<td>' . $row['amount'] . '</td>';
						echo '<td>' . $row['SPSKU'] . '</td>';
						echo '<td>' . $row['SPSKU_beschrijving'] . '</td>';
						echo '<td>' . $row['TDSKU'] . '</td>';
						echo '<td>' . $row['covers'] . '</td>';
						echo '<td class="" style="text-align: right;">' . $row['status'] . '</td>';
					echo '</tr>';

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		?>

		</tbody>
	</table>
</div>
<br><br><br>

<?php
include('footer.php');
?>
