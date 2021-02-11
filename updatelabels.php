<?php

include('conn.php');
include('mssql-100-conn.php');

$orders = explode(';', substr($_POST['updateLabels'], 0, -1));
foreach($orders as $order){

	$details = explode('_', $order);
	if(isset($details[0]) == true && isset($details[1]) == true && isset($details[2]) == true){
		if($details[0] == 'W'){

			$tsql= "update orsrg set instruction = '" . $details[1] . "' where id = '" . $details[2] . "'";
			echo $tsql;

			$updateResults= sqlsrv_query($msconn, $tsql);

			if ($updateResults == FALSE){
				die( print_r( sqlsrv_errors(), true));
			}

		} elseif($details[0] == 'H'){

			$sql = "UPDATE leermiddel.tblcontractdetails SET instruction = '" . $details[1] . "' WHERE ContractVolgnummer = '" . $details[2] . "'";

			if ($conn->query($sql) === TRUE) {
				echo "Record updated successfully";
			} else {
				echo "Error updating record: " . $conn->error;
				die();
			}

		}
	} else {
		echo 'Er is een fout opgetreden.';
		die();
	}
}

$conn->close();
sqlsrv_free_stmt($updateResults);
echo "<script>window.close();</script>";

?>
