<?php

$title = 'PVT - Devices';

include('head.php');
include('nav.php');
include('readonly-conn.php');

?>

<div class="body">


<?php


	$sql = "SELECT SPSKU, productnumber, manufacturer, model,  memory_value as memory,ssd_value as disksize, panel_value as display, warranty, sleve_size as slevesize from devices";
	$result = $conn->query($sql);

	echo createTable($result, 'mysql');

?>

</div>

<?php
include('footer.php');
?>
