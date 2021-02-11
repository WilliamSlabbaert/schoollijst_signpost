<?php

$title = 'Image info nakijken';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<?php

	if (isset($_GET['guid'])!==false) {

		$sql = "SELECT * FROM images2020 WHERE guid = '" . $_GET['guid'] . "'";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$number = 1;
			while($row = $result->fetch_assoc()) {

				echo "<h3>Image " . $number . "</h3>";
				echo "<b>Image Naam:</b> " .  $row['name'] . "<br>";
				echo "<b>Soort:</b> " .  $row['type'] . "<br>";
				echo "<b>Aanmelding: </b>" .  $row['authentication'] . "<br>";
				echo "<b></b>" .  $row['emse3'] . "<br>";
				echo "<b></b>" .  $row['authentication_info'] . "<br>";
				echo "<b>Gratis Software: </b>" .  $row['free_software'] . "<br>";
				echo "<b>Betalende Software: </b>" .  $row['paid_software'] . ".<br>";
				echo "<b>Computernaam: </b>" .  $row['computername'] . "<br>";
				echo "<b>Extra informatie: </b>" .  $row['notes'] . "<br>";
				echo "<b>Toestellen: </b>" .  $row['SPSKU'] . "<br>";
				echo "<br><br>";

				$number++;
			}

		} else {

			echo "0 results";

		}
	} else {
		echo "nothing here :)";
	}

	?>

</div>
