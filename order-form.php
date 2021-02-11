<?php

$title = 'Order Form';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="container body">

<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {

	if ($_POST['device1-hoes'] == "bedrukt") {
		$sleve = $_POST['device1-sleve'] . " (" . $_POST['device1-hoes'] . ")";
	} else {
		$sleve = $_POST['device1-sleve'];
	}

	$sleve = mysqli_real_escape_string($conn, $sleve);
	$synergyid = mysqli_real_escape_string($conn, $_POST['synergyid']);
	$amount = mysqli_real_escape_string($conn, $_POST['amount']);
	$SPSKU1 = mysqli_real_escape_string($conn, $_POST['SPSKU1']);
	$device1finance = mysqli_real_escape_string($conn, $_POST['device1-finance']);
	$device1licenses = mysqli_real_escape_string($conn, $_POST['device1-licenses']);
	$device1consumer = mysqli_real_escape_string($conn, $_POST['device1-consumer']);
	$device1unobligated = mysqli_real_escape_string($conn, $_POST['device1-unobligated']);
	$image = mysqli_real_escape_string($conn, $_POST['image']);
	$shipping_postcode = mysqli_real_escape_string($conn, $_POST['shipping_postcode']);
	$shipping_city = mysqli_real_escape_string($conn, $_POST['shipping_city']);
	$shipping_street = mysqli_real_escape_string($conn, $_POST['shipping_street']);
	$shipping_number = mysqli_real_escape_string($conn, $_POST['shipping_number']);
	$shipping_date = mysqli_real_escape_string($conn, $_POST['shipping_date']);
	$shipping_hour = mysqli_real_escape_string($conn, $_POST['shipping_hour']);
	$sales = mysqli_real_escape_string($conn, $_POST['sales']);
	$notes = mysqli_real_escape_string($conn, $_POST['notes']);
	$forecastid = mysqli_real_escape_string($conn, $_POST['forecastid']);

	$sql = "INSERT INTO `byod-orders`.`orders` (synergyid, amount, SPSKU, covers, finance_type, licenses, consumer, unobligated, imageid, shipping_postcode, shipping_city, shipping_street, shipping_number, shipping_date, shipping_hour, sales, notes, forecastlink)
		VALUES ('" . $synergyid . "', '" . $amount . "', '" . $SPSKU1 . "', '" . $sleve . "', '" . $device1finance . "', '" . $device1licenses . "', '" . $device1consumer . "', '" . $device1unobligated . "', '" . $image . "', '" . $shipping_postcode . "', '" . $shipping_city . "', '" . $shipping_street . "', '" . $shipping_number . "', '" . $shipping_date . "', '" . $shipping_hour . "', '" . $sales . "', '" . $notes . "', '" . $forecastid . "')";

	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo "De bestelling is toegevoegd.<br><br>";
		echo '<a href="'. hasAccessForUrl('management.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
		echo '</div>';
		//echo "<script type='text/javascript'>window.top.location='stock-parts.php';</script>"; exit;
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();
	die();


} else {

	// Order Form

	$fc_model = '';
	$fc_forecastid = '';
	$fc_devicenr = '';
	$fc_synergyid = '';
	$fc_amount = '';
	$fc_spsku = '';
	$fc_finance = '';
	$fc_sleve = '';
	$fc_consumer = '';
	$fc_unobligated = '';
	$fc_sales = '';
	$fc_remarks = '';
	$fc_label = '';
	$fc_campagne = '';

	if (isset($_GET['forecastid']) !== false) {
		$sql3 = "SELECT * FROM forecasts where id = '" . $_GET['forecastid'] . "'";
		$result3 = $conn->query($sql3);

		if ($result3->num_rows > 0) {
			while($row3 = $result3->fetch_assoc()) {

				$fc_forecastid = $_GET['forecastid'];
				$fc_devicenr = $_GET['devicenr'];
				$fc_synergyid = $row3['synergyid'];
				$fc_amount = $row3['device1'];
				$fc_spsku = $row3['device1-SPSKU'];
				$fc_finance = $row3['device1-finance'];
				$fc_sleve = $row3['device1-sleve'];
				$fc_consumer = $row3['device1-consumer'];
				$fc_unobligated = $row3['device1-unobligated'];
				$fc_sales = $row3['sales'];
				$fc_remarks = $row3['remarks'];
				$fc_label = $row3['label'];
				$fc_campagne = $row3['campagne'];

			}
		} else {
			echo "0 results";
		}
	}

?>

	<h2>Nieuw Order</h2><br>
	<form action="order-form.php" method="post">
		<div class="form-row">
			<div class="col">

		<input type="text" name="forecastid" id="forecastid" value="<?php if($fc_forecastid !== ''){ echo $fc_forecastid . '-' . $fc_devicenr; } ?>" hidden>

				<label style="font-weight: bold;" for="synergyid">School</label>
				<select class="form-control SchoolSelect" name="synergyid" style="width:100%;" required <?php if ($fc_synergyid !== '') {echo 'style="pointer-events:none !important;"';}?>>

		<?php if ($fc_synergyid !== '') {
			echo '<option value="' . $fc_synergyid . '" selected>' . htmlspecialchars($fc_synergyid) . '</option>';
		} else {
		?>
		<option value="">Kies een school</option>
		<?php
		$sql = "SELECT distinct synergyid, school FROM forecasts WHERE deleted != 1";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				echo "<option value='" . $row['synergyid'] . "'>" . htmlspecialchars($row['synergyid']) . " - " . htmlspecialchars($row['school']) . "</option>";
			}
		}
	}
