<?php

$title = 'Mijn Orders';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Mijn Orders</h3>

	<a href="<?php hasAccessForUrl('order-form.php'); ?>" class="btn btn-info">Nieuw Order doorgeven</a>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Order ID</th>
				<th scope="col">Aantal</th>
				<th scope="col">SKU</th>
				<th scope="col">Status</th>
				<th scope="col">Sales</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, orders.id as orderid,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
				FROM `byod-orders`.orders LEFT JOIN `byod-orders`.schools ON orders.synergyid = schools.synergyid WHERE sales = '" . $loginname . "' ORDER BY orders.synergyid";
			$result = $conn->query($sql);
			$schools = "";

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

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
						echo '<td>' . $row['synergyid'] . '</td>';
						echo '<td>' . $row['school_name'] . '</td>';
						echo '<td scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';
						echo '<td>' . $row['amount'] . '</td>';
						echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
						echo '<td>' . $row['status'] . '</td>';
						echo '<td>' . $row['sales'] . '</td>';
						echo '<td><a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '', false).'">Order bekijken</a></td>';
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

<?php
include('footer.php');
?>
