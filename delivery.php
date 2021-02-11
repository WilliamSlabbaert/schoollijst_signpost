<?php

$title = 'Leveringen';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

require 'vendor/autoload.php';
ini_set('SMTP', 'localhost');
ini_set('smtp_port', 25);

?>

<div class="body">

<?php
if (isset($_GET['printsuborder']) !== false) {

	$sql = "UPDATE delivery SET printed_on = '" . date('d-m-Y H:i:s') . "', printed_by = '" . $loginname . "' WHERE id = '" . $_GET['suborderid'] . "'";
	if ($conn->query($sql) === TRUE) {
		$URL = 'delivery.php?delivery_number=' . $_GET['delivery_number'] . '&orderid=' . $_GET['orderid'] . '&print';
		if( headers_sent() ) { echo("<script>location.href='$URL'</script>"); }
		else { header("Location: $URL"); }
		exit;
	}

} elseif (isset($_GET['new']) !== false) {

	$sql = "SELECT id, synergyid, magento_type, spsku, amount,
		( SELECT productnumber FROM devices WHERE spsku = orders.spsku ) as artcode,
		( SELECT sum(amount) FROM delivery WHERE orderid = orders.id ) as devicesInSuborders
		FROM orders WHERE id = '" . $_GET["orderid"] . "' AND orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$synergyid = $row["synergyid"];
			$spsku = $row["spsku"];
			$magentoType = $row["magento_type"];
			$artcode = $row["artcode"];
			$amountLeft = $row["amount"] - $row["devicesInSuborders"];
			$spsku = explode("-", $spsku)[0] . '-' . explode("-", $spsku)[1];
		}
	} else {
		echo "Error. Er is een probleem opgetreden.";
		die();
	}

	echo '
	<h1>Nieuwe sublevering</h1>
	<form action="delivery.php" method="post">
		<input type="text" id="new" name="new" value="true" hidden class="form-control">
		<input type="text" id="synergyid" name="synergyid" value="' . $synergyid . '" hidden class="form-control">
		<input type="text" id="spsku" name="spsku" value="' . $spsku . '" hidden class="form-control">
		<input type="text" id="delivery_number" name="delivery_number" value="' . $_GET['delivery_number'] . '" hidden class="form-control">
		<label for="order">Order:</label><br>
		<input type="text" id="orderid" name="orderid" value="' . $_GET['orderid'] . '" readonly class="form-control"><br>
		<input type="text" id="amountLeft" name="amountLeft" value="' . $amountLeft . '" readonly hidden class="form-control">

		<label for="type">Type:</label><br>';
	if($_GET['type'] == 'School'){
		echo '<select name="type" id="type" class="form-control">
			<option value="" selected disabled></option>
			<option value="S">School (S)</option>
			<option value="L">Lease (L)</option>
			</select><br>';
	} elseif($_GET['type'] == 'School-Leasing'){
		echo '<select name="type" id="type" class="form-control">
			<option value="" selected disabled></option>
			<option value="L">Lease (L)</option>
			</select><br>';
	} elseif($_GET['type'] == 'School-Leermiddel'){
		echo '<select name="type" id="type" class="form-control">
			<option value="" selected disabled></option>
			<option value="SH">School Leermiddel (SH)</option>
			</select><br>';
	} elseif($_GET['type'] == 'School-Directe betaling'){
		echo '<select name="type" id="type" class="form-control">
			<option value="" selected disabled></option>
			<option value="S">School (S)</option>
			</select><br>';
	} else {
		echo '<select name="type" id="type" class="form-control" required>
			<option value="" selected disabled></option>
			<option value="W">Webshop (W)</option>
			<option value="H">Leermiddel (H)</option>
			<option value="R">Reservetoestel (R)</option>
			</select><br>';
	}

	$labelsandserials = '';
	$sql = "SELECT GROUP_CONCAT(CONCAT(signpost_label, '__', serialnumber)) AS labelsandserials,
		IFNULL(( SELECT sum(amount) FROM delivery WHERE orderid = labels.orderid ), '0') as amountsubordered
		FROM labels WHERE orderid = '" . $_GET['orderid'] . "'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$labelsandserials = $row['labelsandserials'];
			$amountsubordered = $row['amountsubordered'];
		}
	} else {
		echo "0 results";
	}

	$labelsandserials = explode(',', $labelsandserials);
	$labelsandserials = array_slice($labelsandserials, $amountsubordered, count($labelsandserials));

	if($magentoType == 1){
		$tsql= "SELECT refer, orddat, textfield11 as refer1, refer2, artcode, freefield1, lengte
			FROM orkrg with (nolock)
			INNER JOIN orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
			INNER JOIN cicmpy with (nolock) on cicmpy.debnr=orkrg.debnr
			WHERE freefield1='" . $synergyid ."' and artcode = '" . $artcode . "' and ord_soort='V' AND lengte=0
			order by orkrg.refer";
	} else {
		$tsql= "SELECT refer, orddat, refer1, refer2, artcode, freefield1, lengte
			FROM orkrg with (nolock)
			INNER JOIN orsrg with (nolock) ON orkrg.ordernr=orsrg.ordernr
			INNER JOIN cicmpy with (nolock) ON cicmpy.debnr=orkrg.debnr
			WHERE freefield1='" . $synergyid ."' AND artcode LIKE '" . $spsku . "%' AND lengte=0
			ORDER BY orddat";
	}

	$getResults = sqlsrv_query($msconn, $tsql);
	echo '<p style="display:none;">' . $tsql . '</p>';
	$results = '<ol>';
	$formattedResults = array();
	$i = 0;

	if ($getResults == FALSE){
		die( print_r( sqlsrv_errors(), true));
	}

	while ($sqlresults = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
		$i++;
		$results .= '<li><b>' . explode(' ', $sqlresults['refer'])[0] . '</b> (' . $sqlresults['orddat']->format('d-m-Y') . ') - ' . $sqlresults['refer1'] . ' ' . $sqlresults['refer2'] . '</li>';
		array_push($formattedResults, explode(' ', $sqlresults['refer'])[0]);
	}

	$j = 0;
	$input = '';
	$webshopFormat = '';
	foreach($labelsandserials as $les){
		if(isset($formattedResults[$j]) == true){
			$input = $formattedResults[$j] . '__' . $les;
			$webshopFormat = $webshopFormat . $input . ';';
			$j++;
		}
	}

	echo '<div id="webshop" style="display:none;">';
	if($i == 0){
		echo '<span style="color:red;">Er zijn geen niet-geleverde webshop orders gevonden met SPSKU: <b>' . $spsku . '</b>.<br>Controleer of de SKU correct is.<br><br>';
	} else {
		echo '<input type="text" id="webshopnumbers" name="webshopnumbers" value="' . $webshopFormat . '" hidden class="form-control">';
		echo "Er zijn " . $i . " niet geleverde orders gevonden.<br>";
		echo $results . '</ol>';
	}
	echo '</div>';

	sqlsrv_free_stmt($getResults);

	echo '<div id="leermiddel" style="display:none;">';
	$sql2 = "SELECT * FROM leermiddel.tblcontractdetails AS a
		LEFT JOIN leermiddel.tbltoestelcontractdefinitie AS b ON b.id = a.toestelcontractdefinitieid
		LEFT JOIN leermiddel.tblschool AS c ON c.id = a.schoolid
		WHERE c.synergyschoolid = '" . $synergyid . "' AND b.sku LIKE '" . $spsku . "'
		AND ContractOntvangen = '1' AND deleted = '0' AND VoorschotOntvangen IN ('1', '-1') AND lengte = '0' AND ContractVolgnummer LIKE '%2021___'";
	$result2 = $conn->query($sql2);
	echo '<p style="display:none;">' . $sql2 . '</p>';
	$results = '<ol>';
	$formattedResults = array();
	$i = 0;

	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {
			$i++;
			$results .= '<li>' . $row2['ContractVolgnummer'] . ' (' . $row2['DatumContractopgemaakt'] . ') - ' . $row2['VoornaamLeerling'] . ' ' . $row2['NaamLeerling'] . '</li>';
			array_push($formattedResults, $row2['ContractVolgnummer']);
		}
	}

	$j = 0;
	$input = '';
	$leermiddelFormat = '';
	foreach($labelsandserials as $les){
		if(isset($formattedResults[$j]) == true){
			$input = $formattedResults[$j] . '__' . $les;
			$leermiddelFormat = $leermiddelFormat . $input . ';';
			$j++;
		}
	}

	if($i == 0){
		echo '<span style="color:red;">Er zijn geen niet-geleverde leermiddel orders gevonden met SPSKU: <b>' . $spsku . '</b>.<br>
			Controleer of deze SKU correct is.</span><br><br>';
	} else {
		echo '<input type="text" id="leermiddelnumbers" name="leermiddelnumbers" value="' . $leermiddelFormat . '" hidden class="form-control">';
		echo "Er zijn " . $i . " niet geleverde orders gevonden.<br>";
		echo $results . '</ol>';
	}
	echo '</div>';

	echo '<div id="other" style="display:none;">';
	echo 'Geen reserve gegevens<br><br>';
	echo '</div>';

	echo '	<label for="amount">Aantal:<br><span class="smalltext">
		Je kan niet meer orderen dan ' . $amountLeft . ' toestellen.</span></label>
		<input type="number" id="amount" name="amount" min="0" max="0" value="" class="form-control" required><br>
		<input type="submit" value="Levering Aanmaken" class="btn btn-primary">
		</form>';

} elseif (isset($_POST['new']) !== false) {

	$sql = "INSERT INTO delivery (orderid, delivery_number, type, amount, delivered_by, accepted_by, accepted_email, signature)
		VALUES ('" . $_POST['orderid'] . "', '" . $_POST['delivery_number'] . "', '" . $_POST['type'] . "', '" . $_POST['amount'] . "', '', '', '', '')";
	$magentoOrderNumbers = '';
	$leermiddelOrderNumbers = '';

	$delivery_number_number = $_POST['delivery_number'];
	if(substr($delivery_number_number, -1) == '0'){
		$delivery_number_number = '0' . $delivery_number_number;
	}
	if(substr($delivery_number_number, -2) == '0'){
		$delivery_number_number = '0' . $delivery_number_number;
	}

	if($_POST['type'] == 'W'){
		$magentoOrderNumbers = explode(';', $_POST['webshopnumbers']);
	} elseif($_POST['type'] == 'H'){
		$leermiddelOrderNumbers = explode(';', $_POST['leermiddelnumbers']);
	} else {
		// Geen ordersnummers nodig.
	}

	$i = 0;
	if($magentoOrderNumbers != ''){
		foreach($magentoOrderNumbers as $order){
			if($i < $_POST['amount'] && $order != ''){
				$order = explode('__', $order);
				$i++;

				$tsql2= "SELECT orsrg.id as orsrgid
					FROM orkrg with (nolock)
					INNER JOIN orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
					INNER JOIN cicmpy with (nolock) on cicmpy.debnr=orkrg.debnr
					WHERE freefield1='" . $_POST['synergyid'] . "' AND artcode='" . $_POST['spsku'] . "' AND refer = '" . $order[0] . "' AND lengte = '0'";
				$stmt2 = sqlsrv_query( $msconn, $tsql2);
				if($stmt2 === false) {
					die( print_r( sqlsrv_errors(), true) );
				}
				while( $row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC) ) {

					$tsql= "UPDATE
						orsrg
						SET
						lengte = '" . $_POST['orderid'] . "." . $delivery_number_number . "',
						instruction = '" . $order[1] . "'
						WHERE
						id='" . $row2['orsrgid'] . "'";

					$updateResults= sqlsrv_query($msconn, $tsql);

					if ($updateResults == FALSE){
						die( print_r( sqlsrv_errors(), true));
					}
				}

				sqlsrv_free_stmt($updateResults);
			}
		}
	} elseif($leermiddelOrderNumbers != ''){
		foreach($leermiddelOrderNumbers as $order){
			if($i < $_POST['amount'] && $order != ''){
				$order = explode('__', $order);
				$i++;
				$sql2= "UPDATE
					leermiddel.tblcontractdetails
					SET
					lengte = '" . $_POST['orderid'] . "." . $delivery_number_number . "',
					instruction = '" . $order[1] . "'
					WHERE
					contractvolgnummer='" . $order[0] . "' AND lengte = 0";

				if ($conn->query($sql2) === TRUE) {
					//echo "Record updated successfully";
				} else {
					echo "Error updating record: " . $conn->error;
				}

			}
		}
	}

	if ($conn->query($sql) === TRUE) {
		$message = 'Beste,<br>
			Order SP-BYOD20-' . $_POST['orderid'] . ': ' . $_POST['amount'] . ' stuks<br>
			Signpost referentie: SP-BYOD20-' . $_POST['orderid'] . '-' . $_POST['delivery_type'] . '' . $_POST['delivery_number'] . '<br>
			<a href="'.hasAccessForUrl('delivery.php?delivery_number=' . $_POST['delivery_number'] . '&orderid=' . $_POST['orderid'], false).'">Klik hier om het suborder te openen</a><br>';
		$email = new \SendGrid\Mail\Mail();
		$email->setFrom("byod@signpost.eu", "Signpost BYOD");
		$subject = "Details van levering SP-BYOD20-" . $_POST['orderid'] . "-" . $_POST['delivery_type'] . "" . $_POST['delivery_number'] . "";
		$email->setSubject($subject);
		$email->addTo('nathalie@signpost.eu');
		$email->addContent(
			"text/html", $message
		);
		$sendgrid = new \SendGrid('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');
		try {
			$response = $sendgrid->send($email);
		} catch (Exception $e) {
			echo 'Caught exception: '. $e->getMessage() ."\n";
		}
		echo $i . " nieuwe webshoporders en levering succesvol geregistreerd.<br>";
		echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_POST['orderid'] . '', false).'">Ga terug naar levering van order</a>';
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

} elseif (isset($_POST['signature']) !== false) {

	$sql = "SELECT * FROM delivery WHERE orderid = '" . $_POST["orderid"]. "' AND delivery_number = '" . $_POST["delivery_number"]. "'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$img = $_POST['signature'];
			$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img));
			$sql = "UPDATE delivery SET delivered_by='" . $_POST['delivered_by'] . "', accepted_by='" . $_POST['accepted_by'] . "', accepted_email='" . $_POST['accepted_email'] . "', signature='" . $_POST['signature'] . "', resttype='" . $_POST['resttype'] . "', comments='" . $_POST['comments'] . "', delivered_on = now()
				WHERE orderid='" . $_POST['orderid'] . "' AND delivery_number='" . $_POST['delivery_number'] . "'";

			if ($conn->query($sql) === TRUE) {
				$sql2 = "UPDATE orders SET status='uitgeleverd' WHERE id='" . $_POST['orderid'] . "'";

				if ($conn->query($sql2) === TRUE) {
					echo "Handtekening succesvol geregistreerd en order aangepast naar 'Uitgeleverd'";
				} else {
					echo "Error updating record: " . $conn->error;
				}
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
			echo '<img src="' . $img . '">';

		}
	} else {

		$img = $_POST['signature'];
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img));
		$sql = "INSERT INTO delivery (orderid, delivery_number, amount, delivered_by, accepted_by, accepted_email, signature, delivered_on)
			VALUES ('" . $_POST['orderid'] . "', '" . $_POST['delivery_number'] . "', '" . $_POST['amount'] . "', '" . $_POST['delivered_by'] . "', '" . $_POST['accepted_by'] . "', '" . $_POST['accepted_email'] . "', '" . $_POST['signature'] . "', now())";

		if ($conn->query($sql) === TRUE) {
			$sql2 = "UPDATE orders SET status='uitgeleverd' WHERE id='" . $_POST['orderid'] . "'";

			if ($conn->query($sql2) === TRUE) {
				echo "Handtekening succesvol geregistreerd en order aangepast naar 'Uitgeleverd'";
			} else {
				echo "Error updating record: " . $conn->error;
			}
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		echo '<img src="' . $img . '">';

	}

	$delivery_number_number = $_POST['delivery_number'];
	if(substr($delivery_number_number, -1) == '0'){
		$delivery_number_number = '0' . $delivery_number_number;
	}
	if(substr($delivery_number_number, -2) == '0'){
		$delivery_number_number = '0' . $delivery_number_number;
	}

	if($_POST['delivery_type'] == 'W'){

		$tsql2= "SELECT orsrg.id as orsrgid
			FROM orkrg with (nolock)
			INNER JOIN orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
			WHERE lengte = '" . $_POST['orderid'] . "." . $delivery_number_number . "'";
		$stmt2 = sqlsrv_query( $msconn, $tsql2);
		if($stmt2 === false) {
			die( print_r( sqlsrv_errors(), true) );
		}
		while( $row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC) ) {
			$tsql= "UPDATE
				orsrg
				SET
				breedte = '" . $_POST['orderid'] . "." . $delivery_number_number . "'
				WHERE
				id = '" . $row2['orsrgid'] . "'";

			$updateResults= sqlsrv_query($msconn, $tsql);

			if ($updateResults == FALSE){
				die( print_r( sqlsrv_errors(), true));
			}
		}

		sqlsrv_free_stmt($updateResults);

	}

	if($_POST['delivery_type'] == 'H'){
		$sql = "UPDATE leermiddel.tbltoestelcontractdefinitie
			SET breedte='" . $_POST['orderid'] . "." . $delivery_number_number . "'
			WHERE lengte='" . $_POST['orderid'] . "." . $delivery_number_number . "'";

		if ($conn->query($sql) === TRUE) {
			//echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $conn->error;
		}

		$conn->close();
	}

	$message = $_POST['orderSummary'];
	$email = new \SendGrid\Mail\Mail();
	$email->setFrom("byod@signpost.eu", "Signpost BYOD");
	$subject = "Details van levering SP-BYOD20-" . $_POST['orderid'] . "-" . $_POST['delivery_type'] . "" . $_POST['delivery_number'] . " op " . date('d-m-Y') . "";
	$email->setSubject($subject);
	$email->addTo($_POST['accepted_email']);
	$email->addTo('byod@signpost.eu');
	$email->addContent(
		"text/html", $message
	);
	$sendgrid = new \SendGrid('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');
	try {
		$response = $sendgrid->send($email);
	} catch (Exception $e) {
		echo 'Caught exception: '. $e->getMessage() ."\n";
	}

} elseif (isset($_GET['delivery_number']) !== false) {

	if(isset($_GET['print']) == true){
		echo '
		<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="100px" viewBox="0 0 536.5 131.2" style="enable-background:new 0 0 536.5 131.2;" xml:space="preserve">
			<style type="text/css">
				.st2{fill:#00ADBD;}
				.st3{fill:#000000;}
			</style>
			<defs>
			</defs>
			<g>
				<path class="st2" d="M288.4,108.5c0.2,1.7,0.1,4.5,0,6.2c-0.4,5-3.8,8.6-9.3,8.6c-1.7,0-3.6-0.4-4.7-1v5.9c0,2.9,0,2.9-2.9,2.9
				c-2.9,0-2.9,0-2.9-2.9v-24.1c0-0.6,0.2-1.1,0.7-1.4c1.4-1,4.7-2.7,9.1-2.7C283.9,100,287.9,103.5,288.4,108.5z M274.3,106.7v10.1
				c0.7,0.4,2.4,1,4.1,1c2.6,0,4-1.5,4.2-4c0.1-1.5,0.1-3,0-4.3c-0.2-2.4-1.7-4.1-4.4-4.1C276.9,105.4,275.1,106.1,274.3,106.7z"/>
				<path class="st0" d="M307.4,102c0,0.3-0.1,0.7-0.5,2.1c-0.5,1.5-0.7,2-1.5,2c-0.3,0-0.8-0.1-1.5-0.3c-0.5-0.1-1.2-0.3-2-0.3
				c-1.6,0-2.4,0.8-2.4,2.7v12c0,2.7,0,2.7-2.9,2.7c-2.9,0-2.9,0-2.9-2.7v-13c0-4.4,2.6-7.2,7.4-7.2c1.4,0,3,0.2,4.3,0.6
				C306.7,100.8,307.4,101.2,307.4,102z"/>
				<path class="st0" d="M330.2,108.6c0.2,1.7,0.2,4.3,0,6c-0.5,5-4.5,8.7-10.1,8.7c-5.5,0-9.6-3.7-10.1-8.7c-0.2-1.7-0.2-4.2,0-5.9
				c0.5-5.1,4.5-8.7,10.1-8.7C325.6,100,329.7,103.6,330.2,108.6z M315.6,109.5c-0.1,1.4-0.1,2.8,0,4.2c0.2,2.4,1.8,4.1,4.4,4.1
				s4.2-1.6,4.4-4.1c0.1-1.4,0.1-2.8,0-4.2c-0.2-2.4-1.8-4.1-4.4-4.1S315.9,107.1,315.6,109.5z"/>
				<path class="st0" d="M343.7,100c1.7,0,3.6,0.4,4.9,1v-5.2c0-3,0-3,2.9-3c2.9,0,2.9,0,2.9,3v22.9c0,0.9-0.3,1.5-1,2
				c-1.2,0.9-4.5,2.6-8.9,2.6c-5.8,0-9.5-3.7-10-8.8c-0.1-1.5-0.2-4.5,0-6C334.9,103.3,338.1,100,343.7,100z M344.6,117.7
				c1.5,0,3.3-0.7,4-1.3v-9.9c-0.7-0.5-2.5-1.1-4.3-1.1c-2.6,0-3.9,1.4-4,3.9c-0.1,1-0.1,3.1,0,4.2
				C340.4,116.3,341.9,117.7,344.6,117.7z"/>
				<path class="st0" d="M362.9,100.4c2.9,0,2.9,0,2.9,3v10.5c0,2.5,1.6,3.8,4,3.8c1.8,0,3.2-0.5,4-1.1v-13.3c0-3,0-3,2.9-3
				c2.9,0,2.9,0,2.9,3v15.6c0,1.3,0,1.3-1,2c-1.3,0.9-4.4,2.4-8.9,2.4c-6.1,0-9.6-3.3-9.6-9.2v-10.7
				C360.1,100.4,360.1,100.4,362.9,100.4z"/>
				<path class="st0" d="M404,105.1c0.3,0.8,0.4,1.4,0.4,1.9c0,0.7-0.5,0.8-2,1.3c-1.2,0.4-1.9,0.7-2.3,0.7c-0.5,0-0.6-0.5-1-1.2
				c-0.9-1.7-2.3-2.4-4.1-2.4c-2.7,0-4.2,1.6-4.5,4c-0.1,1.1-0.1,3.1,0,4.2c0.3,2.6,1.8,4.2,4.5,4.2c1.9,0,3.2-0.8,4.1-2.4
				c0.3-0.6,0.5-1.2,1.1-1.2c0.4,0,0.7,0.1,2.2,0.6c1.8,0.7,2.2,0.8,2.2,1.4c0,0.4-0.2,1.2-0.6,2c-0.9,1.9-3.3,5.1-9.1,5.1
				c-5.5,0-9.6-3.9-10-8.8c-0.1-1.7-0.1-4.2,0-5.9c0.3-5,4.5-8.6,10.1-8.6C400.4,100,403.3,103.1,404,105.1z"/>
				<path class="st0" d="M416.4,123.3c-5.2,0-7.6-3.6-7.6-8.3V97.8c0-3,0-3,2.8-3c2.9,0,2.9,0,2.9,3v2.6h5c2.7,0,2.7,0.1,2.7,2.7
				s0,2.6-2.7,2.6h-5v9.3c0,2,0.9,3.1,2.8,3.1c0.9,0,1.8-0.2,2.6-0.4c0.6-0.2,1.1-0.3,1.5-0.3c0.7,0,0.9,0.5,1.4,1.9s0.5,1.7,0.5,1.9
				c0,0.5-0.4,0.8-1.3,1.2C420.2,123,418.4,123.3,416.4,123.3z"/>
				<path class="st0" d="M430.3,97.9c-3,0-3,0-3-3.1c0-3,0-3,3-3c3,0,3,0,3,3C433.3,97.9,433.3,97.9,430.3,97.9z M433.2,103.3v16.9
				c0,2.8,0,2.8-2.9,2.8c-2.8,0-2.8,0-2.8-2.8v-16.9c0-2.9,0-2.9,2.8-2.9C433.2,100.4,433.2,100.4,433.2,103.3z"/>
				<path class="st0" d="M455.4,113.7h-11.3v0.4c0,2.7,2.1,4.1,4.9,4.1c1.6,0,3.1-0.3,4.3-0.8c0.7-0.3,1.3-0.6,1.7-0.6
				c0.6,0,0.9,0.4,1.5,1.9c0.5,1,0.6,1.5,0.6,1.8c0,0.4-0.3,0.8-1.5,1.3c-1.5,0.7-4,1.4-6.7,1.4c-6.1,0-9.9-3.7-10.4-9
				c-0.1-1.6-0.1-4.1,0-5.5c0.5-5.5,4.4-8.9,9.9-8.9c5.8,0,9.7,3.8,9.7,9.7v1.1C458.2,113.4,457.8,113.7,455.4,113.7z M453,109.3
				c0.1-2.8-1.6-4.6-4.4-4.6c-3.2,0-4.6,2-4.6,5.1h8.5C453,109.8,453,109.8,453,109.3z"/>
				<path class="st0" d="M470.6,123.3c-5.2,0-7.6-3.6-7.6-8.3V97.8c0-3,0-3,2.8-3c2.9,0,2.9,0,2.9,3v2.6h5c2.7,0,2.7,0.1,2.7,2.7
				s0,2.6-2.7,2.6h-5v9.3c0,2,0.9,3.1,2.8,3.1c0.9,0,1.8-0.2,2.6-0.4c0.6-0.2,1.1-0.3,1.5-0.3c0.7,0,0.9,0.5,1.4,1.9s0.5,1.7,0.5,1.9
				c0,0.5-0.4,0.8-1.3,1.2C474.4,123,472.6,123.3,470.6,123.3z"/>
				<path class="st0" d="M500.8,108.6c0.2,1.7,0.2,4.3,0,6c-0.5,5-4.5,8.7-10.1,8.7c-5.5,0-9.6-3.7-10.1-8.7c-0.2-1.7-0.2-4.2,0-5.9
				c0.5-5.1,4.5-8.7,10.1-8.7C496.3,100,500.4,103.6,500.8,108.6z M486.3,109.5c-0.1,1.4-0.1,2.8,0,4.2c0.2,2.4,1.8,4.1,4.4,4.1
				s4.2-1.6,4.4-4.1c0.1-1.4,0.1-2.8,0-4.2c-0.2-2.4-1.8-4.1-4.4-4.1S486.6,107.1,486.3,109.5z"/>
				<path class="st0" d="M525.4,108.6c0.2,1.7,0.2,4.3,0,6c-0.5,5-4.5,8.7-10.1,8.7c-5.5,0-9.6-3.7-10.1-8.7c-0.2-1.7-0.2-4.2,0-5.9
				c0.5-5.1,4.5-8.7,10.1-8.7C520.8,100,524.9,103.6,525.4,108.6z M510.9,109.5c-0.1,1.4-0.1,2.8,0,4.2c0.2,2.4,1.8,4.1,4.4,4.1
				s4.2-1.6,4.4-4.1c0.1-1.4,0.1-2.8,0-4.2c-0.2-2.4-1.8-4.1-4.4-4.1S511.1,107.1,510.9,109.5z"/>
				<path class="st0" d="M536.5,95.8v24.4c0,2.8,0,2.8-2.9,2.8c-2.9,0-2.9,0-2.9-2.8V95.8c0-3,0-3,2.9-3
				C536.5,92.8,536.5,92.8,536.5,95.8z"/>
			</g>
			<path class="st3" d="M84.8,0L71.6,15.5c0,0-38,0-41.1,0c-2.5,0-4.5,0.5-5.8,1.6c-1.3,1.1-1.9,2.4-1.9,4c0,1.1,0.5,2.1,1.6,3
			c1,0.9,3.5,1.8,7.3,2.6c9.6,2.1,16.4,4.1,20.5,6.3c4.1,2.1,7.2,4.7,9,7.9c1.9,3.1,2.8,6.6,2.8,10.5c0,4.5-1.3,8.7-3.8,12.6
			c-2.5,3.8-6,6.7-10.5,8.7c-4.5,2-10.2,3-17,3c-12,0-20.4-2.3-25-7C3.2,64,0.6,58.1,0,50.9l20.8-1.3c0.4,3.4,1.4,6,2.8,7.7
			c2.2,2.9,5.5,4.3,9.7,4.3c3.1,0,5.5-0.7,7.2-2.2c1.7-1.5,2.5-3.2,2.5-5.1c0-1.8-0.8-3.5-2.4-4.9c-1.6-1.5-5.4-2.8-11.2-4.1
			c-9.6-2.2-16.4-5-20.5-8.6C4.7,33.1,2.6,28.6,2.6,23c0-3.7,1.1-7.1,3.2-10.3c2.1-3.2,5.5-7.2,9.8-9C19.8,1.8,25.3,0,32.6,0L84.8,0z"
			/>
			<rect x="72.1" y="15.6" class="st3" width="19.6" height="58.7"/>
			<path class="st3" d="M137.5,23h18.4v48.5l0.1,2.3c0,3.2-0.7,6.3-2.1,9.2c-1.4,2.9-3.2,5.3-5.5,7.1c-2.3,1.8-5.2,3.1-8.7,3.9
			c-3.5,0.8-7.5,1.2-12,1.2c-10.3,0-17.4-1.5-21.2-4.6c-3.9-3.1-5.8-7.2-5.8-12.4c0-0.6,0-1.5,0.1-2.6l19.1,2.2c0.5,1.8,1.2,3,2.2,3.7
			c1.4,1,3.3,1.5,5.4,1.5c2.8,0,4.9-0.7,6.3-2.3c1.4-1.5,2.1-4.2,2.1-7.9v-7.8c-1.9,2.3-3.9,4-5.8,5c-3,1.6-6.3,2.4-9.8,2.4
			c-6.9,0-12.4-3-16.6-9c-3-4.3-4.5-9.9-4.5-16.9c0-8,1.9-14.1,5.8-18.3c3.9-4.2,8.9-6.3,15.2-6.3c4,0,7.3,0.7,9.9,2
			c2.6,1.3,5,3.6,7.3,6.7V23z M119,47.9c0,3.7,0.8,6.5,2.4,8.2c1.6,1.8,3.6,2.7,6.2,2.7c2.4,0,4.5-0.9,6.1-2.8
			c1.7-1.9,2.5-4.6,2.5-8.4c0-3.7-0.9-6.6-2.6-8.6c-1.7-2-3.8-3-6.4-3c-2.5,0-4.5,0.9-6,2.7C119.7,40.6,119,43.7,119,47.9"/>
			<path class="st3" d="M165.2,23h18.3v8.4c2.7-3.4,5.5-5.9,8.3-7.3c2.8-1.5,6.2-2.2,10.3-2.2c5.4,0,9.7,1.6,12.8,4.9
			c3.1,3.3,4.6,8.2,4.6,15v32.6h-19.8V46.1c0-3.2-0.6-5.5-1.8-6.8c-1.2-1.3-2.9-2-5-2c-2.4,0-4.3,0.9-5.8,2.7
			c-1.5,1.8-2.2,5.1-2.2,9.7v24.7h-19.6V23z"/>
			<path class="st3" d="M228.7,105V23h18.4v7.6c2.6-3.2,4.9-5.3,7-6.5c2.9-1.5,6-2.3,9.5-2.3c6.9,0,12.2,2.6,15.9,7.9
			c3.8,5.3,5.6,11.8,5.6,19.5c0,8.5-2,15.1-6.1,19.6c-4.1,4.5-9.3,6.7-15.5,6.7c-3,0-5.8-0.5-8.3-1.5c-2.5-1-4.7-2.6-6.7-4.6v11.4
			L228.7,105z M248.4,48.8c0,4.1,0.9,7.1,2.6,9.1c1.7,2,3.9,3,6.5,3c2.3,0,4.2-0.9,5.8-2.8c1.5-1.9,2.3-5.1,2.3-9.6
			c0-4.2-0.8-7.2-2.4-9.2c-1.6-2-3.6-2.9-5.9-2.9c-2.5,0-4.6,1-6.3,3C249.3,41.2,248.4,44.4,248.4,48.8"/>
			<path class="st3" d="M289.6,48.8c0-7.8,2.6-14.3,7.9-19.4c5.3-5.1,12.4-7.6,21.4-7.6c10.3,0,18,3,23.3,8.9
			c4.2,4.8,6.3,10.7,6.3,17.7c0,7.9-2.6,14.4-7.9,19.4c-5.2,5.1-12.5,7.6-21.7,7.6c-8.2,0-14.9-2.1-20-6.3
			C292.8,64,289.6,57.2,289.6,48.8 M309.4,48.8c0,4.6,0.9,8,2.8,10.2c1.8,2.2,4.2,3.3,7,3.3c2.8,0,5.2-1.1,7-3.2
			c1.8-2.2,2.7-5.6,2.7-10.4c0-4.5-0.9-7.8-2.7-9.9c-1.8-2.2-4.1-3.3-6.8-3.3c-2.9,0-5.2,1.1-7.1,3.3
			C310.3,40.9,309.4,44.3,309.4,48.8"/>
			<path class="st3" d="M352.5,58.4l19-0.1c0.8,2.3,1.9,4,3.4,5c1.4,1,3.4,1.5,5.8,1.5c2.6,0,4.7-0.6,6.1-1.7c1.1-0.8,1.7-1.9,1.7-3.1
			c0-1.4-0.7-2.5-2.2-3.3c-1.1-0.6-3.9-1.2-8.5-2c-6.8-1.2-11.6-2.3-14.2-3.3c-2.7-1-4.9-2.7-6.7-5.1c-1.8-2.4-2.7-5.2-2.7-8.3
			c0-3.4,1-6.3,3-8.7c2-2.5,4.7-4.3,8.1-5.5c3.4-1.2,8.1-1.8,13.9-1.8c6.1,0,10.6,0.5,13.6,1.4c2.9,0.9,5.3,2.4,7.3,4.3
			c2,2,3.4,6.6,4.7,9.9l-18.6-0.1c-0.5-1.6-1.3-2.8-2.4-3.6c-1.5-1-3.4-1.5-5.6-1.5c-2.2,0-3.8,0.4-4.8,1.2c-1,0.8-1.5,1.7-1.5,2.9
			c0,1.3,0.6,2.2,1.9,2.8c1.3,0.6,4.1,1.2,8.4,1.7c6.5,0.8,11.4,1.8,14.6,3.1c3.2,1.3,5.6,3.2,7.3,5.7c1.7,2.4,2.5,5.1,2.5,8.1
			c0,3-0.9,5.8-2.7,8.6c-1.8,2.8-4.6,5-8.5,6.7c-3.9,1.7-9.1,2.5-15.7,2.5c-9.4,0-14.8-1.4-18.8-4.1C356.9,68.7,353.6,63.3,352.5,58.4
			"/>
			<path class="st3" d="M437.1,7.3V23h10.8v14.4h-10.8v18.2c0,2.2,0.2,3.6,0.6,4.3c0.6,1.1,1.8,1.6,3.4,1.6c1.4,0,7.4,0.1,7.4,0.1
			l0.1,13.1c-4.9,1.1-9.4,0.8-13.6,0.8c-4.9,0-8.5-0.6-10.8-1.9c-2.3-1.2-4-3.2-5.1-5.7c-1.1-2.5-1.7-6.7-1.7-12.4v-18h-7.2V23h7.2
			l-0.1-19.7L437.1,7.3z"/>
			<polygon class="st2" points="228.7,126.8 248.6,102.6 248.6,88 228.7,112.3 "/>
		</svg><br><br><br><br>';
	}

?>

<h3>Signpost levering</h3>
<br>

<?php

	if($_GET['delivery_number'] !== '0'){
		$sql = "SELECT *, q.id AS orderid, a.id AS deliveryid,
			( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
			( SELECT school_name FROM schools WHERE synergyid = q.synergyid LIMIT 1 ) as schoolnaam,
			( SELECT emse3 from images2020 WHERE id = q.synergyid ) as emse3,
			( SELECT productnumber from devices WHERE spsku = q.spsku ) as productnumber,
			ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = q.id AND delivery_number < a.delivery_number), 0) as amountbefore,
			ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = q.id AND delivery_number > a.delivery_number), 0) as amountafter,
			IFNULL(a.delivery_number, ( SELECT COUNT(*)+1 FROM delivery WHERE synergyid = q.synergyid )) as delivery_number_number
			FROM orders q
			LEFT OUTER JOIN delivery a ON q.id = a.orderid
			WHERE q.id = '" . $_GET['orderid'] . "' AND a.delivery_number = '" . $_GET['delivery_number'] . "' AND q.deleted != 1";
	} else {
		$sql = "SELECT *, orders.id as orderid,
			( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
			( SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ) as schoolnaam,
			( SELECT emse3 from images2020 WHERE id = orders.synergyid ) as emse3,
			( SELECT productnumber from devices WHERE spsku = q.spsku ) as productnumber,
			( SELECT COUNT(*)+1 FROM delivery WHERE synergyid = orders.synergyid ) as delivery_number_number
			FROM orders
			WHERE orders.id = '" . $_GET['orderid'] . "' AND orders.deleted != 1";
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			echo '<table id="orderSummary" class="table">';
			echo "<tr><td>Suborder</td><td><span id='deliveryid' hidden>" . $row["deliveryid"] . "</span>SP-BYOD20-" . $row["orderid"] . "-" . $row["type"] . $row["delivery_number_number"] . "</td></tr>";
			echo "<tr><td>Synergy ID</td><td>" . $row["synergyid"] . "</td></tr>";
			echo "<tr><td>School</td><td>" . $row["schoolnaam"] . "</td></tr>";

			//If there is a specific shipping adress in the database
			if($row['shipping_name'] != ''){
				echo '<tr><td>Shipping info</td><td style="color:red;">';
					echo '' . $row['shipping_name'];
					echo '<br>' . $row['shipping_street'] . ' ' . $row['shipping_number'];
					echo '<br>' . $row['shipping_postcode'] . ' ' . $row['shipping_city'];
					echo '<br>' . $row['shipping_note'];
				echo "</td></tr>";
			}
			echo "<tr><td>Laptop</td><td>" . $row["amount"] . " x <strong>" . $row["devicebeschrijving"] . "</strong> (" . $row["SPSKU"] . ")</td></tr>";

			echo "<tr id='data'><td>Gegevens</td><td>";

			echo '<div style="display:flex; justify-content:space-around;';
			if(isset($_GET['print']) == true){
				echo ' font-size: 14px;';
			}
			echo '"><div>';
			$array = array();
			$sql2 = "SELECT signpost_label, label, serialnumber, serialnumbernote, (SELECT concat(warehouse, ' (', location, ')') FROM stock WHERE stock.label = labels.label OR stock.serial = labels.serialnumber LIMIT 1) AS location FROM labels WHERE orderid = '" . $row["orderid"] . "'";
			$result2 = $conn->query($sql2);
			$school = 0;
			$i = 0;
			$stocklocation = '';

			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) {
					if($row2['serialnumbernote'] != '' && $row2['serialnumbernote'] != $row2['label'] && $i == 0){
						$i++;
						$school = 1;
					}
					$i++;

					if(isset($_GET['print']) == true){
						$stocklocation = $row2["location"] . " - ";
					}

					if($school == 1){
						if($row2["label"][0] == '-' || $row2["signpost_label"] == $row2["label"]){
							array_push($array, "<li class='productieorder'>" . $stocklocation . "<b>" . $row2["signpost_label"] . "</b> - " . $row2["serialnumbernote"] . " - " . $row2["serialnumber"] . "</li>" );
						} else {
							array_push($array, "<li class='productieorder'>" . $stocklocation . "<b>" . $row2["signpost_label"] . "</b> - " . "<b>" . $row2["label"] . "</b> - " . $row2["serialnumbernote"] . " - " . $row2["serialnumber"] . "</li>" );
						}
					} else {
						if($row2["label"][0] == '-' || $row2["signpost_label"] == $row2["label"]){
							array_push($array, "<li class='productieorder'>" . $stocklocation . "<b>" . $row2["signpost_label"] . "</b> - " . $row2["serialnumber"] . "</li>" );
						} else {
							array_push($array, "<li class='productieorder'>" . $stocklocation . "<b>" . $row2["signpost_label"] . "</b> - " . "<b>" . $row2["label"] . "</b> - " . $row2["serialnumber"] . "</li>" );
						}
					}
				}
			} else {
				echo "0 results";
			}

			if($row["delivery_number_number"] == 1){
				$array = array_slice($array, 0, $row["amount"]);
			} elseif($row["amountafter"] !== 0){
				$array = array_slice($array, $row["amountbefore"], $row["amount"]);
			}

			echo '<ol type="a" class="devicedata">';
			foreach($array as $key){
				echo $key;
			}
			echo '</ol>';

			if(count($array) != $row["amount"]){
				echo '<p style="color:red;">Er is iets mis! Te weinig serienummers!!</p>';
			}

			echo '</div><div>';

			$delivery_number_number = $row['delivery_number_number'];
			if(substr($delivery_number_number, -1) == '0'){
				$delivery_number_number = '0' . $delivery_number_number;
			}
			if(substr($delivery_number_number, -2) == '0'){
				$delivery_number_number = '0' . $delivery_number_number;
			}

			if($row["type"] == 'W'){

				$tsql= "SELECT refer, orddat, isnull(refer1, textfield11) as refer1, refer2, artcode, freefield1, lengte, instruction, orsrg.id AS ordersrg
					FROM orkrg with (nolock)
					INNER JOIN orsrg with (nolock) ON orkrg.ordernr=orsrg.ordernr
					INNER JOIN cicmpy with (nolock) ON cicmpy.debnr=orkrg.debnr
					WHERE freefield1='" . $row['synergyid'] ."' AND lengte='" . $row['orderid'] . "." . $delivery_number_number . "'
					ORDER BY orddat, refer";
				$getResults = sqlsrv_query($msconn, $tsql);
				echo '<p style="display:none;">' . $tsql . '</p>';
				$results = '<ol type="a" class="devicedata">';
				$i = 0;

				if ($getResults == FALSE){
					die( print_r( sqlsrv_errors(), true));
				}

				while ($sqlresults = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
					$results .= '<li class="exactorder"><b><span style="display:none;">' . $sqlresults['instruction'] . ';' . $sqlresults['ordersrg'] . '</span>' . $sqlresults['refer'] . '</b> (' . $sqlresults['orddat']->format('d-m-Y') . ') - ' . $sqlresults['refer1'] . ' ' . $sqlresults['refer2'] . '<br></li>';
				}

				echo $results . '</ol>';

			} elseif($row["type"] == 'H'){

				$sql2 = "SELECT * FROM leermiddel.tblcontractdetails AS a
					LEFT JOIN leermiddel.tbltoestelcontractdefinitie AS b ON b.id = a.toestelcontractdefinitieid
					LEFT JOIN leermiddel.tblschool AS c ON c.id = a.schoolid
					WHERE c.synergyschoolid = '" . $row['synergyid'] . "' AND a.lengte = '" . $row['orderid'] . "." . $delivery_number_number . "'";
				$result2 = $conn->query($sql2);
				echo '<p style="display:none;">' . $sql2 . '</p>';
				$results = '<ol type="a" class="devicedata">';
				$i = 0;

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						$i++;
						$results .= '<li class="leermiddelorder"><b><span style="display:none;">' . $row2['instruction'] . ';' . $row2['ContractVolgnummer'] . '</span>' . $row2['ContractVolgnummer'] . '</b> - (' . $row2['DatumContractopgemaakt'] . ') ' . $row2['VoornaamLeerling'] . ' ' . $row2['NaamLeerling'] . '</li>';
					}
				}

				echo $results . '</ol>';

			}

			echo "</div></div>";
			echo "</td></tr>";

			if($row['signature'] !== ''){
				echo "<tr><td>Afgeleverd op</td><td>" . date('d-m-Y H:i:s', strtotime($row['delivered_on'])) . "</td></tr>";
			} else {
				echo "<tr><td>Vandaag</td><td>" . date('d-m-Y') . "</td></tr>";
			}
			echo "</table>";

			if ($row["emse3"] == 0) {
				echo "<p style='color:red;' class='noPrint'>Intune licenties zijn nog niet in orde!</p>";
			}

			if ($loginname == 'Jordy') {
				echo '
				<form action="updatelabels.php" method="post" target="_blank">
					<input type="text" id="updateLabels" name="updateLabels" hidden>
					<button id="updateLabelsSubmit" class="btn btn-danger" hidden>Labels komen niet overeen, klik hier om ze aan te passen</button><br>
				</form>';
			}

			if(isset($_GET['print']) == false){
				echo '
				<form action="delivery-mail.php" method="post" target="_blank">
					<input type="text" id="sendMailId" name="id" hidden>
					<input type="text" id="sendMailData" name="data" hidden>
					<button id="sendMail" class="btn btn-primary">✉️</button><br>
				</form>';
			}

