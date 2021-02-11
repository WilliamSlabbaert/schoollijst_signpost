<!-- onbetaaldeMagentoOrderOpProcessing   -->
<div class="card col-lg-2" style="width: 18rem; border-radius: 25px">
	<a data-toggle="modal" data-target="#onbetaaldeMagentoOrderOpProcessing">
		<div class="card-img-top">
			<p class="<?php if($onbetaaldeMagentoOrderOpProcessingAantal > 0) {echo 'text-danger';} else {echo 'text-success';}?>" style="font-size: 100px; margin: auto;width: 50%; text-align: center; "><?php echo $onbetaaldeMagentoOrderOpProcessingAantal;?></p>
		</div>
		<div class="card-body">
			<p class="card-text" style="text-align: center; font-size: 29px; color:#00adba; font-weight: bold;"><?php echo $onbetaaldeMagentoOrderOpProcessingTitel;?></p>
		</div>
	</a>
</div>

<div class="modal fade" id="onbetaaldeMagentoOrderOpProcessing" tabindex="-1" role="dialog" aria-labelledby="onbetaaldeMagentoOrderOpProcessing" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ontbrekendeWSOrdersDataTitle"><?php echo $onbetaaldeMagentoOrderOpProcessingAantal . ' ' . $onbetaaldeMagentoOrderOpProcessingTitel;?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<thead>
					<tr>
						<th scope="col">Order ID</th>
						<th scope="col">Persoon</th>
						<th scope="col">E-mailadres</th>
						<th scope="col">Bedrag</th>
					</tr>
					</thead>
					<tbody>
					<?php
                        foreach ($onbetaaldeMagentoOrderOpProcessing as $item) {
                            echo "<tr>";
                            echo "<th scope='row'>".$item['id'] . "</th>";
                            echo "<td>".$item['naam']."</td>";
                            echo "<td>" . $item['email'] . "</td>";
                            echo "<td>" . $item['bedrag'] . "</td>";
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

