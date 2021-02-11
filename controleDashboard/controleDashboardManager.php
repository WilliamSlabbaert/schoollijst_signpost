<?php
	/**
	 * haal alle bestellingen/leermiddelen zonder forecast
	 *
	 * @param mysqli $ps2Conn
	 * @param resource $msConn
	 *
	 * @return array
	 */
	function getForcast($ps2Conn, $msConn) {
		$leermiddelen = getLeermiddelen($ps2Conn);
		$bestellingen = getBestellingen($msConn);

		$geenForecast = [];

		foreach ($leermiddelen as $middel) {
			$geenForecast = CheckForcast($ps2Conn, $middel, $geenForecast);
		}

		while ($bestelling = sqlsrv_fetch_array($bestellingen)) {
			$geenForecast = CheckForcast($ps2Conn, $bestelling, $geenForecast);
		}

		return $geenForecast;
	}

	/**
	 * haal ontbrekende data orders
	 *
	 * @param resource $msConn
	 *
	 * @return false|resource
	 */
	function getOntbrekendeDataOrders($msConn) {
		$sql = "
			SELECT orkrg.freefield1 AS SID,
       			orkrg.ordernr AS OrderNr,
				artcode AS SKU,
				refer AS VolgNummer,
				orkrg.refer1 AS Voornaam,
				orkrg.refer2 AS Familienaam,
				(SELECT cmp_name FROM cicmpy WITH (nolock) WHERE TRIM(cmp_code)=TRIM(freefield1)) AS Snaam
			FROM orkrg WITH (nolock)
			INNER JOIN orsrg WITH (nolock) ON orkrg.ordernr = orsrg.ordernr
			WHERE (artcode LIKE 'H%' OR artcode like 'L%')
			AND lengte = 0
			AND ar_soort NOT LIKE 'P'
			AND selcode in ('WS', 'W2')
			AND (orkrg.freefield1 is null OR orkrg.refer1 is null OR orkrg.refer2 is null)
			AND dbo.orkrg.user_id = 'DEALER4D'
			AND ( SELECT IsSerialNumberItem FROM items WITH (nolock) WHERE itemcode = artcode) = 1
		";

		return sqlsrv_query($msConn, $sql, [], ['Scrollable' => 'static']);
	}

	/**
	 * lege data vervangen door rood kruis
	 *
	 * @param string|null $data
	 * @param string|null $text
	 *
	 * @return string
	 */
	function replaceNull($data, $text = null) {
		if (is_null($data)) {
			return '<p class="text-danger">X '. (!is_null($text) ? $text : '') .'</p>';
		}
		return $data;
	}

	/**
	 * ophalen json data voor 30 dagen orders grafiek
	 *
	 * @param resource $msConn
	 * @param mysqli $ps2Conn
	 *
	 * @return array
	 *
	 * @throws Exception
	 */
	function get30DagenOrderGrafiekJsonData($msConn, $ps2Conn) {
		$data = preset30DagenArray();
		$data = getWebSchoolOrders30dagen($data, $msConn);
		$data = getLeermiddelOrders30dagen($data, $ps2Conn);

		return fillJsonData($data);
	}

	/**
	 * ophalen van alle onbetaalde magento orders die op status processing staan
	 *
	 * @param mysqli $mConn
	 *
	 * @return bool|mysqli_result
	 */
	function getOnbetaaldeOpProcessing($mConn) {
		$sql = "
			SELECT
				increment_id AS id,
				CONCAT_WS(' ', customer_firstname, customer_lastname) AS naam,
			   	customer_email AS email,
				base_grand_total AS bedrag
			FROM sales_order
			WHERE status != 'processing'
			AND base_total_paid = NULL
		";

		return $mConn->query($sql);
	}

	/**
	 * ophalen van betaalde magento orders die niet in exact steken
	 *
	 * @param mysqli $mConn
	 * @param resource $msConn
	 *
	 * @return mysqli_result|array
	 */
	function getBetaaldeMagentoOrdersNietInExact($mConn, $msConn) {
		$magentoOrders = getBetaaldeMagentoOrders($mConn);
		$exactOrders = getExactOrderForMagentoOrder($magentoOrders, $msConn);
		$verschil = getVerschilMagentoEnExactOrders($magentoOrders, $exactOrders);

		return getMagentoOrdersVoorIncrementId($mConn, $verschil);
	}

	/**
	 * ophalen van het aantal items dat in het object zitten
	 *
	 * @param $object
	 *
	 * @return false|int
	 */
	function aantalResults($object) {
		if (is_array($object)) {
			return count($object);
		}
		if ($object instanceof mysqli_result) {
			return mysqli_num_rows($object);
		}

		return sqlsrv_num_rows($object);
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Private functions
///
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * haal magento orders voor array van increment_id's
	 *
	 * @param mysqli $mConn
	 * @param array $verschil
	 *
	 * @return mysqli_result|array
	 */
	function getMagentoOrdersVoorIncrementId($mConn, $verschil) {
		if (empty($verschil)) {
			return [];
		}

		$implodedData = "'" . implode("', '", $verschil) . "'";

		$sql = "
			SELECT
				DISCTINCT increment_id AS orderId,
				CONCAT_WS(' ', customer_firstname, customer_lastname) AS naam,
				synergyid AS schoolId,
				sku AS sku
			FROM _alleordersjoined
			WHERE increment_id in (" . $implodedData .")
			GROUP BY increment_id 
		";

		return $mConn->query($sql);
	}

	/**
	 * vergelijken van gevonden betaalde magento orders en orders van gevonden exact orders
	 *
	 * @param array $magentoOrders
	 * @param resource $exactOrders
	 *
	 * @return array
	 */
	function getVerschilMagentoEnExactOrders($magentoOrders, $exactOrders) {
		$row = sqlsrv_fetch_array($exactOrders, SQLSRV_FETCH_NUMERIC, SQLSRV_SCROLL_ABSOLUTE, 0);
		$alles = explode(',', $row[0]);
		return array_diff($magentoOrders, $alles);
	}

	/**
	 * ophalen van exact orders aan de hand van increment_id's van magento
	 *
	 * @param array $magentoOrders
	 * @param resource $msConn
	 *
	 * @return resource
	 */
	function getExactOrderForMagentoOrder($magentoOrders, $msConn) {
		$implodedData = "'" . implode("', '", $magentoOrders) . "'";

		$sql = "
				select stuff((
				SELECT
				',', refer
				FROM orkrg WITH (nolock)
				INNER JOIN orsrg WITH (nolock) on orkrg.ordernr=orsrg.ordernr
				WHERE refer in (" . $implodedData . ")
				GROUP BY refer
				FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') as refer
		";

		return sqlsrv_query($msConn, $sql, [], ['Scrollable' => 'static']);
	}

	/**
	 * ophalen van betaalde magento orders
	 *
	 * @param mysqli $mConn
	 *
	 * @return array
	 */
	function getBetaaldeMagentoOrders($mConn) {
		$sql = "
			SELECT
				increment_id
			FROM `sales_order`
			WHERE status = 'processing'
			AND base_total_paid IS NOT NULL
		";

		$array = [];
		foreach ($mConn->query($sql) as $item) {
			$id = $item['increment_id'];
			$array[$id] = '' . $id;
		}
		return $array;
	}

	/**
	 * vullen en aanmaken van jsondata voor 30 dagen orders grafiek
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	function fillJsonData($data) {
		$jsonLabels = [];
		$jsonData = [
			[
				'seriesname' => 'Webshop',
				'color' => '#00adba',
				'data' => []
			],
			[
				'seriesname' => 'Leermiddel',
				'color' => '#ec6523',
				'data' => []
			],
			[
				'seriesname' => 'HED',
				'color' => '#8EC154',
				'data' => []
			],
		];

		foreach ($data as $key => $item) {
			$jsonLabels[]['label'] = $key;
			$jsonData[0]['data'][]['value'] = $item['Webshop'];
			$jsonData[1]['data'][]['value'] = $item['Leermiddel'];
			$jsonData[2]['data'][]['value'] = $item['Hogeschool'];
		}
		return [
			'labels' => json_encode($jsonLabels),
			'data' => json_encode($jsonData)
		];
	}

	/**
	 * haal alle leermiddel orders van de laatste 30 dagen
	 *
	 * @param array $data
	 * @param mysqli $ps2Conn
	 *
	 * @return array
	 *
	 * @throws Exception
	 */
	function getLeermiddelOrders30dagen($data, $ps2Conn) {
		$leermiddelQuery = "
			SELECT aantal, datum FROM
			(
			   SELECT
				COUNT(ContractID) AS aantal,
				CASE
				WHEN `DatumVoorschotOntvangen` IS NULL OR `DatumContractopgemaakt` IS NULL THEN NULL
				WHEN `DatumVoorschotOntvangen` > `DatumContractopgemaakt`  THEN `DatumVoorschotOntvangen`
				ELSE `DatumContractopgemaakt`
				END AS datum
				FROM leermiddel.tblcontractdetails
						LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `tbltoestelcontractdefinitie`.`id`
							LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
							WHERE `deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND `lengte` = 0
							GROUP BY datum
							) AS q
			WHERE
				q.datum > DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) AND
				q.datum <= CURRENT_DATE()
		";

		$result = $ps2Conn->query($leermiddelQuery);
		foreach ($result as $item) {
			$datum = $item['datum'];
			if (is_string($datum)) {
				$datum = new DateTime($datum);
			}
			$key = $datum->format('d M');
			$data[$key]['Leermiddel'] = (int) $item['aantal'];
		}
		return $data;
	}

	/**
	 * prefill een array met alle data van de afgelope 30 dagen als key
	 *
	 * @return array
	 */
	function preset30DagenArray() {
		$begin = (new DateTime())->sub(new DateInterval('P29D'));
		$eind = (new DateTime())->add(new DateInterval('P1D'));

		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $eind);

		$data = [];
		foreach ($period as $dt) {
			$data[$dt->format('d M')] = [
				'Webshop' => 0,
				'Leermiddel' => 0,
				'Hogeschool' => 0
			];
		}
		return $data;
	}

	/**
	 * haal alle webshop en HED orders op van de laatste 30 dagen
	 *
	 * @param array $data
	 * @param resource $msConn
	 *
	 * @return array
	 */
	function getWebSchoolOrders30dagen($data, $msConn) {
		$webshopQuery = "
				SELECT
					count(ID) AS aantal,
					orddat AS datum,
					selcode AS code
				FROM dbo.orkrg with (nolock)
				WHERE
					orddat > CONVERT(DATE, GETDATE() - 30) AND
					orddat <= CONVERT(DATE, GETDATE()) AND
					(selcode LIKE 'W%' OR selcode = 'HO')
				GROUP BY selcode, orddat
		";
		$stmt = sqlsrv_query($msConn, $webshopQuery);

		while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
			$key = $row['datum']->format('d M');

			if ($row['code'] === 'HO') {
				$data[$key]['Hogeschool'] = $row['aantal'];
				continue;
			}
			$data[$key]['Webshop'] = $row['aantal'];
		}
		return $data;
	}

	/**
	 * ophalen openstaande leermiddelen
	 *
	 * @param mysqli $ps2Conn
	 *
	 * @return mysqli_result|bool
	 */
	function getLeermiddelen($ps2Conn) {
		$sql = "
			SELECT tblschool.SynergySchoolID AS SID,
				tbltoestelcontractdefinitie.SKU AS SKU,
				ContractVolgnummer AS VolgNummer,
				VoornaamLeerling AS Voornaam,
				NaamLeerling AS Familienaam,
				Schoolnaam AS Snaam,
				DATE_FORMAT(
					CASE WHEN `DatumVoorschotOntvangen` IS NULL
					OR `DatumContractopgemaakt` IS NULL
					THEN NULL WHEN `DatumVoorschotOntvangen` > `DatumContractopgemaakt`
					THEN `DatumVoorschotOntvangen` ELSE `DatumContractopgemaakt` END,'%d %b %Y'
				) AS OK,
				'H' AS type
			FROM leermiddel.tblcontractdetails
			LEFT JOIN leermiddel.tbltoestelcontractdefinitie ON ToestelContractDefinitieID = tbltoestelcontractdefinitie.id
			LEFT JOIN leermiddel.tblschool ON tblcontractdetails.SchoolID = tblschool.id
			WHERE deleted = 0
			AND contractontvangen = 1
			AND VoorschotOntvangen IN ('1', '-1')
			AND tblcontractdetails.StartDatum >= '2020-09-01'
			AND lengte = 0
			ORDER BY tblcontractdetails.StartDatum DESC, SynergyHID
		";

		return $ps2Conn->query($sql);
	}

	/**
	 * ophalen openstaande bestellingen
	 *
	 * @param resource $msConn
	 *
	 * @return false|resource
	 */
	function getBestellingen($msConn) {
		$tsql =	"
			SELECT orkrg.freefield1 AS SID,
				artcode AS SKU,
				refer AS VolgNummer,
				orkrg.refer1 AS Voornaam,
				orkrg.refer2 AS Familienaam,
				(SELECT cmp_name FROM cicmpy WITH (nolock) WHERE TRIM(cmp_code)=TRIM(freefield1)) AS Snaam,
				FORMAT(orddat,'dd MMM yyyy') AS OK,
				'W' AS type
			FROM orkrg WITH (nolock)
			INNER JOIN orsrg WITH (nolock) ON orkrg.ordernr = orsrg.ordernr
			WHERE len(orkrg.freefield1) > 0
			AND (artcode LIKE 'H%' OR artcode like 'L%')
			AND lengte = 0
			AND ar_soort NOT LIKE 'P'
			ORDER BY orddat
		";

		return sqlsrv_query($msConn, $tsql);
	}

	/**
	 * nakijken of er een forecast aanwezig is voor de combinatie 'synergyId' en 'device1-SPSKU'
	 *
	 * @param mysqli $conn
	 * @param array $item
	 * @param array $array
	 *
	 * @return array
	 */
	function checkForcast($conn, $item, $array) {
		$sql = "
			SELECT *
			FROM forecasts
			WHERE synergyid = '".trim($item['SID'])."'
			AND ( 
				`device1-SPSKU` LIKE '%" . trim($item['SKU']) . "%'
				OR `device2-SPSKU` LIKE '%" . trim($item['SKU']) . "%'
				OR `device3-SPSKU` LIKE '%" . trim($item['SKU']) . "%'
				OR `device4-SPSKU` LIKE '%" . trim($item['SKU']) . "%'
			)
		";

		if(mysqli_num_rows($conn->query($sql)) == 0) {
			$array[] = $item;
		}
		return $array;
	}