?>

	<br>
	<form action="delivery.php" class="noPrint" method="post" id="form">
	<div>
	Afgeleverd door:
	<input name="delivered_by" value="<?php if($row['delivered_by'] !== ''){echo $row['delivered_by'];}else{echo $loginname;} ?>" class="form-control" style="background-color:#ECF0F1;" readonly />
	</div><br>
	<div>
	<?php if($row['signature'] !== ''){ ?>
	Ontvangen door:
	<input name="accepted_by" value="<?php if($row['accepted_by'] !== ''){echo $row['accepted_by'];} ?>" class="form-control" placeholder="" required />
	</div><br>
	<div>
	<?php
	}
	?>
	<div>
	Ontvangen door:
	<input name="accepted_by" value="<?php if($row['accepted_by'] !== ''){echo $row['accepted_by'];} ?>" class="form-control" placeholder="" required />
	</div><br>
	<div>
	Contact mail adres:
	<input name="accepted_email" type="mail" value="<?php if($row['accepted_email'] !== ''){echo $row['accepted_email'];} ?>" class="form-control" placeholder="" required />
	</div><br>
	<div>
	Commentaar:
	<input name="comments" type="text" value="<?php if($row['comments'] !== ''){echo $row['comments'];} ?>" class="form-control" placeholder="" />
	</div><br>
	<?php if($row['type'] == 'R'){?>
	<div>
	Type restlevering:
		<select name="resttype" id="resttype" class="form-control">
			<?php if(isset($row['resttype']) == true && $row['resttype'] !== ''){
			echo '<option value="' . $row['resttype'] . '">' . $row['resttype'] . '</option>';
			} else { ?>
			<option value=""></option>
			<option value="leermiddel">Leermiddel</option>
			<option value="lastenboek">Lastenboek</option>
			<option value="diefstal">Diefstal</option>
			<option value="verlies">Verlies</option>
			<option value="andere">Andere ( beschrijf in commentaar veld )</option>
			<?php } ?>
		</select>
	</div><br><?php } ?>
	<div>
	Handtekening:<br>
	<?php if($row['signature'] !== ''){
	$img = $row['signature'];
	$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $img));
	echo '<img src="' . $img . '">';
	} else { ?>
	<canvas id="signature" value="" width="400" height="150" style="border: 2px solid black;"></canvas>
	<?php
	}
	?>
	</div><br>
	<div>
	<input type="hidden" name="signature" />
	<input type="hidden" name="delivery_type" value="<?php echo $row["type"]; ?>"/>
	<input type="hidden" name="delivery_number" value="<?php echo $row["delivery_number_number"]; ?>"/>
	<input type="hidden" name="orderid" value="<?php echo $row["orderid"]; ?>"/>
	<textarea id="orderSummaryField" name="orderSummary" style="display:none;">
	</textarea>
	</div>
	<?php if($row['signature'] == ''){ ?>
		<button type="submit" id="submit" class="btn btn-success">Opslaan</button>
	<?php } ?>
	<form>
	<br><br><br><br><br>

			<script>
			$(function () {
				$('#orderSummaryField')[0].innerHTML = $('#orderSummary')[0].outerHTML;
				if(window.location.href.match(/.*print/)){
					print();
				}

				function htmlDecode(value){
					return $('<div/>').html(value).text();
				}

				<?php if($row['type'] == 'W'){ ?>

				$('#sendMailId')[0].value = htmlDecode($('#deliveryid')[0].innerHTML);
				var productieorders = document.getElementsByClassName("productieorder");
				for(i = 0; i < productieorders.length; i++) {
					$('#sendMailData')[0].value = $('#sendMailData')[0].value + htmlDecode($('.productieorder')[i].innerHTML) + ' - ' + htmlDecode($('.exactorder')[i].innerHTML.split('</span>')[1]) + '<br>';
				}

				var exact = document.getElementsByClassName("exactorder");
				var productie = document.getElementsByClassName("productieorder");
				var exactorders = [];

				if(exact.length == productie.length){
					for (i = 0; i < productie.length; i++) {
						exactorders.push(
							Array(
								productie[i].innerHTML.split(' - ')[0].split('<b>')[1].split('</b>')[0],
								exact[i].innerHTML.split('<b>')[1].split('</b>')[0].split('<span style="display:none;">')[1].split('</span>')[0]
							)
						);
					}
					exactorders.forEach(checkOrder);

					function checkOrder(item){
						if(item[0] != item[1].split(';')[0]){
							console.log(""+item[0]+";"+item[1].split(';')[1]+"");
							$('#updateLabels')[0].value = $('#updateLabels')[0].value+"W_"+item[0]+"_"+item[1].split(';')[1]+";";
							$('#updateLabelsSubmit')[0].hidden = false;
						}
					}
				} else {
					$('#updateLabelsSubmit')[0].hidden = false;
					$('#updateLabelsSubmit')[0].innerHTML = 'Serienummers en orders komen niet overeen, koppeling niet mogelijk';
					$('#updateLabelsSubmit')[0].disabled = true;
				}
				<?php }elseif($row['type'] == 'H'){ ?>

				$('#sendMailId')[0].value = htmlDecode($('#deliveryid')[0].innerHTML);
				var productieorders = document.getElementsByClassName("productieorder");
				for(i = 0; i < productieorders.length; i++) {
					$('#sendMailData')[0].value = $('#sendMailData')[0].value + htmlDecode($('.productieorder')[i].innerHTML) + ' - ' + htmlDecode($('.leermiddelorder')[i].innerHTML.split('</span>')[1]) + '<br>';
				}

				var leermiddel = document.getElementsByClassName("leermiddelorder");
				var productie = document.getElementsByClassName("productieorder");
				var leermiddelorders = [];

				if(leermiddel.length == productie.length){
					for (i = 0; i < productie.length; i++) {
						leermiddelorders.push(
							Array(
								productie[i].innerHTML.split(' - ')[0].split('<b>')[1].split('</b>')[0],
								leermiddel[i].innerHTML.split('<b>')[1].split('</b>')[0].split('<span style="display:none;">')[1].split('</span>')[0]
							)
						);
					}
					leermiddelorders.forEach(checkOrder);

					function checkOrder(item){
						if(item[0] != item[1].split(';')[0]){
							console.log(""+item[0]+";"+item[1].split(';')[1]+"");
							$('#updateLabels')[0].value = $('#updateLabels')[0].value+"H_"+item[0]+"_"+item[1].split(';')[1]+";";
							$('#updateLabelsSubmit')[0].hidden = false;
						}
					}
				} else {
					$('#updateLabelsSubmit')[0].hidden = false;
					$('#updateLabelsSubmit')[0].innerHTML = 'Serienummers en orders komen niet overeen, koppeling niet mogelijk';
					$('#updateLabelsSubmit')[0].disabled = true;
				}
				<?php } else { ?>
				$('#sendMailId')[0].value = htmlDecode($('#deliveryid')[0].innerHTML);
				var productieorders = document.getElementsByClassName("productieorder");
				for(i = 0; i < productieorders.length; i++) {
					$('#sendMailData')[0].value = $('#sendMailData')[0].value + htmlDecode($('.productieorder')[i].innerHTML) + '<br>';
				}
				<?php } ?>
			});
			</script>

