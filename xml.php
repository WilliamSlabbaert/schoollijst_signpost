<?php

$title = 'XML View';
include('head.php');
include('nav.php');
include('conn.php');

?>

	<h3><?php echo $title; ?></h3>
	<a href="<?php hasAccessForUrl('xml.php'); ?>" class="btn btn-primary">alles laten zien</a>
	<a href="<?php hasAccessForUrl('xml.php?onlyXML2=true'); ?>" class="btn btn-success">enkel de xml 1 & 2 laten zien</a>&nbsp&nbsp&nbsp&nbsp&nbsp
	<a href="<?php hasAccessForUrl('generateXML.php?id=alles'); ?>" class="btn btn-secondary">batch generate xml 1</a>
	<a href="<?php hasAccessForUrl('generateXML.php?id=alles&type=moving'); ?>" class="btn btn-warning">batch generate xml 2</a><br>

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Suborder</th>
				<th scope="col">Synergy ID</th>
				<th scope="col">Geleverd?</th>
				<th scope="col">Leverdatum Order</th>
				<th scope="col">Suborder aantal</th>
				<th scope="col">Aantal</th>
				<th scope="col">Warehouse</th>
				<th scope="col">XML 1</th>
				<th scope="col">XML 2</th>
				<?php if(isset($_GET['onlyXML2']) != true){ ?>
				<th scope="col">XML 4</th>
				<th scope="col">XML 5</th>
				<?php } ?>
			</tr>
		</thead>

		<tbody>
<?php

