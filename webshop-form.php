<?php

$title = 'Webshop Form';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-conn.php');

?>

<div class="container body">

	<?php

	if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$sql = "INSERT INTO webshops (sales,synergyid,salesorderid,school,description,shipping_postcode,shipping_city,shipping_street,device1,device2,device3,device4,`device1-SPSKU`,`device1-price`,`device1-repaircost`,`device1-finance`,`device1-sleve`,`device1-consumer`,`device1-unobligated`,`device2-SPSKU`,`device2-price`,`device2-repaircost`,`device2-finance`,`device2-sleve`,`device2-consumer`,`device2-unobligated`,`device3-SPSKU`,`device3-price`,`device3-repaircost`,`device3-finance`,`device3-sleve`,`device3-consumer`,`device3-unobligated`,`device4-SPSKU`,`device4-price`,`device4-repaircost`,`device4-finance`,`device4-sleve`,`device4-consumer`,`device4-unobligated`,`huurprijs11`,`huurtermijn11`,`huurwaarborg11`,`huurprijs12`,`huurtermijn12`,`huurwaarborg12`,`huurprijs13`,`huurtermijn13`,`huurwaarborg13`,`huurprijs21`,`huurtermijn21`,`huurwaarborg21`,`huurprijs22`,`huurtermijn22`,`huurwaarborg22`,`huurprijs23`,`huurtermijn23`,`huurwaarborg23`,`huurprijs31`,`huurtermijn31`,`huurwaarborg31`,`huurprijs32`,`huurtermijn32`,`huurwaarborg32`,`huurprijs33`,`huurtermijn33`,`huurwaarborg33`,`huurprijs41`,`huurtermijn41`,`huurwaarborg41`,`huurprijs42`,`huurtermijn42`,`huurwaarborg42`,`huurprijs43`,`huurtermijn43`,`huurwaarborg43`,`webshop-name`,`special-field`,`default-text-changes`,`webshop`,`webshop-text`,`webshop-deadline`,`leermiddel`,`rent-startdate`,`leermiddel-text`,`leermiddel-deadline`,`webshop-exists`,logo,remarks) VALUES ('" . addslashes($_POST['sales']) . "','" . addslashes($_POST['synergyid']) . "','" . addslashes($_POST['salesorderid']) . "','" . addslashes($_POST['school']) . "','" . addslashes($_POST['description']) . "','" . addslashes($_POST['shipping_postcode']) . "','" . addslashes($_POST['shipping_city']) . "','" . addslashes($_POST['shipping_street']) . "','" . addslashes($_POST['amount']) . "','" . addslashes($_POST['amount2']) . "','" . addslashes($_POST['amount3']) . "','" . addslashes($_POST['amount4']) . "','" . addslashes($_POST['SPSKU1']) . "','" . addslashes($_POST['device1-price']) . "','" . addslashes($_POST['device1-repaircost']) . "','" . addslashes($_POST['device1-finance']) . "','" . addslashes($_POST['device1-hoes']) . "','" . addslashes($_POST['device1-consumer']) . "','" . addslashes($_POST['device1-unobligated']) . "','" . addslashes($_POST['SPSKU2']) . "','" . addslashes($_POST['device2-price']) . "','" . addslashes($_POST['device2-repaircost']) . "','" . addslashes($_POST['device2-finance']) . "','" . addslashes($_POST['device2-hoes']) . "','" . addslashes($_POST['device2-consumer']) . "','" . addslashes($_POST['device2-unobligated']) . "','" . addslashes($_POST['SPSKU3']) . "','" . addslashes($_POST['device3-price']) . "','" . addslashes($_POST['device3-repaircost']) . "','" . addslashes($_POST['device3-finance']) . "','" . addslashes($_POST['device3-hoes']) . "','" . addslashes($_POST['device3-consumer']) . "','" . addslashes($_POST['device3-unobligated']) . "','" . addslashes($_POST['SPSKU4']) . "','" . addslashes($_POST['device4-price']) . "','" . addslashes($_POST['device4-repaircost']) . "','" . addslashes($_POST['device4-finance']) . "','" . addslashes($_POST['device4-hoes']) . "','" . addslashes($_POST['device4-consumer']) . "','" . addslashes($_POST['device4-unobligated']) . "','" . addslashes($_POST['huurprijs11']) . "','" . addslashes($_POST['huurtermijn11']) . "','" . addslashes($_POST['huurwaarborg11']) . "','" . addslashes($_POST['huurprijs12']) . "','" . addslashes($_POST['huurtermijn12']) . "','" . addslashes($_POST['huurwaarborg12']) . "','" . addslashes($_POST['huurprijs13']) . "','" . addslashes($_POST['huurtermijn13']) . "','" . addslashes($_POST['huurwaarborg13']) . "','" . addslashes($_POST['huurprijs21']) . "','" . addslashes($_POST['huurtermijn21']) . "','" . addslashes($_POST['huurwaarborg21']) . "','" . addslashes($_POST['huurprijs22']) . "','" . addslashes($_POST['huurtermijn22']) . "','" . addslashes($_POST['huurwaarborg22']) . "','" . addslashes($_POST['huurprijs23']) . "','" . addslashes($_POST['huurtermijn23']) . "','" . addslashes($_POST['huurwaarborg23']) . "','" . addslashes($_POST['huurprijs31']) . "','" . addslashes($_POST['huurtermijn31']) . "','" . addslashes($_POST['huurwaarborg31']) . "','" . addslashes($_POST['huurprijs32']) . "','" . addslashes($_POST['huurtermijn32']) . "','" . addslashes($_POST['huurwaarborg32']) . "','" . addslashes($_POST['huurprijs33']) . "','" . addslashes($_POST['huurtermijn33']) . "','" . addslashes($_POST['huurwaarborg33']) . "','" . addslashes($_POST['huurprijs41']) . "','" . addslashes($_POST['huurtermijn41']) . "','" . addslashes($_POST['huurwaarborg41']) . "','" . addslashes($_POST['huurprijs42']) . "','" . addslashes($_POST['huurtermijn42']) . "','" . addslashes($_POST['huurwaarborg42']) . "','" . addslashes($_POST['huurprijs43']) . "','" . addslashes($_POST['huurtermijn43']) . "','" . addslashes($_POST['huurwaarborg43']) . "','" . addslashes($_POST['webshop-name']) . "','" . addslashes($_POST['special-field']) . "','" . addslashes($_POST['default-text-changes']) . "','" . addslashes($_POST['webshop']) . "','" . addslashes($_POST['webshop-text']) . "','" . addslashes($_POST['webshop-deadline']) . "','" . addslashes($_POST['leermiddel']) . "','" . addslashes($_POST['rent-startdate']) . "','" . addslashes($_POST['leermiddel-text']) . "','" . addslashes($_POST['leermiddel-deadline']) . "','" . addslashes($_POST['webshop-exists']) . "','" . addslashes($_POST['logo']) . "','" . addslashes($_POST['notes']) . "')";

		if ($conn->query($sql) === TRUE) {
			echo '<div class="body">';
			echo "Uw webshop aanvraag is toegevoegd.<br><br>";
			echo '<a href="'. hasAccessForUrl('webshop-form.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			echo '</div>';
			//echo "<script type='text/javascript'>window.top.location='stock-parts.php';</script>"; exit;
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		// print_r($_POST);

		$conn->close();
		die();

	} else {

	?>

	<h2>Nieuwe Webshop</h2><br>
	<form action="webshop-form.php" method="post">
		<div class="form-row">
			<div class="col">

				<label style="font-weight: bold;" for="sales">Sales</label>
				<input type="text" class="form-control" placeholder="sales" name="sales" value="<?php echo $loginname; ?>" readonly>
				<br>
				<hr>
				<br>

				<label style="font-weight: bold;" for="synergyid">School</label>
				<select class="form-control SchoolSelect" name="synergyid" style="width:100%;" required>
					<option value="">Kies een school</option>
					<?php
					$tsql= "Select top (20000) ltrim(cmp_code) as synergyid, cmp_name as schoolnaam, cmp_fadd1 as straat, cmp_fpc as postcode, cmp_fcity as gemeente from cicmpy where cmp_type='C' and sct_code in ('IN', 'IM')";
					$getResults= sqlsrv_query($msconn, $tsql);
					$results = "";

					if ($getResults == FALSE){
						die(FormatErrors(sqlsrv_errors()));
					}

					echo "<option value=''>Kies een school</option>";

					while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
						$results .= "<option value='" . str_replace(' ', '', $row['synergyid']) . "'>" . str_replace(' ', '', $row['synergyid']) . " - " . $row['schoolnaam'] . " ( " . $row['straat'] . " - " . $row['postcode'] . " " . $row['gemeente'] . " )</option>";
					}

					if($results == ""){
						echo "<option value=''>niets gevonden</option>";
					} else {
						echo $results;
					}

					sqlsrv_free_stmt($getResults);

					?>
				</select>
				<br><br><br>

				<!-- <label style="font-weight: bold;" for="verkoopkans">Verkoopkans</label>
				<input type="text" id="verkoopkansid" name="salesorderid" class="form-control">
				<br><br> -->

				<div id="verkoopkansinfo">
					<div class="row">
						<div class="col-3">
							<label for="shipping_postcode">Postcode</label>
							<input type="text" class="form-control" name="shipping_postcode" value="" required>
						</div>

						<div class="col-3">
							<label for="shipping_city">Gemeente</label>
							<input type="text" class="form-control" name="shipping_city" value="" required>
						</div>

						<div class="col-6">
							<label for="street">Straat + Huisnummer</label>
							<input type="text" class="form-control" name="shipping_street" value="" required>
						</div>
					</div>
					<br><br>
				</div>

				<hr>
				<br>

				<h3>Configuratie</h3><br>

				<?php
					$query = "SELECT distinct manufacturer FROM devices";
					$result = $conn->query($query);
				?>

				<!-- ---------------------- 1 ------------------- -->
				<!-- Computer dropdown -->
				<!-- manufacturer - model - motherboard - memory - ssd - panel -->
				<div class="row row-fluid">

					<div class="col">
						<p>#</p>
						<p style="padding-top:8px;">Laptop 1</p>
					</div>

					<!-- <div class="col">
						<label for="amount">Aantal</label>
						<input type="number" class="form-control" name="amount" style="margin-top:8px;">
					</div> -->

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

				<!-- ------------------------------------------------------ 2 --------------------------------------------------- -->

				<div class="row row-fluid">
				<?php
					$query2 = "SELECT distinct manufacturer FROM devices";
					$result2 = $conn->query($query2);
				?>
					<div class="col">
						<p style="padding-top:8px;">Laptop 2</p>
					</div>

					<!-- <div class="col">
						<input type="number" class="form-control" name="amount2">
					</div> -->

					<div class="col">
						<select id="manufacturer2" class="form-control">
							<option value="" disabled selected></option>
							<?php
							if($result2->num_rows > 0){
								while($row2 = $result2->fetch_assoc()){
									echo '<option value="'.$row2['manufacturer'].'">'.$row2['manufacturer'].'</option>';
								}
							} else {
								echo '<option value="">Niets gevonden</option>';
							}
							?>
						</select>

					</div>

					<div class="col">
						<select id="model2" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="motherboard2" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="memory2" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="ssd2" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="panel2" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="warranty2" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

				</div>
				<br>

				<!-- ------------------------------------------------------ 3 --------------------------------------------------- -->

				<div class="row row-fluid">
				<?php
					$query3 = "SELECT distinct manufacturer FROM devices";
					$result3 = $conn->query($query3);
				?>
					<div class="col">
						<p style="padding-top:8px;">Laptop 3</p>
					</div>

					<!-- <div class="col">
						<input type="number" class="form-control" name="amount3">
					</div> -->

					<div class="col">
						<select id="manufacturer3" class="form-control">
							<option value="" disabled selected></option>
							<?php
							if($result3->num_rows > 0){
								while($row3 = $result3->fetch_assoc()){
									echo '<option value="'.$row3['manufacturer'].'">'.$row3['manufacturer'].'</option>';
								}
							} else {
								echo '<option value="">Niets gevonden</option>';
							}
							?>
						</select>

					</div>

					<div class="col">
						<select id="model3" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="motherboard3" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="memory3" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="ssd3" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="panel3" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="warranty3" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

				</div>
				<br>

				<!-- ------------------------------------------------------ 4 --------------------------------------------------- -->

				<div class="row row-fluid">
				<?php
					$query4 = "SELECT distinct manufacturer FROM devices";
					$result4 = $conn->query($query4);
				?>
					<div class="col">
						<p style="padding-top:8px;">Laptop 4</p>
					</div>

					<!--<div class="col">
						<input type="number" class="form-control" name="amount4">
					</div>-->

					<div class="col">
						<select id="manufacturer4" class="form-control">
							<option value="" disabled selected></option>
							<?php
							if($result4->num_rows > 0){
								while($row4 = $result4->fetch_assoc()){
									echo '<option value="'.$row4['manufacturer'].'">'.$row4['manufacturer'].'</option>';
								}
							} else {
								echo '<option value="">Niets gevonden</option>';
							}
							?>
						</select>

					</div>

					<div class="col">
						<select id="model4" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="motherboard4" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="memory4" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="ssd4" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="panel4" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

					<div class="col">
						<select id="warranty4" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div>

				</div>
				<br>

				<hr>
				<br>

				<h3>Prijs</h3><br>
				<!-- ---------------------------------- 1 ---------------------------------- -->
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

				<!-- ---------------------------------- 2 ---------------------------------- -->
				<div class="row">
					<div class="col">
						<p style="padding-top:8px;">Laptop 2</p>
					</div>
					<div class="col">
						<div id="SPSKU2">
						<input type="text" class="form-control" name="SPSKU2" value="" readonly>
						</div>
					</div>
					<div class="col">
						<div id="device2-defaultprice">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device2-defaultprice" disabled>
						</div>
					</div>
					<div class="col">
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device2-price">
					</div>
					<div class="col">
						<div id="device2-defaultrepaircost">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device2-defaultrepaircost" disabled>
						</div>
					</div>
					<div class="col">
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device2-repaircost">
					</div>
				</div>
				<br>

				<!-- ---------------------------------- 3 ---------------------------------- -->
				<div class="row">
					<div class="col">
						<p style="padding-top:8px;">Laptop 3</p>
					</div>
					<div class="col">
						<div id="SPSKU3">
						<input type="text" class="form-control" name="SPSKU3" value="" readonly>
						</div>
					</div>
					<div class="col">
						<div id="device3-defaultprice">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device3-defaultprice" disabled>
						</div>
					</div>
					<div class="col">
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device3-price">
					</div>
					<div class="col">
						<div id="device3-defaultrepaircost">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device3-defaultrepaircost" disabled>
						</div>
					</div>
					<div class="col">
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device3-repaircost">
					</div>
				</div>
				<br>

				<!-- ---------------------------------- 4 ---------------------------------- -->
				<div class="row">
					<div class="col">
						<p style="padding-top:8px;">Laptop 4</p>
					</div>
					<div class="col">
						<div id="SPSKU4">
						<input type="text" class="form-control" name="SPSKU4" value="" readonly>
						</div>
					</div>
					<div class="col">
						<div id="device4-defaultprice">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device4-defaultprice" disabled>
						</div>
					</div>
					<div class="col">
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device4-price">
					</div>
					<div class="col">
						<div id="device4-defaultrepaircost">
							<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device4-defaultrepaircost" disabled>
						</div>
					</div>
					<div class="col">
						<input type="number" pattern="[0-9]+([\.,][0-9]+)?" step="0.01" title="This should be a number with up to 2 decimal places." class="form-control" name="device4-repaircost">
					</div>
				</div>
				<br>


				<!-- ---------------------------------------------------------------------------------------------------------------------- -->

				<hr>
				<br>

				<h3>Huur</h3><br>
				<!-- ---------------------------------- 1 ---------------------------------- -->
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>#</p>
						<p style="padding-top:8px;">Laptop 1</p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurprijs <br>1</p>
						<div id="huurprijs11">
							<input type="text" class="form-control" name="huurprijs11" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurtermijn<br> 1</p>
						<div id="huurtermijn11">
							<input type="text" class="form-control" name="huurtermijn11" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurwaarborg<br> 1</p>
						<div id="huurwaarborg11">
							<input type="text" class="form-control" name="huurwaarborg11" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurprijs<br> 2</p>
						<div id="huurprijs12">
							<input type="text" class="form-control" name="huurprijs12" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurtermijn<br> 2</p>
						<div id="huurtermijn12">
							<input type="text" class="form-control" name="huurtermijn12" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurwaarborg<br> 2</p>
						<div id="huurwaarborg12">
							<input type="text" class="form-control" name="huurwaarborg12" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurprijs<br> 3</p>
						<div id="huurprijs13">
							<input type="text" class="form-control" name="huurprijs13" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurtermijn<br> 3</p>
						<div id="huurtermijn13">
							<input type="text" class="form-control" name="huurtermijn13" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Huurwaarborg<br> 3</p>
						<div id="huurwaarborg13">
							<input type="text" class="form-control" name="huurwaarborg13" value="">
						</div>
					</div>
				</div>
				<br>

				<!-- ---------------------------------- 2 ---------------------------------- -->
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p style="padding-top:8px;">Laptop 2</p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs21">
							<input type="text" class="form-control" name="huurprijs21" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn21">
							<input type="text" class="form-control" name="huurtermijn21" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg21">
							<input type="text" class="form-control" name="huurwaarborg21" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs22">
							<input type="text" class="form-control" name="huurprijs22" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn22">
							<input type="text" class="form-control" name="huurtermijn22" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg22">
							<input type="text" class="form-control" name="huurwaarborg22" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs23">
							<input type="text" class="form-control" name="huurprijs23" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn23">
							<input type="text" class="form-control" name="huurtermijn23" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg23">
							<input type="text" class="form-control" name="huurwaarborg23" value="">
						</div>
					</div>
				</div>

				<!-- ---------------------------------- 3 ---------------------------------- -->
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p style="padding-top:8px;">Laptop 3</p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs31">
							<input type="text" class="form-control" name="huurprijs31" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">

						<div id="huurtermijn31">
							<input type="text" class="form-control" name="huurtermijn31" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg31">
							<input type="text" class="form-control" name="huurwaarborg31" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs32">
							<input type="text" class="form-control" name="huurprijs32" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn32">
							<input type="text" class="form-control" name="huurtermijn32" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg32">
							<input type="text" class="form-control" name="huurwaarborg32" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs33">
							<input type="text" class="form-control" name="huurprijs33" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn33">
							<input type="text" class="form-control" name="huurtermijn33" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg33">
							<input type="text" class="form-control" name="huurwaarborg33" value="">
						</div>
					</div>

				</div>


				<!-- ---------------------------------- 4 ---------------------------------- -->
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p style="padding-top:8px;">Laptop 4</p>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs41">
							<input type="text" class="form-control" name="huurprijs41" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn41">
							<input type="text" class="form-control" name="huurtermijn41" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg41">
							<input type="text" class="form-control" name="huurwaarborg41" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs42">
							<input type="text" class="form-control" name="huurprijs42" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn42">
							<input type="text" class="form-control" name="huurtermijn42" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg42">
							<input type="text" class="form-control" name="huurwaarborg42" value="">
						</div>
					</div>

					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurprijs43">
							<input type="text" class="form-control" name="huurprijs43" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurtermijn43">
							<input type="text" class="form-control" name="huurtermijn43" value="">
						</div>
					</div>
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<div id="huurwaarborg43">
							<input type="text" class="form-control" name="huurwaarborg43" value="">
						</div>
					</div>

				</div>
				<br>

				<!-- ---------------------------------------------------------------------------------------------------------------------- -->

				<hr>
				<br>

				<h3>Opties</h3><br>

				<!-- ---------------------------------- 1 ---------------------------------- -->
				<div class="row">
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>#</p>
						<p style="padding-top:8px;">Laptop 1</p>
					</div>
					<!--<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Financiering</p>
						<select id="device1-finance" name="device1-finance" class="form-control">
							<option value="" disabled selected></option>
							<option value="Particulier">Particulier ( Webshop / Leermiddel )</option>
							<option value="School">School</option>
						</select>
					</div>-->
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Hoes</p>
						<select id="device1-hoes" name="device1-hoes" class="form-control">
							<option value="" disabled selected></option>
							<option value="ja">Ja</option>
							<option value="bedrukt">Ja, bedrukt</option>
							<option value="nee">Nee</option>
						</select>
					</div>
					<!-- <div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Hoes Type</p>
						<select id="device1-hoestype" name="device1-sleve" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div> -->
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Consument</p>
						<select id="device1-consumer" name="device1-consumer" class="form-control">
							<option value="" disabled selected></option>
							<option value="Leerling">Leerling</option>
							<option value="Leerkracht">Leerkracht</option>
						</select>
					</div>
<!-- 					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<p>Vrijblijvend</p>
						<select id="device1-obligated" name="device1-unobligated" class="form-control">
							<option value="" disabled selected></option>
							<option value="Ja">Ja</option>
							<option value="Nee">Nee</option>
						</select>
					</div> -->
				</div>
				<br>

				<!-- ---------------------------------- 2 ---------------------------------- -->
				<div class="row">
					<div class="col">
						<p style="padding-top:8px;">Laptop 2</p>
					</div>
					<!-- <div class="col">
						<select id="device2-finance" name="device2-finance" class="form-control">
							<option value="" disabled selected></option>
							<option value="Particulier">Particulier ( Webshop / Leermiddel )</option>
							<option value="School">School</option>
						</select>
					</div> -->
					<div class="col">
						<select id="device2-hoes" name="device2-hoes" class="form-control">
							<option value="" disabled selected></option>
							<option value="ja">Ja</option>
							<option value="bedrukt">Ja, bedrukt</option>
							<option value="nee">Nee</option>
						</select>
					</div>
					<!-- <div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device2-hoestype" name="device2-sleve" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div> -->
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device2-consumer" name="device2-consumer" class="form-control">
							<option value="" disabled selected></option>
							<option value="Leerling">Leerling</option>
							<option value="Leerkracht">Leerkracht</option>
						</select>
					</div>
<!-- 					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device2-obligated" name="device2-unobligated" class="form-control">
							<option value="" disabled selected></option>
							<option value="Ja">Ja</option>
							<option value="Nee">Nee</option>
						</select>
					</div> -->
				</div>
				<br>

				<!-- ---------------------------------- 3 ---------------------------------- -->
				<div class="row">
					<div class="col">
						<p style="padding-top:8px;">Laptop 3</p>
					</div>
					<!-- <div class="col">
						<select id="device3-finance" name="device3-finance" class="form-control">
							<option value="" disabled selected></option>
							<option value="Particulier">Particulier ( Webshop / Leermiddel )</option>
							<option value="School">School</option>
						</select>
					</div> -->
					<div class="col">
						<select id="device3-hoes" name="device3-hoes" class="form-control">
							<option value="" disabled selected></option>
							<option value="ja">Ja</option>
							<option value="bedrukt">Ja, bedrukt</option>
							<option value="nee">Nee</option>
						</select>
					</div>
					<!-- <div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device3-hoestype" name="device3-sleve" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div> -->
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device3-consumer" name="device3-consumer" class="form-control">
							<option value="" disabled selected></option>
							<option value="Leerling">Leerling</option>
							<option value="Leerkracht">Leerkracht</option>
						</select>
					</div>
<!-- 					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device3-obligated" name="device3-unobligated" class="form-control">
							<option value="" disabled selected></option>
							<option value="Ja">Ja</option>
							<option value="Nee">Nee</option>
						</select>
					</div> -->
				</div>
				<br>

				<!-- ---------------------------------- 4 ---------------------------------- -->
				<div class="row">
					<div class="col">
						<p style="padding-top:8px;">Laptop 4</p>
					</div>
					<!-- <div class="col">
						<select id="device4-finance" name="device4-finance" class="form-control">
							<option value="" disabled selected></option>
							<option value="Particulier">Particulier ( Webshop / Leermiddel )</option>
							<option value="School">School</option>
						</select>
					</div> -->
					<div class="col">
						<select id="device4-hoes" name="device4-hoes" class="form-control">
							<option value="" disabled selected></option>
							<option value="ja">Ja</option>
							<option value="bedrukt">Ja, bedrukt</option>
							<option value="nee">Nee</option>
						</select>
					</div>
					<!-- <div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device4-hoestype" name="device4-sleve" class="form-control">
							<option value="" disabled selected></option>
						</select>
					</div> -->
					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device4-consumer" name="device4-consumer" class="form-control">
							<option value="" disabled selected></option>
							<option value="Leerling">Leerling</option>
							<option value="Leerkracht">Leerkracht</option>
						</select>
					</div>
<!-- 					<div class="col" style="display:flex;flex-direction:column;justify-content:space-between;">
						<select id="device4-obligated" name="device4-unobligated" class="form-control">
							<option value="" disabled selected></option>
							<option value="Ja">Ja</option>
							<option value="Nee">Nee</option>
						</select>
					</div> -->
				</div>
				<br>

				<!--<label style="font-weight: bold;" for="label">Label</label>
				<p>Bijvoorbeeld SPB20</p>
				<div class="row">
					<div class="col"><input type="text" title="Voer het label in zonder -0001" class="form-control" placeholder="" onkeyup="/*this.value = this.value.replace(/-0001$/, '');this.value = this.value.toUpperCase()+'-0001';*/" name="label" required></div>
					<div class="col"><label>-0001</label></div>
				</div>

				<br><br>-->

				<hr>
				<br>

				<!-- <label style="font-weight: bold;" for="start-date">Startdatum van het project</label>
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
				<br><br> -->

				<label style="font-weight: bold;" for="financieringsoptie">Financieringsoptie?</label><br>
				<input type="checkbox" id="webshop" name="webshop" value="ja">
				<label for="webshop"> Webshop (Aankoop door ouders) </label><br>
				<input type="checkbox" id="leermiddel" name="leermiddel" value="ja">
				<label for="leermiddel"> Leermiddel (huur door ouders) </label><br>
				<br><br>

				<label style="font-weight: bold;" for="webshop-exists">Bestaat er reeds een aangepaste shop voor BYOD 2020?</label><br>
				<input type="radio" id="ja" name="webshop-exists" value="ja">
				<label for="ja">Ja</label><br>
				<input type="radio" id="nee" name="webshop-exists" value="nee">
				<label for="nee">Nee</label><br>
				<br><br>

				<label style="font-weight: bold;" for="webshop-name">Naam van de webshop?</label>
				<p style="font-size:12px;color:grey;">Wat na "www.academicshop.be/" komt<br>
				Als er 1 webshop voor de school is, is dit de afkorting v.d. school: VB SFC (=> "www.academicshop.be/sfc" ) <br>
				Als er meer dan 1 webshop is, maak je een aparte naam: VB: LLN.KVRI (=> "www.academicshop.be/lln.kvri") <br>
				NB: Indien meer dan 1 webshop : straks 2e form invullen met de andere naam hier.</p>
				<input type="text" id="webshop-name" name="webshop-name" class="form-control">
				<br><br>

				<label style="font-weight: bold;" for="logo">Logo van de school</label>
				<p style="font-size:12px;color:grey;">Plak hieronder de url van de afbeelding die op de webshop moet komen.<br>Deze kan je vinden door naar de website van de school te gaan, rechts te klikken op het logo van de school en te kiezen voor "Afbeeldingslocatie Kopiëren"</p>
				<input type="text" id="logo" name="logo" class="form-control">
				<br><br>

				<label style="font-weight: bold;" for="default-text-changes">Is er iets BELANGRIJKS aan te merken of toe te voegen op de standaardtekst van openingspagina van de webshop? </label><br>
				<input type="radio" id="ja" name="default-text-changes" value="ja">
				<label for="ja">Ja</label><br>
				<input type="radio" id="nee" name="default-text-changes" value="nee">
				<label for="nee">Nee</label><br>
				<br><br>

				<label style="font-weight: bold;" for="webshop-text">Hieronder de standaard inleidende tekst van de webshop. <em style="color:red;">Pas aan waar nodig!</em></label>
				<textarea class="form-control" id="webshop-text" name="webshop-text" rows="25">
Welkom op de webshop van <<Voorbeeldschool>>

Welkom bij Academic Shop, de online winkel van Signpost, in samenwerking met <<Voorbeeldschool>> (schooljaar 2020-2021).

Speciaal voor de leerlingen van de school werd een sterk aanbod samengesteld met één of meerdere toestellen. Deze toestellen beschikken over een Next Businessday On Site (=NBOS) garantie. Dit betekent dat, bij problemen, een gecertifieerde technieker van Signpost de werkdag nadien het toestel komt herstellen. Op school of tijdens de schoolvakanties bij de leerling thuis.

De toestellen zijn gebruiksklaar bij levering en voorzien van alle nodige software, gedekt door een verzekering tegen diefstal en bevatten een maximale herstelkost voor defecten buiten garantie.

De laptops worden uitgeleverd op school, op het tijdstip dat Signpost en de school hebben afgesproken. Contacteer uw school bij vragen rond deze levering.

Bestellingen die worden betaald voor 20 augustus 2020 worden uitgeleverd op dat afgesproken tijdstip. Bestellingen na 20 augustus 2020, worden binnen maximaal 15 werkdagen na betaling uitgeleverd op uw school.

U kan er voor kiezen om het toestel te kopen of te huren. De details rond de huur (termijn, bedrag per maand, waarborg, …)  vindt u door op de knop “Huur deze laptop” te klikken onder het gekozen toestel.

Veel succes dit schooljaar.
				</textarea>
				<br><br>

				<label style="font-weight: bold;" for="special-field">Moet er een speciaal veld(en) bijgehouden worden? zo ja : wat? </label>
				<p>(rijksregisternummer of S-nummer of ... ?) </p>
				<input type="text" id="special-field" name="special-field" class="form-control">
				<br><br>

<!-- 				<label style="font-weight: bold;" for="leermiddel-text">Detailopmerkingen op de inleidende tekst voor op de pagina van Leermiddel? </label>
				<textarea class="form-control" id="leermiddel-text" name="leermiddel-text" rows="10">
Zoals je kan lezen op <<naam shop>> wordt aan elke leerling van de school gevraagd over een eigen en zelfde laptop te beschikken. Hier kan je deze huren (via Leermiddel BV).
De school biedt in het kader van dit project het volgende aan:
				</textarea>
				<br><br> -->

				<label style="font-weight: bold;" for="rent-startdate">Start het contract op een ander moment dan 1 september?</label>
				<input type="text" id="rent-startdate" name="rent-startdate" class="form-control">
				<br><br>


				<label style="font-weight: bold;" for="webshop-deadline">Gewenste leverdatum webshop/leermiddel</label>
				<p>Vul in met ASAP, op exacte datum, niet zo dringend </p>
				<input type="text" id="webshop-deadline" name="webshop-deadline" class="form-control">
				<br><br>

				<!--<label style="font-weight: bold;" for="leermiddel-deadline">Gewenste leverdatum leermiddel website </label>
				<input type="text" id="leermiddel-deadline" name="leermiddel-deadline" class="form-control">
				<br><br>-->

				<label style="font-weight: bold;" for="notes">Opmerkingen</label>
				<textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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

//manufacturer - model - motherboard - memory - ssd - panel
$(document).ready(function() {

	$('.SchoolSelect').select2({
		theme: "classic"
	});

	$('.ScholengroepSelect').select2({
		theme: "classic"
	});


	// $('#device1-hoes').on('change', function(){
	// 	var sleve = $(this).val();
	// 	var slevetype = $('#device1-hoestype').val();
	// 	var spsku = $($('#SPSKU1')[0].innerHTML)[0].value;

	// 	if(sleve == "ja" || sleve == "bedrukt"){

	// 		$.ajax({
	// 			type:'POST',
	// 			url:'ajaxSleveData.php',
	// 			data:'spsku='+spsku,
	// 			success:function(html){
	// 				$('#device1-hoestype').html(html);
	// 			}
	// 		});

	// 	} else {
	// 		$('#device1-hoestype').html('<option value="geen">Geen</option>');
	// 	}

	// });

	// $('#device2-hoes').on('change', function(){
	// 	var sleve = $(this).val();
	// 	var slevetype = $('#device2-hoestype').val();
	// 	var spsku = $($('#SPSKU2')[0].innerHTML)[0].value;

	// 	if(sleve == "ja" || sleve == "bedrukt"){

	// 		$.ajax({
	// 			type:'POST',
	// 			url:'ajaxSleveData.php',
	// 			data:'spsku='+spsku,
	// 			success:function(html){
	// 				$('#device2-hoestype').html(html);
	// 			}
	// 		});

	// 	} else {
	// 		$('#device2-hoestype').html('<option value="geen">Geen</option>');
	// 	}

	// });

	// $('#device3-hoes').on('change', function(){
	// 	var sleve = $(this).val();
	// 	var slevetype = $('#device3-hoestype').val();
	// 	var spsku = $($('#SPSKU3')[0].innerHTML)[0].value;

	// 	if(sleve == "ja" || sleve == "bedrukt"){

	// 		$.ajax({
	// 			type:'POST',
	// 			url:'ajaxSleveData.php',
	// 			data:'spsku='+spsku,
	// 			success:function(html){
	// 				$('#device3-hoestype').html(html);
	// 				console.log(html);
	// 			}
	// 		});

	// 	} else {
	// 		$('#device3-hoestype').html('<option value="geen">Geen</option>');
	// 	}

	// });

	// $('#device4-hoes').on('change', function(){
	// 	var sleve = $(this).val();
	// 	var slevetype = $('#device4-hoestype').val();
	// 	var spsku = $($('#SPSKU4')[0].innerHTML)[0].value;

	// 	if(sleve == "ja" || sleve == "bedrukt"){

	// 		$.ajax({
	// 			type:'POST',
	// 			url:'ajaxSleveData.php',
	// 			data:'spsku='+spsku,
	// 			success:function(html){
	// 				$('#device4-hoestype').html(html);
	// 			}
	// 		});

	// 	} else {
	// 		$('#device4-hoestype').html('<option value="geen">Geen</option>');
	// 	}

	// });

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

// ----------------------- 2 -----------------------------------

	$('#manufacturer2').on('change', function(){
		var manufacturer = $(this).val();
		if(manufacturer){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+manufacturer,
				success:function(html){
					$('#model2').html(html);
					$('#motherboard2').html('<option value="" disabled selected></option>');
					$('#memory2').html('<option value="" disabled selected></option>');
					$('#ssd2').html('<option value="" disabled selected></option>');
					$('#panel2').html('<option value="" disabled selected></option>');
					$('#warranty2').html('<option value="" disabled selected></option>');
					$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
					$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
					$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#model2').html('<option value="" disabled selected></option>');
			$('#motherboard2').html('<option value="" disabled selected></option>');
			$('#memory2').html('<option value="" disabled selected></option>');
			$('#ssd2').html('<option value="" disabled selected></option>');
			$('#panel2').html('<option value="" disabled selected></option>');
			$('#warranty2').html('<option value="" disabled selected></option>');
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

	$('#model2').on('change', function(){
		var model = $(this).val();
		if(model){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer2').val()+'&model='+model,
				success:function(html){
					$('#motherboard2').html(html);
					$('#memory2').html('<option value="" disabled selected></option>');
					$('#ssd2').html('<option value="" disabled selected></option>');
					$('#panel2').html('<option value="" disabled selected></option>');
					$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
					$('#warranty2').html('<option value="" disabled selected></option>');
					$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
					$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#motherboard2').html('<option value="" disabled selected></option>');
			$('#memory2').html('<option value="" disabled selected></option>');
			$('#ssd2').html('<option value="" disabled selected></option>');
			$('#panel2').html('<option value="" disabled selected></option>');
			$('#warranty2').html('<option value="" disabled selected></option>');
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

	$('#motherboard2').on('change', function(){
		var motherboard = $(this).val();
		if(motherboard){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer2').val()+'&model='+$('#model2').val()+'&motherboard='+motherboard,
				success:function(html){
					$('#memory2').html(html);
					$('#ssd2').html('<option value="" disabled selected></option>');
					$('#panel2').html('<option value="" disabled selected></option>');
					$('#warranty2').html('<option value="" disabled selected></option>');
					$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
					$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
					$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#memory2').html('<option value="" disabled selected></option>');
			$('#ssd2').html('<option value="" disabled selected></option>');
			$('#panel2').html('<option value="" disabled selected></option>');
			$('#warranty2').html('<option value="" disabled selected></option>');
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

	$('#memory2').on('change', function(){
		var memory = $(this).val();
		if(memory){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer2').val()+'&model='+$('#model2').val()+'&motherboard='+$('#motherboard2').val()+'&memory='+memory,
				success:function(html){
					$('#ssd2').html(html);
					$('#panel2').html('<option value="" disabled selected></option>');
					$('#warranty2').html('<option value="" disabled selected></option>');
					$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
					$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
					$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#ssd2').html('<option value="" disabled selected></option>');
			$('#panel2').html('<option value="" disabled selected></option>');
			$('#warranty2').html('<option value="" disabled selected></option>');
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

	$('#ssd2').on('change', function(){
		var ssd = $(this).val();
		if(ssd){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer2').val()+'&model='+$('#model2').val()+'&motherboard='+$('#motherboard2').val()+'&memory='+$('#memory2').val()+'&ssd='+ssd,
				success:function(html){
					$('#panel2').html(html);
					$('#warranty2').html('<option value="" disabled selected></option>');
					$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
					$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
					$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#panel2').html('<option value="" disabled selected></option>');
			$('#warranty2').html('<option value="" disabled selected></option>');
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

	$('#panel2').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer2').val()+'&model='+$('#model2').val()+'&motherboard='+$('#motherboard2').val()+'&memory='+$('#memory2').val()+'&ssd='+$('#ssd2').val()+'&panel='+$('#panel2').val()+'&nr=2',
				success:function(html){
					$('#warranty2').html(html);
					$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
					$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
					$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#warranty2').html('<option value="" disabled selected></option>');
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

	$('#warranty2').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer2').val()+'&model='+$('#model2').val()+'&motherboard='+$('#motherboard2').val()+'&memory='+$('#memory2').val()+'&ssd='+$('#ssd2').val()+'&panel='+$('#panel2').val()+'&warranty='+$('#warranty2').val()+'&nr=2',
				success:function(html){
					var result = $.parseJSON(html);
					$('#SPSKU2').html(result[0]);
					$('#device2-defaultprice').html(result[1]);
					$('#device2-defaultrepaircost').html(result[2]);
					console.log(result);
				}
			});
		}else{
			$('#SPSKU2').html('<input type="text" class="form-control" name="SPSKU2" value="" readonly required>');
			$('#device2-defaultprice').html('<input type="number" value="" class="form-control" name="device2-defaultprice" disabled>');
			$('#device2-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device2-defaultrepaircost" disabled>');
		}
	});

// ------------------------------ 3 ---------------------------------------

	$('#manufacturer3').on('change', function(){
		var manufacturer = $(this).val();
		if(manufacturer){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+manufacturer,
				success:function(html){
					$('#model3').html(html);
					$('#motherboard3').html('<option value="" disabled selected></option>');
					$('#memory3').html('<option value="" disabled selected></option>');
					$('#ssd3').html('<option value="" disabled selected></option>');
					$('#panel3').html('<option value="" disabled selected></option>');
					$('#warranty3').html('<option value="" disabled selected></option>');
					$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
					$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
					$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#model3').html('<option value="" disabled selected></option>');
			$('#motherboard3').html('<option value="" disabled selected></option>');
			$('#memory3').html('<option value="" disabled selected></option>');
			$('#ssd3').html('<option value="" disabled selected></option>');
			$('#panel3').html('<option value="" disabled selected></option>');
			$('#warranty3').html('<option value="" disabled selected></option>');
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

	$('#model3').on('change', function(){
		var model = $(this).val();
		if(model){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer3').val()+'&model='+model,
				success:function(html){
					$('#motherboard3').html(html);
					$('#memory3').html('<option value="" disabled selected></option>');
					$('#ssd3').html('<option value="" disabled selected></option>');
					$('#panel3').html('<option value="" disabled selected></option>');
					$('#warranty3').html('<option value="" disabled selected></option>');
					$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
					$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
					$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#motherboard3').html('<option value="" disabled selected></option>');
			$('#memory3').html('<option value="" disabled selected></option>');
			$('#ssd3').html('<option value="" disabled selected></option>');
			$('#panel3').html('<option value="" disabled selected></option>');
			$('#warranty3').html('<option value="" disabled selected></option>');
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

	$('#motherboard3').on('change', function(){
		var motherboard = $(this).val();
		if(motherboard){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer3').val()+'&model='+$('#model3').val()+'&motherboard='+motherboard,
				success:function(html){
					$('#memory3').html(html);
					$('#ssd3').html('<option value="" disabled selected></option>');
					$('#panel3').html('<option value="" disabled selected></option>');
					$('#warranty3').html('<option value="" disabled selected></option>');
					$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
					$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
					$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#memory3').html('<option value="" disabled selected></option>');
			$('#ssd3').html('<option value="" disabled selected></option>');
			$('#panel3').html('<option value="" disabled selected></option>');
			$('#warranty3').html('<option value="" disabled selected></option>');
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

	$('#memory3').on('change', function(){
		var memory = $(this).val();
		if(memory){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer3').val()+'&model='+$('#model3').val()+'&motherboard='+$('#motherboard3').val()+'&memory='+memory,
				success:function(html){
					$('#ssd3').html(html);
					$('#panel3').html('<option value="" disabled selected></option>');
					$('#warranty3').html('<option value="" disabled selected></option>');
					$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
					$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
					$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#ssd3').html('<option value="" disabled selected></option>');
			$('#panel3').html('<option value="" disabled selected></option>');
			$('#warranty3').html('<option value="" disabled selected></option>');
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

	$('#ssd3').on('change', function(){
		var ssd = $(this).val();
		if(ssd){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer3').val()+'&model='+$('#model3').val()+'&motherboard='+$('#motherboard3').val()+'&memory='+$('#memory3').val()+'&ssd='+ssd,
				success:function(html){
					$('#panel3').html(html);
					$('#warranty3').html('<option value="" disabled selected></option>');
					$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
					$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
					$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#panel3').html('<option value="" disabled selected></option>');
			$('#warranty3').html('<option value="" disabled selected></option>');
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

	$('#panel3').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer3').val()+'&model='+$('#model3').val()+'&motherboard='+$('#motherboard3').val()+'&memory='+$('#memory3').val()+'&ssd='+$('#ssd3').val()+'&panel='+$('#panel3').val()+'&nr=3',
				success:function(html){
					$('#warranty3').html(html);
					$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
					$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
					$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#warranty3').html('<option value="" disabled selected></option>');
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

	$('#warranty3').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer3').val()+'&model='+$('#model3').val()+'&motherboard='+$('#motherboard3').val()+'&memory='+$('#memory3').val()+'&ssd='+$('#ssd3').val()+'&panel='+$('#panel3').val()+'&warranty='+$('#warranty3').val()+'&nr=3',
				success:function(html){
					var result = $.parseJSON(html);
					$('#SPSKU3').html(result[0]);
					$('#device3-defaultprice').html(result[1]);
					$('#device3-defaultrepaircost').html(result[2]);
				}
			});
		}else{
			$('#SPSKU3').html('<input type="text" class="form-control" name="SPSKU3" value="" readonly required>');
			$('#device3-defaultprice').html('<input type="number" value="" class="form-control" name="device3-defaultprice" disabled>');
			$('#device3-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device3-defaultrepaircost" disabled>');
		}
	});

// ------------------------------------ 4 --------------------------------------------

	$('#manufacturer4').on('change', function(){
		var manufacturer = $(this).val();
		if(manufacturer){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+manufacturer,
				success:function(html){
					$('#model4').html(html);
					$('#motherboard4').html('<option value="" disabled selected></option>');
					$('#memory4').html('<option value="" disabled selected></option>');
					$('#ssd4').html('<option value="" disabled selected></option>');
					$('#panel4').html('<option value="" disabled selected></option>');
					$('#warranty4').html('<option value="" disabled selected></option>');
					$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
					$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
					$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#model4').html('<option value="" disabled selected></option>');
			$('#motherboard4').html('<option value="" disabled selected></option>');
			$('#memory4').html('<option value="" disabled selected></option>');
			$('#ssd4').html('<option value="" disabled selected></option>');
			$('#panel4').html('<option value="" disabled selected></option>');
			$('#warranty4').html('<option value="" disabled selected></option>');
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
		}
	});

	$('#model4').on('change', function(){
		var model = $(this).val();
		if(model){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer4').val()+'&model='+model,
				success:function(html){
					$('#motherboard4').html(html);
					$('#memory4').html('<option value="" disabled selected></option>');
					$('#ssd4').html('<option value="" disabled selected></option>');
					$('#panel4').html('<option value="" disabled selected></option>');
					$('#warranty4').html('<option value="" disabled selected></option>');
					$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
					$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
					$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#motherboard4').html('<option value="" disabled selected></option>');
			$('#memory4').html('<option value="" disabled selected></option>');
			$('#ssd4').html('<option value="" disabled selected></option>');
			$('#panel4').html('<option value="" disabled selected></option>');
			$('#warranty4').html('<option value="" disabled selected></option>');
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
		}
	});

	$('#motherboard4').on('change', function(){
		var motherboard = $(this).val();
		if(motherboard){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer4').val()+'&model='+$('#model4').val()+'&motherboard='+motherboard,
				success:function(html){
					$('#memory4').html(html);
					$('#ssd4').html('<option value="" disabled selected></option>');
					$('#panel4').html('<option value="" disabled selected></option>');
					$('#warranty4').html('<option value="" disabled selected></option>');
					$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
					$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
					$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#memory4').html('<option value="" disabled selected></option>');
			$('#ssd4').html('<option value="" disabled selected></option>');
			$('#panel4').html('<option value="" disabled selected></option>');
			$('#warranty4').html('<option value="" disabled selected></option>');
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
		}
	});

	$('#memory4').on('change', function(){
		var memory = $(this).val();
		if(memory){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer4').val()+'&model='+$('#model4').val()+'&motherboard='+$('#motherboard4').val()+'&memory='+memory,
				success:function(html){
					$('#ssd4').html(html);
					$('#panel4').html('<option value="" disabled selected></option>');
					$('#warranty4').html('<option value="" disabled selected></option>');
					$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
					$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
					$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#ssd4').html('<option value="" disabled selected></option>');
			$('#panel4').html('<option value="" disabled selected></option>');
			$('#warranty4').html('<option value="" disabled selected></option>');
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
		}
	});

	$('#ssd4').on('change', function(){
		var ssd = $(this).val();
		if(ssd){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer4').val()+'&model='+$('#model4').val()+'&motherboard='+$('#motherboard4').val()+'&memory='+$('#memory4').val()+'&ssd='+ssd,
				success:function(html){
					$('#panel4').html(html);
					$('#warranty4').html('<option value="" disabled selected></option>');
					$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
					$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
					$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#panel4').html('<option value="" disabled selected></option>');
			$('#warranty4').html('<option value="" disabled selected></option>');
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
		}
	});

	$('#panel4').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer4').val()+'&model='+$('#model4').val()+'&motherboard='+$('#motherboard4').val()+'&memory='+$('#memory4').val()+'&ssd='+$('#ssd4').val()+'&panel='+$('#panel4').val()+'&nr=4',
				success:function(html){
					$('#warranty4').html(html);
					$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
					$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
					$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
				}
			});
		}else{
			$('#warranty4').html('<option value="" disabled selected></option>');
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
		}
	});

	$('#warranty4').on('change', function(){
		var panel = $(this).val();
		if(panel){
			$.ajax({
				type:'POST',
				url:'ajaxComputerData.php',
				data:'manufacturer='+$('#manufacturer4').val()+'&model='+$('#model4').val()+'&motherboard='+$('#motherboard4').val()+'&memory='+$('#memory4').val()+'&ssd='+$('#ssd4').val()+'&panel='+$('#panel4').val()+'&warranty='+$('#warranty4').val()+'&nr=4',
				success:function(html){
					var result = $.parseJSON(html);
					$('#SPSKU4').html(result[0]);
					$('#device4-defaultprice').html(result[1]);
					$('#device4-defaultrepaircost').html(result[2]);
				}
			});
		}else{
			$('#SPSKU4').html('<input type="text" class="form-control" name="SPSKU4" value="" readonly required>');
			$('#device4-defaultprice').html('<input type="number" value="" class="form-control" name="device4-defaultprice" disabled>');
			$('#device4-defaultrepaircost').html('<input type="number" value="" class="form-control" name="device4-defaultrepaircost" disabled>');
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