?>
				</select>
				<br><br><br>

<?php if ($fc_synergyid !== '') {
echo "<script>
	$(window).on('load', function() {

		setTimeout(function () {
			$('.SchoolSelect').trigger('change');
	}, 1000);

	});
						</script>";
					}?>

				<label style="font-weight: bold;">Afleveradres</label>
				<div id="leveradres">
					<div class="row">
						<div class="col-4">
							<label for="shipping_postcode">Postcode</label>
							<input type="text" class="form-control" name="shipping_postcode" required>
						</div>

						<div class="col-8">
							<label for="shipping_city">Gemeente</label>
							<input type="text" class="form-control" name="shipping_city" required>
						</div>
					</div>
					<br><br>

					<div class="row">
						<div class="col-8">
							<label for="shipping_street">straat</label>
							<input type="text" class="form-control" name="shipping_street" required>
						</div>

						<div class="col-4">
							<label for="shipping_number">Huisnummer</label>
							<input type="text" class="form-control" name="shipping_number" required>
						</div>
					</div>
				</div>
				<br><br>

				<label style="font-weight: bold;" for="sales">Sales</label>
				<input type="text" class="form-control" placeholder="sales" name="sales" value="<?php if($fc_sales !== ''){ echo $fc_sales; } else { echo $loginname; } ?>" readonly>
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
					<label for="amount">Aantal</label>
					<input type="number" class="form-control" name="amount" style="margin-top:8px;" value="<?php if ($fc_amount !== '') { echo $fc_amount; }?>" required>
				</div>

				<?php
					if ($fc_spsku !== '') {
						$sql2 = "SELECT * FROM devices where SPSKU = '" . strtok($fc_spsku, ';') . "'";
						$result2 = $conn->query($sql2);

						if ($result2->num_rows > 0) {
							while($row2 = $result2->fetch_assoc()) {
								$merk_found = '<b class="form-control" style="background-color:#ECF0F1;">' . $row2['manufacturer'] . '</b>';
								$model_found = '<b class="form-control" style="background-color:#ECF0F1;width:150px;">' . $row2['model'] . '</b>';
								$cpu_found = '<b class="form-control" style="background-color:#ECF0F1;">' . $row2['motherboard_value'] . '</b>';
								$geheugen_found = '<b class="form-control" style="background-color:#ECF0F1;">' . $row2['memory_value'] . " GB" . '</b>';
								$opslag_found = '<b class="form-control" style="background-color:#ECF0F1;">' . $row2['ssd_value'] . " GB" . '</b>';
								$scherm_found = '<b class="form-control" style="background-color:#ECF0F1;">' . $row2['panel_value'] . '</b>';
								$garantie_found = '<b class="form-control" style="background-color:#ECF0F1;">' . $row2['warranty'] . " jaar" . '</b>';
							}
						} else {
							echo "0 results";
						}
					}
				?>

				<div class="col">
				<p>Merk</p>
				<?php
					if ($fc_spsku !== '') {
						echo $merk_found;
					} else {
				?>
				<select id="manufacturer" class="form-control" required>
					<option value="" disabled selected></option>
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
				<?php
					}
				?>
				</div>

				<div class="col">
				<p>Model</p>
				<?php
					if ($fc_spsku !== '') {
						echo $model_found;
					} else {
				?>
				<select id="model" class="form-control" required>
					<option value="" disabled selected></option>
				</select>
				<?php
					}
				?>
				</div>

				<div class="col">
				<p>CPU</p>
				<?php
					if ($fc_spsku !== '') {
						echo $cpu_found;
					} else {
				?>
				<select id="motherboard" class="form-control" required>
					<option value="" disabled selected></option>
				</select>
				<?php
					}
				?>
				</div>

				<div class="col">
				<p>Geheugen</p>
				<?php
					if ($fc_spsku !== '') {
						echo $geheugen_found;
					} else {
				?>
				<select id="memory" class="form-control" required>
					<option value="" disabled selected></option>
				</select>
				<?php
					}
				?>
				</div>

				<div class="col">
				<p>Opslag</p>
				<?php
					if ($fc_spsku !== '') {
						echo $opslag_found;
					} else {
				?>
				<select id="ssd" class="form-control" required>
					<option value="" disabled selected></option>
				</select>
				<?php
					}
				?>
				</div>

				<div class="col">
				<p>Scherm</p>
				<?php
					if ($fc_spsku !== '') {
						echo $scherm_found;
					} else {
				?>
				<select id="panel" class="form-control" required>
					<option value="" disabled selected></option>
				</select>
				<?php
					}
				?>
				</div>

				<div class="col">
				<p>Garantie</p>
				<?php
					if ($fc_spsku !== '') {
						echo $garantie_found;
					} else {
				?>
				<select id="warranty" class="form-control" required>
					<option value="" disabled selected></option>
				</select>
				<?php
					}
				?>
				</div>
				</div>
				<br>

				<div id="SPSKU1">
				<input type="text" class="form-control" name="SPSKU1" value="<?php if ($fc_spsku !== '') {
				echo $fc_spsku;
	}?>" readonly required>
				</div>
				<br>

				<br>
				<hr>
				<br>
				<h3>Opties</h3><br>

				<!-- ---------------------------------- 1 ---------------------------------- -->
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Financiering</p>
						<select id="device1-finance" name="device1-finance" class="form-control"<?php if ($fc_finance) {echo ' style="pointer-events:none;"';}?>>
				<?php if (strpos($fc_finance, 'School') !== false) {
					if ($fc_finance == "School-Directe betaling") {
						echo '
							<option value="" disabled></option>
							<option value="School-Directe betaling">School - Directe betaling</option>
							';
					} elseif ($fc_finance == "School-Leasing") {
						echo '
							<option value="" disabled></option>
							<option value="School-Leasing">School - Leasing</option>
							';
					} elseif ($fc_finance == "School-Leermiddel") {
						echo '
							<option value="" disabled></option>
							<option value="School-Leermiddel">School - Leermiddel</option>
							';
					} else {
						echo '
							<option value="" disabled></option>
							<option value="School">School</option>
							';
					}
				} elseif ($fc_finance == "Particulier") {
					echo '
								<option value="" disabled></option>
								<option value="Particulier" selected>Particulier ( Webshop / Leermiddel )</option>
								';
				} else {
					echo '
								<option value="" disabled selected></option>
								<option value="Particulier">Particulier ( Webshop / Leermiddel )</option>
								<option value="School">School</option>
								';
				}?>

						</select>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Hoes</p>
						<select id="device1-hoes" name="device1-hoes" class="form-control"<?php if ($fc_sleve !== '') {echo ' style="pointer-events:none;"';}?>>
