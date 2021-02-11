<?php

$title = 'Ombouw';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

<?php

if (isset($_POST['orderid']) !== false) {

	foreach ($_POST as $key => $value) {
		if (strpos($key, 'orderid') !== FALSE) {
			$orderid = $value;
		} elseif (strpos($key, 'synergyid') !== FALSE) {
			$synergyid = $value;
		} elseif (strpos($key, 'laptop') !== FALSE && strpos($key, '_doneby') == FALSE && strpos($key, '_serial') == FALSE && strpos($key, '_panel') == FALSE && strpos($key, '_ssd') == FALSE && strpos($key, '_ram') == FALSE && strpos($key, '_keyboard') == FALSE && strpos($key, '_desc') == FALSE && isset($orderid) == true && isset($synergyid) == true ) {
			$sql = "SELECT * FROM `device-swap` WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					if(isset($_POST[$value.'_doneby']) == true){
						if($row['doneby'] !== $_POST[$value.'_doneby'] && $_POST[$value.'_doneby'] !== ''){
							$sql = "UPDATE `device-swap` SET doneby = '" . $_POST[$value.'_doneby'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Done By  voor " . $key . " is aangepast van " . $row['doneby'] . " naar " . $_POST[$value.'_doneby'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}

					if(isset($_POST[$value.'_serial']) == true){
						if ($row['serialnumber'] !== $_POST[$value.'_serial'] && $_POST[$value.'_serial'] !== '' ){

							$sql = "UPDATE `device-swap` SET serialnumber = '" . $_POST[$value.'_serial'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Serienummer voor " . $key . " is aangepast van " . $row['serialnumber'] . " naar " . $_POST[$value.'_serial'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}

					if(isset($_POST[$value.'_panel']) == true){
						if($row['originalpanel'] !== $_POST[$value.'_panel'] && $_POST[$value.'_panel'] !== ''){
							$sql = "UPDATE `device-swap` SET originalpanel = '" . $_POST[$value.'_panel'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Panel voor " . $key . " is aangepast van " . $row['originalpanel'] . " naar " . $_POST[$value.'_panel'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}

					if(isset($_POST[$value.'_ssd']) == true){
						if($row['originalssd'] !== $_POST[$value.'_ssd'] && $_POST[$value.'_ssd'] !== ''){
							$sql = "UPDATE `device-swap` SET originalssd = '" . $_POST[$value.'_ssd'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "SSD voor " . $key . " is aangepast van " . $row['originalssd'] . " naar " . $_POST[$value.'_ssd'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}

					if(isset($_POST[$value.'_ram']) == true){
						if($row['originalram'] !== $_POST[$value.'_ram'] && $_POST[$value.'_ram'] !== ''){
							$sql = "UPDATE `device-swap` SET originalram = '" . $_POST[$value.'_ram'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Ram voor " . $key . " is aangepast van " . $row['originalram'] . " naar " . $_POST[$value.'_ram'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}

					if(isset($_POST[$value.'_keyboard']) == true){
						if($row['originalkeyboard'] !== $_POST[$value.'_keyboard'] && $_POST[$value.'_keyboard'] !== ''){
							$sql = "UPDATE `device-swap` SET originalkeyboard = '" . $_POST[$value.'_keyboard'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Keyboard voor " . $key . " is aangepast van " . $row['originalkeyboard'] . " naar " . $_POST[$value.'_keyboard'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}

					if(isset($_POST[$value.'_desc']) == true){
						if($row['desc'] !== $_POST[$value.'_desc'] && $_POST[$value.'_desc'] !== '') {
							$sql = "UPDATE `device-swap` SET `desc` = '" . $_POST[$value.'_desc'] . "' WHERE orderid = '" . $orderid . "' AND laptopnr = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Beschrijving voor " . $key . " is aangepast van " . $row['desc'] . " naar " . $_POST[$value.'_desc'] . "<br><br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}

						}
					}
				}

			} else {

				$sql = "INSERT INTO `device-swap` (
								orderid,
								synergyid,
								laptopnr,
								serialnumber,
								doneby,
								originalpanel,
								originalssd,
								originalram,
								originalkeyboard,
								desc)
								VALUES ('" . $orderid . "',
								'" . $synergyid . "',
								'" . $key . "',
								'" . $_POST[$value.'_serial'] . "',
								'" . $_POST[$value.'_doneby'] . "',
								'" . $_POST[$value.'_panel'] . "',
								'" . $_POST[$value.'_ssd'] . "',
								'" . $_POST[$value.'_ram'] . "',
								'" . $_POST[$value.'_keyboard'] . "',
								'" . $_POST[$value.'_desc'] . "')";

				if ($conn->query($sql) === TRUE) {
					echo $_POST[$value.'_serial'] . " succesvol toegevoegd.<br>";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}

			}

		}

	}

	$conn->close();

	echo '<br><a href="'. hasAccessForUrl('ombouw-orders.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';


} elseif (isset($_GET['id']) !== false && isset($_GET['edit']) !== false) {

	$sql = "SELECT *, orders.id as orderid, orders.synergyid as ordersynergyid, GROUP_CONCAT(serialnumber SEPARATOR ';') as serialnumbers FROM orders LEFT JOIN `device-swap` on orders.id = `device-swap`.orderid WHERE orders.id = '" . $_GET['id'] . "' and orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			echo "<h3>Ombouwen van order SP-BYOD20-" . $row['orderid'] . "</h3><br>";

			echo '<form action="ombouw.php" method="post" class="form">
				<input type="hidden" name="orderid" value="' . $row['orderid'] . '">
				<input type="hidden" name="synergyid" value="' . $row['ordersynergyid'] . '">';

			echo'<div class="form-group row">
				<div class="col-sm">
				<b>Laptop #</b>
				</div>';
				echo '<div class="col-sm">
				<b>Technieker</b>
				</div>';
				echo '<div class="col-sm">
				<b>Serial</b>
				</div>';

			if(isset($row['panelswap']) == true && $row['panelswap'] !== ""){
				echo '<div class="col-sm">
					<b>Panel</b>
					</div>';
				$paneldropdown = '<option value=""></option>';
				$sql2 = "SELECT * FROM `device-parts` WHERE type = 'SCHERM'";
				$result2 = $conn->query($sql2);
				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						$paneldropdown .= '<option value="' . $row2['SPSKU'] . '">' . $row2['SPSKU'] . ' - ' . $row2['desc'] . '</option>';
					}
				} else {
					echo "0 results";
				}
			}

			if(isset($row['ssdswap']) == true && $row['ssdswap'] !== ""){
				echo '<div class="col-sm">
					<b>SSD</b>
					</div>';
				$ssddropdown = '<option value=""></option>';
				$sql3 = "SELECT * FROM `device-parts` WHERE type = 'SSD'";
				$result3 = $conn->query($sql3);
				if ($result3->num_rows > 0) {
					while($row3 = $result3->fetch_assoc()) {
						$ssddropdown .= '<option value="' . $row3['SPSKU'] . '">' . $row3['SPSKU'] . ' - ' . $row3['desc'] . '</option>';
					}
				} else {
					echo "0 results";
				}
			}

			if(isset($row['memoryswap']) == true && $row['memoryswap'] !== ""){
				echo '<div class="col-sm">
					<b>RAM</b>
					</div>';
				$memorydropdown = '<option value=""></option>';
				$sql4 = "SELECT * FROM `device-parts` WHERE type = 'RAM'";
				$result4 = $conn->query($sql4);
				if ($result4->num_rows > 0) {
					while($row4 = $result4->fetch_assoc()) {
						$memorydropdown .= '<option value="' . $row4['SPSKU'] . '">' . $row4['SPSKU'] . ' - ' . $row4['desc'] . '</option>';
					}
				} else {
					echo "0 results";
				}
			}

			if(isset($row['keyboardswap']) == true && $row['keyboardswap'] !== ""){
				echo '<div class="col-sm">
					<b>Keyboard</b>
					</div>';
				$keyboarddropdown = '<option value=""></option>';
				$sql5 = "SELECT * FROM `device-parts` WHERE type = 'KEYBOARD'";
				$result5 = $conn->query($sql5);
				if ($result5->num_rows > 0) {
					while($row5 = $result5->fetch_assoc()) {
						$keyboarddropdown .= '<option value="' . $row5['SPSKU'] . '">' . $row5['SPSKU'] . ' - ' . $row5['desc'] . '</option>';
					}
				} else {
					echo "0 results";
				}
			}

			echo '<div class="col-sm">
				<b>Opmerking</b>
				</div>';
			echo '</div>';

			$serialnumbers = explode(';', $row['serialnumbers']);

			$sql6 = "SELECT * FROM `device-swap` WHERE orderid = '" . $row['orderid'] . "'";
			$result6 = $conn->query($sql6);

			if ($result6->num_rows > 0) {
				while($row6 = $result6->fetch_assoc()) {

					$x = str_replace('laptop', '', $row6['laptopnr']);
					echo'<div class="form-group row">
							<div class="col-sm">
								<input type="text" class="form-control" name="laptop' . $x . '" value="' . $row6['laptopnr'] . '" tabindex="-1" readonly>
							</div>
							<div class="col-sm">
								<select name="laptop' . $x . '_doneby" class="form-control PartSelect" id="techniekerswap' . $x . '" tabindex="-1" >';

									echo '<option value="' . $row6['doneby'] . '">' . $row6['doneby'] . '</option>
										<option value="Bart">Bart</option>
										<option value="Bruno">Bruno</option>
										<option value="Ward">Ward</option>
										<option value="Styn">Styn</option>
										<option value="Davy">Davy</option>
										<option value="Felix">Felix</option>
										<option value="Jo">Jo</option>
										<option value="Tibo">Tibo</option>
										<option value="Sander">Sander</option>
										<option value="Michael">Michael</option>
										<option value="Geoffrey">Geoffrey</option>
										<option value="Ronny">Ronny</option>
										<option value="Abdel">Abdel</option>
										<option value="Pieterjan">Pieterjan</option>
										<option value="Anek">Anek</option>
										<option value="Bilal">Bilal</option>
										<option value="Brent">Brent</option>
								</select>
							</div>
							<div class="col-sm">
								<input type="text" class="form-control" name="laptop' . $x . '_serial" placeholder="" value="' . $row6['serialnumber'] . '">
							</div>
						';

					if(isset($row['panelswap']) == true && $row['panelswap'] !== ""){
						echo '<div class="col-sm"><select name="laptop' . $x . '_panel" class="form-control PartSelect" tabindex="-1" id="laptop' . $x . '_panel">';
						if(isset($row6['originalpanel']) == true && $row6['originalpanel'] !== ""){
							echo '<option value="' . $row6['originalpanel'] . '">' . $row6['originalpanel'] . '</option>';
						}
						echo $paneldropdown;
						echo '</select></div>';
					}

					if(isset($row['ssdswap']) == true && $row['ssdswap'] !== ""){
						echo '<div class="col-sm"><select name="laptop' . $x . '_ssd" class="form-control PartSelect" tabindex="-1" id="laptop' . $x . '_ssd">';
						if(isset($row6['originalssd']) == true && $row6['originalssd'] !== ""){
							echo '<option value="' . $row6['originalssd'] . '">' . $row6['originalssd'] . '</option>';
						}
						echo $ssddropdown;
						echo '</select></div>';
					}

					if(isset($row['memoryswap']) == true && $row['memoryswap'] !== ""){
						echo '<div class="col-sm"><select name="laptop' . $x . '_ram" class="form-control PartSelect" tabindex="-1" id="laptop' . $x . '_ram">';
						if(isset($row6['originalram']) == true && $row6['originalram'] !== ""){
							echo '<option value="' . $row6['originalram'] . '">' . $row6['originalram'] . '</option>';
						}
						echo $memorydropdown;
						echo '</select></div>';
					}

					if(isset($row['keyboardswap']) == true && $row['keyboardswap'] !== ""){
						echo '<div class="col-sm"><select name="laptop' . $x . '_keyboard" class="form-control PartSelect" tabindex="-1" id="laptop' . $x . '_keyboard">';
						if(isset($row6['originalkeyboard']) == true && $row6['originalkeyboard'] !== ""){
							echo '<option value="' . $row6['originalkeyboard'] . '">' . $row6['originalkeyboard'] . '</option>';
						}
						echo $keyboarddropdown;
						echo '</select></div>';
					}

						echo '<div class="col-sm">
						<input type="text" class="form-control" name="laptop' . $x . '_desc" placeholder="" tabindex="-1" value="' . $row6['desc'] . '">
						</div>
					</div>';

				}

			}
			echo '<br>';
			echo '<button type="submit" class="btn btn-success">Gegevens Opslaan</button>';
			echo '</form>';
		}

	} else {

		echo "0 results";

	}

	$conn->close();

} elseif (isset($_GET['id']) !== false && isset($_GET['generate']) !== false ) {

	echo 'Generate records..<br>';

	$sql = "SELECT * FROM `device-swap` WHERE orderid = '" . $_GET['id'] . "'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			echo "Gegevens gevonden, er is geen reden om nog data te genereren<br>";
		}
	} else {
		for($x = 1; $x <= $_GET['amount']; $x++) {
			$sql = "INSERT INTO `device-swap` (orderid, synergyid, laptopnr, serialnumber)
				VALUES ('" . $_GET['id'] . "', '" . $_GET['synergyid'] . "', 'laptop" . $x . "', '')";

			if ($conn->query($sql) === TRUE) {
				echo "laptop" . $x . " created successfully<br>";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}

	// redirect
	$URL = 'generateXML.php?id=' . $_GET['orderid'] . '&amount=' . $_GET['amount'] . '&type=technician';
	if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
	else { header("Location: $URL"); }
	exit;

} elseif (isset($_GET['id']) !== false && isset($_GET['finish']) !== false && isset($_GET['post']) !== false && isset($_GET['status']) !== false ) {

	$sql = "UPDATE orders SET status='" . $_GET['status'] . "' WHERE id= '". $_GET['id'] . "'";

	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully<br>";
		echo 'Order ' . $_GET['id'] . ' is aangepast naar ' . $_GET['status'] . '';
	} else {
		echo "Error updating record: " . $conn->error;
	}

} elseif (isset($_GET['id']) !== false && isset($_GET['finish']) !== false ) {

	$sql = "SELECT * FROM orders where id = '" . $_GET['id'] . "' and deleted != 1";
	$result = $conn->query($sql);
	$donotfinish = false;

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			$button = '';
			if($row['imageid'] == 'idk'){
				echo "Bent u zeker dat u bestelling nr " . $row["id"] . " wilt afwerken en doorsturen naar Software Support 'wachten op image' met onderstaande gegevens? <br><br>";
				$button = "<a class='btn btn-success' href='ombouw.php?id=" . $_GET['id'] . "&finish=true&post=true&status=wachten op image'>Ja</a> <a class='btn' href='imaging.php'>Nee</a>";
			} else {
				echo "Bent u zeker dat u bestelling nr " . $row["id"] . " wilt afwerken en doorsturen naar imaging met onderstaande gegevens? <br><br>";
				$button = "<a class='btn btn-success' href='ombouw.php?id=" . $_GET['id'] . "&finish=true&post=true&status=imaging'>Ja</a> <a class='btn' href='imaging.php'>Nee</a>";
			}

			echo "<table class='table'>
				<tr>
					<th>Laptop</th>
					<th>Serial</th>
					<th>Technieker</th>";
					if($row['panelswap'] !== ''){
						echo '<th>Panel</th>';
					}
					if($row['ssdswap'] !== ''){
						echo '<th>SSD</th>';
					}
					if($row['memoryswap'] !== ''){
						echo '<th>RAM</th>';
					}
					if($row['keyboardswap'] !== ''){
						echo '<th>Keyboard</th>';
					}
					echo "<th>Opmerking</th>
				</tr>";

				$sql2 = "SELECT *, (SELECT COUNT(*) FROM `device-swap` WHERE serialnumber = q.serialnumber) as duplicate FROM `device-swap` q where orderid = " . $row["id"];
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {

						$duplicate = $row2['duplicate'];
						if($duplicate !== '1'){
							echo "<tr style='color:red;'>";
							$donotfinish = true;
						} else {
							echo "<tr>";
						}

							echo "<td>" . $row2["laptopnr"] . "</td>
							<td>" . $row2["serialnumber"] . "</td>
							<td>" . $row2["doneby"] . "</td>";
							if($row['panelswap'] !== ''){
								echo "<td>" . $row2["originalpanel"] . "</td>";
							}
							if($row['ssdswap'] !== ''){
								echo "<td>" . $row2["originalssd"] . "</td>";
							}
							if($row['memoryswap'] !== ''){
								echo "<td>" . $row2["originalram"] . "</td>";
							}
							if($row['keyboardswap'] !== ''){
								echo "<td>" . $row2["originalkeyboard"] . "</td>";
							}
							echo "<td>" . $row2["desc"] . "</td></tr>";
					}
				} else {
					echo "0 results";
				}

				echo "</table>";

				echo "<br>";
				if($donotfinish == true){
					echo "<p style='color:red;font-weight:bold;'>Er zijn dubbele serienummers gevonden ( zie rood ), hierdoor kan het order niet verdergaan naar imaging.</p>";
				} else {
					echo $button;
				}

		}
	} else {
		echo "0 results";
	}
	$conn->close();

} elseif (isset($_GET['id']) !== false) {

	$sql = "SELECT *, orders.id as orderid, orders.status as orderstatus, orders.notes as ordernotes, orders.spsku as orderspsku, orders.synergyid as ordersynergyid FROM orders LEFT JOIN `byod-orders`.images2020 ON orders.imageid = images2020.id
			WHERE orders.id = '" . $_GET['id'] . "' and orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {
			echo "<h3>Order SP-BYOD20-" . $row['orderid'] . "</h3>";
			echo '<h4 style="font-weight:bold; color:red;">' . $row['orderstatus'] . '</h4><br>';

			echo '<p>
				<strong>Synergy ID:</strong> ' . $row['ordersynergyid'] . '<br>
				<strong>Sales:</strong> ' . $row['sales'] . '<br>
				<strong>Aangevraagd op:</strong> ' . $row['requested_on'] . '<br><br>
				<strong>SPSKU:</strong> ' . $row['orderspsku'] . '<br>
				<strong>Aantal Computers:</strong> ' . $row['amount'] . '<br>
				<strong>Hoes?</strong> ' . $row['covers'] . '<br></p>';

			if ($row['imageid'] == "nieuw") {
				echo '<p><strong>Image:</strong> Nieuwe image<br>';
			} else {
				echo '<p><strong>Image:</strong> ' . $row['name'] . '<br>';
			}

			echo '<strong>Label: </strong> ' . $row['label'] . '<br><br>
				<strong>Afleveradres:</strong><br>' . $row['shipping_street'] . ' ' . $row['shipping_number'] . '<br>
				' . $row['shipping_postcode'] . ' ' . $row['shipping_city'] . '<br></p>';

			echo '<p><strong>Uitlevering</strong><br>
				' . $row['shipping_date'] . ' ' . $row['shipping_hour'] . '<br></p>';

			if ($row['ordernotes'] !== "") {
				echo '<p><strong>Extra uitleg</strong><br>' . $row['ordernotes'] . '<br></p>';
			}

		}

	} else {

		echo "0 results";

	}

	$conn->close();

}

?>

		</tbody>
	</table>
</div>

<script>
$(document).ready(function() {
	$('.PartSelect').select2({
		theme: "classic",
		dropdownAutoWidth : true
	});
});
</script>

<?php
include('footer.php');
?>
