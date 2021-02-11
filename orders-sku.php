<?php

$title = 'Openstaande bestellingen op sku';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

?>

<div class="body">

	<h3>Openstaande bestellingen op sku</h3>
	<p>Dit bevat de nog niet geleverde toestellen onder een school productieorder, niet geleverde webshopbestelligen uit exact en niet geleverde leermiddel bestellingen.</p>
	<br>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Aantal</th>
				<th scope="col">SKU</th>
				<th scope="col">Toestel</th>
			</tr>
		</thead>

		<tbody>
		<?php

			$toestellen[] = '';

			$sql = "SELECT id, spsku, amount-(ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature != ''), 0)) as aantal
					FROM orders WHERE finance_type = 'School' AND amount-(ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = orders.id AND signature != ''), 0))>0";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					if(isset($toestellen[$row['spsku']]) == true){
						$toestellen[$row['spsku']] = $toestellen[$row['spsku']] + $row['aantal'];
					} else {
						$toestellen[$row['spsku']] = $row['aantal'];
					}

				}

			} else {

				//echo "0 results";

			}

			$sql2 = "SELECT tbltoestelcontractdefinitie.sku, COUNT(*) AS aantal
					FROM leermiddel.`tblcontractdetails`
					LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `id`
					LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
					WHERE `deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum = '2020-09-01' AND `lengte` = 0
					GROUP BY sku";
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {

				while($row2 = $result2->fetch_assoc()) {

					if(isset($toestellen[$row2['sku']]) == true){
						$toestellen[$row2['sku']] = $toestellen[$row2['sku']] + $row2['aantal'];
					} else {
						$toestellen[$row2['sku']] = $row2['aantal'];
					}

				}

			} else {

				//echo "0 results";

			}


			$tsql = "select artcode, count(*) as aantal
					from orkrg with (nolock)
					inner join orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
					left join cicmpy on ltrim(cmp_code) = orkrg.freefield1
					where len(orkrg.freefield1)>0 and (artcode like 'H%' or artcode like 'L%') and lengte=0
					group by artcode";
			$stmt = sqlsrv_query( $msconn, $tsql);
			if($stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			while( $row3 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
					if(isset($toestellen[$row3['artcode']]) == true){
						$toestellen[$row3['artcode']] = $toestellen[$row3['artcode']] + $row3['aantal'];
					} else {
						$toestellen[$row3['artcode']] = $row3['aantal'];
					}
			}

			foreach($toestellen as $toestel => $aantal){

				$beschrijving = '';
				$productnumber = '';

				if($toestel != '' && $toestel != '0'){
					$sql = "SELECT productnumber, CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) as beschrijving
						FROM devices WHERE spsku = '" . $toestel . "'";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							$beschrijving = $row['beschrijving'];
							$productnumber = $row['productnumber'];
						}
					} else {
						echo "0 results op " . $toestel;
					}

					echo '<tr><td>' . $aantal . '</td><td>' . $productnumber . '</td><td>' . $beschrijving . '</td></tr>';
				}

			}

			$conn->close();

			echo '
		</tbody>
	</table><br><br><br><br>';
?>

</div>

<?php
include('footer.php');
?>
