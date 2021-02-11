<div>
	<p>Aantal toestellen</p>
	<div id="magazijnAantalToestellen" class="grafiek-transparant"></div>
</div>

<?php
    // query
	$sql = "SELECT warehouse, SUM(amount) as aantal FROM orders WHERE orders.deleted != 1 GROUP BY warehouse";
	$result = $conn->query($sql);

	// preset data
	$aantallen = [
        'Signpost' => 0,
        'TechData' => 0,
        'Copaco' => 0,
    ];

	// vul data
	foreach ($result as $item) {
        if (!isset($aantallen[$item['warehouse']])) {
            continue;
        }
        $aantallen[$item['warehouse']] = $item['aantal'];
    }

    // maak grafiek
    echo createGrafiek($aantallen, 'Aantal toestellen', $typePie, 'magazijnAantalToestellen');
?>