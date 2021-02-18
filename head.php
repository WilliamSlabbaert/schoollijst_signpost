<head>
	<?php
	if (isset($autoRefresh) && $autoRefresh === true) {
		echo '<meta http-equiv="refresh" content="300">';
	}
	?>

	<meta charset="UTF-8">
	<title><?php echo ($title); ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>

	<!-- Bootstrap -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

	<!-- Datatables -->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
	<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
	<!-- Datables Styling -->
	<link rel='stylesheet' type='text/css' href='css/paging-style.css' />
	<link rel='stylesheet' type='text/css' href='css/datatable-style.css' />

	<!--  apexcharts  -->
	<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
	<script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	

	<!-- Select Dropdown Search -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

	<!-- Date Time Picker -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css" />
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>

	<!-- Signature Plugin -->
	<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

	<!-- Icons -->
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

	<!-- Logo -->
	<link rel="shortcut icon" type="image/png" href="images/favicon.png" />

	<?php
	if (
		($_COOKIE['mode'] == "dark" && !isset($darkModeOff)) ||
		($_COOKIE['mode'] == "dark" && isset($darkModeOff) && !$darkModeOff)
	) {
		echo '<link rel="stylesheet" href="css/darkly.css">';
		echo '<link rel="stylesheet" href="css/custom-dark.css">';
	} else {
		echo '<link rel="stylesheet" href="css/flatly.css">';
		echo '<link rel="stylesheet" href="css/custom-light.css">';
	} ?>

	<style media="print">
		.noPrint {
			display: none;
		}
	</style>

	<script type="application/javascript">
		$(document).ready(function() {
			$("a[href*='#no-access']").click(function() {
				$('#geenToegangPopup').show().delay(2500).fadeOut(700);
			});
		});
	</script>

</head>

<body <?php if ($title === 'Controle Dashboard') {
			echo 'style="padding-top:0px"';
		} ?>>
	<?php

	include('users.php');

	if (isset($_SERVER['AUTH_USER'])) {
		$user = $_SERVER['AUTH_USER'];
		$fullusername = $_SERVER['AUTH_USER'];
	} else {
		$user = "notfound";
		$fullusername = "";
	}

	if ($user == "notfound") {
		$user = "SIGNPOST\developer";
		$fullusername = "SIGNPOST\developer";
	}

	$loginname = ucfirst(str_replace("signpost\\", "", strtolower($user)));

	$role = [];
	if (isset($users[$loginname])) {
		$role = $users[$loginname];
	}

	if ($loginname == "Developer") {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}

	include('conn.php');
	$schoolNotificationSql = "
			SELECT IFNULL(COUNT(*), 0) AS notifications
			FROM schools
			WHERE schoolasignee = '" . $loginname . "'
			";
	$schoolNotificationResult = $conn->query($schoolNotificationSql);

	$notificationSql = "
			SELECT IFNULL(SUM(notification), 0) AS notifications FROM (
			SELECT IFNULL(COUNT(*), 0) AS notification FROM orders WHERE asignee = '" . $loginname . "' and deleted != 1
			UNION ALL
			SELECT IFNULL(COUNT(*), 0) AS notification FROM images2019 WHERE Initials = '" . $loginname . "' AND okvoor2020 = '1' AND status2020 != 'done'
			UNION ALL
			SELECT IFNULL(COUNT(*), 0) AS notification FROM images2020 WHERE asignee = '" . $loginname . "' AND confirmed = '1' AND STATUS != 'done') q
			";
	$notificationResult = $conn->query($notificationSql);

	$notifications = 0;
	$schoolNotifications = 0;

	if ($notificationResult->num_rows > 0) {
		while ($rows = $notificationResult->fetch_assoc()) {
			$notifications = $rows['notifications'];
		}
	} else {
		echo "0 results";
	}

	if ($schoolNotificationResult->num_rows > 0) {
		while ($srows = $schoolNotificationResult->fetch_assoc()) {
			$schoolNotifications = $srows['notifications'];
		}
	} else {
		echo "0 results";
	}

	$allnotifications = $notifications + $schoolNotifications;

	function mysqli_field_name($result, $field_offset)
	{
		$properties = mysqli_fetch_field_direct($result, $field_offset);
		return is_object($properties) ? $properties->name : null;
	}

	function createTable($result, $type)
	{
		if ($type == 'mysql' || $type == 'leermiddel' || $type == 'byod') {

			$table = '<table class="table" id="table"><thead class="thead-dark">';
			for ($x = 0; $x < mysqli_num_fields($result); $x++) $table .= '<th>' . mysqli_field_name($result, $x) . '</th>';
			$table .= '</thead>';
			while ($rows = mysqli_fetch_assoc($result)) {
				$table .= '<tr>';
				foreach ($rows as $row) $table .= '<td>' . $row . '</td>';
				$table .= '</tr>';
			}
			$table .= '</table>';
			//mysql_data_seek($result,0); //if we need to reset the mysql result pointer to 0
			return $table;
		} else if ($type == 'mssql' || $type == 'exact' || $type == 'synergy') {

			$table = '<table class="table" id="table"><thead class="thead-dark">';
			foreach (sqlsrv_field_metadata($result) as $field) $table .= '<th>' . $field['Name'] . '</th>';
			$table .= '</thead>';
			while ($rows = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
				$table .= '<tr>';
				foreach ($rows as $row) {
					if ($row instanceof DateTime) {
						$table .= '<td data-sort="' . strtotime(date_format($row, 'd-m-Y')) . '>' . $row->format('d-m-Y') . '</td>';
					} else {
						$table .= '<td>' . $row . '</td>';
					}
				}
				$table .= '</tr>';
			}
			$table .= '</table>';
			return $table;
		}
	}

	?>