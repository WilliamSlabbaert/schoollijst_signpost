<?php

$limit = ' Alles';
$limiter = '';
$limiturl = '';

if(isset($_GET['add']) == true){
	$title = 'Stock Scannen';
} else if(isset($_GET['limit']) == true){
	$limit = ' ' . $_GET['limit'];
	$limiturl = '&limit=' . $_GET['limit'];
	if($_GET['limit'] == 'Auto'){
		$limiter = " WHERE stock.warehouse LIKE '_-___-___%' ";
	} else {
		$limiter = " WHERE stock.warehouse = '" . strtoupper($_GET['limit']) . "' ";
	}
	$title = 'Stock' . $limit;
} else {
	$title = 'Stock';
}

include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

<?php
if(isset($_GET['add']) == true || isset($_GET['move']) == true){
?>

	<a href="stock.php" class="notOnMobile"><button class="btn btn-dark">Terug naar overzicht</button><br><br></a>

	<h2 class="notOnMobile">Toevoegen van stock</h2>

	<div class="split">
		<form action="stock.php" method="post">

			<input type="text" class="form-control" name="operation" value="add" hidden>

			<div class="form-group row">
				<label for="warehouse" class="col-2 col-form-label notOnMobile">Magazijn</label>
				<div class="col">
					<input id="warehouse" name="warehouse" type="text" class="form-control warehouse" placeholder="Magazijn" readonly>
				</div>
			</div>

			<div class="form-group row">
				<label for="location" class="col-2 col-form-label notOnMobile">Locatie</label>
				<div class="col">
					<input id="location" name="location" type="text" class="form-control location" placeholder="Locatie" readonly>
				</div>
			</div>

			<div class="form-group row">
				<label for="sku" class="col-2 col-form-label notOnMobile">SKU</label>
				<div class="col">
					<input id="sku" name="sku" type="text" class="form-control sku" placeholder="SKU" readonly>
				</div>
			</div>

			<div class="form-group row">
				<label for="serial" class="col-2 col-form-label notOnMobile">Scan</label>
				<div class="col">
					<input id="serial" name="serial" type="text" class="form-control sn" autofocus required>
				</div>
			</div>

		</form>
		<div class="results"></div>
	</div>

<?php
} elseif(isset($_GET['remove']) == true){
?>

	<a href="stock.php" class="notOnMobile"><button class="btn btn-dark">Terug naar overzicht</button><br><br></a>

	<h2 class="notOnMobile">Uit stock halen</h2>

	<div class="split">
		<form action="stock.php" method="post">

			<input type="text" class="form-control" name="operation" value="remove" hidden>

			<div class="form-group row">
				<label for="type" class="col-2 col-form-label notOnMobile">Reden</label>
				<div class="col">
					<input type="text" class="form-control warehouse warehouse_out" name="warehouse" placeholder="Reden" readonly>
				</div>
			</div>

			<div class="form-group row">
				<label for="serial" class="col-2 col-form-label notOnMobile">Scan</label>
				<div class="col">
					<input type="text" class="form-control sn_out" name="serial" id="serial" autofocus required>
				</div>
			</div>

		</form>
		<div class="results"></div>
	</div>

<?php
} else {
?>

	<?php

	if($limit == ' Bruikleen'){
		$sql = "SELECT from_warehouse AS 'Uit Magazijn', sku, label, serial, to_reason AS reden, pickedby, DATE_FORMAT(pickedon, \"%d/%m/%Y\") AS 'Uit stock gehaald op'
			FROM stock_out WHERE to_customer = 'BRUIKLEEN' AND returned IS NULL";
		echo '<h3>' . $limit . '</h3>';
	} elseif(isset($_GET['all']) == true){
		$sql = "SELECT CONCAT(stock.warehouse, '<br><span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock.warehouse AND warehouse_location = stock.location LIMIT 1), ''), '</span>') AS Magazijn, location AS location, SKU, label AS Label, serial AS Serienummer, IFNULL(updatedby, addedby) AS 'Updated By', DATE_FORMAT(updatedat, '%d %M %Y %H:%i') AS 'Updated on' FROM stock " . $limiter . " ORDER BY updatedat DESC";
		echo '<h3>' . $limit . ' - Alle data</h3>';
	} elseif(isset($_GET['before30-1']) == true){
		if($limiter == ''){
			$limiter = " WHERE stock.updatedat >= '2021-01-30 00:00:00' ";
		} else {
			$limiter .= " AND stock.updatedat >= '2021-01-30 00:00:00' ";
		}
		$sql = "SELECT CONCAT(stock.warehouse, '<br><span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock.warehouse AND warehouse_location = stock.location LIMIT 1), ''), '</span>') AS Magazijn, location AS location, SKU, label AS Label, serial AS Serienummer, IFNULL(updatedby, addedby) AS 'Updated By', DATE_FORMAT(updatedat, '%d %M %Y %H:%i') AS 'Updated on' FROM stock " . $limiter . " ORDER BY updatedat DESC";
		echo '<h3>' . $limit . ' - Voor 23-1</h3>';
	} elseif(isset($_GET['lastpicked']) == true){
		$sql = "SELECT CONCAT(stock_out.from_warehouse, '<br><span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock_out.from_warehouse AND warehouse_location = stock_out.from_location LIMIT 1), ''), '</span>') AS 'Uit Magazijn', from_location AS 'Uit locatie', SKU, label AS Label, SERIAL AS Serienummer, to_customer AS Reden, to_reason AS Beschrijving, pickedby AS 'Picked By', DATE_FORMAT(pickedon, '%d %M %Y %H:%i') AS 'Picked on' FROM stock_out WHERE pickedon >= DATE_ADD(CURDATE(), INTERVAL -3 DAY)" . str_replace('WHERE stock.warehouse', 'AND stock_out.from_warehouse', $limiter) . " ORDER BY pickedon DESC";
		echo '<h3>' . $limit . ' - Laatst gepickte toestellen (3 dagen)</h3>';
	} elseif(isset($_GET['lastadded']) == true){
		$sql = "SELECT CONCAT(stock.warehouse, '<br><span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock.warehouse AND warehouse_location = stock.location LIMIT 1), ''), '</span>') AS Magazijn, location AS location, SKU, label AS Label, serial AS Serienummer, IFNULL(updatedby, addedby) AS 'Updated By', DATE_FORMAT(updatedat, '%d %M %Y %H:%i') AS 'Updated on' FROM stock WHERE updatedat >= DATE_ADD(CURDATE(), INTERVAL -3 DAY)" . str_replace('WHERE', 'AND', $limiter) . " ORDER BY updatedat DESC";
		echo '<h3>' . $limit . ' - Laatste toegevoegde toestellen</h3>';
	} elseif(isset($_GET['persku']) == true){
		$sql = "SELECT SKU, IFNULL((SELECT description FROM exact_skus WHERE sku = SUBSTRING_INDEX(SUBSTRING_INDEX(q.sku, ';', 1), '-O', 1) LIMIT 1 ), '') AS Beschrijving, CONCAT('<div style=\"display:flex; justify-content: space-between;\">', GROUP_CONCAT(loc SEPARATOR '</div><div style=\"display:flex; justify-content: space-between;\">'), '</div>') AS Locaties
				FROM (
				SELECT sku,
				CONCAT('<div style=\"width:220px;\">', stock.warehouse, ' <span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock.warehouse AND warehouse_location = stock.location LIMIT 1), ''), '</span></div><div style=\"width:200px;\">', location, '</div><div style=\"width:50px;\">', COUNT(*), '</div>') AS loc
				FROM stock " . $limiter . "GROUP BY stock.warehouse, location, sku) q
				GROUP BY sku";
		echo '<h3>' . $limit . ' - Stock per sku</h3>';
	} elseif(isset($_GET['perlabel']) == true){
		$sql = "SELECT labeltype, sku, IFNULL(
				(SELECT description FROM exact_skus WHERE sku = SUBSTRING_INDEX(SUBSTRING_INDEX(q.sku, ';', 1), '-O', 1) LIMIT 1 ), '') AS Beschrijving,
				CONCAT('<div style=\"display:flex; justify-content: space-between;\">', GROUP_CONCAT(loc SEPARATOR '</div><div style=\"display:flex; justify-content: space-between;\">'), '</div>') AS Locaties
				FROM (
				SELECT SUBSTRING_INDEX(label,'-',1) AS labeltype, sku,
				CONCAT('<div style=\"width:220px;\">', stock.warehouse, ' <span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock.warehouse AND warehouse_location = stock.location LIMIT 1), ''), '</span></div><div style=\"width:200px;\">', location, '</div><div style=\"width:50px;\">', COUNT(*), '</div>') AS loc
				FROM stock " . $limiter . "GROUP BY stock.warehouse, location, sku, SUBSTRING_INDEX(label,'-',1)) q
				WHERE labeltype != ''
				GROUP BY sku, labeltype
				ORDER BY labeltype";
		echo '<h3>' . $limit . ' - Stock per labeltype</h3>';
	} else {
		$sql = "SELECT warehouse AS Magazijn, location AS locatie, CONCAT('<div style=\"display:flex; justify-content: space-between;\">', GROUP_CONCAT(sku SEPARATOR '</div><div style=\"display:flex; justify-content: space-between;\">'), '</div>') AS Toestellen
				FROM (
				SELECT CONCAT(stock.warehouse, '<br><span class=\"smalltext\">', IFNULL((SELECT warehouse_name FROM warehouses WHERE warehouse = stock.warehouse AND warehouse_location = stock.location LIMIT 1), ''), '</span>') AS warehouse, location,
				CONCAT('<div style=\"width:200px;\">', sku, '</div><div style=\"width:410px;\">',
				IFNULL((SELECT description FROM exact_skus WHERE sku = SUBSTRING_INDEX(SUBSTRING_INDEX(stock.sku, ';', 1), '-O', 1) LIMIT 1 ), '')
				, '</div><div style=\"width:50px;\">', COUNT(*), '</div>') AS sku
				FROM stock " . $limiter . "GROUP BY stock.warehouse, location, sku) q
				GROUP BY warehouse, location";
		echo '<h3>' . $limit . ' - Stock per locatie</h3>';
	}
	

	if($limit != ' Bruikleen'){
		?>
		<br>
		<a href="stock.php?add<?php echo $limiturl; ?>"><button class="btn btn-success">Scannen</button></a><br><br>
		<a href="stock.php?all<?php echo $limiturl; ?>"><button class="btn btn-secondary">Alles</button></a>
		<a href="stock.php?persku<?php echo $limiturl; ?>"><button class="btn btn-secondary">Stock per sku</button></a>
		<a href="stock.php?perlabel<?php echo $limiturl; ?>"><button class="btn btn-secondary">Stock per label</button></a>
		<a href="stock.php?<?php echo $limiturl; ?>"><button class="btn btn-secondary">Stock per locatie</button></a>
		<a href="stock.php?lastpicked<?php echo $limiturl; ?>"><button class="btn btn-secondary">Laatste stock picks</button></a>
		<a href="stock.php?lastadded<?php echo $limiturl; ?>"><button class="btn btn-secondary">Laatste toegevoegde toestellen</button></a>
		<a href="stock.php?before23-1<?php echo $limiturl; ?>"><button class="btn btn-secondary">Voor 23-1</button></a>

		<div>
		<?php
	}

	$result = $conn->query($sql);
	echo createTable($result, 'mysql');

	?>
	</div>

<?php
}
?>

	<audio id="beep">
		<source src="https://freesound.org/data/previews/151/151779_2704059-lq.mp3" type="audio/mpeg">
		Your browser does not support the audio element.
	</audio>

	<audio id="errorbeep">
		<source src="https://freesound.org/data/previews/264/264823_2261906-lq.mp3" type="audio/mpeg">
		Your browser does not support the audio element.
	</audio>

	<audio id="ja">
		<source src="audio/ja.mp3" type="audio/mpeg">
		Your browser does not support the audio element.
	</audio>

</div>

<script>
	<?php
	$warehouses = "1 == 0";
	$exactSkus = "1 == 0";
	$duplicateLabels = "1 == 0";

	if(isset($_GET['add']) == true){

		$warehouses = "";
		$sql = "SELECT warehouse FROM `byod-orders`.warehouses GROUP BY warehouse";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$warehouses .= "sn.match(/" . $row['warehouse'] . "/gi) ||
";
			}
		} else {
			echo "0 results";
		}
		$warehouses = rtrim($warehouses, ' ||
');

		$exactSkus = "";
		$sql = "SELECT sku FROM `byod-orders`.exact_skus";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$sku = explode('/', $row['sku']);
				$sku = explode('(', $sku[0]);
				$exactSkus .= "sn == '" . addslashes($sku[0]) . "' ||
";
			}
		} else {
			echo "0 results";
		}
		$exactSkus = rtrim($exactSkus, ' ||
');

		$duplicateLabels = "";
		$sql = "SELECT
					signpost_label,
					COUNT(signpost_label)
				FROM
					labels
				GROUP BY signpost_label
				HAVING COUNT(signpost_label) > 1;";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$duplicateLabels .= "sn == '" . addslashes($row['signpost_label']) . "' ||
";
			}
		} else {
			echo "0 results";
		}
		$duplicateLabels = rtrim($duplicateLabels, ' ||
');

		echo 'var locations = [];';

		$sql = "SELECT
					warehouse,
					warehouse_location,
					warehouse_name
				FROM
					warehouses";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				echo "locations.push({warehouse:'" . addslashes($row['warehouse']) . "',loc:'" . addslashes($row['warehouse_location']) . "',desc:'" . addslashes($row['warehouse_name']) . "'});\n";
			}
		} else {
			echo "0 results";
		}

	}
	?>

	function sleep (time) {
		return new Promise((resolve) => setTimeout(resolve, time));
	}

	var num = 0;
	function scanLine(add){
		if(typeof add !== 'undefined'){
			num = num+1;
			return num;
		} else {
			num = 0;
			return num;
		}
	}

	function saveSerial(serialnumber){

		if($('.warehouse')[0].value == ''){
			$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: Geen magazijn</strong>' + '<br>' + $('.results')[0].innerHTML;
			$('.sn')[0].value = '';
			$('.sn')[0].focus();
		} else if($('.location')[0].value == ''){
			$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: Geen locatie</strong>' + '<br>' + $('.results')[0].innerHTML;
			$('.sn')[0].value = '';
			$('.sn')[0].focus();
		} else if($('.sku')[0].value == ''){
			$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: Geen SKU</strong>' + '<br>' + $('.results')[0].innerHTML;
			$('.sn')[0].value = '';
			$('.sn')[0].focus();
		} else {
			sn = serialnumber;
			if(<?php echo $duplicateLabels; ?>){
				$('.results')[0].innerHTML = '<span style="color:blue;">Scan na de / het serienummer in</span>' + '<br>' + $('.results')[0].innerHTML;
				$('.sn')[0].value = sn+'/';
				$('#beep')[0].play();
				sleep(300).then(() => {
					$('#beep')[0].play();
				});
			} else {
				$.ajax({
					type:'POST',
					url:'ajaxStockData.php',
					data:'warehouse='+$('.warehouse')[0].value+'&location='+$('.location')[0].value+'&sku='+$('.sku')[0].value+'&serial='+sn.toUpperCase()+'&loginname=<?php echo $loginname; ?>&operation=add',
					success:function(html){
						//var result = $.parseJSON(html);
						//result[0]
						if(html.match(/NIET GELUKT/gi) || html.match(/FOUT/gi)){
							$('.results')[0].innerHTML = html + $('.results')[0].innerHTML;
							$('#beep')[0].play();
						} else {
							$('.results')[0].innerHTML = scanLine('add')+'. '+'<a class="deleteButton" onClick="deleteSerial(\''+sn+'\')">x</a> ' + html + $('.results')[0].innerHTML;
							$('#ja')[0].play();
						}
					}
				});
				console.log('warehouse='+$('.warehouse')[0].value+'&location='+$('.location')[0].value+'&sku='+$('.sku')[0].value+'&serial='+sn+'&loginname=<?php echo $loginname; ?>&operation=add');
				$('.sn')[0].value = '';
				$('.sn')[0].focus();
			}
		}
	}

	function deleteSerial(serialnumber){
		if (confirm('Ben je zeker om '+serialnumber+' te verwijderen?')) {
			$.ajax({
				type:'POST',
				url:'ajaxStockData.php',
				data:'warehouse='+$('.warehouse')[0].value+'&serial='+serialnumber+'&loginname=<?php echo $loginname; ?>&operation=delete',
				success:function(html){
					if(html.match(/NIET GELUKT/gi) || html.match(/FOUT/gi)){
						$('.results')[0].innerHTML = '<strong style="color:red;">' + html + '</strong>' + $('.results')[0].innerHTML;
						$('#beep')[0].play();
					} else {
						$('.results')[0].innerHTML = html + $('.results')[0].innerHTML;
						$('#ja')[0].play();
					}
					$('.sn_out')[0].focus();
				}
			});
		}
	}

	$('.sn').on( 'keydown', function( e ) {

		if( e.which == 13 || e.which == 9 ) {

			var sn = $('.sn')[0].value;
			var sn = sn.replace('-UUG', '#UUG');

			if((sn.startsWith('1s') == true || sn.startsWith('1S') == true) && sn.length >= 15){
				if($('.sku')[0].value != sn.substring(2).substr(0, 10)){
					$('.sku')[0].value = sn.substring(2).substr(0, 10);
					$('.results')[0].innerHTML = '<strong>SKU ingesteld op '+sn.substring(2).substr(0, 10)+'</strong>' + '<br>' + $('.results')[0].innerHTML;
					scanLine();
				}
				var sn = $('.sn')[0].value.slice(-8);
			}

			if((sn.startsWith('p2') == true || sn.startsWith('P2') == true) && sn.length >= 15){
				if($('.sku')[0].value != sn.slice(-12)){
					$('.sku')[0].value = sn.slice(-12);
					$('.results')[0].innerHTML = '<strong>SKU ingesteld op '+sn.slice(-12)+'</strong>' + '<br>' + $('.results')[0].innerHTML;
					scanLine();
				}
				var sn = $('.sn')[0].value.slice(0, 8);
			}

			if(sn.startsWith('31P') == true){
				var sn = sn.substring(3).substr(0, 10);
			}

			if(sn.includes('/') == true){
				var sn = sn.split('/');
				var sn0 = sn[0];
				var sn1 = sn[1];
				if((sn1.startsWith('1s') == true || sn1.startsWith('1S') == true) && sn1.length >= 15){
					var sn1 = sn1.substring(12);
				}
				if(sn1.startsWith('31P') == true){
					var sn1 = sn1.substring(3).substr(0, 10);
				}
				if(sn1.includes(',') == true && sn1.length >= 15){
					var sn1 = sn1.split(',');
					if($('.sku')[0].value != sn1[1].toUpperCase()){
						$('.sku')[0].value = sn1[1].toUpperCase();
						$('.results')[0].innerHTML = '<strong>SKU ingesteld op '+sn[1].toUpperCase()+'</strong>' + '<br>' + $('.results')[0].innerHTML;
						scanLine();
					}
					var sn1 = sn1[0];
				}
				if((sn1.match(/^S.*$/i) && sn1.length == 9) || (sn1.match(/^R90.*$/i) && sn1.length == 8) ||
					(sn1.match(/^SPF.*$/i) && sn1.length == 9) || (sn1.match(/^PF.*$/i) && sn1.length == 8) ||
					(sn1.match(/^S4.*$/i) && sn1.length == 8)){
					var sn1 = sn1.slice(-8);
				}

				var sn = sn0 + '/' + sn1;
			} else {
				if(sn.includes(',') == true){
					var sn = sn.split(',');
					if($('.sku')[0].value != sn[1].toUpperCase()){
						$('.sku')[0].value = sn[1].toUpperCase();
						$('.results')[0].innerHTML = '<strong>SKU ingesteld op '+sn[1].toUpperCase()+'</strong>' + '<br>' + $('.results')[0].innerHTML;
						scanLine();
					}
					var sn = sn[0];
				}
			}

			if(sn.match(/stock out.*/i)){

				window.location.href = "stock.php?remove";

			} else if(<?php echo $warehouses; ?>){

				if (sn.match(/^[0-9][-][A-z][A-z][A-z][-][0-9][0-9][0-9].*$/i)){
					$('.results')[0].innerHTML = '<strong>Magazijn en locatie ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
					$('.warehouse')[0].value = sn.toUpperCase();
					$('.location')[0].value = sn.toUpperCase();
					$('.sn')[0].value = '';
					$('.sn')[0].focus();
					scanLine();
				} else {
					$('.results')[0].innerHTML = '<strong>Magazijn ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
					$('.warehouse')[0].value = sn.toUpperCase();
					$('.sn')[0].value = '';
					$('.sn')[0].focus();
					scanLine();
				}

			} else if(sn.match(/verkocht.*/i) || sn.match(/bruikleen.*/i)){

				$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: Dit is geen geldige locatie</strong>' + '<br>' + $('.results')[0].innerHTML;
				$('.sn')[0].value = '';
				$('.sn')[0].focus();

			} else if(sn.match(/^([A-z][0-9][0-9]-[0-9])$/i)){

				sn = sn.toUpperCase();

				// Check if there is a warehouse defined for this location
				if(locations.find( ({ loc }) => loc === sn ) !== undefined){
					var warehouse = locations.find( ({ loc }) => loc === sn ).warehouse;
					var warehouse_desc = locations.find( ({ loc }) => loc === sn ).desc;
					$('.results')[0].innerHTML = '<strong>Magazijn ingesteld op '+warehouse+' ('+warehouse_desc+')</strong>' + '<br>' + $('.results')[0].innerHTML;
					$('.warehouse')[0].value = warehouse.toUpperCase();
				}

				$('.results')[0].innerHTML = '<strong>Locatie ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
				$('.location')[0].value = sn.toUpperCase();
				$('.sn')[0].value = '';
				$('.sn')[0].focus();
				scanLine();

			} else if(sn.includes("#")){

				$('.results')[0].innerHTML = '<strong>SKU ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
				$('.sku')[0].value = sn.toUpperCase();
				$('.sn')[0].value = '';
				$('.sn')[0].focus();
				scanLine();

			} else if(<?php echo $exactSkus; ?>){

				$('.results')[0].innerHTML = '<strong>SKU ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
				$('.sku')[0].value = sn.toUpperCase();
				$('.sn')[0].value = '';
				$('.sn')[0].focus();
				scanLine();

			} else if(sn.match(/^([0-9]{13})$/i)){

				if(sn.startsWith('00') == true || sn.startsWith('04') == true){
					saveSerial(sn);
				} else {
					$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: EAN Nummer is niet ondersteund</strong>' + '<br>' + $('.results')[0].innerHTML;
					$('.sn')[0].value = '';
					$('.sn')[0].focus();
				}

			} else if((sn.match(/^S.*$/i) && sn.length == 9) || (sn.match(/^R90.*$/i) && sn.length == 8) ||
				(sn.match(/^SPF.*$/i) && sn.length == 9) || (sn.match(/^PF.*$/i) && sn.length == 8) ||
				(sn.match(/^S4.*$/i) && sn.length == 8)){

				saveSerial(sn.slice(-8));

			} else if(sn.match(/^P2.*$/i) && sn.length == 8){

				saveSerial(sn.slice(0, 8));

			} else if((sn.match(/^0G.*$/i) || sn.match(/^0k.*$/i)) && sn.length == 13){

				saveSerial(sn);

			} else if(sn.includes("/")){

				saveSerial(sn);

			} else if((sn.match(/^3C.*$/i) || sn.match(/^45.*$/i) || sn.match(/^4C.*$/i) || sn.match(/^5C.*$/i) || sn.match(/^8C.*$/i) || sn.match(/^CN.*$/i) || sn.match(/^CQ.*$/i) || sn.match(/^CZ.*$/i)) && sn.length == 10){

				if(typeof sn1 !== 'undefined'){
					saveSerial(sn0+'/'+sn1);
				} else {
					saveSerial(sn);
				}

			} else {

				saveSerial(sn);

			}

			console.log(sn);

		}

	} );

	$('.sn_out').on( 'keydown', function( e ) {

		if( e.which == 13 || e.which == 9 ) {

			var sn = $('.sn_out')[0].value;
			var warehouse = $('.warehouse')[0].value;
			var sn = sn.replace('-UUG', '#UUG');

			if((sn.startsWith('1s') == true || sn.startsWith('1S') == true) && sn.length >= 15){
				var sn = sn.substring(2).substr(0, 10);
				console.log(sn);
			}

			if(sn.startsWith('31P') == true){
				var sn = sn.substring(3).substr(0, 10);
				console.log(sn);
			}

			if(sn.includes('/') == true){
				var sn = sn.split('/');
				var sn0 = sn[0];
				var sn1 = sn[1];
				if((sn1.startsWith('1s') == true || sn1.startsWith('1S') == true) && sn1.length >= 15){
					var sn1 = sn1.substring(12);
				}
				if(sn1.includes(',') == true && sn1.length >= 15){
					var sn1 = sn1.split(',');
					var sn1 = sn1[0];
				}
				var sn = sn0 + '/' + sn1;
			} else {
				if(sn.includes(',') == true){
					var sn = sn.split(',');
					var sn = sn[0];
				}
			}

			if(sn.match(/stock in.*/i)){

				window.location.href = "stock.php?add";

			} else if(sn.match(/verkocht.*/i) || sn.match(/bruikleen.*/i) || sn.match(/intern.*/i)){

				if(sn.match(/bruikleen/i) || sn.match(/intern/i)){
					if(sn.match(/[/]/i)){
						$('.results')[0].innerHTML = '<strong>Bestemming ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
						$('.warehouse')[0].value = sn.toUpperCase();
						$('.sn_out')[0].value = '';
						$('.sn_out')[0].focus();
					} else {
						$('.results')[0].innerHTML = '<span style="color:blue;">Scan of typ na de / de reden van bruikleen</span>' + '<br>' + $('.results')[0].innerHTML;
						$('.sn_out')[0].value = sn+'/';
						$('#beep')[0].play();
						sleep(300).then(() => {
							$('#beep')[0].play();
						});
					}
				} else {
					$('.results')[0].innerHTML = '<strong>Bestemming ingesteld op '+sn+'</strong>' + '<br>' + $('.results')[0].innerHTML;
					$('.warehouse')[0].value = sn.toUpperCase();
					$('.sn_out')[0].value = '';
					$('.sn_out')[0].focus();
				}

			} else if(sn.match(/alcatraz.*/i) || sn.match(/kortrijk.*/i) || sn.match(/imaging.*/i) ||
				sn.match(/technieker stock.*/i) || sn.match(/1-AAA-111.*/i) || sn.match(/1-BBB-222.*/i)){

				$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: Dit is geen geldige bestemming</strong>' + '<br>' + $('.results')[0].innerHTML;
				$('.sn_out')[0].value = '';
				$('.sn_out')[0].focus();

			} else if(warehouse == ''){

				$('.results')[0].innerHTML = '<strong style="color:red;">FOUT: Geen nieuwe bestemming geselecteerd</strong>' + '<br>' + $('.results')[0].innerHTML;
				$('.sn_out')[0].value = '';
				$('.sn_out')[0].focus();

			} else {

				if(sn.match(/[-][0-9][0-9][0-9][0-9]$/i) || sn.match(/[-][0-9][0-9][0-9]$/i)){
					$('.results')[0].innerHTML = '<span style="color:blue;">Scan na de / het serienummer in</span>' + '<br>' + $('.results')[0].innerHTML;
					$('.sn_out')[0].value = sn+'/';
					$('#beep')[0].play();
					sleep(300).then(() => {
						$('#beep')[0].play();
					});
				} else {
					var temp = sn;
					
					if((sn.match(/^S.*$/i) && sn.length == 9) || (sn.match(/^R90.*$/i) && sn.length == 8) ||
				    (sn.match(/^SPF.*$/i) && sn.length == 9) || (sn.match(/^PF.*$/i) && sn.length == 8) ||
				    (sn.match(/^S4.*$/i) && sn.length == 8)){
					    temp = sn.slice(-8);
					}

				    
					$.ajax({
						type:'POST',
						url:'ajaxStockData.php',
						data:'warehouse='+warehouse+'&serial='+temp+'&loginname=<?php echo $loginname; ?>&operation=remove',
						success:function(html){
							//var result = $.parseJSON(html);
							//result[0]
							$('.results')[0].innerHTML = html + $('.results')[0].innerHTML;
							if(html.match(/NIET GELUKT/gi) || html.match(/FOUT/gi)){
								$('#beep')[0].play();
							} else {
								$('#ja')[0].play();
							}
						}
					});
					$('.sn_out')[0].value = '';
					$('.sn_out')[0].focus();
				}

			}

			console.log(sn);

		}

	} );

	$('.sn').on('keydown', function(e){ if (e.keyCode == 9)  e.preventDefault() });
	$('.sn_out').on('keydown', function(e){ if (e.keyCode == 9)  e.preventDefault() });

	$('.type').on('change', function (e) {
		if ($('.type')[0].value == 'bruikleen'){
			$('.warehouse')[0].innerHTML='<option value="bruikleen">Bruikleen</option>';
		} else if ($('.type')[0].value == 'verkocht'){
			$('.warehouse')[0].innerHTML='<option value="verkocht">Verkocht</option>';
		} else if ($('.type')[0].value == 'auto'){
			$('.warehouse')[0].innerHTML='<option value=""></option><option value="1-AAA-100">1-AAA-100 Ward</option><option value="1-BBB-100">1-BBB-100 Jo</option><option value="1-CCC-100">1-CCC-100 Felix</option>';
		} else if ($('.type')[0].value == 'andere'){
			$('.warehouse')[0].innerHTML='<option value="andere">andere</option>';
		}
	});
</script>

<?php
include('footer.php');
?>
