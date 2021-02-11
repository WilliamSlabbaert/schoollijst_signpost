<?php

$title = 'PVT - label with MAC address';

include('head.php');
include('nav.php');
include('readonly-conn.php');

?>
<style>.dataTables_paginate a {
    color: #00ADBD;
	padding: 8px 15px;
    text-decoration: none;
    transition: background-color .7s;
	margin-top:0.5em;
}

.dataTables_paginate{margin-top:0.5em}

.dataTables_paginate a.current {
    color: white;
		font-size: 16;
		border-style: solid;
		border-width: 1 ;
		border-color: white;
		background-color: #00ADBD;
}

.dataTables_paginate a:hover {background-color: #00ADBD; color:white}

tr:hover{background-color: #00ADBD;}

.dataTables_length label {
    color: #00ADBD;
	padding: 0px 40px;
}
</style>

<?php

echo "<center>".$title."</center><br><br>";

{

	$sql = "SELECT *  FROM decomissioned";
	$result = $conn->query($sql);
	echo createTable($result, 'mysql');
}

?>

</div>

<?php
include('footer2.php');
?>
