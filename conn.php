<?php

$servername = "newserver.signpost.be";
$username = "byod_orders";
$password = "7nG2BimN08f8nYLOHmWtBHohI";
$dbname = "leermiddel";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>
