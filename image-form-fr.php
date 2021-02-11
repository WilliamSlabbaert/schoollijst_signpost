<style>

* {
	box-sizing: border-box;
}

body {
	background-color: #f1f1f1;
}

#regForm {
	margin: 0 auto;
	padding: 40px;
	min-width: 300px;
}

h1 {
	text-align: center;
}

input {
	padding: 10px;
	width: 100%;
	font-size: 17px;
	border: 1px solid #aaaaaa;
}

/* Mark input boxes that gets an error on validation: */
input.invalid {
	background-color: #ffdddd;
}

/* Hide all steps by default: */
.tab {
	display: none;
}

button {
	background-color: #00ADBD;
	color: #ffffff;
	border: none;
	padding: 10px 20px;
	font-size: 17px;
	font-family: Raleway;
	cursor: pointer;
}

button:hover {
	opacity: 0.8;
}

#prevBtn {
	background-color: #bbbbbb;
}

/* Make circles that indicate the steps of the form: */
.step {
	height: 15px;
	width: 15px;
	margin: 0 2px;
	background-color: #bbbbbb;
	border: none;
	border-radius: 50%;
	display: inline-block;
	opacity: 0.5;
}

.step.active {
	opacity: 1;
}

/* Mark the steps that are finished and valid: */
.step.finish {
	background-color: #00ADBD;
}
</style>
<?php
require 'vendor/autoload.php';

