<?php

$title = 'Imaging Windows';
include('head.php');
include('nav.php');
include('conn.php');

?>

<?php
if (isset($_GET['labels_created']) !== false) {

	$sql = "UPDATE orders SET labels_created = '1' WHERE id = '" . $_GET['labels_created'] . "'";

	if ($conn->query($sql) === TRUE) {
		echo "Record updated successfully";
		if(isset($_GET['chrome']) == true){
			$URL = "imaging-chrome.php";
		} else {
			$URL = "imaging.php";
		}
		if( headers_sent() ) { echo("<script>location.href='$URL'</script>"); }
		else { header("Location: $URL"); }
		exit;
	} else {
		echo "Error updating record: " . $conn->error;
	}

	$conn->close();

} elseif (isset($_POST['orderid']) !== false) {

	echo "<br><br>";
	//print_r($_POST);

	echo '<br><br><a href="'. hasAccessForUrl('imaging.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
	echo "<br><br>";

	foreach ($_POST as $key => $value) {
		if (strpos($key, 'orderid') !== FALSE) {
			$orderid = $value;
		} elseif (strpos($key, 'synergyid') !== FALSE) {
			$synergyid = $value;
		} elseif (strpos($key, 'doneby') !== FALSE) {
			$doneby = $value;
		} elseif (strpos($key, '-') !== FALSE && strpos($key, '_desc') == FALSE && strpos($key, '_model') == FALSE && isset($orderid) == true && isset($synergyid) == true && isset($doneby) == true) {

			echo "<br>" . $key . "," . $value . " - ";

			$sql = "SELECT * FROM `labels` where label = '" . $key . "' and orderid = '" . $_POST['orderid'] . "'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					$keydesk = $key . '_desc';
					$keymodel = $key . '_model';
					if(isset($value) == true){
						if ($row['serialnumber'] !== $value && $value !== '') {
							$sql = "UPDATE `labels` SET serialnumber='" . $value . "' WHERE orderid = '" . $orderid . "' AND synergyid = '" . $synergyid . "' AND label = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Serial updated successfully<br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}
						}
					}
					if(isset($_POST[$keydesk]) == true){
						if ($row['serialnumbernote'] !== $_POST[$keydesk] && $_POST[$keydesk] !== '') {
							$sql = "UPDATE `labels` SET serialnumbernote='" . $_POST[$keydesk] . "' WHERE orderid = '" . $orderid . "' AND synergyid = '" . $synergyid . "' AND label = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Desc updated successfully<br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}
						}
					}
					if(isset($_POST[$keymodel]) == true){
						if ($row['model'] !== $_POST[$keymodel] && $_POST[$keymodel] !== '') {
							$sql = "UPDATE `labels` SET model='" . $_POST[$keymodel] . "' WHERE orderid = '" . $orderid . "' AND synergyid = '" . $synergyid . "' AND label = '" . $key . "'";

							if ($conn->query($sql) === TRUE) {
								echo "Model updated successfully<br>";
							} else {
								echo "Error updating record: " . $conn->error;
							}
						}
					}
				}
			} else {
				if (strpos($key, '_desc') == false || strpos($key, '_model') == false) {

					$desc = '';
					$keydesk = $key . '_desc';
					$desc = $_POST[$keydesk];

					$model = '';
					$keymodel = $key . '_model';
					$model = $_POST[$keymodel];

					$labels = explode('__', $key);
					$signpostlabel = $labels[0];
					$schoollabel = $labels[1];

					$sql = "INSERT INTO `labels` (orderid, synergyid, signpost_label, label, serialnumber, model, serialnumbernote, doneby)
						VALUES ('" . $orderid . "', '" . $synergyid . "', '" . $signpostlabel . "', '" . $schoollabel . "', '" . $value . "', '" . $model . "', '" . $desc . "', '" . $doneby . "')";

					if ($conn->query($sql) === TRUE) {
						echo "New record created successfully<br>";
					} else {
						echo "Error: " . $sql . "<br>" . $conn->error;
					}
				}
			}
		}
	}

	$conn->close();

} elseif (isset($_GET['id']) !== false && isset($_GET['edit']) !== false) {

	$label = '';
	$sql = "SELECT
		q.id AS orderid,
		q.imageid AS imageid,
		q.synergyid AS synergyid,
		q.SPSKU AS SPSKU,
		q.covers AS covers,
		q.sales AS sales,
		q.amount AS amount,
		q.campagne AS campagne,
		q.label_prefix AS label_prefix,
		schools.signpost_label_prefix AS signpost_label_prefix,
		ifnull(schools.signpost_label, '') AS signpost_label,
		(SELECT IFNULL(MAX(SUBSTRING_INDEX(signpost_label, '-', '-1')+0),0)+1 FROM `labels` WHERE signpost_label LIKE CONCAT(schools.signpost_label, SUBSTRING(q.campagne, 3, 4), '%')) AS next_signpost_label,
		ifnull(q.label, '') AS label,
		(SELECT IFNULL(MAX(SUBSTRING_INDEX(label, '-', '-1')+0),0)+1 FROM `labels` WHERE label LIKE CONCAT(q.label,'%') AND synergyid = q.synergyid) AS next_label,
		q.synergyid AS ordersynergyid,
		( SELECT COUNT(*) FROM labels WHERE orderid = q.id ) AS labelsfound,
		(SELECT operating_system FROM `devices` WHERE spsku = q.spsku) AS operating_system,
		(SELECT CONCAT(( SELECT synergyid FROM schools WHERE synergyidold = images2019.synergyid), '-', toestel2020, '-V', version2020, '-', ImageNaam) FROM `byod-orders`.images2019 WHERE images2019.id = q.imageid ) AS imagename2019,
		( SELECT CONCAT(synergyid, '-', spsku, '-V', VERSION, '-', NAME) FROM `byod-orders`.images2020 WHERE id = q.imageid ) AS imagename2020,
		( SELECT NAME FROM `byod-orders`.images2020 WHERE id = q.imageid ) AS imageshortname2020
		FROM orders q
		INNER JOIN schools ON schools.synergyid = q.synergyid
		WHERE q.id = '" . $_GET['id'] . "' and q.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {

			if($row['signpost_label'] == "" && $row['imageid'] != 'fabriek') {
				echo "<h4>Geen Signpost label gevonden voor dit order, dus kan niet gestart worden.</h4>";
				die();
			}

			echo '<div style="width:98%">';
			echo "<h3>Imagen van order SP-BYOD20-" . $row['orderid'] . "</h3><br>";

			echo "<b>Synergy ID:</b> " . $row['synergyid'] . "<br>
				<b>Image:</b> " . $row['imagename2020'] . "" . $row['imagename2019'] . "<br>
				<b>SPSKU</b>: " . $row['SPSKU'] . "<br>
				<b>Hoes</b>: " . $row['covers'] . "<br>
				<b>Sales</b>: " . $row['sales'] . "<br><br>";

			echo '<form action="imaging.php" method="post" class="form">
				<input type="hidden" name="orderid" value="' . $row['orderid'] . '">
				<input type="hidden" name="synergyid" value="' . $row['ordersynergyid'] . '">
				<input type="hidden" name="label" value="' . $row['label'] . '">
				<input type="hidden" name="signpost_label" value="' . $row['signpost_label'] . '">
				<input type="text" class="form-control" name="doneby" value="' . $loginname . '" readonly><br><br>';

			echo'<div class="form-group row">';
			if($row['signpost_label'] !== ''){
				echo '<label for="serial" class="col-sm-2 col-form-label">
					<b>Signpost Label</b>
				</label>';
			}
			if($row['label'] !== ''){
				echo '<label for="serial" class="col-sm-2 col-form-label">
					<b>School Label</b>
				</label>';
			}
			echo '<div class="col-sm">
				<b>Serial</b>
				</div>';
			if($loginname !== "Techdata"){
				echo '<div class="col-sm">
					<b>Model</b>
					</div>';
			}
			echo '<div class="col-sm">
				<b>Extra info</b>
				</div>
				</div>';


			if ($row['labelsfound'] == $row['amount']) {

				//$sql = "SELECT label, serialnumber, model, serialnumbernote, (SELECT COUNT(*) FROM devicedata WHERE SerieNummer = TRIM(LEADING 'S'FROM q.serialnumber)) as scripted FROM labels q WHERE orderid = '" . $row['orderid'] . "'";
				$sql2 = "SELECT ifnull(signpost_label, '') as signpost_label, ifnull(label, '') as label, serialnumber, model, serialnumbernote, (SELECT COUNT(*) FROM devicedata WHERE SerieNummer = IF(SUBSTR(q.serialnumber,1,3) = '5CD', SUBSTR(q.serialnumber, 1, 10), TRIM(LEADING 'S'FROM q.serialnumber))) as scripted FROM labels q WHERE orderid = '" . $row['orderid'] . "'";
				$result2 = $conn->query($sql2);

				if ($result2->num_rows > 0) {
					while($row2 = $result2->fetch_assoc()) {
						echo'<div class="form-group row">';
						if($row['signpost_label'] !== ''){
							echo '<label for="serial" class="col-sm-2 col-form-label">' . $row2['signpost_label'] . '</label>';
						}
						if($row['label'] !== ''){
							echo '<label for="serial" class="col-sm-2 col-form-label">' . $row2['label'] . '</label>';
						}
						echo '<div class="col-sm">
						<input type="text" class="form-control" id="serial" name="' . $row2['label'] . '" placeholder="" value="' . $row2['serialnumber'] . '">
						</div>';
						if($loginname !== "Techdata"){
							echo '<div class="col-sm">
								<input type="text" class="form-control" id="model" name="' . $row2['label'] . '_model" placeholder="" value="' . $row2['model'] . '">
								</div>';
						}
						echo '<div class="col-sm">
							<input type="text" class="form-control" id="desc" name="' . $row2['label'] . '_desc" placeholder="" value="' . $row2['serialnumbernote'] . '"';
						if($loginname !== 'Techdata'){
							echo ' tabindex="-1"';
						}
						echo '>
						</div>';

						if($row2["scripted"] == '0'){
							echo '<td>❌</td>';
						} else {
							echo '<td>✔</td>';
						}

						echo '</div>';
					}
				} else {
					echo "Er is een fout opgetreden bij de al ingegeven serienummers";
				}

			} else {

				$labelNumber = $row['next_label'];

				for ($x = $row['next_signpost_label']; $x <= $row['next_signpost_label']+$row['amount']-1; $x++) {

					if($row['signpost_label_prefix'] == true) {
						$signpostLabelNumber = sprintf("%04d", $x);
					} else {
						$signpostLabelNumber = $x;
					}

					$signpostlabel = $row['signpost_label'] . substr($row['campagne'], 2) . '-' . $signpostLabelNumber;

					if($row['label_prefix'] == true) {
						$labelNumber = sprintf("%04d", $labelNumber);
					}
					$label = $row['label'] . '-' . $labelNumber;

					echo'<div class="form-group row">';
					if($row['signpost_label'] !== ''){
						echo '<label for="serial" class="col-sm-2 col-form-label">' . $signpostlabel . '</label>';
					}
					if($row['label'] !== ''){
						echo '<label for="serial" class="col-sm-2 col-form-label">' . $label . '</label>';
					}
					echo '<div class="col-sm">
						<input type="text" class="form-control" id="serial" name="' . $signpostlabel . '__' . $label . '" placeholder="" value="">
						</div>
						<div class="col-sm">
						<input type="text" class="form-control" id="model" name="' . $signpostlabel . '__' . $label . '_model" placeholder="" value="">
						</div>
						<div class="col-sm">
						<input type="text" class="form-control" id="desc" name="' . $signpostlabel . '__' . $label . '_desc" placeholder="" value="" tabindex="-1">
						</div>
						</div>';
					$labelNumber = $labelNumber + 1;

				}
			}

			echo '<br>';
			echo '<button type="submit" class="btn btn-success">Gegevens Opslaan</button><br><br><br>';
			echo '</form>';
			echo '</div>';

		}

	} else {

		echo "0 results";

	}

	$conn->close();

} elseif(isset($_GET['id']) !== false && isset($_GET['finish']) !== false && isset($_GET['post']) !== false) {

	$sql = "UPDATE orders SET status='levering' WHERE id=" . $_GET['id'];

	if ($conn->query($sql) === TRUE) {
		echo "Order is aangepast naar status 'levering'<br>
			<a href='imaging.php'>Klik hier om terug te gaan naar het overzicht</a>";
	} else {
		echo "Error updating record: " . $conn->error;
	}

} elseif(isset($_GET['id']) !== false && isset($_GET['finish']) !== false) {

	$donotfinish = false;
	$sql = "SELECT * FROM orders where id = '" . $_GET['id'] . "' and deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {

			echo "Bent u zeker dat u bestelling nr " . $row["id"] . " wilt afwerken en doorsturen naar delivery met onderstaande gegevens? <br><br>";

			echo "<table class='table'>
				<tr><th>Signpost Label</th><th>School Label</th><th>Serial</th><th>Model</th><th>Scripting ok?</th><th>Opmerkingen</th></tr>";

			$sql2 = "SELECT *, (SELECT COUNT(*) FROM `labels` WHERE serialnumber = q.serialnumber) as duplicate,
				(SELECT COUNT(*) FROM devicedata WHERE SerieNummer = IF(SUBSTR(q.serialnumber,1,3) = '5CD', SUBSTR(q.serialnumber, 1, 10), TRIM(LEADING 'S'FROM q.serialnumber))) as scripted FROM labels q WHERE orderid = " . $row["id"];
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
					echo "<td>" . $row2["signpost_label"] . "</td><td>" . $row2["label"] . "</td><td>" . $row2["serialnumber"] . "</td><td>" . $row2["model"] . "</td>";
					if($row2["scripted"] == '0'){
						echo '<td>❌</td>';
					} else {
						echo '<td>✔</td>';
					}
					echo "<td>" . $row2["serialnumbernote"] . "</td></tr>";
				}
			} else {
				echo "0 results";
			}

			echo "</table>";

			echo "<br>";
			if($donotfinish == true){
				echo "<p style='color:red;font-weight:bold;'>Er zijn dubbele serienummers gevonden ( zie rood ), hierdoor kan het order niet verdergaan naar levering.</p>";
			} else {
				echo "<a class='btn btn-success' href='imaging.php?id=" . $_GET['id'] . "&finish=true&post=true'>Ja</a> <a class='btn' href='imaging.php'>Nee</a>";
			}

		}

	} else {
		echo "0 results";
	}
	$conn->close();

} else {

	if($role == "copaco"){
		$URL = "copaco-orders.php";
		if( headers_sent() ) { echo("<script>location.href='$URL'</script>"); }
		else { header("Location: $URL"); }
		exit;
	}
	if($role == "techdata"){
		$URL = "techdata-orders.php";
		if( headers_sent() ) { echo("<script>location.href='$URL'</script>"); }
		else { header("Location: $URL"); }
		exit;
	}

?>

	<h3>Imaging Orders</h3>

	<table class="table" id="table2">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergy ID</th>
				<th scope="col">School</th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, q.id as orderid, q.synergyid as synergyidid,
				ifnull( (select name from images2020 where id = q.imageid), (select ImageNaam from images2019 where id = q.imageid )) as imagename,
				ifnull( (select authentication from images2020 where id = q.imageid), (select Authenticatie from images2019 where id = q.imageid )) as authentication,
				q.SPSKU as signpostsku, (SELECT count(serialnumber)
				FROM `labels` where orderid = q.id and serialnumber != '') as done,
				( SELECT CONCAT(devices.model, ' - ', devices.motherboard_value, ' - ', devices.ssd_value, 'GB SSD - ', devices.memory_value, 'GB RAM - ', devices.panel_value) FROM devices WHERE SPSKU = SUBSTRING_INDEX(SUBSTRING_INDEX(q.`SPSKU`, ';', 1), '-O', 1) LIMIT 1 ) AS devicebeschrijving,
				( SELECT school_name FROM schools where synergyid = q.synergyid LIMIT 1) as school_name
				FROM `byod-orders`.orders as q
				LEFT JOIN `byod-orders`.devices ON q.SPSKU = devices.SPSKU
				WHERE ( SELECT operating_system FROM devices WHERE SPSKU = q.SPSKU LIMIT 1) != 'Chrome OS' AND q.status = 'imaging' AND warehouse = 'Signpost'
				ORDER BY q.synergyid";
			$result = $conn->query($sql);
			$schools = "";

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					if($schools !== $row['synergyidid']){
						echo '
							<tr class="table-primary">
							<th scope="col">' . $row['synergyidid'] . '</th>
							<th scope="col">' . $row['school_name'] . '</th>
							<th scope="col">Image</th>
							<th scope="col">Type</th>
							<th scope="col">Scripting</th>
							<th scope="col">Aantal</th>
							<th scope="col">Todo</th>
							<th scope="col">Labels ok?</th>
							<th scope="col"></th>
						</tr>
						';
						$schools = $row['synergyidid'];
					}

					$todo = $row['amount'] - $row['done'];

					if ($row['done'] >= 1 && $todo != 0) {
						$color = 'btn-outline-warning';
					} elseif ($todo == 0) {
						$color = 'btn-outline-success';
					} else {
						$color = 'btn-outline-danger';
					}

					$url = "document.location = 'order.php?id=" . $row['orderid'] . "'";

					echo '<tr onclick="' . $url . '" class="' . $color . '">';
					echo '<td scope="row">SP-BYOD20-' . $row['orderid'] . '</td>';

					echo '<td>' . $row['devicebeschrijving'] . '<br><span class="smalltext">' . $row['signpostsku'] . '</span></td>';

					if ($row['imagename'] !== '' && isset($row['imagename']) !== false) {
						$imagename = $row['imagename'];
					} else {
						$imagename = $row['imageid'];
					}

					echo '<td>' . $imagename . '</td>';

					echo '<td>' . $row['authentication'] . '</td>';

					if ($row['authentication'] == '' || $row['authentication'] == 'intune') {
						$scripting = "Nee";
					} else {
						$scripting = "Ja ⚠";
					}

					echo '<td>' . $scripting . '</td>';

					echo '<td>' . $row['amount'] . '</td>';
					echo '<td><strong>' . $todo . '</strong></td>';

					if ($row['labels_created'] == '0') {
						echo '<td>Nee ⚠</td>';
					} else {
						echo '<td>Ja</td>';
					}

					echo '<td class="" style="text-align: right;">';

					echo '<a href="'. hasAccessForUrl('imaging.php?id=' . $row['orderid'] . '&edit=true', false).'">
						<button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px 5px;">Imagen</button>
						</a>';

						if ($row['labels_created'] == '0') {
							echo '<a href="'. hasAccessForUrl('imaging.php?labels_created=' . $row['orderid'] . '', false).'">
								<button type="button" class="btn btn-secondary" style="height:50px !important;width:100px !important;padding:0px;margin:0px;">Labels afgedrukt</button>
								</a>';
						}

						if ($color == 'btn-outline-success') {
							echo '<a href="'. hasAccessForUrl('imaging.php?id=' . $row['orderid'] . '&finish=true', false).'">
								<button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Afwerken</button>
								</a>';
						}
						echo '</td>';
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

<script>
$(document).ready( function () {
	$('#table2').DataTable( {
		"paging":   false,
		//"order": [[ 0, "asc" ]],
		"ordering": false,
		"info":     false
	});
} );
$('.form').on('keyup keypress', function(e) {
	var keyCode = e.keyCode || e.which;
	if (keyCode === 13) {
		e.preventDefault();
		return false;
	}
});
</script>

<?php
include('footer.php');
?>
