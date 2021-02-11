<?php
include_once 'conn.php';
include_once 'mssql-100-conn.php';
include('head.php');
include('nav.php');
include('conn.php');
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<html lang="en">
<meta charset="UTF-8">
<title>School lijsten</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="style.css">

<body>
  <div class="container">
    <h1>School Lijst</h1>
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
    <form id="inptForm">
      <div>
        <input class="form-control" type="text" id="gsearch">
        <div class="form-group">
          <select class="form-control" name="dropdown">
            <option selected>Schoolnaam</option>
            <option>Contractnummer</option>
            <option>Levering</option>
            <option>Datum van levering</option>
            <option>Firstname</option>
            <option>Lastname</option>
            <option>Label</option>
            <option>Serienummer</option>
          </select>
          <input class="form-control" type="button" id="searchButton" value="Search">
        </div>
      </div>
    </form>
    <table data-toggle="table" class="table table-striped table-bordered table-sm" id="dtBasic" cellspacing="0" width="100%">
      <thead>
        <tr id="myTr">
          <th class="th-sm" data-sortable="true">Schoolnaam</th>
          <th class="th-sm" data-sortable="true">Contractnummer</th>
          <th class="th-sm" data-sortable="true">Levering</th>
          <th class="th-sm" data-sortable="true">Datum van levering</th>
          <th class="th-sm" data-sortable="true">Firstname</th>
          <th class="th-sm" data-sortable="true">Lastname</th>
          <th class="th-sm" data-sortable="true">Label</th>
          <th class="th-sm" data-sortable="true">Serienummer</th>
        </tr>
      </thead>
    </table>
  </div>

  <script src="code.js"></script>
</body>

</html>