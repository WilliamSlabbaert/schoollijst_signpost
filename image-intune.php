<?php

$title = 'Intune Images';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<h3>Intune Images</h3>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col">Image</th>
				<th scope="col">EMS E3</th>
				<th scope="col">Script In Intune</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT images2020.id, images2020.synergyid, schools.school_name, images2020.name, images2020.emse3, images2020.intunescripting FROM images2020 left join schools on images2020.synergyid = schools.synergyid where authentication = 'intune' ORDER BY images2020.id";
			$result = $conn->query($sql);
			$class = "";

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					$url = "document.location = 'image.php?id=" . $row['id'] . "'";

					if ($row['emse3'] == 0) {
						$class = "btn-outline-danger";
					} else {
						$class = "";
					}

					echo '<tr onclick="' . $url . '" class="' . $class . '">';
					echo '<td scope="row">' . $row['synergyid'] . '</td>';

					echo '<td>' . $row['school_name'] . '</td>';
					echo '<td>' . $row['name'] . '</td>';
					echo '<td>' . $row['emse3'] . '</td>';
					echo '<td>' . $row['intunescripting'] . '</td>';

					echo '<td class=""><a href=""><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Intune ok</button></a><a href=""><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px 5px;">Scripting ingesteld</button></a></td>';
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
