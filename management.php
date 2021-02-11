<?php

$title = 'Management Alle Orders';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Alle Orders</h3>

	<table class="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Sorteren</th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th onclick="document.location = 'management.php'" class="<?php if($_GET['sort'] == ''){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>">Alles</th>
				<th scope="row" width="15%" class="<?php if($_GET['sort'] == 'nieuw'){ echo "table-danger";}else{ echo "btn-outline-danger";}; ?>" onclick="document.location = 'management.php?sort=nieuw'">nieuw</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'ombouw'){ echo "table-warning";}else{ echo "btn-outline-warning";}; ?>" onclick="document.location = 'management.php?sort=ombouw'">ombouw</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'wachten op image'){ echo "table-info";}else{ echo "btn-outline-info";}; ?>" onclick="document.location = 'management.php?sort=wachten op image'">wachten op image</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'imaging'){ echo "table-primary";}else{ echo "btn-outline-primary";}; ?>" onclick="document.location = 'management.php?sort=imaging'">imaging</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'levering'){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>" onclick="document.location = 'management.php?sort=levering'">levering</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'uitgeleverd'){ echo "table-success";}else{ echo "btn-outline-success";}; ?>" onclick="document.location = 'management.php?sort=uitgeleverd'">uitgeleverd</th>
			</tr>
		</tbody>
	</table>
	<br>

	<a href="<?php hasAccessForUrl('order-form.php'); ?>" class="btn btn-info">Nieuw Order doorgeven</a>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Aantal</th>
				<th scope="col">Status</th>
				<th scope="col">Toegewezen Op</th>
				<th scope="col">Sales</th>
				<th scope="col">Plaats</th>
				<th scope="col">-</th>
			</tr>
		</thead>

		<tbody>
		<?php

			if (isset($_GET['sort']) !== false) {
				$sql = "SELECT *, q.id AS orderid,
					(SELECT SUM(`device1`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts1,
					(SELECT SUM(`device2`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts2,
					(SELECT SUM(`device3`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts3,
					(SELECT SUM(`device4`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts4,
					((SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON images2019.synergyid = schools.synergyidold WHERE schools.synergyid = q.synergyid AND okvoor2020 = '1')+
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND confirmed = '1')) AS allimages,
					((SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON images2019.synergyid = schools.synergyidold WHERE schools.synergyid = q.synergyid AND status = 'done')+
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND status = 'done')) AS imagesdone,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-B1', 1), '-B2', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
					FROM `byod-orders`.orders q LEFT JOIN `byod-orders`.schools ON q.synergyid = schools.synergyid where status = '" . $_GET['sort'] . "' AND q.deleted != 1 ORDER BY q.synergyid";
			} else {
				$sql = "SELECT *, q.id AS orderid,
					(SELECT SUM(`device1`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts1,
					(SELECT SUM(`device2`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts2,
					(SELECT SUM(`device3`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts3,
					(SELECT SUM(`device4`) FROM forecasts WHERE synergyid = q.synergyid AND deleted != 1) AS totaalforecasts4,
					((SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON images2019.synergyid = schools.synergyidold WHERE schools.synergyid = q.synergyid AND okvoor2020 = '1')+
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND confirmed = '1')) AS allimages,
					((SELECT COUNT(*) FROM images2019 LEFT JOIN schools ON images2019.synergyid = schools.synergyidold WHERE schools.synergyid = q.synergyid AND status = 'done')+
					(SELECT COUNT(*) FROM images2020 WHERE synergyid = q.synergyid AND status = 'done')) AS imagesdone,
					( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-B1', 1), '-B2', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
					FROM `byod-orders`.orders q LEFT JOIN `byod-orders`.schools ON q.synergyid = schools.synergyid AND q.deleted != 1 ORDER BY q.synergyid";
			}
			$result = $conn->query($sql);
			$schools = "";
			$subtotaal = 0;
			$totaal = 0;
			$totaalforecasts = 0;

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					if($schools !== $row['synergyid']){
						if($subtotaal !== 0){

							if($subtotaal == $totaalforecasts){
								echo '<tr class="totaal" style="border:1px solid black !important; background-color:green; color:white;">';
							} elseif($subtotaal < $totaalforecasts){
								echo '<tr class="totaal" style="border:1px solid black !important; background-color:orange; color:white;">';
							} else {
								echo '<tr class="totaal" style="border:1px solid black !important; background-color:red; color:white;">';
							}
							echo '<th scope="col"></th>
								<th scope="col">Subtotaal</th>
								<th scope="col">' . $subtotaal . '</th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>
								</tr>
							';

						}

						$subtotaal = 0;
						$totaalforecasts = $row['totaalforecasts1'] + $row['totaalforecasts2'] + $row['totaalforecasts3'] + $row['totaalforecasts4'];

						$schoolUrl = "document.location = 'school.php?synergyid=" . $row['synergyid'] . "'";

						echo '
							<tr onclick="' . $schoolUrl . '" class="table-primary">
								<th scope="col" class="link">' . $row['synergyid'] . '</th>
								<th scope="col">' . $row['school_name'] . '</th>
								<th scope="col"></th>';

						if($row['allimages'] == '0'){
							echo '<th scope="col">Images: <b style="color:red;">Geen bevestigde intakes</b></th>';
						} else {
							echo '<th scope="col">Images: ' . $row['imagesdone'] . '/' . $row['allimages'] . '</th>';
						}

						echo '<th scope="col"></th>
							<th scope="col">In Forecast: ' . $totaalforecasts . '</th>
							<th scope="col"></th>
							<th scope="col"></th>
							</tr>
						';
						$schools = $row['synergyid'];
					}

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
					echo '<td scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';
					echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
					echo '<td>' . $row['amount'] . '</td>';
					echo '<td>' . $row['status'] . '<br>' . $row['status_notes'] . '</td>';
					echo '<td>' . $row['asignee'] . '</td>';
					echo '<td>' . $row['sales'] . '</td>';
					echo '<td>' . $row['warehouse'] . '</td>';

					if ($row['status'] == "nieuw") {
						echo '<td class=""><a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&edit=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Order Starten</button></a></td>';
					} else {
						echo '<td class=""><a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&synergyid=' . $row['synergyid'] . '&duplicate=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Order dupliceren</button></a></td>';
					}

					echo '</tr>';

					$subtotaal = $subtotaal + $row['amount'];
					$totaal = $totaal + $row['amount'];

				}

				echo '
					<tr class="totaal" style="border:1px solid black !important">
						<th scope="col"></th>
						<th scope="col">Subtotaal</th>
						<th scope="col">' . $subtotaal . '</th>
						<th scope="col"></th>
						<th scope="col"></th>
						<th scope="col"></th>
						<th scope="col"></th>
						<th scope="col"></th>
					</tr>
				';

				echo '
					<tr class="totaal" style="border:1px solid black !important">
						<th scope="col"></th>
						<th scope="col">Totaal</th>
						<th scope="col">' . $totaal . '</th>
						<th scope="col"></th>
						<th scope="col"></th>
						<th scope="col"></th>
						<th scope="col"></th>
						<th scope="col"></th>
					</tr>
				';

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
