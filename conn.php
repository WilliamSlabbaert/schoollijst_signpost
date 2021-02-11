<?php

$servername = "newserver.signpost.be";
$username = "readonlyuser";
$password = "ColaReadOnly!";
$dbname = "byod-orders";
/*
$servername = "newserver.signpost.be";
$username = "readonlyuser";
$password = "ColaReadOnly!";
$dbname = "byod-orders";
*/

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>
