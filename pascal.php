<?php

$title = 'PVT - Deliveries';

include('head.php');
include('nav.php');
include('readonly-conn.php');

?>

<div class="body">
<p align=center>

<table width=75% border="0">
	<th colspan=7 ><p align=center><B><font size=8 color=#00ADBD> Pascals SP2 Pages</th>
		<tr>
		<td>
	<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title">Deliveries</h4>
			<p class="card-text">For a given SynergyID, show all deliveries.<br><center><a href="<?php hasAccessForUrl('deliverybysynergy.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>
		</div>
	</div>
	<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title">Signpost SKU definitions</h4>
			<p class="card-text">Overview of all SP Sku definitions in SP2<br><center><a href="<?php hasAccessForUrl('byodskudefinitions.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>
		</div>
		<td width=10> <td>
	</div>	
		<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">External</div>
		<div class="card-body">
			<h4 class="card-title">Labels with devicedata</h4>
			<p class="card-text">For a given SynergyID, show more device info (e.g. MAC address)<br><center><a href="<?php hasAccessForUrl('LabelSerialMAC.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>
		</div>
	</div>
	
		<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title">Open orders</h4>
			<p class="card-text">All open order (Leermiddel, Webshop & School orders)<br><center><a href="<?php hasAccessForUrl('OpenOrders-all.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>&nbsp&nbsp<a href=https://orders.signpost.site/OpenOrdersLMWS.php><button class='btn-success'> Detail LM/WS &nbsp</button></a>
		</div>
	</div>
		<td width=10> <td>
	<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title">Leermiddel contracts</h4>
			<p class="card-text">Overview of all non-deleted Leermiddel contracts<br><center><a href="<?php hasAccessForUrl('Leermiddelbysynergyid.php'); ?>"><button class='btn-success'> SynergyID &nbsp</button></a>&nbsp&nbsp<a href=https://orders.signpost.site/AlleLM.php><button class='btn-success'> ALL &nbsp</button></a>
		</div>
	</div>
	<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title">Stock per SKU</h4>
			<p class="card-text">Show physically present stock grouped by SKU and warehouse <br><center><a href="<?php hasAccessForUrl('stockbyskucount.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>
		</div>
		<td width=10> <td>
	</div>	
		<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title">Decom</h4>
			<p class="card-text">Lijst van alle gedecommissioneerde toestellen in SP2<br><center><a href="<?php hasAccessForUrl('decomlist.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>
		</div>
	</div>
	</div>	
		<div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
		<div class="card-header">Internal</div>
		<div class="card-body">
			<h4 class="card-title"># to deliver by SKU</h4>
			<p class="card-text">Bennies favorite : all open order by SKU (no school detail)<br><center><a href="<?php hasAccessForUrl('BennyNodigBYOD.php'); ?>"><button class='btn-success'> Show &nbsp</button></a>&nbsp &nbsp<a href=https://orders.signpost.site/StockOrders.php><button class='btn-success'> STOCK Orders &nbsp</button></a>
		</div>
	</div>
	</table>
</table>

</div>

<?php
include('footer.php');
?>
