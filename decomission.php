<?php

$title = 'Decomission';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

?>

<div class="body">

<?php
if(isset($_POST['serials']) == true){

	$serials = substr($_POST['serials'], 0, -1);
	$serials = explode(';', $serials);

	foreach($serials as $serial){

		$state = 0;
		$serial = explode('&&', $serial);

		// steek label data in 'decomission'
		$sql = "INSERT INTO decomissioned
			(orderid, synergyid, label, serialnumber, model, serialnumbernote, doneby, datetime)
			SELECT
			orderid, synergyid, label, serialnumber, model, serialnumbernote, doneby, datetime
			FROM
			labels
			WHERE
			label = '" . $serial[1] . "' AND orderid = '" . $serial[2] . "';";
		echo '<p style="display: none;">' . $sql . '</p>';

		if ($conn->query($sql) === TRUE) {
			//echo "Label succesvol aangemaakt bij decomissioned.<br>";
			$state = 1;
		} else {
			echo "Error insert in decomission: " . $sql . "<br>" . $conn->error;
		}


		// verwijderen van uit de labels tabel
		$sql = "DELETE FROM labels
			WHERE label = '" . $serial[1] . "' AND orderid = '" . $serial[2] . "';";
		echo '<p style="display: none;">' . $sql . '</p>';

		if ($conn->query($sql) === TRUE) {
			//echo "Label succesvol verwijderd uit de labels tabel.<br>";
			$state = 2;
		} else {
			echo "Error deleting record: " . $conn->error;
		}


		// order verminderen met 1
		$sql = "UPDATE orders SET amount = amount-1 WHERE id = '" . $serial[2] . "';";
		echo '<p style="display: none;">' . $sql . '</p>';

		if ($conn->query($sql) === TRUE) {
			//echo "Order succesvol verminderd met 1.<br>";
			$state = 3;
		} else {
			echo "Error updating order amount: " . $conn->error;
		}


		$sql = "UPDATE `byod-orders`.`orders` SET status_notes=concat(status_notes, ' ', '" . $serial[0] . " gedecomissioned door " . $loginname . "'), history =
		CASE WHEN history IS NULL
			THEN concat('[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] \"" . $serial[0] . "\" gedecomissioned door " . $loginname . "')
			ELSE concat(history,'<br>[" . $loginname . " - ',DATE_FORMAT(NOW(), \"%d/%m/%Y %H:%i\"),'] \"" . $serial[0] . "\" gedecomissioned door " . $loginname . "')
		END
		WHERE id=" . $serial[2];
		echo '<p style="display: none;">' . $sql . '</p>';

		if ($conn->query($sql) === TRUE) {
			//echo "Aanpassing succesvol weggeschreven op het order.<br>";
			$state = 4;
		} else {
			echo "Error updating order history: " . $conn->error;
		}

		if($state >= 3){
			echo 'LABEL ' . $serial[1] . ' gevonden, verwijderd uit LABEL tabel en order vermiderd met 1.<br>';
		}

	}

	echo '<a href="'.hasAccessForUrl('decomission.php', false).'" class="blue">Ga terug naar de decomission pagina.</a>';
	die();

}

if(isset($_POST['label1']) == true){
	//print_r($_POST);

	$serials = '';
	$error = '';
	$lastlabel = '';

	echo '<table class="table">';
	echo '<thead><td>Serienummer</td><td>Model</td><td>Signpost Label</td><td>School Label</td><td>Order</td><td>SPSKU</td></thead>';
	for($x = 1; $x <= 10; $x++){
		if($_POST['label'.$x] != ''){
			if(isset($_POST['serial'.$x]) == true){
				$sql = "SELECT 
                            *,
                            ( SELECT status FROM orders WHERE id = q.orderid ) AS orderstatus,
                            ( SELECT SPSKU FROM orders WHERE id = q.orderid ) AS orderSku,
					        ( select amount-( select sum(amount) from delivery where orderid = orders.id ) from orders where orders.id = q.orderid and orders.deleted != 1) as amountleft
                        FROM labels q
                        WHERE (label like '" . $_POST['label'.$x] . "' OR signpost_label like '" . $_POST['label'.$x] . "') and serialnumber like '%" . $_POST['serial'.$x] . "%'
                        ";
			} else {
				$sql = "SELECT 
                            *,
                            ( SELECT status FROM orders WHERE id = q.orderid ) AS orderstatus,
                            ( SELECT SPSKU FROM orders WHERE id = q.orderid ) AS orderSku,
					        ( select amount-( select sum(amount) from delivery where orderid = orders.id ) from orders where orders.id = q.orderid and orders.deleted != 1) as amountleft
                        FROM labels q
					    WHERE label like '" . $_POST['label'.$x] . "' OR signpost_label like '" . $_POST['label'.$x] . "'
					    ";
			}
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					// kijken of dat serienummer 2 keer voorkomt = niet mogelijk om de decomissionen
					// kijken of dat toestel niet al geleverd is

					$errortext = '';
					$serials .= $row['serialnumber'] . '&&' . $row['label'] . '&&' . $row['orderid'] . ';';

					$tsql2 = "SELECT * FROM orsrg with (nolock)
						WHERE instruction like '" . $row['signpost_label'] . "'";
					$stmt2 = sqlsrv_query( $msconn, $tsql2);

					if($stmt2 === false) {
						die( print_r( sqlsrv_errors(), true) );
					}

					while( $row3 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC) ) {
						$errortext .= 'Label is al geleverd en kan niet gedecomissioned worden (gevonden in exact).<br>';
						$error .= 'Label is al geleverd en kan niet gedecomissioned worden (gevonden in exact).<br>';
					}

					$sql2 = "SELECT * FROM leermiddel.tblcontractdetails
						WHERE instruction like '" . $row['signpost_label'] . "'";
					$result2 = $conn->query($sql2);

					if ($result2->num_rows > 0) {
						while($row2 = $result2->fetch_assoc()) {
							$errortext .= 'Label is al geleverd en kan niet gedecomissioned worden (gevonden in leermiddel).<br>';
							$error .= 'Label is al geleverd en kan niet gedecomissioned worden (gevonden in leermiddel).<br>';
						}
					}

					if($row['amountleft'] == '0'){
						$errortext .= 'Label is al geleverd en kan niet gedecomissioned worden (alles is uitgeleverd voor dit order).<br>';
						$error .= 'Label is al geleverd en kan niet gedecomissioned worden (alles is uitgeleverd voor dit order).<br>';
					}

					if($lastlabel == $row['signpost_label']){
						$errortext .= 'Het label is 2 keer gebruikt en hierdoor kan het niet gedecomissioned worden.';
						$error .= 'Het label is 2 keer gebruikt en hierdoor kan het niet gedecomissioned worden.';
					}

					$lastlabel = $row['signpost_label'];

					echo '<tr><td>' . $row['serialnumber'] . '</td>';
					echo '<td>' . $row['model'] . '</td>';
					echo '<td>' . $row['signpost_label'];
					if($errortext != ''){
						echo '<br><span class="smalltext" style="color:red;">' . $errortext . '</span>';
					}
					echo '</td>';
					echo '<td>' . $row['label'] . '</td>';
					echo '<td><a href="'. hasAccessForUrl('delivery.php?orderid=' . $row['orderid'], false) .'">SP-BYOD20-' . $row['orderid'] . ' (' . $row['orderstatus'] . ')</td>';
					echo '<td>'.$row['orderSku'].'</td></tr>';

				}
				sqlsrv_free_stmt($stmt2);
				sqlsrv_close($msconn);
			} else {
				echo '<tr><td>x</td>';
				echo '<td>x</td>';
				echo '<td>Niets gevonden voor label ' . $_POST['label'.$x] . '</td>';
				echo '<td>x</td>';
				echo '<td>x</td>';
				echo '<td>x</td></tr>';
			}
		}
	}
	$conn->close();

	echo '</table>';

	if($error == ''){
		echo '<br>Ben je zeker dat deze <strong>allemaal</strong> gedecomissioned moeten worden?<br><br>';
		echo '<form action="decomission.php" method="post">
			<input type="text" id="serials" name="serials" value="' . $serials . '" hidden>
			<input type="submit" value="Ja" class="btn btn-danger" style="width:150px;">
			<a href="'.hasAccessForUrl('index.php', false).'" class="btn btn-success" style="width:150px;">Nee</a>
		</form>';
	} else {
		echo '<p style="color: red;">Er zijn errors dus je kan niet decomissionen.</p>';
		if(hasrole($role, ['admin'])){
			echo '<form action="decomission.php" method="post">
				<input type="text" id="serials" name="serials" value="' . $serials . '" hidden>
				<input type="submit" value="Ja" class="btn btn-danger" style="width:150px;">
				<a href="'.hasAccessForUrl('index.php', false).'" class="btn btn-success" style="width:150px;">Nee</a>
			</form>';
		}
	}

	die();
}

