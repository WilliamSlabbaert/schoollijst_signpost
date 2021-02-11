<?php

$title = 'Image Orders';
include('head.php');
include('nav.php');
include('conn.php');

?>

	<h3>Image Orders</h3>
	<p><em>Selecteer een image voor onderstaande orders</em></p>
	<a href="<?php hasAccessForUrl('image-orders.php?allorders=true'); ?>">Bekijk alle orders (ook niet gestart)</a>

<?php
if (isset($_POST['orderid']) !== false) {

print_r($_POST);


} elseif (isset($_POST['orderid']) !== false) {


} else {

?>
	<table class="table" id="table" data-show-fullscreen="true" style="table-layout: fixed;">
		<thead class="thead-dark">
			<tr>
				<th scope="col" style="width: 180px;">Image Selecteren</th>
				<th scope="col">Order ID</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Aantal</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Leverdatum</th>
				<th scope="col">Toegewezen Op</th>
				<th scope="col">Order Status</th>
				<th scope="col">Plaats</th>
				<th scope="col">Bev. Images</th>
				<th scope="col">Images Af</th>
			</tr>
		</thead>

		<tbody>
		<?php

			if(isset($_GET['allorders']) == true){
				$sql = "SELECT *, orders.id AS orderid, orders.synergyid AS ordersynergyid, orders.SPSKU AS orderspsku, shipping_date,
					(SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1) AS school_name,
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = orders.synergyid AND confirmed = '1') AS imagebevestigd2020,
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = orders.synergyid AND STATUS = 'done') AS imageaf2020,
					(SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND okvoor2020 = '1') AS imagebevestigd2019,
					(SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND status2020 = 'done') AS imageaf2019,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
					FROM orders
					WHERE orders.status != 'afgewerkt' AND (orders.imageid = 'nieuw' OR orders.imageid = 'idk') AND orders.deleted != 1 ORDER BY orders.id";
			} else {
				$sql = "SELECT *, orders.id AS orderid, orders.synergyid AS ordersynergyid, orders.SPSKU AS orderspsku, shipping_date,
					(SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1) AS school_name,
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = orders.synergyid AND confirmed = '1') AS imagebevestigd2020,
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = orders.synergyid AND STATUS = 'done') AS imageaf2020,
					(SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND okvoor2020 = '1') AS imagebevestigd2019,
					(SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON synergyidold = images2019.synergyid WHERE schools.synergyid = orders.synergyid AND status2020 = 'done') AS imageaf2019,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
					FROM orders
					WHERE orders.status != 'nieuw' AND orders.status != 'afgewerkt' AND (orders.imageid = 'nieuw' OR orders.imageid = 'idk') AND orders.deleted != 1 ORDER BY orders.id";
			}
			$result = $conn->query($sql);


			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					$imageaf = 0;
					$imagegoedgekeurd = 0;
						$url = "document.location = 'order.php?id=" . $row['orderid'] . "'";
					$imageaf += $row['imageaf2019'];
					$imageaf += $row['imageaf2020'];
					$imagegoedgekeurd += $row['imagebevestigd2019'] + $row['imagebevestigd2020'];

						echo '<tr class="">';
						echo '<td class=""><a href="'. hasAccessForUrl('image.php?orderid=' . $row['orderid'] . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:90px !important;padding:0px;margin:0px;">Selecteren</button></a></td>';
						echo '<td onclick="' . $url . '" scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';

						echo '<td onclick="' . $url . '">' . $row['ordersynergyid'] . '</td>';
						echo '<td onclick="' . $url . '">' . $row['school_name'] . '</td>';
						echo '<td onclick="' . $url . '">' . $row['amount'] . '</td>';
						echo '<td onclick="' . $url . '">' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['orderspsku'] . '</span></td>';
						echo '<td onclick="' . $url . '">' . $row['shipping_date'] . '</td>';
						echo '<td onclick="' . $url . '">' . $row['asignee'] . '</td>';
						echo '<td onclick="' . $url . '">' . $row['status'] . '<br>' . $row['status_notes'] . '</td>';
						echo '<td onclick="' . $url . '">' . $row['warehouse'] . '</td>';
						echo '<td onclick="' . $url . '">' . $imagegoedgekeurd . '</td>';
						echo '<td onclick="' . $url . '">' . $imageaf . '</td>';

					echo '</tr>';

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		?>

		</tbody>
	</table>
	<br><br><br>

<?php } ?>

<?php
include('footer.php');
?>
