<?php

$title = 'Webshops';
include('head.php');
include('nav.php');
include('conn.php');

?>
<style>
	b{
		display: inline-block;
		width: 300px;
	}
</style>
<div class="body">

<?php
if (isset($_GET['status']) !== false) {

	$sql = "UPDATE webshops SET status='" . addslashes($_GET['status']) . "' WHERE id='" . $_GET['id'] . "'";

	if ($conn->query($sql) === TRUE) {
		echo "De webshop status is aangepast.<br><br>";
		echo '<a href="'. hasAccessForUrl('webshops.php', false).'" class="btn btn-secondary">Ga terug naar het overzicht</a>';
	} else {
		echo "Error updating record: " . $conn->error;
	}

	$conn->close();
	die();

} elseif (isset($_POST['feedback']) !== false) {

	$sql = "UPDATE webshops SET feedback='" . addslashes($_POST['feedback']) . "', status='fout-na-controle' WHERE id='" . $_POST['id'] . "'";

	if ($conn->query($sql) === TRUE) {

		echo "De webshop feedback is aangepast.<br><br>";
		echo '<a href="'. hasAccessForUrl('webshops.php', false).'" class="btn btn-secondary">Ga terug naar het overzicht</a>';

	} else {

		echo "Error updating record: " . $conn->error;

	}

	$conn->close();
	die();

} elseif (isset($_GET['feedback']) !== false) {

	$sql = "SELECT * FROM webshops WHERE id='" . $_GET['id'] . "'";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {
			echo '
				<form action="webshops.php" method="post">

					<label for="id">ID:</label><br>
					<input type="text" id="id" name="id" value="' . $_GET['id'] . '" class="form-control" readonly><br>

					<label for="name">Naam:</label><br>
					<input type="text" id="name" name="name" value="' . $row['webshop-name'] . '" class="form-control" readonly><br>

					<label for="feedback">Feedback:</label><br>
					<textarea id="feedback" name="feedback" rows="4" cols="50" class="form-control">' . $row['feedback'] . '</textarea><br>

					<input type="submit" value="Doorsturen" class="btn btn-secondary">

				</form>
			';
			echo '<a href="'. hasAccessForUrl('webshops.php', false).'" class="btn btn-secondary">Ga terug naar het overzicht</a>';
		}
	} else {
		echo "0 results";
	}

	$conn->close();

} elseif (isset($_GET['id']) !== false) {
?>

<h3>Webshop</h3>

<?php
$sql = "SELECT * FROM webshops where id = " . $_GET['id'];
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		// print_r($row);
		$requestdate = strtotime($row['date']);

		echo "
<img src=\"" . $row['logo'] . "\" width=\"300px\"><br><br>
<b>Synergy ID</b>" . $row['synergyid'] . "<br>
<b>Verkoopkans</b>" . $row['salesorderid'] . "<br>
<b>Sales</b>" . $row['sales'] . "<br>
<b>Aangevraagd op</b>" . date("d/m/Y H:m:s", $requestdate) . "<br><br>
<b>Status</b><strong style=\"color:red;text-decoration:underline;\">" . $row['status'] . "</strong><br><br>

<b>Webshop Naam</b>" . $row['webshop-name'] . "<br>
<b>Kopen?</b>" . $row['webshop'] . "<br>
<b>Huren?</b>" . $row['leermiddel'] . "<br>
<b>Bestaat de webshop al?</b>" . $row['webshop-exists'] . "<br><br>";

if (isset($row['feedback']) == 'true') {
	echo '<h4 style="color:red;">Feedback:<br>' . $row['feedback'] . "</h4><br>";
}

echo "<b>Opmerkingen</b><div>" . $row['remarks'] . "</div><br>

<b>Deadline</b>" . $row['webshop-deadline'] . "<br>
<b>Extra veld bij checkout</b>" . $row['special-field'] . "<br>
<b>Aanpassingen aan tekst?</b>" . $row['default-text-changes'] . "<br>

<b>Logo</b>" . $row['logo'] . "<br>
<br>
<div style=\"border:3px solid grey; padding:10px;\">" . nl2br(htmlspecialchars($row['webshop-text'])) . "</div><br>

<b>Startdatum contracten</b>";

if ($row['rent-startdate'] == 'nee' || $row['rent-startdate'] == "Neen") {
	echo "1 september";
} else {
	echo $row['rent-startdate'];
}

echo "<br>

<b>Adres</b>" . $row['shipping_postcode'] . " " . $row['shipping_city'] . " - " . $row['shipping_street'] . "<br><br>";

if ($row['device1-SPSKU'] !== false) {

	$laptoptype = "";
	$specs = "";
	$sql2 = "SELECT * FROM devices where SPSKU = '" . strtok($row['device1-SPSKU'], ';') . "'";
	$result2 = $conn->query($sql2);

	if ($result2->num_rows > 0) {

		while($row2 = $result2->fetch_assoc()) {
			$laptoptype = $row2['model'] . " " . $row2['motherboard_code'];
			$specs = $row2['motherboard_value'] . " - " . $row2['memory_value'] . "GB - " . $row2['ssd_value'] . "GB - " . $row2['panel_value'] . " - " . $row2['warranty'] . "j garantie";
		}
	}
	echo "
	<table class=\"table\">
		<thead class=\"thead-light\">
			<tr><th>Laptop 1</th><th>" . strtok($row['device1-SPSKU'], ';') . "</th><th>Hoes: " . $row['device1-sleve'] . "</th></tr>
			<tr><th></th><th>" . $laptoptype . "</th><th>" . $specs . "</th></tr>
		</thead>
		<tbody>
			<tr><td>Aankoop prijs: € " . $row['device1-price'] . "</td><td>Reparatie Kost: € " . $row['device1-repaircost'] . "</td><td>Consument: " . $row['device1-consumer'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs11'] . "</td><td>Huurtermijn: " . $row['huurtermijn11'] . "</td><td>Waarborg: " . $row['huurwaarborg11'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs12'] . "</td><td>Huurtermijn: " . $row['huurtermijn12'] . "</td><td>Waarborg: " . $row['huurwaarborg12'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs13'] . "</td><td>Huurtermijn: " . $row['huurtermijn13'] . "</td><td>Waarborg: " . $row['huurwaarborg13'] . "</td></tr>
		</tbody>
	</table>";
}

if ($row['device2-SPSKU'] !== "") {

	$laptoptype = "";
	$specs = "";
	$sql2 = "SELECT * FROM devices where SPSKU = '" . strtok($row['device2-SPSKU'], ';') . "'";
	$result2 = $conn->query($sql2);

	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {
			$laptoptype = $row2['model'] . " " . $row2['motherboard_code'];
			$specs = $row2['motherboard_value'] . " - " . $row2['memory_value'] . "GB - " . $row2['ssd_value'] . "GB - " . $row2['panel_value'] . " - " . $row2['warranty'] . "j garantie";
		}
	}

	echo "
	<table class=\"table\">
		<thead class=\"thead-light\">
			<tr><th>Laptop 2</th><th>" . strtok($row['device2-SPSKU'], ';') . "</th><th>Hoes: " . $row['device2-sleve'] . "</th></tr>
			<tr><th></th><th>" . $laptoptype . "</th><th>" . $specs . "</th></tr>
		</thead>
		<tbody>
			<tr><td>Aankoop prijs: € " . $row['device2-price'] . "</td><td>Reparatie Kost: € " . $row['device2-repaircost'] . "</td><td>Consument: " . $row['device2-consumer'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs21'] . "</td><td>Huurtermijn: " . $row['huurtermijn21'] . "</td><td>Waarborg: " . $row['huurwaarborg21'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs22'] . "</td><td>Huurtermijn: " . $row['huurtermijn22'] . "</td><td>Waarborg: " . $row['huurwaarborg22'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs23'] . "</td><td>Huurtermijn: " . $row['huurtermijn23'] . "</td><td>Waarborg: " . $row['huurwaarborg23'] . "</td></tr>
		</tbody>
	</table>";
}

