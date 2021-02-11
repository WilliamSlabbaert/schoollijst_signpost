<?php
	$title = 'Leermiddel Contracten';
	include('head.php');
	include('nav.php');
	include('conn.php');
	include('readonly-conn.php');

		$sql = "SELECT
    					ContractVolgnummer as Contract, VoornaamLeerling as Voornaam, NaamLeerling as Naam, instruction as Label, NaamOuder as Ouder, Email1 as Email, StartDatum, DatumVoorschotOntvangen as DatumVoorschot, DatumContractOntvangen as DatumContract, lengte as Levering, CAST(greatest(DatumVoorschotOntvangen, DatumContractOntvangen) as date) AS HoogsteDatum
				FROM
    					leermiddel.tblcontractdetails
				WHERE
								((VoorschotOntvangen = '1' AND ContractOntvangen = '1' AND deleted = '0')
						AND
								((DatumContractOntvangen BETWEEN CURDATE() - INTERVAL '29' DAY AND CURDATE())
						OR
								(DatumVoorschotOntvangen BETWEEN CURDATE() - INTERVAL '29' DAY AND CURDATE())))
						ORDER BY
								HoogsteDatum Desc";
		$leermiddeltotaal = 0;
		$leermiddelgeleverd = 0;

		$dag1 = 0;
		$dag2 = 0;
		$dag3 = 0;
		$dag4 = 0;
		$dag5 = 0;
		$dag6 = 0;
		$dag7 = 0;

		$datum1 = date("Y-m-d",strtotime("-1 days"));
		$datum2 = date("Y-m-d",strtotime("-2 days"));
		$datum3 = date("Y-m-d",strtotime("-3 days"));
		$datum4 = date("Y-m-d",strtotime("-4 days"));
		$datum5 = date("Y-m-d",strtotime("-5 days"));
		$datum6 = date("Y-m-d",strtotime("-6 days"));
		$datum7 = date("Y-m-d",strtotime("-7 days"));


		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				switch ($row['HoogsteDatum'])
				{
					case $datum7:
						$dag7+=1;
						break;
					case $datum6:
						$dag6+=1;
						break;
					case $datum5:
						$dag5+=1;
						break;
					case $datum4:
						$dag4+=1;
						break;
					case $datum3:
						$dag3+=1;
						break;
					case $datum2:
						$dag2+=1;
						break;
					case $datum1:
						$dag1+=1;
						break;
				}
				if (!empty($row['Levering']))
				{
					$leermiddelgeleverd+=1;
				}
				$leermiddeltotaal+=1;
			}
		}
		$leermiddelnietgeleverd = $leermiddeltotaal - $leermiddelgeleverd;

?>

<div class="container body">
	<center>
	<div style="display:flex; justify-content:space-around; width:100%; flex-wrap:wrap;">
		<div style="width:50%; min-width:550px; height:270px;">
			<h3>Leermiddel</h3>
			<div style="display:flex;justify-content:space-around;height:250px;">
				<div style="width:50%;height:100%;">
					<p>Geleverd in laatste 30 dagen</p>
					<canvas id="myChart9" width="100%" height="60px"></canvas>
				</div>
				<div style="width:45%;height:100%;">
				<p>Aantal Contracten In Orde</p>
				<canvas id="myChart10" width="100%" height="92px"></canvas>
			</div>
			</div>
		</div>
	</div>
	</center>

<?php
	echo "
		<script>
		var ctx = document.getElementById('myChart9').getContext('2d');
		var myChart9 = new Chart(ctx, {
			type: 'pie',
			data: {
				labels: ['Geleverd', 'Niet geleverd'],
				datasets: [{
					label: 'Aantal toestellen',
					data: [" . $leermiddelgeleverd . ", " . $leermiddelnietgeleverd . "],
					backgroundColor: [
						'rgba(54, 162, 235, 1)',
						'rgba(231, 76, 60, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(44, 62, 80, 1)',
						'rgba(170, 170, 170, 1)',
						'rgba(24, 188, 156, 1)'
					],
					borderColor: [
						'rgba(0, 0, 0, 0.4)',
						'rgba(0, 0, 0, 0.4)',
						'rgba(0, 0, 0, 0.4)',
						'rgba(0, 0, 0, 0.4)',
						'rgba(0, 0, 0, 0.4)',
						'rgba(0, 0, 0, 0.4)'
					],
					borderWidth: 2
				}]
			},
			options: {
				legend: {
					position: 'bottom'
				}
			}
		});
		</script>";

	echo "
<script>
var ctx = document.getElementById('myChart10').getContext('2d');
var myChart10 = new Chart(ctx, {
	type: 'bar',
	data: {
		labels: ['$datum7', '$datum6', '$datum5', '$datum4', '$datum3', '$datum2', '$datum1'],
		datasets: [{
			label: 'Aantal contracten in orde gebracht',
			data: [" . $dag7 . ", " . $dag6 . ", " . $dag5 . ", " . $dag4 . ", " . $dag3 . ", " . $dag2 . ", " . $dag1 . "],
			backgroundColor: [
				'rgba(231, 76, 60, 1)',
				'rgba(255, 206, 86, 1)',
				'rgba(54, 162, 235, 1)',
				'rgba(44, 62, 80, 1)',
				'rgba(170, 170, 170, 1)',
				'rgba(24, 188, 156, 1)',
				'rgba(233, 30, 99, 0.7)'
			],
			borderColor: [
				'rgba(0, 0, 0, 0.4)',
				'rgba(0, 0, 0, 0.4)',
				'rgba(0, 0, 0, 0.4)',
				'rgba(0, 0, 0, 0.4)',
				'rgba(0, 0, 0, 0.4)',
				'rgba(0, 0, 0, 0.4)',
				'rgba(0, 0, 0, 0.4)'
			],
			borderWidth: 2
		}]
	},
	options: {
		scales: {
			yAxes: [{
				ticks: {
					beginAtZero: true
				}
			}]
		},
		legend: {
			display: false
		}
	}
});
</script>";

?>

</div>



<?php
include('footer2.php');
?>
