<?php
$title = 'XML Generator';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

echo '<div class="body">';

if($_POST['type'] == '5W'){

	// XML 5W

	$sql = "UPDATE orders SET xmlstate='5' WHERE id= '" . $_POST['orderid'] . "' AND xmlstate = 4";
	if ($conn->query($sql) === TRUE) {
		// OK
	}

	$i = 0;
	$fileContent = '';
	$sql = "UPDATE delivery SET exact_delivery = '1'
	WHERE id = '" . $_POST['deliveryid'] . "'";

	if ($conn->query($sql) === TRUE) {

		$warehouse = '';
		$sql2 = "SELECT warehouse, shipping_date FROM orders WHERE id = '" . $_POST['orderid'] . "' and deleted != 1";
		$result2 = $conn->query($sql2);

		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {
				$warehouse = $row2['warehouse'];
				$shippingdate = $row2['shipping_date'];
			}
		} else {
			echo "0 results";
		}

		$deliveryNumber = $_POST['deliverynumber'];
		if(substr($deliveryNumber, -1) == '0'){
			$deliveryNumber = '0' . $deliveryNumber;
		}
		if(substr($deliveryNumber, -2) == '0'){
			$deliveryNumber = '0' . $deliveryNumber;
		}

		$tsql= "SELECT *, orsrg.artcode as spsku, orkrg.id as orkrgid, orsrg.id as orsrgid
			FROM orkrg with (nolock)
			INNER JOIN orsrg with (nolock) ON orkrg.ordernr=orsrg.ordernr
			LEFT JOIN _serienummerlocatie with (nolock) ON orsrg.instruction = _serienummerlocatie.Serienummer
			WHERE lengte='" . $_POST['orderid'] . "." . $deliveryNumber . "'";
		$stmt = sqlsrv_query( $msconn, $tsql);

		if($stmt === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {

			$fileContent = '';
			$i++;
			$pickingid = 30000000 + $_POST['deliveryid'];
			$suborderType = 'SP-BYOD20-' . $_POST['orderid'] . '-W' . $_POST['deliverynumber'];
			$deliveryNumber = $_POST['deliverynumber'];
			$orderid = $_POST['orderid'];

			if(substr($deliveryNumber, -1) == '0'){
				$deliveryNumber = '0' . $deliveryNumber;
			}
			if(substr($deliveryNumber, -2) == '0'){
				$deliveryNumber = '0' . $deliveryNumber;
			}

			echo '<div>
				<a href="" class="btn btn-primary" id="download_link" download="export.xml" style="display:none;">Export XML</a>
				<p>Webshop XML 5 export gelukt</p>
					<a href="'. hasAccessForUrl('delivery.php?orderid='.$_POST['orderid'], false) .'" class="btn btn-primary" id="download_link">Ga terug naar het overzicht</a>
			</div>';

			$fileContent .= '<?xml version="1.0" ?>' . "\n";
			$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
			$fileContent .= '<Logistics>' . "\n";
			$fileContent .= '<Logistic type="D">' . "\n";

			$fileContent .= '      <Line number="1">' . "\n";
			$fileContent .= '          <OrderNumber>' . $row['ordernr'] . '</OrderNumber>' . "\n";
			$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
			$fileContent .= '          <Item>' . "\n";
			$fileContent .= '          <Code>' . $row['spsku'] . '</Code>' . "\n";
			$fileContent .= '          </Item>' . "\n";
			$fileContent .= '          <Quantity>1</Quantity>' . "\n";
			$fileContent .= '              <ToBeFulfilled>0</ToBeFulfilled>' . "\n";
			$fileContent .= '              <GenerateCreditNote>1</GenerateCreditNote>' . "\n";
			$fileContent .= '                   <Serialnumber>' . $row['instruction'] . '</Serialnumber>' . "\n";
			//$fileContent .= '                   <Description>' . $key[1] . '</Description>' . "\n";
			$fileContent .= '          <Warehouse>' . "\n";

			if($warehouse == 'Signpost'){
				$fileContent .= '              <Code>700</Code>' . "\n";
				$locationCode = '700';
			} else {
				$fileContent .= '              <Code>100</Code>' . "\n";
				$locationCode = '100';
			}

			$tsql= "UPDATE
				orkrg
				SET
				magcode = '" . $locationCode . "'
				WHERE id = '" . $row['orkrgid'] . "'";

			$updateResults= sqlsrv_query($msconn, $tsql);

			if ($updateResults == FALSE){
				die( print_r( sqlsrv_errors(), true));
			}

			$tsql2= "UPDATE
				orsrg
				SET
				magcode = '" . $locationCode . "'
				WHERE id = '" . $row['orsrgid'] . "'";

			$updateResults2= sqlsrv_query($msconn, $tsql2);

			if ($updateResults2 == FALSE){
				die( print_r( sqlsrv_errors(), true));
			}

			if($warehouse == 'Copaco'){
				$fileContent .= '              <Location>COPA</Location>' . "\n";
			} elseif($warehouse == 'TechData'){
				$fileContent .= '              <Location>DROP</Location>' . "\n";
			} else {
				$fileContent .= '              <Location>' . $_POST['location'] . '</Location>' . "\n";
			}

			$fileContent .= '          </Warehouse>' . "\n";
			$fileContent .= '      </Line>' . "\n";

			$fileContent .= '  </Logistic>' . "\n";
			$fileContent .= '</Logistics>' . "\n";
			$fileContent .= '</eExact>' . "\n";

			$fileContent = $fileContent . PHP_EOL;
			$filename = 'ExactXMLWebshop\5_' . $suborderType . '_' . $row['ordernr'] . '_' . $row['refer'] . '_lev.xml';
			$filename = '\\\\spb-srv-exact\c$\\' . str_replace('-', '_', $filename);
			file_put_contents($filename, $fileContent);
			//print($fileContent);

		}
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

} elseif($_GET['type'] == '5'){

	$options = '<option value=""></option>';
	$warehouse = $_GET['warehouse'];
	if($warehouse == 'TechData' || $warehouse == 'Copaco'){
		$warehouseloc = '100';
	} else {
		$warehouseloc = '700';
	}

	if(isset($_GET['webshop']) == true){
		$webshop = 'W';
	} else {
		$webshop = '';
	}

	$tsql= "SELECT maglok from evloc with (nolock) WHERE magcode = '" . $warehouseloc . "'";
	$stmt = sqlsrv_query( $msconn, $tsql);

	if($stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
		$options .= '<option value="' . $row['maglok'] . '">' . $row['maglok'] . '</option>';
	}

	echo '<h1>XML 5' . $webshop . '</h1><form action="generateXML.php" method="post">
		<input type="text" id="type" name="type" value="5' . $webshop . '" hidden class="form-control"><br>
		<input type="text" id="warehouse" name="warehouse" value="' . $_GET['warehouse'] . '" hidden class="form-control">
		<label for="orderid">Order:</label><br>
		<input type="text" id="orderid" name="orderid" value="' . $_GET['orderid'] . '" readonly class="form-control"><br>
		<label for="deliveryid">Suborder:</label><br>
		<input type="text" id="deliveryid" name="deliveryid" value="' . $_GET['deliveryid'] . '" readonly class="form-control"><br>
		<label for="deliverynumber">Delivery:</label><br>
		<input type="text" id="deliverynumber" name="deliverynumber" value="' . $_GET['deliverynumber'] . '" readonly class="form-control"><br>
		<label for="date">Datum:</label><br>
		<input type="text" id="date" name="date" value="" class="form-control"><br>
		Locatie afgewerkt toestel:<br>
		<span class="smalltext">Indien niet ingevuld, dan is het leeg voor Signpost, COPA voor Copaco en DROP voor TechData.<br>
		De locatie van dit order is ' . $_GET['warehouse'] . '</span>
		<select class="form-control" name="location" id="location">
		' . $options . '
		</select><br>
		<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

} elseif($_POST['type'] == '5'){

	// XML 5

	$sql = "UPDATE orders SET xmlstate='5' WHERE id= '" . $_GET['id'] . "' AND xmlstate = 4";
	if ($conn->query($sql) === TRUE) {
		// OK
	}

	$fileContent = '';
	$sql = "UPDATE delivery SET exact_delivery = '1'
	WHERE id = '" . $_POST['deliveryid'] . "'";

	if ($conn->query($sql) === TRUE) {

		$i = 0;
		$pickingid = 30000000 + $_POST['deliveryid'];

		echo '<div>
			<a href="" class="btn btn-primary" id="download_link" download="export.xml" style="display:none;">Export XML</a>
			<p>XML 5 export gelukt</p>
			<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_POST['orderid'] . '', false).'" class="btn btn-primary" id="download_link">Ga terug naar het overzicht</a>
		</div>';

		$fileContent .= '<?xml version="1.0" ?>' . "\n";
		$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
		$fileContent .= '<Logistics>' . "\n";
		$fileContent .= '<Logistic type="D">' . "\n";

		$sql3 = "SELECT *, q.id AS orderid, a.amount AS deliveryamount, a.type as subordertype,
			IFNULL(( SELECT SUM(amount) FROM delivery WHERE orderid = q.id AND delivery_number < a.delivery_number), 0) AS amountbefore,
			IFNULL(( SELECT SUM(amount) FROM delivery WHERE orderid = q.id AND delivery_number > a.delivery_number), 0) AS amountafter,
			IFNULL(a.delivery_number, ( SELECT COUNT(*)+1 FROM delivery WHERE orderid = q.id )) AS delivery_number_number
			FROM orders q
			LEFT JOIN schools ON schools.synergyid = q.synergyid
			LEFT JOIN delivery a ON a.orderid = q.id
			WHERE a.id = '" . $_POST['deliveryid'] . "' and q.deleted != 1";
		$result3 = $conn->query($sql3);

		if ($result3->num_rows > 0) {
			while($row3 = $result3->fetch_assoc()) {

				$orderid = $row3['orderid'];
				$suborderType = $row3['subordertype'];
				$deliveryNumber = $row3['delivery_number_number'];

				if(substr($deliveryNumber, -1) == '0'){
					$deliveryNumber = '0' . $deliveryNumber;
				}
				if(substr($deliveryNumber, -2) == '0'){
					$deliveryNumber = '0' . $deliveryNumber;
				}

				$array = array();


				if($suborderType == 'H'){
					$sql2 = "SELECT instruction, (SELECT serialnumber FROM labels WHERE label = tblcontractdetails.instruction limit 1) as serial FROM leermiddel.tblcontractdetails WHERE lengte = '" . $row3["orderid"] . "." . $_POST["deliverynumber"] . "'";
					$result2 = $conn->query($sql2);

					if ($result2->num_rows > 0) {
						while($row2 = $result2->fetch_assoc()) {
							array_push($array, array($row2["instruction"], $row2["serial"]) );
						}
					}
				} else {
					$sql2 = "SELECT label, serialnumber FROM labels WHERE orderid = '" . $row3["orderid"] . "'";
					$result2 = $conn->query($sql2);

					if ($result2->num_rows > 0) {
						while($row2 = $result2->fetch_assoc()) {
							array_push($array, array($row2["label"], $row2["serialnumber"]) );
						}
					} else {
						echo "0 results";
					}

					if($row3["delivery_number_number"] == 1){
						$array = array_slice($array, 0, $row3["deliveryamount"]);
					} elseif($row3["amountafter"] !== 0){
						$array = array_slice($array, $row3["amountbefore"], $row3["deliveryamount"]);
					}

					if(count($array) != $row3["amount"]){
						echo '<p style="color:red;">Er is iets mis! Te weinig serienummers!!</p>';
						die();
					}
				}

				foreach($array as $key){
					$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row3['SPSKU']))));
					$i++;
					$fileContent .= '      <Line number="' . $i . '">' . "\n";
					$fileContent .= '          <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          <Item>' . "\n";
					$fileContent .= '          <Code>' . $spsku . '</Code>' . "\n";
					$fileContent .= '          </Item>' . "\n";
					$fileContent .= '          <Quantity>1</Quantity>' . "\n";
					$fileContent .= '              <ToBeFulfilled>0</ToBeFulfilled>' . "\n";
					$fileContent .= '              <GenerateCreditNote>1</GenerateCreditNote>' . "\n";
					$fileContent .= '                   <Serialnumber>' . $key[0] . '</Serialnumber>' . "\n";
					$fileContent .= '                   <Description>' . $key[1] . '</Description>' . "\n";
					$fileContent .= '          <Warehouse>' . "\n";

					if($row3['warehouse'] == 'Signpost'){
						$fileContent .= '              <Code>700</Code>' . "\n";
					} else {
						$fileContent .= '              <Code>100</Code>' . "\n";
					}

					if($_POST['location'] !== ''){
						$fileContent .= '              <Location>' . $_POST['location'] . '</Location>' . "\n";
					} elseif($row3['warehouse'] == 'Copaco'){
						$fileContent .= '              <Location>COPA</Location>' . "\n";
					} elseif($row3['warehouse'] == 'TechData'){
						$fileContent .= '              <Location>DROP</Location>' . "\n";
					} else {
						$fileContent .= '              <Location></Location>' . "\n";
					}

					$fileContent .= '          </Warehouse>' . "\n";
					$fileContent .= '      </Line>' . "\n";
				}

			}
		} else {
			echo "0 results, er is iets fout gelopen. <br>";
		}

		$fileContent .= '  </Logistic>' . "\n";
		$fileContent .= '</Logistics>' . "\n";
		$fileContent .= '</eExact>' . "\n";

		$fileContent = $fileContent . PHP_EOL;
		$filename = '\\\\spb-srv-exact\c$\ExactXML\XML5_ship_lev\5_SP_BYOD20_' . $orderid . '_' . $suborderType . '' . $deliveryNumber . '_lev.xml';
		file_put_contents($filename, $fileContent);

	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

} elseif($_GET['type'] == '4'){

		echo '<h1>XML 4</h1><form action="generateXML.php" method="post">
			<input type="text" id="type" name="type" value="4" hidden class="form-control"><br>
			<label for="id">Suborder:</label><br>
			<input type="text" id="id" name="id" value="' . $_GET['id'] . '" readonly class="form-control"><br>
			<label for="orderid">Order:</label><br>
			<input type="text" id="orderid" name="orderid" value="' . $_GET['orderid'] . '" readonly class="form-control"><br>
			<label for="date">Datum:</label><br>
			<p class="smalltext">Mag leeg zijn, dan is het de leverdatum. In te geven in de vorm YYYY-MM-DD.</p>
			<input type="text" id="date" name="date" value="" class="form-control"><br>
			<label for="factuurdebiteur">Factuurdebiteur:</label><br>
			<input type="text" id="factuurdebiteur" name="factuurdebiteur" class="form-control"><br>
			<label for="factuurref">Factuur referentie:</label><br>
			<input type="text" id="factuurref" name="factuurref" class="form-control"><br>
			<label for="verkoopprijs">Verkoopprijs:</label>
			<p class="smalltext">Heeft enkel effect als het een -H.. suborder is.</p>
			<select name="verkoopprijs" id="verkoopprijs" class="form-control">
				<option value=""></option>
				<option value="Leermiddel">Leermiddel - Verkoopprijs = (Verkoopprijs uit Forecast)/1,21 en  BTWcode = 0</option>
				<option value="Leasemij">Leasemij - Verkoopprijs  = Kostprijs uit artikelfiche en BTWcode = 5</option>
			</select><br>
			<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

} elseif($_POST['type'] == '4'){

	// XML 4

	$sql = "UPDATE orders SET xmlstate='4' WHERE id= '" . $_GET['id'] . "' AND xmlstate = 2";
	if ($conn->query($sql) === TRUE) {
		// OK
	}

	$fileContent = '';
	$sql = "UPDATE delivery SET exact_generated = '1'
	WHERE id = '" . $_POST['id'] . "'";

	if ($conn->query($sql) === TRUE) {

		echo '<div>
			<a href="" class="btn btn-primary" id="download_link" download="export.xml" style="display:none;">Export XML</a>
			<p>XML 4 export gelukt</p>
			<a href="'. hasAccessForUrl('delivery.php?orderid=' . $_POST['orderid'] . '', false).'" class="btn btn-primary" id="download_link">Ga terug naar het overzicht</a>
		</div>';

		$fileContent .= '<?xml version="1.0" ?>' . "\n";
		$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
		$fileContent .= '<Orders>' . "\n";

		$sql = "SELECT *, orders.id as orderid, delivery.id as deliveryid, delivery.amount as suborderamount, delivery.type as deliverytype, devices.productnumber as deviceproductnumber,
			( SELECT id FROM orderpicking ORDER BY id DESC LIMIT 1 ) as lastpick,
			IFNULL(( SELECT `device1-price` FROM forecasts WHERE REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(`device1-spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') = REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(orders.`spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') AND synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ),
			IFNULL(( SELECT `device2-price` FROM forecasts WHERE REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(`device2-spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') = REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(orders.`spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') AND synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ),
			IFNULL(( SELECT `device3-price` FROM forecasts WHERE REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(`device3-spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') = REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(orders.`spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') AND synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ),
			IFNULL(( SELECT `device4-price` FROM forecasts WHERE REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(`device4-spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') = REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(orders.`spsku`, ';', 1), '-O', ''), '-B1', ''), '-B2', '') AND synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ),
			devices.default_price)))) as deviceprice,
			( SELECT sku FROM `device-parts` WHERE spsku = orders.panelswap LIMIT 1 ) as panelswappart,
			( SELECT sku FROM `device-parts` WHERE spsku = orders.ssdswap LIMIT 1 ) as ssdswappart,
			( SELECT sku FROM `device-parts` WHERE spsku = orders.memoryswap LIMIT 1 ) as memoryswappart,
			( SELECT sku FROM `device-parts` WHERE spsku = orders.memoryswap2 LIMIT 1 ) as memoryswappart2,
			( SELECT sku FROM `device-parts` WHERE spsku = orders.keyboardswap LIMIT 1 ) as keyboardswappart
			FROM orders
			LEFT JOIN schools ON schools.synergyid = orders.synergyid
			LEFT JOIN devices ON devices.spsku = orders.spsku
			LEFT JOIN delivery ON delivery.orderid = orders.id
			WHERE delivery.id = '" . $_POST['id'] . "' and orders.deleted != 1";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {

				$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row['SPSKU']))));
				$price = '';
				$vat = '5I';
				$tsql= "SELECT CostPriceStandard FROM Items with (nolock) WHERE Items.Type = 'S' AND ItemCode like '" . $spsku . "'";
				$stmt = sqlsrv_query( $msconn, $tsql);

				if($stmt === false) {
					die( print_r( sqlsrv_errors(), true) );
				}

				while( $row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
					if($_POST['verkoopprijs'] == 'Leermiddel'){
						$price = round($row['deviceprice']/1.21, 3);
						$vat = '0';
					} elseif($_POST['verkoopprijs'] == 'Leasemij'){
						$price = $row2['CostPriceStandard'];
						$vat = '5';
					} else {
						$price = $row['deviceprice'];
						$vat = '5';
					}
				}

				if($row['deliverytype'] != 'H'){
					$price = $row['deviceprice'];
				}

				if($_POST['date'] != ''){
					$date = $_POST['date'];
				} else {
					$date = date("Y-m-d", strtotime($row['shipping_date']));
				}

				if($_POST['factuurdebiteur'] != '' && $row['deliverytype'] == 'H'){
					$debtor1 = $_POST['factuurdebiteur'];
					$debtor2 = $row['synergyid'];
				} elseif($_POST['factuurdebiteur'] != ''){
					$debtor1 = $_POST['factuurdebiteur'];
					$debtor2 = $_POST['factuurdebiteur'];
				} else {
					$debtor1 = $row['synergyid'];
					$debtor2 = $row['synergyid'];
				}

				$lastpick = 30000000 + $row['id'];
				$fullOrderNumber = 'SP-BYOD20-' . $row['orderid'] . '-' . $row['type'] . '' . $row['deliveryid'];
				// Eerste loop
				$fileContent .= '  <Order type="' . $row['exact_ordertype'] . '" number="' . $lastpick . '" partialdelivery="' . $row['exact_partialdelivery'] . '" confirm="' . $row['exact_confirm'] . '" invoicemethod="' . $row['exact_invoicemethod'] . '">' . "\n";
				$fileContent .= '      <Description>' . $row['synergyid'] . ' - ' . $row['school_name'] . '</Description>' . "\n";
				$fileContent .= '      <YourRef>SP-BYOD20-' . $row['orderid'] . '-' . $row['type'] . '' . $row['deliveryid'] . '</YourRef>' . "\n";
				$fileContent .= '      <CalcIncludeVAT></CalcIncludeVAT>' . "\n";
				$fileContent .= '      <OrderedBy>' . "\n";
				$fileContent .= '          <Debtor code="' . $debtor2 . '" />' . "\n";
				$fileContent .= '      <Date>' . $date . '</Date>' . "\n";
				$fileContent .= '      </OrderedBy>' . "\n";
				$fileContent .= '      <DeliverTo>' . "\n";
				$fileContent .= '          <Debtor code="' . $debtor2 . '" />' . "\n";
				$fileContent .= '      <Date>' . $date . '</Date>' . "\n";
				$fileContent .= '      </DeliverTo>' . "\n";
				$fileContent .= '      <InvoiceTo>' . "\n";
				$fileContent .= '          <Debtor code="' . $debtor1 . '" />' . "\n";
				$fileContent .= '      </InvoiceTo>' . "\n";

				if($row['warehouse'] == 'Signpost'){
					$fileContent .= '      <Warehouse code="700"></Warehouse>' . "\n";
				} else {
					$fileContent .= '      <Warehouse code="100"></Warehouse>' . "\n";
				}

				if($row['warehouse'] == 'Copaco'){
					$fileContent .= '      <Selection code="PC" />' . "\n";
				} elseif($row['warehouse'] == 'TechData'){
					$fileContent .= '      <Selection code="PT" />' . "\n";
				} else {
					$fileContent .= '      <Selection code="PL" />' . "\n";
				}

				// Tweede loop
				$i = 0;

				$i++;
				$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
				$fileContent .= '          <Item code="' . $spsku . '" />' . "\n";
				$fileContent .= '          <Quantity>' . $row['suborderamount'] . '</Quantity>' . "\n";
				$fileContent .= '          <Delivery>' . "\n";
				$fileContent .= '              <Date>' . $date . '</Date>' . "\n";
				$fileContent .= '          </Delivery>' . "\n";
				$fileContent .= '          <Price type="S">' . "\n";
				$fileContent .= '              <Currency code="EUR" />' . "\n";
				$fileContent .= '              <Value>' . $price . '</Value>' . "\n";

				if($row['deliverytype'] == 'H'){
					$fileContent .= '              <VAT code="' . $vat . '" />' . "\n";
				} else {
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
				}

				$fileContent .= '          </Price>' . "\n";
				$fileContent .= '      </OrderLine>' . "\n";

				$fileContent .= '  </Order>' . "\n";
			}
		} else {
			echo "0 results";
		}

		$fileContent .= '</Orders>' . "\n";
		$fileContent .= '</eExact>' . "\n";

		$fileContent = $fileContent . PHP_EOL;
		$filename = '\\\\spb-srv-exact\c$\ExactXML\XML4_ship\4_' . str_replace('-', '_', $fullOrderNumber) . '_ord.xml';
		file_put_contents($filename, $fileContent);

	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

} elseif($_GET['type'] == 'moving'){

	echo '<h1>XML 2</h1>';
	echo '<form action="generateXML.php" method="post">
		<input type="text" id="type" name="type" value="2" hidden class="form-control">';

		if($_GET['id'] == 'alles'){
			echo '<label for="order">Order:</label><br>
			<input type="text" id="order" name="order" value="alles" readonly class="form-control"><br>
			<label for="date">Datum:</label><br>
			<input type="text" id="date" name="date" value="' . date('Y-m-d') . '" class="form-control"><br>';
		} else {
			echo '<input type="text" id="pickingid" name="pickingid" value="' . $_GET['pickingid'] . '" hidden class="form-control">
			<input type="text" id="amount" name="amount" value="' . $_GET['amount'] . '" hidden class="form-control">
			<label for="order">Order:</label><br>
			<input type="text" id="order" name="order" value="' . $_GET['id'] . '" readonly class="form-control"><br>
			<label for="date">Datum:</label><br>
			<input type="text" id="date" name="date" value="' . date('Y-m-d') . '" class="form-control"><br>';

			$warehouse = $_GET['warehouse'];
			if($warehouse == 'TechData' || $warehouse == 'Copaco'){
				$warehouseloc = '100';
			} else {
				$warehouseloc = '700';
			}
			$options = '';
			$tsql= "SELECT maglok from evloc with (nolock) WHERE magcode = '" . $warehouseloc . "'";
			$stmt = sqlsrv_query( $msconn, $tsql);

			if($stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}

			while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
				$options .= '<option value="' . $row['maglok'] . '">' . $row['maglok'] . '</option>';
			}

			$sql = "SELECT * FROM orders
				LEFT JOIN devices ON devices.spsku = orders.spsku
				WHERE orders.id = '" . $_GET['id'] . "' and orders.deleted != 1 LIMIT 1";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					echo 'Locatie toestel (' . $row['productnumber'] . '):';
					echo '<select class="form-control" name="skulocation" id="skulocation">';
					echo $options;
					echo '</select><br>';

					echo 'Locatie hoes (' . $row['covers'] . '):';
					echo '<select class="form-control" name="coverlocation" id="coverlocation">';
					echo $options;
					echo '</select><br>';

					if ($row['ssdswap'] !== "" && isset($row['ssdswap']) == true) {
						echo 'Locatie SSD (' . $row['ssdswap'] . '):';
						echo '<select class="form-control" name="ssdlocation" id="ssdlocation">';
						echo $options;
						echo '</select><br>';
					}

					if ($row['memoryswap'] !== "" && isset($row['memoryswap']) == true) {
						echo 'Locatie Ram 1 (' . $row['memoryswap'] . '):';
						echo '<select class="form-control" name="ramlocation" id="ramlocation">';
						echo $options;
						echo '</select><br>';
					}

					if ($row['memoryswap2'] !== "" && isset($row['memoryswap2']) == true) {
						echo 'Locatie Ram 2 (' . $row['memoryswap2'] . '):';
						echo '<select class="form-control" name="ram2location" id="ram2location">';
						echo $options;
						echo '</select><br>';
					}

					if ($row['panelswap'] !== "" && isset($row['panelswap']) == true) {
						echo 'Locatie Panel (' . $row['panelswap'] . '):';
						echo '<select class="form-control" name="panellocation" id="panellocation">';
						echo $options;
						echo '</select><br>';
					}

					if ($row['keyboardswap'] !== "" && isset($row['keyboardswap']) == true) {
						echo 'Locatie Keyboard (' . $row['keyboardswap'] . '):';
						echo '<select class="form-control" name="keyboardlocation" id="keyboardlocation">';
						echo $options;
						echo '</select><br>';
					}

					echo 'Locatie afgewerkt toestel (' . $row['SPSKU'] . '):';
					echo '<select class="form-control" name="finallocation" id="finallocation">';
					echo $options;
					echo '</select><br>';

					echo '<br>';

				}
			} else {
				echo "0 results";
			}
		}

	echo '<input type="submit" value="Submit">
	</form>';

} elseif($_POST['type'] == '2'){

	// XML 2

	echo '<div>
		<a href="" class="btn btn-primary" id="download_link" download="export.xml" style="display:none;">Export XML</a>
		<p>Move export (XML 2) gelukt</p>
		<a href="'. hasAccessForUrl('ombouw-scholen.php', false).'" class="btn btn-primary" id="download_link">Ga terug naar het overzicht</a>
	</div>';

	if($_POST['order'] == 'alles'){
		$sql = "select *, id AS orderid, (SELECT id FROM orderpicking WHERE orderid = orders.id) as pickingid from orders where xmlstate = '1'";
	} else {
		$sql = "select *, id AS orderid, (SELECT id FROM orderpicking WHERE orderid = orders.id) as pickingid from orders where id = '" . $_POST['order'] . "'";
	}
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$fileContent = '';
			$fileContent .= '<?xml version="1.0" ?>' . "\n";
			$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
			$fileContent .= '<Logistics>' . "\n";
			$fileContent .= '<Logistic type="D">' . "\n";

			$orderamount = '';
			$error = '';
			$i = 0;
			$pickingid = 20000000 + $row['pickingid'];
			if(isset($_POST['date']) == true && $_POST['date'] !== ''){
				$date = $_POST['date'];
			} else {
				$date = $row['shipping_date'];
			}

			if($_POST['order'] !== 'alles'){
				$skulocation = $_POST['skulocation'];
				$coverlocation = $_POST['coverlocation'];
				$panellocation = $_POST['panellocation'];
				$ssdlocation = $_POST['ssdlocation'];
				$ramlocation = $_POST['ramlocation'];
				$ram2location = $_POST['ram2location'];
				$keyboardlocation = $_POST['keyboardlocation'];
				$finallocation = $_POST['finallocation'];
				$orderid = $row['orderid'];
				$orderamount = $row['amount'];
			} else {
				if($row['warehouse'] == 'TechData'){
					$skulocation = 'DROP';
					$coverlocation = 'DROP';
					$panellocation = 'DROP';
					$ssdlocation = 'DROP';
					$ramlocation = 'DROP';
					$ram2location = 'DROP';
					$keyboardlocation = 'DROP';
					$finallocation = 'DROP';
				} else if($row['warehouse'] == 'Copaco'){
					$skulocation = 'COPA';
					$coverlocation = 'COPA';
					$panellocation = 'COPA';
					$ssdlocation = 'COPA';
					$ramlocation = 'COPA';
					$ram2location = 'COPA';
					$keyboardlocation = 'COPA';
					$finallocation = 'COPA';
				} else {
					$skulocation = '700';
					$coverlocation = '700';
					$panellocation = '700';
					$ssdlocation = '700';
					$ramlocation = '700';
					$ram2location = '700';
					$keyboardlocation = '700';
					$finallocation = '700';
				}
				$orderid = $row['orderid'];
				$orderamount = $row['amount'];
			}

			if($orderamount <= 0){
				$error .= 'Order ' . $orderid . ' heeft 0 toestellen en kan dus niet gegenereerd worden.<br>';
			}

			$sql6 = "UPDATE orders SET xmlstate='2' WHERE id= '" . $orderid . "' AND xmlstate = 1";
			if ($conn->query($sql6) === TRUE) {
				// OK
			}

			$sql5 = "SELECT *, orders.id as orderid,
				( SELECT GROUP_CONCAT(serialnumber) FROM `labels` WHERE orderid = orders.id ) as serialnumbers
				FROM orders
				LEFT JOIN schools ON schools.synergyid = orders.synergyid
				LEFT JOIN devices ON devices.spsku = orders.spsku
				WHERE orders.id ='" . $orderid . "' and orders.deleted != 1";
			$result5 = $conn->query($sql5);

			if ($result5->num_rows > 0) {
				while($row5 = $result5->fetch_assoc()) {

					$orderid = $row5['orderid'];
					$serialarr = explode(',', $row5['serialnumbers']);
					$warehouse = $row5['warehouse'];

					foreach($serialarr as $serialnumber){
						$i++;
						// for loop met serialnumbers
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '          <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '              <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '          <Item>' . "\n";
						$fileContent .= '          <Code>' . $row5['productnumber'] . '</Code>' . "\n";
						$fileContent .= '          </Item>' . "\n";
						$fileContent .= '          <Quantity>1</Quantity>' . "\n";
						$fileContent .= '                  <Serialnumber>' . $serialnumber . '</Serialnumber>' . "\n";
						$fileContent .= '          <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '              <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '              <Code>700</Code>' . "\n";
						}
						$fileContent .= '              <Location>' . $skulocation . '</Location>' . "\n";
						$fileContent .= '          </Warehouse>' . "\n";
						$fileContent .= '      </Line>' . "\n";
					}

				}
			} else {
				echo "0 results";
				$error .= 'Geen serienummers, kan order ' . $orderid . ' niet genereren.<br>';
			}

			$sql4 = "SELECT *, orders.id as orderid,
				ifnull(( SELECT sku FROM `device-parts` WHERE SPSKU = orders.panelswap LIMIT 1 ), '') as panelswapsku,
				ifnull(( SELECT sku FROM `device-parts` WHERE SPSKU = orders.ssdswap LIMIT 1 ), '') as ssdswapsku,
				ifnull(( SELECT sku FROM `device-parts` WHERE SPSKU = orders.memoryswap LIMIT 1 ), '') as memoryswapsku,
				ifnull(( SELECT sku FROM `device-parts` WHERE SPSKU = orders.memoryswap2 LIMIT 1 ), '') as memoryswap2sku,
				ifnull(( SELECT sku FROM `device-parts` WHERE SPSKU = orders.keyboardswap LIMIT 1 ), '') as keyboardswapsku
				FROM orders
				LEFT JOIN schools ON schools.synergyid = orders.synergyid
				WHERE orders.id ='" . $orderid . "' and orders.deleted != 1";
			$result4 = $conn->query($sql4);

			if ($result4->num_rows > 0) {
				while($row4 = $result4->fetch_assoc()) {
					if(stripos($row4['covers'], 'QNS') !== FALSE){
						$i++;
						$fileContent .= '		<Line number="' . $i . '">' . "\n";
						$fileContent .= '		  <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '		      <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '		  <Item>' . "\n";
						$fileContent .= '		  <Code>' . $row4['covers'] . '</Code>' . "\n";
						$fileContent .= '		  </Item>' . "\n";
						$fileContent .= '		  <Quantity>' . $row4['amount'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '		      <Location>' . $coverlocation . '</Location>' . "\n";
						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	     </Line>' . "\n";
					}

					if($row4['panelswapsku'] !== ''){
						$i++;
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '		  <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '		      <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '		  <Item>' . "\n";
						$fileContent .= '		  <Code>' . $row4['panelswapsku'] . '</Code>' . "\n";
						$fileContent .= '		  </Item>' . "\n";
						$fileContent .= '		  <Quantity>' . $row4['amount'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '		      <Location>' . $panellocation . '</Location>' . "\n";
						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	      </Line>' . "\n";
					}

					if($row4['ssdswapsku'] !== ''){
						$i++;
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '		  <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '		      <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '		  <Item>' . "\n";
						$fileContent .= '		  <Code>' . $row4['ssdswapsku'] . '</Code>' . "\n";
						$fileContent .= '		  </Item>' . "\n";
						$fileContent .= '		  <Quantity>' . $row4['amount'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '		      <Location>' . $ssdlocation . '</Location>' . "\n";
						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	      </Line>' . "\n";
					}

					if($row4['memoryswapsku'] !== ''){
						$i++;
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '		  <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '		      <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '		  <Item>' . "\n";
						$fileContent .= '		  <Code>' . $row4['memoryswapsku'] . '</Code>' . "\n";
						$fileContent .= '		  </Item>' . "\n";
						$fileContent .= '		  <Quantity>' . $row4['amount'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '		      <Location>' . $ramlocation . '</Location>' . "\n";
						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	      </Line>' . "\n";
					}

					if($row4['memoryswap2sku'] !== ''){
						$i++;
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '		  <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '		      <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '		  <Item>' . "\n";
						$fileContent .= '		  <Code>' . $row4['memoryswap2sku'] . '</Code>' . "\n";
						$fileContent .= '		  </Item>' . "\n";
						$fileContent .= '		  <Quantity>' . $row4['amount'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '		      <Location>' . $ram2location . '</Location>' . "\n";
						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	      </Line>' . "\n";
					}

					if($row4['keyboardswapsku'] !== ''){
						$i++;
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '		  <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '		      <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '		  <Item>' . "\n";
						$fileContent .= '		  <Code>' . $row4['keyboardswapsku'] . '</Code>' . "\n";
						$fileContent .= '		  </Item>' . "\n";
						$fileContent .= '		  <Quantity>' . $row4['amount'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '		      <Location>' . $keyboardlocation . '</Location>' . "\n";
						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	      </Line>' . "\n";
					}

				}
			} else {
				echo "0 results";
			}


			$sql3 = "SELECT *, orders.id as orderid,
				( SELECT GROUP_CONCAT(concat(label, ';', serialnumber)) FROM `labels` WHERE orderid = orders.id ) as labelnumbers
				FROM orders
				LEFT JOIN schools ON schools.synergyid = orders.synergyid
				LEFT JOIN devices ON devices.spsku = orders.spsku
				WHERE orders.id ='" . $orderid . "' and orders.deleted != 1";
			$result3 = $conn->query($sql3);

			if ($result3->num_rows > 0) {
				while($row3 = $result3->fetch_assoc()) {

					$labelarr = explode(',', $row3['labelnumbers']);
					foreach($labelarr as $labelnumber){
						$comparr = explode(';', $labelnumber);
						$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row3['SPSKU']))));
						$i++;
						$fileContent .= '      <Line number="' . $i . '">' . "\n";
						$fileContent .= '          <OrderNumber>' . $pickingid . '</OrderNumber>' . "\n";
						$fileContent .= '              <Date>' . $date . '</Date>' . "\n";
						$fileContent .= '          <Item>' . "\n";
						$fileContent .= '          <Code>' . $spsku . '</Code>' . "\n";
						$fileContent .= '          </Item>' . "\n";
						$fileContent .= '          <Quantity>-1</Quantity>' . "\n";
						$fileContent .= '              <ToBeFulfilled>0</ToBeFulfilled>' . "\n";
						$fileContent .= '              <GenerateCreditNote>1</GenerateCreditNote>' . "\n";
						$fileContent .= '                   <Serialnumber>' . $comparr[0] . '</Serialnumber>' . "\n";
						$fileContent .= '                   <Description>' . $comparr[1] . '</Description>' . "\n";
						$fileContent .= '          <Warehouse>' . "\n";
						if($warehouse == 'TechData' || $warehouse == 'Copaco'){
							$fileContent .= '		      <Code>100</Code>' . "\n";
						} else {
							$fileContent .= '		      <Code>700</Code>' . "\n";
						}
						$fileContent .= '              <Location>' . $finallocation . '</Location>' . "\n";
						$fileContent .= '          </Warehouse>' . "\n";
						$fileContent .= '      </Line>' . "\n";
					}

				}
			} else {
				echo "0 results";
			}

			$fileContent .= '  </Logistic>' . "\n";
			$fileContent .= '</Logistics>' . "\n";
			$fileContent .= '</eExact>' . "\n";

			if($error == ''){
				$fileContent = $fileContent . PHP_EOL;
				$filename = '\\\\spb-srv-exact\c$\ExactXML\XML2_prod_lev\2_SP_BYOD20_' . str_replace('-', '_', $orderid). '_lev.xml';
				file_put_contents($filename, $fileContent);
			} else {
				echo $error;
			}

		}
	}

} elseif(isset($_POST['amount']) == true){

	// XML 1

	$fileContent = '';
	$alles = '';

	if($_POST['order'] != 'alles'){
		$sql = "UPDATE orders SET xmlstate='1' WHERE id= '" . $_GET['id'] . "' AND xmlstate = 0";
		if ($conn->query($sql) === TRUE) {
			// OK
		}

		$sql = "INSERT INTO orderpicking (orderid, amount)
			SELECT * FROM ( SELECT '" . $_POST['order'] . "', '" . $_POST['amount'] . "') as tmp
			WHERE NOT EXISTS (
				SELECT orderid FROM orderpicking WHERE orderid = '" . $_POST['order'] . "'
			) LIMIT 1";
	} else {
		$sql = 'select 1';
		$alles = 'true';
	}

	if ($conn->query($sql) === TRUE || $alles == 'true') {

		echo '<div>
			<a href="" class="btn btn-primary" id="download_link" download="export.xml" style="display:none;">Export XML</a>
			<p>XML 1 export gelukt</p>
			<a href="'. hasAccessForUrl('ombouw-scholen.php', false).'" class="btn btn-primary" id="download_link">Ga terug naar het overzicht</a>
			</div>';

		$fileContent .= '<?xml version="1.0" ?>' . "\n";
		$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
		$fileContent .= '<Orders>' . "\n";

		if($_POST['order'] == 'alles'){
			$sql = "SELECT *, orders.id as orderid,
				( SELECT id FROM orderpicking WHERE orderid = orders.id AND amount = orders.amount ORDER BY id DESC LIMIT 1 ) as lastpickfromorder,
				( SELECT id FROM orderpicking ORDER BY id DESC LIMIT 1 ) as lastpick,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.panelswap LIMIT 1 ) as panelswappart,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.ssdswap LIMIT 1 ) as ssdswappart,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.memoryswap LIMIT 1 ) as memoryswappart,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.memoryswap2 LIMIT 1 ) as memoryswappart2,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.keyboardswap LIMIT 1 ) as keyboardswappart
				FROM orders
				LEFT JOIN schools ON schools.synergyid = orders.synergyid
				LEFT JOIN devices ON devices.spsku = orders.spsku
				WHERE ifnull(( SELECT id FROM orderpicking WHERE orderid = orders.id ORDER BY id DESC LIMIT 1 ), 0)=0 and orders.deleted != 1";
		} else {
			$sql = "SELECT *, orders.id as orderid,
				( SELECT id FROM orderpicking WHERE orderid = orders.id AND amount = orders.amount ORDER BY id DESC LIMIT 1 ) as lastpickfromorder,
				( SELECT id FROM orderpicking ORDER BY id DESC LIMIT 1 ) as lastpick,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.panelswap LIMIT 1 ) as panelswappart,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.ssdswap LIMIT 1 ) as ssdswappart,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.memoryswap LIMIT 1 ) as memoryswappart,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.memoryswap2 LIMIT 1 ) as memoryswappart2,
				( SELECT sku FROM `device-parts` WHERE spsku = orders.keyboardswap LIMIT 1 ) as keyboardswappart
				FROM orders
				LEFT JOIN schools ON schools.synergyid = orders.synergyid
				LEFT JOIN devices ON devices.spsku = orders.spsku
				WHERE orders.id ='" . $_POST['order'] . "' and orders.deleted != 1";
		}
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {

				$newnumber = 0;
				if($_POST['order'] == 'alles'){
					$sql = "UPDATE orders SET xmlstate='1' WHERE id= '" . $row['orderid'] . "' AND xmlstate = 0";
					if ($conn->query($sql) === TRUE) {
						// OK
					}
					$sql = "INSERT INTO orderpicking (orderid, amount)
						SELECT * FROM ( SELECT '" . $row['orderid'] . "', '" . $row['amount'] . "') as tmp
						WHERE NOT EXISTS (
							SELECT orderid FROM orderpicking WHERE orderid = '" . $row['orderid'] . "'
					) LIMIT 1";
					if ($conn->query($sql) === TRUE) {
						// OK
						$newnumber = $conn->insert_id;
					}
				}

				if($newnumber !== 0){
					$lastpick = 20000000 + $newnumber;
				} elseif($row['lastpickfromorder'] !== ''){
					$lastpick = 20000000 + $row['lastpickfromorder'];
				} else {
					$lastpick = 20000000 + $row['lastpick'];
				}
				$orderid = $row['orderid'];
				$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row['SPSKU']))));
				// Eerste loop
				$fileContent .= '  <Order type="' . $row['exact_ordertype'] . '" number="' . $lastpick . '" partialdelivery="' . $row['exact_partialdelivery'] . '" confirm="' . $row['exact_confirm'] . '" invoicemethod="' . $row['exact_invoicemethod'] . '">' . "\n";
				$fileContent .= '      <Description>' . $row['synergyid'] . ' - ' . $row['school_name'] . '</Description>' . "\n";
				$fileContent .= '      <YourRef>SP-BYOD20-' . $row['orderid'] . '</YourRef>' . "\n";
				$fileContent .= '      <CalcIncludeVAT></CalcIncludeVAT>' . "\n";
				$fileContent .= '      <OrderedBy>' . "\n";
				$fileContent .= '          <Debtor code="' . $row['exact_debtor'] . '" />' . "\n";
				$fileContent .= '      <Date>' . $_POST['date'] . '</Date>' . "\n";
				$fileContent .= '      </OrderedBy>' . "\n";
				$fileContent .= '      <DeliverTo>' . "\n";
				$fileContent .= '          <Debtor code="' . $row['exact_debtor'] . '" />' . "\n";
				$fileContent .= '      <Date>' . $_POST['date'] . '</Date>' . "\n";
				$fileContent .= '      </DeliverTo>' . "\n";
				$fileContent .= '      <InvoiceTo>' . "\n";
				$fileContent .= '          <Debtor code="' . $row['exact_debtor'] . '" />' . "\n";
				$fileContent .= '      </InvoiceTo>' . "\n";

				if($row['warehouse'] == 'Signpost'){
					$fileContent .= '      <Warehouse code="700"></Warehouse>' . "\n";
				} else {
					$fileContent .= '      <Warehouse code="100"></Warehouse>' . "\n";
				}

				//$fileContent .= '      <Selection code="' . $row['exact_selectioncode'] . '" />' . "\n";

				if($row['warehouse'] == 'Copaco'){
					$fileContent .= '      <Selection code="PC" />' . "\n";
				} elseif($row['warehouse'] == 'TechData'){
					$fileContent .= '      <Selection code="PT" />' . "\n";
				} else {
					$fileContent .= '      <Selection code="PL" />' . "\n";
				}

				// Tweede loop
				$i = 0;

				$i++;
				$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
				$fileContent .= '          <Item code="' . $row['productnumber'] . '" />' . "\n";
				if($_POST['order'] == 'alles'){
					$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
				} else {
					$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
				}
				$fileContent .= '          <Delivery>' . "\n";
				$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
				$fileContent .= '          </Delivery>' . "\n";
				$fileContent .= '          <Price type="S">' . "\n";
				$fileContent .= '              <Currency code="EUR" />' . "\n";
				$fileContent .= '              <Value>0</Value>' . "\n";
				$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
				$fileContent .= '          </Price>' . "\n";
				$fileContent .= '      </OrderLine>' . "\n";

				if(stripos($row['covers'], 'QNS') !== FALSE){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['covers'] . '" />' . "\n";
					if($_POST['order'] == 'alles'){
						$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
					} else {
						$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
					}
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

				if(isset($row['panelswappart']) == true){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['panelswappart'] . '" />' . "\n";
					if($_POST['order'] == 'alles'){
						$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
					} else {
						$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
					}
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

				if(isset($row['ssdswappart']) == true){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['ssdswappart'] . '" />' . "\n";
					if($_POST['order'] == 'alles'){
						$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
					} else {
						$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
					}
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

				if(isset($row['memoryswappart']) == true){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['memoryswappart'] . '" />' . "\n";
					if($_POST['order'] == 'alles'){
						$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
					} else {
						$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
					}
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";;
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

				if(isset($row['memoryswappart2']) == true){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['memoryswappart2'] . '" />' . "\n";
					if($_POST['order'] == 'alles'){
						$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
					} else {
						$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
					}
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

				if(isset($row['keyboardswappart']) == true){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['keyboardswappart'] . '" />' . "\n";
					if($_POST['order'] == 'alles'){
						$fileContent .= '          <Quantity>' . $row['amount'] . '</Quantity>' . "\n";
					} else {
						$fileContent .= '          <Quantity>' . $_POST['amount'] . '</Quantity>' . "\n";
					}
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

				$i++;
				$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
				$fileContent .= '          <Item code="' . $spsku . '" />' . "\n";
				if($_POST['order'] == 'alles'){
					$fileContent .= '          <Quantity>-' . $row['amount'] . '</Quantity>' . "\n";
				} else {
					$fileContent .= '          <Quantity>-' . $_POST['amount'] . '</Quantity>' . "\n";
				}
				$fileContent .= '          <Delivery>' . "\n";
				$fileContent .= '              <Date>' . $_POST['date'] . '</Date>' . "\n";
				$fileContent .= '          </Delivery>' . "\n";
				$fileContent .= '          <Price type="S">' . "\n";
				$fileContent .= '              <Currency code="EUR" />' . "\n";
				$fileContent .= '              <Value>0</Value>' . "\n";
				$fileContent .= '              <VAT code="' . $row['exact_vat'] . '" />' . "\n";
				$fileContent .= '          </Price>' . "\n";
				$fileContent .= '      </OrderLine>' . "\n";

				$fileContent .= '  </Order>' . "\n";
			}
		} else {
			//echo "0 results, er is iets fout gelopen. <br>";
		}

		$fileContent .= '</Orders>' . "\n";
		$fileContent .= '</eExact>' . "\n";

		$fileContent = $fileContent . PHP_EOL;
		if($_POST['order'] == 'alles'){
			$filename = '\\\\spb-srv-exact\c$\ExactXML\XML1_prod\1_SP_BYOD20_alles_ord.xml';
		} else {
			$filename = '\\\\spb-srv-exact\c$\ExactXML\XML1_prod\1_SP_BYOD20_' . $orderid . '_ord.xml';
		}
		file_put_contents($filename, $fileContent);

	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	if($_POST['type'] == 'technician'){
		// redirect
		$URL = 'ombouw-orders.php';
		if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
		else { header("Location: $URL"); }
		exit;
	}


	} else {

		echo '<form action="generateXML.php" method="post">';
		echo '<input type="text" id="type" name="type" value="' . $_GET['type'] . '" hidden class="form-control">';
		if($_GET['id'] == 'alles'){
			echo '<label for="order">Order:</label><br>
			<input type="text" id="order" name="order" value="alles" readonly class="form-control"><br>
			<label for="amount">Aantal:</label><br>
			<input type="text" id="amount" name="amount" value="alles" readonly class="form-control"><br>';
		} else {
			echo '<label for="order">Order:</label><br>
			<input type="text" id="order" name="order" value="' . $_GET['id'] . '" readonly class="form-control"><br>
			<label for="amount">Aantal:</label><br>
			<input type="number" id="amount" name="amount" value="' . $_GET['amount'] . '" class="form-control"><br>';
		}
		echo '<label for="date">Datum:</label><br>
			<input type="text" id="date" name="date" value="' . date('Y-m-d') . '" class="form-control"><br>
			<input type="submit" id="submitBtn" value="Submit">
		</form>';

		if($_GET['type'] == 'technician'){
			// redirect
			echo "<script>$('#submitBtn').click();</script>";
		}

	}
echo '</div>';

?>
