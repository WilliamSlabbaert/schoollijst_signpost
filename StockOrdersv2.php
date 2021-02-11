<?php

$title = 'PVT - alle open Stockorders';

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
			echo "<tr>";
			$rij=0;
			foreach ($rows as $row) {
				$rij++;
				if(($rij >4) && ($rij<8)){
					echo "<td align=right>".$row.'</td>';
				} else {
					if($rij===4){
						echo "<td data-sort='".$volgnr."'>".$row.'</td>';
					}else {
						echo "<td>".$row.'</td>';
					}
				}
			}
			echo "</tr>";
			$volgnr++;
			}
		return;
	}


	$tsql= "SET LANGUAGE DUTCH";
	$stmt = sqlsrv_query( $msconn, $tsql);
	
	$tsql=	"SELECT orkrg.ord_debtor_name, CONCAT('<B><font color=#eb6421>',orkrg.ordernr) as OrderNR, CONCAT('<I>',orkrg.refer) as Referentie, CONCAT('<center>',format(orkrg.orddat,'dd MMM yy')) as datum,CONCAT('<B><center>',esr_aantal,' x'), CONCAT(orsrg.artcode,'<font color=grey>&nbsp&nbsp&nbsp ',orsrg.oms45), instruction as ETA as Toestel FROM orkrg with (nolock) INNER JOIN cicmpy with (nolock) ON orkrg.crdnr = cicmpy.crdnr LEFT JOIN orsrg with (nolock) on orkrg.ordernr = orsrg.ordernr WHERE ( orkrg.ord_soort = 'B' AND orkrg.afgehandld = 0 AND orkrg.selcode LIKE 'SO%' and artcode<>'DTXT' and aant_gelev =0) ORDER BY orkrg.ordernr DESC";

	$tsql=	"SELECT (orkrg.ord_debtor_name) as Distri, CONCAT('<B><font color=#eb6421>',orkrg.ordernr) as OrderNR, CONCAT('<I>',orkrg.refer) as Referentie, CONCAT('<center>',format(orkrg.orddat,'dd MMM yy')) as Datum,concat('<B>',sum(esr_aantal)) as Totaal,concat('<font color=tomato><B>',sum(aant_gelev)) as Geleverd,concat('<b><font color=green>',(sum(esr_aantal) -sum(aant_gelev) ))as Beschikbaar , CONCAT(orsrg.artcode,'<font color=grey>&nbsp&nbsp&nbsp ',orsrg.oms45) as Toestel, orsrg.instruction as ETA FROM orkrg with (nolock) INNER JOIN cicmpy with (nolock) ON orkrg.crdnr = cicmpy.crdnr LEFT JOIN orsrg with (nolock) on orkrg.ordernr = orsrg.ordernr WHERE ( orkrg.ord_soort = 'B' AND orkrg.afgehandld = 0 AND orkrg.selcode LIKE 'SO%' and artcode<>'DTXT') group by orkrg.ord_debtor_name,orkrg.refer,orkrg.orddat,orsrg.artcode,orsrg.oms45,orkrg.ordernr,orsrg.instruction ORDER BY orkrg.ordernr DESC";

	$stmt = sqlsrv_query( $msconn, $tsql);
	
	echo "<h4 align=left><font color=#00ADBD>&nbsp&nbsp&nbspAlle open STOCK orders (Exact)</H4>";
	echo "<table class='table' id='table'><thead class='thead-dark'><tr>";
	echo "<th scope='col'>Distributeur</th>";
	echo "<th scope='col' width=120><font color=#eb6421>Order</th>";
	echo "<th scope='col'>Referentie</th>";
	echo "<th scope='col' width=100>Datum</th>";
	echo "<th scope='col' width=80 align=right>Totaal</th>";
	echo "<th scope='col' width=80 align=right><font color=tomato>Geleverd</th>";
	echo "<th scope='col' width=80 align=right><font color=green>Afroepbaar</th>";
	echo "<th scope='col'>Toestel</th>";
	echo "<th scope='col' width=80 align=right>ETA</th>";
	echo "</tr><tbody>";
	createTable2($stmt);
	echo "</tbody></table>";

?>

</div>

<?php
include('footer2.php');
?>
