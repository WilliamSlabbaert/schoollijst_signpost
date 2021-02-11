<?php
require 'vendor/autoload.php';

$title = 'Mail verzenden';
include('head.php');
include('nav.php');
include('conn.php');

ini_set('SMTP', 'smtp-mail.outlook.com');
ini_set('smtp_port', 587);

//get value from URL
$guid = $_GET['q'];
$sql2 = "SELECT * FROM schools where guid = '".$_GET['q']."'";
$result2 = $conn->query($sql2);
if ($result2->num_rows > 0) {
	while($q = $result2->fetch_assoc()){
		$school_name = $q['school_name'];
		$city = $q['city'];
		$synergyid = $q['synergyid'];
		$guid = $q['guid'];
		//print_r($q);
	}
}

if(isset($_POST['submit'])){

	echo  $_POST['to'];
	$email = new \SendGrid\Mail\Mail();
	$email->setFrom("byod@signpost.eu", "Signpost BYOD");
	$email->setSubject("Het imagen van uw BYOD-2020-laptops: 3 2 1 : START NU");
	$email->addTo($_POST['to']);
	// $email->addCc("nova@signpost.eu");
	$email->addContent(
		"text/plain", $_POST['message']
	);
	$sendgrid = new \SendGrid('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');
	try {
		$response = $sendgrid->send($email);
		print $response->statusCode() . "\n";
		print_r($response->headers());
		print $response->body() . "\n";
		echo $_POST['guid'];

		//update
		$sql = "UPDATE schools SET intake_sent='1', intake_sent_on='" . date("d/m/Y H:m") . "' WHERE guid = '" . $_POST['guid'] . "'";

		if ($conn->query($sql) === TRUE) {
			echo "test";
		} else {
			echo "Error updating record: " . $conn->error;
		}

		//$conn->close();


	} catch (Exception $e) {
		echo 'Caught exception: '. $e->getMessage() ."\n";
	}

}
?>

<div class="body">

	<h3>Mail verzenden</h3>
	<form method="POST" action="mail-verzenden.php">

