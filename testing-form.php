<?php

$title = 'Testing Form';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="container body">

	<?php
	if (isset($_POST['secure_boot'])) {

		$software = "";
		$software_comment = "";
		$imageid = mysqli_real_escape_string($conn, $_POST['imageid']);
		$jaar = mysqli_real_escape_string($conn, $_POST['jaar']);
		$authentication = mysqli_real_escape_string($conn, $_POST['authentication']);
		$authentication_comment = mysqli_real_escape_string($conn, $_POST['authentication_comment']);
		$secure_boot = mysqli_real_escape_string($conn, $_POST['secure_boot']);
		$secure_boot_comment = mysqli_real_escape_string($conn, $_POST['secure_boot_comment']);
		$uefi = mysqli_real_escape_string($conn, $_POST['uefi']);
		$uefi_comment = mysqli_real_escape_string($conn, $_POST['uefi_comment']);
		$activated = mysqli_real_escape_string($conn, $_POST['activated']);
		$activated_comment = mysqli_real_escape_string($conn, $_POST['activated_comment']);
		$updates = mysqli_real_escape_string($conn, $_POST['updates']);
		$updates_comment = mysqli_real_escape_string($conn, $_POST['updates_comment']);
		$timezone = mysqli_real_escape_string($conn, $_POST['timezone']);
		$timezone_comment = mysqli_real_escape_string($conn, $_POST['timezone_comment']);
		$computername = mysqli_real_escape_string($conn, $_POST['computername']);
		$computername_comment = mysqli_real_escape_string($conn, $_POST['computername_comment']);
		$msphotos = mysqli_real_escape_string($conn, $_POST['msphotos']);
		$msphotos_comment = mysqli_real_escape_string($conn, $_POST['msphotos_comment']);
		$msstore = mysqli_real_escape_string($conn, $_POST['msstore']);
		$msstore_comment = mysqli_real_escape_string($conn, $_POST['msstore_comment']);
		$settings = mysqli_real_escape_string($conn, $_POST['settings']);
		$settings_comment = mysqli_real_escape_string($conn, $_POST['settings_comment']);
		$software_opened = mysqli_real_escape_string($conn, $_POST['software_opened']);
		$software_opened_comment = mysqli_real_escape_string($conn, $_POST['software_opened_comment']);
		$default_software = mysqli_real_escape_string($conn, $_POST['default_software']);
		$default_software_comment = mysqli_real_escape_string($conn, $_POST['default_software_comment']);
		$onedrive = mysqli_real_escape_string($conn, $_POST['onedrive']);
		$onedrive_comment = mysqli_real_escape_string($conn, $_POST['onedrive_comment']);
		$drivers = mysqli_real_escape_string($conn, $_POST['drivers']);
		$drivers_comment = mysqli_real_escape_string($conn, $_POST['drivers_comment']);
		$hotkeys = mysqli_real_escape_string($conn, $_POST['hotkeys']);
		$hotkeys_comment = mysqli_real_escape_string($conn, $_POST['hotkeys_comment']);
		$youtube = mysqli_real_escape_string($conn, $_POST['youtube']);
		$youtube_comment = mysqli_real_escape_string($conn, $_POST['youtube_comment']);
		$audiojack = mysqli_real_escape_string($conn, $_POST['audiojack']);
		$audiojack_comment = mysqli_real_escape_string($conn, $_POST['audiojack_comment']);
		$closelid = mysqli_real_escape_string($conn, $_POST['closelid']);
		$closelid_comment = mysqli_real_escape_string($conn, $_POST['closelid_comment']);
		$standby = mysqli_real_escape_string($conn, $_POST['standby']);
		$standby_comment = mysqli_real_escape_string($conn, $_POST['standby_comment']);
		$shutdown = mysqli_real_escape_string($conn, $_POST['shutdown']);
		$shutdown_comment = mysqli_real_escape_string($conn, $_POST['shutdown_comment']);
		$usbports = mysqli_real_escape_string($conn, $_POST['usbports']);
		$usbports_comment = mysqli_real_escape_string($conn, $_POST['usbports_comment']);
		$touchpad = mysqli_real_escape_string($conn, $_POST['touchpad']);
		$touchpad_comment = mysqli_real_escape_string($conn, $_POST['touchpad_comment']);
		$charging = mysqli_real_escape_string($conn, $_POST['charging']);
		$charging_comment = mysqli_real_escape_string($conn, $_POST['charging_comment']);
		$hdmi = mysqli_real_escape_string($conn, $_POST['hdmi']);
		$hdmi_comment = mysqli_real_escape_string($conn, $_POST['hdmi_comment']);
		$wifi = mysqli_real_escape_string($conn, $_POST['wifi']);
		$wifi_comment = mysqli_real_escape_string($conn, $_POST['wifi_comment']);
		$schoolwifi = mysqli_real_escape_string($conn, $_POST['schoolwifi']);
		$schoolwifi_comment = mysqli_real_escape_string($conn, $_POST['schoolwifi_comment']);
		$comments = mysqli_real_escape_string($conn, $_POST['comments']);

		for ($x = 1; $x <= 150; $x++) {
			if (isset($_POST['software'.$x]) == true) {
				${'software' . $x} = mysqli_real_escape_string($conn, $_POST['software'.$x]);
				if ($software == "") {
					$software = ${'software' . $x};
				} else {
					$software = $software . ";" . ${'software' . $x};
				}
			}

			if (isset($_POST['software' . $x . '_comment']) == true) {
				if ($_POST['software' . $x . '_comment'] !== "") {
					${'software' . $x . '_comment'} = mysqli_real_escape_string($conn, $_POST['software' . $x . '_comment']);
					$software = $software . " - " . ${'software' . $x . '_comment'};
				}
			}
		}

		$sql = "INSERT INTO testlab (imageid, authentication, authentication_comment, secure_boot, secure_boot_comment, uefi, uefi_comment, activated, activated_comment, updates, updates_comment, timezone, timezone_comment, computername, computername_comment, msphotos, msphotos_comment, msstore, msstore_comment, settings, settings_comment, software, software_comment, software_opened, software_opened_comment, default_software, default_software_comment, onedrive, onedrive_comment, drivers, drivers_comment, hotkeys, hotkeys_comment, youtube, youtube_comment, audiojack, audiojack_comment, closelid, closelid_comment, standby, standby_comment, shutdown, shutdown_comment, usbports, usbports_comment, touchpad, touchpad_comment, charging, charging_comment, hdmi, hdmi_comment, wifi, wifi_comment, schoolwifi, schoolwifi_comment, comments, tested_by) VALUES ('" . $imageid . "', '" . $authentication . "', '" . $authentication_comment . "', '" . $secure_boot . "', '" . $secure_boot_comment . "', '" . $uefi . "', '" . $uefi_comment . "', '" . $activated . "', '" . $activated_comment . "', '" . $updates . "', '" . $updates_comment . "', '" . $timezone . "', '" . $timezone_comment . "', '" . $computername . "', '" . $computername_comment . "', '" . $msphotos . "', '" . $msphotos_comment . "', '" . $msstore . "', '" . $msstore_comment . "', '" . $settings . "', '" . $settings_comment . "', '" . $software . "', '" . $software_comment . "', '" . $software_opened . "', '" . $software_opened_comment . "', '" . $default_software . "', '" . $default_software_comment . "', '" . $onedrive . "', '" . $onedrive_comment . "', '" . $drivers . "', '" . $drivers_comment . "', '" . $hotkeys . "', '" . $hotkeys_comment . "', '" . $youtube . "', '" . $youtube_comment . "', '" . $audiojack . "', '" . $audiojack_comment . "', '" . $closelid . "', '" . $closelid_comment . "', '" . $standby . "', '" . $standby_comment . "', '" . $shutdown . "', '" . $shutdown_comment . "', '" . $usbports . "', '" . $usbports_comment . "', '" . $touchpad . "', '" . $touchpad_comment . "', '" . $charging . "', '" . $charging_comment . "', '" . $hdmi . "', '" . $hdmi_comment . "', '" . $wifi . "', '" . $wifi_comment . "', '" . $schoolwifi . "', '" . $schoolwifi_comment . "', '" . $comments . "', '" . $loginname . "')";

		if ($conn->query($sql) === TRUE) {
			echo "Test formulier in opgeslagen in de database<br>";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$softwareok = "";

		if (strpos($software, 'Nee_') !== false) {
			$softwareok = "fout";
		}

		if ($authentication == "Nee" || $secure_boot == "Nee" || $uefi == "Nee" || $activated == "Nee" || $updates == "Nee" || $timezone == "Nee" || $computername == "Nee" || $msphotos == "Nee" || $msstore == "Nee" || $settings == "Nee" || $software_opened == "Nee" || $default_software == "Nee" || $onedrive == "Nee" || $drivers == "Nee" || $hotkeys == "Nee" || $youtube == "Nee" || $audiojack == "Nee" || $closelid == "Nee" || $standby == "Nee" || $shutdown == "Nee" || $usbports == "Nee" || $touchpad == "Nee" || $charging == "Nee" || $hdmi == "Nee" || $wifi == "Nee" || $schoolwifi == "Nee" || $softwareok == "fout") {

			if ($jaar == "2019") {
				$sql = "UPDATE images2019 SET status2020 = 'fouten' WHERE id = '" . $imageid . "'";
			} elseif ($jaar == "2020") {
				$sql = "UPDATE images2020 SET status = 'fouten' WHERE id = '" . $imageid . "'";
			}

			if ($conn->query($sql) === TRUE) {
				echo "Fouten gevonden in het testformulier, aangepast naar 'Fouten na testing'<br>";
			} else {
				echo "Error updating record: " . $conn->error;
			}

		} else {

			if ($jaar == "2019") {
				$sql = "UPDATE images2019 SET status2020 = 'testingok' WHERE id = '" . $imageid . "'";
			} elseif ($jaar == "2020") {
				$sql = "UPDATE images2020 SET status = 'testingok' WHERE id = '" . $imageid . "'";
			}

			if ($conn->query($sql) === TRUE) {
				echo "Geen fouten gevonden in het test formulier, aangepast naar 'Testing OK'<br><br>";
			} else {
				echo "Error updating record: " . $conn->error;
			}

		}

		echo '<a href="'. hasAccessForUrl('testing.php', false).'" class="btn btn-primary">Terug naar testlab overzicht</a>';

		$conn->close();

	} elseif(isset($_GET['imageid'])) { // end if post ?>

		<h3>Kwaliteitscontrole van image</h3><br>

		<?php

		$sql = "SELECT * FROM `byod-orders`.images" . $_GET['jaar'] . " where id = '" . $_GET['imageid'] . "'";
		$result = $conn->query($sql);
		$authentication = "";
		$customsoftware = array('.NET Framework 3.5');

		if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {
				echo "<strong>Image ID:</strong> <a href='image.php?id=" . $_GET["imageid"] . "&jaar=" . $_GET["jaar"] . "&edit=true'>" . $_GET["imageid"] . "</a><br>";
				echo "<strong>Naam:</strong> " . $row["name"] . "<br>";
				echo "<strong>Type:</strong> " . $row["type"] . "<br>";
				$computernaam = $row["computername"];
				echo "<strong>asignee:</strong> " . $row["asignee"] . "<br>";
				echo "<strong>date:</strong> " . $row["date"] . "<br>";
				echo "<strong>Extra uitleg:</strong><br>" . $row["internalnotes"] . "<br>";

				$authentication = $row["authentication"];

				if ($row['type'] == '1') {

					# code...

				} elseif ($row['type'] == '2') {

					array_push($customsoftware, 'Office 365','Google Chrome','Mozilla Firefox','Acrobat Reader','Adobe Flash','Microsoft Silverlight','Microsoft Visual C++ Redist packages','Java RE x86 & x64');

				} elseif ($row['type'] == '3') {

					array_push($customsoftware, 'Office 365','Google Chrome','Mozilla Firefox','Acrobat Reader','Adobe Flash','Microsoft Silverlight','Microsoft Visual C++ Redist packages','Java RE x86 & x64', '7-Zip', 'Geogebra Classic', 'VideoLAN VLC Player', 'pdf Split and Merge', 'Microsoft Teams', 'Teamviewer', 'Safe Exam Browser');

				}

				if (isset($row["free_software"])) {
					$free_software = explode(";", $row["free_software"]);
					foreach ($free_software as $key) {
						array_push($customsoftware, $key);
					}
				}

				if (isset($row["paid_software"])) {
					$paid_software = explode(",", $row["paid_software"]);
					foreach ($paid_software as $key) {
						array_push($customsoftware, $key);
					}
				}

			}
		} else {
			echo "0 results";
		}
		$conn->close();

		?>

		<br>
		<hr>
		<br>

		<form action="testing-form.php" method="post">
		<input type="text" class="form-control" name="imageid" value="<?php echo $_GET['imageid']; ?>" hidden>
		<input type="text" class="form-control" name="jaar" value="<?php echo $_GET['jaar']; ?>" hidden>
		<table class="table table-striped testform">
			<tr class="table-dark">
				<th>Vraag</th>
				<th width="60px">Ja</th>
				<th width="60px">Nee</th>
				<th width="60px">N.V.T.</th>
				<th>Opmerking</th>
			</tr>
			<tr class="table-secondary">
				<th>Toestel</th>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Staat Secure Boot aan in BIOS?</td>
				<td><input type="radio" class="form-control" name="secure_boot" value="ja"></td>
				<td><input type="radio" class="form-control" name="secure_boot" value="nee"></td>
				<td><input type="radio" class="form-control" name="secure_boot" value="nvt"></td>
				<td><input type="text" class="form-control" name="secure_boot_comment"></td>
			</tr>
			<tr>
				<td>Staat UEFI aan in BIOS?</td>
				<td><input type="radio" class="form-control" name="uefi" value="ja"></td>
				<td><input type="radio" class="form-control" name="uefi" value="nee"></td>
				<td><input type="radio" class="form-control" name="uefi" value="nvt"></td>
				<td><input type="text" class="form-control" name="uefi_comment"></td>
			</tr>

			<!-- -------------------------------------------------------------------------------- -->

			<tr class="table-secondary">
				<th>Software</th>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Werkt de aanmelding via <?php echo $authentication; ?> correct?</td>
				<td><input type="radio" class="form-control" name="authentication" value="ja"></td>
				<td><input type="radio" class="form-control" name="authentication" value="nee"></td>
				<td><input type="radio" class="form-control" name="authentication" value="nvt"></td>
				<td><input type="text" class="form-control" name="authentication_comment"></td>
			</tr>
			<!-- <tr>
				<td>Is de laptop correct toegevoegd aan het domein/intune?</td>
				<td><input type="radio" class="form-control" name="gender" value="male"></td>
				<td><input type="radio" class="form-control" name="gender" value="male"></td>
				<td><input type="radio" class="form-control" name="gender" value="male"></td>
				<td><input type="text" class="form-control" name="gender"></td>
			</tr> -->
			<tr>
				<td>Is Windows geactiveerd?</td>
				<td><input type="radio" class="form-control" name="activated" value="ja"></td>
				<td><input type="radio" class="form-control" name="activated" value="nee"></td>
				<td><input type="radio" class="form-control" name="activated" value="nvt"></td>
				<td><input type="text" class="form-control" name="activated_comment"></td>
			</tr>
			<tr>
				<td>Zijn alle Windows updates geïnstalleerd?</td>
				<td><input type="radio" class="form-control" name="updates" value="ja"></td>
				<td><input type="radio" class="form-control" name="updates" value="nee"></td>
				<td><input type="radio" class="form-control" name="updates" value="nvt"></td>
				<td><input type="text" class="form-control" name="updates_comment"></td>
			</tr>
			<tr>
				<td>Staat datum en tijd correct ingesteld?</td>
				<td><input type="radio" class="form-control" name="timezone" value="ja"></td>
				<td><input type="radio" class="form-control" name="timezone" value="nee"></td>
				<td><input type="radio" class="form-control" name="timezone" value="nvt"></td>
				<td><input type="text" class="form-control" name="timezone_comment"></td>
			</tr>

			<tr>
				<td>Is de computernaam aangepast naar <strong><?php echo $computernaam; ?>20-001</strong>?</td>
				<td><input type="radio" class="form-control" name="computername" value="ja"></td>
				<td><input type="radio" class="form-control" name="computername" value="nee"></td>
				<td><input type="radio" class="form-control" name="computername" value="nvt"></td>
				<td><input type="text" class="form-control" name="computername_comment"></td>
			</tr>

			<!-- -------------------------------------------------------------------------------- -->

			<tr class="table-secondary">
				<th>Windows Apps</th>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Microsoft Photo's</td>
				<td><input type="radio" class="form-control" name="msphotos" value="ja"></td>
				<td><input type="radio" class="form-control" name="msphotos" value="nee"></td>
				<td><input type="radio" class="form-control" name="msphotos" value="nvt"></td>
				<td><input type="text" class="form-control" name="msphotos_comment"></td>
			</tr>
			<tr>
				<td>Microsoft Store</td>
				<td><input type="radio" class="form-control" name="msstore" value="ja"></td>
				<td><input type="radio" class="form-control" name="msstore" value="nee"></td>
				<td><input type="radio" class="form-control" name="msstore" value="nvt"></td>
				<td><input type="text" class="form-control" name="msstore_comment"></td>
			</tr>
			<tr>
				<td>Instellingen-venster</td>
				<td><input type="radio" class="form-control" name="settings" value="ja"></td>
				<td><input type="radio" class="form-control" name="settings" value="nee"></td>
				<td><input type="radio" class="form-control" name="settings" value="nvt"></td>
				<td><input type="text" class="form-control" name="settings_comment"></td>
			</tr>

			<!-- -------------------------------------------------------------------------------- -->

			<tr class="table-secondary">
				<th>Custom Software</th>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>

			<?php
			$i = 0;
			foreach ($customsoftware as $key) {
				$i++;
				$key = str_replace(";", " - ", $key);
				echo '
					<tr>
						<td>' . $key . '</td>
						<td><input type="radio" class="form-control" name="software' . $i . '" value="Ja_' . $key . '"></td>
						<td><input type="radio" class="form-control" name="software' . $i . '" value="Nee_' . $key . '"></td>
						<td><input type="radio" class="form-control" name="software' . $i . '" value="NVT_' . $key . '"></td>
						<td><input type="text" class="form-control" name="software' . $i . '_comment"></td>
					</tr>
				';
			}
			?>


			<tr>
				<td>Is alle geïnstalleerde software 1x geopend?</td>
				<td><input type="radio" class="form-control" name="software_opened" value="ja"></td>
				<td><input type="radio" class="form-control" name="software_opened" value="nee"></td>
				<td><input type="radio" class="form-control" name="software_opened" value="nvt"></td>
				<td><input type="text" class="form-control" name="software_opened_comment"></td>
			</tr>
			<tr>
				<td>Kunnen de standaard programma's ingesteld worden? <br><em style="font-size:12px;">(Wanneer het programma 'TWINUI' te zien is, markeer dit dan als fout in de image)</em></td>
				<td><input type="radio" class="form-control" name="default_software" value="ja"></td>
				<td><input type="radio" class="form-control" name="default_software" value="nee"></td>
				<td><input type="radio" class="form-control" name="default_software" value="nvt"></td>
				<td><input type="text" class="form-control" name="default_software_comment"></td>
			</tr>
			<tr>
				<td>Werkt het Automatisch opslaan in Office 365 in OneDrive?</em></td>
				<td><input type="radio" class="form-control" name="onedrive" value="ja"></td>
				<td><input type="radio" class="form-control" name="onedrive" value="nee"></td>
				<td><input type="radio" class="form-control" name="onedrive" value="nvt"></td>
				<td><input type="text" class="form-control" name="onedrive_comment"></td>
			</tr>

			<!-- -------------------------------------------------------------------------------- -->

			<tr class="table-secondary">
				<th>Drivers / Hardware</th>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Zijn alle drivers in orde bij Apparaatbeheer?</td>
				<td><input type="radio" class="form-control" name="drivers" value="ja"></td>
				<td><input type="radio" class="form-control" name="drivers" value="nee"></td>
				<td><input type="radio" class="form-control" name="drivers" value="nvt"></td>
				<td><input type="text" class="form-control" name="drivers_comment"></td>
			</tr>
			<tr>
				<td>Werken de hotkeys correct?</td>
				<td><input type="radio" class="form-control" name="hotkeys" value="ja"></td>
				<td><input type="radio" class="form-control" name="hotkeys" value="nee"></td>
				<td><input type="radio" class="form-control" name="hotkeys" value="nvt"></td>
				<td><input type="text" class="form-control" name="hotkeys_comment"></td>
			</tr>
			<tr>
				<td>Speelt het geluid en beeld synchroon af? (Youtube)</td>
				<td><input type="radio" class="form-control" name="youtube" value="ja"></td>
				<td><input type="radio" class="form-control" name="youtube" value="nee"></td>
				<td><input type="radio" class="form-control" name="youtube" value="nvt"></td>
				<td><input type="text" class="form-control" name="youtube_comment"></td>
			</tr>
			<tr>
				<td>Werkt de audio poort correct (met oortjes) af?</td>
				<td><input type="radio" class="form-control" name="audiojack" value="ja"></td>
				<td><input type="radio" class="form-control" name="audiojack" value="nee"></td>
				<td><input type="radio" class="form-control" name="audiojack" value="nvt"></td>
				<td><input type="text" class="form-control" name="audiojack_comment"></td>
			</tr>
			<tr>
				<td>Is het beeld terug in orde na het dicht- en openklappen van het scherm?</td>
				<td><input type="radio" class="form-control" name="closelid" value="ja"></td>
				<td><input type="radio" class="form-control" name="closelid" value="nee"></td>
				<td><input type="radio" class="form-control" name="closelid" value="nvt"></td>
				<td><input type="text" class="form-control" name="closelid_comment"></td>
			</tr>
			<tr>
				<td>Ontwaakt de laptop correct nadat het in stand-by is gegaan?</td>
				<td><input type="radio" class="form-control" name="standby" value="ja"></td>
				<td><input type="radio" class="form-control" name="standby" value="nee"></td>
				<td><input type="radio" class="form-control" name="standby" value="nvt"></td>
				<td><input type="text" class="form-control" name="standby_comment"></td>
			</tr>
			<tr>
				<td>Sluit het toestel correct af?</td>
				<td><input type="radio" class="form-control" name="shutdown" value="ja"></td>
				<td><input type="radio" class="form-control" name="shutdown" value="nee"></td>
				<td><input type="radio" class="form-control" name="shutdown" value="nvt"></td>
				<td><input type="text" class="form-control" name="shutdown_comment"></td>
			</tr>
			<tr>
				<td>Werken alle USB-poorten van de laptop correct?</td>
				<td><input type="radio" class="form-control" name="usbports" value="ja"></td>
				<td><input type="radio" class="form-control" name="usbports" value="nee"></td>
				<td><input type="radio" class="form-control" name="usbports" value="nvt"></td>
				<td><input type="text" class="form-control" name="usbports_comment"></td>
			</tr>
			<tr>
				<td>Werkt de touchpad van het toestel op een correcte manier?</td>
				<td><input type="radio" class="form-control" name="touchpad" value="ja"></td>
				<td><input type="radio" class="form-control" name="touchpad" value="nee"></td>
				<td><input type="radio" class="form-control" name="touchpad" value="nvt"></td>
				<td><input type="text" class="form-control" name="touchpad_comment"></td>
			</tr>
			<tr>
				<td>Laadt het toestel correct op met de lader aan volledige snelheid?</td>
				<td><input type="radio" class="form-control" name="charging" value="ja"></td>
				<td><input type="radio" class="form-control" name="charging" value="nee"></td>
				<td><input type="radio" class="form-control" name="charging" value="nvt"></td>
				<td><input type="text" class="form-control" name="charging_comment"></td>
			</tr>
			<tr>
				<td>Werkt de HDMI poort correct?</td>
				<td><input type="radio" class="form-control" name="hdmi" value="ja"></td>
				<td><input type="radio" class="form-control" name="hdmi" value="nee"></td>
				<td><input type="radio" class="form-control" name="hdmi" value="nvt"></td>
				<td><input type="text" class="form-control" name="hdmi_comment"></td>
			</tr>
			<tr>
				<td>Kan wifi correct verbinden met een netwerk?</td>
				<td><input type="radio" class="form-control" name="wifi" value="ja"></td>
				<td><input type="radio" class="form-control" name="wifi" value="nee"></td>
				<td><input type="radio" class="form-control" name="wifi" value="nvt"></td>
				<td><input type="text" class="form-control" name="wifi_comment"></td>
			</tr>
			<tr>
				<td>Connecteert Wifi met gevraagde netwerk van de school?</td>
				<td><input type="radio" class="form-control" name="schoolwifi" value="ja"></td>
				<td><input type="radio" class="form-control" name="schoolwifi" value="nee"></td>
				<td><input type="radio" class="form-control" name="schoolwifi" value="nvt"></td>
				<td><input type="text" class="form-control" name="schoolwifi_comment"></td>
			</tr>
			<tr>
				<td colspan="5">
					<p>Verdere opmerkingen</p>
					<textarea name="comments" class="form-control" id="comments" cols="90" rows="10"></textarea>
				</td>
			</tr>

		</table>

		<input type="submit" value="Indienen" class="btn btn-primary">
		</form>
		<br><br><br><br><br><br>

	<?php
		} elseif($_GET['testingid']) {
			$sql = "SELECT * FROM testlab WHERE id =" . $_GET['testingid'];
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				echo '<table class="table table-striped testform">';
					while($row = $result->fetch_assoc()) {
						echo "<tr><td>Test nummer: </td><td>" . $row["id"] . "</td></tr>";
						echo '<tr><td>Image: </td><td>' . $row["imageid"] . '</td></tr>';
						echo "<tr><td>Authentication: </td><td>" . $row["authentication"] . " - " . $row["authentication_comment"] . "</td></tr>";
						echo "<tr><td>Secure Boot: </td><td>" . $row["secure_boot"] . " - " . $row["secure_boot_comment"] . "</td></tr>";
						echo "<tr><td>UEFI in bios: </td><td>" . $row["uefi"] . " - " . $row["uefi_comment"] . "</td></tr>";
						echo "<tr><td>Activated: </td><td>" . $row["activated"] . " - " . $row["activated_comment"] . "</td></tr>";
						echo "<tr><td>updates: </td><td>" . $row["updates"] . " - " . $row["updates_comment"] . "</td></tr>";
						echo "<tr><td>timezone: </td><td>" . $row["timezone"] . " - " . $row["timezone_comment"] . "</td></tr>";
						echo "<tr><td>computername: </td><td>" . $row["computername"] . " - " . $row["computername_comment"] . "</td></tr>";
						echo "<tr><td>msphotos: </td><td>" . $row["msphotos"] . " - " . $row["msphotos_comment"] . "</td></tr>";
						echo "<tr><td>msstore: </td><td>" . $row["msstore"] . " - " . $row["msstore_comment"] . "</td></tr>";
						echo "<tr><td>settings: </td><td>" . $row["settings"] . " - " . $row["settings_comment"] . "</td></tr>";
						echo "<tr><td>software: </td><td>";

						$software = explode(";",$row["software"]);
						foreach($software as $softwaretitle){
							if(substr( $softwaretitle, 0, 3 ) === "Nee"){
								echo "<strong>" . $softwaretitle . "</strong><br>";
							} else {
								echo $softwaretitle . "<br>";
							}
						}

						echo "</td></tr>";
						echo "<tr><td>software_opened: </td><td>" . $row["software_opened"] . " - " . $row["software_opened_comment"] . "</td></tr>";
						echo "<tr><td>default_software: </td><td>" . $row["default_software"] . " - " . $row["default_software_comment"] . "</td></tr>";
						echo "<tr><td>onedrive: </td><td>" . $row["onedrive"] . " - " . $row["onedrive_comment"] . "</td></tr>";
						echo "<tr><td>drivers: </td><td>" . $row["drivers"] . " - " . $row["drivers_comment"] . "</td></tr>";
						echo "<tr><td>hotkeys: </td><td>" . $row["hotkeys"] . " - " . $row["hotkeys_comment"] . "</td></tr>";
						echo "<tr><td>youtube: </td><td>" . $row["youtube"] . " - " . $row["youtube_comment"] . "</td></tr>";
						echo "<tr><td>audiojack: </td><td>" . $row["audiojack"] . " - " . $row["audiojack_comment"] . "</td></tr>";
						echo "<tr><td>closelid: </td><td>" . $row["closelid"] . " - " . $row["closelid_comment"] . "</td></tr>";
						echo "<tr><td>standby: </td><td>" . $row["standby"] . " - " . $row["standby_comment"] . "</td></tr>";
						echo "<tr><td>shutdown: </td><td>" . $row["shutdown"] . " - " . $row["shutdown_comment"] . "</td></tr>";
						echo "<tr><td>usbports: </td><td>" . $row["usbports"] . " - " . $row["usbports_comment"] . "</td></tr>";
						echo "<tr><td>touchpad: </td><td>" . $row["touchpad"] . " - " . $row["touchpad_comment"] . "</td></tr>";
						echo "<tr><td>charging: </td><td>" . $row["charging"] . " - " . $row["charging_comment"] . "</td></tr>";
						echo "<tr><td>hdmi: </td><td>" . $row["hdmi"] . " - " . $row["hdmi_comment"] . "</td></tr>";
						echo "<tr><td>wifi: </td><td>" . $row["wifi"] . " - " . $row["wifi_comment"] . "</td></tr>";
						echo "<tr><td>schoolwifi: </td><td>" . $row["schoolwifi"] . " - " . $row["schoolwifi_comment"] . "</td></tr>";
						echo "<tr><td>comments: </td><td>" . $row["comments"] . "</td></tr>";
						echo "<tr><td>Getest door: </td><td>" . $row["tested_by"] . "</td></tr>";
						echo "<tr><td>Ingegeven op: </td><td>" . $row["created_at"] . "</td></tr>";
					}
				echo "</table>";
			} else {
				echo "0 results";
			}
		}
	?>

</div>

<?php
include('footer.php');
?>
