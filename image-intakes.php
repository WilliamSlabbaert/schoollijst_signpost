<?php

$title = 'Image Intakes';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Image Intakes</h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">Aantal forecasts</th>
				<th scope="col">School Naam</th>
				<th scope="col">Intake verzonden ?</th>

				<?php if (hasRole($role, ['software', 'management'])) { ?>
				<th scope="col">Verzend intake</th>
				<th scope="col">Goedgekeurde images</th>
				<?php } ?>

			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, guid AS schoolguid, synergyid AS newsynergyid, ( SELECT COUNT(*) FROM forecasts WHERE synergyid = newsynergyid AND deleted != 1) AS 'forecastsingevuld', ( SELECT COUNT(*) FROM images2019 WHERE guid = schoolguid AND okvoor2020 = '1' ) AS '2019', ( SELECT COUNT(*) FROM images2019 WHERE guid = schoolguid AND okvoor2020 != '1' ) AS '2019slecht', ( SELECT COUNT(*) FROM images2020 WHERE guid = schoolguid AND confirmed = '1' ) AS '2020', ( SELECT COUNT(*) FROM images2020 WHERE guid = schoolguid AND confirmed != '1' ) AS '2020slecht', ( SELECT COUNT(*) FROM devices LEFT JOIN forecasts ON synergyid = synergyid WHERE forecasts.synergyid = newsynergyid AND devices.spsku = forecasts.`device1-spsku` AND devices.operating_system = 'Chrome OS') AS chrome FROM schools";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					//$url = "document.location = 'image-form.php?q=" . $row['guid'] . "'";
					$url = "";
					$url2 = "mail-verzenden.php?q=" . $row['guid'] ;
					$goedgekeurd = $row['2019'] + $row['2020'];

					if ($goedgekeurd >= 1) {

						$color = 'btn-outline-success';
						$sent = 'image ontvangen';

					} elseif ($row['intake_sent'] == 1) {

						$color = 'btn-outline-warning';
						$sent = 'verzonden<br>' . $row['intake_sent_on'];

					} else {

						$color = "";
						$sent = 'nog niet';

					}


					if ($row['chrome'] == "0") {
						echo '<tr class="' . $color . '">';
						$schoolUrl = "document.location = 'school.php?synergyid=" . $row['synergyid'] . "'";
						echo '<th onclick="' . $schoolUrl . '" class="link" scope="row">' . $row['synergyid'] . '</th>';
						echo '<td onclick="' . $url . '"  >' . $row['forecastsingevuld'] . '</td>';
						echo '<td onclick="' . $url . '"  >' . $row['school_name'] . '</td>';
						echo '<td onclick="' . $url . '" >' . $sent . '</td>';

						if (hasRole($role, ['webshop', 'software', 'management'])) {
							echo '<td><a href="'. hasAccessForUrl(''.$url2.'', false).'"><button>x</button></a></td>';
							echo '<td>' . $goedgekeurd . '</td>';
						}
						echo '</tr>';
					}

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
