<?php

$title = 'Field Services';
include('head.php');
include('nav.php');
include('conn.php');

?>

<div class="body container">

<?php if(isset($_GET['1'])){ ?>

<h1>Nieuw defect aanmelden</h1>
<br>

<form class="">
	<div class="form-group card">
		<div class="card-header">
			<h4 class="my-0 font-weight-normal">Toestel</h4>
		</div>

		<div class="card-body">
			<label for="serial">Serienummer</label>
			<input id="serial" name="serial" type="text" class="form-control"><br>
			<div class="card flex-md-row mb-4 box-shadow h-md-250">
				<div class="card-body d-flex flex-column align-items-start">
					<h3 class="mb-0">
						<a class="text-dark" href="#">HP Chromebook x360 11 G3</a>
					</h3>
					<div class="mb-1 text-muted">15T03ES#UUG</div>
						<p class="card-text mb-auto">
							11.6" HD BV UWVA 220 touch screen<br>
							Glass 3, 220 nits, 45% NTSC (1366 x 768);<br>
							Intel® Celeron® N4120 (1.1 GHz 4 MB cache, 4 cores)<br>
							Intel® UHD Graphics 600<br>
							4 GB LPDDR4-2400 SDRAM soldered down Memory<br>
							32 GB eMMC 5.0<br>
						</p>
					</div>
					<img class="card-img-right flex-auto d-none d-md-block" alt="Thumbnail [200x250]" style="height: 250px;" src="https://personeel.doko-signpost.eu/media/catalog/product/cache/d17a0e9c22c7f115450e34046f21348b/1/5/15t03es_uug2_2.jpg" data-holder-rendered="true">
				</div>
				<label class="col-form-label">Type probleem</label>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_4" type="radio" class="custom-control-input" value="idk">
						<label for="type_4" class="custom-control-label">Geen idee</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
					<input name="type" id="type_0" type="radio" class="custom-control-input" value="water">
					<label for="type_0" class="custom-control-label">Waterschade</label>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_1" type="radio" class="custom-control-input" value="panel">
						<label for="type_1" class="custom-control-label">Schermbreuk</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_2" type="radio" class="custom-control-input" value="bsod">
						<label for="type_2" class="custom-control-label">Blauw scherm met windows foutcode (BSOD)</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_3" type="radio" class="custom-control-input" value="charge">
						<label for="type_3" class="custom-control-label">Laad niet op</label>
					</div>
				</div>
				<div class="custom-controls-stacked">
					<div class="custom-control custom-radio">
						<input name="type" id="type_5" type="radio" class="custom-control-input" value="sound">
						<label for="type_5" class="custom-control-label">Geluid werkt niet</label>
					</div>
				</div>
			</div>
			<br>
			<label for="problemDesc">Beschrijving van probleem</label>
			<textarea id="problemDesc" name="problemDesc" cols="40" rows="5" class="form-control"></textarea>
		</div>
	</div>

	<div class="form-group card">
		<div class="card-header">
			<h4 class="my-0 font-weight-normal">Contact informatie</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="firstName">Voornaam</label>
					<input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
					<div class="invalid-feedback">
						Valid first name is required.
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<label for="lastName">Achternaam</label>
					<input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
					<div class="invalid-feedback">
						Valid last name is required.
					</div>
				</div>
			</div>
			<div class="mb-3">
				<label for="email">E-mail</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<div class="input-group-text">
							<i class="fa fa-at"></i>
						</div>
					</div>
					<input id="email" name="email" type="text" class="form-control">
				</div>
				<div class="invalid-feedback">
					Please enter a valid email address for shipping updates.
				</div>
			</div>
			<div class="mb-3">
				<label for="phone">Telefoon</label>
				<div class="input-group">
					<div class="input-group-prepend">
						<div class="input-group-text">
							<i class="fa fa-phone"></i>
						</div>
					</div>
					<input id="phone" name="phone" type="text" class="form-control">
				</div>
				<div class="invalid-feedback">
					Please enter a valid phone address for shipping updates.
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 mb-3">
					<label for="country">Land</label>
					<select class="custom-select d-block w-100" id="country" required="">
						<option value="">Choose...</option>
						<option>België</option>
						<option>Nederland</option>
					</select>
					<div class="invalid-feedback">
						Please select a valid country.
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<label for="zip">Postcode</label>
					<input type="text" class="form-control" id="zip" placeholder="" required="">
					<div class="invalid-feedback">
						Zip code required.
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<label for="city">Gemeente</label>
					<input type="text" class="form-control" id="city" placeholder="" required="">
					<div class="invalid-feedback">
						Gemeente is verplicht
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 mb-3">
					<label for="address">Straat</label>
					<input type="text" class="form-control" id="address" placeholder="" required="">
					<div class="invalid-feedback">
						Please enter your shipping address.
					</div>
				</div>
				<div class="col-md-4 mb-3">
					<label for="address">Nummer</label>
					<input type="text" class="form-control" id="address2" placeholder="">
				</div>
			</div>
		</div>
	</div>

	<div class="form-group card">
		<div class="card-header">
			<h4 class="my-0 font-weight-normal">Bijlage</h4>
		</div>
		<div class="card-body">
			<label for="text">Hier kan u extra foto's of informatie uploaden die kan helpen bij de probleembeschrijving.</label><br>
			<div class="file-loading">
				<input id="bfupload-b6" name="bfupload-b6[]" type="file" multiple>
			</div>
			<script>
				$(document).ready(function() {
					$("#bfupload-b6").fileinput({
						showUpload: false,
						dropZoneEnabled: false,
						maxFileCount: 10,
						mainClass: "bfupload-group-lg"
					});
				});
			</script>
		</div>
	</div>

	<div class="form-group">
		<button name="submit" type="submit" class="btn btn-lg btn-block btn-primary">Doorsturen</button>
	</div>

</form>

<br>
<br>
<br>
<br>

<?php } ?>