echo '<h1>Decomission laptop</h1>
	<p>Laptop zal verwijderd worden uit de labels tabel en het order verminderd met 1.<br>
	' . (isset($_GET['withserials']) == true ? '<a href="'.hasAccessForUrl('decomission.php', false).'">Klik hier om terug te gaan naar de gewone decomission pagina.</a></p>' : 'Melding over dubbel serienummer? <a href="'.hasAccessForUrl('decomission.php?withserials=true', false).'">Klik hier om ook het serienummer mee te geven.</a></p>' ) . '
<form action="decomission.php" method="post">

	<ol>
		<li>
			<div style="display: flex;">
				<input type="text" id="label1" name="label1" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial1" name="serial1" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label2" name="label2" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial2" name="serial2" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label3" name="label3" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial3" name="serial3" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label4" name="label4" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial4" name="serial4" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label5" name="label5" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial5" name="serial5" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label6" name="label6" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial6" name="serial6" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label7" name="label7" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial7" name="serial7" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label8" name="label8" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial8" name="serial8" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label9" name="label9" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial9" name="serial9" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>

		<li>
			<div style="display: flex;">
				<input type="text" id="label10" name="label10" placeholder="label" class="form-control">
				' . (isset($_GET['withserials']) == true ? '<input type="text" id="serial10" name="serial10" placeholder="serienummer" class="form-control">' : '') . '
			</div><br>
		</li>
	</ol>

	<input type="submit" value="Submit" class="btn btn-primary">
</form>';
?>

</div>

<script>
$(document).ready( function () {

	$(document).on("input", "input[name^=label]", function(e) {
		var text = $(this).val().split(' ');
		for (i=1 ; i<=10; i++) {
			if (text.length >= 2) {
				var split = text[i-1].split(',');
				$("input[name^=label]").eq(i-1).val(split[0]);
				$("input[name^=serial]").eq(i-1).val(split[1]);
			} else if (text.length > 1) {
				$(this).val(text[0]);
			}
		}
	});

});
</script>
<?php
include('footer.php');
?>
