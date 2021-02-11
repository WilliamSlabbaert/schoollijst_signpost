<?php

$title = 'Webshop leveringen koppelen';
include('head.php');
include('nav.php');
include('conn.php');
include('mssql-100-conn.php');

?>

<div class="body">

	<h3>Webshop leveringen handmatig koppelen</h3>
	<p style="smalltext">Uitleg: Heb je een lijst van een school ontvangen met leerling - label nummers?<br>
	Klik dan op de knop naast de juiste synergyid, vul daarna het label in bij de juiste leerling.<br>
	<span style="color: red !important;">Als je het niet weet, laat het dan leeg!</span><br>
	Klik dan op submit om de aanpassing door te sturen. Nu zijn deze orders gemarkeerd als geleverd.</p>
	<br>

<?php
if(isset($_POST['synergyid']) == true){

	$synergyid = 0;
	foreach($_POST as $key => $value){
		if($value !== ''){
			if($key == 'synergyid'){
				$synergyid = $value;
			} else {
				$arr = explode(';', $key);
				if($arr[0] == 'exact'){

					$tsql= "SELECT id
						FROM orsrg with (nolock)
						WHERE instruction = '" . $value . "';";
					$stmt = sqlsrv_query( $msconn, $tsql);

					if($stmt === false) {
						die( print_r( sqlsrv_errors(), true) );
					}

					while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {

						$tsql= "UPDATE orsrg
							SET instruction = '" . strtok($value, '-') . "'
							WHERE id = '" . $row['id'] . "';";
						$updateResults= sqlsrv_query($msconn, $tsql);
						if ($updateResults == FALSE){
							die( print_r( sqlsrv_errors(), true));
						}
					}

					$tsql= "UPDATE orsrg
						SET lengte = '999.999', instruction = '" . $value . "'
						WHERE id = '" . $arr[1] . "';";
					$updateResults= sqlsrv_query($msconn, $tsql);
					if ($updateResults == FALSE){
						die( print_r( sqlsrv_errors(), true));
					}

				} elseif($arr[0] == 'leermiddel'){

					$sql = "UPDATE leermiddel.tblcontractdetails SET instruction='" . strtok($value, '-') . "' WHERE instruction = '" . $value . "';";
					if ($conn->query($sql) === TRUE) {
						echo "Record updated successfully";
					} else {
						echo "Error updating record: " . $conn->error;
					}

					$sql = "UPDATE leermiddel.tblcontractdetails SET lengte='999.999', instruction='" . $value . "' WHERE contractvolgnummer = '" . $arr[1] . "';";
					if ($conn->query($sql) === TRUE) {
						echo "Record updated successfully";
					} else {
						echo "Error updating record: " . $conn->error;
					}
				}
			}
		}
	}

	echo 'Indien u geen errors ziet, dan is het gelukt.<br>
		<a href="'. hasAccessForUrl('webshop-instructionfix.php', false).'">Klik hier om terug te gaan naar het overzicht</a>';

} elseif(isset($_GET['synergyid']) == true){
?>
	<form action="webshop-instructionfix.php" method="POST">
	<?php
		echo '<input type="text" name="synergyid" value="' . $_GET['synergyid'] . '" hidden>';
	?>
	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Bestelnummer</th>
				<th scope="col">Voornaam</th>
				<th scope="col">Achternaam</th>
				<th scope="col">Label van het toestel dat aan de klant geleverd is</th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *
					FROM leermiddel.`tblcontractdetails`
					LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `id`
					LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
					WHERE `deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum = '2020-09-01' AND `lengte` = 0 AND SynergySchoolID = '" . $_GET['synergyid'] . "'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					echo '<tr>';
					echo '<td>' . $row['ContractVolgnummer'] . '</td>';
					echo '<td>' . $row['VoornaamLeerling'] . '</td>';
					echo '<td>' . $row['NaamLeerling'] . '</td>';
					echo '<td><input type="text" name="leermiddel;' . $row['ContractVolgnummer'] . '" class="form-control"></td>';
					echo '</tr>';

				}

			} else {

				//echo "0 results";

			}

			$conn->close();

			$tsql = "SELECT refer, refer1, refer2, refer3, freefield1, lengte, breedte, instruction, artcode, orsrg.id as orsrgid, ord_soort, user_id
					FROM orkrg with (nolock)
					INNER JOIN orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
					where len(orkrg.freefield1)>0 and (artcode like 'H%' or artcode like 'L%') and lengte=0 and freefield1 = '" . $_GET['synergyid'] . "'";
			$stmt = sqlsrv_query( $msconn, $tsql);
			if($stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			while( $row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
				echo '<tr>';
					echo '<td>' . $row2['refer'] . '</td>';
					echo '<td>' . $row2['refer1'] . '</td>';
					echo '<td>' . $row2['refer2'] . '</td>';

					echo '<td><input type="text" name="exact;' . $row2['orsrgid'] . '" class="form-control"></td>';
				echo '</tr>';
			}
		?>

		</tbody>
	</table><br><br><br><br>
	<input type="submit" value="Submit">
	</form>
<?php
} else {
?>
	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Synergyid</th>
				<th scope="col">School</th>
				<th scope="col">Aantal</th>
				<th scope="col"></th>
			</tr>
		</thead>

		<tbody>
		<?php

			$sql = "SELECT *, COUNT(*) AS aantal
					FROM leermiddel.`tblcontractdetails`
					LEFT JOIN leermiddel.`tbltoestelcontractdefinitie` ON `ToestelContractDefinitieID` = `id`
					LEFT JOIN leermiddel.`tblschool` ON tblcontractdetails.SchoolID = tblschool.id
					WHERE `deleted` = 0 AND contractontvangen = 1 AND VoorschotOntvangen IN ('1', '-1') AND tblcontractdetails.StartDatum = '2020-09-01' AND `lengte` = 0
					GROUP BY SynergySchoolID";
			$result = $conn->query($sql);

			if ($result->num_rows > 0) {

				while($row = $result->fetch_assoc()) {

					echo '<tr>';
					echo '<td>' . $row['SynergySchoolID'] . '</td>';
					echo '<td>' . $row['SchoolNaam'] . '</td>';
					echo '<td>' . $row['aantal'] . '</td>';
					echo '<td><a href="'. hasAccessForUrl('webshop-instructionfix.php?fix&synergyid=' . $row['SynergySchoolID'] . '', false).'">Bekijk openstaande orders</a></td>';
					echo '</tr>';

				}

			} else {

				//echo "0 results";

			}

			$conn->close();

			$tsql = "select orkrg.freefield1, cmp_name as schoolnaam, count(*) as aantal
					from orkrg with (nolock)
					inner join orsrg with (nolock) on orkrg.ordernr=orsrg.ordernr
					left join cicmpy with (nolock) on ltrim(cmp_code) = orkrg.freefield1
					where len(orkrg.freefield1)>0 and (artcode like 'H%' or artcode like 'L%') and lengte=0
					group by orkrg.freefield1, cmp_name";
			$stmt = sqlsrv_query( $msconn, $tsql);
			if($stmt === false) {
				die( print_r( sqlsrv_errors(), true) );
			}
			while( $row2 = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
					echo '<tr>';
					echo '<td>' . $row2['freefield1'] . '</td>';
					echo '<td>' . $row2['schoolnaam'] . '</td>';
					echo '<td>' . $row2['aantal'] . '</td>';
					echo '<td><a href="'. hasAccessForUrl('webshop-instructionfix.php?fix&synergyid=' . $row2['freefield1'] . '', false).'">Bekijk openstaande orders</a></td>';
					echo '</tr>';
			}

			echo '
		</tbody>
	</table><br><br><br><br>';
}
?>

</div>

<?php
include('footer.php');
?>