if ($row['device3-SPSKU'] !== "") {

	$laptoptype = "";
	$specs = "";
	$sql2 = "SELECT * FROM devices where SPSKU = '" . strtok($row['device3-SPSKU'], ';') . "'";
	$result2 = $conn->query($sql2);

	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {
			$laptoptype = $row2['model'] . " " . $row2['motherboard_code'];
			$specs = $row2['motherboard_value'] . " - " . $row2['memory_value'] . "GB - " . $row2['ssd_value'] . "GB - " . $row2['panel_value'] . " - " . $row2['warranty'] . "j garantie";
		}
	}

echo "
<table class=\"table\">
	<thead class=\"thead-light\">
		<tr><th>Laptop 3</th><th>" . strtok($row['device3-SPSKU'], ';') . "</th><th>Hoes: " . $row['device3-sleve'] . "</th></tr>
		<tr><th></th><th>" . $laptoptype . "</th><th>" . $specs . "</th></tr>
	</thead>
	<tbody>
		<tr><td>Aankoop prijs: € " . $row['device3-price'] . "</td><td>Reparatie Kost: € " . $row['device3-repaircost'] . "</td><td>Consument: " . $row['device3-consumer'] . "</td></tr>
		<tr><td>Huurprijs: " . $row['huurprijs31'] . "</td><td>Huurtermijn: " . $row['huurtermijn31'] . "</td><td>Waarborg: " . $row['huurwaarborg31'] . "</td></tr>
		<tr><td>Huurprijs: " . $row['huurprijs32'] . "</td><td>Huurtermijn: " . $row['huurtermijn32'] . "</td><td>Waarborg: " . $row['huurwaarborg32'] . "</td></tr>
		<tr><td>Huurprijs: " . $row['huurprijs33'] . "</td><td>Huurtermijn: " . $row['huurtermijn33'] . "</td><td>Waarborg: " . $row['huurwaarborg33'] . "</td></tr>
	</tbody>
