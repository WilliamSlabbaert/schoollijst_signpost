<?php

$title = 'Ombouw Orders';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Orders</h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Order ID</th>
				<th scope="col">Toestel</th>
				<th scope="col">Ombouw Van</th>
				<th scope="col">Image al af?</th>
				<th scope="col">Vrijblijvend</th>
				<th scope="col">Aantal</th>
				<th scope="col">Nog te doen</th>
				<th scope="col">Picked</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
<?php

$sql = "SELECT *, q.id as orderid,
	IFNULL((SELECT count(laptopnr) FROM `device-swap` where orderid = q.id ), '0') as generated,
	( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
	(SELECT count(serialnumber) FROM `device-swap` where orderid = q.id and serialnumber != '') as done,
	( SELECT ifnull(sum(amount), 0) FROM orderpicking WHERE orderid = q.id ) as picked,
	( SELECT id FROM orderpicking WHERE orderid = q.id LIMIT 1 ) as pickingid
	FROM `byod-orders`.orders as q
	LEFT JOIN `byod-orders`.devices ON q.SPSKU = devices.SPSKU
	WHERE status = 'ombouw' ORDER BY orderid";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		$todo = $row['amount'] - $row['done'];

		if ($row['done'] >= 1 && $todo != 0) {
			$color = 'btn-outline-warning';
		} elseif ($todo == 0) {
			$color = 'btn-outline-success';
		} else {
			$color = 'btn-outline-danger';
		}

		$url = "document.location = 'order.php?id=" . $row['orderid'] . "'";

		echo '<tr onclick="' . $url . '" class="' . $color . '">';
		echo '<td scope="row"><strong>SP-BYOD20-' . $row['orderid'] . '</strong></td>';

		echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';

		/* ombouw kolom*/
		echo '<td>';

		if ($row['ssdswap'] !== "") {
			echo "SSD (" . $row['ssd_code'] . " -> " . $row['ssdswap'] . ")<br>";
		}

		if ($row['memoryswap'] !== "") {
			echo "RAM Slot 1 (" . $row['memory_code'] . " -> " . $row['memoryswap'] . ")<br>";
		}

		if ($row['memoryswap2'] !== "") {
			echo "RAM Slot 2 ( ...  -> " . $row['memoryswap2'] . ")<br>";
		}

		if ($row['panelswap'] !== "") {
			echo "Panel (" . $row['panel_code'] . " -> " . $row['panelswap'] . ")<br>";
		}

		if ($row['keyboardswap'] !== "") {
			echo "Keyboard (" . $row['keyboard_code'] . " -> " . $row['keyboardswap'] . ")<br>";
		}

		echo '</td>';

		if($row['imageid'] !== 'idk'){
			echo '<td>✔</td>';
		} else {
			echo '<td>❌</td>';
		}

		echo '<td>' . $row['unobligated'] . '</td>';
		echo '<td>' . $row['amount'] . '</td>';

		echo '<td>' . $todo . '</td>';

		echo '<td>' . $row['picked'] . '</td>';

		echo '<td class="">';

		if ($row['generated'] == '0') {
			echo '<a href="'. hasAccessForUrl('ombouw.php?id=' . $row['orderid'] . '&synergyid=' . $row['synergyid'] . '&amount=' . $row['amount'] . '&generate=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Pick Order</button></a>';
		} else {
			echo '<a href="'. hasAccessForUrl('ombouw.php?id=' . $row['orderid'] . '&edit=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px 5px;">Ombouwen</button></a>';
		}

		if ($todo == '0') {
			echo '<a href="'. hasAccessForUrl('ombouw.php?id=' . $row['orderid'] . '&finish=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:5px;">Afwerken</button></a>';
		}

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
