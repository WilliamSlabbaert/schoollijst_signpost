<div style="width:50%;height:100%;">
	<p>OS</p>
	<div id="forecastsOS" class="grafiek-transparant"></div>
</div>

<?php
    // query
	$sql = "SELECT SUM(subtotaal) AS totaal, os
			FROM ( SELECT SUM(device1) AS subtotaal,
			( SELECT operating_system FROM devices WHERE spsku = SUBSTRING_INDEX(forecasts.`device1-SPSKU`,';',1) LIMIT 1) AS os
			FROM forecasts
			WHERE device1 != '' AND deleted != 1
			GROUP BY os
			UNION ALL
			SELECT SUM(device2) AS subtotaal,
			( SELECT operating_system FROM devices WHERE spsku = SUBSTRING_INDEX(forecasts.`device2-SPSKU`,';',1) LIMIT 1) AS os
			FROM forecasts
			WHERE device2 != '' AND deleted != 1
			GROUP BY os
			UNION ALL
			SELECT SUM(device3) AS subtotaal,
			( SELECT operating_system FROM devices WHERE spsku = SUBSTRING_INDEX(forecasts.`device3-SPSKU`,';',1) LIMIT 1) AS os
			FROM forecasts
			WHERE device3 != '' AND deleted != 1
			GROUP BY os
			UNION ALL
			SELECT SUM(device4) AS subtotaal,
			( SELECT operating_system FROM devices WHERE spsku = SUBSTRING_INDEX(forecasts.`device4-SPSKU`,';',1) LIMIT 1) AS os
			FROM forecasts
			WHERE device4 != '' AND deleted != 1
			GROUP BY os) q
			GROUP BY os";
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
        $aantallen[$item['os']] = $item['totaal'];
    }

    // maak grafiek
    echo createGrafiek($aantallen, 'Aantal toestellen', $typePie, 'forecastsOS');
?>
