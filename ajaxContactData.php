<?php
// Include the database config file
//manufacturer - model - motherboard - memory - ssd - panel
include_once 'mssql-conn.php';

if(!empty($_POST["contactid"])){
	// Fetch state data based on the specific country

	//where contactid
	$tsql= "select * from cicntp where ID like '".$_POST["contactid"]."'";
	$getResults= sqlsrv_query($msconn, $tsql);


	if ($getResults == FALSE){
		die(FormatErrors(sqlsrv_errors()));
	}

	while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
		echo '<div class="form-group mb-4">
			<h5 for="contactnaam">Contactpersoon Image*</h5>
			<small class="form-text pb-1">Wie kunnen wij contacteren met technische vragen i.v.m. het imagen van de toestellen? (ICT-Coördinator)</small>
			<input type="text" name="contactnaam" required class="form-control verify" id="contactnaam"  placeholder="" value="'.$row['FullName'].'">
			</div>

			<div class="form-group mb-4">
			<h5 for="contacttel">Contactpersoon Image: Telefoonnummer*</h5>
			<small class="form-text pb-1">Op welk telefoonnummer is de technische contactpersoon bereikbaar?</small>
			<input type="text" name="contacttel" required class="form-control verify" id="contacttel"  placeholder="" value="'.$row['cnt_f_tel'].'">
			</div>

			<div class="form-group mb-4">
			<h5 for="contactemail">Contactpersoon Image: E-mailadres*</h5>
			<small class="form-text pb-1">Op welk e-mailadres is de technische contactpersoon bereikbaar?</small>
			<input type="email" name="contactemail" required class="form-control verify" id="contactemail"  placeholder="" value="'.$row['cnt_email'].'">
			</div>';


	}

}

?>