<?php if(isset($_GET['2'])){ ?>
<h1>Technieker plant de repair in</h1>
<form>
  <div class="form-group">
    <label for="desc">Beschrijving case</label>
      <textarea id="desc" name="desc" cols="40" rows="5" class="form-control" aria-describedby="descHelpBlock" readonly>Status, Serienummer, synergyid, school, adres, factuur, garantie</textarea>
  </div>
  <div class="form-group">
		<label for="shortDescription">Korte beschrijving</label>
		<input id="shortDescription" name="shortDescription" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="date">Datum van repair</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i class="fa fa-calendar"></i>
          </div>
        </div>
        <input id="date" name="date" placeholder="datum + uur" type="text" class="form-control">
      </div>
  </div>
  <div class="form-group">
    <label for="technician">Technieker (indien genoeg rechten)</label>
      <select id="technician" name="technician" required="required" class="custom-select">
		<option value="Bart">Bart</option>
		<option value="Bruno">Bruno</option>
		<option value="Ward">Ward</option>
		<option value="Styn">Styn</option>
		<option value="Davy">Davy</option>
		<option value="Felix">Felix</option>
		<option value="Jo">Jo</option>
		<option value="Tibo">Tibo</option>
		<option value="Sander">Sander</option>
		<option value="Michael">Michael</option>
		<option value="Geoffrey">Geoffrey</option>
		<option value="Ronny">Ronny</option>
		<option value="Abdel">Abdel</option>
		<option value="Pieterjan">Pieterjan</option>
		<option value="Anek">Anek</option>
      </select>
  </div>
  <div class="form-group">
      <button name="submit" type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
<?php } ?>

<?php if(isset($_GET['3'])){ ?>
<h1>Technieker werkt de case af</h1>
<form>
  <div class="form-group">
    <label for="shortDescription">Korte beschrijving</label>
    <input id="shortDescription" name="shortDescription" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="desc">Beschrijving case</label>
    <textarea id="desc" name="desc" cols="40" rows="5" aria-describedby="descHelpBlock" class="form-control" readonly>Status, Serienummer, synergyid, school, adres, factuur, garantie</textarea>
  </div>
  <div class="form-group">
    <label for="date">Datum van repair</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            <i class="fa fa-calendar"></i>
          </div>
        </div>
        <input id="date" name="date" placeholder="datum + uur" type="text" class="form-control">
      </div>
  </div>
  <div class="form-group">
    <label for="text2">Casenummer</label>
    <input id="text2" name="text2" placeholder="hp, lenovo, .. case" type="text" required="required" class="form-control">
  </div>
  <div class="form-group">
    <label for="part1">Onderdeel 1</label>
    <div>
      <select id="part1" name="part1" class="custom-select">
        <option value="ssd">SP-SSD-....</option>
        <option value="part">part</option>
        <option value="ram">ram</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label></label>
    <input id="text1" name="text1" placeholder="+" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="reason">Oorzaak</label>
    <div>
      <select id="reason" name="reason" required="required" class="custom-select">
        <option value="user">Fout van de gebruiker</option>
        <option value="age">Leeftijd</option>
        <option value="3thparty">3de partij</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="textarea">Opmerkingen</label>
    <textarea id="textarea" name="textarea" cols="40" rows="5" class="form-control"></textarea>
  </div>
  <div class="form-group">
    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
<?php } ?>

<?php if(isset($_GET['4'])){ ?>
<h1>Technieker geeft een opmerking</h1>
<form>
  <div class="form-group">
    <label for="desc">Beschrijving case</label>
    <textarea id="desc" name="desc" cols="40" rows="5" aria-describedby="descHelpBlock" class="form-control" readonly>Status, Serienummer, synergyid, school, adres, factuur, garantie</textarea>
  </div>
  <div class="form-group">
    <label for="note">Type opmerking</label>
    <div>
      <select id="note" name="note" class="custom-select">
        <option value="internal">Intern</option>
        <option value="external">Extern</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="textarea">Opmerking</label>
    <textarea id="textarea" name="textarea" cols="40" rows="5" class="form-control"></textarea>
  </div>
  <div class="form-group">
    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
<?php } ?>

