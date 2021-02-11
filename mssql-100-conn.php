<?php
$serverName = "SPB-SRV-EXACT";
$connectionOptions = array(
    "Database" => "100",
    "Uid" => "jordy",
    "PWD" => "A8z4c95gz62t",
	"CharacterSet"=>"UTF-8"
);
//Establishes the connection

$msconn = sqlsrv_connect($serverName, $connectionOptions);

?>
