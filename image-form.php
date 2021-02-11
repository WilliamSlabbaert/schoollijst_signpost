<style>

* {
	box-sizing: border-box;
}

body {
	background-color: #f1f1f1;
}

#regForm {
	margin: 0 auto;
	padding: 40px;
	min-width: 300px;
}

h1 {
	text-align: center;
}

input {
	padding: 10px;
	width: 100%;
	font-size: 17px;
	border: 1px solid #aaaaaa;
}

/* Mark input boxes that gets an error on validation: */
input.invalid {
	background-color: #ffdddd;
}

/* Hide all steps by default: */
.tab {
	display: none;
}

button {
	background-color: #00ADBD;
	color: #ffffff;
	border: none;
	padding: 10px 20px;
	font-size: 17px;
	font-family: Raleway;
	cursor: pointer;
}

button:hover {
	opacity: 0.8;
}

#prevBtn {
	background-color: #bbbbbb;
}

/* Make circles that indicate the steps of the form: */
.step {
	height: 15px;
	width: 15px;
	margin: 0 2px;
	background-color: #bbbbbb;
	border: none;
	border-radius: 50%;
	display: inline-block;
	opacity: 0.5;
}

.step.active {
	opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
	background-color: #00ADBD;
}
</style>
<?php
require 'vendor/autoload.php';

