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

.dataTables_paginate a:hover {background-color: #00ADBD30; color:white}

tr:hover{background-color: #00ADBD;}

.dataTables_length label {
    color: #00ADBD;
	padding: 0px 40px;
}
</style>
<?php

$title = 'PowerPivot';

include('head.php');
include('nav.php');
include('readonly-conn.php');
include('readonly-mssql-100-conn.php');

?>

<div class="body">
    <form class="Search" action="powerpivot.php" method="post">
	Enter the <B>reference</B> for the order :
	  <input type="Reference" placeholder="Reference..." value="<?php echo $_POST['Reference']; ?>" name="Reference">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</form>

<?php

    if(!empty($_POST))
    {
	   $a =$_POST['Reference'];



        $sql = "SELECT KlantNummer,
						OrderNummer,
						BestelKlant,
						FactuurKlant,
						CAST(OrderDatum AS VARCHAR(11)) as Dag,
						referklant,
							esr_aantal as aantal,
							artcode,
							oms45,
							aant_gelev,
							aant_fakt,
							reeds_fakt,
						AankoopOrders,
						Leveranciers,
						OpmerkingOrderIntern,
						offerte_nr,
						FactuurNummers,
						selcode,
						afgehandld,
						Kostdragers
					FROM _pl_all
					WITH (nolock) INNER JOIN orsrg
					WITH (nolock) ON orsrg.ordernr=_pl_all.OrderNummer
					AND ar_soort IN ('T','V')
					AND not artcode='DOKO'
					WHERE
						((referentie LIKE '%$a%')
					OR  (BestelKlant LIKE '%$a%')
					OR  (AankoopOrders LIKE '%$a%')
					OR  (Leveranciers LIKE '%$a%')
					OR  (FactuurNummers LIKE '%$a%')
					OR  (offerte_nr LIKE '%$a%')
					OR  (KlantNummer LIKE '%$a%')
					OR  (OrderNummer LIKE '%$a%')
					OR  (oms45 LIKE '%$a%')
					OR	(artcode LIKE '%$a%'))";


	   $result = sqlsrv_query($msconn, $sql);
       echo createTable($result, 'exact');
//       echo $sql;
    }

?>

</div>

<?php
include('footer2.php');
?>