</table>";
}

if ($row['device4-SPSKU'] !== "") {

	$laptoptype = "";
	$specs = "";
	$sql2 = "SELECT * FROM devices where SPSKU = '" . strtok($row['device4-SPSKU'], ';') . "'";
	$result2 = $conn->query($sql2);

	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {
			$laptoptype = $row2['model'] . " " . $row2['motherboard_code'];
			$specs = $row2['motherboard_value'] . " - " . $row2['memory_value'] . "GB - " . $row2['ssd_value'] . "GB - " . $row2['panel_value'] . " - " . $row2['warranty'] . "j garantie";
		}
	}

	echo "
	<table class=\"table\">
		<thead class=\"thead-light\">
			<tr><th>Laptop 4</th><th>" . strtok($row['device4-SPSKU'], ';') . "</th><th>Hoes: " . $row['device4-sleve'] . "</th></tr>
			<tr><th></th><th>" . $laptoptype . "</th><th>" . $specs . "</th></tr>
		</thead>
		<tbody>
			<tr><td>Aankoop prijs: € " . $row['device4-price'] . "</td><td>Reparatie Kost: € " . $row['device4-repaircost'] . "</td><td>Consument: " . $row['device4-consumer'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs41'] . "</td><td>Huurtermijn: " . $row['huurtermijn41'] . "</td><td>Waarborg: " . $row['huurwaarborg41'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs42'] . "</td><td>Huurtermijn: " . $row['huurtermijn42'] . "</td><td>Waarborg: " . $row['huurwaarborg42'] . "</td></tr>
			<tr><td>Huurprijs: " . $row['huurprijs43'] . "</td><td>Huurtermijn: " . $row['huurtermijn43'] . "</td><td>Waarborg: " . $row['huurwaarborg43'] . "</td></tr>
		</tbody>
	</table>";

	}

	echo "<br><br><br><br><br><br><br><br>";

	}
} else {
	echo "0 results";
}
$conn->close();
?>