$title = 'Image Form';
include('head.php');
include('nav.php');
include('conn.php');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ini_set('SMTP', 'localhost');
ini_set('smtp_port', 25);



	//check if query string
	if (isset($_POST['submit'])) {
		//print_r($_POST);

		$synergyid = mysqli_real_escape_string($conn, $_POST['synergy']);
		$guid = mysqli_real_escape_string($conn, $_POST['GUID']);
		$SPSKUstring = implode(";",$_POST['toestel']);
		$SPSKU = mysqli_real_escape_string($conn, $SPSKUstring);
		$name = mysqli_real_escape_string($conn, $_POST['imagenaam']);
		$type = mysqli_real_escape_string($conn, $_POST['imagekeuze']);
		$authentication = mysqli_real_escape_string($conn, $_POST['authenticatie']);
		$authentication_info_string = $_POST['lokaal'] . $_POST['vpn'] .$_POST['accountsvpn'];
		$authentication_info = mysqli_real_escape_string($conn, $authentication_info_string);
		// $free_software = print_r($_POST['customgratissoftware']);
		// $paid_software = print_r($_POST['customsoftware']);
		$emse3 = mysqli_real_escape_string($conn, $_POST['e3check']);
		// $intunescripting =
		$computername = mysqli_real_escape_string($conn, $_POST['hostname']);
		$notes = mysqli_real_escape_string($conn, $_POST['comment']);
		$contactname = mysqli_real_escape_string($conn, $_POST['contactnaam']);
		$contacttel = mysqli_real_escape_string($conn, $_POST['contacttel']);
		$contactemail = mysqli_real_escape_string($conn, $_POST['contactemail']);
		$deliverydate = mysqli_real_escape_string($conn, $_POST['deliverydate']);
		if (isset($customimageaanmaak) == true) {
			$type = $type . " - " . $_POST['customimageaanmaak'];
		}
		$type = mysqli_real_escape_string($conn, $type);

		$customgratissoftwarestring = implode(";",$_POST['customgratissoftware']);
		$customgratissoftware = mysqli_real_escape_string($conn, $customgratissoftwarestring);
		$customsoftwarestring = implode(";",$_POST['customsoftware']);
		$customsoftware = mysqli_real_escape_string($conn, $customsoftwarestring);

		$assessmentq = mysqli_real_escape_string($conn, $_POST['assessmentq']);
		$assessmentqplatform = mysqli_real_escape_string($conn, $_POST['assessmentqplatform']);
		$assessmentqlogin = mysqli_real_escape_string($conn, $_POST['assessmentqlogin']);


		$sql = "INSERT INTO images2020 (synergyid, guid, SPSKU, name, type, authentication, authentication_info, free_software, paid_software, emse3, computername, notes, contactname, contacttel, contactemail, deliverydate, assessmentq, assessmentqplatform, assessmentqlogin)
		VALUES ('" . $synergyid . "', '" . $guid . "', '" . $SPSKU . "', '" . $name . "', '" . $type . "', '" . $authentication . "', '" . $authentication_info . "', '" . $customgratissoftware . "', '" . $customsoftware . "', '" . $emse3 . "', '" . $computername . "', '" . $notes . "', '" . $contactname . "', '" . $contacttel . "', '" . $contactemail . "', '" . $deliverydate . "' , '" . $assessmentq . "', '" . $assessmentqplatform . "', '" . $assessmentqlogin . "')";

		if ($conn->query($sql) === TRUE) {
			echo 'Bedankt voor het invullen van uw image-informatie. <a href="'. hasAccessForUrl('image-replies.php?guid=' . $_POST['GUID'] . '', false).'">Klik hier om alles te bekijken en goed te keuren.</a>';

			$message="Beste

We hebben uw aanvraag voor de BYOD-intake van 2021 goed ontvangen.
Mogen we u vragen om de gegevens te controleren en te bevestigen via onderstaande link:

productie.signpost.site/image-replies.php?guid=" . $_POST['GUID'] ."

Alvast bedankt.

Het Signpost-team";

			$email = new \SendGrid\Mail\Mail();
			$email->setFrom("byod@signpost.eu", "Signpost BYOD");
			$email->setSubject("Bevestiging van uw image");
			$email->addTo($contactemail);
			// $email->addCc("nova@signpost.eu");
			$email->addContent(
				"text/plain", $message
			);
			$sendgrid = new \SendGrid('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');
			try {
				$response = $sendgrid->send($email);
			} catch (Exception $e) {
				echo 'Caught exception: '. $e->getMessage() ."\n";
			}

		} else {

			echo "Beste<br>
			Er is iets fout gelopen met de inzending van uw formulier.<br>
			Gelieve softwaresupport@signpost.eu te contacteren.<br>
			Wij zullen u dan verder helpen.";

			$sqlerror = "Error: " . $sql . "<br>" . $conn->error;
			$sqlerror = mysqli_real_escape_string($conn, $sqlerror);
			$ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
			$sql = "INSERT INTO errors (error, ip) VALUES ('" . $sqlerror . "', '" . $ip . "')";
			if ($conn->query($sql) === TRUE) {
				echo "";
			}

		}

		die();

	} elseif(!isset($_GET['q'])){

		echo "Beste<br>
		Er is iets fout gelopen met de inzending van uw formulier.<br>
		Gelieve softwaresupport@signpost.eu te contacteren.<br>
		Wij zullen u dan verder helpen.";

	} else {
		//check if geldig
		$sql = "SELECT * FROM schools where GUID = '".$_GET['q']."'";
		$result = $conn->query($sql);
		if ($result->num_rows == 0) {

			echo "Beste<br>
			Er is iets fout gelopen met de inzending van uw formulier.<br>
			Gelieve softwaresupport@signpost.eu te contacteren.<br>
			Wij zullen u dan verder helpen.";

		} else {  //--> geldig

			$sql2 = "SELECT * FROM schools where guid = '".$_GET['q']."'";
			$result2 = $conn->query($sql2);
			if ($result2->num_rows > 0) {
				while($q = $result2->fetch_assoc()){

					$school_name = $q['school_name'];
					$city = $q['city'];
					$synergyid = $q['synergyid'];
					$guid = $q['guid'];
					//print_r($q);
				}
			}

		?>

		<main role="main" class="container col-md-6">
			<form action="image-form.php" id="regForm" method="post">

			<h3>BYOD 2020 Intake-formulier voor <?php echo $school_name; ?></h3>
			<h6 class="mb-4"><strong>Bij vragen of twijfels kan u ons contacteren op het nummer <a href="03 500 49 28" class="blue">03 500 49 28</a>.</strong></h6>
			<hr>
			<input type="text" hidden name="synergy" value="<?php echo $synergyid; ?>">
			<input type="text" hidden name="GUID" value="<?php echo $guid; ?>">
			<input type="text" hidden name="schoolname" value="<?php echo $school_name; ?>">
			<input type="text" hidden name="plaats" value="<?php echo $city; ?>">

			<div class="tab">
				<!-- keuze image -->
				<div class="form-group mb-4" id="imagekeuze">
					<h5 for="">Soort image*</h5>
					<small class="form-text pb-1" >
						<p>Welke image moeten we voorzien op de toestellen? <br>
						Indien er wordt gekozen voor 'Wij (de school) maken onze eigen image' moet deze geüpload worden <strong>vóór 20/06/2021</strong>. Instructies volgen nog via een aparte mail. <br></p>
					</small>
					<div class="custom-control custom-radio" >
						<input type="radio" id="customRadio1" name="imagekeuze" value="geen" class="custom-control-input verify" required>
						<label class="custom-control-label" for="customRadio1" style="text-decoration:underline;text-decoration-color: #00ADBD;">Geen image - Originele image van leverancier (HP/Lenovo)</label>
					</div>
					<small class="form-text pb-1" id="geenimage">
						Deze image bevat Windows 10 samen met een aantal specifieke software van HP of Lenovo. <br><br>
					</small>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio2"  name="imagekeuze" value="school" class="custom-control-input">
						<label class="custom-control-label" for="customRadio2" style="text-decoration:underline;text-decoration-color: #00ADBD;">Wij (de school) maken onze eigen image </label>
						<br><br>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio3" name="imagekeuze" value="1" class="custom-control-input">
						<label class="custom-control-label" for="customRadio3" style="text-decoration:underline;text-decoration-color: #00ADBD;">ACSW-IP1</label>
					</div>
					<small class="form-text pb-1" id="bundel1">
						De Academic Software Image Pack 1 bevat de volgende software en features:
						<ul>
							<li>Windows 10 Education Pro - zonder bloatware (versie 1909)</li>
							<li>.NET Framework 3.5</li>

						</ul>
					</small>

					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio5" name="imagekeuze" value="3" class="custom-control-input">
						<label class="custom-control-label" for="customRadio5" style="text-decoration:underline;text-decoration-color: #00ADBD;">ACSW-IP3</label>
					</div>
					<small class="form-text pb-1" id="bundel3">
						De Academic Software Image Pack 3 bevat <strong>Academic Software Image Pack 1 + de volgende software en features:</strong>
						<ul>
							<li>7-Zip</li>
							<li>GeoGebra Classic</li>
							<li>VideoLAN VLC Player</li>
							<li>PDF Split and Merge</li>
							<li>Microsoft Teams</li>
							<li>TeamViewer</li>
							<li>Office 365</li>
							<li>Google Chrome</li>
							<li>Mozilla Firefox</li>
							<li>Acrobat Reader</li>
							<li>Adobe Flash</li>
							<li>Microsoft Silverlight</li>
							<li>Microsoft Visual C++ Redist packages</li>
							<li>Java RE x86 & x64</li>
							<li>Safe Exam Browser</li>

						</ul>
					</small>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio6" name="imagekeuze" value="custom" class="custom-control-input">
						<label class="custom-control-label" for="customRadio6" style="text-decoration:underline;text-decoration-color: #00ADBD;">Custom image</label>
						<small class="form-text pb-1" id="custom">
							Bovenstaande opties zijn niet voldoende, u wilt liever een volledig aangepaste image. <br>
							Deze image start vanuit de <strong>Academic Software Image Pack 3</strong>
						</small>
					</div>
				</div>
			</div>

			<div class="tab">

				<!-- custom image naam bij meerdere -->
				<div class="form-group mb-4" id="naamimage" > <!-- style="display:none" -->
					<h5 for="imagenaam" id="imagename">Naam van deze  image*</h5>
					<small class="form-text pb-1">Bijvoorbeeld: Leerkracht, Leerling, AV, IWEM, STEM, Kantoor, Nijverheid…</small>
					<input type="text" name="imagenaam" class="form-control verify" id="imagenaam"  placeholder="" required>
				</div>
				<br>

				<!-- school image info -->
				<div class="form-group mb-4" id="school"> <!-- style="display:none" -->
					<h5 for="contactemail">Hoe zullen jullie de image opmaken?*</h5></br>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio2" name="customimageaanmaak" value="Clonezilla" class="custom-control-input verify">
						<label class="custom-control-label" for="custom1Radio2">Clonezilla-image (Onze voorkeur) - </label> <a href="Clonezilla.pdf">Clonezilla-handleiding</a>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio1" name="customimageaanmaak" value="Demo" class="custom-control-input">
						<label class="custom-control-label" for="custom1Radio1">Image geconfigureerd op demotoestel</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio3" name="customimageaanmaak" value="WDS" class="custom-control-input">
						<label class="custom-control-label" for="custom1Radio3">WDS MDT</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio4" name="customimageaanmaak" value="imagedonderdag" class="custom-control-input">
						<label class="custom-control-label" for="custom1Radio4">Op één van de Signpost Image Donderdagen</label>
					</div>
				</div>

				<div class="form-group mb-4" id="gratissoftwarediv">
					<h5 for="exampleInputEmail1">Gratis software</h5>
					<small class="form-text pb-1">Klik hier in het zoekveld en zoek naar de software die u in de image wilt en druk op <em>ENTER</em>.</small>
					<select id="SoftwareSelect" multiple="multiple" class="form-control" name="customgratissoftware[]" style="width:100% !important;">
					<?php
						$sql = "SELECT * FROM software ORDER BY naam asc";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								echo "<option value='".$row['naam']."'>".$row['naam']."</option>";
							}
						}

						?>
					</select>
				</div>
				<br>

				<div class="form-group mb-4" id="customsoftwarediv">
					<h5 for="">Indien u de software in bovenstaande lijst niet kan terugvinden, kan u hieronder software toevoegen</h5>
					<small class="form-text pb-1" >
						<p>Graag per softwaretitel de naam, de downloadlink en de licentie-informatie vermelden.</p>
					</small>
					<div id="customsoftware"></div>
						<button type="button" class="btn btn-primary" id="addcustomsoftware" onclick="GenerateCustomSoftwareField()">Extra software toevoegen</button>
					</div>
				</div>
			</div>

			<div class="tab">
				<!-- auth info -->
				<div class="form-group mb-4" id="bundel"> <!-- style="display:none" -->
					<div class="form-group mb-4" id="authenticatie">
						<h5 for="">Authenticatie*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="custom2Radio1" name="authenticatie" value="lokaal" class="custom-control-input">
							<label class="custom-control-label" for="custom2Radio1">Lokale accounts, geen koppeling met Active Directory, Azure AD of Intune</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="custom2Radio2" name="authenticatie" value="DomainJoined" class="custom-control-input">
							<label class="custom-control-label" for="custom2Radio2">Domain joined, koppeling met Active Directory</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="custom2Radio3" name="authenticatie" value="intune" class="custom-control-input">
							<label class="custom-control-label" for="custom2Radio3">Signpost Intune + Signpost OOBE
							</label>
						</div>
					</div>

					<!-- local info -->
					<div class="form-group mb-4" id="lokaal"> <!-- style="display:none" -->
						<div class="form-group mb-4">
							<h5 for="contactnaam">Lokale accounts</h5>
							<!-- <a class="" data-toggle="collapse" role="button" type="button" href="#collapse2" aria-expanded="false">
							Meer info bij lokale accounts...
							</a> -->
							<small class="form-text pb-1" id="collapse2">
								<p>Standaard worden volgende accounts voorzien:</br>
									(onderstaande lijst is in CSV-formaat met de eerste regel als voorbeeld)
								</p>

								<table width="400px" class="table">
									<tr>
										<th>AccountName</th>
										<th>Password</th>
										<th>Group</th>
									</tr>
									<tr>
										<td>SchoolAdmin</td>
										<td>Not4UAll</td>
										<td>Administrator</td>
									</tr>
									<tr>
										<td>Leerling</td>
										<td>/</td>
										<td>User</td>
									</tr>
								</table>

								<p>Indien u deze accounts wenst te wijzigen of aan te vullen, kunt u dit hier in hetzelfde formaat opgeven.</p>
							</small>
							<textarea rows="3" name="lokaal" class="form-control verify" id="lokaaltext"  placeholder=""></textarea>
						</div>
					</div>

					<!-- domein info -->
					<div class="form-group mb-4" id="vpn"> <!-- style="display:none" -->
						<div class="form-group mb-4">
							<h5 for="contactnaam">VPN Connectie naar Domain Controller</h5>
							<!-- <a class="" data-toggle="collapse" role="button" type="button" href="#collapse3" aria-expanded="false">
							Meer info bij VPN Connectie naar Domain Controller...
							</a> -->
							<small class="form-text pb-1" id="collapse3">
								<strong>Gegevens die u kan ingeven op de firewall:</strong>
								<p>Phase 1:</br>
									IKEv2
									Remote Gateway: 109.135.16.180</br>
									Encryption: <strong>enkel</strong> AES (256 bits)</br>
									Hash: <strong>enkel</strong> SHA256</br>
									PFS / DH Key Group: DH Group 14 (2048 bits)</br>
									Lifetime 28800</br>

																	</p>
																	<p>Phase 2:</br>
									Remote Network Address: 10.1.6.0 / 24</br>
									Encryption: enkel AES (256 bits) </br>
									Hash: enkel SHA256</br>
									PFS / DH Key Group: 14</br>
									Lifetime: 27000
									<p>Phase 2:</br>
									Remote Network Address: 192.168.124.0 / 24</br>
									Encryption: seulement AES (256 bits) </br>
									Hash: seulement SHA256</br>
									PFS / DH Key Group: 14</br>
									Lifetime: 27000

									</p>
									</p>
									<p><strong>Gegevens die wij van u nodig hebben:</strong></br>
									Openbare IP-adres van uw firewall</br>
									Pre-shared key (PSK)</br>
									Lokaal adresbereik waar uw AD Server op staat</br>
									Naam van de AD Server
									IP-adres van de AD Server
									OU waar de toestellen moeten komen
									</p>
							</small>
							<textarea rows="3" name="vpn" class="form-control" id="vpntext"  placeholder="">Publiek IP van jullie firewall: </br>
									Pre-shared key (PSK): </br>
									Lokaal adresbereik waar uw AD Server op staat: </br>
									Naam van de AD Server: </br>
									IP-adres van de AD Server: </br>
									OU waar de toestellen moeten komen: </textarea>
						</div>

						<!-- domein 2 info -->
						<div class="form-group mb-4" id="accountsvpn" >
							<h5 for="accountsvpn" id="accountsvpn">Accounts voor Signpost</h5>
							<small class="form-text pb-1">Om alles goed te laten verlopen hebben we een domein Admin-account (indien mogelijk met de naam “Signpost”) nodig waarmee we laptops aan het domein kunnen toevoegen en een remote desktopverbinding mogen maken naar de Active Directory server.<br>
							Ook zouden we graag een testaccount hebben die gelijkgesteld is met een leerlingaccount (indien mogelijk met de naam “Signpost.Test” ). Zo kunnen we er zeker van zijn dat de aanmeldprocedure voor een leerling goed zal verlopen.<br>
							Gelieve deze accounts niet te verwijderen.</small>
							<textarea rows="3" name="accountsvpn" class="form-control verify" id="accountsvpn"  placeholder=""></textarea>
						</div>
					</div>

					<!-- intune info -->
					<div class="form-group mb-4" id="intune"> <!-- style="display:none" -->
						<div class="form-group mb-4">
							<h5 for="contactnaam">Intune-beheerdersaccount</h5>
							<!-- <a class="" data-toggle="collapse" role="button" type="button" href="#collapse3" aria-expanded="false">
							Meer info bij Intune beheerdersaccount...
							</a> -->
							<small class="form-text pb-1" id="collapse3">
							<p>Voor correcte configuratie van uw Intune-omgeving is het aangewezen dat u ons toevoegt als Delegated Admin op uw portaal. Dit kan via onderstaande link:</p>
								<strong><p>
									<a href="https://businessstore.microsoft.com/manage/partner-invitation?invType=IndirectResellerRelationship&partnerId=6fff47dc-5f67-41b5-b48b-9d4c6de48eab&msppId=4794019&DAP=true" class="btn btn-primary" target="_blank">Klik hier</a> </strong>
								</p>
								<p>Eens we toegang hebben, zullen wij een account “Signpost.Test” aanmaken. Gelieve deze niet te verwijderen.</p>
							</small>
							<!-- <textarea rows="3" name="intune" class="form-control verify" id="intunetext"  placeholder=""></textarea> -->
						</div>

						Ik beschik over een EMS E3-licentie.
						<div class="form-check">
							<input class="form-check-input" type="radio" name="e3check" id="exampleRadios1" value="1" style="width:200px">
							Ja
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="e3check" id="exampleRadios2" value="0" checked style="width:200px">
							Nee
						</div>
					</div>

					<div class="form-group mb-4" id="assessmentq">
						<h5 for="">Wenst u gebruik te maken van assessmentQ?*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio1" name="assessmentq" value="Ja" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio1">Ja</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio2" name="assessmentq" value="Nee" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio2">Nee</label>
						</div>

					</div>
					<div class="form-group mb-4" id="assessmentqplatform">
						<h5 for="">Heeft u reeds een platform voor assessmentQ?*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio3" name="assessmentqplatform" value="Ja" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio3">Ja</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio4" name="assessmentqplatform" value="Nee" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio4">Nee</label>
						</div>

					</div>
					<div class="form-group mb-4" id="assessmentqlogin">
						<h5 for="">Op welke manier loggen leerlingen/leerkrachten in?*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio5" name="assessmentqlogin" value="Office 365" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio5">Office 365</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio6" name="assessmentqlogin" value="Google" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio6">Google</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio7" name="assessmentqlogin" value="Smartschool" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio7">Smartschool</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio8" name="assessmentqlogin" value="Andere" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio8">Andere</label>
						</div>
					</div>
				</div>
			</div>

			<div class="tab">

					<div class="form-group mb-4">
						<h5 for="toestel">Voor toestel(en):*</h5>

						<select id="ToestelSelect" class="form-control" name="toestel[]" style="width:100% !important;" multiple="multiple">
					<?php
						$sql = "SELECT SUBSTRING_INDEX(`device1-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device1-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND forecasts.deleted != 1 UNION
								SELECT SUBSTRING_INDEX(`device2-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device2-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND forecasts.deleted != 1 UNION
								SELECT SUBSTRING_INDEX(`device3-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device3-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND forecasts.deleted != 1 UNION
								SELECT SUBSTRING_INDEX(`device4-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device4-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND forecasts.deleted != 1
						";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								if($row['SPSKU']!=''){
									echo "<option value='".$row['SPSKU']."'>".$row['model']." - " .$row['warranty']." jaar garantie - " .$row['SPSKU']. "</option>";
								}
							}
						}

						?>
					</select>
					</div>

					<div class="form-group mb-4">
						<h5 for="contactnaam">Label voor de toestellen</h5>
						<small class="form-text pb-1">Indien de toestellen een specifieke computernaam dienen te krijgen, kunt u hier de naamgeving of volledige lijst opgeven.<br>
						Bijvoorbeeld: SCH19-001, SCH19-002… (max. 5 tekens voor de '-'). Het standaardlabel wordt door ons aangevuld.</small>
						<input type="text" name="hostname" class="form-control verify" id="hostname"  placeholder="" value="Hetzelfde als het label" style="color:black" maxlength="5" required></textarea>
					</div>
					<div class="form-group mb-4">
						<h5 for="contactnaam">Gewenste leverdatum</h5>
						<small class="form-text pb-1"></small>
						<input type="date" name="deliverydate" class="form-control verify" id=""  required placeholder="" value="" style="color:black" ></textarea>
					</div>
					<br>

					<select class="form-control ContactSelect" name="contactid" style="width:100%;">
					<?php
					include('mssql-conn.php');


					$tsql= "select cicntp.ID, cicntp.cnt_email, cicntp.FullName, cicntp.cnt_f_tel, cicntp.cnt_f_mobile,  cicmpy.cmp_name, opportunities.code, ltrim(cicmpy.cmp_code) as klantnummer from OpportunityContacts
					inner join cicntp on cicntp.cnt_id=OpportunityContacts.contactid inner join opportunities on opportunities.id=OpportunityID inner join cicmpy on cicmpy.cmp_wwn=OpportunityContacts.AccountID where  RoleID in (select id from OpportunityRoles where Description like '%image%')
					and cicmpy.cmp_code like '% ".$synergyid."'";
					$getResults= sqlsrv_query($msconn, $tsql);

					if ($getResults == FALSE){
						die(FormatErrors(sqlsrv_errors()));
					}

					while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
						echo "<option value='" . $row['ID'] . "'>" .  $row['FullName']  ." - "  . $row['cnt_email']  ."</option>";
					}

					sqlsrv_free_stmt($getResults);
					?>
					</select>
					<br>
					<br>

					<div id="contactdiv">
					<div class="form-group mb-4">
						<h5 for="contactnaam">Contactpersoon image*</h5>
						<small class="form-text pb-1">Wie kunnen wij contacteren met technische vragen over het imagen van de toestellen? (ICT-coördinator)</small>
						<input type="text" name="contactnaam" required class="form-control verify" id="contactnaam"  placeholder="">
					</div>

					<div class="form-group mb-4">
						<h5 for="contacttel">Contactpersoon image: telefoonnummer*</h5>
						<small class="form-text pb-1">Op welk telefoonnummer is de technische contactpersoon bereikbaar?</small>
						<input type="text" name="contacttel" required class="form-control verify" id="contacttel"  placeholder="">
					</div>

					<div class="form-group mb-4">
						<h5 for="contactemail">Contactpersoon image: e-mailadres*</h5>
						<small class="form-text pb-1">Op welk e-mailadres is de technische contactpersoon bereikbaar?</small>
						<input type="email" name="contactemail" required class="form-control verify" id="contactemail"  placeholder="">
					</div>
					</div>

					<div class="form-group mb-4">
						<h5 for="comment">Opmerkingen</h5>
						<small class="form-text pb-1">Hier kunt u verdere details of vragen noteren. </small>
						<textarea rows="3" name="comment" class="form-control verify" id="comment"  placeholder=""></textarea>
					</div>

					<button name="submit" type="submit" class="btn btn-primary mb-5">Indienen</button>
				</div>

				<div style="overflow:auto;">
					<div style="float:right;">
						<button type="button" id="prevBtn" onclick="nextPrev(-1)">Vorige</button>
						<button type="button" id="nextBtn" onclick="nextPrev(1)">Volgende</button>
					</div>
				</div>
				<!-- Circles which indicates the steps of the form: -->
				<div style="text-align:center;margin-top:40px;">
					<span class="step"></span>
					<span class="step"></span>
					<span class="step"></span>
					<span class="step"></span>
				</div>
			</form>
		</main>
<?php } ?>

<script>
	(function() {

		$( '#imagekeuze input[type="radio"]' ).on( "change", function() {
			console.log(this.value);
			ResetFields();
			ResetRequired();
			this.value == 'school' ? $('#school').show() && $("#custom1Radio1").prop('required',true) : $('#school').hide()
			this.value == '1' || this.value == '2' || this.value == '3' ? $('#bundel').show() : $('#bundel').hide()
			this.value == 'custom' ? $('#gratissoftwarediv').show() && $('#customsoftwarediv').show() && $("#custom2Radio1").prop('required',true) && $('#bundel').show() : $('#gratissoftwarediv').hide() && $('#customsoftwarediv').hide();
			this.value == 'geen' ? $('#imagenaam').prop('value','Geen Image') : $('#imagenaam').prop('value','')
		});

		$( '#authenticatie input[type="radio"]' ).on( "change", function() {
			console.log(this.value);
			$('#vpntext').val("");
			$('#accountsvpn').val("");

			$('#lokaaltext').val("");
			$('#intunetext').val("");

			this.value == 'DomainJoined' ? $('#vpn').show() : $('#vpn').hide()
			this.value == 'intune' ? $('#intune').show() && $("#exampleRadios1").prop('required',true) : $('#intune').hide() && $("#exampleRadios1").prop('required',false)
			this.value == 'lokaal' ? $('#lokaal').show() : $('#lokaal').hide()

		});


		function ResetFields(){
			$('#hostname').val("");
			$('#labeling').val("");
			$('#comment').val("");
			$('#vpntext').val("");
			$('#accountsvpn').val("");

			$('#gratissoftwarediv').val("");
			$('#customsoftwarediv').val("");

			$('#lokaaltext').val("");
			$('#intunetext').val("");

			$('#bundel').hide()
			$('#intune').hide()
			$('#vpn').hide()
			$('#lokaal').hide()

			$('#custom1Radio1').prop('checked', false);
			$('#custom1Radio2').prop('checked', false);
			$('#custom1Radio3').prop('checked', false);

			$('#custom2Radio1').prop('checked', false);
			$('#custom2Radio2').prop('checked', false);
			$('#custom2Radio3').prop('checked', false);


			$("#SoftwareSelect").val('').change();

		}

		function ResetRequired(){
			$("#custom1Radio1").prop('required',false)
			$("#custom2Radio1").prop('required',false)
		}

	})();

</script>

<script>

var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
	// This function will display the specified tab of the form ...
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
	// ... and fix the Previous/Next buttons:
	if (n == 0) {
		document.getElementById("prevBtn").style.display = "none";
	} else {
		document.getElementById("prevBtn").style.display = "none";
	}
	if (n == (x.length - 1)) {
		document.getElementById("nextBtn").innerHTML = "Indienen";
		document.getElementById("nextBtn").style.display = "none";

	} else {
		document.getElementById("nextBtn").innerHTML = "Volgende";
	}
	// ... and run a function that displays the correct step indicator:
	fixStepIndicator(n)
}

function GenerateCustomSoftwareField() {

	var string = '<div class="row row-fluid"><div class="col"><input type="text" name="customsoftware[]" class="form-control" placeholder="Software Naam"></div><div class="col"><input type="text" name="customsoftware[]" class="form-control" placeholder="Download Link"></div><div class="col"><input type="text" name="customsoftware[]" class="form-control" placeholder="Licentie"></div></div><br>';
	$('#customsoftware').append(string);

	console.log(document.getElementById('customsoftware').innerHTML);

}

function nextPrev(n) {
	// This function will figure out which tab to display
	var x = document.getElementsByClassName("tab");
	// Exit the function if any field in the current tab is invalid:
	if (n == 1 && !validateForm()) return false;
	// Hide the current tab:
	x[currentTab].style.display = "none";
	// Increase or decrease the current tab by 1:
	if (document.getElementById('customRadio1').checked == true){
		currentTab = currentTab + 3;
	} else if (document.getElementById('customRadio2').checked == true && document.getElementById('imagenaam').value != ""){
		currentTab = currentTab + 2;
	} else {
		currentTab = currentTab + n;
	}

	// if you have reached the end of the form... :
	if (currentTab >= x.length) {
		//...the form gets submitted:
		document.getElementById("regForm").submit();
		return false;
	}

	// Otherwise, display the correct tab:
	showTab(currentTab);
	window.scrollTo(0, 0);
}

function validateForm() {
	// This function deals with validation of the form fields
	var x, y, i = false;
	var valid = "";

	if ($('#regform *:not(#nextBtn):not(#prevBtn)').filter(':input:visible').length == 0) {
		console.log('niets gevonden');
		valid = true;
	} else {

		$('#regform *:not(#nextBtn):not(#prevBtn):not(.notrequired)').filter(':input:visible').each(function() {

			if (this.type == "radio" || this.type == "text") {
				console.log(this);
			}

			// my validation here

			if (this.checked !== false && this.type == "radio") {
				valid = true;
				//this.className += " invalid";
				console.log("valid radio");
			}

			if ( this.value !== "" && this.type == "text") {
				valid = true;
				//this.className += " invalid";
				console.log("valid text");
			}

		});
	}

	console.log('test is '+valid);
	// If the valid status is true, mark the step as finished and valid:

	if (valid == "") {
		//valid = true;
	}

	if (valid == true) {
		document.getElementsByClassName("step")[currentTab].className += " finish";
	}
	return valid; // return the valid status
}

function fixStepIndicator(n) {
	// This function removes the "active" class of all steps...
	var i, x = document.getElementsByClassName("step");
	for (i = 0; i < x.length; i++) {
		x[i].className = x[i].className.replace(" active", "");
	}
	//... and adds the "active" class to the current step:
	x[n].className += " active";
}

$('#SoftwareSelect').select2({
	placeholder: "Selecteer software"
});

$('#ToestelSelect').select2({
	placeholder: "Selecteer Toestellen"
});

$('.ContactSelect').select2({
	theme: "classic",
	placeholder: "Selecteer contactpersoon"
});

$('.ContactSelect').on('change', function(){
	console.log(this)
	var contactid = $(this).val();
	console.log(contactid)
	if(contactid){

		$.ajax({
			type:'POST',
			url:'ajaxContactData.php',
			data:'contactid='+contactid,
			success:function(html){
				$('#contactdiv').html(html);
			}
		});

	}else{
		$('#image').html('x');
	}
});

</script>

<?php
}
?>
