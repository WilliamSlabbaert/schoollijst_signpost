<?php
// Include the database config file
//manufacturer - model - motherboard - memory - ssd - panel
include_once 'conn.php';

if(!empty($_POST["ombouw"]) && !empty($_POST["spsku"])) {

	$query = 'SELECT * FROM devices WHERE SPSKU = "' . $_POST['spsku'] . '" LIMIT 1';
	$result = $conn->query($query);
	$panelswap = 0;
	$ssdswap = 0;
	$memoryswap = 0;
	$keyboardswap = 0;

	// Generate HTML of state options list
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			if ($row['panel_swap'] == 'x') {
				$panelswap = 1;
			}
			if ($row['ssd_swap'] == 'x') {
				$ssdswap = 1;
			}
			if ($row['memory_swap'] == 'x') {
				$memoryswap = 1;
			}
			if ($row['keyboard_swap'] == 'x') {
				$keyboardswap = 1;
			}
		}
	}else{
		echo 'Geen SPSKU gevonden in de database';
	}
	if ($panelswap !== 0) {
		//Select panel
		?>
		Panel
		<select id="panelswap" name="panelswap" class="form-control" required>
			<option value="" selected disabled></option>
			<?php
				$sql = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS panelswaporder FROM orders WHERE panelswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS panelswaporder,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'SCHERM'
						GROUP BY SPSKU";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$stock = $row['stock'] - $row['panelswaporder'];
						echo '<option value="' . $row["SPSKU"]. '">' . $row["SPSKU"]. ' - ' . $row["desc"]. ' (' . $stock . ')</option>';
					}
				} else {
					echo "0 results";
				}
			?>
		</select><br>
		<?php
	}

	if ($ssdswap !== 0) {
		//Select panel
		?>
		SSD
		<select id="ssdswap" name="ssdswap" class="form-control" required>
			<option value="" selected disabled></option>
			<?php
				$sql = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS ssdswaporder FROM orders WHERE ssdswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS ssdswaporder,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'SSD'
						GROUP BY SPSKU";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$stock = $row['stock'] - $row['ssdswaporder'];
						echo '<option value="' . $row["SPSKU"]. '">' . $row["SPSKU"]. ' - ' . $row["desc"]. ' (' . $stock . ')</option>';
					}
				} else {
					echo "0 results";
				}
			?>
		</select><br>
		<?php
	}

	if ($memoryswap !== 0) {
		//Select panel
		?>
		RAM Slot 1
		<select id="memoryswap" name="memoryswap" class="form-control" required>
			<option value="" selected disabled></option>
			<?php
				$sql = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS memoryswaporder FROM orders WHERE memoryswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder,
						IFNULL((SELECT SUM(amount) AS memoryswaporder2 FROM orders WHERE memoryswap2 = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder2,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'RAM'
						GROUP BY SPSKU";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$stock = $row['stock'] - $row['memoryswaporder'] - $row['memoryswaporder2'];
						echo '<option value="' . $row["SPSKU"]. '">' . $row["SPSKU"]. ' - ' . $row["desc"]. ' (' . $stock . ')</option>';
					}
				} else {
					echo "0 results";
				}
			?>
		</select><br>
		RAM Slot 2
		<select id="memoryswap2" name="memoryswap2" class="form-control">
			<option value="" selected disabled></option>
			<?php
				$sql = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
						IFNULL((SELECT SUM(amount) AS memoryswaporder FROM orders WHERE memoryswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder,
						IFNULL((SELECT SUM(amount) AS memoryswaporder2 FROM orders WHERE memoryswap2 = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS memoryswaporder2,
						`type`, location, notes, campagne, origin
						FROM `device-parts` q WHERE `type` = 'RAM'
						GROUP BY SPSKU";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$stock = $row['stock'] - $row['memoryswaporder'] - $row['memoryswaporder2'];
						echo '<option value="' . $row["SPSKU"]. '">' . $row["SPSKU"]. ' - ' . $row["desc"]. ' (' . $stock . ')</option>';
					}
				} else {
					echo "0 results";
				}
			?>
		</select><br>
		<?php
	}


	if ($keyboardswap !== 0) {
	//Select Keyboard
		?>
		Keyboard
	<select id="keyboardswap" name="keyboardswap" class="form-control" required>
	<option value="" selected disabled></option>
	<?php
		$sql5 = "SELECT id, SPSKU, SKU, `desc`, SUM(stock) as stock,
							IFNULL((SELECT SUM(amount) AS keyboardswaporder FROM orders WHERE keyboardswap = q.SPSKU AND STATUS != 'nieuw' and deleted != 1), 0) AS keyboardswaporder,
							`type`, location, notes, campagne, origin
							FROM `device-parts` q WHERE `type` = 'KEYBOARD'
							GROUP BY SPSKU";
					$result5 = $conn->query($sql5);

					if ($result5->num_rows > 0) {
						while($row5 = $result5->fetch_assoc()) {
							$stock = $row5['stock'] - $row5['keyboardswaporder'];
							echo '<option value="' . $row5["SPSKU"]. '">' . $row5["SPSKU"]. ' - ' . $row5["desc"]. ' (' . $stock . ')</option>';
						}
					} else {
						echo "0 results";
					}
		?>
			</select><br>
		<?php
		}

} elseif(!empty($_POST["manufacturer"]) && empty($_POST["model"]) && empty($_POST["motherboard"]) && empty($_POST["memory"]) && empty($_POST["ssd"]) && empty($_POST["panel"]) && empty($_POST["warranty"])){
	// Fetch state data based on the specific country
	$query = 'SELECT distinct model FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '"';
	$result = $conn->query($query);

	// Generate HTML of state options list
	if($result->num_rows > 0){
		echo '<option value="" disabled selected></option>';
		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['model'].'">'.$row['model'].'</option>';
		}
	}else{
		echo '<option value="">model not available</option>';
	}
}elseif(!empty($_POST["manufacturer"]) && !empty($_POST["model"]) && empty($_POST["motherboard"]) && empty($_POST["memory"]) && empty($_POST["ssd"]) && empty($_POST["panel"]) && empty($_POST["warranty"])){
	// Fetch city data based on the specific state
	$query = 'SELECT distinct motherboard_value FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '" AND model = "' . $_POST['model'] . '"';
	$result = $conn->query($query);

	// Generate HTML of city options list
	if($result->num_rows > 0){
		echo '<option value="" disabled selected></option>';
		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['motherboard_value'].'">'.$row['motherboard_value'].'</option>';
		}
	}else{
		echo '<option value="">motherboard not available</option>';
	}
}elseif(!empty($_POST["manufacturer"]) && !empty($_POST["model"]) && !empty($_POST["motherboard"]) && empty($_POST["memory"]) && empty($_POST["ssd"]) && empty($_POST["panel"]) && empty($_POST["warranty"])){
	// Fetch city data based on the specific state
	$query = 'SELECT distinct memory_value FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '" AND model = "' . $_POST['model'] . '" AND motherboard_value = "' . $_POST['motherboard'] . '"';
	$result = $conn->query($query);

	// Generate HTML of city options list
	if($result->num_rows > 0){
		echo '<option value="" disabled selected></option>';
		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['memory_value'].'">'.$row['memory_value'].' GB</option>';
		}
	}else{
		echo '<option value="">memory not available</option>';
	}
}elseif(!empty($_POST["manufacturer"]) && !empty($_POST["model"]) && !empty($_POST["motherboard"]) && !empty($_POST["memory"]) && empty($_POST["ssd"]) && empty($_POST["panel"]) && empty($_POST["warranty"])){
	// Fetch city data based on the specific state
	$query = 'SELECT distinct ssd_value FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '" AND model = "' . $_POST['model'] . '" AND motherboard_value = "' . $_POST['motherboard'] . '" AND memory_value = "' . $_POST['memory'] . '"';
	$result = $conn->query($query);

	// Generate HTML of city options list
	if($result->num_rows > 0){
		echo '<option value="" disabled selected></option>';
		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['ssd_value'].'">'.$row['ssd_value'].' GB</option>';
		}
	}else{
		echo '<option value="">ssd not available</option>';
	}
}elseif(!empty($_POST["manufacturer"]) && !empty($_POST["model"]) && !empty($_POST["motherboard"]) && !empty($_POST["memory"]) && !empty($_POST["ssd"]) && empty($_POST["panel"]) && empty($_POST["warranty"])){
	// Fetch city data based on the specific state
	$query = 'SELECT distinct panel_value FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '" AND model = "' . $_POST['model'] . '" AND motherboard_value = "' . $_POST['motherboard'] . '" AND memory_value = "' . $_POST['memory'] . '" AND ssd_value = "' . $_POST['ssd'] . '"';
	$result = $conn->query($query);

	// Generate HTML of city options list
	if($result->num_rows > 0){
		echo '<option value="" disabled selected></option>';
		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['panel_value'].'">'.$row['panel_value'].'</option>';
		}
	}else{
		echo '<option value="">panel not available</option>';
	}
}elseif(!empty($_POST["manufacturer"]) && !empty($_POST["model"]) && !empty($_POST["motherboard"]) && !empty($_POST["memory"]) && !empty($_POST["ssd"]) && !empty($_POST["panel"]) && empty($_POST["warranty"])){
	// Fetch city data based on the specific state
	$query = 'SELECT distinct warranty FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '" AND model = "' . $_POST['model'] . '" AND motherboard_value = "' . $_POST['motherboard'] . '" AND memory_value = "' . $_POST['memory'] . '" AND ssd_value = "' . $_POST['ssd'] . '" AND panel_value = "' . $_POST['panel'] . '"';
	$result = $conn->query($query);

	// Generate HTML of city options list
	if($result->num_rows > 0){
		echo '<option value="" disabled selected></option>';
		while($row = $result->fetch_assoc()){
			echo '<option value="'.$row['warranty'].'">'.$row['warranty'].' jaar</option>';
		}
	}else{
		echo '<option value="">warranty not available</option>';
	}
}elseif(!empty($_POST["manufacturer"]) && !empty($_POST["model"]) && !empty($_POST["motherboard"]) && !empty($_POST["memory"]) && !empty($_POST["ssd"]) && !empty($_POST["panel"]) && !empty($_POST["warranty"])){
	// Fetch city data based on the specific state
	$query = 'SELECT * FROM devices WHERE manufacturer = "' . $_POST['manufacturer'] . '" AND model = "' . $_POST['model'] . '" AND motherboard_value = "' . $_POST['motherboard'] . '" AND memory_value = "' . $_POST['memory'] . '" AND ssd_value = "' . $_POST['ssd'] . '" AND panel_value = "' . $_POST['panel'] . '" AND warranty = "' . $_POST['warranty'] . '"';
	$result = $conn->query($query);

	$stock = 0;
	$SPSKU = "";
	$price = "";
	$repiar = "";

	$output = "";
	$output_sku = "";
	$output_price = "";
	$output_repiar = "";

	// Generate HTML of city options list
	if($result->num_rows > 0){

		while($row = $result->fetch_assoc()){

			if ($SPSKU == ""){
				$SPSKU .= $row['SPSKU'];
			} else {
				$SPSKU .= ';' . $row['SPSKU'];
			}

			$price = $row['default_price'];
			$repiar = $row['default_repair'];
			$stock = $stock + $row['sp-stock'] + $row['td-stock'];

		}

		if($stock <= 0){
			$output_sku .= '<input type="text" class="form-control" name="SPSKU' . $_POST['nr'] . '" value="' . $SPSKU . '" readonly required style="border:1px solid red;">';
			$output_sku .= '<br><strong style="color:red;font-size:10px;">Momenteel zijn er van dit type, geen laptops op stock,<br>De levertermijn kan hierdoor oplopen.</strong>';
			//echo $SPSKU;
		} else {
			$output_sku .= '<input type="text" class="form-control" name="SPSKU' . $_POST['nr'] . '" value="' . $SPSKU . '" readonly required>';
			//echo $SPSKU;
		}

		$output_price = '<input type="number" value="' . $price . '" class="form-control" name="device' . $_POST['nr'] . '-defaultprice" disabled>';
		$output_repiar = '<input type="number" value="' . $repiar . '" class="form-control" name="device' . $_POST['nr'] . '-defaultrepair" disabled>';

		echo json_encode(array($output_sku, $output_price, $output_repiar));

	} else {

		echo '<strong>niets gevonden</strong>';

	}
}
?>
