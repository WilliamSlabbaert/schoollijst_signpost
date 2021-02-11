<div style="width:50%;height:100%;">
	<p style="padding-top:45px;">OS</p>
	<div id="ordersOS" class="grafiek-transparant"></div>
</div>

<?php
    // query
	$sql = "SELECT
	    operating_system as os,
        sum(amount) as aantal
        FROM `byod-orders`.orders
        left join devices on devices.SPSKU = orders.SPSKU
        group by operating_system
    ";
	$result = $conn->query($sql);

    // preset data
    $aantallen = [
        'Windows 10' => 0,
        'Chrome OS' => 0,
    ];

    // vul data
    foreach ($result as $item) {
		if (!isset($aantallen[$item['os']])) {
			continue;
		}
		$aantallen[$item['os']] = $item['aantal'];
    }

    // maak grafiek
    echo createGrafiek($aantallen, 'Aantal toestellen', $typePie, 'ordersOS');
?>