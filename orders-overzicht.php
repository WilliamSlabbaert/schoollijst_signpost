<?php
$title = 'orders overzicht';
include_once 'mssql-100-conn.php';
include_once 'head.php';
include_once 'nav.php';
include_once 'conn.php';
include_once 'orders-overzicht-query.php';

?>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<body>
    <h1 class="container-fluid"><?php echo $title; ?></h1>

    <script>
        let itemArray = <?php echo json_encode($data); ?>;
        let itemArray2 = <?php echo json_encode($data2); ?>;
    </script>
    <h4 class="container-fluid">Aantal orders per school</h4>
    <div id="treeMapLocation" class="container-fluid"></div>

    <h4 class="container-fluid">Openstaande orders</h4>
    <div class="container-fluid">
        <table class="table" id="table" cellspacing="0" width="100%">
            <thead class="thead-dark">
                <tr id="myTr">
                    <th scope="col">School</th>
                    <th scope="col">Refer</th>
                    <th scope="col">Order datum</th>
                    <th scope="col">Email</th>
                    <th scope="col">Artikel</th>
                </tr>
            </thead>
            <tbody id="values"></tbody>
        </table>
    </div>
    <script type="text/javascript" src="treeMap-light.js"></script>;
</body>
<?php
include_once 'footer.php';
?>

</html>