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

$title = 'PVT - Open Leermiddel SKU total';

include('head.php');
include('nav.php');
include('LMconn.php');

echo "<H3><center>".$title."<br><h6>";

	$sql = "SELECT SKU,COUNT(*) AS OpenOrders FROM `tblcontractdetails` INNER JOIN `tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id` WHERE deleted=0 AND lengte ='0' AND `tblcontractdetails`.`StartDatum` >'2020-09-01' AND SKU IS NOT NULL AND SKU <>'' GROUP BY sku ORDER BY OpenOrders DESC";
	$result = $conn->query($sql);
	echo createTable($result, 'mysql');
	
?>


<?php
include('footer2.php');
?>
