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

$title = 'PVT - Deliveries';

include('head.php');
include('nav.php');
include('readonly-conn.php');

?>

<div class="body">

	<form class="Search" action="deliverybysynergy.php" method="post">
	<center> <B>SynergyID</B> for which you want to see all created deliveries :
	  <input type="SynergyID" placeholder="SynergyID..." name="SynergyID">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</center>
	</form>

<?php

if(!empty($_POST))
{
	$a =$_POST['SynergyID'];

	$sql = "SELECT CONCAT(orders.synergyID,' - ', schools.school_name) as School, concat('SP-',delivery.orderID,'-',delivery.type , delivery.delivery_number) as Delivery,  created_at as Created, SPSKU, concat('BYOD20-',orderid,'-', delivery.type, delivery.ID) as Reference, delivery.amount as Amount,Comments  FROM delivery INNER JOIN orders ON delivery.orderid=orders.id INNER JOIN schools ON orders.synergyid=schools.synergyID WHERE schools.SynergyID = '$a'";
	$result = $conn->query($sql);

	echo createTable($result, 'mysql');
}

?>

</div>

<?php
include('footer2.php');
?>
