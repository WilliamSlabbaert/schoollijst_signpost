<?php

$title = 'PVT - DOKO group members';

include('head.php');
include('nav.php');
include('mssql-100-conn.php');

?>
<style>.dataTables_paginate a {
    color: #00ADBD;
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
		background-color: #00ADBD;
}

.dataTables_paginate a:hover {background-color: #00ADBD; color:white}

tr:hover{background-color: #00ADBD;}

.dataTables_length label {
    color: #00ADBD;
	padding: 0px 40px;
}
</style>
<div class="body">


<?php
	function createTable2($result) {
		$volgnr=0;
		while ($rows = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			if(true){
				echo "<tr>";
				$rij=0;
				foreach ($rows as $row) {
					$rij++;
					if ($rij===2){
						echo "<td align=right><font color=#eb6421><b>".$row.'</td>';
					} else{
						if ($rij===3){
							echo "<td align=left><font color=grey><b>".$row.'</td>';
						} else{
							echo "<td align=center>".$row.'</td>';
						}
					}
				}
				echo "</tr>";
			$volgnr++;
			}
		}
		return;
	}


	$tsql= "SET LANGUAGE DUTCH";
	$stmt = sqlsrv_query( $msconn, $tsql);
	
	$tsql=	"select TextField20 as Schoolbestuur, cmp_code as SynergyID, cmp_name as Naam,(SELECT sum(frhkrg.tot_bdr_vv) AS Amount FROM frhkrg with (nolock) WHERE frhkrg.fakdebnr =cmp_code AND frhkrg.fak_soort IN ('V','C','A','B','R') group by frhkrg.fakdebnr) as omzet from [dbo].[cicmpy] with (nolock) where TextField20 IS NOT NULL AND TextField20 <>'0' AND YesNoField4 = 1 ORDER by TextField20";
	
	echo $tsql;

	$stmt = sqlsrv_query( $msconn, $tsql);
	
	echo "<h4 align=left><font color=#00ADBD>&nbsp&nbsp&nbspDOKO scholen (Exact)   --- TEST TEST TEST --- </H4>";
	echo "<table class='table' id='table'><thead class='thead-dark'><tr>";
	echo "<th scope='col'>Schoolbestuur</th>";
	echo "<th scope='col' width=120><center><font color=#eb6421>SynergyID</th>";
	echo "<th scope='col'><center>Schoolnaam</th>";
	echo "<th scope='col'><center>Omzet (totaal)</th>";
	echo "</tr><tbody>";
	createTable2($stmt);
	echo "</tbody></table>";

?>

</div>

<?php
include('footer2.php');
?>
