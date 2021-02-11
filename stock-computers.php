<?php

$title = 'Stock Computers';
include('head.php');
include('nav.php');
include('conn.php');

?>

<?php
if (isset($_GET['id']) !== false) {

	if ($_GET['id'] == 0) {
		# nieuwe aanmaken
	?>

	<div class="body">
		<a href="<?php hasAccessForUrl('stock-computers.php'); ?>"><button class="btn btn-dark">Terug naar overzicht</button></a>
		<br><br><br>
		<h2>Nieuw Onderdeel</h2><br>
		<form action="stock-computers.php" method="post">
			<div class="form-row">
				<div class="col col-5">
					<input type="text" class="form-control" placeholder="SKU" name="SKU" required>
					<br>
					<input type="text" class="form-control" placeholder="Beschrijving" name="Beschrijving" required>
					<br>
					<input type="number" class="form-control" placeholder="Stock" name="Stock" required>
				</div>
			</div>
			<br>
			<div class="">
				<button type="submit" class="btn btn-primary">Toevoegen</button>
			</div>
		</form>
	</div>

	<?php
	} else {
		# bestaande aanpassen
		?>

		<?php

		$sql = "SELECT * FROM `byod-orders`.`device-parts` where id =" . $_GET['id'];
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {

			while($row = $result->fetch_assoc()) {

				echo '<div class="body">
				<a href="'. hasAccessForUrl('stock-computers.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>
				<br><br><br>
				<h2>Nieuw Onderdeel</h2><br>
				<form action="stock-computers.php" method="post">
				<div class="form-row">
				<div class="col col-5">
				<input type="hidden" name="id" value="' . $row['id'] . '">
				<input type="text" class="form-control" placeholder="SKU" name="SKU" value="' . $row['SKU'] . '">
				<br>
				<input type="text" class="form-control" placeholder="Beschrijving" name="Beschrijving" value="' . $row['desc'] . '">
				<br>
				<input type="number" class="form-control" placeholder="Stock" name="Stock" value="' . $row['stock'] . '">
				</div>
				</div>
				<br>
				<div class="">
				<button type="submit" class="btn btn-danger">Aanpassen</button>
				</div>
				</form>
				</div>';

			}

		} else {

			echo "0 results";

		}

		$conn->close();

	}

} elseif (isset($_POST["SKU"]) !== false) {

	if (isset($_POST["id"]) !== false) {
		# bestaande aanpassen in db

		$sql = "UPDATE `byod-orders`.`device-parts` SET SKU='" . $_POST['SKU'] . "', `desc`='" . $_POST['Beschrijving'] . "', stock='" . $_POST['Stock'] . "' WHERE id=" . $_POST['id'];

		if ($conn->query($sql) === TRUE) {
			echo '<div class="body">';
			echo $_POST['SKU'] . "is aangepast.<br><br>";
			echo '<a href="'. hasAccessForUrl('stock-computers.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			echo '</div>';
			echo "<script type='text/javascript'>window.top.location='stock-computers.php';</script>"; exit;
		} else {
			echo "Error updating record: " . $conn->error;
		}

		$conn->close();


	} else {

		#nieuwe aanmaken in db
		$sql = "INSERT INTO `byod-orders`.`device-parts` (SKU, `desc`, stock)
		VALUES ('" . $_POST['SKU'] . "', '" . $_POST['Beschrijving'] . "', '" . $_POST['Stock'] . "')";

		if ($conn->query($sql) === TRUE) {
			echo '<div class="body">';
			echo $_POST['SKU'] . "is toegevoegd.<br><br>";
			echo '<a href="'. hasAccessForUrl('stock-computers.php', false).'"><button class="btn btn-dark">Terug naar overzicht</button></a>';
			echo '</div>';
			echo "<script type='text/javascript'>window.top.location='stock-computers.php';</script>"; exit;
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();

	}

} else {
#view

?>


<div class="body">
	<h3>Stock van computers</h3>
	<br>
	<!-- <a href="stock-computers.php?id=0"><button class="btn btn-primary">Nieuwe Computer</button></a> -->

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">SPSKU</th>
				<th scope="col">Beschrijving</th>
				<th scope="col">Stock SP</th>
				<th scope="col">Stock CO</th>
				<th scope="col">Stock TD</th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = 'SELECT *,
			(SELECT SUM(amount) FROM orders WHERE SPSKU = q.SPSKU AND STATUS != "nieuw" AND warehouse = "Signpost" and deleted != 1) AS besteldSignpost,
			(SELECT SUM(amount) FROM orders WHERE SPSKU = q.SPSKU AND STATUS != "nieuw" AND warehouse = "Copaco" and deleted != 1) AS besteldCopaco,
			(SELECT SUM(amount) FROM orders WHERE SPSKU = q.SPSKU AND STATUS != "nieuw" AND warehouse = "TechData" and deleted != 1) AS besteldTechData
			FROM `byod-orders`.`devices` q';
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					//$url = "document.location = 'stock-computers.php?id=" . $row['id'] . "'";
					$url = "";
					$spstock = $row['sp-stock'] - $row['besteldSignpost'];
					$costock = $row['co-stock'] - $row['besteldCopaco'];
					$tdstock = $row['td-stock'] - $row['besteldTechData'];

					if (substr($row['SPSKU'],-1) !== 'H' && substr($row['SPSKU'],-1) !== 'F' ) {
					if ($spstock <= -1 || $costock <= -1 || $tdstock <= -1) {
						$color = 'btn-outline-danger';
					} else {
						$color = "";
					}
				} else {
					$color = 'btn-primary';
				}

					echo '<tr onclick="' . $url . '" class="' . $color . '">';
						echo '<th scope="row">' . $row['SPSKU'] . '</th>';
						echo '<td>' . $row['manufacturer'] . ' ' . $row['model'] . ' - ' . $row['motherboard_value'] . ' - ' . $row['memory_value'] . ' - ' . $row['ssd_value'] . ' - ' . $row['panel_value'] . '</td>';
						echo '<td>' . $spstock . '</td>';
						echo '<td>' . $costock . '</td>';
						echo '<td>' . $tdstock . '</td>';
					echo '</tr>';

				}

			} else {

				echo "0 results";

			}

			$conn->close();

		?>

		</tbody>
	</table>
</div>

<?php } ?>

<?php
include('footer.php');
?>
