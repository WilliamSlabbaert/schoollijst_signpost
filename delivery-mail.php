<?php
require 'vendor/autoload.php';

$title = 'Mail verzenden';
include('head.php');
include('nav.php');
include('conn.php');

ini_set('SMTP', 'smtp-mail.outlook.com');
ini_set('smtp_port', 587);

if(isset($_POST['submit'])){

	$email = new \SendGrid\Mail\Mail();
	$email->setFrom("byod@signpost.eu", "Signpost BYOD");
	$email->setSubject("Details van levering SP-BYOD20-".$_POST['id']);
	$email->addTo($_POST['to']);
	$email->addCc($_POST['sales']);
	$email->addCc("byod@signpost.eu");
	$email->addContent(
		"text/plain", $_POST['message']
	);
	$sendgrid = new \SendGrid('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');
	try {
		$response = $sendgrid->send($email);
		echo 'Mail succesvol verzonden';
	} catch (Exception $e) {
		echo 'Caught exception: '. $e->getMessage() ."\n";
	}
	die();

}
?>

<div class="body">

	<h3>Levering info versturen</h3>
	<form method="POST" action="delivery-mail.php">

	<?php
	$synergyid = 0;
	$orderid = 0;
	$sql = "SELECT *, orders.id AS orderid, orders.shipping_postcode, orders.shipping_date,
		orders.shipping_street, orders.shipping_number, orders.shipping_city
		FROM orders
		LEFT JOIN delivery ON delivery.orderid = orders.id
		WHERE delivery.id = '" . $_POST['id'] . "' AND orders.deleted != 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$synergyid = $row['synergyid'];
			$orderid = $row['orderid'];
			$sales = $row['sales'];
			$message = 'Beste

				Uw nalevering van order SP-BYOD20-' . $orderid . ' wordt momenteel zorgvuldig klaargezet voor verzending.
				Deze zending wordt verwacht op ' . $row['shipping_date'] . ' en zal geleverd worden op ' . $row['shipping_street'] . ' ' . $row['shipping_number'] . ' - ' . $row['shipping_postcode'] . ' ' . $row['shipping_city'] . '.
				De toestellen zullen bestemd zijn voor volgende leerlingen en/of leerkrachten:

				' . str_replace('<br>', "
				", $_POST['data']) . '
				Alvast een fijne dag gewenst.

				Met vriendelijke groeten
				Het Signpost Team';
			$message = str_replace('	', '', $message);

		}
	} else {
		echo "0 results";
	}

	echo '<hr>';
	?>

	<input type="text" value='<?php echo $orderid; ?>' name="id" hidden>

	<label for="to">Aan:</label>
	<select class="form-control ContactSelect" name="to" required>
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
		echo "<option value='" . $row['cnt_email'] . "'>" .  $row['FullName']  . " - "  . $row['cnt_email']  ."</option>";
	}

	sqlsrv_free_stmt($getResults);
	?>
	</select>
	<br>

	<label for="sales">Sales:</label>
	<input type="text" value='<?php echo $sales; ?>@signpost.eu' name="sales" class="form-control"><br>

	<label for="message">Bericht:</label><br>
	<textarea name="message" id="message" style="width:900px;height:500px"><?php echo $message; ?></textarea><br><br>
	<input type="submit" value="Verzenden" name="submit" class="btn btn-primary">

</form>

</div>

<?php
include('footer.php');
?>
