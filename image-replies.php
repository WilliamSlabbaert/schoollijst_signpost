<?php

$title = 'Alle image antwoorden';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">
<?php

if (isset($_POST['guid']) == true) {

	echo "<br><br><br>";
	$guid = mysqli_real_escape_string($conn, $_POST['guid']);
	$bevestiging = mysqli_real_escape_string($conn, $_POST['bevestiging']);
	$toestel = mysqli_real_escape_string($conn, $_POST['toestel']);
	$deliverydate = mysqli_real_escape_string($conn, $_POST['deliverydate']);
	$imageid = mysqli_real_escape_string($conn, $_POST['imageid']);

	if ($_POST['jaar'] == "2019") {
		$sql = "UPDATE images2019 SET okvoor2020='" . $bevestiging . "', toestel2020='" . $toestel . "', leverdatum2020='" . $deliverydate . "' WHERE id='" . $imageid . "' AND GUID='" . $guid . "'";
	} elseif ($_POST['jaar'] == "2020") {
		$sql = "UPDATE images2020 SET confirmed='" . $bevestiging . "' WHERE id='" . $imageid . "' AND guid='" . $guid . "'";
	}

	if ($conn->query($sql) === TRUE) {
		echo 'Uw feedback is opgeslagen.<br><a href="'. hasAccessForUrl('image-replies.php?guid=' . $guid . '', false).'">Klik hier om terug te gaan naar het overzicht</a><br><br><br></div>';
	} else {
		echo "Error updating record: " . $conn->error;
		echo "<br>Contacteer aub Signpost via softwaresupport@signpost.be<br>Onze excuses voor het ongemak<br><br><br>";
	}

	$conn->close();


} else if (isset($_GET['guid']) !== false && isset($_GET['id']) !== false && isset($_GET['jaar']) !== false) {

	$id = mysqli_real_escape_string($conn, $_GET['id']);
	$guid = mysqli_real_escape_string($conn, $_GET['guid']);
	$jaar = mysqli_real_escape_string($conn, $_GET['jaar']);

if ($_GET['jaar'] == "2020") {
	$sql = "SELECT *, images2020.SPSKU as SPSKU, CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) as devicebeschrijving FROM images" . $jaar . " LEFT JOIN devices on devices.spsku = images" . $jaar . ".SPSKU WHERE guid = '" . $guid . "' AND images" . $jaar . ".id = '" . $id . "'";
	# code...
} else if ($_GET['jaar'] == "2019"){
	$sql = "SELECT * FROM images" . $jaar . " WHERE guid = '" . $guid . "' AND images" . $jaar . ".id = '" . $id . "'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {

	$number = 1;

	while($row = $result->fetch_assoc()) {

		if ($_GET['jaar'] == "2019") {

			echo '<a href="'. hasAccessForUrl('image-replies.php?guid=' . $row['GUID'] . '', false).'">Terug naar het overzicht</a><br><br>';
			echo "<h3>Info over image</h3>";

			echo '<table class="table table-sm table-striped" style="table-layout: fixed; width: 100%">';
			echo "<tr><td>Image Naam</td><td>" . $row['ImageNaam'] . "</td></tr>";
			echo "<tr><td>Contactgegevens</td><td>" . $row['TechnischContactpersoonNaam'] . "<br>" . $row['TechnischContactpersoonTel'] . "<br>" . $row['TechnischContactpersoonGsm'] . "<br>" . $row['Email'] . "</td></tr>";
			echo "<tr><td>Voor toestel</td><td>" . $row['toestel'] . "</td></tr>";
			echo "<tr><td>Type image</td><td>" . $row['ImageKeuze'] . "</td></tr>";
			echo "<tr><td>Aanmeldmethode</td><td>" . $row['Authenticatie'] . "</td></tr>";
			echo "<tr><td>Exta informatie over aanmeldmethode</td><td>" . $row['Lokaal'] . "" . $row['VPN'] . "" . $row['VPNAccount'] . "" . $row['Intune'] . "</td></tr>";

			if ($row['Authenticatie'] == "intune") {
				if ($row['emse3bool'] == "1") {
					echo "<tr><td>Beschikt over EMSE3 licenties?</td><td>Ja</td></tr>";
				} else {
					echo "<tr><td>Beschikt over EMSE3 licenties?</td><td>Nee</td></tr>";
				}
			}

			echo "<tr><td>Software</td><td style=\"word-wrap: break-word\">" . $row['CustomSoftwareCheck'] . "<br>" . $row['CustomSoftware'] . "<br>" . $row['ExtraCustomSoftware'] . "</td></tr>";

			echo "<tr><td>Label</td><td>" . $row['Labeling'] . "</td></tr>";
			echo "<tr><td>Opmerkingen</td><td style=\"word-wrap: break-word\">" . $row['Comment'] . "</td></tr>";
			echo "<tr><td>Leverdatum 2020</td><td>" . $row['leverdatum2020'] . "</td></tr>";
			echo "<tr><td>Toestellen 2020</td><td>" . $row['toestel2020'] . "</td></tr>";

			if ($row['okvoor2020'] == "1") {
				echo "<tr><td>Bevestigd voor gebruik in 2020</td><td>✅</td></tr>";
			} elseif ($row['okvoor2020'] == "-1") {
				echo "<tr><td>Bevestigd voor gebruik in 2020</td><td>❌</td></tr>";
			} else {
				echo "<tr><td>Bevestigd voor gebruik in 2020</td><td>Nog niet doorgegeven</td></tr>";
			}
			echo "</table>";
			echo "<br><br>";

			if ($row['okvoor2020'] !== "1" && $row['okvoor2020'] !== "-1") {
				echo "<h3>Beoordeling</h3>";
				echo '
				<form action="image-replies.php" method="post">
				<div class="form-group mb-4">

					<input type="text" id="imageid" name="imageid" value="' . $row['id'] . '" hidden>
					<input type="text" id="guid" name="guid" value="' . $row['GUID'] . '" hidden>
					<input type="text" id="jaar" name="jaar" value="2019" hidden>

					<input type="radio" id="goed" name="bevestiging" value="1" required>
					<label for="goed" style="color:green;">Ik keur deze image goed voor 2020</label><br>
					<input type="radio" id="slecht" name="bevestiging" value="-1">
					<label for="slecht" style="color:red;">Ik keur deze image af voor 2020</label><br><br>

					<label for="toestel">Voor toestel(en):*</label>
					<!-- <small class="form-text pb-1">Bijvoorbeeld: Lenovo L390 i3, HP 430 G6 Celeron, ...</small> -->
					<!-- <input type="text" name="toestel" class="form-control verify" id="toestel"  placeholder=""> -->

					<select id="ToestelSelect" class="form-control" name="toestel" style="width:100% !important;" multiple="multiple">
					';
					$sql = "SELECT `device1-SPSKU`, devices.* FROM forecasts LEFT JOIN devices ON forecasts.`device1-SPSKU` = devices.SPSKU LEFT JOIN schools ON forecasts.synergyid = schools.synergyid WHERE synergyidold= '".$row['SynergyID']."' AND deleted != 1 UNION ALL
SELECT `device2-SPSKU`, devices.* FROM forecasts LEFT JOIN devices ON forecasts.`device2-SPSKU` = devices.SPSKU LEFT JOIN schools ON forecasts.synergyid = schools.synergyid WHERE synergyidold= '".$row['SynergyID']."' AND deleted != 1 UNION ALL
SELECT `device3-SPSKU`, devices.* FROM forecasts LEFT JOIN devices ON forecasts.`device3-SPSKU` = devices.SPSKU LEFT JOIN schools ON forecasts.synergyid = schools.synergyid WHERE synergyidold= '".$row['SynergyID']."' AND deleted != 1 UNION ALL
SELECT `device4-SPSKU`, devices.* FROM forecasts LEFT JOIN devices ON forecasts.`device4-SPSKU` = devices.SPSKU LEFT JOIN schools ON forecasts.synergyid = schools.synergyid WHERE synergyidold= '".$row['SynergyID']."' AND deleted != 1";

					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
					if($row['SPSKU']!=''){
					echo "<option value='".$row['SPSKU']."'>".$row['model']." - " .$row['warranty']." jaar garantie - " .$row['SPSKU']. "</option>";}
					}
					}

					echo '
					</select>
				</div>
				<label for="deliverydate">Leverdatum*:</label>
				<input type="date" name="deliverydate" class="form-control verify" id=""  placeholder="" value="" style="color:black"><br>

				<input type="submit" value="Submit">
				</form>
				<br><br><br><br><br><br><br><br><br>
				';
			}
			echo "</div>";

		} else {

			echo '<a href="'. hasAccessForUrl('image-replies.php?guid=' . $row['guid'] . '', false).'">Terug naar het overzicht</a><br><br>';
			echo "<h3>Info over image</h3>";

			echo '<table class="table table-sm table-striped" style="table-layout: fixed; width: 100%">';
			echo "<tr><td>Image Naam</td><td>" . $row['name'] . "</td></tr>";
			echo "<tr><td>Contactgegevens</td><td>" . $row['contactname'] . "<br>" . $row['contacttel'] . "<br>" . $row['contactemail'] . "</td></tr>";
			echo "<tr><td>Voor toestel</td><td>";

			$SPSKUS = explode(";",$row['SPSKU']);
			foreach ($SPSKUS as $key) {
				$sql2 = "SELECT * FROM devices WHERE spsku = '" . $key . "'";
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						echo $row2['model'] . ' - ' . $row2['motherboard_value'] . ' - ' . $row2['ssd_value'] . 'GB SSD - ' . $row2['memory_value'] . 'GB RAM - ' . $row2['panel_value'] . '<br>';
					}
				} else {
					echo "0 results";
				}
			}

			echo "</td></tr>";
			echo "<tr><td>Type image</td><td>" . $row['type'] . "</td></tr>";
			echo "<tr><td>Aanmeldmethode</td><td>" . $row['authentication'] . "</td></tr>";
			echo "<tr><td>Exta informatie over aanmeldmethode</td><td style=\"word-wrap: break-word\">" . $row['authentication_info'] . "</td></tr>";

			if ($row['authentication'] == "intune") {
				if ($row['emse3'] == "1") {
					echo "<tr><td>Beschikt over EMSE3 licenties?</td><td>Ja</td></tr>";
				} else {
					echo "<tr><td>Beschikt over EMSE3 licenties?</td><td>Nee</td></tr>";
				}
			}

			echo "<tr><td>Gratis software</td><td>" . $row['free_software'] . "</td></tr>";

			if (isset($row['paid_software']) == true) {
				echo "<tr><td>Extra (betalende) software</td><td>" . $row['paid_software'] . "</td></tr>";
			} else {
				echo "<tr><td>Extra (betalende) software</td><td>Geen</td></tr>";
			}


			echo "<tr><td>Label</td><td>" . $row['computername'] . "</td></tr>";
			echo "<tr><td>Leverdatum</td><td>" . $row['deliverydate'] . "</td></tr>";
			echo "<tr><td>Opmerkingen</td><td>" . $row['notes'] . "</td></tr>";

			if ($row['confirmed'] == "1") {
				echo "<tr><td>Bevestigd</td><td>✅</td></tr>";
			} elseif ($row['confirmed'] == "-1") {
				echo "<tr><td>Bevestigd</td><td>❌</td></tr>";
			} else {
				echo "<tr><td>Bevestigd</td><td>Nog niet doorgegeven</td></tr>";
			}
			echo "</table>";


			if ($row['confirmed'] !== "1" && $row['confirmed'] !== "-1") {
				echo "<h3>Beoordeling</h3>";
				echo '
				<form action="image-replies.php" method="post">
				<div class="form-group mb-4">

					<input type="text" id="imageid" name="imageid" value="' . $_GET['id'] . '" hidden>
					<input type="text" id="guid" name="guid" value="' . $_GET['guid'] . '" hidden>
					<input type="text" id="jaar" name="jaar" value="2020" hidden>

					<input type="radio" id="goed" name="bevestiging" value="1" required>
					<label for="goed" style="color:green;">Ik keur deze image goed voor 2020</label><br>
					<input type="radio" id="slecht" name="bevestiging" value="-1">
					<label for="slecht" style="color:red;">Ik keur deze image af voor 2020</label><br><br>

				<input type="submit" value="Submit">
				</form>
				<br><br><br><br><br><br><br><br><br>
				';
			}

		}

		$number++;

	}

} else {

	echo "0 results";

}

