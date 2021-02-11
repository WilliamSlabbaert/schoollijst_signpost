<?php

$title = 'Stock Onderdelen';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">
<?php
if (isset($_GET['SPSKU']) !== false) {

	if ($_GET['SPSKU'] == "0") {

?>

	<a href="<?php hasAccessForUrl('stock-parts.php'); ?>"><button class="btn btn-dark">Terug naar overzicht</button></a>
	<br><br><br>
	<h2>Nieuw Onderdeel</h2><br>
	<form action="stock-parts.php" method="post">
		<div class="form-row">
			<div class="col col-5">
				<input type="text" class="form-control" placeholder="SPSKU" name="SPSKU" required>
				<br>
				<input type="text" class="form-control" placeholder="SKU" name="SKU" required>
				<br>
				<input type="text" class="form-control" placeholder="Beschrijving" name="Beschrijving" required>
				<br>
				<input type="number" class="form-control" placeholder="Stock" name="Stock" required>
				<br>
				Type<br>
				<select name="type" id="type" class="form-control">
					<option value="RAM">RAM</option>
					<option value="SSD">SSD</option>
					<option value="SCHERM">Scherm</option>
					<option value="LCD Kabel">LCD Kabel</option>
					<option value="Pen">Pen</option>
					<option value="Plastic Part">Plastic Part</option>
					<option value="USB">USB Stick</option>
					<option value="Hoes">Hoes</option>
				</select>
				<br>
				<input type="text" class="form-control" placeholder="locatie" name="location" required>
				<br>
				<input type="text" class="form-control" placeholder="opmerking" name="notes" required>
				<br>
				<input type="text" class="form-control" placeholder="campagne" name="campagne" required>
				<br>
				<input type="text" class="form-control" placeholder="oorsprong" name="origin" required>
			</div>
		</div>
		<br>
		<div class="">
			<button type="submit" class="btn btn-primary">Toevoegen</button>
		</div>
	</form>


<?php

} else {
?>

<?php

$sql = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) AS stock,
		IFNULL((SELECT SUM(amount) AS panelswaporder FROM orders WHERE panelswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS panelswaporder,
		IFNULL((SELECT SUM(amount) AS memoryswaporder FROM orders WHERE memoryswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS memoryswaporder,
		IFNULL((SELECT SUM(amount) AS memoryswaporder2 FROM orders WHERE memoryswap2 = q.SPSKU AND STATUS != 'nieuw'), 0) AS memoryswaporder2,
		IFNULL((SELECT SUM(amount) AS ssdswaporder FROM orders WHERE ssdswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS ssdswaporder,
		IFNULL((SELECT SUM(amount) AS keyboardswaporder FROM orders WHERE keyboardswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS keyboardswaporder,
		type, location, notes, campagne, origin FROM `byod-orders`.`device-parts` q where SPSKU ='" . $_GET['SPSKU'] . "' GROUP BY SPSKU ORDER BY id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		$stock = 0;
		$stock = $row['stock'] - $row['panelswaporder'] - $row['ssdswaporder'] - $row['memoryswaporder'] - $row['memoryswaporder2'] - $row['keyboardswaporder'];
		echo '<a href="'. hasAccessForUrl('stock-parts.php', false).'" class="btn btn-info">Terug naar overzicht</a><br><br>';
		echo '<h3>' . $row['SPSKU'] . '</h3>';

		echo "<table class=\"table table-sm table-striped\">";

		echo '<tr><td>SPSKU</td><td>' . $row['SPSKU'] . '</td></tr>';
		echo '<tr><td>SKU</td><td>' . $row['SKU'] . '</td></tr>';
		echo '<tr><td>Beschrijving</td><td>' . $row['desc'] . '</td></tr>';
		echo '<tr><td>stock</td><td>' . $stock . '</td></tr>';
		echo '<tr><td>type</td><td>' . $row['type'] . '</td></tr>';
		echo '<tr><td>locatie</td><td>' . $row['location'] . '</td></tr>';
		echo '<tr><td>notes</td><td>' . $row['notes'] . '</td></tr>';
		echo '<tr><td>campagne</td><td>' . $row['campagne'] . '</td></tr>';
		echo '<tr><td>oorsprong</td><td>' . $row['origin'] . '</td></tr>';
		echo '</table>';

		echo '<br><br>
		<h3>Stock toevoegen</h3>
		<form action="stock-parts.php" method="post">
			SPSKU<input type="text" class="form-control" id="SPSKU" name="SPSKU" value="' . $row['SPSKU'] . '" readonly required><br>
			Naam<input type="text" class="form-control" id="user" name="user" value="' . $loginname . '" readonly required><br>
			Type<input type="text" class="form-control" id="type" name="type" value="' . $row['type'] . '" readonly required><br>
			Aantal<input type="number" class="form-control" id="stock" name="stock" value="" required><br>
			<input type="submit" class="btn btn-info" value="Submit"><br><br>
		</form>
		';

		echo "<h3>Historiek</h3>";

		echo "<table class=\"table table-sm table-striped\">";

		$sql = "SELECT SPSKU, user, stock, timestamp FROM `device-parts` WHERE spsku = '" . $row['SPSKU'] . "'
				UNION ALL
				SELECT panelswap AS SPSKU, CONCAT('SP-BYOD-',id) as user, CONCAT('-',amount) AS stock, requested_on AS timestamp FROM orders WHERE panelswap = '" . $row['SPSKU'] . "' and deleted != 1
				UNION ALL
				SELECT ssdswap AS SPSKU, CONCAT('SP-BYOD-',id) as user, CONCAT('-',amount) AS stock, requested_on AS timestamp FROM orders WHERE ssdswap = '" . $row['SPSKU'] . "' and deleted != 1
				UNION ALL
				SELECT memoryswap AS SPSKU, CONCAT('SP-BYOD-',id) as user, CONCAT('-',amount) AS stock, requested_on AS timestamp FROM orders WHERE memoryswap = '" . $row['SPSKU'] . "' and deleted != 1
				UNION ALL
				SELECT memoryswap2 AS SPSKU, CONCAT('SP-BYOD-',id) as user, CONCAT('-',amount) AS stock, requested_on AS timestamp FROM orders WHERE memoryswap2 = '" . $row['SPSKU'] . "' and deleted != 1
				UNION ALL
				SELECT keyboardswap AS SPSKU, CONCAT('SP-BYOD-',id) as user, CONCAT('-',amount) AS stock, requested_on AS timestamp FROM orders WHERE keyboardswap = '" . $row['SPSKU'] . "' and deleted != 1
				ORDER BY timestamp ASC";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {

				echo '<tr><td>' . $row['stock'] . '</td><td>' . $row['user'] . '</td><td>' . $row['timestamp'] . '</td></tr>';

			}

		} else {

			echo "0 results";

		}

		echo '</table><br><br><br>';

	}

} else {

	echo "0 results";

}

$conn->close();

?>

<?php
}
?>

<?php
} elseif (isset($_POST["SPSKU"]) !== false) {

	if (isset($_POST["stock"]) !== false) {

		echo "post stock data";

		$SPSKU = mysqli_real_escape_string($conn, $_POST['SPSKU']);
		$user = mysqli_real_escape_string($conn, $_POST['user']);
		$Stock = mysqli_real_escape_string($conn, $_POST['stock']);
		$type = mysqli_real_escape_string($conn, $_POST['type']);

		$sql = "INSERT INTO `byod-orders`.`device-parts` (SPSKU, user, stock, type)
		VALUES ('" . $SPSKU . "', '" . $user . "', '" . $Stock . "', '" . $type . "')";

		if ($conn->query($sql) === TRUE) {
			echo '<div class="body">';
			echo $_POST['SPSKU'] . " stock is toegevoegd.<br><br>";
			echo '<a href="'. hasAccessForUrl('stock-parts.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			echo '</div>';
			echo "<script type='text/javascript'>window.top.location='stock-parts.php?SPSKU=" . $SPSKU . "';</script>"; exit;
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();



	} elseif (isset($_POST["id"]) !== false) {

		$sql = "UPDATE `byod-orders`.`device-parts` SET SPSKU='" . $_POST['SPSKU'] . "', SKU='" . $_POST['SKU'] . "', `desc`='" . $_POST['Beschrijving'] . "', stock='" . $_POST['Stock'] . "' WHERE id=" . $_POST['id'];

		if ($conn->query($sql) === TRUE) {
			echo '<div class="body">';
			echo $_POST['SPSKU'] . " is aangepast.<br><br>";
			echo '<a href="'. hasAccessForUrl('stock-parts.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			echo '</div>';
			echo "<script type='text/javascript'>window.top.location='stock-parts.php';</script>"; exit;
		} else {
			echo "Error updating record: " . $conn->error;
		}

		$conn->close();


	} else {

		$SPSKU = mysqli_real_escape_string($conn, $_POST['SPSKU']);
		$SKU = mysqli_real_escape_string($conn, $_POST['SKU']);
		$Beschrijving = mysqli_real_escape_string($conn, $_POST['Beschrijving']);
		$Stock = mysqli_real_escape_string($conn, $_POST['Stock']);
		$type = mysqli_real_escape_string($conn, $_POST['type']);
		$location = mysqli_real_escape_string($conn, $_POST['location']);
		$notes = mysqli_real_escape_string($conn, $_POST['notes']);
		$campagne = mysqli_real_escape_string($conn, $_POST['campagne']);
		$origin = mysqli_real_escape_string($conn, $_POST['origin']);

		$sql = "INSERT INTO `byod-orders`.`device-parts` (SPSKU, SKU, `desc`, stock, type, location, notes, campagne, origin)
		VALUES ('" . $SPSKU . "', '" . $SKU . "', '" . $Beschrijving . "', '" . $Stock . "', '" . $type . "', '" . $location . "', '" . $notes . "', '" . $campagne . "', '" . $origin . "')";

		if ($conn->query($sql) === TRUE) {
			echo '<div class="body">';
			echo $_POST['SPSKU'] . " is toegevoegd.<br><br>";
			echo '<a href="'. hasAccessForUrl('stock-parts.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			echo '</div>';
			echo "<script type='text/javascript'>window.top.location='stock-parts.php';</script>"; exit;
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();

	}

} else {
?>


<div class="body">

	<h3>Stock van onderdelen</h3>
	<br>
	<a href="<?php hasAccessForUrl('stock-parts.php?SPSKU=0'); ?>"><button class="btn btn-primary">Nieuw onderdeel</button></a>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">ID</th>
				<th scope="col">SPSKU</th>
				<th scope="col">SKU</th>
				<th scope="col">Beschrijving</th>
				<th scope="col">Stock</th>
				<th scope="col">Locatie</th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) AS stock,
					IFNULL((SELECT SUM(amount) AS panelswaporder FROM orders WHERE panelswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS panelswaporder,
					IFNULL((SELECT SUM(amount) AS memoryswaporder FROM orders WHERE memoryswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS memoryswaporder,
					IFNULL((SELECT SUM(amount) AS memoryswaporder2 FROM orders WHERE memoryswap2 = q.SPSKU AND STATUS != 'nieuw'), 0) AS memoryswaporder2,
					IFNULL((SELECT SUM(amount) AS ssdswaporder FROM orders WHERE ssdswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS ssdswaporder,
					IFNULL((SELECT SUM(amount) AS keyboardswaporder FROM orders WHERE keyboardswap = q.SPSKU AND STATUS != 'nieuw'), 0) AS keyboardswaporder,
					`type`, location, notes, campagne, origin
					FROM `device-parts` q GROUP BY spsku";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					$url = "document.location = 'stock-parts.php?SPSKU=" . $row['SPSKU'] . "'";
					$stock = 0;
					$stock = $row['stock'] - $row['panelswaporder'];
					$stock = $stock - $row['ssdswaporder'];
					$stock = $stock - $row['memoryswaporder'];
					$stock = $stock - $row['memoryswaporder2'];
					$stock = $stock - $row['keyboardswaporder'];

					# Main SPSKU types have hidden 1 when technicians don't want to show them in the list
					# Also negative stock won't be shown
					//if (strpos($row['hidden'], "1") === false && $stock >= -1) {

						if ($row['stock'] <= 0) {
							$color = 'btn-outline-danger';
						} else {
							$color = "";
						}

						echo '<tr onclick="' . $url . '" class="' . $color . '">';
						echo '<td>' . $row['id'] . '</td>';
						echo '<th>' . $row['SPSKU'] . '</th>';
						echo '<td>' . $row['SKU'] . '</td>';
						echo '<td>' . $row['desc'] . '</td>';
						echo '<td>' . $stock . '</td>';
						echo '<td>' . $row['location'] . '</td>';
						echo '</tr>';

					//}

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		?>

		</tbody>
	</table><br><br><br><br>
</div>

<?php } ?>

</div>

<?php
include('footer.php');
?>
