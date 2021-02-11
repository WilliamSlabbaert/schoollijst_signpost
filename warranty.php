<?php

$title = 'Warranty check';
include('head.php');
include('nav.php');

?>

<div class="body">

<?php
if(isset($_POST['type']) == true){

	//
	// curl --request POST \
	//      --url 'https://supportapi.lenovo.com/v2.5/warranty' \
	//      --header 'ClientID: okknb8cVKwLtBAKn46b5LQ==' \
	//      --header 'Content-Type: application/x-www-form-urlencoded' \
	//      --data Serial=R90XM4RS
	//

	function callAPI($method, $url, $data){
		$curl = curl_init();
		switch ($method){
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		case "PUT":
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		default:
			if ($data)
				$url = sprintf("%s?%s", $url, http_build_query($data));
		}
		// OPTIONS:
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'ClientID: okknb8cVKwLtBAKn46b5LQ==',
			'Content-Type: application/x-www-form-urlencoded',
		));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		// EXECUTE:
		$result = curl_exec($curl);
		if(!$result){die("Connection Failure");}
		curl_close($curl);
		return $result;
	}

	$serials = ltrim(str_replace(',S', ',', preg_replace('/\s\s+/', ',',$_POST['serials'])), 'S');
	$result = json_decode(callAPI('POST', 'https://supportapi.lenovo.com/v2.5/warranty', 'Serial='.$serials));
	//print_r($result);

	echo '<table class="table table-striped">';
	echo '<thead>
		<td>Serial</td>
		<td>1YR Battery (On-site Warranty)</td>
		<td>1Y Depot</td>
		<td>3Y On-site</td>
		<td>3YR Sealed Battery On-site</td>
		<td>3Y Depot</td>
		<td>5Y On-site</td>
	</thead>';

	foreach($result as $computer){
		//print_r($computer);
		$oneybatt = 'n.v.t.';
		$oneyd = 'n.v.t.';
		$threey = 'n.v.t.';
		$threeyd = 'n.v.t.';
		$threeybatt = 'n.v.t.';
		$fivey = 'n.v.t.';

		for ($x = 0; $x <= 5; $x++) {
			if(isset($computer->Warranty[$x]->End) == true){
				if(strpos($computer->Warranty[$x]->Name, '1YR Battery') !== false){
					$oneybatt = date('d-m-Y', strtotime($computer->Warranty[$x]->End));
				} elseif(strpos($computer->Warranty[$x]->Name, '1Y Depot') !== false){
					$oneyd = date('d-m-Y', strtotime($computer->Warranty[$x]->End));
				} elseif(strpos($computer->Warranty[$x]->Name, '3Y On-site') !== false){
					$threey = date('d-m-Y', strtotime($computer->Warranty[$x]->End));
				} elseif(strpos($computer->Warranty[$x]->Name, '3YR Sealed Battery On-site') !== false){
					$threeybatt = date('d-m-Y', strtotime($computer->Warranty[$x]->End));
				} elseif(strpos($computer->Warranty[$x]->Name, '3Y Depot') !== false){
					$threeyd = date('d-m-Y', strtotime($computer->Warranty[$x]->End));
				} elseif(strpos($computer->Warranty[$x]->Name, '5Y On-site') !== false){
					$fivey = date('d-m-Y', strtotime($computer->Warranty[$x]->End));
				}
			}
		}
		echo '<tr>
			<td>' . $computer->Serial . '</td>
			<td>' . $oneybatt . '</td>
			<td>' . $oneyd . '</td>
			<td>' . $threey . '</td>
			<td>' . $threeybatt . '</td>
			<td>' . $threeyd . '</td>
			<td>' . $fivey . '</td>
		</tr>';
	}
	echo '</table>';

	die();
}

?>


<h3>Zoek naar garantieinfo</h3>
<form action="warranty.php" method="post">
	<label for="type">Type:</label><br>
	<select class="form-control" name="type" id="type">
		<option value="lenovo">Lenovo</option>
	</select><br>
	<label for="serials">Serienummers:</label><br>
	<textarea rows="10" placeholder="Serienummer1&#x0a;Serienummer2&#x0a;..." type="text" id="serials" name="serials" class="form-control"></textarea><br>
	<input type="submit" value="Submit" class="btn btn-primary">
</form>


</div>

<?php
include('footer.php');
?>