<?php
$sql = "SELECT * FROM schools where guid  = '".$guid."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

	while($row = $result->fetch_assoc()) {

		if ($row['CampagneJaar'] == '2019') {

			$message="Beste

				BYOD-2020 komt er (opnieuw) aan en wij danken u van harte voor het vernieuwde vertrouwen in Signpost voor uw BYOD-project dit jaar.

				Het imagen van jullie bestelde laptops (voor BYOD2020 en andere projecten) gebeurt dit jaar opnieuw met alle aandacht voor kwaliteit en leveringsstiptheid.

				In de flow die we hiervoor ontwikkelden, zitten daarom diverse interne en externe checks en kwaliteitscontroles. Dat neemt wat tijd, maar de doelstelling is om tegen eind juni (streefdatum) een goedgekeurde en foutloze image op maat voor u klaar te hebben. Die kunnen we dan deze zomer uitrollen, perfect zoals jullie die verwachten.

				Ook dit jaar stellen wij 2 vrij complete image-packs voor vanuit Signpost, die wij u ten zeerste aanraden.

				Maar het staat u vrij toch nog aanvullingen of andere software in uw image op te nemen om deze naar uw wensen aan te passen, mocht u dat echt wensen.

				Gelieve de volgende stappen te nemen om snel tot een goed resultaat te komen:

				Via onderstaande link kan u de formulieren die in het verleden werden geregistreerd raadplegen.
				https://orders.signpost.site/image-replies.php?guid=".$row['guid']."

				Mogen we u vragen om per image deze ofwel goed of af te keuren. Indien u alsnog een nieuwe image wenst, dan zal deze optie verschijnen na het afkeuren van uw oude image intake

				Opgelet: Per benodigde image moet er 1 formulier worden ingevuld.

				Ook als u zelf de image maakt, vragen we u het formulier in te vullen om alle misverstanden te vermijden

				Na het invullen van de form krijgt u ook nog een bevestigingsmail toegestuurd met een link om deze te bevestigen. Gelieve deze zeker aan te klikken, zodra wij we bevestiging ontvangen, gaat het software-team aan de slag.

				OVER DE DOLLE IMAGE-ZOMER-DONDERDAGEN:

				Om het opmaken van de image helemaal vlot te laten verlopen starten we na de corona-lockdown ook opnieuw met de befaamde image-zomer-dolle-donderdagen !

				Koppel het aangename aan het nuttige: wij ontvangen u dan graag in Lokeren op een donderdag naar keuze.

				In een ontspannen zomerse sfeer: ‘s middags voorzien we food & verkoelende drinks in ons café, café-spelen inbegrepen…

				Intussen maken we samen met u persoonlijk, snel en efficiënt, uw image klaar en controleren we die onmiddellijk in uw aanwezigheid.

				We houden u op de hoogte zodra deze opnieuw starten.

				Bijkomende vragen of onduidelijkheden? Aarzel niet ons te contacteren! softwaresupport@signpost.eu

				Telefoonnummer: 03 500 49 28

				Bedankt!

				Het Signpost Team
			";

		} elseif ($row['CampagneJaar'] =='2020') {

			$message = "Beste

				BYOD-2020 komt er aan en wij danken u van harte voor het vertrouwen in Signpost voor uw BYOD-project dit jaar.

				Het imagen van jullie bestelde laptops (voor BYOD2020 en andere projecten) gebeurt met volle aandacht voor kwaliteit en leveringsstiptheid.

				In de flow die we hiervoor ontwikkelden, zitten daarom diverse interne en externe checks en kwaliteitscontroles. Dat neemt wat tijd, maar de doelstelling is om tegen eind juni (streefdatum) een goedgekeurde en foutloze image op maat voor u klaar te hebben. Die kunnen we dan deze zomer uitrollen, perfect zoals jullie die verwachten.

				Wij stellen u graag 2 vrij complete image-packs voor vanuit Signpost, die wij u ten zeerste aanraden.

				Maar het staat u vrij toch nog aanvullingen of andere software in uw image op te nemen om deze naar uw wensen aan te passen, mocht u dat echt wensen.

				Gelieve de volgende stappen te nemen om snel tot een goed resultaat te komen:

				Alles start uiteraard met een gedocumenteerde navraag naar jullie image-wensen. Die wensen mag u hier kenbaar maken:
				https://orders.signpost.site/image-form.php?q=".$row['guid']."

				Opgelet: Per benodigde image moet er 1 formulier worden ingevuld.

				Ook als u zelf de image maakt, vragen we u het formulier in te vullen om alle misverstanden te vermijden .

				Na het invullen van de form krijgt u ook nog een bevestigingsmail toegestuurd met een link om deze te bevestigen. Gelieve deze zeker aan te klikken, zodra wij we bevestiging ontvangen, gaat het software-team aan de slag.

				Bijkomende vragen of onduidelijkheden? Aarzel niet ons te contacteren! softwaresupport@signpost.eu

				Telefoonnummer: 03 500 49 28

				Bedankt!

				Het Signpost Team .
			";
		}
	}

} else {

	echo "0 results";

}

//$conn->close();


//echo 'Onderstaand bericht wordt verzonden naar '. $to .' met ' .$cc.  ' in cc';
echo '<hr>';
//echo $message;
?>
<label for="to">Aan:</label>

<select class="form-control ContactSelect" name="to" style="" required>
<?php
include('mssql-conn.php');


$tsql= "select cicntp.ID, cicntp.cnt_email, cicntp.FullName, cicntp.cnt_f_tel, cicntp.cnt_f_mobile,  cicmpy.cmp_name, opportunities.code, ltrim(cicmpy.cmp_code) as klantnummer from OpportunityContacts
	inner join cicntp on cicntp.cnt_id=OpportunityContacts.contactid inner join opportunities on opportunities.id=OpportunityID inner join cicmpy on cicmpy.cmp_wwn=OpportunityContacts.AccountID where  RoleID in (select id from OpportunityRoles where Description like '%image%')
	and cicmpy.cmp_code like '% ".$synergyid."'";
