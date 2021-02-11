<div>
	<p>Aantal Images</p>
	<div id="imagesAantalImages" class="grafiek-transparant"></div>
</div>

<?php
    // query
    $sql = "SELECT status, SUM(aantal) as aantal
            FROM (
            SELECT status2020 AS status, COUNT(*) AS aantal FROM `byod-orders`.images2019
            WHERE okvoor2020 = '1'
            GROUP BY status
            UNION ALL
            SELECT status, COUNT(*) AS aantal FROM `byod-orders`.images2020
            WHERE confirmed = '1'
            GROUP BY status) q
            WHERE status != 'done'
            GROUP BY status";
    $result = $conn->query($sql);

    // preset data
    $aantallen = [
        'nieuw' => 0,
        'open' => 0,
        'testing' => 0,
        'testingok' => 0,
        'fouten' => 0
    ];

    // vul data
    foreach ($result as $item) {
		if (!isset($aantallen[$item['status']])) {
			continue;
		}
        $aantallen[$item['status']] = $item['aantal'];
    }

    // maak grafiek
    echo createGrafiek($aantallen, 'Aantal images', $typeBar, 'imagesAantalImages');
?>
