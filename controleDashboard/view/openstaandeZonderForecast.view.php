<!-- openstaandeZonderForecast   -->
<div class="card col-lg-2" style="width: 18rem; border-radius: 25px">
	<a data-toggle="modal" data-target="#openstaandeZonderForecast">
		<div class="card-img-top">
			<p class="<?php if($geenForecastAantal > 0) {echo 'text-danger';} else {echo 'text-success';}?>" style="font-size: 100px; margin: auto;width: 50%; text-align: center; "><?php echo $geenForecastAantal;?></p>
		</div>
		<div class="card-body">
			<p class="card-text" style="text-align: center; font-size: 29px; color:#00adba; font-weight: bold;"><?php echo $geenForecastTitel;?></p>
		</div>
	</a>
</div>

<div class="modal fade" id="openstaandeZonderForecast" tabindex="-1" role="dialog" aria-labelledby="openstaandeZonderForecastTitle" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="openstaandeZonderForecastTitle"><?php echo $geenForecastAantal . ' ' . $geenForecastTitel;?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
					<tr>
						<th scope="col">Leerling</th>
						<th scope="col">School</th>
						<th scope="col">OK</th>
						<th scope="col">Type</th>
						<th scope="col">Toestel</th>
					</tr>
					</thead>
					<tbody>
					<?php
                        foreach ($geenForecast as $item) {
                            echo "<tr>";
                            echo "<th scope='row'>".$item['VolgNummer'] . " - ". $item['Voornaam']. " " . $item['Familienaam']."</th>";
                            echo "<td>".$item['SID'] ." ".$item['Snaam']."</td>";
                            echo "<td>" . $item['OK'] . "</td>";
                            echo "<td>" . $item['type'] . "</td>";
                            echo "<td>" . $item['SKU'] . "</td>";
                            echo "</tr>";
                        }
					?>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

