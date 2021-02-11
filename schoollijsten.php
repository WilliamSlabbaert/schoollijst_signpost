<?php
include_once 'conn.php';
include_once 'mssql-100-conn.php';
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<html lang="en">
<meta charset="UTF-8">
<title>School lijsten</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="style.css">

<body>
  <h1>School Lijst</h1>

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

    <form id="inptForm" >
      <div class="form-group">
        <input class="form-control"  type="text" id="gsearch">
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
    </form>
    <table class="table" style="width:100%">
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
    </table>

  <script src="code.js"></script>
</body>

</html>