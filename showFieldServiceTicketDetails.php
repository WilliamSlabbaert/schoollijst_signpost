<?php
$title = 'Field Service - Case details';
include('head.php');
include('nav.php');
include('conn.php');
include('fieldServices/showFieldServiceTicketDetails.php');

if(isset($_POST['submit'])){
	echo file_get_contents('fieldServices/createFieldServiceTicket.php://input');
} elseif(isset($_GET['id'])){
	$id = mysqli_real_escape_string($conn, $_GET['id']);
	echo showFieldServiceTicketDetails($conn, $id);
} else {
	echo "Geen case gevonden";
}
