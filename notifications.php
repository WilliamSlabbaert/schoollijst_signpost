<?php

if(isset($_GET['type']) == true){
	if($_GET['type'] == 'school'){
		$title = 'School Notificaties';
	} else {
		$title = 'Order Notificaties';
	}
} else {
	die('Geen type gevonden');
}
if(isset($_GET['all']) == true){
	$title = 'Alle ' . $title;
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
				<th scope="col">#</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Update</th>
				<th scope="col">Toegewezen op</th>
				<th scope="col">Status</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			if($_GET['type'] == 'school'){
				if(isset($_GET['all']) == true){
					$sql = "SELECT id, id as longid, synergyid, 'School' AS TYPE, updated_at, schoolstatus AS STATUS, school_name, schoolasignee as asignee
						FROM schools
						WHERE schoolasignee != ''";
				} else {
					$sql = "SELECT id, id as longid, synergyid, 'School' AS TYPE, updated_at, schoolstatus AS STATUS, school_name, schoolasignee as asignee
						FROM schools
						WHERE schoolasignee = '" . $loginname . "'";
				}
			} else {
				if(isset($_GET['all']) == true){
					$sql = "SELECT orders.id, CONCAT('SP-BYOD20-', orders.id) as longid, orders.synergyid, school_name, 'Order' AS TYPE, orders.updated_at, CONCAT(STATUS, '<br>', status_notes) AS STATUS, asignee
						FROM orders
						INNER JOIN schools ON schools.synergyid = orders.synergyid
						WHERE asignee != '' and orders.deleted != 1
						ORDER BY updated_at DESC";
				} else {
					$sql = "SELECT orders.id, CONCAT('SP-BYOD20-', orders.id) as longid, orders.synergyid, school_name, 'Order' AS TYPE, orders.updated_at, CONCAT(STATUS, '<br>', status_notes) AS STATUS, asignee
						FROM orders
						INNER JOIN schools ON schools.synergyid = orders.synergyid
						WHERE asignee = '" . $loginname . "' and orders.deleted != 1
						ORDER BY updated_at DESC";
				}
			}
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$updated_at = date("d/m/Y H:i", strtotime($row['updated_at']));
					echo '<tr>';
						echo '<th scope="row">' . $row['longid'] . '</th>';
						echo '<td>' . $row['synergyid'] . '</td>';
						echo '<td>' . $row['school_name'] . '</td>';
						echo '<td>' . $updated_at . '</td>';
						echo '<td>' . $row['asignee'] . '</td>';
						echo '<td>' . $row['STATUS'] . '</td>';
						echo '<td class="" style="text-align: right;">';

							if ($row['TYPE'] == "Order") {
								echo '<a href="'. hasAccessForUrl('order.php?id=' . $row['id'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:5px 0px;">Bekijken</button></a>';
							} elseif (substr( $row['id'], 0, 2 ) === "20" && $row['TYPE'] == 'Image') {
								echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&edit=true&jaar=2020', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:5px 0px;">Bekijken</button></a>';
							} elseif ($row['TYPE'] == 'Image') {
								echo '<a href="'. hasAccessForUrl('image.php?id=' . $row['id'] . '&edit=true&jaar=2019', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:5px 0px;">Bekijken</button></a>';
							} elseif ($row['TYPE'] == 'School') {
								echo '<a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:5px 0px;">Bekijken</button></a>';
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