$title = 'Image Form';
include('head.php');
include('nav.php');
include('conn.php');

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
ini_set('SMTP', 'localhost');
ini_set('smtp_port', 25);

	//check if query string
	if (isset($_POST['submit'])) {
		//print_r($_POST);

		$synergyid = mysqli_real_escape_string($conn, $_POST['synergy']);
		$guid = mysqli_real_escape_string($conn, $_POST['GUID']);
		$SPSKUstring = implode(";",$_POST['toestel']);
		$SPSKU = mysqli_real_escape_string($conn, $SPSKUstring);
		$name = mysqli_real_escape_string($conn, $_POST['imagenaam']);
		$type = mysqli_real_escape_string($conn, $_POST['imagekeuze']);
		$authentication = mysqli_real_escape_string($conn, $_POST['authenticatie']);
		$authentication_info_string = $_POST['lokaal'] . $_POST['vpn'] .$_POST['accountsvpn'];
		$authentication_info = mysqli_real_escape_string($conn, $authentication_info_string);
		// $free_software = print_r($_POST['customgratissoftware']);
		// $paid_software = print_r($_POST['customsoftware']);
		$emse3 = mysqli_real_escape_string($conn, $_POST['e3check']);
		// $intunescripting =
		$computername = mysqli_real_escape_string($conn, $_POST['hostname']);
		$notes = mysqli_real_escape_string($conn, $_POST['comment']);
		$contactname = mysqli_real_escape_string($conn, $_POST['contactnaam']);
		$contacttel = mysqli_real_escape_string($conn, $_POST['contacttel']);
		$contactemail = mysqli_real_escape_string($conn, $_POST['contactemail']);
		$deliverydate = mysqli_real_escape_string($conn, $_POST['deliverydate']);
		if (isset($customimageaanmaak) == true) {
			$type = $type . " - " . $_POST['customimageaanmaak'];
		}
		$type = mysqli_real_escape_string($conn, $type);

		$customgratissoftwarestring = implode(";",$_POST['customgratissoftware']);
		$customgratissoftware = mysqli_real_escape_string($conn, $customgratissoftwarestring);
		$customsoftwarestring = implode(";",$_POST['customsoftware']);
		$customsoftware = mysqli_real_escape_string($conn, $customsoftwarestring);

		$assessmentq = mysqli_real_escape_string($conn, $_POST['assessmentq']);
		$assessmentqplatform = mysqli_real_escape_string($conn, $_POST['assessmentqplatform']);
		$assessmentqlogin = mysqli_real_escape_string($conn, $_POST['assessmentqlogin']);


		$sql = "INSERT INTO images2020 (synergyid, guid, SPSKU, name, type, authentication, authentication_info, free_software, paid_software, emse3, computername, notes, contactname, contacttel, contactemail, deliverydate, assessmentq, assessmentqplatform, assessmentqlogin)
		VALUES ('" . $synergyid . "', '" . $guid . "', '" . $SPSKU . "', '" . $name . "', '" . $type . "', '" . $authentication . "', '" . $authentication_info . "', '" . $customgratissoftware . "', '" . $customsoftware . "', '" . $emse3 . "', '" . $computername . "', '" . $notes . "', '" . $contactname . "', '" . $contacttel . "', '" . $contactemail . "', '" . $deliverydate . "' , '" . $assessmentq . "', '" . $assessmentqplatform . "', '" . $assessmentqlogin . "')";

		if ($conn->query($sql) === TRUE) {
			echo 'Merci d\'avoir rempli les informations relatives à votre image. <a href="'. hasAccessForUrl('image-replies.php?guid=' . $_POST['GUID'] . '', false).'">Cliquez ici pour les vérifier et approuver.</a>';

			$message="Madame, Monsieur,

Nous avons bien reçu votre demande pour le formulaire d\'image BYOD 2020.
Veuillez vérifier et confirmer les données via le lien ci-dessous :

productie.signpost.site/image-replies.php?guid=" . $_POST['GUID'] ."

Merci d\'avance,

L\'équipe Signpost";


			$email = new \SendGrid\Mail\Mail();
			$email->setFrom("byod@signpost.eu", "Signpost BYOD");
			$email->setSubject("Confirmation de votre image");
			$email->addTo($contactemail);
			// $email->addCc("nova@signpost.eu");
			$email->addContent(
				"text/plain", $message
			);
			$sendgrid = new \SendGrid('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');
			try {
				$response = $sendgrid->send($email);
			} catch (Exception $e) {
				echo 'Caught exception: '. $e->getMessage() ."\n";
			}

		} else {

			echo "Madame, Monsieur,<br>
			Il y a eu un problème avec la soumission.<br>
			Veuillez contacter softwaresupport@signpost.eu.<br>
			Nous vous aiderons dans les plus brefs délais.";

			$sqlerror = "Error: " . $sql . "<br>" . $conn->error;
			$sqlerror = mysqli_real_escape_string($conn, $sqlerror);
			$ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);
			$sql = "INSERT INTO errors (error, ip) VALUES ('" . $sqlerror . "', '" . $ip . "')";
			if ($conn->query($sql) === TRUE) {
				echo "";
			}

		}


		die();
	} elseif(!isset($_GET['q'])){

		echo "Madame, Monsieur,<br>
		Il y a eu un problème avec la soumission.<br>
		Veuillez contacter softwaresupport@signpost.eu.<br>
		Nous vous aiderons dans les plus brefs délais.";

	} else {
		//check if geldig
		$sql = "SELECT * FROM schools where GUID = '".$_GET['q']."'";
		$result = $conn->query($sql);
		if ($result->num_rows == 0) {

			echo "Madame, Monsieur,<br>
			Il y a eu un problème avec la soumission.<br>
			Veuillez contacter softwaresupport@signpost.eu.<br>
			Nous vous aiderons dans les plus brefs délais.";

		} else {  //--> geldig

			$sql2 = "SELECT * FROM schools where guid = '".$_GET['q']."'";
			$result2 = $conn->query($sql2);
			if ($result2->num_rows > 0) {
				while($q = $result2->fetch_assoc()){

					$school_name = $q['school_name'];
					$city = $q['city'];
					$synergyid = $q['synergyid'];
					$guid = $q['guid'];
					//print_r($q);
				}
			}

		?>

		<main role="main" class="container col-md-6">
			<form action="image-form.php" id="regForm" method="post">

			<h3>BYOD 2020 Formulaire d’orientation <?php echo $school_name; ?></h3>
			<h6 class="mb-4"><strong>Si vous avez des questions ou des doutes, vous pouvez toujours nous contacter au numéro <a href="03 500 49 28" class="blue">03 500 49 28</a>.</strong></h6>
			<hr>
			<input type="text" hidden name="synergy" value="<?php echo $synergyid; ?>">
			<input type="text" hidden name="GUID" value="<?php echo $guid; ?>">
			<input type="text" hidden name="schoolname" value="<?php echo $school_name; ?>">
			<input type="text" hidden name="plaats" value="<?php echo $city; ?>">

			<div class="tab">
				<!-- keuze image -->
				<div class="form-group mb-4" id="imagekeuze">
					<h5 for="">Type d’image*</h5>
					<small class="form-text pb-1" >
						<p>Quelle image doit être prévue sur les appareils ? <br>
						Si vous choisissez ‘Nous (l’école) créons notre propre image’, celle-ci doit être téléchargée <strong>avant le 20/06/2021</strong>. Vous recevrez un courrier séparé avec les instructions. <br></p>
					</small>
					<div class="custom-control custom-radio" >
						<input type="radio" id="customRadio1" name="imagekeuze" value="geen" class="custom-control-input verify" required>
						<label class="custom-control-label" for="customRadio1" style="text-decoration:underline;text-decoration-color: #00ADBD;">Pas d’image - image originale du fournisseur (HP/Lenovo) </label>
					</div>
					<small class="form-text pb-1" id="geenimage">
					Cette image comprend Windows 10, ainsi que certains logiciels spécifiques de HP ou Lenovo. <br><br>
					</small>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio2"  name="imagekeuze" value="school" class="custom-control-input">
						<label class="custom-control-label" for="customRadio2" style="text-decoration:underline;text-decoration-color: #00ADBD;">Nous (l’école) créons notre propre image  </label>
						<br><br>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio3" name="imagekeuze" value="1" class="custom-control-input">
						<label class="custom-control-label" for="customRadio3" style="text-decoration:underline;text-decoration-color: #00ADBD;">ACSW-IP1</label>
					</div>
					<small class="form-text pb-1" id="bundel1">
					Le paquet d’images 1 d’Academic Software comprend les logiciels et fonctionnalités suivants :
											<ul>
							<li>Windows 10 Education Pro - sans bloatware (version 1909)</li>
							<li>.NET Framework 3.5</li>

						</ul>
					</small>
					<!-- <div class="custom-control custom-radio">
						<input type="radio" id="customRadio4" name="imagekeuze" value="2" class="custom-control-input">
						<label class="custom-control-label" for="customRadio4" style="text-decoration:underline;text-decoration-color: #00ADBD;">Signpost image Pack 2</label>
					</div>
					<small class="form-text pb-1" id="bundel2">
						De Signpost Image Pack 2 bevat <strong>Signpost Image Pack 1 + de volgende software & features:</strong>
						<ul>
							<li>Office 365</li>
							<li>Google Chrome</li>
							<li>Mozilla Firefox</li>
							<li>Acrobat Reader</li>
							<li>Adobe Flash</li>
							<li>Microsoft Silverlight</li>
							<li>Microsoft Visual C++ Redist packages</li>
							<li>Java RE x86 & x64</li>
						</ul>
					</small> -->
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio5" name="imagekeuze" value="3" class="custom-control-input">
						<label class="custom-control-label" for="customRadio5" style="text-decoration:underline;text-decoration-color: #00ADBD;">ACSW-IP3</label>
					</div>
					<small class="form-text pb-1" id="bundel3">
					Le paquet d’images 3 d’Academic Software comprend le <strong>paquet d’images 1 + les logiciels et fonctionnalités suivants :</strong>
						<ul>
							<li>7-Zip</li>
							<li>GeoGebra Classic</li>
							<li>VideoLAN VLC Player</li>
							<li>PDF Split and Merge</li>
							<li>Microsoft Teams</li>
							<li>TeamViewer</li>
							<li>Office 365</li>
							<li>Google Chrome</li>
							<li>Mozilla Firefox</li>
							<li>Acrobat Reader</li>
							<li>Adobe Flash</li>
							<li>Microsoft Silverlight</li>
							<li>Microsoft Visual C++ Redist packages</li>
							<li>Java RE x86 & x64</li>
							<li>Safe Exam Browser</li>

						</ul>
					</small>
					<div class="custom-control custom-radio">
						<input type="radio" id="customRadio6" name="imagekeuze" value="custom" class="custom-control-input">
						<label class="custom-control-label" for="customRadio6" style="text-decoration:underline;text-decoration-color: #00ADBD;">Image personnalisée </label>
						<small class="form-text pb-1" id="custom">
						Les options ci-dessus ne suffisent pas. Nous souhaitons une image entièrement personnalisée.  <br>
						Cette image est basée sur le <strong>paquet d’images 3 d’Academic Software. </strong>
						</small>
					</div>
				</div>
			</div>

			<div class="tab">

				<!-- custom image naam bij meerdere -->
				<div class="form-group mb-4" id="naamimage" > <!-- style="display:none" -->
					<h5 for="imagenaam" id="imagename">Nom de l’image*</h5>
					<small class="form-text pb-1">Par exemple : Professeur, Étudiant, AV, IWEM, STIM, Bureau, Industrie… </small>
					<input type="text" name="imagenaam" class="form-control verify" id="imagenaam"  placeholder="" required>
				</div>
				<br>

				<!-- school image info -->
				<div class="form-group mb-4" id="school"> <!-- style="display:none" -->
					<h5 for="contactemail">Comment allez-vous créer l’image ?*</h5></br>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio2" name="customimageaanmaak" value="Clonezilla" class="custom-control-input verify">
						<label class="custom-control-label" for="custom1Radio2">Image Clonezilla (notre préférence) -  </label> <a href="Clonezilla.pdf">manuel Clonezilla </a>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio1" name="customimageaanmaak" value="Demo" class="custom-control-input">
						<label class="custom-control-label" for="custom1Radio1">Image configurée sur un appareil de démonstration </label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio3" name="customimageaanmaak" value="WDS" class="custom-control-input">
						<label class="custom-control-label" for="custom1Radio3">WDS MDT</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="custom1Radio4" name="customimageaanmaak" value="imagedonderdag" class="custom-control-input">
						<label class="custom-control-label" for="custom1Radio4">Lors de l’un des jeudis d’imagerie organisé par Signpost </label>
					</div>
				</div>

				<!-- custom image aantal -->
				<!--<div class="form-group mb-4" style="" id="meerdere">
					<h5 for="contactemail">Wensen jullie één image of meerdere?*</h5>
						<small class="form-text pb-1">Indien meerdere images gewenst zijn dient men per image een aparte aanvraag in te dienen. Dit kan je doen met dezelfde link
					</small>
					<div class="custom-control custom-radio">
						<input type="radio" id="meerdere1" required name="multiple" value="0" class="custom-control-input">
						<label class="custom-control-label" for="meerdere1">één image</label>
					</div>
					<div class="custom-control custom-radio">
						<input type="radio" id="meerdere2" name="multiple" value="1" class="custom-control-input">
						<label class="custom-control-label" for="meerdere2">
							meerdere images
					</div>
				</div> -->

				<div class="form-group mb-4" id="gratissoftwarediv">
					<h5 for="exampleInputEmail1">Logiciels gratuits</h5>
					<small class="form-text pb-1">Cliquez sur le champ de recherche, cherchez le logiciel que vous souhaitez inclure dans l’image et appuyez sur <em>ENTER</em>.</small>
					<select id="SoftwareSelect" multiple="multiple" class="form-control" name="customgratissoftware[]" style="width:100% !important;">
					<?php
						$sql = "SELECT * FROM software ORDER BY naam asc";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								echo "<option value='".$row['naam']."'>".$row['naam']."</option>";
							}
						}

						?>
					</select>
				</div>
				<br>

				<div class="form-group mb-4" id="customsoftwarediv">
					<h5 for="">Si vous ne trouvez pas le logiciel dans la liste susmentionnée, vous pouvez l’ajouter ci-dessous.</h5>
					<small class="form-text pb-1" >
						<p>Veuillez indiquer clairement le nom de chaque logiciel, ainsi que le lien de téléchargement et les informations relatives à la licence.</p>
					</small>
					<div id="customsoftware"></div>
						<button type="button" class="btn btn-primary" id="addcustomsoftware" onclick="GenerateCustomSoftwareField()">Ajouter des logiciels supplémentaires</button>
					</div>
				</div>
			</div>

			<div class="tab">
				<!-- auth info -->
				<div class="form-group mb-4" id="bundel"> <!-- style="display:none" -->
					<div class="form-group mb-4" id="authenticatie">
						<h5 for="">Authentification*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="custom2Radio1" name="authenticatie" value="lokaal" class="custom-control-input">
							<label class="custom-control-label" for="custom2Radio1">Comptes locaux, pas de lien avec Active Directory, Azure AD ou Intune </label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="custom2Radio2" name="authenticatie" value="DomainJoined" class="custom-control-input">
							<label class="custom-control-label" for="custom2Radio2">Joint au domaine, lien avec Active Directory </label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="custom2Radio3" name="authenticatie" value="intune" class="custom-control-input">
							<label class="custom-control-label" for="custom2Radio3">Signpost Intune + Signpost OOBE
							</label>
						</div>
					</div>

					<!-- local info -->
					<div class="form-group mb-4" id="lokaal"> <!-- style="display:none" -->
						<div class="form-group mb-4">
							<h5 for="contactnaam">Comptes locaux</h5>
							<!-- <a class="" data-toggle="collapse" role="button" type="button" href="#collapse2" aria-expanded="false">
							Meer info bij lokale accounts...
							</a> -->
							<small class="form-text pb-1" id="collapse2">
								<p>Les comptes suivants sont fournis par défaut :</br>
								(la liste ci-dessous est en format CSV, avec la première ligne comme exemple)
								</p>

								<table width="400px" class="table">
									<tr>
										<th>AccountName</th>
										<th>Password</th>
										<th>Group</th>
									</tr>
									<tr>
										<td>SchoolAdmin</td>
										<td>Not4UAll</td>
										<td>Administrator</td>
									</tr>
									<tr>
										<td>Leerling</td>
										<td>/</td>
										<td>User</td>
									</tr>
								</table>

								<p>Si ces comptes doivent être modifiés ou complétés, cela peut être précisé ici dans le même format.</p>
							</small>
							<textarea rows="3" name="lokaal" class="form-control verify" id="lokaaltext"  placeholder=""></textarea>
						</div>
					</div>

					<!-- domein info -->
					<div class="form-group mb-4" id="vpn"> <!-- style="display:none" -->
						<div class="form-group mb-4">
							<h5 for="contactnaam">Connexion VPN au contrôleur de domaine</h5>
							<!-- <a class="" data-toggle="collapse" role="button" type="button" href="#collapse3" aria-expanded="false">
							Meer info bij VPN Connectie naar Domain Controller...
							</a> -->
							<small class="form-text pb-1" id="collapse3">
								<strong>Données que vous pouvez saisir sur votre pare-feu :</strong>
								<p>Phase 1:</br>
									IKEv2
									Remote Gateway: 109.135.16.180</br>
									Encryption: <strong>seulement</strong> AES (256 bits)</br>
									Hash: <strong>seulement</strong> SHA256</br>
									PFS / DH Key Group: DH Group 14 (2048 bits)</br>
									Lifetime 28800</br>

																	</p>
																	<p>Phase 2:</br>
									Remote Network Address: 10.1.6.0 / 24</br>
									Encryption: seulement AES (256 bits) </br>
									Hash: seulement SHA256</br>
									PFS / DH Key Group: 14</br>
									Lifetime: 27000

																	</p>
																	<p>Phase 2:</br>
									Remote Network Address: 192.168.124.0 / 24</br>
									Encryption: seulement AES (256 bits) </br>
									Hash: seulement SHA256</br>
									PFS / DH Key Group: 14</br>
									Lifetime: 27000

																	</p>
																	<p><strong>Les données dont nous avons besoin de votre part :</strong></br>
																	L’IP publique de votre pare-feu</br>
