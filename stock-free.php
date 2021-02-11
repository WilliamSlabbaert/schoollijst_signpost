<?php

	$title = 'Vrije stock';

	include('head.php');
	include('nav.php');
	include('conn.php');

?>

<div class="body">

	<h1>Vrije stock</h1>
	<p style="color:red;">Opgelet: Deze weergave is niet definitief, bij bestellingen ook altijd navragen bij Nathalie!</p>

	<?php
		$sql = "SELECT SKU, (SELECT description FROM exact_skus WHERE sku = stock.sku LIMIT 1) AS Beschrijving, COUNT(*) AS aantal
				FROM stock
				WHERE label = ''
				GROUP BY sku";
		$result = $conn->query($sql);
		echo createTable($result, 'mysql');
	?>

</div>

<?php
	include('footer.php');
?>
