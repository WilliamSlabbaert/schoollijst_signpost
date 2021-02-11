<?php

$title = 'Field Services - Cases';

include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body">

<h2>Field Services - Cases</h2>
<a href="<?php hasAccessForUrl('createFieldServiceTicket.php'); ?>" class="btn btn-primary">Maak een nieuwe case aan</a>

<?php

$sql = "SELECT
			CONCAT('#FS-', id) AS '#',
			serial AS Serienummer,
			Type,
			Status,
			asignee AS 'Toegewezen Op',
			createdOn,
			updatedAt,
			CONCAT('<a href=\"showFieldServiceTicketDetails.php?id=', id, '\" target=\"_blank\">Bekijk Case #FS-', id, '</a>') AS 'Link'
		FROM fieldServiceCases";
$result = $conn->query($sql);
echo createTable($result, 'mysql');

?>

</div>

<?php
include('footer.php');
?>