La clé pré-partagée (PSK)</br>
La plage d’adresses locale où se trouve votre serveur AD</br>
Le nom du serveur AD
L’adresse IP du serveur AD
L’unité d’organisation à laquelle vous voulez ajouter les appareils

									</p>
							</small>
							<textarea rows="3" name="vpn" class="form-control" id="vpntext"  placeholder="">L’IP publique de votre pare-feu
La clé pré-partagée (PSK)
La plage d’adresses locale où se trouve votre serveur AD
Le nom du serveur AD
L’adresse IP du serveur AD
L’unité d’organisation à laquelle vous voulez ajouter les appareils
</textarea>
						</div>

						<!-- domein 2 info -->
						<div class="form-group mb-4" id="accountsvpn" >
							<h5 for="accountsvpn" id="accountsvpn">Comptes pour Signpost</h5>
							<small class="form-text pb-1">Pour que tout se passe bien, nous avons besoin d’un compte Admin pour le domaine (si possible avec le nom ‘Signpost’), avec lequel nous pouvons ajouter des ordinateurs portables au domaine et établir une connexion bureau à distance au serveur Active Directory.<br>
							Nous aimerions également disposer d’un compte test équivalent à un compte étudiant (si possible avec le nom ‘Signpost.Test’), afin d’être sûrs que la procédure d’inscription pour les étudiants se déroulera sans problème.<br>
							Veuillez ne pas supprimer ces comptes. </small>
							<textarea rows="3" name="accountsvpn" class="form-control verify" id="accountsvpn"  placeholder=""></textarea>
						</div>
					</div>

					<!-- intune info -->
					<div class="form-group mb-4" id="intune"> <!-- style="display:none" -->
						<div class="form-group mb-4">
							<h5 for="contactnaam">Compte Administrateur Intune</h5>
							<!-- <a class="" data-toggle="collapse" role="button" type="button" href="#collapse3" aria-expanded="false">
							Meer info bij Intune beheerdersaccount...
							</a> -->
							<small class="form-text pb-1" id="collapse3">
							<p>Pour une configuration correcte de votre environnement Intune, il est recommandé de nous ajouter en tant qu’administrateur délégué sur votre portail. Ceci peut être fait via le lien ci-dessous :</p>
								<strong><p>
									<a href="https://businessstore.microsoft.com/manage/partner-invitation?invType=IndirectResellerRelationship&partnerId=6fff47dc-5f67-41b5-b48b-9d4c6de48eab&msppId=4794019&DAP=true" class="btn btn-primary" target="_blank">cliquez ici</a> </strong>
								</p>
								<p>Une fois que nous aurons l’accès, nous créerons un compte ‘Signpost.Test’. Veuillez ne pas le supprimer.</p>
							</small>
							<!-- <textarea rows="3" name="intune" class="form-control verify" id="intunetext"  placeholder=""></textarea> -->
						</div>

						J’ai une licence EMS E3.
						<div class="form-check">
							<input class="form-check-input" type="radio" name="e3check" id="exampleRadios1" value="1" style="width:200px">
							Oui
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="e3check" id="exampleRadios2" value="0" checked style="width:200px">
							Non
						</div>
					</div>

					<div class="form-group mb-4" id="assessmentq">
						<h5 for="">Souhaitez-vous utiliser assessmentQ ?*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio1" name="assessmentq" value="Ja" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio1">Oui</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio2" name="assessmentq" value="Nee" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio2">Non</label>
						</div>

					</div>
					<div class="form-group mb-4" id="assessmentqplatform">
						<h5 for="">Disposez-vous déjà d'une plate-forme pour assessmentQ ?*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio3" name="assessmentqplatform" value="Ja" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio3">Oui</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio4" name="assessmentqplatform" value="Nee" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio4">Non</label>
						</div>

					</div>
					<div class="form-group mb-4" id="assessmentqlogin">
						<h5 for="">Comment les élèves/enseignants se connectent-ils ?*</h5>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio5" name="assessmentqlogin" value="Office 365" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio5">Office 365</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio6" name="assessmentqlogin" value="Google" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio6">Google</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio7" name="assessmentqlogin" value="Smartschool" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio7">Smartschool</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="assessmentqRadio8" name="assessmentqlogin" value="Andere" class="custom-control-input">
							<label class="custom-control-label" for="assessmentqRadio8">Autre</label>
						</div>
					</div>
				</div>
			</div>

				<!-- verder deel -->
			<div class="tab">

					<div class="form-group mb-4">
						<h5 for="toestel">Pour le(s) appareil(s) :*</h5>
						<!-- <small class="form-text pb-1">Bijvoorbeeld: Lenovo L390 i3, HP 430 G6 Celeron, ...</small> -->
						<!-- <input type="text" name="toestel" required class="form-control verify" id="toestel"  placeholder=""> -->

						<select id="ToestelSelect" class="form-control" name="toestel[]" style="width:100% !important;" multiple="multiple">
					<?php
						$sql = "SELECT SUBSTRING_INDEX(`device1-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device1-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND deleted != 1 UNION
								SELECT SUBSTRING_INDEX(`device2-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device2-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND deleted != 1 UNION
								SELECT SUBSTRING_INDEX(`device3-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device3-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND deleted != 1 UNION
								SELECT SUBSTRING_INDEX(`device4-SPSKU`, ';', 1), devices.* FROM forecasts LEFT JOIN devices ON SUBSTRING_INDEX(`device4-SPSKU`, ';', 1) = devices.SPSKU WHERE synergyid= '".$synergyid."' AND deleted != 1
						";
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								if($row['SPSKU']!=''){
									echo "<option value='".$row['SPSKU']."'>".$row['model']." - " .$row['warranty']." jaar garantie - " .$row['SPSKU']. "</option>";
								}
							}
						}

						?>
					</select>
					</div>

					<div class="form-group mb-4">
						<h5 for="contactnaam">Nom d’ordinateur des appareils</h5>
						<small class="form-text pb-1">Si les appareils doivent recevoir un nom d’ordinateur spécifique, vous pouvez saisir le nom ou la liste complète ici.<br>
						Par exemple : SCH19-001, SCH19-002… (au maximum 5 caractères avant le ‘-’). Le label standard est complété par nous.</small>
						<input type="text" name="hostname" class="form-control verify" id="hostname"  placeholder="" value="Hetzelfde als het label" style="color:black" maxlength="5" required></textarea>
					</div>
					<div class="form-group mb-4">
						<h5 for="contactnaam">Date de livraison souhaitée</h5>
						<small class="form-text pb-1"></small>
						<input type="date" name="deliverydate" class="form-control verify" id=""  required placeholder="" value="" style="color:black" ></textarea>
					</div>
					<br>

					<select class="form-control ContactSelect" name="contactid" style="width:100%;">
					<?php
					include('mssql-conn.php');


					$tsql= "select cicntp.ID, cicntp.cnt_email, cicntp.FullName, cicntp.cnt_f_tel, cicntp.cnt_f_mobile,  cicmpy.cmp_name, opportunities.code, ltrim(cicmpy.cmp_code) as klantnummer from OpportunityContacts
					inner join cicntp on cicntp.cnt_id=OpportunityContacts.contactid inner join opportunities on opportunities.id=OpportunityID inner join cicmpy on cicmpy.cmp_wwn=OpportunityContacts.AccountID where  RoleID in (select id from OpportunityRoles where Description like '%image%')
					and cicmpy.cmp_code like '% ".$synergyid."'";
					$getResults= sqlsrv_query($msconn, $tsql);

					if ($getResults == FALSE){
						die(FormatErrors(sqlsrv_errors()));
					}

					while ($row = sqlsrv_fetch_array($getResults, SQLSRV_FETCH_ASSOC)) {
						echo "<option value='" . $row['ID'] . "'>" .  $row['FullName']  ." - "  . $row['cnt_email']  ."</option>";
					}

					sqlsrv_free_stmt($getResults);
					?>
					</select>
					<br>
					<br>

					<div id="contactdiv">
					<div class="form-group mb-4">
						<h5 for="contactnaam">Personne de contact image*</h5>
						<small class="form-text pb-1">Qui pouvons-nous contacter pour les questions techniques concernant l’imagerie des appareils ? (équipe DSI) </small>
						<input type="text" name="contactnaam" required class="form-control verify" id="contactnaam"  placeholder="">
					</div>

					<div class="form-group mb-4">
						<h5 for="contacttel">Personne de contact image : numéro de téléphone*</h5>
						<small class="form-text pb-1">À quel numéro de téléphone pouvons-nous joindre la personne de contact technique ? </small>
						<input type="text" name="contacttel" required class="form-control verify" id="contacttel"  placeholder="">
					</div>

					<div class="form-group mb-4">
						<h5 for="contactemail">Personne de contact image : adresse e-mail*</h5>
						<small class="form-text pb-1">À quelle adresse e-mail pouvons-nous joindre la personne de contact technique ? </small>
						<input type="email" name="contactemail" required class="form-control verify" id="contactemail"  placeholder="">
					</div>
					</div>
					<!-- <div class="form-group mb-4">
						<h5 for="contactnaam">Contactpersoon Image*</h5>
						<small class="form-text pb-1">Wie kunnen wij contacteren met technische vragen i.v.m. het imagen van de toestellen? (ICT-Coördinator)</small>
						<input type="text" name="contactnaam" required class="form-control verify" id="contactnaam"  placeholder="">
					</div>

					<div class="form-group mb-4">
						<h5 for="contacttel">Contactpersoon Image: Telefoonnummer*</h5>
						<small class="form-text pb-1">Op welk telefoonnummer is de technische contactpersoon bereikbaar?</small>
						<input type="text" name="contacttel" required class="form-control verify" id="contacttel"  placeholder="">
					</div>

					<div class="form-group mb-4">
						<h5 for="contactemail">Contactpersoon Image: E-mailadres*</h5>
						<small class="form-text pb-1">Op welk e-mailadres is de technische contactpersoon bereikbaar?</small>
						<input type="email" name="contactemail" required class="form-control verify" id="contactemail"  placeholder="">
					</div> -->

					<div class="form-group mb-4">
						<h5 for="comment">Remarques</h5>
						<small class="form-text pb-1">Ici, vous pouvez noter des détails ou des questions supplémentaires. </small>
						<textarea rows="3" name="comment" class="form-control verify" id="comment"  placeholder=""></textarea>
					</div>

					<button name="submit" type="submit" class="btn btn-primary mb-5">Soumettre</button>
				</div>

				<div style="overflow:auto;">
					<div style="float:right;">
						<button type="button" id="prevBtn" onclick="nextPrev(-1)">Précédent</button>
						<button type="button" id="nextBtn" onclick="nextPrev(1)">Suivant</button>
					</div>
				</div>
				<!-- Circles which indicates the steps of the form: -->
				<div style="text-align:center;margin-top:40px;">
					<span class="step"></span>
					<span class="step"></span>
					<span class="step"></span>
					<span class="step"></span>
				</div>
			</form>
		</main>
