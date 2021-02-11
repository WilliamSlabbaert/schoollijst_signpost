<?php
include_once 'conn.php';

if(!empty($_POST["synergyid"])){

	echo '<option value="" selected disabled></option><option value="idk">Ik weet het niet</option><option value="chrome">Chromebook</option><option value="fabriek">OOBE Fabriek (Lenovo/HP Incl. bloatware)</option><option value="clean">Clean Windows 10</option><option value="nieuw">Nieuwe nog te maken Signpost Image</option>';

	$sql = "SELECT *, images2019.id as imageid FROM images2019
		LEFT JOIN schools ON schools.synergyidold = images2019.synergyid
		WHERE schools.synergyid = '" . $_POST['synergyid'] . "' AND status2020 = 'done'
		GROUP BY images2019.id";
	$result2 = $conn->query($sql);
	if ($result2->num_rows > 0) {
		while($row2 = $result2->fetch_assoc()) {
			echo "<option value=" .  $row2['imageid'] . ">" .  $row2['toestel2020'] . " - " .  $row2['ImageNaam'] . " (id: " .  $row2['imageid'] . ")</option>";
		}
	} else {
		//echo "Er bestaan nog geen images voor deze school<br>";
	}

	$sql = "SELECT *,
		( SELECT CONCAT(synergyid, '-', spsku, '-V', version, '-', NAME) FROM `byod-orders`.images2020 WHERE id = q.id ) AS imagename2020
		FROM images2020 q
		WHERE (synergyid = '" . $_POST['synergyid'] . "' OR (synergyid = '666' AND SPSKU LIKE '" . substr($_POST['spsku'], '0', '11') . "%')) AND status = 'done'";
	$result3 = $conn->query($sql);
	if ($result3->num_rows > 0) {
		while($row3 = $result3->fetch_assoc()) {
			echo "<option value=" .  $row3['id'] . ">" .  $row3['imagename2020'] . " (id: " .  $row3['id'] . ")</option>";
		}
	} else {
		//echo "Er bestaan nog geen images voor deze school<br>";
	}

}

?>
