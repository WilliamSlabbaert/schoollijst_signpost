<?php

$title = 'Forecasts per device';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Forecasts per device</h3>
	<br>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">SPSKU</th>
				<th scope="col">Merk</th>
				<th scope="col">Model</th>
				<th scope="col">CPU</th>
				<th scope="col">RAM</th>
				<th scope="col">SSD</th>
				<th scope="col">Scherm</th>
				<th scope="col">Bevestigde forecast</th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT t.SPSKU,
					(SELECT manufacturer FROM devices WHERE spsku = SUBSTRING_INDEX(t.SPSKU,';',1) LIMIT 1) AS manufacturer,
					(SELECT model FROM devices WHERE spsku = SUBSTRING_INDEX(t.SPSKU,';',1) LIMIT 1) AS model,
					(SELECT motherboard_value FROM devices WHERE spsku = SUBSTRING_INDEX(t.SPSKU,';',1) LIMIT 1) AS motherboard_value,
					(SELECT ssd_value FROM devices WHERE spsku = SUBSTRING_INDEX(t.SPSKU,';',1) LIMIT 1) AS ssd_value,
					(SELECT memory_value FROM devices WHERE spsku = SUBSTRING_INDEX(t.SPSKU,';',1) LIMIT 1) AS memory_value,
					(SELECT panel_value FROM devices WHERE spsku = SUBSTRING_INDEX(t.SPSKU,';',1) LIMIT 1) AS panel_value,
					SUM(Forecast) AS 'Bevestigde Forecast'
					FROM (
					SELECT `device1-SPSKU` AS SPSKU, SUM(device1) AS Forecast
					FROM forecasts
					WHERE deleted != 1
					GROUP BY `device1-SPSKU`
					UNION ALL
					SELECT `device2-SPSKU` AS SPSKU, SUM(device2) AS Forecast
					FROM forecasts
					WHERE deleted != 1
					GROUP BY `device2-SPSKU`
					UNION ALL
					SELECT `device3-SPSKU` AS SPSKU, SUM(device3) AS Forecast
					FROM forecasts
					WHERE deleted != 1
					GROUP BY `device3-SPSKU`
					UNION ALL
					SELECT `device4-SPSKU` AS SPSKU, SUM(device4) AS Forecast
					FROM forecasts
					WHERE deleted != 1
					GROUP BY `device4-SPSKU`) t
					GROUP BY t.spsku";
			$result = $conn->query($sql);

			$total = 0;

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$total = $total + $row['Bevestigde Forecast'];
					$url = "#";
					echo '<tr onclick="' . $url . '">';
						echo '<th scope="row">' . $row['SPSKU'] . '</th>';
						echo '<td>' . $row['manufacturer'] . '</td>';
						echo '<td>' . $row['model'] . '</td>';
						echo '<td>' . $row['motherboard_value'] . '</td>';
						echo '<td>' . $row['memory_value'] . '</td>';
						echo '<td>' . $row['ssd_value'] . '</td>';
						echo '<td>' . $row['panel_value'] . '</td>';
						echo '<td>' . $row['Bevestigde Forecast'] . '</td>';
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
	echo '<table class="table"><thead><tr onclick="' . $url . '">';
	echo '<td scope="row"></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td>Totaal</td>';
	echo '<td>' . $total . '</td>';
	echo '</tr></thead></table>'; ?>
</div>

<script>
	$(document).ready( function () {
		$('#table').DataTable( {
			"paging":   false,
			"order": [[ 0, "asc" ]],
			//"ordering": false,
			"info":     false
		} );
	} );
</script>

<?php
include('footer.php');
?>