$conn->close();

?>

</div></div>

<?php

} else {

$schoolnaam = "";
$images = "";
$goedgekeurd = "";
$button = "";
$allesafgekeurd = 1;

$guid = mysqli_real_escape_string($conn, $_GET['guid']);

$sql2 = "SELECT images2020.id AS id, schools.school_name AS schoolnaam, images2020.synergyid, SPSKU AS toestel, '2020' AS jaar, NAME AS imagenaam, confirmed as goedgekeurd, images2020.guid FROM images2020
LEFT JOIN schools ON images2020.guid = schools.guid
WHERE images2020.guid = '" . $guid . "'
UNION ALL
SELECT images2019.id, schools.school_name AS schoolnaam, images2019.synergyid, toestel, '2019' AS jaar, imagenaam, okvoor2020 as goedgekeurd, images2019.guid FROM images2019
LEFT JOIN schools ON images2019.guid = schools.guid
WHERE images2019.guid = '" . $guid . "'";

$result2 = $conn->query($sql2);

if ($result2->num_rows > 0) {
	while($row = $result2->fetch_assoc()) {
		$schoolnaam = $row['schoolnaam'];

		if ($row['goedgekeurd'] == "1") {
			$goedgekeurd = "✅";
			$button = '<a href="'. hasAccessForUrl('image-replies.php?guid=' . $row['guid'] . '&id=' . $row['id'] . '&jaar=' . $row['jaar'] . '', false).'">Image bekijken</a>';
			//$allesafgekeurd = 0;
		} elseif ($row['goedgekeurd'] == "-1") {
			$goedgekeurd = "❌";
			$button = '<a href="'. hasAccessForUrl('image-replies.php?guid=' . $row['guid'] . '&id=' . $row['id'] . '&jaar=' . $row['jaar'] . '', false).'">Image bekijken</a>';
		} else {
			$goedgekeurd = "Nog niet doorgegeven";
			if($row['jaar'] == "2019"){
				$button = '<a href="'. hasAccessForUrl('image-replies.php?guid=' . $row['guid'] . '&id=' . $row['id'] . '&jaar=' . $row['jaar'] . '', false).'">Image beoordelen</a>';
			} elseif($row['jaar'] == "2020") {
				$button = '<a href="'. hasAccessForUrl('image-replies.php?guid=' . $row['guid'] . '&id=' . $row['id'] . '&jaar=' . $row['jaar'] . '', false).'">Image beoordelen</a>';
			}
			$allesafgekeurd = 0;

		}

		$images = $images . '<tr>
				<td scope="col">' . $row['toestel'] . '</td>
				<td scope="col">' . $row['imagenaam'] . '</td>
				<td scope="col">' . $row['jaar'] . '</td>
				<td scope="col">' . $goedgekeurd . '</td>
				<td scope="col">' . $button . '</td>
			</tr>';
	}
} else {
	echo "Spijtig genoeg zijn er geen bestaande images terug gevonden, contacteer byod@signpost.eu indien u vermoed dat dit niet klopt,<br>
		Of stel via onderstaande knop een nieuwe image samen:<br><br>";
	echo '<a href="'. hasAccessForUrl('image-form.php?q=' . $_GET['guid'] . '', false).'" class="btn btn-primary">Klik hier om een nieuwe image samen te stellen</a>';
	die();
}
$conn->close();

?>

	<h3>Alle Images van <?php echo $schoolnaam; ?></h3>
	<br>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Toestel</th>
				<th scope="col">Imagenaam</th>
				<th scope="col">Image uit jaar</th>
				<th scope="col">Goedgekeurd voor 2020</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

		echo $images;

		?>
		</tbody>
	</table>
	<?php
		if($allesafgekeurd == 1){
			echo '<a href="'. hasAccessForUrl('image-form.php?q=' . $_GET['guid'] . '', false).'">Klik hier om een nieuwe image in te geven</a>';
		}
	?>
</div>

<?php } ?>

<script>
	$('#ToestelSelect').select2({
		placeholder: "Selecteer Toestellen"
	});
</script>

<?php
include('footer.php');
?>
