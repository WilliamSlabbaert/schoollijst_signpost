<?php

$title = 'Leveringen Timeline';
include('head.php');
include('nav.php');
include('conn.php');

?>

<!-- <div class="body"> -->

<?php

if(isset($_GET['orderid']) == true){

	echo '<div class="body">';
	echo '<h3>Datum aanpassen</h3>
	<form action="delivery-dates.php" method="post">
		<div class="row">
			<div class="col-md-6">
		<label for="order">Order:</label><br>
		<input type="text" id="orderid" name="orderid" value="' . $_GET['orderid'] . '" readonly class="form-control"><br>
		<input type="text" id="olddate" name="olddate" value="' . $_GET['date'] . '" hidden>
		</div>
		</div>
		<label for="date">Datum:</label><br>';
	echo '<div class="row">
			<div class="col-md-6">
				<input type="text" class="form-control datetimepicker-input" id="datetimepicker1" data-toggle="datetimepicker" data-target="#datetimepicker1" name="date" placeholder="' . $_GET['date'] . '" /><br>
				Other status:
				<input type="text" class="form-control" id="on_hold" name="on_hold" value="' . $_GET['on_hold'] . '"><br>
			</div>
		</div></br>';

	echo '';

	echo '<button type="submit" class="btn btn-primary">Doorsturen</button>
		</form>';
	echo '</div>';
	echo "<script type=\"text/javascript\">
		$(function () {
			$('#datetimepicker1').datetimepicker({
				locale: 'nl',
				format: 'L'
			});
		});
	</script>";

} elseif(isset($_POST['orderid']) == true){

	if(isset($_POST['date']) == true && $_POST['date'] !== ''){
		$sql = "UPDATE orders SET shipping_date = '" . $_POST['date'] . "', on_hold = '" . $_POST['on_hold'] . "', history =
		CASE WHEN history IS NULL
			THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Leverdatum op order aangepast van " . $_POST['olddate'] . " naar " . $_POST['date'] . " met status " . $_POST['on_hold'] . "')
			ELSE concat(history,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Leverdatum op order aangepast van " . $_POST['olddate'] . " naar " . $_POST['date'] . " met status " . $_POST['on_hold'] . "')
		END
		WHERE id = '" . $_POST['orderid'] . "'";
	} else {
		$sql = "UPDATE orders SET on_hold = '" . $_POST['on_hold'] . "', history =
		CASE WHEN history IS NULL
			THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Leverdatum op order aangepast naar " . $_POST['on_hold'] . "')
			ELSE concat(history,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Leverdatum op order aangepast " . $_POST['on_hold'] . "')
		END
		WHERE id = '" . $_POST['orderid'] . "'";
	}

	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo 'Gelukt!<br>
			<a href="'. hasAccessForUrl('delivery-dates.php', false) . '">Klik hier om terug te gaan</a>';
		echo '</div>';
	} else {
		echo $sql;
		echo "Error updating record: " . $conn->error;
	}

	$conn->close();

} else {
?>

	<h3>Orders</h3>
	<p>Datum veld is Image(2019)>Image(2020).</p>

	<a href="<?php hasAccessForUrl( 'delivery-dates.php'); ?>" class="btn btn-primary">Alles</a>
	<a href="<?php hasAccessForUrl( 'delivery-dates.php?sort=levering'); ?>" class="btn btn-primary">Bekijk enkel orders klaar voor levering</a>

	<table class="table table-sm" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Sales</th>
				<th scope="col">Leverdatum van intake</th>
				<th scope="col">Voorbereidings Datum</th>
				<th scope="col">Aangepaste leverdatum</th>
				<th scope="col">Plaats</th>
				<th scope="col">Originele Plaats</th>
				<th scope="col">Financiering</th>
				<th scope="col">Aantal</th>
				<th scope="col">Klaar voor uitlevering</th>
				<th scope="col">Effectief uitgeleverd</th>
				<th scope="col">Toestel</th>
				<th scope="col">Vendor SKU</th>
				<th scope="col">Status</th>
			</tr>
		</thead>

		<tbody>
<?php

if(isset($_GET['sort']) == true){
	$sql = "SELECT *, orders.sales as sales, orders.shipping_date as orderdeliverydate,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		( SELECT productnumber FROM devices WHERE SPSKU = SUBSTRING_INDEX(orders.`SPSKU`, ';', 1) LIMIT 1 ) AS VendorSKU,
		ifnull(ifnull(( SELECT DATE_FORMAT(leverdatum2020, '%d-%m-%Y') FROM images2019 LEFT JOIN schools ON schools.synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND leverdatum2020 != '' AND leverdatum2020 IS NOT NULL AND okvoor2020 = '1' LIMIT 1),
		( SELECT DATE_FORMAT(deliverydate, '%d-%m-%Y') FROM images2020 WHERE synergyid = orders.synergyid AND deliverydate != '' AND deliverydate IS NOT NULL AND confirmed = '1' LIMIT 1)), '-') as deliverydatefromimage,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ) as schoolnaam,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature = ''), 0) as klaarvoorlevering,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature != ''), 0) as aantaluitgeleverd
		FROM `byod-orders`.orders
		WHERE status = '" . $_GET['sort'] . "' AND deleted != 1";
} else {
	$sql = "SELECT *, orders.sales as sales, orders.shipping_date as orderdeliverydate,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		( SELECT productnumber FROM devices WHERE SPSKU = SUBSTRING_INDEX(orders.`SPSKU`, ';', 1) LIMIT 1 ) AS VendorSKU,
		ifnull(ifnull(( SELECT DATE_FORMAT(leverdatum2020, '%d-%m-%Y') FROM images2019 LEFT JOIN schools ON schools.synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND leverdatum2020 != '' AND leverdatum2020 IS NOT NULL AND okvoor2020 = '1' LIMIT 1),
		( SELECT DATE_FORMAT(deliverydate, '%d-%m-%Y') FROM images2020 WHERE synergyid = orders.synergyid AND deliverydate != '' AND deliverydate IS NOT NULL AND confirmed = '1' LIMIT 1)), '-') as deliverydatefromimage,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ) as schoolnaam,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature = ''), 0) as klaarvoorlevering,
		ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature != '' ), 0) as aantaluitgeleverd
		FROM `byod-orders`.orders
		WHERE deleted != 1";
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
		} elseif ($row['status'] !== 'levering') {
			$color = 'btn-outline-secondary';
		} elseif ($fullvbdate < $fulltoday) {
			$color = 'btn-outline-warning';
		} else {
			$color = 'btn-outline-danger';
		}

		$url = "window.open('delivery.php?orderid=" . $row['id'] . "', '_blank')";
		$schoolUrl = "window.open('school.php?synergyid=" . $row['synergyid'] . "', '_blank')";

		echo '<tr class="' . $color . '">';
		echo '<td onclick="' . $url . '" scope="row"><strong>SP-BYOD20-' . $row['id'] . '</strong></td>';

		echo '<td class="link" onclick="' . $schoolUrl . '" >' . $row['synergyid'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['schoolnaam'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['sales'] . '</td>';

		echo '<td onclick="' . $url . '" data-sort="'. $fullimagedate .'">' . $row['deliverydatefromimage'] . '</td>';

		if ($vbdate == '27-12-1969'){
			echo '<td onclick="' . $url . '" data-sort="0">-</td>';
		} else  {
			echo '<td onclick="' . $url . '" data-sort="'. $fullvbdate .'">' . $vbdate . '</td>';
		}

		echo '<td data-sort="'. $orderdeliverydate .'">';
		$url = 'delivery-dates.php?orderid=' . $row['id'] . '&date=' . $row['orderdeliverydate'] . '&on_hold=' . $row['on_hold'];
		if($row['on_hold'] != '') {
			echo '<a href="'. hasAccessForUrl($url, false) . '" style="color:black !important; padding:10px;" target="_blank">' . $row['on_hold'] . '</a>';
		} else {
			echo '<a href="'. hasAccessForUrl($url, false) . '" style="color:black !important;" target="_blank">' . $row['orderdeliverydate'] . '</a>';
		}
		echo '</td>';

		echo '<td onclick="' . $url . '" >' . $row['warehouse'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['original_warehouse'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['finance_type'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['amount'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['klaarvoorlevering'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['aantaluitgeleverd'] . '</td>';
		//echo '<td onclick="' . $url . '" >' . $row['SPSKU'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
		echo '<td onclick="' . $url . '" >' . $row['VendorSKU'] . '</td>';
		echo '<td onclick="' . $url . '" >' . $row['status'] . '</td>';

		echo '</tr>';

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

<?php
include('footer.php');
?>
