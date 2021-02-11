<?php

$title = 'Alle Images';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Alle Images</h3>

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
				<th onclick="document.location = 'image-list.php'" class="<?php if($_GET['sort'] == ''){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>">Alles</th>
				<th scope="row" width="15%" class="<?php if($_GET['sort'] == 'nieuw'){ echo "table-danger";}else{ echo "btn-outline-danger";}; ?>" onclick="document.location = 'image-list.php?sort=nieuw'">Nieuw</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'open'){ echo "table-warning";}else{ echo "btn-outline-warning";}; ?>" onclick="document.location = 'image-list.php?sort=open'">Open</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'testing'){ echo "table-info";}else{ echo "btn-outline-info";}; ?>" onclick="document.location = 'image-list.php?sort=testing'">Testing</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'fouten'){ echo "table-primary";}else{ echo "btn-outline-primary";}; ?>" onclick="document.location = 'image-list.php?sort=fouten'">Fouten na Testing</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'testingok'){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>" onclick="document.location = 'image-list.php?sort=testingok'">Testing OK</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'done'){ echo "table-success";}else{ echo "btn-outline-success";}; ?>" onclick="document.location = 'image-list.php?sort=done'">Done</th>
			</tr>
		</tbody>
	</table>
	<br>


	<table class="table" id="table2">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Image Naam</th>
				<th scope="col">Type</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Status</th>
				<th scope="col">Verantwoordelijke</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			if (isset($_GET['sort']) == true) {
				$sql = "SELECT images2020.id as id, images2020.guid AS imageid, images2020.synergyid AS synergyid, SPSKU, NAME AS imagenaam, type, `version`, status, asignee, confirmed, school_name, '2020' as year,
						(SELECT GROUP_CONCAT(shipping_date) FROM orders WHERE synergyid = images2020.synergyid ORDER BY DATE(shipping_date) ASC ) AS deadline
						FROM `byod-orders`.images2020
						LEFT JOIN `byod-orders`.schools ON images2020.synergyid = schools.synergyid
						WHERE status = '" . $_GET['sort'] . "' AND confirmed = 1
						GROUP BY id
						UNION ALL
						SELECT images2019.id as id, images2019.guid AS imageid, schools.synergyid AS synergyid, toestel2020 AS SPSKU, ImageNaam AS imagenaam, ImageKeuze AS type, version2020 AS `version`, status2020 AS status, Initials AS asignee, okvoor2020 AS confirmed, SchoolNaam, '2019' as year,
						(SELECT GROUP_CONCAT(shipping_date) FROM orders WHERE synergyid = images2019.synergyid ORDER BY DATE(shipping_date) ASC ) AS deadline
						FROM `byod-orders`.images2019
						LEFT JOIN `byod-orders`.schools ON images2019.synergyid = schools.synergyidold
						WHERE status2020 = '" . $_GET['sort'] . "' AND okvoor2020 = 1
						ORDER BY synergyid";
			} else {
				$sql = "SELECT images2020.id AS id, images2020.guid AS imageid, images2020.synergyid AS synergyid, SPSKU, NAME AS imagenaam, type, `version`, status, asignee, confirmed, school_name, '2020' AS year,
						(SELECT GROUP_CONCAT(shipping_date) FROM orders WHERE synergyid = images2020.synergyid ORDER BY DATE(shipping_date) ASC ) AS deadline
						FROM `byod-orders`.images2020
						LEFT JOIN `byod-orders`.schools ON images2020.synergyid = schools.synergyid
						WHERE confirmed = 1
						GROUP BY id
						UNION ALL
						SELECT images2019.id AS id, images2019.guid AS imageid, schools.synergyid AS synergyid, toestel2020 AS SPSKU, ImageNaam AS imagenaam, ImageKeuze AS type, version2020 AS `version`, status2020 AS status, Initials AS asignee, okvoor2020 AS confirmed, SchoolNaam, '2019' AS year,
						(SELECT GROUP_CONCAT(shipping_date) FROM orders WHERE synergyid = images2019.synergyid ORDER BY DATE(shipping_date) ASC ) AS deadline
						FROM `byod-orders`.images2019
						LEFT JOIN `byod-orders`.schools ON images2019.synergyid = schools.synergyidold
						WHERE okvoor2020 = 1
						ORDER BY synergyid";
			}

			$result = $conn->query($sql);
			$schools = "";

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					if($schools !== $row['synergyid']){
						echo '
							<tr class="table-primary">
								<th scope="col">' . $row['synergyid'] . '</th>
								<th scope="col">' . $row['school_name'] . '</th>
								<th scope="col">' . $row['deadline'] . '</th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>
							</tr>
						';
						$schools = $row['synergyid'];
					}

					if ($row['confirmed'] == "") {
						$color = 'btn-outline-secondary';
					} elseif ($row['status'] == "nieuw") {
						$color = 'btn-outline-danger';
					} elseif ($row['status'] == "open") {
						$color = 'btn-outline-warning';
					} elseif ($row['status'] == "testing") {
						$color = 'btn-outline-info';
					} elseif ($row['status'] == "fouten") {
						$color = 'btn-outline-primary';
					} elseif ($row['status'] == "finished") {
						$color = 'btn-outline-success';
					} else {
						$color = "";
					}

					$url = "document.location = 'image.php?id=" . $row['imageid'] . "&jaar='" . $row['year'];

					echo '<tr onclick="' . $url . '" class="' . $color . '">';
						echo '<th scope="row">' . $row['imagenaam'] . '</th>';
						echo '<td>' . $row['type'] . '</td>';

						echo '<td>';
						$deviceskusplit = explode(';', $row['SPSKU']);
						foreach($deviceskusplit as $split){
							$sql2 = "SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) as devicebeschrijving FROM devices WHERE SPSKU = '" . $split . "' LIMIT 1";
							$result2 = $conn->query($sql2);

							if ($result2->num_rows > 0) {
								while($row2 = $result2->fetch_assoc()) {
									echo $row2["devicebeschrijving"] . '<br>';
								}
							} else {
								echo "0 results";
							}
						}
						echo '<span class="smalltext">' . $row['SPSKU'] . '</span></td>';

						echo '<td>' . $row['status'] . '</td>';
						echo '<td>' . $row['asignee'] . '</td>';
						echo '<td class="" style="text-align: right;">';
						echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&edit=true&jaar=' . $row['year'] . '', false).'">
							<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:5px 0px;">Aanpassen</button>
							</a>';

						if (hasRole($role, ['software'])) {
							if ($row['status'] == "nieuw") {
								echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&jaar=' . $row['year'] . '&status=open', false).'">
										<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Starten</button>
									</a>';
							} elseif ($row['status'] == "open") {
								echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&jaar=' . $row['year'] . '&status=testing', false).'">
										<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Laten Testen</button>
									</a>';
							} elseif ($row['status'] == "testing") {
								// echo '<a href="">
								// 		<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Testen</button>
								// 	</a>';
							} elseif ($row['status'] == "fouten") {
								echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&jaar=' . $row['year'] . '&status=testing', false).'">
										<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Laten Testen</button>
									</a>';
							} elseif ($row['status'] == "testingok") {
								echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&jaar=' . $row['year'] . '&status=done', false).'">
										<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Laten Testen</button>
										<button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Afwerken</button>
									</a>';
							}
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
<br><br><br>

<script>
	$(document).ready( function () {
		$('#table2').DataTable( {
			"paging":   false,
			"ordering": false,
			"info":     false
		} );
	} );
</script>

<?php
include('footer.php');
?>
