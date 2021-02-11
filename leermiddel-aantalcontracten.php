<style>.dataTables_paginate a {
    color: #00ADBD30;
		padding: 8px 15px;
    text-decoration: none;
    transition: background-color .7s;
		margin-top:0.5em;
}

.dataTables_paginate{margin-top:0.5em}

.dataTables_paginate a.current {
    color: white;
		font-size: 16;
		border-style: solid;
		border-width: 1 ;
		border-color: white;
		background-color: #00ADBD30;
}

.dataTables_paginate a:hover {background-color: #00ADBD30; color:white}

tr:hover{background-color: #00ADBD30;}

.dataTables_length label {
    color: #00ADBD30;
	padding: 0px 40px;
}
</style>
<?php
	$title = 'Leermiddel Contracten';
	include('head.php');
	include('nav.php');
	include('conn.php');
	include('readonly-conn.php');
?>
<div>

	<form class="Search" action="leermiddel-aantalcontracten.php" method="post">
	<p style="color: #e77443">Enter amount of past days you want to see contracts for:<br>
	  <input type="Reference" placeholder="days" name="Reference">
	  <button type="submit"><i class="fa fa-search"></i></button></p>
	</form>
<center>
	<table class="table" id="table">
		<thead class="thead-dark">
			 <tr>
				<th scope="col">Contract</th>
				<th scope="col">Voornaam</th>
				<th scope="col">Naam</th>
				<th scope="col">Label</th>
				<th scope="col">Ouder</th>
				<th scope="col">Email</th>
				<th scope="col">Voorschot</th>
				<th scope="col">Getekend</th>
				<th scope="col" width=80 align=right><font color=#e77443>Levering</th>
				<th scope="col">HoogsteDatum</th>
			</tr>
		</thead>
		<tbody>

    <?php
    $tekstweergave = "Please enter a viable number of days.";

	if(!empty($_POST))
    	{
			$days =$_POST['Reference'];
			$a = $days-1;

		$sql = "SELECT
    					ContractVolgnummer as Contract, VoornaamLeerling as Voornaam, NaamLeerling as Naam, instruction as Label, NaamOuder as Ouder, Email1 as Email, DatumVoorschotOntvangen as Voorschot, DatumContractOntvangen as Getekend, CONCAT('<B><font color=#eb6421>',lengte) as Levering, CAST(greatest(DatumVoorschotOntvangen, DatumContractOntvangen) as date) AS HoogsteDatum
				FROM
    					leermiddel.tblcontractdetails
				WHERE
								((VoorschotOntvangen = '1' AND ContractOntvangen = '1' AND deleted = '0')
						AND
								((DatumContractOntvangen BETWEEN CURDATE() - INTERVAL '$a' DAY AND CURDATE())
						OR
								(DatumVoorschotOntvangen BETWEEN CURDATE() - INTERVAL '$a' DAY AND CURDATE())))
						ORDER BY
								HoogsteDatum Desc";

                $totaal = 0;
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo '<tr>';
				echo '<td>' . $row['Contract'] . '</td>';
				echo '<td>' . $row['Voornaam'] . '</td>';
				echo '<td>' . $row['Naam'] . '</td>';
				echo '<td>' . $row['Label'] . '</td>';
				echo '<td>' . $row['Ouder'] . '</td>';
				echo '<td>' . $row['Email'] . '</td>';
				echo '<td>' . $row['Voorschot'] . '</td>';
				echo '<td>' . $row['Getekend'] . '</td>';
        echo '<td>' . $row['Levering'] . '</td>';
				echo '<td>' . $row['HoogsteDatum'] . '</td>';
				echo '</tr>';
				$totaal+=1;
			}
		}

		switch (true) {
			case $days == 1:
				$tekstweergave = "$totaal verified contracts today.";
				break;
		case  $days > 1:
				$tekstweergave = "$totaal verified contracts since last $days days.";
        break;
		}
	}
  echo "<h4 align=left><font color=#00ADBD>$tekstweergave<font size=3 color=grey>";
?>
		</tbody>
	</table>
</div>
<?php
include('footer2.php');
?>