<?php } ?>

<script>
	(function() {

		$( '#imagekeuze input[type="radio"]' ).on( "change", function() {
			console.log(this.value);
			ResetFields();
			ResetRequired();
			this.value == 'school' ? $('#school').show() && $("#custom1Radio1").prop('required',true) : $('#school').hide()
			this.value == '1' || this.value == '2' || this.value == '3' ? $('#bundel').show() : $('#bundel').hide()
			this.value == 'custom' ? $('#gratissoftwarediv').show() && $('#customsoftwarediv').show() && $("#custom2Radio1").prop('required',true) && $('#bundel').show() : $('#gratissoftwarediv').hide() && $('#customsoftwarediv').hide();
			this.value == 'geen' ? $('#imagenaam').prop('value','Geen Image') : $('#imagenaam').prop('value','')
		});
		/*
		$( '#meerdere input[type="radio"]' ).on( "change", function() {
			this.value == '1' ? $('#naamimage').show() && $("#imagenaam").prop('required',true) : $('#naamimage').hide() && $('#imagenaam').val("") && $("#imagenaam").prop('required',false)
		});
		*/

		$( '#authenticatie input[type="radio"]' ).on( "change", function() {
			console.log(this.value);
			$('#vpntext').val("");
			$('#accountsvpn').val("");

			$('#lokaaltext').val("");
			$('#intunetext').val("");

			this.value == 'DomainJoined' ? $('#vpn').show() : $('#vpn').hide()
			this.value == 'intune' ? $('#intune').show() && $("#exampleRadios1").prop('required',true) : $('#intune').hide() && $("#exampleRadios1").prop('required',false)
			this.value == 'lokaal' ? $('#lokaal').show() : $('#lokaal').hide()

		});


		function ResetFields(){
			$('#hostname').val("");
			$('#labeling').val("");
			$('#comment').val("");
			$('#vpntext').val("");
			$('#accountsvpn').val("");

			$('#gratissoftwarediv').val("");
			$('#customsoftwarediv').val("");

			$('#lokaaltext').val("");
			$('#intunetext').val("");

			$('#bundel').hide()
			$('#intune').hide()
			$('#vpn').hide()
			$('#lokaal').hide()

			$('#custom1Radio1').prop('checked', false);
			$('#custom1Radio2').prop('checked', false);
			$('#custom1Radio3').prop('checked', false);

			$('#custom2Radio1').prop('checked', false);
			$('#custom2Radio2').prop('checked', false);
			$('#custom2Radio3').prop('checked', false);


			$("#SoftwareSelect").val('').change();

		}
		function ResetRequired(){
			$("#custom1Radio1").prop('required',false)
			$("#custom2Radio1").prop('required',false)


		}
		/*
		$('#SoftwareSelect').select2({
			placeholder: "Selecteer software"
		});
		*/

	})();

