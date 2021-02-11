<?php

$title = 'Admin Panel';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

?>

<div class="container body">

<?php

if (isset($_GET['sync']) !== false) {

	if($_GET['sync'] == 'labels'){
		echo 'sync is bezig<br>';

		// SYNC EXACT ORDERS TO LABELS
		$tsql = "SELECT concat('UPDATE labels SET used_for = ''', refer, ''' WHERE label = ''', instruction, ''';') AS query
			FROM orkrg WITH (NOLOCK)
			INNER JOIN orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
			WHERE instruction != '' AND instruction != 'Teruggave'";
		$stmt = sqlsrv_query( $msconn, $tsql);

		if($stmt === false) {
			die( print_r( sqlsrv_errors(), true) );
		}

		$i = 0;
		while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
			$sql = $row['query'];
			//echo $sql;
			if ($conn->query($sql) === TRUE) {
				$i++;
			} else {
				echo "Error updating label: " . $conn->error;
			}
		}
		echo $i . ' labels successfully updated from exact.';

		// SYNC LEERMIDDEL ORDERS TO LABELS
		$x = 0;
		$sql = "SELECT concat('UPDATE labels SET used_for = concat(ifnull(used_for, ''''), ''__'', ''', contractvolgnummer, ''') WHERE label = ''', instruction, ''';') AS `query`
				FROM leermiddel.tblcontractdetails
				WHERE instruction != '' AND deleted != '1'";
		$result2 = $conn->query($sql);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {
				$sql3 = $row2['query'];
				//echo $sql3;
				if ($conn->query($sql3) === TRUE) {
					$x++;
				} else {
					echo "" . $conn->error . '<br>';
				}
			}
		} else {
			//echo "Er bestaan nog geen images voor deze school<br>";
		}
		echo $x . ' labels successfully updated from leermiddel.';

		$y = 0;
		$sql4 = "SELECT *,
				ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = a.orderid AND delivery_number < a.delivery_number), 0) as amountbefore,
				ifnull(( SELECT SUM(amount) FROM delivery WHERE orderid = a.orderid AND delivery_number > a.delivery_number), 0) as amountafter
				FROM delivery a
				WHERE type = 'S' OR type = 'R'";
		$result4 = $conn->query($sql4);
		if ($result4->num_rows > 0) {
			while($row4 = $result4->fetch_assoc()) {

				$array = array();
				$sql5 = "SELECT label FROM labels WHERE orderid = '" . $row4['orderid'] . "'";
				$result5 = $conn->query($sql5);
				$school = 0;
				$i = 0;

				if ($result5->num_rows > 0) {
					while($row5 = $result5->fetch_assoc()) {
						array_push($array, $row5["label"]);
					}
				} else {
					echo "0 results";
				}

				$array = array_slice($array, $row4["amountbefore"], $row4["amount"]);

				foreach($array as $key){
					$sql3 = "UPDATE labels SET used_for = concat(ifnull(used_for, ''), '__', '" . $row4['orderid'] . '-' . $row4['type']  . $row4['delivery_number'] . "') WHERE label = '" . $key . "';";
					//echo $sql3 . '<br>';
					if ($conn->query($sql3) === TRUE) {
						$y++;
					} else {
						echo "" . $conn->error . '<br>';
					}
				}

			}
		} else {
			//echo "Er bestaan nog geen images voor deze school<br>";
		}
		echo $y . ' labels successfully updated from other delivery types.';

	}

} elseif (isset($_POST['delete']) !== false) {

	$order = mysqli_real_escape_string($conn, $_POST['order']);
	$suborder = mysqli_real_escape_string($conn, $_POST['suborder']);
	$delete = mysqli_real_escape_string($conn, $_POST['delete']);

	//print_r($_POST);
	$sql = "DELETE FROM delivery
		WHERE orderid = '" . $order . "' AND delivery_number = '" . $suborder . "'";
	if ($conn->query($sql) === TRUE) {
		echo '<div class="body">';
		echo $order . '.' . $suborder . ' is verwijderd.<br>';
		echo '</div>';
	} else {
		echo "Error updating record: " . $conn->error;
	}

	$sql = "UPDATE leermiddel.`tblcontractdetails`
		SET lengte = '0',
		breedte = '0',
		instruction = ''
		WHERE lengte = '" . $order . "." . $suborder . "'";
	if ($conn->query($sql) === TRUE) {
		#$last_id = $conn->insert_id;
		#$last_iecho $last_id . " updated successfully<br>";
	} else {
		echo "Error updating record: " . $conn->error;
	}

	$exactorders = '';
	$tsql = "SELECT concat(id, ',') AS orsrgid FROM orsrg WITH (NOLOCK)
		WHERE lengte = '" . $order . "." . $suborder . "'";
	$stmt = sqlsrv_query( $msconn, $tsql);

	if($stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
		$exactorders .= $row['orsrgid'];
	}

	$tsql= "UPDATE
		orsrg
		SET
		lengte = '0',
		breedte = '0',
		instruction = ''
		WHERE id in (" . substr($exactorders, 0, -1) . ")";

	$updateResults= sqlsrv_query($msconn, $tsql);

	if ($updateResults == FALSE){
		die( print_r( sqlsrv_errors(), true));
	}

	echo '<a href="'.hasAccessForUrl('admin-panel.php', false).'"><button class="btn btn-dark">Terug naar admin panel</button></a><br>';

} else {

	echo '<h1>Delete suborder</h1>
		<form action="admin-panel.php" method="post">
			<label for="order">Order:</label><br>
			<input type="number" id="order" name="order" value="" class="form-control"><br>
			<label for="suborder">Suborder:</label><br>
			<input type="number" id="suborder" name="suborder" value="" class="form-control"><br>
			<input type="checkbox" id="delete" name="delete" value="yes">
			<label for="delete" class="red">Delete</label><br>
			<input type="submit" value="Submit" class="btn btn-primary">
		</form>';

	echo '<h1>Sync: label <-> leermiddel/exact orders</h1>';
	echo '<a href="'.hasAccessForUrl('admin-panel.php?sync=labels', false).'" class="btn btn-outline-primary">Klik hier</a>';

}

?>

</div>

<?php
include('footer.php');
?>
