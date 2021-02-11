<?php
	$typeBar = 'bar2d';
	$typePie = 'pie2d';

	$dataOptions = [
		'HP' => [
			'kleur' => '#36a2eb',
			'label' => 'HP'
		],
		'Lenovo' => [
			'kleur' => '#e74c3c',
			'label' => 'Lenovo'
		],
		'Windows 10' => [
			'kleur' => '#36a2eb',
			'label' => 'Windows'
		],
		'Chrome OS' => [
			'kleur' => '#ffce56',
			'label' => 'Chrome'
		],
		'nieuw' => [
			'kleur' => '#e74c3c',
			'label' => 'Nieuw'
		],
		'open' => [
			'kleur' => '#ffce56',
			'label' => 'Open'
		],
		'testing' => [
			'kleur' => '#36a2eb',
			'label' => 'Testfase'
		],
		'testingok' => [
			'kleur' => '#2c3e50',
			'label' => 'Testing OK'
		],
		'fouten' => [
			'kleur' => '#aaaaaa',
			'label' => 'Fout na test'
		],
		'done' => [
			'kleur' => '#18bc9c',
			'label' => 'Afgewerkt'
		],
		'Signpost' => [
			'kleur' => '#36a2eb',
			'label' => 'Signpost'
		],
		'TechData' => [
			'kleur' => '#e74c3c',
			'label' => 'TechData'
		],
		'Copaco' => [
			'kleur' => '#ffce56',
			'label' => 'Copaco'
		],
		'ombouw' => [
			'kleur' => '#ffce56',
			'label' => 'Ombouw'
		],
		'wachten op image' => [
			'kleur' => '#36a2eb',
			'label' => 'Wachten op Image'
		],
		'imaging' => [
			'kleur' => '#2c3e50',
			'label' => 'Imaging'
		],
		'levering' => [
			'kleur' => '#aaaaaa',
			'label' => 'Levering'
		],
		'uitgeleverd' => [
			'kleur' => '#18bc9c',
			'label' => 'Uitgeleverd'
		],
	];

	/**
	*  maken van de grafiek
	*
	* @param array $data
	* @param string $label
	* @param string $type
	* @param string $domId
	*
	* @return string
	*/
	function createGrafiek($data, $label, $type, $domId) {
		$jsonData = createJsonGrafiekData($data);

		return createChart(
			$domId,
			$label,
			$jsonData,
			$type
		);
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Private functions
///
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	* @param array $data
	*
	* @return false|string
	*/
	function createJsonGrafiekData(array $data) {
		global $dataOptions;

		$grafiekData = [];

		foreach ($data as $key => $item) {
			$grafiekData[] = [
				'label' => $dataOptions[$key]['label'],
				'value' => $item,
				'color' => $dataOptions[$key]['kleur'],
				'plotGradientColor' => $dataOptions[$key]['kleur'],
			];
		}

		return json_encode($grafiekData);
	}

	/**
	* aanmaken van het chart script
	*
	* @param string $domId
	* @param string $label
	* @param string $jsonData
	* @param string $type
	*
	* @return string
	*/
	function createChart($domId, $label, $jsonData, $type) {
		global $typePie;
		$showLabels = 1;
		if ($type === $typePie) {
			$showLabels = 0;
		}
		
		return "
			<script type='text/javascript'>
				FusionCharts.ready(function(){
					var chartObj = new FusionCharts({
							type: '" . $type . "',
							renderAt: '"  . $domId . "',
							width: '252',
							height: '231',
							dataFormat: 'json',
							dataSource: {
								'chart': {
									'caption': '" . $label . "',
									'showPercentInTooltip': '0',
									'decimals': '1',
									//Theme
									'theme': 'fusion',
									'canvasBgAlpha' : '0',
									'bgColor' : '#ffffff',
									'bgAlpha' : '0',
									'showLabels' : '".$showLabels."',
									'showValues' : '0'
									 								
								},
								'data': " . $jsonData . "
							}
						}
					);
					chartObj.render();
				});
			</script>
		";
	}