</script>

<script>

var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
	// This function will display the specified tab of the form ...
	var x = document.getElementsByClassName("tab");
	x[n].style.display = "block";
	// ... and fix the Previous/Next buttons:
	if (n == 0) {
		document.getElementById("prevBtn").style.display = "none";
	} else {
		document.getElementById("prevBtn").style.display = "none";
	}
	if (n == (x.length - 1)) {
		document.getElementById("nextBtn").innerHTML = "Indienen";
		document.getElementById("nextBtn").style.display = "none";

	} else {
		document.getElementById("nextBtn").innerHTML = "Volgende";
	}
	// ... and run a function that displays the correct step indicator:
	fixStepIndicator(n)
}

function GenerateCustomSoftwareField() {

	var string = '<div class="row row-fluid"><div class="col"><input type="text" name="customsoftware[]" class="form-control" placeholder="Software Naam"></div><div class="col"><input type="text" name="customsoftware[]" class="form-control" placeholder="Download Link"></div><div class="col"><input type="text" name="customsoftware[]" class="form-control" placeholder="Licentie"></div></div><br>';
	$('#customsoftware').append(string);

	console.log(document.getElementById('customsoftware').innerHTML);

}

function nextPrev(n) {
	// This function will figure out which tab to display
	var x = document.getElementsByClassName("tab");
	// Exit the function if any field in the current tab is invalid:
	if (n == 1 && !validateForm()) return false;
	// Hide the current tab:
	x[currentTab].style.display = "none";
	// Increase or decrease the current tab by 1:
	if (document.getElementById('customRadio1').checked == true){
		currentTab = currentTab + 3;
	}
	else if (document.getElementById('customRadio2').checked == true && document.getElementById('imagenaam').value != ""){
		currentTab = currentTab + 2;
	}
	else {
		currentTab = currentTab + n;
	}

	// if you have reached the end of the form... :
	if (currentTab >= x.length) {
		//...the form gets submitted:
		document.getElementById("regForm").submit();
		return false;
	}
	// Otherwise, display the correct tab:
	showTab(currentTab);
	window.scrollTo(0, 0);
}

