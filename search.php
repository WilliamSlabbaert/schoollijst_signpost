<?php

$title = 'Search ' . $_POST['searchtype'];
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<?php

	$type = mysqli_real_escape_string($conn, $_POST['searchtype']);
	$search = mysqli_real_escape_string($conn, $_POST['search']);

	if($search == ''){

			echo 'Niets ingegeven';

	} else if($type == '' || $type == 'order'){

		if(preg_match("/[0-9][0-9][0-9][-][0-9]/i", $search) == 1 || preg_match("/[0-9][0-9][0-9][.][0-9]/i", $search) == 1){
			$search = str_replace('.', '-', $search);
			$search = explode('-', $search);
			$URL = "delivery.php?delivery_number=".$search[1]."&orderid=" . $search[0];
			if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
			else { header("Location: $URL"); }
			exit;
		} else if(is_numeric($search) == true && $search <= 2000){
			$URL = "order.php?id=" . $search;
			if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
			else { header("Location: $URL"); }
			exit;
		} else {
			echo '<h3>Orders</h3>';
			$sql = "SELECT CONCAT('SP-BYOD20-', id) as OrderID, SynergyID, IFNULL((SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1), '') AS School, amount as aantal, SPSKU, Warehouse, Status, CONCAT('<a href=\"order.php?id=', id, '\">Bekijk order</a>') as Link
				FROM orders
				WHERE synergyid LIKE '%" . $search. "%'
				OR (SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1) LIKE '%" . $search. "%'
				OR spsku LIKE '%" . $search . "%'";
			$result = $conn->query($sql);
			echo createTable($result, 'mysql');
		}

	} else if($type == 'serial'){

		echo '<h3>Labels</h3>';
		$sql = "SELECT OrderID, SynergyID, signpost_label AS 'Signpost Label', Label AS 'School Label', Serialnumber, Model
			FROM labels
			WHERE label LIKE '%" . $search. "%'
			OR signpost_label LIKE '%" . $search . "%'
			OR serialnumber LIKE '%" . $search . "%'
			OR model LIKE '%" . $search . "%'
			OR synergyid LIKE '%" . $search . "%'";
		$result = $conn->query($sql);
		echo createTable($result, 'mysql');

	} else if($type == 'mac'){

		echo '<form class="Search" action="LabelSerialMAC.php" method="post">
			<center><B>SynergyID</B> for the labels/MAC addresses :
			<input type="SynergyID" placeholder="SynergyID..." value="' . $search . '" name="SynergyID">
			<button type="submit"><i class="fa fa-search"></i></button>
			</center>
			</form>';
		$sql = "SELECT labels.SynergyID, schools.school_name as School, labels.Signpost_label,labels.label as Lavel,
				labels.serialnumber as Serial,devicedata.MacWifi, devicedata.MacEthernet
				FROM labels
				LEFT JOIN devicedata ON TRIM(LEADING 'S' FROM labels.serialnumber) = devicedata.SerieNummer
				LEFT JOIN Schools ON schools.SynergyID=labels.synergyID
				WHERE labels.SynergyID = '" . $search . "'";
		$result = $conn->query($sql);
		echo createTable($result, 'mysql');

	} else if($type == 'forecast'){

		echo '<h3>Forecasts</h3>';
		$sql = "SELECT SynergyID, salesorderid, school, CONCAT(device1, ' - ', `device1-SPSKU`, '<br>', device2, ' - ', `device2-SPSKU`, '<br>', device3, ' - ', `device3-SPSKU`, '<br>', device4, ' - ', `device4-SPSKU`) AS devices, CONCAT('<a href=\"forecasts.php?forecastid=', id, '\">Bekijk forecast</a>') as Link
			FROM forecasts
			WHERE synergyid LIKE '%" . $search. "%'
			OR salesorderid LIKE '%" . $search . "%'
			OR school LIKE '%" . $search . "%'";
		$result = $conn->query($sql);
		echo createTable($result, 'mysql');

	} else if($type == 'suborder'){

		if(preg_match("/[0-9][0-9][0-9][-][0-9]/i", $search) == 1 || preg_match("/[0-9][0-9][0-9][.][0-9]/i", $search) == 1){
			$search = str_replace('.', '-', $search);
			$search = explode('-', $search);
			$URL = "delivery.php?delivery_number=".$search[1]."&orderid=" . $search[0];
			if( headers_sent() ) { echo("<script>setTimeout(function(){location.href='$URL';},500);</script>"); }
			else { header("Location: $URL"); }
			exit;
		} else {
			echo '<h3>Suborders</h3>';
			$sql = "SELECT CONCAT('SP-BYOD20-', orders.id, '-', delivery.type, delivery.delivery_number) AS Suborder, SynergyID, (SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1) AS School, delivery.amount AS Aantal, orders.SPSKU AS SPSKU, CONCAT('<a href=\"delivery.php?delivery_number=', delivery.delivery_number, '&orderid=', orders.id, '\">Bekijk suborder</a>') as Link
				FROM orders
				INNER JOIN delivery ON delivery.orderid = orders.id
				WHERE orders.id LIKE '%" . $search. "%'
				OR orders.synergyid LIKE '%" . $search . "%'
				OR orders.SPSKU LIKE '%" . $search . "%'
				OR (SELECT school_name FROM schools WHERE synergyid = orders.synergyid LIMIT 1) LIKE '%" . $search. "%'
				OR CONCAT(orders.id, '-', delivery.type, delivery.delivery_number) LIKE '%" . $search . "%'";
			$result = $conn->query($sql);
			echo createTable($result, 'mysql');
		}

	} else if ($type == 'magento1'){
		echo '<form id="myForm" action="magento1.php" method="post">';
		foreach ($_POST as $a => $b) {
			echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
		}
		echo '</form>
		<script type="text/javascript">
			document.getElementById(\'myForm\').submit();
		</script>';
	} else if ($type == 'exact-invoice'){

		echo '<form id="myForm" action="exact-invoices.php" method="post">';
		foreach ($_POST as $a => $b) {
			echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
		}
		echo '</form>
		<script type="text/javascript">
			document.getElementById(\'myForm\').submit();
		</script>';

	}
	?>

</div>

<?php
include('footer.php');
?>
