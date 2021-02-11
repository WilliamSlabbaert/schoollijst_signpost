<?php
if(isset($_POST['submit'])){

	include(dirname(__FILE__) . '/../conn.php');
	include(dirname(__FILE__) . '/showFieldServiceTicketDetails.php');
	require dirname(__FILE__) . '/../vendor/autoload.php';
	ini_set('SMTP', 'localhost');
	ini_set('smtp_port', 25);

	$serial = mysqli_real_escape_string($conn, $_POST['serial']);
	$type = mysqli_real_escape_string($conn, $_POST['type']);
	$problemDesc = mysqli_real_escape_string($conn, $_POST['problemDesc']);
	$firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$phone = mysqli_real_escape_string($conn, $_POST['phone']);
	$country = mysqli_real_escape_string($conn, $_POST['country']);
	$zip = mysqli_real_escape_string($conn, $_POST['zip']);
	$city = mysqli_real_escape_string($conn, $_POST['city']);
	$street = mysqli_real_escape_string($conn, $_POST['street']);
	$street2 = mysqli_real_escape_string($conn, $_POST['street2']);

	// file upload
	// [bfupload] => Array ( [0] => MicrosoftTeams-image.png )

	$sql = "INSERT INTO fieldServiceCases (
				serial,
				type,
				description,
				firstname,
				lastname,
				email,
				phone,
				country,
				zip,
				city,
				street,
				number)
		VALUES (
				'" . $serial . "',
				'" . $type . "',
				'" . $problemDesc . "',
				'" . $firstname . "',
				'" . $name . "',
				'" . $email . "',
				'" . $phone . "',
				'" . $country . "',
				'" . $zip . "',
				'" . $city . "',
				'" . $street . "',
				'" . $street2 . "'
				)";

	if ($conn->query($sql) === TRUE) {

		$caseId = mysqli_insert_id($conn);

		$caseDetails = showFieldServiceTicketDetails($conn, $caseId);

		echo '<p>Bedankt voor het aanmelden van uw probleem.<br>
			Uw ticket is opgeslagen in het systeem met nummer <b>#FS-' . $caseId . '</b>.<br>
			U zal zodra ook via e-mail een bevestiging krijgen van uw aanmelding.</p>';
		echo $caseDetails;

		$message = '<p>Bedankt voor het aanmelden van uw probleem.<br>
			Uw ticket is opgeslagen in het systeem met nummer <b>#FS-' . $caseId . '</b>.<br>
			Hieronder kan u de details van uw aanmelding bekijken:</p>';
		$message .= $caseDetails;

		// Send email
		$mail = new \SendGrid\Mail\Mail();
		$mail->setFrom("byod@signpost.eu", "Signpost BYOD");
		$subject = 'Aanmelding #FS-' . $caseId;
		$mail->setSubject($subject);
		$mail->addTo($email);
		$mail->addContent(
			"text/html", $message
		);
		$sendgrid = new \SendGrid('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');
		try {
			$response = $sendgrid->send($mail);
		} catch (Exception $e) {
			echo 'Caught exception: '. $e->getMessage() ."\n";
		}

	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();

	die();
}
?>

<h1>Nieuw defect aanmelden</h1><br>

