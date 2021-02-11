<?php

require 'vendor/autoload.php';

$title = 'Forecast Form';
include('head.php');
include('nav.php');
include('conn.php');

ini_set('SMTP', 'localhost');
ini_set('smtp_port', 25);

function getGUID(){
	if (function_exists('com_create_guid')){
		return com_create_guid();
	}else{
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12);
		return $uuid;
	}
}
?>

<div class="container body">

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {

	if ($_POST['device1-hoes'] == "bedrukt") {
		$sleve1 = $_POST['device1-sleve'] . " (" . $_POST['device1-hoes'] . ")";
	} else {
		$sleve1 = $_POST['device1-sleve'];
	}

	$sleve1 = mysqli_real_escape_string($conn, $sleve1);

	$sales = mysqli_real_escape_string($conn, $_POST['sales']);
	$synergyid = mysqli_real_escape_string($conn, $_POST['synergyid']);
	$salesorderid = mysqli_real_escape_string($conn, $_POST['salesorderid']);
	$school = mysqli_real_escape_string($conn, $_POST['school']);
	$label = mysqli_real_escape_string($conn, $_POST['label']);
	$byodyear = mysqli_real_escape_string($conn, $_POST['byodyear']);
	$description = mysqli_real_escape_string($conn, $_POST['description']);

	$shipping_type = mysqli_real_escape_string($conn, $_POST['delivery_type']);
	if($shipping_type == 'delivery_school'){
		$shipping_postcode = mysqli_real_escape_string($conn, $_POST['shipping_postcode']);
		$shipping_city = mysqli_real_escape_string($conn, $_POST['shipping_city']);
		$shipping_street = mysqli_real_escape_string($conn, $_POST['shipping_street']);
	} else if($shipping_type == 'delivery_other'){
		$shipping_postcode = mysqli_real_escape_string($conn, $_POST['delivery_shipping_postcode']);
		$shipping_city = mysqli_real_escape_string($conn, $_POST['delivery_shipping_city']);
		$shipping_street = mysqli_real_escape_string($conn, $_POST['delivery_shipping_street']);
	} else if($shipping_type == 'delivery_home'){
		$shipping_postcode = 'thuislevering';
		$shipping_city = 'thuislevering';
		$shipping_street = 'thuislevering';
	}

	$delivery_contact_name = mysqli_real_escape_string($conn, $_POST['delivery_contact_name']);
	$delivery_contact_email = mysqli_real_escape_string($conn, $_POST['delivery_contact_email']);
	$delivery_contact_tel = mysqli_real_escape_string($conn, $_POST['delivery_contact_tel']);
	$financial_contact_name = mysqli_real_escape_string($conn, $_POST['financial_contact_name']);
	$financial_contact_email = mysqli_real_escape_string($conn, $_POST['financial_contact_email']);
	$financial_contact_tel = mysqli_real_escape_string($conn, $_POST['financial_contact_tel']);

	$startdate = mysqli_real_escape_string($conn, $_POST['start-date']);

	$amount = mysqli_real_escape_string($conn, $_POST['amount']);
	$SPSKU1 = mysqli_real_escape_string($conn, $_POST['SPSKU1']);

	$device1price = mysqli_real_escape_string($conn, $_POST['device1-price']);
	$device1repaircost = mysqli_real_escape_string($conn, $_POST['device1-repaircost']);
	$device1finance = mysqli_real_escape_string($conn, $_POST['device1-finance']);
	$device1licenses = mysqli_real_escape_string($conn, $_POST['device1-licenses']);
	$device1doko = mysqli_real_escape_string($conn,$_POST['billing_DOKO']);
	$originalbox = mysqli_real_escape_string($conn,$_POST['original_box']);
	$device1consumer = mysqli_real_escape_string($conn, $_POST['device1-consumer']);
	$device1unobligated = mysqli_real_escape_string($conn, $_POST['device1-unobligated']);

	$refer = mysqli_real_escape_string($conn, $_POST['refer']);
	$refer_invoice = mysqli_real_escape_string($conn, $_POST['refer_invoice']);

	$notes = mysqli_real_escape_string($conn, $_POST['notes']);

	$sql = "INSERT INTO forecasts (sales,synergyid,salesorderid,school,label,description,campagne,
		shipping_postcode,shipping_city,shipping_street,
		device1,
		`device1-SPSKU`,`device1-price`,`device1-repaircost`,`device1-finance`,`device1-sleve`,`device1-licenses`,`device1-consumer`,`device1-unobligated`,
		startdate, remarks, shipping_type, delivery_contact_name, delivery_contact_email, delivery_contact_tel,
		financial_contact_name, financial_contact_email, financial_contact_tel, refer, refer_invoice, billing_DOKO,original_box)
		VALUES ('" . $sales . "','" . $synergyid . "','" . $salesorderid . "','" . $school . "','" . $label . "','" . $description . "','" . $byodyear . "',
			'" . $shipping_postcode . "','" . $shipping_city . "','" . $shipping_street . "',
			'" . $amount . "',
			'" . $SPSKU1 . "','" . $device1price . "','" . $device1repaircost . "','" . $device1finance . "','" . $sleve1 . "','" . $device1licenses . "','" . $device1consumer . "','" . $device1unobligated . "',
			'" . $startdate . "', '" . $notes . "', '" . $shipping_type . "', '" . $delivery_contact_name . "', '" . $delivery_contact_email . "', '" . $delivery_contact_tel . "',
			'" . $financial_contact_name . "', '" . $financial_contact_email . "', '" . $financial_contact_tel . "', '" . $refer . "', '" . $refer_invoice . "' , '" . $device1doko . "', '". $originalbox . "')";

	if ($conn->query($sql) === TRUE) {

		$forecastid = mysqli_insert_id($conn);

		$sql = "SELECT * FROM schools WHERE synergyid = '" . $_POST['synergyid'] . "'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
			}
		} else {

			$guid = getGUID();
			$sql2 = "INSERT INTO schools (guid, synergyid, school_name, street, city, postcode, contact, CampagneJaar) VALUES ('" . $guid . "', '" . $synergyid . "', '" . $school . "', '" . $shipping_street . "', '" . $shipping_city . "', '" . $shipping_postcode . "', '" . $sales . "', '" . $byodyear . "')";

			if ($conn->query($sql2) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: " . $sql2 . "<br>" . $conn->error;
			}

		}

		function getHTMLByID($id, $html) {
			$dom = new DOMDocument;
			libxml_use_internal_errors(true);
			$dom->loadHTML($html);
			$node = $dom->getElementById($id);
			if ($node) {
				return $dom->saveXML($node);
			}
			return FALSE;
		}

			$message = '<p style="color:red;">Mochten er fouten staan in deze forecast,
			dan kan je deze nog verwijderen en opnieuw maken via <a href="' . hasAccessForUrl('sales-forecasts.php', false) . '">het forecast overzicht</a>.<br></p>';
			$message .= "<h3>Details van forecast</h3><br>";

			$sql2 = "SELECT * FROM forecasts where id = '" . $forecastid . "' AND deleted != 1";
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) {

					$message .= "<table class=\"table table-sm table-striped\">";
					$message .= "<tr><td>Synergy ID</td><td>" . $row2['synergyid'] . "</td></tr>";
					$message .= "<tr><td>Verkoopkans</td><td>" . $row2['salesorderid'] . "</td></tr>";
					$message .= "<tr><td>Beschrijving</td><td>" . $row2['description'] . "</td></tr>";
					$message .= "<tr><td>Sales</td><td>" . $row2['sales'] . "</td></tr>";

					$message .= "<tr><td></td><td></td></tr>";
					$message .= "<tr><td>School</td><td>" . $row2['school'] . "</td></tr>";
					$message .= "<tr><td>Postcode</td><td>" . $row2['shipping_postcode'] . "</td></tr>";
					$message .= "<tr><td>Gemeente</td><td>" . $row2['shipping_city'] . "</td></tr>";
					$message .= "<tr><td>Straat</td><td>" . $row2['shipping_street'] . "</td></tr>";

					$message .= "<tr><td></td><td></td></tr>";
					$message .= "<tr><th>Toestel 1</th><th></th></tr>";
					$message .= "<tr><td>Aantal</td><td>" . $row2['device1'] . "</td></tr>";
					$message .= "<tr><td>SPSKU</td><td>" . $row2['device1-SPSKU'] . "</td></tr>";
					$message .= "<tr><td>Prijs</td><td>" . $row2['device1-price'] . "</td></tr>";
					$message .= "<tr><td>Herstelkost</td><td>" . $row2['device1-repaircost'] . "</td></tr>";
					$message .= "<tr><td>Financiering</td><td>" . $row2['device1-finance'] . "</td></tr>";
					$message .= "<tr><td>Hoes</td><td>" . $row2['device1-sleve'] . "</td></tr>";
					$message .= "<tr><td>Licenties</td><td>" . $row2['device1-licenses'] . "</td></tr>";
					$message .= "<tr><td>Consument</td><td>" . $row2['device1-consumer'] . "</td></tr>";
					$message .= "<tr><td>Vrijblijvend</td><td>" . $row2['device1-unobligated'] . "</td></tr>";

					$message .= "<tr><td></td><td></td></tr>";
					$message .= "<tr><td>Opmerkingen</td><td>" . $row2['remarks'] . "</td></tr>";
					$message .= "<tr><td>Datum ingevuld</td><td>" . $row2['date'] . "</td></tr>";
					$message .= "<tr><td>Start Datum</td><td>" . $row2['startdate'] . "</td></tr>";
					$message .= "<tr><td>Campagne Jaar</td><td>" . $row2['campagne'] . "</td></tr>";
					$message .= "</table>";
					$message .= "<br><br><br><br>";

				}
			}

			// Send email
			$email = new \SendGrid\Mail\Mail();
			$email->setFrom("byod@signpost.eu", "Signpost BYOD");
			$subject = 'Nieuw aangemaakte forecast';
			$email->setSubject($subject);
			$email->addTo($loginname . '@signpost.eu');
			$email->addContent(
				"text/html", $message
			);
			$sendgrid = new \SendGrid('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');
			try {
				$response = $sendgrid->send($email);
			} catch (Exception $e) {
				echo 'Caught exception: '. $e->getMessage() ."\n";
			}

			$sql = "INSERT INTO orders (synergyid, amount, SPSKU, covers, finance_type, licenses, consumer, unobligated, imageid, shipping_postcode, shipping_city, shipping_street, shipping_number, shipping_date, shipping_hour, sales, notes, forecastlink)
					SELECT synergyid, device1, `device1-SPSKU`, `device1-sleve`, `device1-finance`, `device1-licenses`, `device1-consumer`, `device1-unobligated`, 'idk', shipping_postcode, shipping_city, shipping_street, '', startdate, '', sales, remarks, concat(id,'-1')
					FROM forecasts
					where id = '" . $forecastid . "'";

			if ($conn->query($sql) === TRUE) {
				echo "Order is succesvol aangemaak.";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}

			echo '<div class="body">';
			echo "Uw forecast is toegevoegd.<br><br>";

			if ($role == 'sales') {
				echo '<a href="'. hasAccessForUrl('sales-forecasts.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			} else {
				echo '<a href="'. hasAccessForUrl('forecasts.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			}
			echo '</div>';
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		die();

	} else {

	?>

	<h2>Nieuwe Forecast</h2><br>
	<form action="forecast-form.php" name="form" method="post">
		<div class="form-row">
			<div class="col">

				<label style="font-weight: bold;" for="sales">Sales</label>
				<input type="text" class="form-control" placeholder="sales" name="sales" value="<?php echo $loginname; ?>" readonly>
				<br>
				<hr>
				<br>

				<label style="font-weight: bold;" for="verkoopkans">Verkoopkans</label>
				<input type="text" id="verkoopkansid" name="salesorderid" class="form-control">
				<br><br>

				<div id="verkoopkansinfo">
					<div class="row">
						<div class="col-3">
							<label for="synergyid">Synergy ID</label>
							<input type="text" class="form-control" name="synergyid" value="" required readonly>
						</div>

						<div class="col-9">
							<label for="school">School</label>
							<input type="text" class="form-control" name="school" value="" required readonly>
						</div>
					</div>
					<br><br>

					<div class="row">
						<div class="col">
							<label for="description">Beschrijving</label>
							<input type="text" class="form-control" name="description" value="" required readonly>
						</div>
					</div>
					<br><br>

					<div class="row">
						<div class="col-3">
							<label for="shipping_postcode">Postcode</label>
							<input type="text" class="form-control" name="shipping_postcode" value="" required readonly>
						</div>

						<div class="col-3">
							<label for="shipping_city">Gemeente</label>
							<input type="text" class="form-control" name="shipping_city" value="" required readonly>
						</div>

						<div class="col-6">
							<label for="street">Straat + Huisnummer</label>
							<input type="text" class="form-control" name="shipping_street" value="" required readonly>
						</div>
					</div>
					<br><br>
				</div>

				<hr>
				<br>

				<h3>Levering</h3><br>

				<h5>Leveradres</h5>
				<input type="radio" id="delivery_school" name="delivery_type" value="delivery_school">
				<label for="delivery_school">Levering op de school</label><br>
				<input type="radio" id="delivery_other" name="delivery_type" value="delivery_other">
				<label for="delivery_other">Levering op andere locatie</label><br>
				<input type="radio" id="delivery_home" name="delivery_type" value="delivery_home">
				<label for="delivery_home">Thuislevering</label><br><br>

				<div id="delivery_school_detail" style="display:none;">
					<div class="row">
						<div class="col-3">
							<label for="shipping_postcode">Postcode</label>
							<input type="text" class="form-control" name="delivery_shipping_postcode" value="" required readonly>
						</div>

						<div class="col-3">
							<label for="shipping_city">Gemeente</label>
							<input type="text" class="form-control" name="delivery_shipping_city" value="" required readonly>
						</div>

						<div class="col-6">
							<label for="street">Straat + Huisnummer</label>
							<input type="text" class="form-control" name="delivery_shipping_street" value="" required readonly>
						</div>
					</div>
					<br><br>
				</div>

				<div id="delivery_other_detail" style="display:none;">
					<div class="row">
						<div class="col-3">
							<label for="delivery_shipping_postcode">Postcode</label>
							<input type="text" class="form-control" name="delivery_shipping_postcode" value="">
						</div>

						<div class="col-3">
							<label for="delivery_shipping_city">Gemeente</label>
							<input type="text" class="form-control" name="delivery_shipping_city" value="">
						</div>

						<div class="col-6">
							<label for="delivery_street">Straat + Huisnummer</label>
							<input type="text" class="form-control" name="delivery_shipping_street" value="">
						</div>
					</div>
					<br><br>
				</div>

				<div id="delivery_home_detail" style="display:none;">
					<br>
					<div class="row">
						<div class="col-6">
							<p>De levering zal thuis ( bij de leerling of leerkracht ) plaatsvinden.</p>
						</div>
					</div>
					<br><br>
				</div>

				<h5>Contactpersoon voor de levering</h5>
				<div class="row">
				<div class="col-4">
					<label for="delivery_contact_name">Naam</label>
					<input type="text" class="form-control" name="delivery_contact_name" value="" required>
				</div>

				<div class="col-4">
					<label for="delivery_contact_email">E-Mail</label>
					<input type="text" class="form-control" name="delivery_contact_email" value="" required>
				</div>

				<div class="col-4">
					<label for="delivery_contact_tel">Telefoon</label>
					<input type="text" class="form-control" name="delivery_contact_tel" value="" required>
				</div>
				</div>
				<br><br>

				<h5>Contactpersoon voor financiële vragen</h5>
				<div class="row">
				<div class="col-4">
					<label for="financial_contact_name">Naam</label>
					<input type="text" class="form-control" name="financial_contact_name" value="">
				</div>

				<div class="col-4">
					<label for="financial_contact_email">E-Mail</label>
					<input type="text" class="form-control" name="financial_contact_email" value="">
				</div>

				<div class="col-4">
					<label for="financial_contact_tel">Telefoon</label>
					<input type="text" class="form-control" name="financial_contact_tel" value="">
				</div>
				</div>
				<br><br>

				<label style="font-weight: bold;" for="start-date">Te leveren vanaf</label>
				<div class="row">
					<div class="col">
						<input type="text" class="form-control datetimepicker-input" id="datetimepicker1" data-toggle="datetimepicker" data-target="#datetimepicker1" name="start-date" required />
					</div>
					<script type="text/javascript">
					$(function () {
						$('#datetimepicker1').datetimepicker({
							locale: 'nl',
							format: 'L'
						});
					});
					</script>
				</div>
				<br><br>

				<br>
				<hr>
				<br>

				<h3>Configuratie</h3><br>

				<?php
					$query = "SELECT distinct manufacturer FROM devices";
					$result = $conn->query($query);
				?>

				<!-- Computer dropdown -->
				<!-- manufacturer - model - motherboard - memory - ssd - panel -->
				<div class="row row-fluid">

					<div class="col">
						<p>#</p>
						<p style="padding-top:8px;">Laptop 1</p>
					</div>

					<div class="col">
						<label for="amount">Aantal</label>
						<input type="number" class="form-control" name="amount" style="margin-top:8px;">
					</div>

					<div class="col">
					<p>Merk</p>
						<select id="manufacturer" class="form-control" required>
							<option value="" selected></option>
							<?php
							if($result->num_rows > 0){
								while($row = $result->fetch_assoc()){
									echo '<option value="'.$row['manufacturer'].'">'.$row['manufacturer'].'</option>';
								}
							} else {
								echo '<option value="">Niets gevonden</option>';
							}
							?>
						</select>

					</div>

					<div class="col">
						<p>Model</p>
						<select id="model" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<p>CPU</p>
						<select id="motherboard" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<p>Geheugen</p>
						<select id="memory" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<p>Opslag</p>
						<select id="ssd" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<p>Scherm</p>
						<select id="panel" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<p>Garantie</p>
						<select id="warranty" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

				</div>
				<br>

				<hr>
				<br>

				<h3>Prijs</h3><br>
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>#</p>
						<p style="padding-top:8px;">Laptop 1</p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>SPSKU</p>
						<div id="SPSKU1">
							<input type="text" class="form-control" name="SPSKU1" value="" readonly>
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Standaard Prijs <br><i style="color:grey;font-size:12px;">( incl. BTW )</i></p>
						<div id="device1-defaultprice">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device1-defaultprice" disabled>
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Overeengekomen Prijs <br><i style="color:grey;font-size:12px;">( incl. BTW )</i></p>
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device1-price">
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Standaard Forfaitaire herstelprijs <br><i style="color:grey;font-size:12px;">( incl. BTW )</i></p>
						<div id="device1-defaultrepaircost">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device1-defaultrepaircost" disabled>
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Overeengekomen Forfaitaire herstelprijs <br><i style="color:grey;font-size:12px;">( incl. BTW )</i></p>
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device1-repaircost">
					</div>

				</div>
				<br>
				<br>
				<div class="row">
					<div class="col" style="display:flex;">
						<p>Facturatie via DOKO </p> <input type="checkbox" step="0.01" title=""  name="billing_DOKO" value="yes" style="margin-left:5%;transform:scale(2, 2);">
						
					</div>
				</div>
				<br>
				<hr>
				<br>

				<h3>Opties</h3><br>
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>#</p>
						<p style="padding-top:8px;">Laptop 1</p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Financiering</p>
						<select id="device1-finance" name="device1-finance" class="form-control">
							<option value="" disabled selected></option>
							<option value="School-Directe betaling">School - Directe betaling</option>
							<option value="School-Leasing">School - Leasing</option>
							<option value="School-Leermiddel">School - Leermiddel</option>
							<option value="Particulier">Particulier ( Webshop / Leermiddel )</option>
						</select>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Hoes</p>
						<select id="device1-hoes" name="device1-hoes" class="form-control">
							<option value="" disabled selected></option>
							<option value="ja">Ja</option>
							<option value="bedrukt">Ja, bedrukt</option>
							<option value="nee">Nee</option>
						</select>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Hoes Type</p>
						<select id="device1-hoestype" name="device1-sleve" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Chrome Licenties</p>
						<select id="device1-licenses" name="device1-licenses" class="form-control">
							<option value="" disabled selected></option>
							<option value="">Geen</option>
							<option value="Chrome Enkel koppelen">Enkel koppelen</option>
							<option value="Chrome Aankopen en koppelen">Aankopen en koppelen</option>
						</select>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Consument</p>
						<select id="device1-consumer" name="device1-consumer" class="form-control">
							<option value="" disabled selected></option>
							<option value="Leerling">Leerling</option>
							<option value="Leerkracht">Leerkracht</option>
						</select>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Vrijblijvend</p>
						<select id="device1-obligated" name="device1-unobligated" class="form-control">
							<option value="" disabled selected></option>
							<option value="Ja">Ja</option>
							<option value="Nee">Nee</option>
						</select>
					</div>
				</div>
				<br>

				<br>
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;max-width:fit-content;">
						<p>Orginele doos behouden <br></p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">

					<input type="checkbox" step="0.01" title=""  name="original_box" value="yes" style="transform:scale(2, 2);">
					</div>
				</div>
				<br>
				<label style="font-weight: bold;" for="label">School Label</label>
				<p>Bijvoorbeeld <b>SPB20</b></p>
				<div class="row">
					<div class="col"><input type="text" title="Voer het label in zonder -0001" class="form-control" placeholder="" name="label"></div>
					<div class="col"><label class="form-control" readonly>-0001</label></div>
				</div>
				<br><br>

				<hr>
				<br>

				<label style="font-weight: bold;" for="byodyear">BYOD Jaar</label>
				<div class="row">
					<div class="col">
					<select class="form-control" name="byodyear">
						<option value="2020">2020 - 2021</option>
						<option value="2021">2021 - 2022</option>
						<option value="2022">2022 - 2023</option>
					</select>
				</div>
				</div>
				<br><br>

				<label style="font-weight: bold;" for="refer">Referentie School</label>
				<p class="smalltext">Bijvoorbeeld "Bouw", "3de graad", "IW".</p>
				<input type="text" id="refer" name="refer" class="form-control">
				<br><br>

				<label style="font-weight: bold;" for="refer_invoice">Referentie voor op de factuur</label>
				<p class="smalltext">Bijvoorbeeld intern bestelbonnummer.</p>
				<input type="text" id="refer_invoice" name="refer_invoice" class="form-control">
				<br><br>

				<label style="font-weight: bold;" for="notes">Extra informatie over deze forecast</label>
				<textarea class="form-control" id="exampleFormControlTextarea1" name="notes" rows="3"></textarea>
				<br><br>

			</div>
		</div>

		<div class="">
			<button type="submit" class="btn btn-success">Doorsturen</button>
		</div>
		<br><br><br>

	</form>

<?php
	}
?>

</div>

<script>
var rad = document.form.delivery_type;
for (var i = 0; i < rad.length; i++) {
	rad[i].addEventListener('change', function() {
		if(this.value == 'delivery_school'){
			$('#delivery_school_detail')[0].style.display = 'block';
			$('#delivery_other_detail')[0].style.display = 'none';
			$('#delivery_home_detail')[0].style.display = 'none';
		} else if(this.value == 'delivery_other'){
			$('#delivery_school_detail')[0].style.display = 'none';
			$('#delivery_other_detail')[0].style.display = 'block';
			$('#delivery_home_detail')[0].style.display = 'none';
		} else if(this.value == 'delivery_home'){
			$('#delivery_school_detail')[0].style.display = 'none';
			$('#delivery_other_detail')[0].style.display = 'none';
			$('#delivery_home_detail')[0].style.display = 'block';
		}
	});
}

//manufacturer - model - motherboard - memory - ssd - panel
$(document).ready(function() {

	$('.SchoolSelect').select2({
		theme: "classic"
	});

	$('.ScholengroepSelect').select2({
		theme: "classic"
	});

	$('#verkoopkansid').on('change', function(){
		var verkoopkansid = $(this).val();
		if(verkoopkansid){
			$.ajax({
				type:'POST',
				url:'ajaxSchoolData.php',
				data:'verkoopkansid='+verkoopkansid,
				success:function(html){
					$('#verkoopkansinfo').html(html);
				}
			});
			$.ajax({
				type:'POST',
				url:'ajaxSchoolData.php',
				data:'verkoopkansid='+verkoopkansid+'&type=delivery',
				success:function(html){
					$('#delivery_school_detail').html(html);
				}
			});
		}
	});

	$('#device1-hoes').on('change', function(){
		var sleve = $(this).val();
		var slevetype = $('#device1-hoestype').val();
		var spsku = $($('#SPSKU1')[0].innerHTML)[0].value;

		if(sleve == "ja" || sleve == "bedrukt"){

			$.ajax({
				type:'POST',
				url:'ajaxSleveData.php',
				data:'spsku='+spsku,
				success:function(html){
					$('#device1-hoestype').html(html);
				}
			});

		} else {
			$('#device1-hoestype').html('<option value="geen">Geen</option>');
		}

	});

	$('#manufacturer').on('change', function(){
		var manufacturer = $(this).val();
		if(manufacturer){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+manufacturer,
				success:function(html){
					$('#model').html(html);
					$('#motherboard').html('<option value="" disabled selected></option>');
					$('#memory').html('<option value="" disabled selected></option>');
					$('#ssd').html('<option value="" disabled selected></option>');
					$('#panel').html('<option value="" disabled selected></option>');
					$('#warranty').html('<option value="" disabled selected></option>');
					$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
					$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
					$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#model').html('<option value="" disabled selected></option>');
			$('#motherboard').html('<option value="" disabled selected></option>');
			$('#memory').html('<option value="" disabled selected></option>');
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#model').on('change', function(){
		var model = $(this).val();
		if(model){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer').val()+'&model='+model,
				success:function(html){
					$('#motherboard').html(html);
					$('#memory').html('<option value="" disabled selected></option>');
					$('#ssd').html('<option value="" disabled selected></option>');
					$('#panel').html('<option value="" disabled selected></option>');
					$('#warranty').html('<option value="" disabled selected></option>');
					$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
					$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
					$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#motherboard').html('<option value="" disabled selected></option>');
			$('#memory').html('<option value="" disabled selected></option>');
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#motherboard').on('change', function(){
		var motherboard = $(this).val();
		if(motherboard){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer').val()+'&model='+$('#model').val()+'&motherboard='+motherboard,
				success:function(html){
					$('#memory').html(html);
					$('#ssd').html('<option value="" disabled selected></option>');
					$('#panel').html('<option value="" disabled selected></option>');
					$('#warranty').html('<option value="" disabled selected></option>');
					$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
					$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
					$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#memory').html('<option value="" disabled selected></option>');
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#memory').on('change', function(){
		var memory = $(this).val();
		if(memory){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer').val()+'&model='+$('#model').val()+'&motherboard='+$('#motherboard').val()+'&memory='+memory,
				success:function(html){
					$('#ssd').html(html);
					$('#panel').html('<option value="" disabled selected></option>');
					$('#warranty').html('<option value="" disabled selected></option>');
					$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
					$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
					$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#ssd').on('change', function(){
		var ssd = $(this).val();
		if(ssd){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer').val()+'&model='+$('#model').val()+'&motherboard='+$('#motherboard').val()+'&memory='+$('#memory').val()+'&ssd='+ssd,
				success:function(html){
					$('#panel').html(html);
					$('#warranty').html('<option value="" disabled selected></option>');
					$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
					$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
					$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#panel').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer').val()+'&model='+$('#model').val()+'&motherboard='+$('#motherboard').val()+'&memory='+$('#memory').val()+'&ssd='+$('#ssd').val()+'&panel='+$('#panel').val()+'&nr=1',
				success:function(html){
					$('#warranty').html(html);
					$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
					$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
					$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#warranty').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer').val()+'&model='+$('#model').val()+'&motherboard='+$('#motherboard').val()+'&memory='+$('#memory').val()+'&ssd='+$('#ssd').val()+'&panel='+$('#panel').val()+'&warranty='+$('#warranty').val()+'&nr=1',
				success:function(html){
					var result = $.parseJSON(html);
					$('#SPSKU1').html(result[0]);
					$('#device1-defaultprice').html(result[1]);
					$('#device1-defaultrepaircost').html(result[2]);
				}
			});
		}else{
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
			$('#device1-defaultprice').html('<input type="number" value="" class="form-control" name="device1-defaultprice" disabled>');
			$('#device1-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device1-defaultrepaircost" disabled>');
		}
	});

	$('#competition').on('change', function(){
		var competition = $(this).val();
		if(competition !== "andere"){
			$('#other-competition-div').html('<input type="text" title="Voer een concurentie in die niet te vinden is in bovenstaande lijst" class="form-control" placeholder="Andere concurentie" name="other-competition" id="other-competition" style="display:none;">');
		}else{
			$('#other-competition-div').html('<p style="font-size:12px;">Vul hier de andere competitie in:</p><input type="text" title="Voer een concurentie in die niet te vinden is in bovenstaande lijst" class="form-control" name="other-competition" id="other-competition" required><br>');
		}
	});

	$('#datetimepicker13').datetimepicker({
		inline: true,
		sideBySide: true
	});

});
</script>

<?php
include('footer.php');
?>
