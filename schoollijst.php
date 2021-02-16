<?php
$title = 'Schoollijst';
include_once 'conn.php';
include_once 'mssql-100-conn.php';
include_once 'head.php';
include_once 'nav.php';
include_once 'conn.php';
?>
<html lang="en">
<meta charset="UTF-8">
<title>School lijsten</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  tbody {
    color: #292929;
    border-color: #bfbfbf;
  }
</style>

<body>

  <div class="container-fluid">
    <?php echo "<h1 id='title'>{$title}</h1>" ?>
  </div>

  <?php
  $result = $conn->query('SELECT `leermiddel`.tblschool.SchoolNaam,
`leermiddel`.tblcontractdetails.ContractVolgnummer,
`byod-orders`.delivery.delivery_number,
`leermiddel`.tblcontractdetails.StartDatum,
`leermiddel`.tblcontractdetails.VoornaamLeerling,
`leermiddel`.tblcontractdetails.NaamLeerling,
`byod-orders`.labels.label,
`byod-orders`.labels.serialnumber
FROM `leermiddel`.tblcontractdetails 
INNER JOIN `leermiddel`.tblschool 
ON `leermiddel`.tblcontractdetails.SchoolID = `leermiddel`.tblschool.id
INNER JOIN `byod-orders`.labels
ON `leermiddel`.tblcontractdetails.instruction = `byod-orders`.labels.signpost_label
INNER JOIN `byod-orders`.delivery
ON `byod-orders`.labels.orderid = `byod-orders`.delivery.orderid;');
  ?>

  <script>
    var itemArray = <?php echo json_encode($result->fetch_all()); ?>
  </script>
  <div class="container-fluid">
    <table class="table" id="table" cellspacing="0" width="100%">
      <thead class="thead-dark">
        <tr id="myTr">
          <th scope="col">Schoolnaam</th>
          <th scope="col">Contractnummer</th>
          <th scope="col">Levering</th>
          <th scope="col">Datum van levering</th>
          <th scope="col">Firstname</th>
          <th scope="col">Lastname</th>
          <th scope="col">Label</th>
          <th scope="col">Serienummer</th>
        </tr>
      </thead>
      <tbody id="values"></tbody>
    </table>
  </div>
</body>
<?php
include_once 'footer.php';
?>

</html>