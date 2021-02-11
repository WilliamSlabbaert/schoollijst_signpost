<?php

$title = 'Image ';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<?php

	if (isset($_POST['id'])) {

		$date = new DateTime();
		$date = $date->getTimestamp();

		if ($_POST['jaar'] == "2019") {

			if (isset($_POST['asignee']) == true && isset($_POST['internalnotes']) == true) {
				$sql = "UPDATE images2019 SET Initials='" . $_POST['asignee'] . "', internalnotes =
				CASE WHEN internalnotes IS NULL
					THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] " . $_POST['internalnotes'] . "')
					ELSE concat(internalnotes,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] " . $_POST['internalnotes'] . "')
				END
				WHERE id=" . $_POST['id'];
			} elseif (isset($_POST['asignee']) == true) {
				$sql = "UPDATE images2019 SET Initials='" . $_POST['asignee'] . "' WHERE id=" . $_POST['id'];
			} elseif (isset($_POST['internalnotes']) == true) {
				$sql = "UPDATE images2019 SET internalnotes =
				CASE WHEN internalnotes IS NULL
					THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] " . $_POST['internalnotes'] . "')
					ELSE concat(internalnotes,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] " . $_POST['internalnotes'] . "')
				END
				WHERE id=" . $_POST['id'];
			}

			if ($conn->query($sql) === TRUE) {
				echo "De wijzigingen zijn opgeslagen<br><br>";
				echo '<a href="'. hasAccessForUrl('image.php?id=' . $_POST['id'] . '&jaar=2019&edit=true', false).'">Klik hier om terug te gaan naar de image</a>';
			} else {
				echo "Error updating record: " . $conn->error;
			}

			$conn->close();

		} else {

			$internalnotes = mysqli_real_escape_string($conn, $_POST['internalnotes']);
			$asignee = mysqli_real_escape_string($conn, $_POST['asignee']);

			if (isset($_POST['asignee']) == true && isset($_POST['internalnotes']) == true) {
				$sql = "UPDATE images2020 SET asignee='" . $asignee . "', internalnotes =
				CASE WHEN internalnotes IS NULL
					THEN '[" . $loginname . " - " . date("d/m/Y H:i", $date) . "] " . $internalnotes . "'
					ELSE concat(internalnotes,'<br>[" . $loginname . " - " . date("d/m/Y H:i", $date) . "] " . $internalnotes . "')
				END
				WHERE id=" . $_POST['id'];
			} elseif (isset($_POST['asignee']) == true) {
				$sql = "UPDATE images2020 SET asignee='" . $asignee . "' WHERE id=" . $_POST['id'];
			} elseif (isset($_POST['internalnotes']) == true) {
				$sql = "UPDATE images2020 SET internalnotes =
				CASE WHEN internalnotes IS NULL
					THEN '[" . $loginname . " - " . date("d/m/Y H:i", $date) . "] " . $internalnotes . "'
					ELSE concat(internalnotes,'<br>[" . $loginname . " - " . date("d/m/Y H:i", $date) . "] " . $internalnotes . "')
				END
				WHERE id=" . $_POST['id'];
			}

			if ($conn->query($sql) === TRUE) {
				echo "De wijzigingen zijn opgeslagen<br><br>";
				echo '<a href="'. hasAccessForUrl('image.php?id=' . $_POST['id'] . '&jaar=2020&edit=true', false).'">Klik hier om terug te gaan naar de image</a>';
				//header($header);
			} else {
				echo "Error updating record: " . $conn->error;
			}

			$conn->close();

		}

	} elseif(isset($_GET['status'])!==false && isset($_GET['edit'])==false){

		echo "Ben je zeker om image " . $_GET['id'] . " aan te passen naar status " . $_GET['status'] . "<br>";

		if ($_GET['status'] == "open") {
			echo "Als je deze image op 'open' plaatst, dan is het de bedoeling dat je start met het maken van de image.<br>
			Klik op de rode knop om dit te bevestigen<br><br>";
		} elseif ($_GET['status'] == "testing") {
			echo "<b>Als je deze image op 'testing' plaatst. Dan is het de bedoeling dat je de laptop met de image eerst in het testlab legt, met een vermelding van de Synergy ID en naam van de image.</b><br>
			Klik op de rode knop om dit te bevestigen<br><br>";
		} elseif ($_GET['status'] == "done") {
			echo "<b>Als je deze image op 'done' plaatst. Dan is het de bedoeling dat deze image beschikbaar is op de server van Signpost, Techdata en Copaco.</b><br>
			Klik op de rode knop om dit te bevestigen<br><br>";
		}

		echo '<a href="'. hasAccessForUrl('image.php?id=' . $_GET['id'] . '&jaar=' . $_GET['jaar'] . '&status=' . $_GET['status'] . '&edit=true', false).'">
				<button type="button" class="btn btn-danger" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">' . $_GET['status'] . '</button>
			</a>
			<a href="'. hasAccessForUrl('image-mylist.php', false).'">
				<button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px;">Terug naar het overzicht</button>
			</a>';

	} elseif(isset($_GET['status'])!==false && isset($_GET['edit'])!==false){

		if ($_GET['jaar'] == "2019") {
			$sql = "UPDATE images2019 SET status2020='" . $_GET['status'] . "' WHERE id='" . $_GET['id'] . "'";
		} else {
			$sql = "UPDATE images2020 SET status='" . $_GET['status'] . "' WHERE id='" . $_GET['id'] . "'";
		}

		if ($conn->query($sql) === TRUE) {
			echo 'De status is aangepast naar ' . $_GET['status'] . '<br><br>
				<a href="'. hasAccessForUrl('image-mylist.php', false).'">
					<button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px;">Terug naar het overzicht</button>
				</a>';
		} else {
			echo "Error updating record: " . $conn->error;
		}

		$conn->close();

	} elseif(isset($_GET['id'])!==false && isset($_GET['jaar'])!==false){

		if ($_GET['jaar'] == '2020') {

			$sql = "SELECT *,
				( SELECT CONCAT(synergyid, '-', spsku, '-V', version, '-', NAME) FROM `byod-orders`.images2020 WHERE id = q.id ) AS imagename2020,
				( select count(*) from testlab where imageid = q.id ) as testlabforms FROM images2020 q WHERE q.id = " . $_GET['id'];
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					echo "<h3>Image aanpassen</h3>";

					echo "<table class=\"table table-sm table-striped\"  style=\"table-layout: fixed; width: 100%\">";

					// echo '<tr><td>id</td><td>' . $row['id'] . '</td></tr>';
					echo '<tr><td>Synergy ID</td><td>' . $row['synergyid'] . '</td></tr>';
					echo '<tr><td>Image Naam</td><td>' . strtoupper(str_replace(' ', '-', $row['imagename2020'])) . '</td></tr>';
					// echo '<tr><td>guid</td><td>' . $row['guid'] . '</td></tr>';
					echo '<tr><td>SPSKU</td><td>' . $row['SPSKU'] . '</td></tr>';
					echo '<tr><td>Contact</td><td>' . $row['contactname'] . '<br>' . $row['contactemail'] . '<br>' . $row['contacttel'] . '</td></tr>';
					echo '<tr><td>deliverydate</td><td>' . $row['deliverydate'] . '</td></tr>';
					echo "<tr><td></td><td></td></tr>";
					echo '<tr><td>Toegewezen aan</td><td>' . $row['asignee'] . '</td></tr>';
					echo "<tr><td></td><td></td></tr>";
					echo '<tr><td>Image Naam</td><td>' . $row['name'] . '</td></tr>';
					echo '<tr><td>Type Image</td><td>' . $row['type'] . '</td></tr>';

					echo '<tr><td>authentication</td><td><b>' . $row['authentication'] . '</b><br>' . $row['authentication_info'] . '</td></tr>';

					echo '<tr><td>Gratis Software</td><td style="word-wrap: break-word">' . str_replace(";", "<br>", $row['free_software']) . '</td></tr>';
					echo '<tr><td>Betaalde Software</td><td style="word-wrap: break-word">' . str_replace(";", "<br>", $row['paid_software']) . '</td></tr>';

					echo '<tr><td>Computer Naam</td><td>' . $row['computername'] . '</td></tr>';
					echo '<tr><td>Notities</td><td>' . $row['notes'] . '</td></tr>';
					// echo '<tr><td>history</td><td>' . $row['history'] . '</td></tr>';
					echo '<tr><td>Ingevuld op</td><td>' . $row['date'] . '</td></tr>';
					echo '<tr><td>Bevestigd</td><td>' . $row['confirmed'] . '</td></tr>';

					echo '<tr><td></td><td></td></tr>';

					echo '<tr><td>Aantal Tests</td><td>' . $row['testlabforms'] . '<br>';

					if ($row['testlabforms'] >= 1) {
						$sql3 = "SELECT * FROM testlab WHERE imageid = '" . $row['id'] . "'";
						$result3 = $conn->query($sql3);
						$testresultaten = 0;

						if ($result3->num_rows > 0) {
							while($row3 = $result3->fetch_assoc()) {
								$testresultaten++;
								echo 'Test ' . $testresultaten . ': <a href="'. hasAccessForUrl('testing-form.php?testingid=' . $row3['id'] . '', false).'" style="btn btn-secondary">Testresultaten bekijken</a><br>';
							}
						} else {
							echo "0 results";
						}

					} else {
						echo '<tr><td>Aantal Tests</td><td>' . $row['testlabforms'];
					}
					echo '</td></tr>';

					echo '<tr><td>Versie</td><td>' . $row['version'] . '</td></tr>';
					echo '<tr><td>Status</td><td>' . $row['status'] . '</td></tr>';
					echo '<tr><td>Interne opmerkingen</td><td>' . $row['internalnotes'] . '</td></tr>';

					echo "</table><br>";

					if (isset($_GET['edit']) == 'true') {
						echo '
						<div class="form-group">
						<h3>Informatie toevoegen</h3>
						<form action="image.php" method="post">
						<input type="text" class="form-control" name="id" value="' . $row['id'] . '" hidden>
						<input type="text" class="form-control" name="jaar" value="' . $_GET['jaar'] . '" hidden>';

						if ($row['asignee'] == "" || $loginname == "Jordy" || $loginname == "Mike") {
							echo '<label class="my-1 mr-2" for="asignee">Verantwoordelijke</label>
							<select class="custom-select my-1 mr-sm-2" id="asignee" name="asignee">
								<option value="">Kiezen...</option>';
							if($row['asignee'] == "Brent.spruyt"){ echo '<option value="Brent.spruyt" selected>Brent.spruyt</option>'; } else { echo '<option value="Brent.spruyt">Brent.spruyt</option>'; }
							if($row['asignee'] == "Jelle"){ echo '<option value="Jelle" selected>Jelle</option>'; } else { echo '<option value="Jelle">Jelle</option>'; }
							if($row['asignee'] == "Jens.pinoy"){ echo '<option value="Jens.pinoy" selected>Jens.pinoy</option>'; } else { echo '<option value="Jens.pinoy">Jens.pinoy</option>'; }
							if($row['asignee'] == "Joe.specker"){ echo '<option value="Joe.specker" selected>Joe</option>'; } else { echo '<option value="Joe.specker">Joe</option>'; }
							if($row['asignee'] == "Jordy"){ echo '<option value="Jordy" selected>Jordy</option>'; } else { echo '<option value="Jordy">Jordy</option>'; }
							if($row['asignee'] == "Quinten"){ echo '<option value="Quinten" selected>Quinten</option>'; } else { echo '<option value="Quinten">Quinten</option>'; }
							if($row['asignee'] == "Stef.pattyn"){ echo '<option value="Stef.pattyn" selected>Stef</option>'; } else { echo '<option value="Stef.pattyn">Stef</option>'; }
							echo '</select><br><br>';
						}

						echo '<label for="internalnotes">Interne opmerking</label>
							<textarea class="form-control" id="internalnotes" name="internalnotes" rows="6" placeholder="Vul hier een extra toevoeging of opmerking voor de image in."></textarea><br>';

						echo '<button type="submit" class="btn btn-success">Save</button>
							</form>';

						echo '</div>';
					}

					echo "<br><br><br><br><br><br><br><br>";

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		} else {

			$sql = "SELECT *,
				( SELECT CONCAT(( SELECT synergyid FROM schools WHERE synergyidold = images2019.synergyid), '-', toestel2020, '-V', version2020, '-', ImageNaam) FROM `byod-orders`.images2019 WHERE images2019.id = q.id ) AS imagename2019,
				( select count(*) from testlab where imageid = q.id ) as testlabforms, ( SELECT synergyid FROM schools WHERE schools.synergyidold = q.synergyid limit 1 ) as newsynergyid
				FROM images2019 q WHERE q.id = " . $_GET['id'];
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					echo "<h3>Image aanpassen</h3>";

					echo "<table class=\"table table-sm table-striped\"  style=\"table-layout: fixed; width: 100%\">";

					echo '<tr><td>Synergy ID</td><td>' . $row['newsynergyid'] . '</td></tr>';
					echo '<tr><td>Image Naam</td><td>' . $row['imagename2019'] . '</td></tr>';

					if($row['toestel2020'] == ''){
						echo '<tr><td>Toestel</td><td style="color:red;">Geen toestel gevonden</td></tr>';
					} else {
						echo '<tr><td>Toestel</td><td>' . $row['toestel2020'] . '</td></tr>';
					}
					echo '<tr><td>Contact</td><td>' . $row['TechnischContactpersoonNaam'] . '<br>' . $row['Email'] . '<br>' . $row['TechnischContactpersoonTel'] . '<br>' . $row['TechnischContactpersoonGsm'] . '</td></tr>';
					echo '<tr><td>Leverdatum</td><td>' . $row['leverdatum2020'] . '</td></tr>';
					// echo '<tr><td>Sales</td><td>' . $row['Sales'] . '</td></tr>';

					echo '<tr><td></td><td></td></tr>';
					echo '<tr><td>Toegewezen aan</td><td>' . $row['Initials'] . '</td></tr>';
					echo '<tr><td></td><td></td></tr>';

					echo '<tr><td>Ingevuld op</td><td>' . $row['Date'] . '</td></tr>';

					echo '<tr><td></td><td></td></tr>';
					echo '<tr><td>Image Naam</td><td>' . $row['ImageNaam'] . '</td></tr>';
					echo '<tr><td>Type Image</td><td>' . $row['ImageKeuze'] . '</td></tr>';
					echo '<tr><td>School Image Info</td><td>' . $row['SchoolImagineAanmaak'] . '</td></tr>';
					echo '<tr><td>Authenticatie</td><td>' . $row['Authenticatie'] . '<br>' . $row['Lokaal'] . '' . $row['VPN'] . '<br>' . $row['VPNAccount'] . '<br>' . $row['Intune'] . '<br>E3:' . $row['emse3bool'] . '<br></td></tr>';
					echo '<tr><td>Software</td><td style="word-wrap: break-word">' . $row['CustomSoftwareCheck'] . '</td></tr>';
					echo '<tr><td>Software</td><td style="word-wrap: break-word">' . $row['CustomSoftware'] . '</td></tr>';
					echo '<tr><td>Software</td><td style="word-wrap: break-word">' . $row['ExtraCustomSoftware'] . '</td></tr>';
					echo '<tr><td>Computernaam</td><td>' . $row['Hostname'] . '</td></tr>';
					echo '<tr><td>Label</td><td>' . $row['Labeling'] . '</td></tr>';
					echo '<tr><td>Notes</td><td>' . $row['Comment'] . '</td></tr>';
					echo '<tr><td>Bevestigd voor 2020</td><td>' . $row['okvoor2020'] . '</td></tr>';

					echo '<tr><td></td><td></td></tr>';


					echo '<tr><td>Aantal Tests</td><td>' . $row['testlabforms'] . '<br>';

					if ($row['testlabforms'] >= 1) {
						$sql3 = "SELECT * FROM testlab WHERE imageid = '" . $row['id'] . "'";
						$result3 = $conn->query($sql3);
						$testresultaten = 0;

						if ($result3->num_rows > 0) {
							while($row3 = $result3->fetch_assoc()) {
								$testresultaten++;
								echo 'Test ' . $testresultaten . ': <a href="'. hasAccessForUrl('testing-form.php?testingid=' . $row3['id'] . '', false).'" style="btn btn-secondary">Testresultaten bekijken</a><br>';
							}
						} else {
							echo "0 results";
						}

					} else {
						echo '<tr><td>Aantal Tests</td><td>' . $row['testlabforms'];
					}
					echo '</td></tr>';

					echo '<tr><td>Versie</td><td>' . $row['version2020'] . '</td></tr>';
					echo '<tr><td>Status</td><td>' . $row['status2020'] . '</td></tr>';
					echo '<tr><td>Werkwijze</td><td>' . $row['werkwijze'] . '</td></tr>';
					echo '<tr><td>Interne opmerkingen</td><td>' . $row['internalnotes'] . '</td></tr>';


					echo "</table><br>";

					if (isset($_GET['edit']) == 'true') {
						echo '
						<div class="form-group">
						<h3>Informatie toevoegen</h3>
						<form action="image.php" method="post">
						<input type="text" class="form-control" name="id" value="' . $row['id'] . '" hidden>
						<input type="text" class="form-control" name="jaar" value="' . $_GET['jaar'] . '" hidden>';

						if ($row['Initials'] == "" || $loginname == "Jordy" || $loginname == "Mike") {
							echo '<label class="my-1 mr-2" for="asignee">Verantwoordelijke</label>
							<select class="custom-select my-1 mr-sm-2" id="asignee" name="asignee">
								<option value="">Kiezen...</option>';
							if($row['Initials'] == "Jelle"){ echo '<option value="Jelle" selected>Jelle</option>'; } else { echo '<option value="Jelle">Jelle</option>'; }
							if($row['asignee'] == "Joe"){ echo '<option value="Joe.specker" selected>Joe</option>'; } else { echo '<option value="Joe.specker">Joe</option>'; }
							if($row['Initials'] == "Jordy"){ echo '<option value="Jordy" selected>Jordy</option>'; } else { echo '<option value="Jordy">Jordy</option>'; }
							if($row['Initials'] == "Quinten"){ echo '<option value="Quinten" selected>Quinten</option>'; } else { echo '<option value="Quinten">Quinten</option>'; }
							echo '</select><br><br>';
						}

						echo '<label for="internalnotes">Interne opmerking</label>
							<textarea class="form-control" id="internalnotes" name="internalnotes" rows="6" placeholder="Vul hier een extra toevoeging of opmerking voor de image in."></textarea><br>';

						echo '<button type="submit" class="btn btn-success">Save</button>
							</form>';

						echo '</div>';
					}

					echo "<br><br><br><br><br><br><br><br>";

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		}


	} elseif (isset($_GET['synergyid'])!==false) {

		$sql = "SELECT * FROM images2020 WHERE synergyid = " . $_GET['synergyid'];
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {

				echo "<b>" . $row['synergyid'] . "</b><br>";
				echo $row['SPSKU'] . "<br>";
				echo $row['name'] . "<br>";
				echo $row['type'] . "<br>";
				echo $row['authentication'] . "<br>";
				echo $row['authentication_info'] . "<br>";
				echo $row['free_software'] . "<br>";
				echo $row['paid_software'] . ".<br>";
				echo $row['emse3'] . "<br>";
				echo $row['computername'] . "<br>";
				echo $row['notes'] . "<br>";
				echo $row['asignee'] . "<br><br>";

			}

		} else {

			echo "0 results";

		}

	} elseif (isset($_GET['guid'])!==false) {

		$sql = "SELECT * FROM images2020 WHERE guid = '" . $_GET['guid'] . "'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$number = 1;
			while($row = $result->fetch_assoc()) {

				echo "<h3>Image " . $number . "</h3>";
				echo "<b>Image Naam:</b> " .  $row['name'] . "<br>";
				echo "<b>Soort:</b> " .  $row['type'] . "<br>";
				echo "<b>Aanmelding: </b>" .  $row['authentication'] . "<br>";
				echo "<b></b>" .  $row['emse3'] . "<br>";
				echo "<b></b>" .  $row['authentication_info'] . "<br>";
				echo "<b>Gratis Software: </b>" .  $row['free_software'] . "<br>";
				echo "<b>Betalende Software: </b>" .  $row['paid_software'] . ".<br>";
				echo "<b>Computernaam: </b>" .  $row['computername'] . "<br>";
				echo "<b>Extra informatie: </b>" .  $row['notes'] . "<br>";
				echo "<b>Toestellen: </b>" .  $row['SPSKU'] . "<br>";
				echo "<br><br>";

				$number++;
			}
		} else {
			echo "0 results";
		}

	} elseif (isset($_GET['orderid'])!==false) {

		$sql = "SELECT *, orders.id as orderid FROM orders left join schools on schools.synergyid = orders.synergyid WHERE orders.id = '" . $_GET['orderid'] . "' and orders.deleted != 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$number = 1;
			while($row = $result->fetch_assoc()) {


				echo '<h1>Kies een image voor SP-BYOD20-' . $row['orderid'] . '</h1>';
				echo '<p>' . $row['synergyid'] . '<br>';
				echo '' . $row['school_name'] . '<br>';
				echo '' . $row['SPSKU'] . '</p>';

				echo '<form action="image.php" method="post">

					<input type="text" name="orderid" hidden value="' . $row['orderid'] . '"><br>
					Warehouse:
					<input type="text" class="form-control" name="warehouse" readonly value="' . $row['warehouse'] . '"><br>';

				if($row['warehouse'] == 'TechData') {
					echo 'Image status aanpassen naar:<br><input type="text" class="form-control" name="status" readonly value="tdconfigadmin"><br>';
				} elseif($row['status'] == 'wachten op image') {
					echo 'Image status aanpassen naar:<br><input type="text" class="form-control" name="status" readonly value="imaging"><br>';
				} else {
					echo 'Image status aanpassen naar:<br><input type="text" class="form-control" name="status" readonly value="' . $row['status'] . '"><br>';
				}

				echo '<label style="font-weight: bold;" for="image">Image</label>
					<p>Kies een image uit de onderstaande lijst, indien u er geen te zien krijgt, zal er een image aangemaakt moeten worden</p>
					<select id="image" name="image" class="form-control" required>
					<option value="" selected disabled></option>
					<option value="fabriek">OOBE Fabriek (Lenovo/HP Incl. bloatware)</option>
					<option value="chrome">Chromebook</option>
					<option value="clean">Clean Windows 10</option>';

				$sql = "SELECT *, images2019.id as imageid FROM images2019
					LEFT JOIN schools ON schools.synergyidold = images2019.synergyid
					WHERE schools.synergyid = '" . $row['synergyid'] . "' AND status2020 = 'done'
					GROUP BY images2019.id";
				$result2 = $conn->query($sql);
				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						echo "<option value=" .  $row2['imageid'] . ">" .  $row2['toestel2020'] . " - " .  $row2['ImageNaam'] . " (id: " .  $row2['imageid'] . ")</option>";
					}
				} else {
					//echo "Er bestaan nog geen images voor deze school<br>";
				}

				$sql = "SELECT *,
					( SELECT CONCAT(synergyid, '-', spsku, '-V', version, '-', NAME) FROM `byod-orders`.images2020 WHERE id = q.id ) AS imagename2020
					FROM images2020 q
					WHERE (synergyid = '" . $row['synergyid'] . "' OR (synergyid = '666' AND SPSKU LIKE '" . substr($row['SPSKU'], '0', '11') . "%')) AND status = 'done'";
				$result3 = $conn->query($sql);
				if ($result3->num_rows > 0) {
					while($row3 = $result3->fetch_assoc()) {
						echo "<option value=" .  $row3['id'] . ">" .  $row3['imagename2020'] . " (id: " .  $row3['id'] . ")</option>";
					}
				} else {
					//echo "Er bestaan nog geen images voor deze school<br>";
				}

				echo '</select>

					<br>
					<button type="submit" class="btn btn-success">Opslaan</button>

					</form>
					<br><br>';

				$number++;
			}

		} else {

			echo "0 results";

		}

	} elseif (isset($_POST['orderid'])!==false){

		if ($_POST['image'] == "geen") {
			echo "Order is aangepast naar geen image voor Signpost<br><br>";
			$sql = "UPDATE orders SET `imageid` = '" . $_POST['image'] . "', `status` = '" . $_POST['status'] . "' WHERE id = " . $_POST['orderid'];
		} elseif ($_POST['warehouse'] == "TechData") {
			echo "Image is geselecteerd voor SP-BYOD20-" . $_POST['orderid'] . " voor TechData<br><br>";
			$sql = "UPDATE orders SET `imageid` = '" . $_POST['image'] . "', `status` = '" . $_POST['status'] . "' WHERE id = " . $_POST['orderid'];
		} else {
			echo "Image is geselecteerd voor SP-BYOD20-" . $_POST['orderid'] . " voor Signpost<br><br>";
			$sql = "UPDATE orders SET `imageid` = '" . $_POST['image'] . "', `status` = '" . $_POST['status'] . "' WHERE id = " . $_POST['orderid'];
		}

		if ($conn->query($sql) === TRUE) {
			echo '<a href="'. hasAccessForUrl('image-orders.php', false).'" class="btn btn-success">Klik hier om terug te gaan</a>';
			//echo $sql;
		} else {
			echo "Error updating record: " . $conn->error;
		}

		$conn->close();

	} else {
		echo "nothing here :)";
	}

?>

</div>

<?php
include('footer.php');
?>
