<?php

$title = 'Exact Facturatie';
include('head.php');
include('nav.php');
include('mssql-001-conn.php');
include('mssql-100-conn.php');

if(isset($_POST['search']) == false){
	die('Geen zoekwaarde');
}
?>

<div class="body">

	<table class="table" id="table">
		<thead class="thead-dark">
			<tr>
				<th scope="col">Factuurnummer</th>
				<th scope="col">Serienummer</th>
				<th scope="col">Artikel Code</th>
				<th scope="col">Omschrijving</th>
				<th scope="col">Klantnummer</th>
				<th scope="col">Klantnaam</th>
				<th scope="col">Datum</th>
				<th scope="col">Extra garantie</th>
				<th scope="col">DVD</th>
				<th scope="col">SSD</th>
				<th scope="col">HDD</th>
				<th scope="col">FHD</th>
				<th scope="col">Verzendnota</th>
				<th scope="col">Factuur</th>
			</tr>
		</thead>

		<tbody>
	<?php

	$tsql= "select *,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%garan%' or oms45 like '%warr%')) as ExtraGarantie,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%dvd%' )) as DVD,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%ssd%' )) as SSD,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%hdd%' )) as HDD,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%fhd up%' )) as FHD
	from _serienummer_view with (nolock)
	where Klantnaam LIKE '%" . $_POST['search'] . "%'
	OR factuurnr = '" . $_POST['search'] . "'
	OR artikelcode = '" . $_POST['search'] . "'
	OR Klantnummer = '" . $_POST['search'] . "'
	OR serienummer LIKE '" . $_POST['search'] . "%'";
	$stmt = sqlsrv_query( $msconn, $tsql);

	if($stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
		echo '
			<tr>
				<td scope="col">' . $row['factuurnr'] . '</a></td>
				<td scope="col">' . $row['Serienummer'] . '</td>
				<td scope="col">' . $row['ArtikelCode'] . '</td>
				<td scope="col">' . $row['ArtikelOmschrijving'] . '</td>
				<td scope="col">' . $row['Klantnummer'] . '</td>
				<td scope="col">' . $row['Klantnaam'] . '</td>
				<td scope="col" data-sort="'. strtotime(date_format($row['datum'], 'd-m-Y')) .'">' . date_format($row['datum'], 'd-m-Y') . '</td>
				<td scope="col">' . $row['ExtraGarantie'] . '</td>
				<td scope="col">' . $row['DVD'] . '</td>
				<td scope="col">' . $row['SSD'] . '</td>
				<td scope="col">' . $row['HDD'] . '</td>
				<td scope="col">' . $row['FHD'] . '</td>
				<td scope="col"><a target="_blank" href="https://synergy.signpost.site/synergy/docs/DocBinBlob.aspx?Download=1&ID=' . $row['verzendnota'] . '&AttId=&Division=100">Klik hier</a></td>
				<td scope="col"><a target="_blank" href="https://synergy.signpost.site/synergy/docs/DocBinBlob.aspx?Download=1&ID=' . $row['factuur'] . '&AttId=&Division=100">Klik hier</a></td>
			</tr>
		';
	}

	$tsql= "select *,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%garan%' or oms45 like '%warr%')) as ExtraGarantie,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%dvd%' )) as DVD,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%ssd%' )) as SSD,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%hdd%' )) as HDD,
	(select top 1 oms45 from frhsrg with (nolock) where frhsrg.faknr=factuurnr and ar_soort not in ('P') and (oms45 like '%fhd up%' )) as FHD
	from _serienummer_view with (nolock)
	where Klantnaam LIKE '%" . $_POST['search'] . "%'
	OR factuurnr = '" . $_POST['search'] . "'
	OR artikelcode = '" . $_POST['search'] . "'
	OR Klantnummer = '" . $_POST['search'] . "'
	OR serienummer LIKE '" . $_POST['search'] . "%'";
	$stmt = sqlsrv_query( $msconn001, $tsql);

	if($stmt === false) {
		die( print_r( sqlsrv_errors(), true) );
	}

	while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
		echo '
			<tr>
				<td scope="col">' . $row['factuurnr'] . '</a></td>
				<td scope="col">' . $row['Serienummer'] . '</td>
				<td scope="col">' . $row['ArtikelCode'] . '</td>
				<td scope="col">' . $row['ArtikelOmschrijving'] . '</td>
				<td scope="col">' . $row['Klantnummer'] . '</td>
				<td scope="col">' . $row['Klantnaam'] . '</td>
				<td scope="col" data-sort="'. strtotime(date_format($row['datum'], 'd-m-Y')) .'">' . date_format($row['datum'], 'd-m-Y') . '</td>
				<td scope="col">' . $row['ExtraGarantie'] . '</td>
				<td scope="col">' . $row['DVD'] . '</td>
				<td scope="col">' . $row['SSD'] . '</td>
				<td scope="col">' . $row['HDD'] . '</td>
				<td scope="col">' . $row['FHD'] . '</td>
				<td scope="col"><a target="_blank" href="http://81.246.60.76:8080/synergy/docs/DocBinBlob.aspx?Download=1&ID=' . $row['verzendnota'] . '">Klik hier</a></td>
				<td scope="col"><a target="_blank" href="http://81.246.60.76:8080/synergy/docs/DocBinBlob.aspx?Download=1&ID=' . $row['factuur'] . '">Klik hier</a></td>
			</tr>
		';
	}

	?>
</tbody>

</table>

</div>

<?php
include('footer.php');
?>