$getResults= sqlsrv_query($msconn, $tsql);


if ($getResults == FALSE){
	die(FormatErrors(sqlsrv_errors()));
}

while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
	echo "<option value='" . $row['cnt_email'] . "'>" .  $row['cnt_f_name']  . " " .  $row['cnt_l_name']  . " - "  . $row['cnt_email']  ."</option>";
}

sqlsrv_free_stmt($getResults);
?>
</select>
<br>

<?php
$sql = "SELECT guid, synergyidold AS oldsynergyid,
	synergyid AS newsynergyid,
	( SELECT COUNT(*) FROM images2019 WHERE synergyid = oldsynergyid AND okvoor2020 = '1' ) AS 'goedgekeurd2019',
	( SELECT COUNT(*) FROM images2019 WHERE synergyid = oldsynergyid AND okvoor2020 = '-1' ) AS 'afgekeurd2019',
	( SELECT COUNT(*) FROM images2019 WHERE synergyid = oldsynergyid AND okvoor2020 IS NULL ) AS 'geenbeoordeling2019',
	( SELECT COUNT(*) FROM images2020 WHERE synergyid = newsynergyid AND confirmed = '1' ) AS 'goedgekeurd2020',
	( SELECT COUNT(*) FROM images2020 WHERE synergyid = newsynergyid AND confirmed = '-1' ) AS 'afgekeurd2020',
	( SELECT COUNT(*) FROM images2020 WHERE synergyid = newsynergyid AND confirmed IS NULL ) AS 'geenbeoordeling2020'
	FROM `byod-orders`.schools
	WHERE guid = '" . $_GET['q'] . "'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "Images 2019:<br> Goedgekeurd: " . $row["goedgekeurd2019"]. " - Afgekeurd: " . $row["afgekeurd2019"]. " - Geen beoordeling: " . $row["geenbeoordeling2019"]. "<br>";
		echo "Images 2020:<br> Goedgekeurd: " . $row["goedgekeurd2020"]. " - Afgekeurd: " . $row["afgekeurd2020"]. " - Geen beoordeling: " . $row["geenbeoordeling2020"]. "<br>";
	}
} else {
	echo "0 results";
}
?>
		<br>
		<button type="button" onclick="changetext(2019)">2019 Template - Beoordelen van oude images</button>
		<button type="button" onclick="changetext(2020)">2020 Template - Nieuwe intake doorsturen</button><br>
		<label for="message">Bericht:</label><br>
		<textarea name="message" id="message" style="width:900px;height:500px">
		<?php echo $message; ?>
		</textarea><br><br>
		<input name="guid" id="guid" type="text" style="display:none" value="<?php echo $guid?>">
		<input type="submit" value="Verzenden" name="submit">
	</form>

</div>

<script>