<?php
		}
	} else {
		echo "0 results";
	}
	$conn->close();
?>

	<script>

	var canvas = document.getElementById('signature');
	var signaturePad = new SignaturePad(canvas);
	signaturePad.minWidth = 1;
	signaturePad.maxWidth = 3;
	signaturePad.penColor = "rgb(66, 133, 244)";

	function getSignaturePad() {
		var imageData = signaturePad.toDataURL();
		document.getElementsByName("signature")[0].setAttribute("value", imageData);
	}

	$('#form').submit(function() {
		getSignaturePad();
		return true;
	});


	$(function () {
		if(window.location.href.match(/.*print/)){
			print();
		}
	});

	</script>


<?php
} elseif (isset($_GET['orderid']) !== false) {

	echo '<h3>Order details</h3>';
	$synergyid = '';

	$sql = "SELECT *, orders.id as orderid, orders.synergyid as ordersynergyid, orders.spsku as orderspsku, orders.status as orderstatus,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		( SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ) as schoolnaam,
		ifnull( (SELECT Email FROM images2019 WHERE id = orders.imageid LIMIT 1), (SELECT contactemail FROM images2020 WHERE id = orders.imageid LIMIT 1)) as contactemail
		FROM orders left join images2020 on orders.imageid = images2020.id
		where orders.id = '" . $_GET['orderid'] . "' AND orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			echo '<table class="table">';
			echo "<tr><td>Order</td><td><a href='order.php?id=" . $row["orderid"] . "'>SP-BYOD20-" . $row["orderid"] . "</a></td></tr>";
			echo '<tr><td>Contactpersoon</td><td><a href="mailto:' . $row["contactemail"] . '">' . $row["contactemail"] . '</a></td></tr>';
			echo "<tr><td>Magazijn</td><td>" . $row["warehouse"] . "</td></tr>";
			echo "<tr><td>Synergy ID</td><td>" . $row["ordersynergyid"] . "</td></tr>";
			echo "<tr><td>School</td><td>" . $row["schoolnaam"] . "</td></tr>";
			echo "<tr><td>Laptop</td><td>" . $row["amount"] . " x <strong>" . $row["devicebeschrijving"] . "</strong> (" . $row["orderspsku"] . ")</td></tr>";
			echo "</table>";
			$status = $row['orderstatus'];
			$finance_type = $row['finance_type'];
			$synergyid = $row['ordersynergyid'];

		}

	} else {
		echo "0 results";
	}

	echo '<br><br>';

	$sql2 = "SELECT *, delivery.id AS deliveryid, delivery.amount as suborderamount, delivery.type as subordertype, delivery.updated_at as suborder_updated_at, delivered_on
		FROM delivery
		LEFT JOIN orders ON orders.id = delivery.orderid
		WHERE orderid = " . $_GET['orderid'];
	$result2 = $conn->query($sql2);

	echo '<table class="table" style="table-layout: fixed">';
	echo "<tr>
		<th>Aangevraagd</th>
		<th>Levering</th>
		<th>Aantal</th>
		<th>Afgeleverd door</th>
		<th>Aangenomen door</th>
		<th>Aangemaakt op</th>
		<th></th>
		<th></th>
		</tr>";

	$i = 0;
	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {

			$i++;
			if($row2["delivered_by"] !== ''){
				echo '<tr>
					<td>';
				if($row2['printed_on'] == ''){
					if($row2['signature'] !== ''){
						echo '<span class="smalltext">' . date('d-m-Y H:i:s', strtotime($row2['delivered_on'])) . '</span><br>';
						echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_GET['orderid'] . '&delivery_number=' . $row2['delivery_number'] . '&suborderid=' . $row2['deliveryid'] . '&printsuborder=true', false).'">Print opnieuw</a>';
					} else {
						echo 'x<br>';
						echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_GET['orderid'] . '&delivery_number=' . $row2['delivery_number'] . '&suborderid=' . $row2['deliveryid'] . '&printsuborder=true', false).'">Print nu</a>';
					}
				} else {
					echo '<span class="smalltext">' . $row2['printed_on'] . '<br>' . $row2['printed_by'] . '</span><br>';
					echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_GET['orderid'] . '&delivery_number=' . $row2['delivery_number'] . '&suborderid=' . $row2['deliveryid'] . '&printsuborder=true', false).'">Print opnieuw</a>';
				}
				echo '</td>
					<td>' . $row2['subordertype'] . $row2['delivery_number'] . '</td>
					<td>' . $row2['suborderamount'] . '</td>
					<td style="word-wrap:break-word;">' . $row2['delivered_by'] . '</td>
					<td>' . $row2['accepted_by'] . '</td>
					<td>' . date("d-m-Y", strtotime($row2['created_at'])) . '</td>
					<td><a href="'. hasAccessForUrl('delivery.php?delivery_number=' . $row2['delivery_number'] . '&orderid=' . $_GET['orderid'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Details</button></a></td>';
				if($row2['exact_generated'] == '0' && $row2['type'] != 'W'){
					echo '<td><a href="'. hasAccessForUrl('generateXML.php?id=' . $row2['deliveryid'] . '&orderid=' . $row2['orderid'] . '&date=' . date('Y-m-d') . '&type=4', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:0px 5px;">Genereren in exact (4)</button></a></td>';
				} else {
					if($row2['exact_delivery'] == '0'){
						if($row2['type'] == 'W'){
							echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row2['deliveryid'] . '&deliverynumber=' . $row2['delivery_number'] . '&orderid=' . $row2['orderid'] . '&warehouse=' . $row2['warehouse'] . '&type=5&webshop=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Webshop levering in exact (5W)</button></a></td>';
							//echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row2['deliveryid'] . '&orderid=' . $row2['orderid'] . '&deliveryNumber=' . $row2['delivery_number'] . '&type=5W', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Webshop levering in exact (5W)</button></a></td>';
						} else {
							echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row2['deliveryid'] . '&orderid=' . $row2['orderid'] . '&warehouse=' . $row2['warehouse'] . '&type=5', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Levering in exact (5)</button></a></td>';
						}
					} else {
						echo '<td></td>';
					}
				}

				echo '</tr>';
			} else {
				echo '<tr class="btn-outline-danger">
					<td>';
					if($row2['printed_on'] == ''){
						if($row2['signature'] !== ''){
							echo '<span class="smalltext">' . date('d-m-Y H:i:s', strtotime($row2['delivered_on'])) . '</span><br>';
							echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_GET['orderid'] . '&delivery_number=' . $row2['delivery_number'] . '&suborderid=' . $row2['deliveryid'] . '&printsuborder=true', false).'">Print opnieuw</a>';
						} else {
							echo 'x<br><a href="'. hasAccessForUrl('delivery.php?orderid=' . $_GET['orderid'] . '&delivery_number=' . $row2['delivery_number'] . '&suborderid=' . $row2['deliveryid'] . '&printsuborder=true', false).'">Print nu</a>';
						}
					} else {
						echo '<span class="smalltext">' . $row2['printed_on'] . '<br>' . $row2['printed_by'] . '</span><br>';
							echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_GET['orderid'] . '&delivery_number=' . $row2['delivery_number'] . '&suborderid=' . $row2['deliveryid'] . '&printsuborder=true', false).'">Print opnieuw</a>';
					}
				echo '</td>
					<td>' . $row2['subordertype'] . $row2['delivery_number'] . '</td>
					<td>' . $row2['suborderamount'] . '</td>
					<td>Nog niet geleverd</td>
					<td></td>
					<td></td>
					<td><a href="'. hasAccessForUrl('delivery.php?delivery_number=' . $row2['delivery_number'] . '&orderid=' . $_GET['orderid'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Uitleveren</button></a></td>
					<td></td>
					</tr>';
			}
		}

	} else {
		echo "<tr><td>Nog geen leveringen gebeurd</td><td></td><td></td><td></td><td></td><td></td></tr>";
	}
	echo "</table>";

	$i++;
	if($status == 'levering' || $status == 'uitgeleverd' || $status == 'tdafgewerkt'){
		echo '<a href="'. hasAccessForUrl('delivery.php?new=true&delivery_number=' . $i . '&orderid=' . $_GET['orderid'] . '&type=' . $finance_type . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Nieuwe sublevering toevoegen</button></a><br><br><br><br>';
	} else {
		echo '<b>Order zit nog in ' . $status . ' en heeft nog geen serienummers,<br>
			het is dus niet mogelijk om een suborder aan te maken.</b><br>';
	}

	echo '<br><br>';
	echo '<h3>Openstaande webshop orders van dit Synergy ID</h3>';
	echo '<table class="table">';
	$leermiddelorders = 0;
	$webshoporders = 0;
	$sql = "SELECT CONCAT('SP-BYOD-', id) AS orderid, GROUP_CONCAT(id) AS alleorderids, synergyid, SPSKU, warehouse, shipping_date,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		(SELECT COUNT(*) FROM leermiddel.tblcontractdetails AS a
		LEFT JOIN leermiddel.tbltoestelcontractdefinitie AS b ON b.id = a.toestelcontractdefinitieid
		LEFT JOIN leermiddel.tblschool AS c ON c.id = a.schoolid
		WHERE c.synergyschoolid = q.synergyid AND b.sku = REPLACE(REPLACE(REPLACE(q.spsku, '-O', ''), '-B1', ''), '-B2', '')
		AND ContractOntvangen = '1' AND deleted = '0' AND VoorschotOntvangen IN ('1', '-1') AND lengte = '0' AND ContractVolgnummer LIKE '%2021___') AS leermiddelorders
		FROM orders q
		WHERE finance_type = 'Particulier' AND q.synergyid = '" . $synergyid . "' AND q.deleted != 1
		GROUP BY synergyid, spsku";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			if($row['leermiddelorders'] != '0'){
				echo '<tr>';
				echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
				echo '<td>Leermiddel: ' . $row['leermiddelorders'] . '</td>';

				echo '<td class="">';
				$orderids = explode(',', $row['alleorderids']);
				foreach($orderids as $id){
					echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $id . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
				}
				echo '</td>';
				echo '</tr>';
				$leermiddelorders = 1;
			}
		}
	} else {

		echo "Geen leermiddel orders <br>";

	}

	if($leermiddelorders == '0'){
		echo "Geen leermiddel orders <br>";
	}

	$sql = "SELECT id, CONCAT('SP-BYOD-', id) AS orderid, GROUP_CONCAT(id) AS alleorderids, synergyid, SPSKU, warehouse, shipping_date,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
		FROM orders q
		WHERE finance_type = 'Particulier' AND synergyid = '" . $synergyid . "' AND q.deleted != 1
		GROUP BY synergyid, SPSKU
		ORDER BY synergyid, SPSKU";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row['SPSKU']))));
			$tsql= "SELECT count(*) AS aantal
				FROM orkrg with (nolock)
				INNER JOIN orsrg with (nolock) ON orkrg.ordernr=orsrg.ordernr
				INNER JOIN cicmpy with (nolock) ON cicmpy.debnr=orkrg.debnr
				WHERE freefield1='" . $row['synergyid'] . "' AND artcode LIKE '" . $spsku . "%' AND lengte=0";
			$stmt = sqlsrv_query( $msconn, $tsql);
			if($stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			$aantalwebshoporders = 0;
			while( $row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
				$aantalwebshoporders = $row2['aantal'];
			}

			if($aantalwebshoporders != '0'){
				echo '<tr>';
				echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';
				echo '<td>Webshop: ' . $aantalwebshoporders . '</td>';

				echo '<td class="">';
				$orderids = explode(',', $row['alleorderids']);
				foreach($orderids as $id){
					echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $id . '', false).'" target="_blank"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:5px 0px;">Order ' . $id . ' bekijken</button></a><br>';
				}
				echo '</td>';
				echo '</tr>';
				$webshoporders = 1;
			}
		}
	} else {

		echo "Geen webshop ( magento ) orders <br>";

	}
	if($webshoporders == '0'){
		echo "Geen webshop ( magento ) orders <br>";
	}

	echo "</table><br><br><br><br>";


	$conn->close();

} else {
?>
	<h3>Mijn Orders</h3>

	<table class="table" id="table2">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
<?php

	$sql = "SELECT *, q.id as orderid,
		( SELECT ifnull(sum(amount), 0) FROM orderpicking WHERE orderid = q.id ) as picked,
		( SELECT id FROM orderpicking WHERE orderid = q.id LIMIT 1 ) as pickingid,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
		(SELECT count(serialnumber) FROM `labels` where orderid = q.id and serialnumber != '') as done
		FROM `byod-orders`.orders as q LEFT JOIN `byod-orders`.devices ON q.SPSKU = devices.SPSKU LEFT JOIN schools on q.synergyid = schools.synergyid WHERE status = 'levering' ORDER BY q.synergyid";
	$result = $conn->query($sql);
	$schools = "";

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			if($schools !== $row['synergyid']){
				echo '
					<tr class="table-primary">
					<th scope="col">' . $row['synergyid'] . '</th>
					<th scope="col">' . $row['school_name'] . '</th>
					<th scope="col"></th>
					<th scope="col"></th>
					<th scope="col">aantal</th>
					<th scope="col"></th>
					</tr>
				';
				$schools = $row['synergyid'];
			}


			$url = "document.location = 'order.php?id=" . $row['orderid'] . "'";

			echo '<tr onclick="' . $url . '" class="">';
			echo '<td scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';

			echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['SPSKU'] . '</span></td>';

			echo '<td></td>';
			echo '<td></td>';

			echo '<td>' . $row['amount'] . '</td>';

			echo '<td class="">';
			if ($row['picked'] == '0') {
				echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['orderid'] . '&amount=' . $row['amount'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Pick Order</button></a><br>';
			} else {
				echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['orderid'] . '&pickingid=' . $row['pickingid'] . '&amount=' . $row['amount'] . '&warehouse=' . $row['warehouse'] . '&date=' . date('Y-m-d') . '&type=moving', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Move Order</button></a><br>';
			}
			echo '<a href="'. hasAccessForUrl('delivery.php?orderid=' . $row['orderid'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:120px !important;padding:0px;margin:0px 5px;">Uitleveren</button></a>
				<a href=""><button type="button" class="btn btn-secondary" style="height:25px !important;width:150px !important;padding:0px;margin:0px;">Decomission</button></a>
				</td>';
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

	<script>
	$(function () {

		$('#type').on('change', function(){
			var shoptype = $(this).val();
			if(shoptype == 'W') {
				$('#webshop').show( "slow" );
				$('#leermiddel').hide( "slow" );
				$('#other').hide( "slow" );
				if($('#webshopnumbers')[0].value.split(';').length-1 == 0){
					$('#amount')[0].max = 0;
				} else {
					if($('#amountLeft')[0].value > $('#webshopnumbers')[0].value.split(';').length-1){
						$('#amount')[0].max = $('#webshopnumbers')[0].value.split(';').length-1;
					} else {
						$('#amount')[0].max = $('#amountLeft')[0].value;
					}
				}
			} else if(shoptype == 'H') {
				$('#webshop').hide( "slow" );
				$('#leermiddel').show( "slow" );
				$('#other').hide( "slow" );
				if($('#leermiddelnumbers')[0].value.split(';').length-1 == 0){
					$('#amount')[0].max = 0;
				} else {
					if($('#amountLeft')[0].value > $('#leermiddelnumbers')[0].value.split(';').length-1){
						$('#amount')[0].max = $('#leermiddelnumbers')[0].value.split(';').length-1;
					} else {
						$('#amount')[0].max = $('#amountLeft')[0].value;
					}
				}
			} else if(shoptype == 'R') {
				$('#amount')[0].max = $('#amountLeft')[0].value;
				$('#webshop').hide( "slow" );
				$('#leermiddel').hide( "slow" );
				$('#other').show( "slow" );
			}else{
				$('#amount')[0].max = $('#amountLeft')[0].value;
				$('#webshop').hide( "slow" );
				$('#leermiddel').hide( "slow" );
				$('#other').hide( "slow" );
			}
		});

	});
	</script>

<?php
	include('footer.php');
?>
