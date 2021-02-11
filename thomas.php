<?php

$title = 'STT - Signpost Thomas Tools';

include('head.php');
include('nav.php');
include('readonly-conn.php');

?>

<div class="body">
<p align=center>

<table width=75% border="0">
	<th colspan=7 ><p align=center><B><font size=8 color=#00ADBD> Thomas' SP² Pages</th>
		<tr>
		<td>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style="background: #00ADBD">
				<div class="card-header">Exact</div>
				<div class="card-body">
					<h4 class="card-title">PowerPivot</h4>
					<p class="card-text">For a given reference, give all orders from Exact.<br><center><a href="<?php hasAccessForUrl('powerpivot.php'); ?>" target="_blank"><button class='btn-secondary'> Show &nbsp</button></a>
			</div>
		</div>
	</div>
		<div class="card text-white mb-3" style="max-width: 20rem;">
			<div style="background: #00ADBD">
				<div class="card-header">Exact</div>
				<div class="card-body">
					<h4 class="card-title">PowerPivot BYOD.</h4>
					<p class="card-text">For a given reference, give all BYOD orders from Exact.<br><center><a href="<?php hasAccessForUrl('powerpivotBYOD.php'); ?>" target="_blank"><button class='btn-secondary'> Show &nbsp</button></a>
				</div>
			</div>
		<td width=10> <td>
	</div>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style ="background: #e77443">
			<div class="card-header">Leermiddel</div>
			<div class="card-body">
				<h4 class="card-title">Leermiddel OK Contracts</h4>
				<p class="card-text">For a given number of days, shows all paid and signed Leermiddel contracts.<br><center><a href="<?php hasAccessForUrl('leermiddel-aantalcontracten.php'); ?>" target="_blank"><button class='btn-secondary'> Show &nbsp</button></a>
			</div>
		</div>
	</div>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style ="background: #e77443">
				<div class="card-header">Leermiddel</div>
				<div class="card-body">
					<h4 class="card-title">Leermiddel Contracts</h4>
					<p class="card-text">For a given name, contractnumber or label, show all (not deleted) contracts.<br><center><a href="<?php hasAccessForUrl('leermiddel-contracten.php'); ?>" target="_blank"><button class='btn-secondary'> Show &nbsp</button></a>
				</div>
			</div>
		<td width=10> <td>
	</div>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style ="background: #e77443">
				<div class="card-header">Leermiddel</div>
				<div class="card-body">
					<h4 class="card-title">Leermiddel Graphs</h4>
					<p class="card-text">All useful Leermiddel numbers in a visual representation.<br><center><a href="<?php hasAccessForUrl('leermiddel-grafieken.php'); ?>" target="_blank"><button class='btn-secondary'> Show &nbsp</button></a>
				</div>
		</div>
	</div>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style="background: #8dc154">
			<div class="card-header">Work in Progress</div>
			<div class="card-body">
				<h4 class="card-title">This page has yet to be developed.</h4>
				<p class="card-text">Working hard on having this page online as soon as possible.<br><center>
			</div>
		</div>
		<td width=10> <td>
	</div>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style="background: #8dc154">
			<div class="card-header">Work in Progress</div>
			<div class="card-body">
				<h4 class="card-title">This page has yet to be developed.</h4>
				<p class="card-text">Working hard on having this page online as soon as possible.<br><center>
			</div>
		</div>
	</div>
	<div class="card text-white mb-3" style="max-width: 20rem;">
		<div style="background: #8dc154">
			<div class="card-header">Work in Progress</div>
			<div class="card-body">
				<h4 class="card-title">This page has yet to be developed.</h4>
				<p class="card-text">Working hard on having this page online as soon as possible.<br><center>
			</div>
		</div>
	</div>
</div>
	</table>
</table>

</div>

<?php
include('footer.php');
?>