<form class="" name="createFieldServiceTicket" action="" method="POST" >
	<div class="form-group card">
		<div class="card-header">
			<h4 class="my-0 font-weight-normal">Toestel</h4>
		</div>

		<div class="card-body">
			<label for="serial">Serienummer</label>
			<input id="serial" name="serial" type="text" class="form-control"><br>

			<!-- Deze card zou dynamisch opgehaald moeten worden -->
			<div class="card flex-md-row mb-4 box-shadow h-md-250">
				<div class="card-body d-flex flex-column align-items-start">
					<h3 class="mb-0">
						<a class="text-dark" href="#">HP Chromebook x360 11 G3</a>
					</h3>
					<div class="mb-1 text-muted">15T03ES#UUG</div>
						<p class="card-text mb-auto">
							11.6" HD BV UWVA 220 touch screen<br>
							Glass 3, 220 nits, 45% NTSC (1366 x 768);<br>
							Intel® Celeron® N4120 (1.1 GHz 4 MB cache, 4 cores)<br>
							Intel® UHD Graphics 600<br>
							4 GB LPDDR4-2400 SDRAM soldered down Memory<br>
							32 GB eMMC 5.0<br>
						</p>
					</div>
					<img class="card-img-right flex-auto d-none d-md-block" alt="Thumbnail [200x250]" style="height: 250px;" src="https://personeel.doko-signpost.eu/media/catalog/product/cache/d17a0e9c22c7f115450e34046f21348b/1/5/15t03es_uug2_2.jpg" data-holder-rendered="true">
				</div>
				<label class="col-form-label">Type probleem</label>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_4" type="radio" class="custom-control-input" value="idk">
						<label for="type_4" class="custom-control-label">Geen idee</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
					<input name="type" id="type_0" type="radio" class="custom-control-input" value="water">
					<label for="type_0" class="custom-control-label">Waterschade</label>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_1" type="radio" class="custom-control-input" value="panel">
						<label for="type_1" class="custom-control-label">Schermbreuk</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_2" type="radio" class="custom-control-input" value="bsod">
						<label for="type_2" class="custom-control-label">Blauw scherm met windows foutcode (BSOD)</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_3" type="radio" class="custom-control-input" value="charge">
						<label for="type_3" class="custom-control-label">Laad niet op</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_5" type="radio" class="custom-control-input" value="sound">
						<label for="type_5" class="custom-control-label">Geluid werkt niet</label>
					</div>
				</div>
			</div>
			<br>
			<label for="problemDesc">Beschrijving van probleem</label>
			<textarea id="problemDesc" name="problemDesc" cols="40" rows="5" class="form-control"></textarea>
		</div>
	</div>

	<div class="form-group card">
		<div class="card-header">
			<h4 class="my-0 font-weight-normal">Contact informatie</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="firstname">Voornaam</label>
					<input type="text" class="form-control" name="firstname" id="firstname" placeholder="" value="" required="">
					<div class="invalid-feedback">
						Valid first name is required.
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<label for="name">Achternaam</label>
					<input type="text" class="form-control" name="name" id="name" placeholder="" value="" required="">
					<div class="invalid-feedback">
						Valid last name is required.
					</div>
				</div>
			</div>
			<div class="mb-3">
				<label for="email">E-mail</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<div class="input-group-text">
							<i class="fa fa-at"></i>
						</div>
					</div>
					<input id="email" name="email" type="text" class="form-control">
				</div>
				<div class="invalid-feedback">
					Please enter a valid email address for shipping updates.
				</div>
			</div>
			<div class="mb-3">
				<label for="phone">Telefoon</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<div class="input-group-text">
							<i class="fa fa-phone"></i>
						</div>
					</div>
					<input id="phone" name="phone" type="text" class="form-control">
				</div>
				<div class="invalid-feedback">
					Please enter a valid phone address for shipping updates.
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 mb-3">
					<label for="country">Land</label>
					<select class="custom-select d-block w-100" name="country" id="country" required="">
						<option value="">Choose...</option>
						<option>België</option>
						<option>Nederland</option>
					</select>
					<div class="invalid-feedback">
						Please select a valid country.
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<label for="zip">Postcode</label>
					<input type="text" class="form-control" name="zip" id="zip" placeholder="" required="">
					<div class="invalid-feedback">
						Zip code required.
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<label for="city">Gemeente</label>
					<input type="text" class="form-control" name="city" id="city" placeholder="" required="">
					<div class="invalid-feedback">
						Gemeente is verplicht
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 mb-3">
					<label for="address">Straat</label>
					<input type="text" class="form-control" name="street" id="street" placeholder="" required="">
					<div class="invalid-feedback">
						Please enter your shipping address.
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<label for="address">Nummer</label>
					<input type="text" class="form-control" name="street2" id="street2" placeholder="" required>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group card">
		<div class="card-header">
			<h4 class="my-0 font-weight-normal">Bijlage</h4>
		</div>
		<div class="card-body">
			<label for="text">Hier kan u extra foto's of informatie uploaden die kan helpen bij de probleembeschrijving.</label><br>
			<div class="file-loading">
				<input id="bfupload" name="bfupload[]" type="file" multiple>
			</div>
			<script>
				$(document).ready(function() {
					$("#bfupload").fileinput({
						showUpload: false,
						dropZoneEnabled: false,
						maxFileCount: 10,
						mainClass: "bfupload-group-lg"
					});
				});
			</script>
		</div>
	</div>

	<div class="form-group">
		<button name="submit" type="submit" class="btn btn-lg btn-block btn-primary">Doorsturen</button>
	</div>

</form>