<?php
} else {
?>
	<h3>Webshops</h3>

	<table class="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Status</th>
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
				<th onclick="document.location = 'webshops.php'" class="<?php if($_GET['sort'] == ''){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>">Alles</th>
				<th scope="row" width="15%" class="<?php if($_GET['sort'] == 'nieuw'){ echo "table-danger";}else{ echo "btn-outline-danger";}; ?>" onclick="document.location = 'webshops.php?sort=nieuw'">Webshop Maken</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'aankoop-ok'){ echo "table-warning";}else{ echo "btn-outline-warning";}; ?>" onclick="document.location = 'webshops.php?sort=aankoop-ok'">Leermiddel Maken</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'fout-na-controle'){ echo "table-info";}else{ echo "btn-outline-info";}; ?>" onclick="document.location = 'webshops.php?sort=fout-na-controle'">Fout Na Controle</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'na-te-kijken'){ echo "table-primary";}else{ echo "btn-outline-primary";}; ?>" onclick="document.location = 'webshops.php?sort=na-te-kijken'">Na te kijken</th>
				<th scope="row" class="<?php if($_GET['sort'] == 'controle-klant'){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>" onclick="document.location = 'webshops.php?sort=controle-klant'">Controle door klant<br></th>
				<th scope="row" class="<?php if($_GET['sort'] == 'done'){ echo "table-success";}else{ echo "btn-outline-success";}; ?>" onclick="document.location = 'webshops.php?sort=done'">Done<br></th>
			</tr>
		</tbody>
	</table>
	<br>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">Sales</th>
				<th scope="col">Naam</th>
				<th scope="col">Nieuw?</th>
				<th scope="col">Kopen</th>
				<th scope="col">Huren</th>
				<th scope="col">Deadline</th>
				<th scope="col">Aanvraag Datum</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sort = "";
			if (isset($_GET['sort'])) {
				$sort = $_GET['sort'];
			}

			$sql = "SELECT * FROM `byod-orders`.webshops WHERE status LIKE '%" . $sort . "%' ORDER BY `date`";
			$result = $conn->query($sql);
			$new = "";
			// $schools = "";

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					$new = "";
					$webshop = "";
					$leermiddel = "";
					$status = "";
					$requestdate = strtotime($row['date']);
					$url = "document.location = 'webshops.php?id=" . $row['id'] . "'";

					if ($row['webshop-exists'] == "ja") {
						$new = "❌";
					} else {
						$new = "✔";
					}

					if ($row['webshop'] == "nee") {
						$webshop = "❌";
					} else {
						$webshop = "✔";
					}

					if ($row['leermiddel'] == "nee") {
						$leermiddel = "❌";
					} else {
						$leermiddel = "✔";
					}

					if ($row['status'] == 'nieuw') {
						$status = "btn-outline-danger";
					} else if ($row['status'] == 'aankoop-ok') {
						$status = "btn-outline-warning";
					} else if ($row['status'] == 'fout-na-controle') {
						$status = "btn-outline-info";
					} else if ($row['status'] == 'na-te-kijken') {
						$status = "btn-outline-primary";
					} else if ($row['status'] == 'controle-klant') {
						$status = "btn-outline-secondary";
					} else if ($row['status'] == 'done') {
						$status = "btn-outline-success";
					}

					echo '<tr onclick="' . $url . '" class="' . $status . '">';
					echo '<td scope="row">' . $row['synergyid'] . '</td>';
					echo '<td>' . $row['sales'] . '</td>';
					echo '<td>' . $row['webshop-name'] . '</td>';
					echo '<td>' . $new . '</td>';
					echo '<td>' . $webshop . '</td>';
					echo '<td>' . $leermiddel . '</td>';
					echo '<td>' . $row['webshop-deadline'] . '</td>';
					echo '<td>' . date('d/m', $requestdate) . '</td>';

					echo '<td class="">';

					if ($row['status'] == 'nieuw') {

						echo '<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&status=aankoop-ok', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Aankoop OK</button></a><p class="info-text">Klik hier als de shop is aangemaakt</p>';

					} else if ($row['status'] == 'fout-na-controle') {

						echo '<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&status=na-te-kijken', false).'"><button type="button" class="btn btn-secondary" style="width:120px !important;padding:0px;margin:0px 5px;">Fouten zijn aangepast</button></a><p class="info-text">Klik hier als de fouten zijn aangepast</p>';

					}  else if ($row['status'] == 'aankoop-ok') {

						echo '<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&status=na-te-kijken', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Leermiddel OK</button></a><p class="info-text">Klik hier als de Leermiddel is aangemaakt</p>';

					}  else if ($row['status'] == 'na-te-kijken'  && ( $loginname == "Jens.cuypers" || $loginname == "Karin" || $loginname == "Jordy" )) {

						echo '<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&status=controle-klant', false).'"><button type="button" class="btn btn-success" style="height:25px !important;width:120px !important;padding:0px;margin:5px 5px;">Goed</button></a>
						<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&feedback=true', false).'"><button type="button" class="btn btn-danger" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Fouten</button></a>
						<p class="info-text">Klik op OK als alles goed staat, en op Fouten als er nog dingen aangepast moeten worden</p>';

					}   else if ($row['status'] == 'controle-klant' && ( $loginname == "Thomas" || $loginname == "Jens.cuypers" || $loginname == "Jordy" )) {

						echo '<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&status=done', false).'"><button type="button" class="btn btn-success" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Done</button></a>
						<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&feedback=true', false).'"><button type="button" class="btn btn-danger" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Feedback</button></a>
						<p class="info-text">Klik op Done als alles ok is, of op Feedback als er nog dingen aangepast moeten worden</p>';

					}   else if ($row['status'] == 'done' && ( $loginname == "Thomas" || $loginname == "Jens.cuypers" || $loginname == "Jordy" )) {

						echo '<a href="'. hasAccessForUrl('webshops.php?id=' . $row['id'] . '&feedback=true', false).'"><button type="button" class="btn btn-danger" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Feedback</button></a>';

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

<?php } ?>

</div>

<?php
include('footer.php');
?>