<?php if ($fc_sleve !== '' && strpos($fc_sleve, 'bedrukt') !== false) {
echo '
								<option value="" disabled></option>
								<option value="ja">Ja</option>
								<option value="bedrukt" selected>Ja, bedrukt</option>
								<option value="nee">Nee</option>';
				} elseif ($fc_sleve == '' || $fc_sleve == "geen") {
					echo '
								<option value="" disabled></option>
								<option value="ja">Ja</option>
								<option value="bedrukt">Ja, bedrukt</option>
								<option value="nee" selected>Nee</option>';
				} elseif($fc_sleve !== '') {
					echo '
								<option value="" disabled></option>
								<option value="ja" selected>Ja</option>
								<option value="bedrukt">Ja, bedrukt</option>
								<option value="nee">Nee</option>
								';
				}?>
						</select>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Hoes Type</p>
						<select id="device1-hoestype" name="device1-sleve" class="form-control"<?php if ($fc_sleve !== '') {echo ' style="pointer-events:none;"';}?>>
<?php if ($fc_sleve !== '') {
echo '
								<option value="" disabled></option>
								<option value="' . $fc_sleve . '" selected>' . $fc_sleve . '</option>';
							} else {
								echo '
								<option value="" disabled></option>
								';
							}?>
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
						<select id="device1-consumer" name="device1-consumer" class="form-control"<?php if ($fc_consumer !== '') {echo ' style="pointer-events:none;"';}?>>
