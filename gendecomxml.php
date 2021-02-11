<?php
$title = 'XML Generator';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

if(isset($_GET['1']) == true){

	// XML 1

	$fileContent = '';
	$alles = '';

	// $sql = "UPDATE decomission SET removed_from_exact='1' WHERE removed_from_exact is null";

	//if ($conn->query($sql) === TRUE) {

		echo '<div>
			<p>XML export gelukt</p>
			</div>';

		$fileContent .= '<?xml version="1.0" ?>' . "\n";
		$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
		$fileContent .= '<Orders>' . "\n";


		$lastpick = 20999997;
		$fileContent .= '  <Order type="V" number="' . $lastpick . '" partialdelivery="1" confirm="0" invoicemethod="H">' . "\n";
		$fileContent .= '      <Description>Omschrijving Decommissionering SP2 20200104</Description>' . "\n";
		$fileContent .= '      <YourRef>SP-BYOD20-DECOM</YourRef>' . "\n";
		$fileContent .= '      <CalcIncludeVAT></CalcIncludeVAT>' . "\n";
		$fileContent .= '      <OrderedBy>' . "\n";
		$fileContent .= '          <Debtor code="100" />' . "\n";
		$fileContent .= '      <Date>2020-01-04</Date>' . "\n";
		$fileContent .= '      </OrderedBy>' . "\n";
		$fileContent .= '      <DeliverTo>' . "\n";
		$fileContent .= '          <Debtor code="100" />' . "\n";
		$fileContent .= '      <Date>2020-01-04</Date>' . "\n";
		$fileContent .= '      </DeliverTo>' . "\n";
		$fileContent .= '      <InvoiceTo>' . "\n";
		$fileContent .= '          <Debtor code="100" />' . "\n";
		$fileContent .= '      </InvoiceTo>' . "\n";

		$fileContent .= '      <Warehouse code="700"></Warehouse>' . "\n";

		$fileContent .= '      <Selection code="PL" />' . "\n";


		$sql = "SELECT COUNT(*) AS aantal, orders.spsku as SPSKU, productnumber, covers
				FROM decomissioned
				LEFT JOIN orders ON orders.id = decomissioned.orderid
				LEFT JOIN devices ON devices.spsku = orders.spsku
				WHERE removed_from_exact IS NULL
				GROUP BY orders.spsku";
		$result = $conn->query($sql);

		$i = 0;
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {

				$newnumber = 0;

				$test = '';

				$spsku = strtoupper(str_replace('-O', '', str_replace('-B1', '', str_replace('-B2', '', $row['SPSKU']))));

				$i++;
				$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
				$fileContent .= '          <Item code="' . $spsku . '" />' . "\n";
				$fileContent .= '          <Quantity>' . $row['aantal'] . '</Quantity>' . "\n";
				$fileContent .= '          <Delivery>' . "\n";
				$fileContent .= '              <Date>2020-01-04</Date>' . "\n";
				$fileContent .= '          </Delivery>' . "\n";
				$fileContent .= '          <Price type="S">' . "\n";
				$fileContent .= '              <Currency code="EUR" />' . "\n";
				$fileContent .= '              <Value>0</Value>' . "\n";
				$fileContent .= '              <VAT code="5I" />' . "\n";
				$fileContent .= '          </Price>' . "\n";
				$fileContent .= '      </OrderLine>' . "\n";

				$i++;
				$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
				$fileContent .= '          <Item code="' . $row['productnumber'] . '" />' . "\n";
				$fileContent .= '          <Quantity>-' . $row['aantal'] . '</Quantity>' . "\n";
				$fileContent .= '          <Delivery>' . "\n";
				$fileContent .= '              <Date>2020-01-04</Date>' . "\n";
				$fileContent .= '          </Delivery>' . "\n";
				$fileContent .= '          <Price type="S">' . "\n";
				$fileContent .= '              <Currency code="EUR" />' . "\n";
				$fileContent .= '              <Value>0</Value>' . "\n";
				$fileContent .= '              <VAT code="5I" />' . "\n";
				$fileContent .= '          </Price>' . "\n";
				$fileContent .= '      </OrderLine>' . "\n";


				if(stripos($row['covers'], 'QNS') !== FALSE){
					$i++;
					$fileContent .= '      <OrderLine lineNo="' . $i . '">' . "\n";
					$fileContent .= '          <Item code="' . $row['covers'] . '" />' . "\n";
					$fileContent .= '          <Quantity>-' . $row['aantal'] . '</Quantity>' . "\n";
					$fileContent .= '          <Delivery>' . "\n";
					$fileContent .= '              <Date>2020-01-04</Date>' . "\n";
					$fileContent .= '          </Delivery>' . "\n";
					$fileContent .= '          <Price type="S">' . "\n";
					$fileContent .= '              <Currency code="EUR" />' . "\n";
					$fileContent .= '              <Value>0</Value>' . "\n";
					$fileContent .= '              <VAT code="5I" />' . "\n";
					$fileContent .= '          </Price>' . "\n";
					$fileContent .= '      </OrderLine>' . "\n";
				}

			}
		} else {
			//echo "0 results, er is iets fout gelopen. <br>";
		}

		$fileContent .= '  </Order>' . "\n";

		$fileContent .= '</Orders>' . "\n";
		$fileContent .= '</eExact>' . "\n";

		$fileContent = $fileContent . PHP_EOL;
		$filename = '\\\\spb-srv-exact\c$\ExactXML\XML1_prod\1_SP_BYOD20_decom_ord.xml';
		file_put_contents($filename, $fileContent);

} elseif(isset($_GET['2']) == true){

	echo '<div>
		<p>Decom move export (XML 2) gelukt</p>
	</div>';


		$sql = "SELECT *, COUNT(*) AS aantal, orderid, orders.spsku AS SPSKU, productnumber, (SELECT id FROM orderpicking WHERE orderid = orders.id) AS pickingid
			FROM decomissioned
			LEFT JOIN orders ON orders.id = decomissioned.orderid
			LEFT JOIN devices ON devices.spsku = orders.spsku
			WHERE removed_from_exact IS NULL
			GROUP BY orders.spsku, orderid";

			$fileContent = '';
			$fileContent .= '<?xml version="1.0" ?>' . "\n";
			$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
			$fileContent .= '<Logistics>' . "\n";
			$fileContent .= '<Logistic type="D">' . "\n";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {



			$orderamount = '';
			$error = '';
			$i = 0;
			$pickingid = 20999997;
			$date = '2020-01-04';
			$skulocation = '700';
			$coverlocation = '700';
			$panellocation = '700';
			$ssdlocation = '700';
			$ramlocation = '700';
			$ram2location = '700';
			$keyboardlocation = '700';
			$finallocation = '700';
			$orderid = $row['orderid'];
			$orderamount = $row['amount'];

			$sql5 = "SELECT *, orders.id as orderid,
				( SELECT GROUP_CONCAT(serialnumber) FROM `decomissioned` WHERE orderid = orders.id ) as serialnumbers
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
						$fileContent .= '          <Quantity>-1</Quantity>' . "\n";
						$fileContent .= '                  <Serialnumber>' . $serialnumber . '</Serialnumber>' . "\n";
						$fileContent .= '          <Warehouse>' . "\n";
						$fileContent .= '              <Code>100</Code>' . "\n";

						$fileContent .= '          </Warehouse>' . "\n";
						$fileContent .= '      </Line>' . "\n";
					}

				}
			} else {
				echo "0 results";
				$error .= 'Geen serienummers, kan order ' . $orderid . ' niet genereren.<br>';
			}

			$sql4 = "SELECT *, orders.id as orderid,
			( select count(*) from decomissioned where orderid = orders.id ) as amountofdecom,
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
						$fileContent .= '		  <Quantity>-' . $row4['amountofdecom'] . '</Quantity>' . "\n";
						$fileContent .= '		  <Warehouse>' . "\n";
							$fileContent .= '		      <Code>100</Code>' . "\n";

						$fileContent .= '		  </Warehouse>' . "\n";
						$fileContent .= '	     </Line>' . "\n";
					}

				}
			} else {
				echo "0 results";
			}


			$sql3 = "SELECT *, orders.id as orderid,
				( SELECT GROUP_CONCAT(concat(label, ';', serialnumber)) FROM `decomissioned` WHERE orderid = orders.id ) as labelnumbers
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
						$fileContent .= '          <Quantity>1</Quantity>' . "\n";
						$fileContent .= '              <ToBeFulfilled>0</ToBeFulfilled>' . "\n";
						$fileContent .= '              <GenerateCreditNote>1</GenerateCreditNote>' . "\n";
						$fileContent .= '                   <Serialnumber>' . $comparr[0] . '</Serialnumber>' . "\n";
						$fileContent .= '                   <Description>' . $comparr[1] . '</Description>' . "\n";
						$fileContent .= '          <Warehouse>' . "\n";

							$fileContent .= '		      <Code>100</Code>' . "\n";

						$fileContent .= '              <Location>' . $finallocation . '</Location>' . "\n";
						$fileContent .= '          </Warehouse>' . "\n";
						$fileContent .= '      </Line>' . "\n";
					}

				}
			} else {
				echo "0 results";
			}


		}

					$fileContent .= '  </Logistic>' . "\n";
			$fileContent .= '</Logistics>' . "\n";
			$fileContent .= '</eExact>' . "\n";

			if($error == ''){
				$fileContent = $fileContent . PHP_EOL;
				$filename = '\\\\spb-srv-exact\c$\ExactXML\XML2_prod_lev\2_SP_BYOD20_decom_lev.xml';
				file_put_contents($filename, $fileContent);
			} else {
				echo $error;
			}


	}
} elseif(isset($_GET['5']) == true){

	// XML 5

	$i = 0;
	

	echo '<div>
		
		<p>XML 5 batch export gelukt</p>
		
		</div>';

	$fileContent = '';
	$fileContent .= '<?xml version="1.0" ?>' . "\n";
	$fileContent .= '<eExact xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="eExact-Schema.xsd">' . "\n";
	$fileContent .= '<Logistics>' . "\n";
	$fileContent .= '<Logistic type="D">' . "\n";

	$tsql = "SELECT orsrg.ordernr, refer, refer1, refer2, refer3, freefield1, lengte, breedte, instruction, artcode, selcode, orkrg.id as orkrgid, orsrg.id as orsrgid, ord_soort, user_id, ar_soort
			FROM orkrg with (nolock)
			INNER JOIN orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
			where selcode in ('WS', 'W2') and aant_gelev = 0 and (artcode like 'H%' or artcode like 'L%') and instruction != ''";
	$stmt = sqlsrv_query( $msconn, $tsql);

	if($stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {

		$serialnumber = '';
			$sql2 = "SELECT serialnumber FROM labels WHERE label = '" . $row["instruction"] . "'";
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) {
					$serial = $row2['serialnumber'];
				}
			} else {
				echo "0 results";
			}

					$i++;
					$fileContent .= '      <Line number="' . $i . '">' . "\n";
					$fileContent .= '          <OrderNumber>' . $row['ordernr'] . '</OrderNumber>' . "\n";
					$fileContent .= '              <Date>2020-12-31</Date>' . "\n";
					$fileContent .= '          <Item>' . "\n";
					$fileContent .= '          <Code>' . $row['artcode'] . '</Code>' . "\n";
					$fileContent .= '          </Item>' . "\n";
					$fileContent .= '          <Quantity>1</Quantity>' . "\n";
					$fileContent .= '              <ToBeFulfilled>0</ToBeFulfilled>' . "\n";
					$fileContent .= '              <GenerateCreditNote>1</GenerateCreditNote>' . "\n";
					$fileContent .= '                   <Serialnumber>' . $row['instruction'] . '</Serialnumber>' . "\n";
					$fileContent .= '                   <Description>' . $serial . '</Description>' . "\n";
					$fileContent .= '          <Warehouse>' . "\n";

					$fileContent .= '              <Code>100</Code>' . "\n";

					$fileContent .= '          </Warehouse>' . "\n";
					$fileContent .= '      </Line>' . "\n";

		}

	$fileContent .= '  </Logistic>' . "\n";
	$fileContent .= '</Logistics>' . "\n";
	$fileContent .= '</eExact>' . "\n";

	$fileContent = $fileContent . PHP_EOL;
	$filename = '\\\\spb-srv-exact\c$\ExactXML\XML5_ship_lev\5_SP_BYOD20_batch_lev.xml';
	file_put_contents($filename, $fileContent);


}
?>
