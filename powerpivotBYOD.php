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
<?php

$title = 'PowerPivot BYOD';

include('head.php');
include('nav.php');
include('readonly-conn.php');
include('readonly-mssql-100-conn.php');


?>

<div class="body">
    <form class="Search" action="powerpivotBYOD.php" method="post">
	Enter the <B>reference</B> for the order :
	  <input type="Reference" placeholder="Reference..." value="<?php echo $_POST['Reference']; ?>" name="Reference">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</form>

<?php

    if(!empty($_POST))
    {
	   $a =$_POST['Reference'];



        $sql = "SELECT
						BestelKlant,
						OrderNummer as ExactOrder,
						instruction as Label,
						referentie as MagentoOrder,
						referklant,
						FactuurNummers,
						lengte as levering,
						breedte,
						CAST(OrderDatum AS VARCHAR(11)) as Datum
				FROM
						_pl_all
					WITH (nolock) INNER JOIN orsrg
					WITH (nolock) ON orsrg.ordernr=_pl_all.OrderNummer
					AND ar_soort IN ('T','V')
					AND not artcode='DOKO'
				WHERE
						referklant LIKE '%BYOD%'
				AND
						(BestelKlant LIKE '%$a%'
					OR	instruction LIKE '%$a%'
					OR	referentie LIKE '%$a%'
					OR	FactuurNummers LIKE '%$a%')
				ORDER BY
						OrderNummer desc";


	   $result = sqlsrv_query($msconn, $sql);
       echo createTable($result, 'exact');
//       echo $sql;
    }

?>

</div>

<?php
include('footer2.php');
?>
