<?php
// Include the database config file

include_once 'conn.php';
include_once 'mssql-conn.php';

if(isset($_POST["synergyid"]) !== false){

	$query = 'SELECT * FROM forecasts WHERE synergyid = "' . $_POST['synergyid'] . '" AND deleted != 1 LIMIT 1';
	$result = $conn->query($query);

	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){

			if ($_POST['type'] == 'forecast') {
				//echo '<label style="font-weight: bold;">Adres</label><br><p>'.$row['street'].' '.$row['house_number'].' - '.$row['postcode'].' '.$row['city'].'</p><br><br>';
			} else {
				echo '
				<div class="row">
					<div class="col-4">
						<label for="shipping_postcode">Postcode</label>
						<input type="text" class="form-control" name="shipping_postcode" value="'.$row['shipping_postcode'].'" required>
					</div>

					<div class="col-8">
						<label for="shipping_city">Gemeente</label>
						<input type="text" class="form-control" name="shipping_city" value="'.$row['shipping_city'].'" required>
					</div>
				</div>
				<br><br>

				<div class="row">
					<div class="col-8">
						<label for="shipping_street">straat</label>
						<input type="text" class="form-control" name="shipping_street" value="'.$row['shipping_street'].'" required>
					</div>

					<div class="col-4">
						<label for="shipping_number">Huisnummer</label>
						<input type="text" class="form-control" name="shipping_number" value="" required>
					</div>
				</div>
				';
			}
		}
	}else{
		if ($_POST['type'] == 'forecast') {
			//echo '<label style="font-weight: bold;">Adres</label><br><p>'.$row['postcode'].' '.$row['city'].' - '.$row['street'].' '.$row['house_number'].'</p><br><br>';
		} else {
			echo '
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
			';
		}
	}

} else if(isset($_POST["synergygroupid"]) !== false){

	if ($_POST["synergygroupid"] == "") {

		$tsql= "Select top (10000) cmp_code as synergyid, cmp_name as schoolnaam, cmp_fadd1 as straat, cmp_fpc as postcode, cmp_fcity as gemeente from cicmpy where cicmpy.cmp_parent is not null";
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

	} else {

		//Read Query
		$tsql= "SELECT cmp_code as synergyid, cmp_name as schoolnaam, cmp_fadd1 as straat, cmp_fpc as postcode, cmp_fcity as gemeente from cicmpy where cicmpy.cmp_parent = '" . $_POST["synergygroupid"] . "'";
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

	}

} else if(isset($_POST["verkoopkansid"]) !== false){

	//Read Query
	$tsql= "select trim(cmp_code) as klantnummer, Description, cmp_name as school, cmp_fadd1 as street, cmp_fpc as postcode, cmp_fcity as city, * from Opportunities inner join cicmpy on cicmpy.cmp_wwn = Opportunities.AccountID where code='" . $_POST["verkoopkansid"] . "'";
	$getResults= sqlsrv_query($msconn, $tsql);
	$results = "";

	if ($getResults == FALSE){
		die(FormatErrors(sqlsrv_errors()));
	}

	while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {

		$delivery = '';
		if(isset($_POST["type"]) == false){
			$results .= '
			<div class="row">
				<div class="col-3">
					<label for="synergyid">Synergy ID</label>
					<input type="text" class="form-control" name="synergyid" value="'.$row['klantnummer'].'" required readonly>
				</div>

				<div class="col-9">
					<label for="school">School</label>
					<input type="text" class="form-control" name="school" value="'.$row['school'].'" required readonly>
				</div>
			</div>
			<br><br>

			<div class="row">
				<div class="col">
					<label for="description">Beschrijving</label>
					<input type="text" class="form-control" name="description" value="'.$row['Description'].'" required readonly>
				</div>
			</div>
			<br><br>';
		} else {
			$delivery = 'delivery_';
		}

		$results .= '<div class="row">
			<div class="col-3">
				<label for="shipping_postcode">Postcode</label>
				<input type="text" class="form-control" name="' . $delivery . 'shipping_postcode" value="'.$row['postcode'].'" required readonly>
			</div>

			<div class="col-3">
				<label for="shipping_city">Gemeente</label>
				<input type="text" class="form-control" name="' . $delivery . 'shipping_city" value="'.$row['city'].'" required readonly>
			</div>

			<div class="col-6">
				<label for="street">Straat + Huisnummer</label>
				<input type="text" class="form-control" name="' . $delivery . 'shipping_street" value="'.$row['street'].'" required readonly>
			</div>
		</div>
		<br>
		';
	}

	if($results == ""){
		echo "Dit is geen geldige verkoopkans!";
	} else {
		echo $results;
	}

	sqlsrv_free_stmt($getResults);

	echo "<br>";

}
?>