function changetext(x){
	var message;
	var guid = document.getElementById('guid').value;

	if(x==2019){
		message = `Beste

			BYOD-2020 komt er (opnieuw) aan en wij danken u van harte voor het vernieuwde vertrouwen in Signpost voor uw BYOD-project dit jaar.

			Het imagen van jullie bestelde laptops (voor BYOD2020 en andere projecten) gebeurt dit jaar opnieuw met alle aandacht voor kwaliteit en leveringsstiptheid.

			In de flow die we hiervoor ontwikkelden, zitten daarom diverse interne en externe checks en kwaliteitscontroles. Dat neemt wat tijd, maar de doelstelling is om tegen eind juni (streefdatum) een goedgekeurde en foutloze image op maat voor u klaar te hebben. Die kunnen we dan deze zomer uitrollen, perfect zoals jullie die verwachten.

			Ook dit jaar stellen wij 2 vrij complete image-packs voor vanuit Signpost, die wij u ten zeerste aanraden.

			Maar het staat u vrij toch nog aanvullingen of andere software in uw image op te nemen om deze naar uw wensen aan te passen, mocht u dat echt wensen.

			Gelieve de volgende stappen te nemen om snel tot een goed resultaat te komen:

			Via onderstaande link kan u de formulieren die in het verleden werden geregistreerd raadplegen.
			https://orders.signpost.site/image-replies.php?guid=${guid}

		Mogen we u vragen om per image deze ofwel goed of af te keuren. Indien u alsnog een nieuwe image wenst, dan zal deze optie verschijnen na het afkeuren van uw oude image intake

			Opgelet: Per benodigde image moet er 1 formulier worden ingevuld.

			Ook als u zelf de image maakt, vragen we u het formulier in te vullen om alle misverstanden te vermijden

			Na het invullen van de form krijgt u ook nog een bevestigingsmail toegestuurd met een link om deze te bevestigen. Gelieve deze zeker aan te klikken, zodra wij we bevestiging ontvangen, gaat het software-team aan de slag.

			OVER DE DOLLE IMAGE-ZOMER-DONDERDAGEN:

			Om het opmaken van de image helemaal vlot te laten verlopen starten we na de corona-lockdown ook opnieuw met de befaamde image-zomer-dolle-donderdagen !

			Koppel het aangename aan het nuttige: wij ontvangen u dan graag in Lokeren op een donderdag naar keuze.

			In een ontspannen zomerse sfeer: ‘s middags voorzien we food & verkoelende drinks in ons café, café-spelen inbegrepen…

			Intussen maken we samen met u persoonlijk, snel en efficiënt, uw image klaar en controleren we die onmiddellijk in uw aanwezigheid.

			We houden u op de hoogte zodra deze opnieuw starten.

			Bijkomende vragen of onduidelijkheden? Aarzel niet ons te contacteren! softwaresupport@signpost.eu

			Telefoonnummer: 03 500 49 28

			Bedankt!

			Het Signpost Team`;
	}
	if(x == 2020){
		message = `Beste

			BYOD-2020 komt er aan en wij danken u van harte voor het vertrouwen in Signpost voor uw BYOD-project dit jaar.

			Het imagen van jullie bestelde laptops (voor BYOD2020 en andere projecten) gebeurt met volle aandacht voor kwaliteit en leveringsstiptheid.

			In de flow die we hiervoor ontwikkelden, zitten daarom diverse interne en externe checks en kwaliteitscontroles. Dat neemt wat tijd, maar de doelstelling is om tegen eind juni (streefdatum) een goedgekeurde en foutloze image op maat voor u klaar te hebben. Die kunnen we dan deze zomer uitrollen, perfect zoals jullie die verwachten.

			Wij stellen u graag 2 vrij complete image-packs voor vanuit Signpost, die wij u ten zeerste aanraden.

			Maar het staat u vrij toch nog aanvullingen of andere software in uw image op te nemen om deze naar uw wensen aan te passen, mocht u dat echt wensen.

			Gelieve de volgende stappen te nemen om snel tot een goed resultaat te komen:

			Alles start uiteraard met een gedocumenteerde navraag naar jullie image-wensen. Die wensen mag u hier kenbaar maken:
			https://orders.signpost.site/image-form.php?q=${guid}

		Opgelet: Per benodigde image moet er 1 formulier worden ingevuld.

			Ook als u zelf de image maakt, vragen we u het formulier in te vullen om alle misverstanden te vermijden .

			Na het invullen van de form krijgt u ook nog een bevestigingsmail toegestuurd met een link om deze te bevestigen. Gelieve deze zeker aan te klikken, zodra wij we bevestiging ontvangen, gaat het software-team aan de slag.

			Bijkomende vragen of onduidelijkheden? Aarzel niet ons te contacteren! softwaresupport@signpost.eu

			Telefoonnummer: 03 500 49 28

			Bedankt!

			Het Signpost Team .`;
	}

	document.getElementById('message').value = message
}

</script>

<?php
include('footer.php');
?>
