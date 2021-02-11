<?php

$title = 'Magento 1';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

	<form action="magento1.php" method="post" class="searchbar">
		Magento ordernummer: <input type="text" name="search" class="form-control"  placeholder="🔍 Zoeken" value="<?php if(isset($_POST['search']) == true){ echo $_POST['search']; } else { echo '780000'; } ?>" autocomplete="off">
		<input type="submit" class="form-control col-1 btn-success" value="Zoeken">
	</form><br>

	<?php

	if(isset($_POST['search']) == true && $_POST['search'] !== ''){
		$search = mysqli_real_escape_string($conn, $_POST['search']);
		echo '<h3>Magento 1 bestellingen</h3>';
		$sql = "SELECT increment_id AS Bestelnummer, Status, CONCAT(customer_firstname, ' ', customer_lastname) AS Klant, customer_email AS 'E-mail', customer_prefix AS School, ifnull(customer_taxvat, '/') as 'BTW Nummer', ifnull(coupon_code, '/') as 'Korting Code',
			(SELECT concat(firstname, ' ', lastname, '<br>', ifnull(company, '/'), '<br>', street, '<br>', postcode, ' ', city, '<br>', country_id, '<br>', telephone) FROM magento.sales_flat_order_address WHERE parent_id = sales_flat_order.entity_id AND address_type = 'shipping' ) AS Leveradres,
			(SELECT concat(firstname, ' ', lastname, '<br>', ifnull(company, '/'), '<br>', street, '<br>', postcode, ' ', city, '<br>', country_id, '<br>', telephone) FROM magento.sales_flat_order_address WHERE parent_id = sales_flat_order.entity_id AND address_type = 'billing' ) AS Facturatieadres
			FROM magento.sales_flat_order
			WHERE increment_id LIKE '" . $search . "%'";
		$result = $conn->query($sql);
		echo createTable($result, 'mysql');
	}

	?>

</div>

<?php
include('footer.php');
?>