<?php if ($fc_consumer == "Leerling") {
echo '<option value="" disabled></option>
	<option value="Leerling" selected>Leerling</option>
	<option value="Leerkracht">Leerkracht</option>';
							} elseif ($fc_consumer == "Leerkracht") {
								echo '<option value="" disabled></option>
									<option value="Leerling">Leerling</option>
									<option value="Leerkracht" selected>Leerkracht</option>';
							} else {
								echo '<option value="" disabled selected></option>
									<option value="Leerling">Leerling</option>
									<option value="Leerkracht">Leerkracht</option>';
							}?>
						</select>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Vrijblijvend</p>
						<select id="device1-obligated" name="device1-unobligated" class="form-control"<?php if ($fc_unobligated !== '') {echo ' style="pointer-events:none;"';}?>>
<?php if ($fc_unobligated == "Nee") {
echo '<option value="" disabled></option>
	<option value="Ja">Ja</option>
	<option value="Nee" selected>Nee</option>';
									} elseif ($fc_unobligated == "Ja") {
										echo '<option value="" disabled></option>
											<option value="Ja" selected>Ja</option>
											<option value="Nee">Nee</option>';
									} else {
										echo '<option value="" disabled selected></option>
											<option value="Ja">Ja</option>
											<option value="Nee">Nee</option>';
									}?>
						</select>
					</div>

				</div>
				<br>

				<br>
				<hr>
				<br>
				<h3>Bestelling</h3><br>

				<label style="font-weight: bold;" for="leverdatum">Leverdatum</label>

				<div class="row">
					<div class="col">
						<p>Dag</p>
						<input type="text" class="form-control datetimepicker-input" id="datetimepicker1" data-toggle="datetimepicker" data-target="#datetimepicker1" name="shipping_date" required />
					</div>
					<div class="col">
						<p>Uur <em style="font-size:10px;">( niet verplicht )</em></p>
						<input type="text" class="form-control datetimepicker-input" id="datetimepicker2" data-toggle="datetimepicker" data-target="#datetimepicker2" name="shipping_hour" />
					</div>
										<script type="text/javascript">
										$(function () {
											$('#datetimepicker1').datetimepicker({
											locale: 'nl',
												format: 'L'
										});
											$('#datetimepicker2').datetimepicker({
											locale: 'nl',
												format: 'LT'
										});
										});
	</script>
				</div>

				<br><br>

				<label style="font-weight: bold;" for="image">Image</label>
				<p>Kies een image uit de onderstaande lijst, indien u er geen te zien krijgt, zal er een image aangemaakt moeten worden</p>
				<select id="image" name="image" class="form-control" required>
					<option value="" selected disabled></option>
					<option value="idk">Ik weet het niet</option>
					<option value="chrome">Chromebook</option>
					<option value="fabriek">OOBE Fabriek (Lenovo/HP Incl. bloatware)</option>
					<option value="clean">Clean Windows 10</option>
					<option value="nieuw">Nieuwe nog te maken Signpost Image</option>
				</select>
				<br><br>

				<label style="font-weight: bold;" for="label">School Label</label>
				<p>Bijvoorbeeld <b>SPB20</b><br>
