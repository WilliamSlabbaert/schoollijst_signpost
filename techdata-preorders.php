<?php

$title = 'Pre-Orders TechData';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

<?php
if (isset($_POST['id']) == true):

	$message = "";
	if ($_POST['status'] == "tdimaging" && isset($_POST['td_dnote_nr']) == true) {
		$sql = "UPDATE orders SET status='" . $_POST['status'] . "', td_dnote_nr='" . $_POST['td_dnote_nr'] . "', td_lb_order='" . $_POST['td_lb_order'] . "' WHERE id='" . $_POST['id'] . "'";
		$message = "Status is aangepast naar " . $_POST['status'] . " en d note en tl order toegevoegd.";
	} elseif(isset($_POST['td_dnote_nr']) == true) {
		$sql = "UPDATE orders SET td_dnote_nr='" . $_POST['td_dnote_nr'] . "', td_lb_order='" . $_POST['td_lb_order'] . "' WHERE id='" . $_POST['id'] . "'";
		$message = "d note en tl order toegevoegd.";
	} else {
		$sql = "UPDATE orders SET status='" . $_POST['status'] . "' WHERE id='" . $_POST['id'] . "'";
		$message = "Status is aangepast naar " . $_POST['status'] . "";
	}

	if ($conn->query($sql) === TRUE) {
		echo $message;
	} else {
		echo "Error updating record: " . $conn->error;
	}

elseif (isset($_GET['id']) == true):

	$sql = "SELECT * FROM orders WHERE id = '" . $_GET['id'] . "' and orders.deleted != 1";
	$result = $conn->query($sql);
	$td_lb_order = "";
	$td_dnote_nr = "";

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$td_lb_order = $row['td_lb_order'];
			$td_dnote_nr = $row['td_dnote_nr'];
		}
	} else {
		echo "0 results";
	}

	$id = "";
	$status = "";
	if (isset($_GET['status']) == true) {
		$status = $_GET['status'];
	}
	if (isset($_GET['id']) == true) {
		$id = $_GET['id'];
	}

	echo '<form action="techdata-orders.php" method="POST">
	Order ID
	<input type="text" class="form-control" id="id" name="id" value="' . $_GET['id'] . '" readonly><br>';

	if ($status !== "") {
		echo "Status wijzigen naar " . $status . "?<br><br>";
		echo 'Aanpassen naar status
		<input type="text" class="form-control" id="status" name="status" value="' . $status . '" readonly><br>';
	}

	if ($status == "tdimaging" || isset($_GET['status']) == false) {
		echo 'LB Order
		<input type="text" class="form-control" id="td_lb_order" name="td_lb_order" value="' . $td_lb_order . '" required><br>
		D Note NR
		<input type="text" class="form-control" id="td_dnote_nr" name="td_dnote_nr" value="' . $td_dnote_nr . '" required><br>';
	}

	echo '<input type="submit" value="Submit" class="btn btn-danger">
	</form>';


else: ?>

	<h3>Techdata Pre-Orders</h3>

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
					WHERE warehouse = 'TechData' ORDER BY q.synergyid";
			} else {
				$sql = "SELECT *, q.id AS orderid,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) as SPSKU_beschrijving FROM devices WHERE SPSKU = q.SPSKU LIMIT 1) as SPSKU_beschrijving,
					(SELECT productnumber as TDSKU FROM devices WHERE SPSKU = q.SPSKU LIMIT 1 ) as TDSKU
					FROM `byod-orders`.orders q
					LEFT JOIN `byod-orders`.schools ON q.synergyid = schools.synergyid
					WHERE warehouse = 'TechData' ORDER BY q.synergyid";
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
<?php endif ?>
</div>
<br><br><br>

<?php
include('footer.php');
?>