if(isset($_GET['onlyXML2']) == true){
	$sql = "SELECT *, q.shipping_date AS oshipping, q.amount AS orderamount, q.warehouse as owarehouse,
		q.synergyid AS ordersyn, q.amount AS orderamount, q.id AS ordernummer, q.id AS orderid,
		'0' as signature, '0' as delivery_number, '0' as deliverytype, '0' as deliverydate, '0' as suborderamount, '0' as deliveryid,
		( SELECT COUNT(orderid) FROM orderpicking WHERE orderid = q.id ) AS xml1,
		( SELECT id FROM orderpicking WHERE orderid = q.id Order by id desc limit 1) AS pickingid
		FROM orders q
		WHERE q.deleted != 1";
} else {
	$sql = "SELECT *, delivery.id as deliveryid, delivery.type as deliverytype, q.shipping_date AS oshipping, q.amount AS orderamount, q.warehouse as owarehouse, delivery.delivered_on AS deliverydate, delivery.amount AS suborderamount,
		q.synergyid AS ordersyn, q.amount AS orderamount,
		q.id AS ordernummer,
		( SELECT COUNT(orderid) FROM orderpicking WHERE orderid = q.id ) AS xml1,
		( SELECT id FROM orderpicking WHERE orderid = q.id Order by id desc limit 1) AS pickingid
		FROM orders q
		LEFT JOIN delivery ON delivery.orderid = q.id
		WHERE q.deleted != 1";
}
$result = $conn->query($sql);

	if ($result->num_rows > 0) {

		while($row = $result->fetch_assoc()) {


				if($row['signature'] == ''){
					echo '<tr class="btn-danger">';
				} else {
					echo '<tr>';
				}
				echo '<td data-sort="'. $row['ordernummer'] .'" style="width:180px;"><a href="'. hasAccessForUrl('delivery.php?delivery_number=' . $row['delivery_number'] . '&orderid=' . $row['ordernummer'] . '', false).'" target="_blank">SP-BYOD20-' . $row['ordernummer'] . '-' . $row['deliverytype'] . '' . $row['delivery_number'] . '-' . $row['deliveryid'] . '</strong></td>';
				echo '<td style="width:180px;"><a href="'. hasAccessForUrl('school.php?synergyid=' . $row['synergyid'] . '', false).'" target="_blank">' . $row['synergyid'] . '</a></td>';

				if($row['signature'] == ''){
					echo '<td>❌ Nee</td>';
				} else {
					echo '<td>✅ Ja</td>';
				}

				//echo '<td>' . $row['oshipping'] . '</td>';
				echo '<td data-sort="'. strtotime($row['deliverydate']) .'">' . $row['deliverydate'] . '</td>';
				echo '<td>' . $row['suborderamount'] . '</td>';
				echo '<td>' . $row['orderamount'] . '</td>';
				echo '<td>' . $row['owarehouse'] . '</td>';

				echo '<td class="">';
				if($row['xml1'] != 0 || $row['xmlstate'] >= 1){
					echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['orderid'] . '&amount=' . $row['orderamount'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important; color: red; width:100px !important;padding:0px;margin:0px;">Pick Order</button></a><br>';
				} else {
					echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['orderid'] . '&amount=' . $row['orderamount'] . '', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Pick Order</button></a><br>';
				}
				echo '</td>';

				echo '<td class="">';
				if($row['xmlstate'] >= 2){
					echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['orderid'] . '&pickingid=' . $row['pickingid'] . '&amount=' . $row['orderamount'] . '&warehouse=' . $row['warehouse'] . '&date=' . date('Y-m-d') . '&type=moving', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;color:red;">Move Order</button></a><br>';
				} else {
					echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['orderid'] . '&pickingid=' . $row['pickingid'] . '&amount=' . $row['orderamount'] . '&warehouse=' . $row['warehouse'] . '&date=' . date('Y-m-d') . '&type=moving', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:100px !important;padding:0px;margin:0px;">Move Order</button></a><br>';
				}
				echo '</td>';

				if(isset($_GET['onlyXML2']) != true){
					echo '<td>';
					if($row['deliverytype'] != 'W'){
						if($row['exact_generated'] == '1' || $row['xmlstate'] >= 4){
							echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['deliveryid'] . '&orderid=' . $row['orderid'] . '&date=' . date('Y-m-d') . '&type=4', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:0px 5px;color:red;">Gegenereerd - exact (4)</button></a>';
						} else {
							echo '<a href="'. hasAccessForUrl('generateXML.php?id=' . $row['deliveryid'] . '&orderid=' . $row['orderid'] . '&date=' . date('Y-m-d') . '&type=4', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:200px !important;padding:0px;margin:0px 5px;">Genereren in exact (4)</button></a>';
						}
					}
					echo '</td>';

					if($row['type'] == 'W'){
						if($row['exact_delivery'] == '1' || $row['xmlstate'] >= 4){
							echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row['deliveryid'] . '&deliverynumber=' . $row['delivery_number'] . '&orderid=' . $row['orderid'] . '&warehouse=' . $row['warehouse'] . '&type=5&webshop=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;color:red;">Webshop levering in exact (5W)</button></a></td>';
							//echo '<td><a href="generateXML.php?deliveryid=' . $row['deliveryid'] . '&orderid=' . $row['orderid'] . '&deliveryNumber=' . $row['delivery_number'] . '&type=5W"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;color:red;">Webshop levering in exact (5W)</button></a></td>';
						} else {
							echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row['deliveryid'] . '&deliverynumber=' . $row['delivery_number'] . '&orderid=' . $row['orderid'] . '&warehouse=' . $row['warehouse'] . '&type=5&webshop=true', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Webshop levering in exact (5W)</button></a></td>';
							//echo '<td><a href="generateXML.php?deliveryid=' . $row['deliveryid'] . '&orderid=' . $row['orderid'] . '&deliveryNumber=' . $row['delivery_number'] . '&type=5W"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Webshop levering in exact (5W)</button></a></td>';
						}
					} else {
						if($row['exact_delivery'] == '1' || $row['xmlstate'] >= 5){
							echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row['deliveryid'] . '&deliverynumber=' . $row['delivery_number'] . '&orderid=' . $row['orderid'] . '&warehouse=' . $row['warehouse'] . '&type=5', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;color:red;">Levering in exact (5)</button></a></td>';
						} else {
							echo '<td><a href="'. hasAccessForUrl('generateXML.php?deliveryid=' . $row['deliveryid'] . '&deliverynumber=' . $row['delivery_number'] . '&orderid=' . $row['orderid'] . '&warehouse=' . $row['warehouse'] . '&type=5', false).'"><button type="button" class="btn btn-secondary" style="height:25px !important;width:250px !important;padding:0px;margin:0px 5px;">Levering in exact (5)</button></a></td>';
						}
					}
				}

				echo '</tr>';

		}
	} else {

		echo "0 results";

	}

	$conn->close();

?>

		</tbody>
	</table>

<?php
include('footer.php');
?>
