<?php

	include('roles.php');
	include('pathBegrensing.php');

	// Gebruikers
	$users = [

		//admin
		'Bart' => $admin,
		'Developer' => $admin,
		'Exact' => $admin,
		'Gertjan.vendeloo' => $admin,
		'Ilias.mesbahi' => $admin,
		'Jordy' => $admin,
		'Joris' => $admin,
		'Laurens.desutter' => $admin,
		'Mattias.delang' => $admin,
		'Mike' => $admin,
		'Nathalie.desmaele' => $admin,
		'Pascal.vandervennet' => $admin,
		'Thomas.buyssens' => $admin,

		//management
		'Arne' => $management,
		'Karin' => $management,
		'Pieter' => $management,

		//internalSales
		'Benny' => $internalSales,
		'Elif' => $internalSales,
		'Jeroen.vansimpsen' => $internalSales,
		'Matthias.bossee' => $internalSales,

		// management + sales
		'Jens.cuypers' => $manSales,

		// management + delivery + stock
		'Arne.desmet' => $arne,

		// management + delivery
		'Dominique' => $manDel,
		'Gilles.vanhuffel' => $manDel,
		'Kim.massart' => $manDel,
		'Sandra.vandorsselaer' => $manDel,
		'Sarah.pazrodriguez' => $manDel,

		// management + delivery + imagen + stock
		'Maaike.ryckbosch' => $manDelImagenStock,

		// webshop + delivery
		'Bart.staes' => $webDel,

		// technieker + stock
		'Abdel.akkad' => $techStock,
		'Anek.bunkhumyoo' => $techStock,
		'Bilal.elkhattoti' => $techStock,
		'Brent.vanwayenberg' => $techStock,
		'Davy' => $techStock,
		'Felix' => $techStock,
		'Geoffrey.morren' => $techStock,
		'Jo' => $techStock,
		'Pieterjan.vandewalle' => $techStock,
		'Ronny.cloof' => $techStock,
		'Sander.gps' => $techStock,
		'Simon.moonens' => $techStock,
		'Styn' => $techStock,
		'Ward.vanoverwalle' => $techStock,
		'Wouter.theys' => $techStock,

		// technieker + imaging + stock + operations
		'Bruno.vanlaetem' => $bruno,

		//imagen
		'Techimaging' => $imagen,

		//sales + imagen
		'Alain.leuregans' => $salIma,

		//sales
		'Birger' => $sales,
		'Birgit.deblieck' => $sales,
		'Grace.larose' => $sales,
		'Hanne' => $sales,
		'Jasper' => $sales,
		'Pieter-jan' => $sales,
		'Pieter.braem' => $sales,
		'Pieter.destaebel' => $sales,
		'Simon' => $sales,
		'Soetkin' => $sales,
		'Steven.malfroidt' => $sales,
		'Thomas' => $sales,

		//software
		'Brent.spruyt' => $software,
		'Jelle' => $software,
		'Jens.pinoy' => $software,
		'Joe.specker' => $software,
		'Quinten' => $software,
		'Stef.pattyn' => $software,
		'Yakup' => $software,

		//testing
		'Eline.matthys' => $imageTester,
		'Nathalie.matthys' => $imageTester,
		'Haron.zaidi' => $imageTester,
		'Marcos.mendez' => $imageTester,

		// imagen + testing
		'Ismail' => $imagenImageTester,

		// imagen + timeline + decom
		'Hicham.ziani' => $imagenTimeDecom,

		//stock + operations
		'Eddy.vankenhove' => $eddy,

		//techdata
		'Techdata' => $techdata,

		//copaco
		'Copaco' => $copaco,

		//courier
		'Joris.hoedemaekers' => $courier,
		'Thibaud.decanniere' => $courier,
		'Wietse.desmet' => $courier,
		'Dirk.tuyttens' => $courier,

	];

	/**
	 * controleer of gebruiker een role heeft
	 *
	 * @param array $role
	 * @param array $gewensteRoles
	 *
	 * @return bool
	 */
	function hasRole($role, $gewensteRoles) {
		if (isset($role['admin'])) {
			return true;
		}

		foreach ($gewensteRoles as $gewensteRole) {
			if (isset($role[$gewensteRole])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * check of gebruiker access heeft tot huidige pagina
	 *
	 * @return bool
	 */
	function hasAccessForFile() {
		global $role;
		$url = $_SERVER['SCRIPT_NAME'];

		$devEnvPrefix = '/byod-orders';

		if (strpos($url, $devEnvPrefix) === 0) {
			$url = str_replace($devEnvPrefix, '', $url);
		}
		return checkAccess($role, $url);
	}

	/**
	 * check of gebruiker access heeft tot deze url
	 *
	 * @param string $url
	 * @param boolean $echo
	 *
	 * @return string
	 */
	function hasAccessForUrl($url, $echo = true) {
		global $role;

		$cleanUrl = strstr($url, '.php', true).'.php';
		$hasAccess = checkAccess($role, $cleanUrl);

		if ($hasAccess) {
			if ($echo) {
				echo $url;
				return;
			}
			return $url;
		}
		if ($echo) {
			echo '#no-access';
			return;
		}
		return '#no-access';
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Private functions
///
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * check of gebruiker access heeft
	 *
	 * @param array $role
	 * @param string $url
	 *
	 * @return bool
	 */
	function checkAccess($role, $url) {
		global $begrensingen;

		$url = ltrim($url, '/');

		if (!isset($begrensingen[$url])) {
			return true;
		}
		return hasRole($role, $begrensingen[$url]);
	}
