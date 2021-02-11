<!-- ontbrekendeWSOrdersData   -->
<div class="card col-lg-2" style="width: 18rem; border-radius: 25px">
	<a data-toggle="modal" data-target="#ontbrekendeWSOrdersData">
		<div class="card-img-top">
			<p class="<?php if($ontbrekendeDataOrdersAantal > 0) {echo 'text-danger';} else {echo 'text-success';}?>" style="font-size: 100px; margin: auto;width: 50%; text-align: center; "><?php echo $ontbrekendeDataOrdersAantal;?></p>
		</div>
		<div class="card-body">
			<p class="card-text" style="text-align: center; font-size: 29px; color:#00adba; font-weight: bold;"><?php echo $ontbrekendeDataOrdersTitel;?></p>
		</div>
	</a>
</div>

<div class="modal fade" id="ontbrekendeWSOrdersData" tabindex="-1" role="dialog" aria-labelledby="ontbrekendeWSOrdersDataTitle" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ontbrekendeWSOrdersDataTitle"><?php echo $ontbrekendeDataOrdersAantal . ' ' . $ontbrekendeDataOrdersTitel;?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
					<tr>
						<th scope="col">Order NR</th>
						<th scope="col">Leerling</th>
						<th scope="col">School</th>
						<th scope="col">Toestel</th>
					</tr>
					</thead>
					<tbody>
					<?php
                        while ($item = sqlsrv_fetch_array($ontbrekendeDataOrders)) {
                            echo "<tr>";
                            echo "<th scope='row'>".$item['OrderNr']."</th>";
                            echo "<td>".$item['VolgNummer'] . " - ". replaceNull($item['Voornaam'], 'Voornaam leerling'). " " . replaceNull($item['Familienaam'], 'Naam leerling')."</td>";
                            echo "<td>".replaceNull($item['SID'], 'SynergyID') ." ".$item['Snaam']."</td>";
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

