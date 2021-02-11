<?php

$title = 'Order';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

?>

<div class="container body">

<?php

if (isset($_POST['labelchange']) !== false) {

	$orderid = mysqli_real_escape_string($conn, $_POST['id']);
	$orderlabel = strtoupper(mysqli_real_escape_string($conn, $_POST['orderlabel']));

	$sql = "UPDATE orders
			SET label = '" . $orderlabel . "', history =
			CASE WHEN history IS NULL
				THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Schoollabel aangepast naar \"" . $orderlabel . "\"')
				ELSE concat(history,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Schoollabel aangepast naar \"" . $orderlabel . "\"')
			END
			WHERE id = '" . $orderid . "'";
	if ($conn->query($sql) === TRUE) {

		echo "Record updated successfully";

		// redirect
		$URL = "order.php?id=" . $orderid;
		if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
		else { header("Location: $URL"); }
		exit;

	} else {
		echo "Error updating record: " . $conn->error;
	}

	die();

} elseif (isset($_GET['labelchange']) !== false) {

	echo '<h1>Label van order SP-BYOD20-' . $_GET['id'] . '</h1>
		<form action="order.php" method="post">
			<label for="id">Order:</label><br>
			<input type="text" id="statuschange" name="labelchange" value="true" hidden>
			<input type="text" id="id" name="id" value="' . $_GET['id'] . '" readonly class="form-control"><br>
			<label for="orderlabel">Label:</label><br>';

		$sql = "SELECT (SELECT GROUP_CONCAT(label) FROM orders WHERE synergyid=q.synergyid) AS labels FROM orders q WHERE id = '" . $_GET['id'] . "'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				if($row['labels'] !== ''){
					echo '<span class="smalltext">Eerder gebruikte labels: ' . $row['labels'] . '</span><br><br>';
				}
			}
		} else {
			echo "0 results";
		}

		echo '<div class="row">
				<div class="col"><input type="text" title="Voer het label in zonder -0001" class="form-control" placeholder="" Name="orderlabel"></div>
				<div class="col"><input type="text" value="-0001" readonly class="form-control"></div>
			</div><br>
			<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

	die();
} elseif (isset($_POST['statuschange']) !== false) {

	$orderid = mysqli_real_escape_string($conn, $_POST['id']);
	$status = mysqli_real_escape_string($conn, $_POST['status']);
	$oldstatus = mysqli_real_escape_string($conn, $_POST['oldstatus']);

	$sql = "UPDATE orders
			SET status = '" . $status . "', history =
			CASE WHEN history IS NULL
				THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Order manueel aangepast van status \"" . $oldstatus . "\" naar \"" . $status . "\"')
				ELSE concat(history,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Order manueel aangepast van status \"" . $oldstatus . "\" naar \"" . $status . "\"')
			END
			WHERE id = '" . $orderid . "' and status = '" . $oldstatus . "'";
	if ($conn->query($sql) === TRUE) {

		echo "Record updated successfully";

		// redirect
		$URL = "order.php?id=" . $orderid;
		if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
		else { header("Location: $URL"); }
		exit;

	} else {
		echo "Error updating record: " . $conn->error;
	}

	die();

} elseif (isset($_GET['statuschange']) !== false) {

	echo '<h1>Status van order SP-BYOD20-' . $_GET['id'] . '</h1>
			<p style="color:red;">LET GOED OP WANT DIT KAN FOUTEN GEVEN IN DE FLOW.</p>
		<form action="order.php" method="post">
			<label for="id">Order:</label><br>
			<input type="text" id="statuschange" name="statuschange" value="true" hidden>
			<input type="text" id="id" name="id" value="' . $_GET['id'] . '" readonly class="form-control"><br>
			<label for="oldstatus">Huidige status:</label><br>
			<input type="text" id="oldstatus" name="oldstatus" value="' . $_GET['status'] . '" readonly class="form-control"><br>
			<label for="status">Nieuwe status:</label>
			<select name="status" id="status" class="form-control" required>
				<option value="" disabled selected></option>
				<option style="color:black;" value="nieuw">nieuw</option>
				<option style="color:black;" value="ombouw">ombouw</option>
				<option style="color:black;" value="wachten op image">wachten op image</option>
				<option style="color:black;" value="imaging">imaging</option>
				<option style="font-style:italic; color:grey;" value="tdconfigadmin">tdconfigadmin</option>
				<option style="font-style:italic; color:grey;" value="tdgeenvoorraad">tdgeenvoorraad</option>
				<option style="font-style:italic; color:grey;" value="tdimaging">tdimaging</option>
				<option style="font-style:italic; color:grey;" value="tdafgewerkttemp">tdafgewerkttemp</option>
				<option style="font-style:italic; color:grey;" value="tdafgewerkt">tdafgewerkt</option>
				<option style="color:black;" value="levering">levering</option>
				<option style="color:black;" value="geen bestellingen">geen bestellingen</option>
				<option style="color:black;" value="uitgeleverd">uitgeleverd</option>
			</select><br>
			<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

	die();
} elseif (isset($_POST['duplicate']) !== false) {

	$orderid = mysqli_real_escape_string($conn, $_POST['orderid']);
	$synergyid = mysqli_real_escape_string($conn, $_POST['synergyid']);
	$amount = mysqli_real_escape_string($conn, $_POST['amount']);

	$sql = "INSERT INTO orders
			(synergyid, amount, SPSKU, covers, panelswap, ssdswap, memoryswap, memoryswap2, keyboardswap, finance_type, licenses, consumer, unobligated, imageid, label, shipping_street, shipping_number, shipping_city, shipping_postcode, shipping_date, shipping_hour, sales, notes, requested_on, usb, `status`, warehouse, campagne, asignee, status_notes, labels_created, history, updated_at, td_lb_order, td_dnote_nr, magento_type, exact_debtor, exact_ordertype, exact_partialdelivery, exact_confirm, exact_invoicemethod, exact_selectioncode, exact_vat, on_hold)
		SELECT
			synergyid, '" . $amount . "', SPSKU, covers, panelswap, ssdswap, memoryswap, memoryswap2, keyboardswap, finance_type, licenses, consumer, unobligated, imageid, label, shipping_street, shipping_number, shipping_city, shipping_postcode, shipping_date, shipping_hour, sales, notes, requested_on, usb, 'nieuw', warehouse, campagne, asignee, 'Order gedupliceerd van order " . $orderid . "', labels_created, 'Order gedupliceerd van order " . $orderid . "', updated_at, td_lb_order, td_dnote_nr, magento_type, exact_debtor, exact_ordertype, exact_partialdelivery, exact_confirm, exact_invoicemethod, exact_selectioncode, exact_vat, on_hold
		FROM
			orders
		WHERE
			id = '" . $orderid . "'";

	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo 'Order is gedupliceerd.<br>';

		echo '<a href="'. hasAccessForUrl('school.php?synergyid=' . $synergyid . '', false).'"><button class="btn btn-dark">Terug naar het overzicht</button></a>';
		echo '</div>';
		//echo "<script type='text/javascript'>window.top.location='management.php';</script>"; exit;
	} else {
		echo "Error updating record: " . $conn->error;
	}

} elseif (isset($_GET['duplicate']) !== false) {

	echo '<h3>Dupliceer order SP-BYOD20-' . $_GET['id'] . '</h3>
	<form action="order.php" method="post">
		<input type="text" id="orderid" name="orderid" value="' . $_GET['id'] . '" hidden class="form-control">
		<input type="text" id="synergyid" name="synergyid" value="' . $_GET['synergyid'] . '" hidden class="form-control">
		<input type="text" id="duplicate" name="duplicate" value="true" hidden class="form-control">
		<label for="amount">Aantal toestellen:</label><br>
		<input type="number" id="amount" name="amount" class="form-control"><br>
		<input type="submit" value="Submit" class="btn btn-primary">
	</form>';

} elseif (isset($_POST['asignee']) !== false) {

	$asignee = mysqli_real_escape_string($conn, $_POST['asignee']);
	$status_notes = mysqli_real_escape_string($conn, $_POST['status_notes']);

	$sql = "UPDATE `byod-orders`.`orders` SET asignee='" . $asignee . "', status_notes='" . $status_notes . "', history =
	CASE WHEN history IS NULL
		THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Toegewezen op " . $asignee . " met status " . $status_notes . "')
		ELSE concat(history,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] Toegewezen op " . $asignee . " met status " . $status_notes . "')
	END
	WHERE id=" . $_POST['id'];

	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo 'SP-BYOD20-' . $_POST['id'] . " is aangepast.<br>
			Dit order is nu toegewezen op <em>" . $asignee . "</em> met status '" . $_POST['status_notes'] . "'.<br>";

		echo '<a href="'. hasAccessForUrl('order.php?id=' . $_POST['id'] . '', false).'"><button class="btn btn-dark">Terug naar het overzicht</button></a>';
		echo '</div>';
		//echo "<script type='text/javascript'>window.top.location='management.php';</script>"; exit;
	} else {
		echo "Error updating record: " . $conn->error;
	}

} elseif (isset($_POST['id']) !== false) {


	$sql = "SELECT * FROM devices WHERE SPSKU = '" . $_POST['SPSKU'] . "'";
	$result = $conn->query($sql);
	$ombouw = 0;
	$imagemaken = 0;
	$status = "";

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			if ($row['panel_swap'] !== '' || $row['ssd_swap'] !== '' || $row['memory_swap'] !== '' || $row['keyboard_swap'] !== '' ) {
				$ombouw = 1;
			}

		}

	} else {

		echo "0 results";

	}

	$sql2 = "SELECT * FROM orders WHERE id = '" . $_POST['id'] . "' and deleted != 1";
	$result2 = $conn->query($sql2);

	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {
			if ($row2['imageid'] == "nieuw" || $row2['imageid'] == "idk") {
				$imagemaken = 1;
			}
		}
	} else {
		echo "0 results";
	}

	if($ombouw == 1){
		$sql = "UPDATE `byod-orders`.`orders` SET SPSKU='" . $_POST['SPSKU'] . "', warehouse='" . $_POST['warehouse'] . "', panelswap='" . $_POST['panelswap'] . "', ssdswap='" . $_POST['ssdswap'] . "', memoryswap='" . $_POST['memoryswap'] . "', memoryswap2='" . $_POST['memoryswap2'] . "', keyboardswap='" . $_POST['keyboardswap'] . "', status='ombouw' WHERE id=" . $_POST['id'];
		$status = "ombouw";
	} elseif($imagemaken == 1){
		$sql = "UPDATE `byod-orders`.`orders` SET SPSKU='" . $_POST['SPSKU'] . "', warehouse='" . $_POST['warehouse'] . "', status='wachten op image' WHERE id=" . $_POST['id'];
		$status = "wachten op image";
	} else {
		$sql = "UPDATE `byod-orders`.`orders` SET SPSKU='" . $_POST['SPSKU'] . "', warehouse='" . $_POST['warehouse'] . "', status='imaging' WHERE id=" . $_POST['id'];
		$status = "imaging";
	}

	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo 'SP-BYOD20-' . $_POST['id'] . " is aangepast.<br>
			De status is nu <em>" . $status . "</em>.<br>";

		echo '<a href="'. hasAccessForUrl('management.php', false).'"><button class="btn btn-dark">Terug naar het overzicht</button></a>';
		echo '</div>';
		//echo "<script type='text/javascript'>window.top.location='management.php';</script>"; exit;
	} else {
		echo "Error updating record: " . $conn->error;
	}

	$conn->close();

} elseif (isset($_GET['id']) !== false && isset($_GET['edit']) !== false) {

	$sql = "SELECT *, orders.id AS orderid, orders.status AS orderstatus, orders.notes AS ordernotes, orders.spsku AS orderspsku, orders.synergyid AS ordersynergyid,
			( SELECT school FROM forecasts WHERE synergyid = orders.synergyid AND deleted != 1 limit 1 ) AS school_naam,
			( SELECT panel_swap FROM devices WHERE SPSKU = orderspsku limit 1 ) AS panelswap,
			( SELECT ssd_swap FROM devices WHERE SPSKU = orderspsku limit 1 ) AS ssdswap,
			( SELECT memory_swap FROM devices WHERE SPSKU = orderspsku limit 1 ) AS memoryswap,
			( SELECT keyboard_swap FROM devices WHERE SPSKU = orderspsku limit 1 ) AS keyboardswap
			FROM orders
			WHERE orders.id = '" . $_GET['id'] . "' and orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			echo "<h3>Edit order SP-BYOD20-" . $row['orderid'] . "</h3><br>";

			echo '<p>
				<strong>Synergy ID:</strong> <a href="'. hasAccessForUrl('school.php?synergyid=' . $row['ordersynergyid'] . '', false).'">' . $row['ordersynergyid'] . '</a><br>
				<strong>School:</strong> ' . $row['school_naam'] . '<br>
				<strong>Sales:</strong> ' . $row['sales'] . '<br>
				<strong>Aangevraagd op:</strong> ' . $row['requested_on'] . '<br><br>
				<strong>Aantal Computers:</strong> ' . $row['amount'] . '<br>
				<strong>Hoes?</strong> ' . $row['covers'] . '<br></p>';

			$sql2 = "SELECT SchoolNaam as name, status2020 as status FROM images2019 WHERE id = '" . $row['imageid'] . "'
				UNION ALL
				SELECT name, status FROM images2020 WHERE id = '" . $row['imageid'] . "'";
			$result2 = $conn->query($sql2);
			$image = "";

			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) {
					if ($row2['status'] == "done") {
						$image = "af";
					} elseif ($row2['status'] == "nieuw") {
						$image = "nieuw";
					} else {
						$image = "in progress";
					}
				}
			} else {
				$image = "geen";
			}

			if ($image == "af") {
				echo '<p><strong>Image bestaat al</strong></p>';
			} elseif ($image == "in progress") {
				echo '<p><strong style="color:red;">Image wordt momenteel aangemaakt<br>Dit kan vertraging oplopen</strong></p>';
			} elseif ($image == "nieuw") {
				echo '<p><strong style="color:red;">Image moet nog gestart worden<br>Dit zal vertraging oplopen</strong></p>';
			} else {
				echo '<p><strong style="color:red;">Image bestaat nog niet<br>Dit zal vertraging oplopen</strong></p>';
			}

			echo '<form action="order.php" method="post">
				<input type="hidden" name="id" value="' . $row['orderid'] . '">';

			if (strpos($row['orderspsku'], ';') !== false) {

				echo '
				<label style="font-weight: bold;" for="SPSKU">Laptop SKU</label>
				<p>Kies welke laptop er gebruikt moet worden in dit order.</p>
				<select id="SPSKU" name="SPSKU" class="form-control" required>';
				echo '<option value="" selected disabled></option>';

				$SPSKUS = explode(";", $row['orderspsku']);

				foreach ($SPSKUS as $value) {
					echo '<option value="' . $value . '">' . $value . '</option>';
				}

				echo '</select>
					<br>';

			} elseif(strpos($row['orderspsku'], '-O') !== false) {

				echo '<input type="hidden" name="SPSKU" value="' . $row['orderspsku'] . '">';
				echo '<p style="color:red;"><strong>' . $row['orderspsku'] . ' moet omgebouwd worden.<br>Dit zal vertraging oplopen</strong></p>';
				echo '<label style="font-weight: bold;" for="ombouw">Ombouw opties</label><br>';

				echo '<div id="ombouwOptions">';
				if ($row['panelswap'] !== "") {
					//Select panel
				?>
					Panel
					<select id="panelswap" name="panelswap" class="form-control" required>
						<option value="" selected disabled></option>
				<?php
					$sql2 = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS panelswaporder FROM orders WHERE panelswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS panelswaporder,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'SCHERM'
						GROUP BY SPSKU";
					$result2 = $conn->query($sql2);

					if ($result2->num_rows > 0) {

						while($row2 = $result2->fetch_assoc()) {
							$stock = $row2['stock'] - $row2['panelswaporder'];
							echo '<option value="' . $row2["SPSKU"]. '">' . $row2["SPSKU"]. ' - ' . $row2["SKU"]. ' - ' . $row2["desc"]. ' (' . $stock . ')</option>';
						}
					} else {
						echo "0 results";
					}
				?>
					</select><br>
				<?php
				}

				if ($row['ssdswap'] !== "") {
					//Select SSD
				?>
					SSD
					<select id="ssdswap" name="ssdswap" class="form-control" required>
						<option value="" selected disabled></option>
				<?php
					$sql3 = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS ssdswaporder FROM orders WHERE ssdswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS ssdswaporder,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'SSD'
						GROUP BY SPSKU";
					$result3 = $conn->query($sql3);

					if ($result3->num_rows > 0) {

						while($row3 = $result3->fetch_assoc()) {
							$stock = $row3['stock'] - $row3['ssdswaporder'];
							echo '<option value="' . $row3["SPSKU"]. '">' . $row3["SPSKU"]. ' - ' . $row3["desc"]. ' (' . $stock . ')</option>';
						}
					} else {
						echo "0 results";
					}
				?>
					</select><br>
				<?php
				}

				if ($row['memoryswap'] !== "") {
					//Select RAM
				?>
					RAM Slot 1
					<select id="memoryswap" name="memoryswap" class="form-control" required>
						<option value="" selected disabled></option>
				<?php
					$sql4 = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS memoryswaporder FROM orders WHERE memoryswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder,
						IFNULL((SELECT SUM(amount) AS memoryswaporder2 FROM orders WHERE memoryswap2 = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder2,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'RAM'
						GROUP BY SPSKU";
					$result4 = $conn->query($sql4);

					if ($result4->num_rows > 0) {

						while($row4 = $result4->fetch_assoc()) {
							$stock = $row4['stock'] - $row4['memoryswaporder'] - $row4['memoryswaporder2'];
							echo '<option value="' . $row4["SPSKU"]. '">' . $row4["SPSKU"]. ' - ' . $row4["desc"]. ' (' . $stock . ')</option>';
						}
					} else {
						echo "0 results";
					}
				?>
					</select><br>
				<?php
				}

				if ($row['memoryswap'] !== "") {
					//Select RAM
				?>
					RAM Slot 2
					<select id="memoryswap2" name="memoryswap2" class="form-control">
						<option value="" selected disabled></option>
				<?php
					$sql4 = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS memoryswaporder FROM orders WHERE memoryswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder,
						IFNULL((SELECT SUM(amount) AS memoryswaporder2 FROM orders WHERE memoryswap2 = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder2,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'RAM'
						GROUP BY SPSKU";
					$result4 = $conn->query($sql4);

					if ($result4->num_rows > 0) {
						while($row4 = $result4->fetch_assoc()) {
							$stock = $row4['stock'] - $row4['memoryswaporder'] - $row4['memoryswaporder2'];
							echo '<option value="' . $row4["SPSKU"]. '">' . $row4["SPSKU"]. ' - ' . $row4["desc"]. ' (' . $stock . ')</option>';
						}
					} else {
						echo "0 results";
					}
				?>
					</select><br>
				<?php
				}

				if ($row['keyboardswap'] !== "") {
					//Select RAM
				?>
					Keyboard
					<select id="keyboardswap" name="keyboardswap" class="form-control" required>
						<option value="" selected disabled></option>
				<?php
					$sql5 = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS keyboardswaporder FROM orders WHERE keyboardswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS keyboardswaporder,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'KEYBOARD'
						GROUP BY SPSKU";
					$result5 = $conn->query($sql5);

					if ($result5->num_rows > 0) {

						while($row5 = $result5->fetch_assoc()) {
							$stock = $row5['stock'] - $row5['keyboardswaporder'];
							echo '<option value="' . $row5["SPSKU"]. '">' . $row5["SPSKU"]. ' - ' . $row5["desc"]. ' (' . $stock . ')</option>';
						}
					} else {
						echo "0 results";
					}
				?>
					</select><br>
				<?php
				}

				echo '</div>';

			} else {

				echo '<input type="hidden" name="SPSKU" value="' . $row['orderspsku'] . '">';
				echo '<p><strong>' . $row['orderspsku'] . '</strong> moet niet omgebouwd worden.</p>';

			}

			echo '<div id="ombouwOptions"></div>';
			echo '<label style="font-weight: bold;" for="warehouse">Magazijn</label>
				<p>Kies in welk magazijn dit order voorbereid moet worden.</p>
				<select id="warehouse" name="warehouse" class="form-control" required>
				<option value="" selected disabled></option>
				<option value="Signpost">Signpost</option>
				<option value="Copaco">Copaco</option>
				<option value="TechData">Techdata</option>
				</select>
				<br><br>
				<button type="submit" class="btn btn-success">Order Starten</button>';

			//echo json_encode($row);
		}

	} else {

		echo "0 results";

	}

	$conn->close();

} elseif (isset($_GET['id']) !== false && isset($_GET['print']) !== false) {

	$sql = "SELECT *, orders.id AS orderid, orders.status AS orderstatus, orders.notes AS ordernotes,
		orders.spsku AS orderspsku, orders.synergyid AS ordersynergyid, orders.asignee AS orderasignee, orders.history AS orderhistory,
		( SELECT salesorderid FROM forecasts WHERE synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ) AS vkk,
		( SELECT school FROM forecasts WHERE synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ) AS school_naam,
		( SELECT CONCAT(( SELECT synergyid FROM schools WHERE synergyidold = images2019.synergyid), '-', toestel2020, '-V', version2020, '-', ImageNaam) FROM `byod-orders`.images2019 WHERE id = orders.imageid ) AS imagename2019,
		( SELECT Labeling FROM `byod-orders`.images2019 WHERE id = orders.imageid ) AS imagelabel2019,
		( SELECT CONCAT(synergyid, '-', spsku, '-V', version, '-', NAME) FROM `byod-orders`.images2020 WHERE id = orders.imageid ) AS imagename2020,
		( SELECT computername FROM `byod-orders`.images2020 WHERE id = orders.imageid ) AS imagelabel2020,
		( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(orders.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving
		FROM orders
		LEFT JOIN devices ON devices.SPSKU = orders.SPSKU
		WHERE orders.id = '" . $_GET['id'] . "' and orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

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

			echo "<h1>......... toestellen van order SP-BYOD20-" . $row['orderid'] . "<br>
				Pallet ......... van totaal .........</h1><br>";

			echo '<table class="table table-striped">
				<tr><td style="font-weight:bold;">Synergy ID</td><td colspan="5">' . $row['ordersynergyid'] . '</td></tr>
				<tr><td style="font-weight:bold;">School</td><td colspan="5">' . $row['school_naam'] . '</td></tr>';
			if($row['ordernotes'] !== ""){
				echo '<tr><td style="font-weight:bold;">Opmerkingen</td><td colspan="5">' . $row['ordernotes'] . '</td></tr>';
			}

			$toestelsku = $row['productnumber'];

			echo '<tr><td>' . $row['amount'] . ' x Toestel</td><td>' . $row['SPSKU'] . '</td><td>' . $row['productnumber'] . '</td><td colspan="2">' . $row['devicebeschrijving'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>
				<tr><td>' . $row['amount'] . ' x Hoes</td><td colspan="4">' . $row['covers'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';

		if ($row['ssdswap'] !== "" && isset($row['ssdswap']) == true) {
			$sql2 = "SELECT * FROM `device-parts` WHERE SPSKU = '" . $row['ssdswap'] . "' AND SKU IS NOT NULL LIMIT 1";
			$result2 = $conn->query($sql2);

			if ($result2->num_rows > 0) {
				while($row2 = $result2->fetch_assoc()) {
					$ssdswap = $row2['SKU'];
					echo '<tr><td>' . $row['amount'] . ' x SSD</td><td>' . $row2['SPSKU'] . '</td><td>' . $row2['SKU'] . '</td><td colspan="2">' . $row2['desc'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
				}
			} else {
				echo '<tr><td>' . $row['amount'] . ' x SSD</td><td>' . $row['ssdswap'] . '</td><td>SKU niet gevonden</td><td colspan="2">Beschrijving niet gevonden</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
			}
		}

		if ($row['memoryswap'] !== "" && isset($row['memoryswap']) == true) {
			$sql3 = "SELECT * FROM `device-parts` WHERE SPSKU = '" . $row['memoryswap'] . "' AND SKU IS NOT NULL LIMIT 1";
			$result3 = $conn->query($sql3);

			if ($result3->num_rows > 0) {
				while($row3 = $result3->fetch_assoc()) {
					$memoryswap = $row3['SKU'];
					echo '<tr><td>' . $row['amount'] . ' x RAM</td><td>' . $row3['SPSKU'] . '</td><td>' . $row3['SKU'] . '</td><td colspan="2">' . $row3['desc'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
				}
			} else {
				echo '<tr><td>' . $row['amount'] . ' x RAM</td><td>' . $row['memoryswap'] . '</td><td>SKU niet gevonden</td><td colspan="2">Beschrijving niet gevonden</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
			}
		}

		if ($row['memoryswap2'] !== "" && isset($row['memoryswap2']) == true) {
			$sql4 = "SELECT * FROM `device-parts` WHERE SPSKU = '" . $row['memoryswap2'] . "' AND SKU IS NOT NULL LIMIT 1";
			$result4 = $conn->query($sql4);

			if ($result4->num_rows > 0) {
				while($row4 = $result4->fetch_assoc()) {
					$memoryswap2 = $row4['SKU'];
					echo '<tr><td>' . $row['amount'] . ' x RAM 2</td><td>' . $row4['SPSKU'] . '</td><td>' . $row4['SKU'] . '</td><td colspan="2">' . $row4['desc'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
				}
			} else {
				echo '<tr><td>' . $row['amount'] . ' x RAM 2</td><td>' . $row['memoryswap2'] . '</td><td>SKU niet gevonden</td><td colspan="2">Beschrijving niet gevonden</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
			}
		}

		if ($row['panelswap'] !== "" && isset($row['panelswap']) == true) {
			$sql5 = "SELECT * FROM `device-parts` WHERE SPSKU = '" . $row['panelswap'] . "' AND SKU IS NOT NULL LIMIT 1";
			$result5 = $conn->query($sql5);

			if ($result5->num_rows > 0) {
				while($row5 = $result5->fetch_assoc()) {
					$panelswap = $row5['SKU'];
					echo '<tr><td>' . $row['amount'] . ' x Panel</td><td>' . $row5['SPSKU'] . '</td><td>' . $row5['SKU'] . '</td><td colspan="2">' . $row5['desc'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
				}
			} else {
				echo '<tr><td>' . $row['amount'] . ' x Panel</td><td>' . $row['panelswap'] . '</td><td>SKU niet gevonden</td><td colspan="2">Beschrijving niet gevonden</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
			}
		}

		if ($row['keyboardswap'] !== "" && isset($row['keyboardswap']) == true) {
			$sql6 = "SELECT * FROM `device-parts` WHERE SPSKU = '" . $row['keyboardswap'] . "' AND SKU IS NOT NULL LIMIT 1";
			$result6 = $conn->query($sql6);

			if ($result6->num_rows > 0) {
				while($row6 = $result6->fetch_assoc()) {
					$keyboardswap = $row6['SKU'];
					echo '<tr><td>' . $row['amount'] . ' x Keyboard</td><td>' . $row6['SPSKU'] . '</td><td>' . $row6['SKU'] . '</td><td colspan="2">' . $row6['desc'] . '</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
				}
			} else {
				echo '<tr><td>' . $row['amount'] . ' x Keyboard</td><td>' . $row['keyboardswap'] . '</td><td>SKU niet gevonden</td><td colspan="2">Beschrijving niet gevonden</td><td><input type="text" class="form-control" style="width:50px;"></td></tr>';
			}
		}
		echo '</table><br>';

		echo '<h1>......... aantal toestellen omgebouwd op deze pallet</h1>';


		$sql = "SELECT warehouse, location, sum(sku) as aantal FROM stock WHERE sku = '" . rtrim($toestelsku, 'BYOD') . "' GROUP BY warehouse, location";
		$result = $conn->query($sql);
		echo "<p><br>Locaties van " . rtrim($toestelsku, 'BYOD') . ":<br>";
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo $row['warehouse'] . " " . $row['location'] . " - Aantal: " . $row['aantal'] . "<br>";
			}
		} else {
			echo 'Niets gevonden<br>';
		}
		echo "</p>";


		if(isset($ssdswap) == true){
			$tsql2= "SELECT lokatie as Location, sum(vrd) AS StockQty FROM _serienummerlocatie with (nolock)
				WHERE artcode = '" . $ssdswap . "'
				GROUP BY lokatie";
			$getResults2= sqlsrv_query($msconn, $tsql2);
			$results2 = "";

			if ($getResults2 == FALSE){
				echo '2';
				die( print_r( sqlsrv_errors(), true) );
			}

			while ($row2 = sqlsrv_fetch_array($getResults2, SQLSRV_FETCH_ASSOC)) {
				$results2 .= "<p>" . $row2['Location'] . " - Aantal: " . $row2['StockQty'] . "</p>";
			}

			if($results2 == ""){
				//echo "<p>Geen mogelijke locatie gevonden van " . $ssdswap . ".</p>";
			} else {
				echo "<br>Mogelijke locaties van " . $ssdswap . ":";
				echo $results2;
			}

			sqlsrv_free_stmt($getResults2);
		}

		if(isset($memoryswap) == true){
			$tsql3= "SELECT lokatie as Location, sum(vrd) AS StockQty FROM _serienummerlocatie with (nolock)
				WHERE artcode = '" . $memoryswap . "'
				GROUP BY lokatie";
			$getResults3= sqlsrv_query($msconn, $tsql3);
			$results3 = "";

			if ($getResults3 == FALSE){
				echo '3';
				die( print_r( sqlsrv_errors(), true) );
			}

			while ($row3 = sqlsrv_fetch_array($getResults3, SQLSRV_FETCH_ASSOC)) {
				$results3 .= "<p>" . $row3['Location'] . " - Aantal: " . $row3['StockQty'] . "</p>";
			}

			if($results3 == ""){
				//echo "<p>Geen mogelijke locatie gevonden van " . $memoryswap . ".</p>";
			} else {
				echo "<br>Mogelijke locaties van " . $memoryswap . ":";
				echo $results3;
			}

			sqlsrv_free_stmt($getResults3);
		}

		if(isset($memoryswap2) == true){
			$tsql4= "SELECT lokatie as Location, sum(vrd) AS StockQty FROM _serienummerlocatie with (nolock)
				WHERE artcode = '" . $memoryswap2 . "'
				GROUP BY lokatie";
			$getResults4= sqlsrv_query($msconn, $tsql4);
			$results4 = "";

			if ($getResults4 == FALSE){
				echo '4';
				die( print_r( sqlsrv_errors(), true) );
			}

			while ($row4 = sqlsrv_fetch_array($getResults4, SQLSRV_FETCH_ASSOC)) {
				$results4 .= "<p>" . $row4['Location'] . " - Aantal: " . $row4['StockQty'] . "</p>";
			}

			if($results4 == ""){
				//echo "<p>Geen mogelijke locatie gevonden van " . $memoryswap2 . ".</p>";
			} else {
				echo "<br>Mogelijke locaties van " . $memoryswap2 . ":";
				echo $results4;
			}

			sqlsrv_free_stmt($getResults4);
		}

		if(isset($panelswap) == true){
			$tsql5= "SELECT lokatie as Location, sum(vrd) AS StockQty FROM _serienummerlocatie with (nolock)
				WHERE artcode = '" . $panelswap . "'
				GROUP BY lokatie";
			$getResults5= sqlsrv_query($msconn, $tsql5);
			$results5 = "";

			if ($getResults5 == FALSE){
				echo '5';
				die( print_r( sqlsrv_errors(), true) );
			}

			while ($row5 = sqlsrv_fetch_array($getResults5, SQLSRV_FETCH_ASSOC)) {
				$results5 .= "<p>" . $row5['Location'] . " - Aantal: " . $row5['StockQty'] . "</p>";
			}

			if($results5 == ""){
				//echo "<p>Geen mogelijke locatie gevonden van " . $panelswap . ".</p>";
			} else {
				echo "<br>Mogelijke locaties van " . $panelswap . ":";
				echo $results5;
			}

			sqlsrv_free_stmt($getResults5);
		}

		if(isset($keyboardswap) == true){
			$tsql6= "SELECT lokatie as Location, sum(vrd) AS StockQty FROM _serienummerlocatie with (nolock)
				WHERE artcode = '" . $keyboardswap . "'
				GROUP BY lokatie";
			$getResults6= sqlsrv_query($msconn, $tsql6);
			$results6 = "";

			if ($getResults6 == FALSE){
				die( print_r( sqlsrv_errors(), true) );
			}

			while ($row6 = sqlsrv_fetch_array($getResults6, SQLSRV_FETCH_ASSOC)) {
				$results6 .= "<p>" . $row6['Location'] . " - Aantal: " . $row6['StockQty'] . "</p>";
			}

			if($results6 == ""){
				//echo "<p>Geen mogelijke locatie gevonden van " . $keyboardswap . ".</p>";
			} else {
				echo "<br>Mogelijke locaties van " . $keyboardswap . ":";
				echo $results6;
			}

			sqlsrv_free_stmt($getResults6);
		}

		echo '<script type="text/javascript">
			window.print();
			</script>';
		}

	} else {

		echo "0 results";

	}

	$conn->close();

} elseif (isset($_GET['id']) !== false) {

	$sql = "SELECT *, orders.id AS orderid, orders.status AS orderstatus, orders.notes AS ordernotes, orders.label AS orderlabel,
		orders.spsku AS orderspsku, orders.synergyid AS ordersynergyid, orders.asignee AS orderasignee, orders.history AS orderhistory,
		( SELECT GROUP_CONCAT(CONCAT('-', amount, ' (', timestamp, ')')) FROM orderpicking WHERE orderid = orders.id ) AS pickinghistory,
		( SELECT salesorderid FROM forecasts WHERE synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ) AS vkk,
		( SELECT school FROM forecasts WHERE synergyid = orders.synergyid AND deleted != 1 LIMIT 1 ) AS school_naam,
		( SELECT CONCAT(( SELECT synergyid FROM schools WHERE synergyidold = images2019.synergyid), '-', toestel2020, '-V', version2020, '-', ImageNaam) FROM `byod-orders`.images2019 WHERE id = orders.imageid ) AS imagename2019,
		( SELECT Comment FROM `byod-orders`.images2019 WHERE id = orders.imageid ) AS comments2019,
		( SELECT Labeling FROM `byod-orders`.images2019 WHERE id = orders.imageid ) AS imagelabel2019,
		( SELECT CONCAT(synergyid, '-', spsku, '-V', version, '-', NAME) FROM `byod-orders`.images2020 WHERE id = orders.imageid ) AS imagename2020,
		( SELECT name FROM `byod-orders`.images2020 WHERE id = orders.imageid ) AS imageshortname2020,
		( SELECT notes FROM `byod-orders`.images2020 WHERE id = orders.imageid ) AS comments2020,
		( SELECT computername FROM `byod-orders`.images2020 WHERE id = orders.imageid ) AS imagelabel2020,
		CONCAT(( SELECT signpost_label FROM schools WHERE synergyid = orders.synergyid LIMIT 1 ), SUBSTRING(orders.campagne, 3, 4)) AS signpost_label
		FROM orders
		LEFT JOIN devices ON devices.SPSKU = orders.SPSKU
		WHERE orders.id = '" . $_GET['id'] . "' and orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {
			echo "<h3>Order SP-BYOD20-" . $row['orderid'] . "</h3>";
			echo '<h4 style="font-weight:bold; color:red;">' . $row['orderstatus'] . '</h4><br>';
			?>

				<table class="table">

					<tr>
						<th scope="row" width="15%" class="<?php if($row['orderstatus'] == 'nieuw'){ echo "table-danger";}else{ echo "btn-outline-danger";}; ?>">nieuw</th>
						<th scope="row" class="<?php if($row['orderstatus'] == 'ombouw'){ echo "table-warning";}else{ echo "btn-outline-warning";}; ?>">ombouw</th>
						<th scope="row" class="<?php if($row['orderstatus'] == 'wachten op image'){ echo "table-info";}else{ echo "btn-outline-info";}; ?>">wachten op image</th>
						<th scope="row" class="<?php if($row['orderstatus'] == 'imaging'){ echo "table-primary";}else{ echo "btn-outline-primary";}; ?>">imaging</th>
						<th scope="row" class="<?php if($row['orderstatus'] == 'levering'){ echo "table-secondary";}else{ echo "btn-outline-secondary";}; ?>">levering</th>
						<th scope="row" class="<?php if($row['orderstatus'] == 'uitgeleverd'){ echo "table-success";}else{ echo "btn-outline-success";}; ?>">uitgeleverd</th>
					</tr>

				</table>

			<?php
			echo '<p>';
			if(hasRole($role, ['management'])){
				echo '<a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&status=' . $row['orderstatus'] . '&statuschange', false).'"><button class="btn btn-primary">Status aanpassen</button></a><br><br>';
			}
			echo '<strong>Synergy ID:</strong> <a href="'. hasAccessForUrl('school.php?synergyid=' . $row['ordersynergyid'] . '', false).'">' . $row['ordersynergyid'] . '</a><br>
		<strong>Verkoopkans:</strong> ' . $row['vkk'] . '<br>
		<strong>School:</strong> ' . $row['school_naam'] . '<br>
			<strong>Sales:</strong> ' . $row['sales'] . '<br>
			<strong>Financiering Type:</strong> ' . $row['finance_type'] . '<br>
			<strong>Consument:</strong> ' . $row['consumer'] . '<br>
			<strong>Aangevraagd op:</strong> ' . $row['requested_on'] . '<br>
		<strong>Plaats van imaging:</strong> ' . $row['warehouse'] . '<br><br>
		<strong><a href="'. hasAccessForUrl('scripting.php?type=csv&orderid=' . $row['orderid'] . '', false).'">Bekijk label/serienummers van dit order</a></strong><br><br>
		<strong><a href="'. hasAccessForUrl('delivery.php?orderid=' . $row['orderid'] . '', false).'">Bekijk suborders</a></strong><br><br>

			<strong>SPSKU:</strong> ' . $row['orderspsku'] . '<br>';
		if(strpos($row['orderspsku'], '-O') == true){
			echo '<p><strong>Ombouw info:</strong><br>';
				if ($row['ssdswap'] !== "" && isset($row['ssdswap']) == true) {
					echo "SSD (" . $row['ssd_code'] . " -> " . $row['ssdswap'] . ")<br>";
				}

				if ($row['memoryswap'] !== "" && isset($row['memoryswap']) == true) {
					echo "RAM Slot 1 (" . $row['memory_code'] . " -> " . $row['memoryswap'] . ")<br>";
				}

				if ($row['memoryswap2'] !== "" && isset($row['memoryswap2']) == true) {
					echo "RAM Slot 2 ( ...  -> " . $row['memoryswap2'] . ")<br>";
				}

				if ($row['panelswap'] !== "" && isset($row['panelswap']) == true) {
					echo "Panel (" . $row['panel_code'] . " -> " . $row['panelswap'] . ")<br>";
				}

				if ($row['keyboardswap'] !== "" && isset($row['keyboardswap']) == true) {
					echo "Keyboard (" . $row['keyboard_code'] . " -> " . $row['keyboardswap'] . ")<br>";
				}
			echo '</p>';
		}
			echo '<strong>Aantal Computers:</strong> ' . $row['amount'] . '<br>
				<strong>Hoes?</strong> ' . $row['covers'] . '<br></p>';

			if ($row['imageid'] == "geen") {

				echo '<p><strong>Image:</strong> Geen image<br>';

			} elseif ($row['imagename2020'] == 'fabriek' || $row['imageid'] == 'fabriek') {

				echo '<p><strong>Image:</strong> OOBE Fabriek (Lenovo/HP Incl. bloatware) - Dus moeten niet geimaged als OS aanwezig is<br>';

			} elseif ($row['imagename2020'] == 'chrome' || $row['imageid'] == 'chrome') {

				echo '<p><strong>Image:</strong> Chromebook<br>';

			} elseif ($row['imagename2019'] !== '' && isset($row['imagename2019']) !== false) {

				echo '<p><strong>Image:</strong> <a href="'. hasAccessForUrl('image.php?id=' . $row['imageid'] . '&jaar=2019', false).'">' . $row['imagename2019'] . '</a><br>';
				echo '<strong>Opmeringen Image: </strong> ' . $row['comments2019'] . '<br>';
				if($row['imagelabel2019'] !== ''){
					echo '<p style="color:red">Opgelet, label "' . $row['imagelabel2019'] . '" gekozen in de image-intake!</p>';
				}

			} elseif ($row['imagename2020'] !== '' && isset($row['imagename2020']) !== false) {

				echo '<p><strong>Image:</strong> <a href="'. hasAccessForUrl('image.php?id=' . $row['imageid'] . '&jaar=2020', false).'">' . $row['imagename2020'] . '</a><br>';
				echo '<strong>Opmeringen Image: </strong> ' . $row['comments2020'] . '<br>';
				if($row['imagelabel2020'] !== ''){
					echo '<p style="color:red">Opgelet, label "' . $row['imagelabel2020'] . '" gekozen in de image-intake!</p>';
				}

			} else {

				echo '<p><strong>Image:</strong> Image moet nog gekoppeld worden<br>';
				if (hasRole($role, ['management'])){
					echo '<a href="'. hasAccessForUrl('image.php?orderid=' . $row['orderid'] . '', false).'">( Klik hier om te koppelen )</a><br>';
				}

			}

			echo '<strong>Signpost Label: </strong> ' . $row['signpost_label'] . '<br>';
			echo '<strong>School Label: </strong> ' . $row['orderlabel'];
			if(hasRole($role, ['management'])){
				echo '<a href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&labelchange', false).'">( Klik hier het schoollabel aan te passen )</a>';
			}
			echo '<br>';

			echo '<br><strong>Opmerkingen Order:</strong><br>';
			if($row['notes'] == ""){
				echo 'Geen opmerkingen<br><br>';
			} else {
				echo '' . $row['notes'] . '<br><br>';
			}

			echo '<strong>Afleveradres:</strong><br>' . $row['shipping_street'] . ' ' . $row['shipping_number'] . '<br>
				' . $row['shipping_postcode'] . ' ' . $row['shipping_city'] . '<br></p>';

			echo '<p><strong>Uitlevering</strong><br>
				' . $row['shipping_date'] . ' ' . $row['shipping_hour'] . '<br></p>';

			echo '<a class="btn btn-secondary" href="'. hasAccessForUrl('order.php?id=' . $row['orderid'] . '&print=true', false).'">Printen</a><br><br>';

			if ($row['ordernotes'] !== "") {
				echo '<p><strong>Extra uitleg</strong><br>' . $row['ordernotes'] . '<br></p>';
			}

			if (hasRole($role, ['management', 'software', 'webshop', 'orderhistory'])) {
				echo '
					<form action="order.php" method="post">
						<input type="text" name="id" id="id" value="' . $row['orderid'] . '" hidden>
						<input type="text" name="user" id="user" value="' . $loginname . '" hidden>
						<label for="asignee"><b>Toegewezen op:</b></label><br>
						<select name="asignee" class="form-control" id="asignee">';

				if ($row['orderasignee'] !== '') {
					echo '<option value="' . $row['orderasignee'] . '">' . $row['orderasignee'] . '</option>';
				}

				echo '<option value="">Niet toegewezen</option>
					<option value="Alain.leuregans">Alain</option>
					<option value="Bart">Bart C</option>
					<option value="Ismail">Ismail</option>
					<option value="Jelle">Jelle</option>
					<option value="Jens">Jens</option>
					<option value="Joe.specker">Joe</option>
					<option value="Jordy">Jordy</option>
					<option value="Mike">Mike</option>
					<option value="Nathalie.desmaele">Nathalie</option>
					<option value="Quinten">Quinten</option>
					<option value="Thomas">Thomas</option>
					<option value="Yakup">Yakup</option>
					</select>
					<br>
					<label for="status_notes"><b>Status beschrijving:</b></label><br>
					<textarea id="status_notes" name="status_notes" rows="4" class="form-control">' . $row['status_notes'] . '</textarea><br>
					<input type="submit" value="Opslaan" class="btn btn-danger"><br>
					</form>
				';
			}

			echo '<strong>Geschiedenis</strong><br>';
			$history = explode(';', $row['orderhistory']);
			foreach ($history as $event) {
				echo $event . '<br>';
			}
			echo '<br>';

			echo '<strong>Stock picks</strong><br>';
			$picks = explode(',', $row['pickinghistory']);
			foreach ($picks as $event) {
				echo $event . '<br>';
			}

			//echo json_encode($row);
		}

	} else {

		echo "0 results";

	}

	$conn->close();

}

?>

		</tbody>
	</table><br><br><br><br>
</div>

<script>

//manufacturer - model - motherboard - memory - ssd - panel
$(document).ready(function() {
	$('#SPSKU').on('change', function(){
		var spsku = $(this).val();
		if(spsku){
			$.ajax({
			type:'POST',
				url:'ajaxComputerData.php',
				data:'spsku='+spsku+'&ombouw=true',
				success:function(html){
					$('#ombouwOptions').html(html);
				}
			});
		}
	});
});
</script>

<?php
include('footer.php');
?>
