<?php

$title = 'Copaco Orders';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Copaco Orders</h3>

	<table class="table" id="table2">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, q.id as orderid, q.synergyid as synergyidid, images2020.name as imagename, q.SPSKU as signpostsku, (SELECT count(serialnumber)
				FROM `labels` where orderid = q.id and serialnumber != '') as done,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
				FROM `byod-orders`.orders as q
				LEFT JOIN `byod-orders`.devices ON q.SPSKU = devices.SPSKU
				LEFT JOIN schools on q.synergyid = schools.synergyid
				LEFT JOIN images2020 on q.imageid = images2020.id
				WHERE q.status = 'imaging' AND warehouse = 'Copaco'
				ORDER BY q.synergyid";
			$result = $conn->query($sql);
			$schools = "";

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					if($schools !== $row['synergyidid']){
						echo '
							<tr class="table-primary">
								<th scope="col">' . $row['synergyidid'] . '</th>
								<th scope="col">' . $row['school_name'] . '</th>
								<th scope="col">Image</th>
								<th scope="col">Scripting</th>
								<th scope="col">Aantal</th>
								<th scope="col">Todo</th>
								<th scope="col"></th>
							</tr>
						';
						$schools = $row['synergyidid'];
					}

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
					echo '<td scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';

					echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['signpostsku'] . '</span></td>';

					if ($row['imagename'] !== '' && isset($row['imagename']) !== false) {
						$imagename = $row['imagename'];
					} else {
						$imagename = $row['imageid'];
					}

					echo '<td>' . $imagename . '</td>';

					if ($row['authentication'] == '' || $row['authentication'] == 'intune') {
						$scripting = "Nee";
					} else {
						$scripting = "Ja ⚠";
					}

					echo '<td>' . $scripting . '</td>';

					echo '<td>' . $row['amount'] . '</td>';
					echo '<td><strong>' . $todo . '</strong></td>';

					echo '<td class="" style="text-align: right;">';

					echo '<a href="'. hasAccessForUrl('imaging.php?id=' . $row['orderid'] . '&edit=true', false).'">
					<button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px 5px;">Imagen</button>
					</a>';

					if ($color == 'btn-outline-success') {
						echo '<a href="'.hasAccessForUrl('imaging.php?id=' . $row['orderid'] . '&finish=true', false).'">
						<button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Afwerken</button>
						</a>';
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

<script>
	$(document).ready( function () {
		$('#table2').DataTable( {
			"paging":   false,
			//"order": [[ 0, "asc" ]],
			"ordering": false,
			"info":     false
		} );
	} );
</script>

<?php
include('footer.php');
?>
