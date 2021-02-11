<?php
// Include the database config file
//manufacturer - model - motherboard - memory - ssd - panel
include_once 'conn.php';

if(!empty($_POST["spsku"])){
	// Fetch state data based on the specific country
	$spsku = explode(';', $_POST['spsku']);
	$query = 'SELECT sleves.sku, sleves.name FROM sleves INNER JOIN devices ON devices.sleve_size = sleves.size WHERE devices.spsku = "' . $spsku[0] . '"';
	$result = $conn->query($query);

	// Generate HTML of state options list
	if($result->num_rows > 0){

		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['sku'].'">'.$row['name'].'</option>';
		}

	} else {
		echo '<option value="geen">Geen gevonden</option>';
	}
}

?>