function validateForm() {
	// This function deals with validation of the form fields
	var x, y, i = false;
	var valid = "";

	if ($('#regform *:not(#nextBtn):not(#prevBtn)').filter(':input:visible').length == 0) {
		console.log('niets gevonden');
		valid = true;
	} else {

		$('#regform *:not(#nextBtn):not(#prevBtn):not(.notrequired)').filter(':input:visible').each(function() {

			if (this.type == "radio" || this.type == "text") {
				console.log(this);
			}

			// my validation here

			if (this.checked !== false && this.type == "radio") {
				valid = true;
				//this.className += " invalid";
				console.log("valid radio");
			}

			if ( this.value !== "" && this.type == "text") {
				valid = true;
				//this.className += " invalid";
				console.log("valid text");
			}

		});
	}

	console.log('test is '+valid);
	// If the valid status is true, mark the step as finished and valid:

	if (valid == "") {
		//valid = true;
	}

	if (valid == true) {
		document.getElementsByClassName("step")[currentTab].className += " finish";
	}
	return valid; // return the valid status
}

function fixStepIndicator(n) {
	// This function removes the "active" class of all steps...
	var i, x = document.getElementsByClassName("step");
	for (i = 0; i < x.length; i++) {
		x[i].className = x[i].className.replace(" active", "");
	}
	//... and adds the "active" class to the current step:
	x[n].className += " active";
}

$('#SoftwareSelect').select2({
	placeholder: "Selecteer software"
});

$('#ToestelSelect').select2({
	placeholder: "Selecteer Toestellen"
});


$('.ContactSelect').select2({
	theme: "classic",
	placeholder: "Selecteer contactpersoon"
});

$('.ContactSelect').on('change', function(){
	console.log(this)
	var contactid = $(this).val();
	console.log(contactid)
	if(contactid){

		$.ajax({
			type:'POST',
			url:'ajaxContactData.php',
			data:'contactid='+contactid,
			success:function(html){
				$('#contactdiv').html(html);
			}
		});

	}else{
		$('#image').html('x');
	}
});

</script>

<?php
}
?>
