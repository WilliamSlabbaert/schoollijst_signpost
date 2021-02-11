<?php

$title = 'Testing';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Image Testing</h3>
	<a href="<?php hasAccessForUrl( 'testing-form.php?id=1'); ?>">test form template</a><br><br>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">SPSKU</th>
				<th scope="col">Image Naam</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT images2020.id AS id, images2020.guid AS imageid, images2020.synergyid AS synergyid, SPSKU, NAME AS imagenaam, TYPE, `version`, STATUS, asignee, confirmed, school_name, '2020' AS jaar
					FROM `byod-orders`.images2020
					LEFT JOIN `byod-orders`.schools ON images2020.synergyid = schools.synergyid
					WHERE STATUS = 'testing' AND confirmed = 1
					UNION ALL
					SELECT images2019.id AS id, images2019.guid AS imageid, ( SELECT synergyid FROM schools WHERE synergyidold = images2019.synergyid ) AS synergyid, toestel2020 AS SPSKU, ImageNaam AS imagenaam, ImageKeuze AS TYPE, version2020 AS `version`, status2020 AS STATUS, Initials AS asignee, okvoor2020 AS confirmed, SchoolNaam, '2019' AS jaar
					FROM `byod-orders`.images2019
					WHERE status2020 = 'testing' AND okvoor2020 = 1
					ORDER BY synergyid";
			$result = $conn->query($sql);
			$schools = "";

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					echo '<tr>';
					echo '<td scope="row">' . $row['synergyid'] . '</td>';
					echo '<td scope="row">' . $row['school_name'] . '</td>';
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
					echo '<td>' . $row['imagenaam'] . '</td>';

					echo '<td class=""><a href="'. hasAccessForUrl('testing-form.php?imageid=' . $row['id'] . '&jaar=' . $row['jaar'] . '&edit=true', false).'"><button type="button" class="btn btn-light" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Starten Testing</button></a></td>';
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
