<?php

$title = 'TechData Timeline';
include('head.php');
include('nav.php');
include('conn.php');

?>

	<h3>TechData Timeline</h3>

	<a href="<?php hasAccessForUrl('techdata-timeline.php'); ?>" class="btn btn-primary">Alles</a>
	<a href="<?php hasAccessForUrl('techdata-timeline.php?sort=tdafgewerkt'); ?>" class="btn btn-primary">Bekijk enkel orders klaar voor levering</a>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Aangepaste leverdatum</th>
				<th scope="col">Plaats</th>
				<th scope="col">Aantal</th>
				<th scope="col">Klaar voor uitlevering</th>
				<th scope="col">Effectief uitgeleverd</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Toestel</th>
				<th scope="col">Status</th>
			</tr>
		</thead>

		<tbody>
<?php

if(isset($_GET['sort']) == true){
	$sql = "SELECT *, orders.shipping_date as orderdeliverydate,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		ifnull(ifnull(( SELECT DATE_FORMAT(leverdatum2020, '%d-%m-%Y') FROM images2019 LEFT JOIN schools ON schools.synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND leverdatum2020 != '' AND leverdatum2020 IS NOT NULL AND okvoor2020 = '1' LIMIT 1),
		( SELECT DATE_FORMAT(deliverydate, '%d-%m-%Y') FROM images2020 WHERE synergyid = orders.synergyid AND deliverydate != '' AND deliverydate IS NOT NULL AND confirmed = '1' LIMIT 1)), '-') as deliverydatefromimage,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ) as schoolnaam,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature = ''), 0) as klaarvoorlevering,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature != ''), 0) as aantaluitgeleverd
		FROM `byod-orders`.orders
		WHERE orders.warehouse = 'TechData' AND status = '" . $_GET['sort'] . "'";
} else {
	$sql = "SELECT *, orders.shipping_date as orderdeliverydate,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		ifnull(ifnull(( SELECT DATE_FORMAT(leverdatum2020, '%d-%m-%Y') FROM images2019 LEFT JOIN schools ON schools.synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND leverdatum2020 != '' AND leverdatum2020 IS NOT NULL AND okvoor2020 = '1' LIMIT 1),
		( SELECT DATE_FORMAT(deliverydate, '%d-%m-%Y') FROM images2020 WHERE synergyid = orders.synergyid AND deliverydate != '' AND deliverydate IS NOT NULL AND confirmed = '1' LIMIT 1)), '-') as deliverydatefromimage,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ) as schoolnaam,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature = ''), 0) as klaarvoorlevering,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature != '' ), 0) as aantaluitgeleverd
		FROM `byod-orders`.orders
		WHERE orders.warehouse = 'TechData'";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		$orderdeliverydate = strtotime($row['orderdeliverydate']);
		$vbdate = date('d-m-Y', strtotime('-5 days', $orderdeliverydate));
		$fullvbdate = strtotime($vbdate);
		$fullimagedate = strtotime($row['deliverydatefromimage']);
		$today = date("d-m-Y");
		$fulltoday = strtotime(date("d-m-Y"));

		if ($row['status'] == 'uitgeleverd') {
			$color = 'btn-outline-success';
		} elseif ($row['status'] !== 'tdafgewerkt') {
			$color = 'btn-outline-secondary';
		} elseif ($orderdeliverydate < $fulltoday) {
			$color = 'btn-outline-warning';
		} else {
			$color = 'btn-outline-danger';
		}

		$url = "window.open('order.php?id=" . $row['id'] . "', '_blank')";

		echo '<tr onclick="' . $url . '" class="' . $color . '">';
		echo '<td scope="row"><strong>SP-BYOD20-' . $row['id'] . '</strong></td>';

		echo '<td>' . $row['synergyid'] . '</td>';
		echo '<td>' . $row['schoolnaam'] . '</td>';

		echo '<td data-sort="'. $orderdeliverydate .'">';
		if($row['on_hold'] != ''){
			echo '<p style="color:black !important;">' . $row['on_hold'] . '</p>';
		} else {
			echo '<p style="color:black !important;">' . $row['orderdeliverydate'] . '</p>';
		}
		echo '</td>';

		echo '<td>' . $row['warehouse'] . '</td>';
		echo '<td>' . $row['amount'] . '</td>';
		echo '<td>' . $row['klaarvoorlevering'] . '</td>';
		echo '<td>' . $row['aantaluitgeleverd'] . '</td>';
		echo '<td>' . $row['SPSKU'] . '</td>';
		echo '<td>' . $row['devicebeschrijving'] . '</td>';
		echo '<td>' . $row['status'] . '</td>';

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
