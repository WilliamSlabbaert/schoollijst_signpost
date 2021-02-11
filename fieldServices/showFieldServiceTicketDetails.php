<?php

	function showFieldServiceTicketDetails($conn, $caseId) {

		$sql = "SELECT * FROM fieldServiceCases where id = '" . $caseId . "';";
		$result = $conn->query($sql);

		$card = 'test';

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				date_default_timezone_set("Europe/Brussels");
				$card = '<div class="card-deck col">
							<div class="card">
								<div class="card-body">
									<h5 class="card-title">' . $row['serial'] . '</h5>
									<p class="card-text">' . $row['type'] . ' - ' . $row['description'] . '</p>
								</div>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										' . $row['firstname'] . ' ' . $row['lastname'] . '<br>
										<a href="mailto:' . $row['email'] . '">' . $row['email'] . '</a><br>
										' . $row['phone'] . '<br>
									</li>
									<li class="list-group-item">
										' . $row['street'] . ' ' . $row['number'] . '<br>
										' . $row['zip'] . ' ' . $row['city'] . '<br>
										' . $row['country'] . '
									</li>
									<li class="list-group-item">
										Bijlage: <a href="#">' . $row['serial'] . '.png</a>
									</li>
								</ul>
								<div class="card-footer">
									<small class="text-muted">Laatste update: ' . date("l d F Y - H:i", strtotime($row['updatedAt'])) . '</small>
								</div>
							</div>
						</div>';

			}
		} else {
			echo 'nothing found';
		}

		return $card;

	}
