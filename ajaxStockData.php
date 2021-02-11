<?php
include('conn.php');

if (isset($_POST['warehouse']) !== false) {

	if ($_POST['operation'] == 'add') {

		$warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
		$location = mysqli_real_escape_string($conn, $_POST['location']);
		$sku = mysqli_real_escape_string($conn, $_POST['sku']);
		$serial = mysqli_real_escape_string($conn, $_POST['serial']);
		$username = mysqli_real_escape_string($conn, $_POST['loginname']);
		$label = '';

		if (strpos($serial, '/') !== false) {

			$serial = explode('/', $serial);
			$label = $serial[0];
			$serial = $serial[1];

		} elseif (preg_match('/[-][0-9][0-9][0-9][0-9]$/i', $serial) || preg_match('/[-][0-9][0-9][0-9]$/i', $serial)){

			$label = $serial;
			$sql = "SELECT * FROM labels WHERE signpost_label LIKE '%" . $label . "%'";
			$i = 0;
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$i++;
					if($i >= 2){
						echo '<span style="color:red;">FOUT: Serienummer is 2 keer teruggevonden</span><br>';
						die();
					} else {
						$serial = $row['serialnumber'];
					}
				}
			} else {
				echo '<span style="color:red;">FOUT: Serienummer is niet teruggevonden</span><br>';
				die();
			}

		}

		if($serial != ''){

			$sql = "SELECT warehouse, location, sku, label FROM stock WHERE serial = '" . $serial . "'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {

					if($label == ''){
						if($row['label'] !== $label || $row['warehouse'] !== $warehouse || $row['location'] !== $location || $row['sku'] !== $sku){
							$sql = "UPDATE stock SET sku='" . $sku . "', warehouse='" . $warehouse . "', location='" . $location . "', updatedby='" . $username . "' WHERE serial = '" . $serial . "'";
							if ($conn->query($sql) === TRUE) {
								if($row['warehouse'] !== $warehouse){
									echo '<span style="color:orange;">' . $serial . ' > verplaatst van ' . $row['warehouse'] . ' naar ' . $warehouse . '</span><br>';
								}
								if($row['location'] !== $location){
									echo '<span style="color:orange;">' . $serial . ' > verplaatst van ' . $row['location'] . ' naar ' . $location . '</span><br>';
								}
								if($row['sku'] !== $sku){
									echo '<span style="color:orange;">' . $serial . ' > sku veranderd van ' . $row['sku'] . ' naar ' . $sku . '</span><br>';
								}
							} else {
								echo "Error updating record: " . $conn->error;
							}
						} else {
							echo '<span>' . $serial . ' heeft geen veranderingen</span><br>';
						}
					} else {
						if($row['location'] !== $location || $row['warehouse'] !== $warehouse || $row['label'] !== $label || $row['sku'] !== $sku){
							$sql = "UPDATE stock SET sku='" . $sku . "', location='" . $location . "', warehouse='" . $warehouse . "', label='" . $label . "', updatedby='" . $username . "' WHERE serial = '" . $serial . "'";
							if ($conn->query($sql) === TRUE) {
								if($row['warehouse'] !== $warehouse){
									echo '<span style="color:orange;">' . $serial . ' > verplaatst van ' . $row['warehouse'] . ' naar ' . $warehouse . '</span><br>';
								}
								if($row['location'] !== $location){
									echo '<span style="color:orange;">' . $label . ' - ' . $serial . ' > verplaatst van ' . $row['location'] . ' naar ' . $location . '</span><br>';
								}
								if($row['label'] !== $label){
									echo '<span style="color:orange;">' . $label . ' - ' . $serial . ' > label veranderd van "' . $row['label'] . '" naar ' . $label . '</span><br>';
								}
								if($row['sku'] !== $sku){
									echo '<span style="color:orange;">' . $label . ' - ' . $serial . ' > sku veranderd van ' . $row['sku'] . ' naar ' . $sku . '</span><br>';
								}
							} else {
								echo "Error updating record: " . $conn->error;
							}
						} else {
							echo '<span>' . $serial . ' met label ' . $label . ' heeft geen veranderingen</span><br>';
						}
					}

				}
			} else {

				$sql = "INSERT INTO stock (warehouse, location, sku, serial, label, addedby)
					VALUES ('" . $warehouse . "', '" . $location . "', '" . $sku . "', '" . $serial . "', '" . $label . "', '" . $username . "')";

				if ($conn->query($sql) === TRUE) {
					if($label != ''){
						echo '<span style="color:green;">' . $serial . ' met label ' . $label . ' correct toegevoegd</span><br>';
					} else {
						echo '<span style="color:green;">' . $serial . ' correct toegevoegd</span><br>';
					}

					// Zet de toestellen uit de stock_out als returned als het ingescant wordt in de stock
					mysqli_query($conn, "UPDATE stock_out SET returned = NOW() WHERE serial = '" . $serial . "' AND returned IS NULL");
					if(mysqli_affected_rows($conn) >= 1){
						echo '<span style="color:blue;">' . $serial . ' is teruggekomen.</span><br>';
					}

				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
					die();
				}

			}
		} else {
			echo '<span style="color:red;">FOUT: Serienummer is leeg</span><br>';
		}

		$conn->close();
		//echo '<div class="body">';
		//echo "Stock is toegevoegd.<br><br>";
		//echo '<a href="stock.php"><button class="btn btn-dark">Terug naar overzicht</button></a>';
		//echo '</div>';
		//echo "<script type='text/javascript'>window.top.location='stock.php';</script>"; exit;
		die();

	} elseif ($_POST['operation'] == 'remove') {

		$serial = mysqli_real_escape_string($conn, $_POST['serial']);
		$label = '';
		$name = mysqli_real_escape_string($conn, $_POST['loginname']);
		$warehouse = mysqli_real_escape_string($conn, $_POST['warehouse']);
		$reason = '';

		if (strpos($serial, '/') !== false) {
			$serial = explode('/', $serial);
			$label = $serial[0];
			$serial = $serial[1];
		}

		if (strpos($warehouse, '/') !== false) {
			$warehouse = explode('/', $warehouse);
			$reason = $warehouse[1];
			$warehouse = $warehouse[0];
		}

		if($label != ''){
			$sql = "INSERT INTO stock_out (from_warehouse, from_location, sku, serial, label, to_customer, to_reason, pickedby, pickedon)
				SELECT warehouse, location, sku, serial, label, '" . $warehouse . "', '" . $reason . "', '" . $name . "', NOW()
				FROM stock
				WHERE serial = '" . $serial . "' AND label = '" . $label . "'";
		} else {
			$sql = "INSERT INTO stock_out (from_warehouse, from_location, sku, serial, label, to_customer, to_reason, pickedby, pickedon)
				SELECT warehouse, location, sku, serial, label, '" . $warehouse . "', '" . $reason . "', '" . $name . "', NOW()
				FROM stock
				WHERE serial = '" . $serial . "'";
		}

		if ($conn->query($sql) === TRUE) {

			if($label != ''){
				$sql = "DELETE FROM stock
					WHERE serial = '" . $serial . "' AND label = '" . $label . "'";
			} else {
				$sql = "DELETE FROM stock
					WHERE serial = '" . $serial . "'";
			}

			if ($conn->query($sql) === TRUE) {
				$amount = $conn->affected_rows;
				if ($amount >= 2){
					echo $serial . ' is ' . $amount . ' keer uit de stock gehaald<br>';
				} elseif ($amount == 1){
					echo $serial . ' is uit de stock gehaald<br>';
				} else {
					echo 'FOUT: ' . $serial . ' was niet gevonden<br>';
				}
			} else {
				echo "Error deleting record: " . $conn->error;
				die();
			}
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
			die();
		}

		$conn->close();

	} elseif ($_POST['operation'] == 'delete') {

		$serial = mysqli_real_escape_string($conn, $_POST['serial']);
		$label = '';
		$name = mysqli_real_escape_string($conn, $_POST['loginname']);

		if (strpos($serial, '/') !== false) {
			$serial = explode('/', $serial);
			$label = $serial[0];
			$serial = $serial[1];
		}

		if($label != ''){
			$sql = "DELETE FROM stock
				WHERE serial = '" . $serial . "' AND label = '" . $label . "' AND addedon >= DATE_ADD(CURDATE(), INTERVAL -30 MINUTE)";
		} else {
			$sql = "DELETE FROM stock
				WHERE serial = '" . $serial . "' AND addedon >= DATE_ADD(CURDATE(), INTERVAL -30 MINUTE)";
		}

		if ($conn->query($sql) === TRUE) {
			$amount = $conn->affected_rows;
			if ($amount >= 2){
				echo '<span style="color:red;">' . $serial . ' is ' . $amount . ' keer verwijderd</span><br>';
			} elseif ($amount == 1){
				echo '<span style="color:red;">' . $serial . ' is verwijderd</span><br>';
			} else {
				echo $serial . ' niet gevonden in de laatste 30min en dus niet verwijderd<br>';
			}
		} else {
			echo "Error deleting record: " . $conn->error;
			die();
		}
	} else {
		echo "Error";
		die();
	}

	$conn->close();

} else {

	echo 'Magazijn is niet opgegeven';

}

?>
