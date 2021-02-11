<div style="width:50%;height:100%;">
	<p>Merk</p>
	<div id="forecastsMerk" class="grafiek-transparant"></div>
</div>

<?php
    // query
	$sql = "SELECT manufacturer, SUM(`device1` + `device2` + `device3` + `device4`) AS aantal
			FROM `byod-orders`.forecasts
			LEFT JOIN devices ON devices.SPSKU = SUBSTRING_INDEX(forecasts.`device1-SPSKU`,';',1) GROUP BY manufacturer";
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
    echo createGrafiek($aantallen, 'Aantal toestellen', $typePie, 'forecastsMerk');
?>