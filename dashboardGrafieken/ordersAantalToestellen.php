<div style="width:45%;height:100%;">
	<p>Aantal Toestellen</p>
	<div id="ordersAantalToestellen" class="grafiek-transparant"></div>
</div>

<?php
    // query
    $result = $conn->query("SELECT status, sum(amount) as aantal FROM `byod-orders`.orders where status != 'uitgeleverd' group by status");

    // preset data
    $aantallen = [
        'nieuw' => 0,
        'ombouw' => 0,
        'wachten op image' => 0,
        'imaging' => 0,
        'levering' => 0
    ];
    $plusImagenArray = [
        'tdconfigadmin' => 'tdconfigadmin',
        'tdgeenvoorraad' => 'tdgeenvoorraad',
        'tdimaging' => 'tdimaging',
        'tdafgewerkttemp' => 'tdafgewerkttemp'
    ];

    // vul data
    foreach ($result as $item) {
        $status = $item['status'];

		if (!isset($aantallen[$status])) {
			continue;
		}

        if ($status === 'tdafgewerkt') {
            $aantallen[$status] = $item['aantal'] + $aantallen['levering'];
            continue;
        } elseif( isset($plusImagenArray[$status])) {
            $aantallen[$status] = $item['aantal'] + $aantallen['imaging'];
            continue;
        }
        $aantallen[$status] = $item['aantal'];
    }

    // maak grafiek
    echo createGrafiek($aantallen, 'Aantal toestellen', $typeBar, 'ordersAantalToestellen');
?>
