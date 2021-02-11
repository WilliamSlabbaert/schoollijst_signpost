<?php

if($_GET['uitgeleverd'] == true){
	$title = 'Uitgeleverd';
} else {
	$title = 'Uitleveringen';
}

include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3><?php echo $title; ?></h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Levering ID</th>
				<th scope="col">Aantal</th>
				<th scope="col">Toestel</th>
				<th scope="col">School</th>
				<th scope="col">Adres</th>
				<th scope="col">Magazijn</th>
				<?php if(isset($_GET['uitgeleverd']) == true){ ?>
				<th scope="col">Uitgeleverd op</th>
				<?php } else { ?>
				<th scope="col">Leverdatum van order</th>
				<?php } ?>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
<?php

if(isset($_GET['uitgeleverd']) == true){
	$sql = "SELECT *, delivery.amount as deliveryamount, ( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving, delivery.delivered_on AS deliverydate,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid AND school_name != '' limit 1) as school_naam
		FROM `byod-orders`.delivery
		LEFT JOIN `byod-orders`.orders ON delivery.orderid = orders.id
		WHERE delivered_by != '' or accepted_by like '%autosigned%'
		ORDER BY shipping_date";
} else {
	$sql = "SELECT *, delivery.amount as deliveryamount, ( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving, shipping_date AS deliverydate,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid AND school_name != '' limit 1) as school_naam
		FROM `byod-orders`.delivery
		LEFT JOIN `byod-orders`.orders ON delivery.orderid = orders.id
		WHERE delivered_by = '' and accepted_by not like '%autosigned%'
		ORDER BY shipping_date";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		echo '<tr>';
		echo '<td style="width:180px;"><strong>SP-BYOD20-' . $row['orderid'] . '-' . $row['type'] . '' . $row['delivery_number'] . '</strong></td>';
		echo '<td>' . $row['deliveryamount'] . '</td>';
		echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';

		echo '<td>' . $row['school_naam'] . '</td>';
		echo '<td>' . $row['shipping_street'] . ' ' . $row['shipping_number'] . '<br>' . $row['shipping_postcode'] . ' ' . $row['shipping_city'] . '</td>';

		echo '<td>' . $row['warehouse'] . '</td>';
		echo '<td data-sort="'. strtotime($row['deliverydate']) .'">' . $row['deliverydate'] . '</td>';

		echo '<td class="">';
		echo '<a href="'. hasAccessForUrl('delivery.php?delivery_number=' . $row['delivery_number'] . '&orderid=' . $row['orderid'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:5px 0px;">Ondertekenen</button></a>';
		echo '<a href="'. hasAccessForUrl('delivery.php?delivery_number=' . $row['delivery_number'] . '&orderid=' . $row['orderid'] . '&print', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Printen</button></a>';
		echo '</td>';
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
