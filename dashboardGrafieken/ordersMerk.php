<div style="width:50%;height:100%;">
	<p>Merk</p>
	<div id="ordersMerk" class="grafiek-transparant"></div>
</div>

<?php
    // query
	$sql = "SELECT manufacturer, sum(amount) as aantal FROM `byod-orders`.orders left join devices on devices.SPSKU = orders.SPSKU group by manufacturer";
	$result = $conn->query($sql);

	// preset data
    $aantallen = [
        'HP' => 0,
        'Lenovo' => 0
    ];

    // vul data
	foreach ($result as $item) {
        if (!isset($aantallen[$item['manufacturer']])) {
            continue;
        }
        $aantallen[$item['manufacturer']] = $item['aantal'];
    }

    // maak grafiek
    echo createGrafiek($aantallen, 'Aantal toestellen', $typePie, 'ordersMerk');
?>