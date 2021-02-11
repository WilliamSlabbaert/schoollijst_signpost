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

<div class="body">
    <form class="Search" action="leermiddel-contracten.php" method="post">
	  <p style="color:#00ADBD"><B>Enter name, label, contractnumber</B> :
	  <input type="Reference" placeholder="Reference..." name="Reference">
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
  				<th scope="col">Synergy</th>
  				<th scope="col">Voorschot</th>
  				<th scope="col">Getekend</th>
  				<th scope="col" width=80 align=right><font color=#e77443>Levering</th>
  			</tr>
  		</thead>
  		<tbody>
<?php
  $a = "";
  $tekstweergave = "Please enter a reference.";
	if(!empty($_POST))
    {
	   $a =$_POST['Reference'];

	$sql = "SELECT ContractVolgnummer as Contract, VoornaamLeerling as Voornaam, NaamLeerling as Naam, instruction as Label, NaamOuder as Ouder, Email1 as Email, SynergySchoolID as Synergy, DatumVoorschotOntvangen as Voorschot, DatumContractOntvangen as Getekend, CONCAT('<B><font color=#eb6421>',lengte) as Levering
			FROM leermiddel.tblcontractdetails
      INNER JOIN leermiddel.tblschool ON leermiddel.tblcontractdetails.SchoolID = leermiddel.tblschool.id
			WHERE
			(	(ContractVolgnummer LIKE '%$a%')
			OR	(VoornaamLeerling LIKE '%$a%')
			OR	(NaamLeerling LIKE '%$a%')
			OR	(instruction LIKE '%$a%'))";

		$result = $conn->query($sql);
    $totaal = 0;
    if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo '<tr>';
				echo '<td>' . $row['Contract'] . '</td>';
				echo '<td>' . $row['Voornaam'] . '</td>';
				echo '<td>' . $row['Naam'] . '</td>';
				echo '<td>' . $row['Label'] . '</td>';
				echo '<td>' . $row['Ouder'] . '</td>';
				echo '<td>' . $row['Email'] . '</td>';
				echo '<td>' . $row['Synergy'] . '</td>';
				echo '<td>' . $row['Voorschot'] . '</td>';
				echo '<td>' . $row['Getekend'] . '</td>';
				echo '<td>' . $row['Levering'] . '</td>';
				echo '</tr>';
				$totaal+=1;
			}
		}

    switch (true) {
		case  $totaal > 1:
				$tekstweergave = "$totaal contracts found with reference ";
        break;
	}
}
  echo "<h4 align=left><font color=#00ADBD>$tekstweergave<font color=#e77443>$a";
?>

    </tbody>
  </table>
</div>

<?php
include('footer2.php');
?>
