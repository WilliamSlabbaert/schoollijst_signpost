<?php

$title = 'Leermiddel Contracten';

include('head.php');
include('nav.php');
include('conn.php');
include('readonly-conn.php');


?>

	<div class="body">
    <form class="Search" action="aantalcontracten.php" method="post">
	<B>Enter number of recent days to search completed contracts</B> :
	  <input type="Reference" placeholder="Days..." value="<?php echo $_POST['Reference']; ?>" name="Reference">
	  <button type="submit"><i class="fa fa-search"></i></button>
	</form>
    <?php
	if(!empty($_POST))
    {
	   $a =$_POST['Reference'];
		
	$sql = "SELECT
    COUNT(*) AS Contracten       
	FROM
    	leermiddel.tblcontractdetails
	WHERE
		((VoorschotOntvangen IS NOT NULL AND ContractOntvangen IS NOT NULL AND deleted = 0)
	AND
		(DatumContractOntvangen BETWEEN CURDATE() - INTERVAL '$a' DAY AND CURDATE())
	AND
		(DatumVoorschotOntvangen BETWEEN CURDATE() - INTERVAL '$a' DAY AND CURDATE()))";
		$result = $conn->query($sql);
       echo createTable($result, 'leermiddel');
//       echo $sql;
		
		$sql2 = "SELECT
    	ContractVolgnummer, VoornaamLeerling as Voornaam, NaamLeerling as Naam, instruction as Label, NaamOuder as Ouder1, NaamOuder2 as Ouder2, Email1 as Email, StartDatum, DatumVoorschotOntvangen, DatumContractOntvangen, lengte as Levering, greatest(DatumVoorschotOntvangen, DatumContractOntvangen) AS HoogsteDatum      
	FROM
    	leermiddel.tblcontractdetails
	WHERE
		((VoorschotOntvangen IS NOT NULL AND ContractOntvangen IS NOT NULL AND deleted = 0)
	AND
		(DatumContractOntvangen BETWEEN CURDATE() - INTERVAL '$a' DAY AND CURDATE())
	AND
		(DatumVoorschotOntvangen BETWEEN CURDATE() - INTERVAL '$a' DAY AND CURDATE()))
	GROUP BY HoogsteDatum Desc";
		$result2 = $conn->query($sql2);
       echo createTable($result2, 'leermiddel');
//       echo $sql2;
		
		
	}
?>
		
</div>

<?php
include('footer.php');
?>