<?php
										$labels = '';
	$query = "SELECT GROUP_CONCAT(computername) AS labels FROM images2020 WHERE synergyid = '" . $fc_synergyid . "' AND status = 'done'";
	$result = $conn->query($query);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$labels .= $row['labels'] . ',';
		}
	}
	$query = "SELECT GROUP_CONCAT(label) AS labels FROM orders WHERE synergyid = '" . $fc_synergyid . "'";
	$result = $conn->query($query);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$labels .= $row['labels'] . ', ';
		}
	}
	if (rtrim($labels, " \,") !== ''){
		echo 'Eerder gekozen labels voor Synergy ID ' . $fc_synergyid . ' zijn: ' . rtrim($labels, " \,");
	}
?>
				</p>

				<div class="row">
					<div class="col"><input type="text" title="Voer het label in zonder -0001" class="form-control" placeholder="" value="<?php if ($fc_label !== '') { echo $fc_label; }?>" name="label" required></div>
					<div class="col"><label class="form-control">-0001</label></div>
				</div>
				<br><br>

				<label style="font-weight: bold;" for="campagne">BYOD Jaar</label>
				<div class="row">
					<div class="col">
					<select class="form-control" name="campagne">
						<option value="2020" <?php if ($fc_campagne == '2020') { echo ' selected'; }?>>2020 - 2021</option>
						<option value="2021" <?php if ($fc_campagne == '2021') { echo ' selected'; }?>>2021 - 2022</option>
						<option value="2022" <?php if ($fc_campagne == '2022') { echo ' selected'; }?>>2022 - 2023</option>
					</select>
				</div>
				</div>
				<br><br>

				<!-- <label style="font-weight: bold;" for="vrijblijvend">Vrijblijvend aanbod?</label><br>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="vrijblijvend" id="vrijblijvend" value="ja">
					<label class="form-check-label" for="vrijblijvend">Ja</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="vrijblijvend" id="vrijblijvend" value="nee">
					<label class="form-check-label" for="vrijblijvend">Nee</label>
				</div>
				<br><br><br> -->

				<label style="font-weight: bold;" for="notes">Extra informatie over dit order</label>
				<textarea class="form-control" id="exampleFormControlTextarea1" name="notes" rows="5"><?php echo $fc_remarks; ?></textarea>
				<br><br>

			</div>
		</div>
	<br>

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

//manufacturer - model - motherboard - memory - ssd - panel
$(document).ready(function() {
	$('.SchoolSelect').select2({
		theme: "classic"
	});

	$('.SchoolSelect').on('change', function(){
		var synergyid = $(this).val();
		var spsku = $($('#SPSKU1')[0].innerHTML)[0].value;
		if(synergyid){

			$.ajax({
				type:'POST',
				url:'ajaxSchoolData.php',
				data:'synergyid='+synergyid+'&type=order',
				success:function(html){
					$('#leveradres').html(html);
				}
			});

			$.ajax({
				type:'POST',
				url:'ajaxImageData.php',
				data:'synergyid='+synergyid+'&spsku='+spsku,
				success:function(html){
					$('#image').html(html);
				}
			});

		}else{
			$('#image').html('<option value="" selected></option><option value="geen">Geen</option><option value="fabriek">Fabrieks Image (Lenovo/HP)</option><option value="nieuw">Nieuwe Signpost Image</option>');
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
				}
			});
		}else{
			$('#motherboard').html('<option value="" disabled selected></option>');
			$('#memory').html('<option value="" disabled selected></option>');
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
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
				}
			});
		}else{
			$('#memory').html('<option value="" disabled selected></option>');
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
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
				}
			});
		}else{
			$('#ssd').html('<option value="" disabled selected></option>');
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
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
				}
			});
		}else{
			$('#panel').html('<option value="" disabled selected></option>');
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
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
				}
			});
		}else{
			$('#warranty').html('<option value="" disabled selected></option>');
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
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
				}
			});
		}else{
			$('#SPSKU1').html('<input type="text" class="form-control" name="SPSKU1" value="" readonly required>');
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
