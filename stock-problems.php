<?php

	$title = 'Stock Problemen';

	include('head.php');
	include('nav.php');
	include('conn.php');

?>

<div class="body">

<?php

	$sql = "SELECT concat('<a href=\"delivery.php?orderid=', orderid, '\" target=\"_blank\">', orderid, '</a>') as orderid,
		( SELECT spsku FROM orders WHERE id = orderid LIMIT 1) AS SPSKU,
		( SELECT productnumber FROM devices WHERE spsku = ( SELECT spsku FROM orders WHERE id = orderid ) LIMIT 1) AS SKU,
		COUNT(labels.label) AS 'aantal labels',
		(IFNULL((SELECT amount FROM orders WHERE id = labels.orderid), 0)-IFNULL((SELECT SUM(amount) FROM delivery WHERE orderid = labels.orderid AND accepted_by != ''), 0)) AS 'theoretisch in stock',
		COUNT(stock.label) AS 'effectief in stock',
		ABS((IFNULL((SELECT amount FROM orders WHERE id = labels.orderid), 0)-IFNULL((SELECT SUM(amount) FROM delivery WHERE orderid = labels.orderid AND accepted_by != ''), 0))-(COUNT(stock.label))) AS 'verschil'
		FROM labels
		LEFT JOIN stock ON stock.label = labels.label
		GROUP BY orderid
		HAVING COUNT(stock.label) != (IFNULL((SELECT amount FROM orders WHERE id = labels.orderid), 0)-IFNULL((SELECT SUM(amount) FROM delivery WHERE orderid = labels.orderid AND accepted_by != ''), 0))
		ORDER BY (IFNULL((SELECT amount FROM orders WHERE id = labels.orderid), 0)-IFNULL((SELECT SUM(amount) FROM delivery WHERE orderid = labels.orderid AND accepted_by != ''), 0)) DESC;";
	echo '<h3>Stock problemen</h3>';

	$result = $conn->query($sql);
	echo createTable($result, 'mysql');

?>
</div>

<?php
	include('footer.php');
?>
