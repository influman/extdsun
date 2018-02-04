<?php
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";  
	// *********************************************************************************************************************
	// V1.0 : Script qui fournit les phases du soleil incluant des phases personnalisées en écart
	//*************************************** *****************************************************************************
	// latitude
  	$latitude = getArg("lat", $mandatory = true, $default = '48.8534');
  	// longitude
  	$longitude = getArg("long", $mandatory = true, $default = '2.3488');
  	// délai
  	$delay = getArg("delay", $mandatory = false, $default = '30');
  	// update forcée
	$update = getArg("update", $mandatory = false, $default = '');

	
	/***********************************************************************************************************************/
	
	if ($delay >= 60) {
		$delay = 59;
	}
	if ($delay <= 0) {
		$delay = 1;
	}
	
	list($gmt,$mn) = sscanf(date("P"), "%d:%d");

  // 1ère utilisation
  if (loadVariable('EXTDSUN_COUCHE') == '')
  {
    $update = "now";
  }
	$actualisation = "03:00";
	$heurenum = date("H").":".date("i");
	if ($heurenum == $actualisation || $update == "now") {
		// mise à jour des données soleil via api
		$url = "http://api.sunrise-sunset.org/json?lat=".$latitude."&lng=".$longitude;
    		$result = sdk_json_decode(utf8_encode(httpQuery($url,'GET')));


		// mise au format PM et GMT des horaires
		// SE LEVE
		list($api_seleve_h,$api_seleve_m,$sec,$ampm) = sscanf($result['results']['civil_twilight_begin'],"%d:%d:%d %s");
		if ($ampm == 'PM') {
			$api_seleve_h += 12;
		}
		$api_seleve_h += $gmt;
		if ($api_seleve_h < 0) {
			$api_seleve_h += 24;
		}
		if ($api_seleve_h >= 24) {
			$api_seleve_h -= 24;
		}
		
		// AVANT LEVE
		$api_avantleve_h =$api_seleve_h;
		$api_avantleve_m = $api_seleve_m - $delay;
		if ($api_avantleve_m < 0) {
			$api_avantleve_m += 60;
			$api_avantleve_h -= 1;
			if ($api_avantleve_h < 0) {
				$api_avantleve_h += 24;
			}
		}
		

		// LEVE
		list($api_leve_h,$api_leve_m,$sec,$ampm) = sscanf($result['results']['sunrise'],"%d:%d:%d %s");
		if ($ampm == 'PM') {
			$api_leve_h += 12;
		}
		$api_leve_h += $gmt;
		if ($api_leve_h < 0) {
			$api_leve_h += 24;
		}
		if ($api_leve_h >= 24) {
			$api_leve_h -= 24;
		}
		
		
		// APRES LEVE
		$api_apresleve_h =$api_leve_h;
		$api_apresleve_m = $api_leve_m + $delay;
		if ($api_apresleve_m > 59) {
			$api_apresleve_m -= 60;
			$api_apresleve_h += 1;
			if ($api_apresleve_h > 23) {
				$api_apresleve_h -= 24;
			}
		}
	

		//SE COUCHE
		list($api_secouche_h,$api_secouche_m,$sec,$ampm) = sscanf($result['results']['sunset'],"%d:%d:%d %s");
		if ($ampm == 'PM') {
			$api_secouche_h += 12;
		}
		$api_secouche_h += $gmt;
		if ($api_secouche_h < 0) {
			$api_secouche_h += 24;
		}
		if ($api_secouche_h >= 24) {
			$api_secouche_h -= 24;
		}
	

		// AVANT COUCHE
		$api_avantcouche_h =$api_secouche_h;
		$api_avantcouche_m = $api_secouche_m - $delay;
		if ($api_avantcouche_m < 0) {
			$api_avantcouche_m += 60;
			$api_avantcouche_h -= 1;
			if ($api_avantcouche_h < 0) {
				$api_avantcouche_h += 24;
			}
		}
		

		// COUCHE
		list($api_couche_h,$api_couche_m,$sec,$ampm) = sscanf($result['results']['civil_twilight_end'],"%d:%d:%d %s");
		if ($ampm == 'PM') {
			$api_couche_h += 12;
		}
		$api_couche_h += $gmt;
		if ($api_couche_h < 0) {
			$api_couche_h += 24;
		}
		if ($api_couche_h >= 24) {
			$api_couche_h -= 24;
		}
		

		// APRES COUCHE
		$api_aprescouche_h =$api_couche_h;
		$api_aprescouche_m = $api_couche_m + $delay;
		if ($api_aprescouche_m > 59) {
			$api_aprescouche_m -= 60;
			$api_aprescouche_h += 1;
			if ($api_aprescouche_h > 23) {
				$api_aprescouche_h -= 24;
			}
		}
		

		// ajoute les zéros de tête si besoin
		if (strlen($api_seleve_h) == 1) {
			$api_seleve_h = "0".$api_seleve_h;
		}
		if (strlen($api_seleve_m) == 1) {
			$api_seleve_m = "0".$api_seleve_m;
		}
		$api_seleve = $api_seleve_h.":".$api_seleve_m;

		if (strlen($api_avantleve_h) == 1) {
			$api_avantleve_h = "0".$api_avantleve_h;
		}
		if (strlen($api_avantleve_m) == 1) {
			$api_avantleve_m = "0".$api_avantleve_m;
		}
		$api_avantleve = $api_avantleve_h.":".$api_avantleve_m;

		if (strlen($api_leve_h) == 1) {
			$api_leve_h = "0".$api_leve_h;
		}
		if (strlen($api_leve_m) == 1) {
			$api_leve_m = "0".$api_leve_m;
		}
		$api_leve = $api_leve_h.":".$api_leve_m;

		if (strlen($api_apresleve_h) == 1) {
			$api_apresleve_h = "0".$api_apresleve_h;
		}
		if (strlen($api_apresleve_m) == 1) {
			$api_apresleve_m = "0".$api_apresleve_m;
		}
		$api_apresleve = $api_apresleve_h.":".$api_apresleve_m;

		if (strlen($api_avantcouche_h) == 1) {
			$api_avantcouche_h = "0".$api_avantcouche_h;
		}
		if (strlen($api_avantcouche_m) == 1) {
			$api_avantcouche_m = "0".$api_avantcouche_m;
		}
		$api_avantcouche = $api_avantcouche_h.":".$api_avantcouche_m;

		if (strlen($api_secouche_h) == 1) {
			$api_secouche_h = "0".$api_secouche_h;
		}
		if (strlen($api_secouche_m) == 1) {
			$api_secouche_m = "0".$api_secouche_m;
		}
		$api_secouche = $api_secouche_h.":".$api_secouche_m;

		if (strlen($api_couche_h) == 1) {
			$api_couche_h = "0".$api_couche_h;
		}
		if (strlen($api_couche_m) == 1) {
			$api_couche_m = "0".$api_couche_m;
		}
		$api_couche = $api_couche_h.":".$api_couche_m;

		if (strlen($api_aprescouche_h) == 1) {
			$api_aprescouche_h = "0".$api_aprescouche_h;
		}
		if (strlen($api_aprescouche_m) == 1) {
			$api_aprescouche_m = "0".$api_aprescouche_m;
		}
		$api_aprescouche = $api_aprescouche_h.":".$api_aprescouche_m;

		saveVariable('EXTDSUN_AVANTLEVE', $api_avantleve);
		saveVariable('EXTDSUN_SELEVE', $api_seleve);
		saveVariable('EXTDSUN_LEVE', $api_leve);
		saveVariable('EXTDSUN_APRESLEVE', $api_apresleve);
		saveVariable('EXTDSUN_AVANTCOUCHE', $api_avantcouche);
		saveVariable('EXTDSUN_SECOUCHE', $api_secouche);
		saveVariable('EXTDSUN_COUCHE', $api_couche);
		saveVariable('EXTDSUN_APRESCOUCHE', $api_aprescouche);
	}

	$avantleve = loadVariable('EXTDSUN_AVANTLEVE');
	$seleve = loadVariable('EXTDSUN_SELEVE');	
	$leve = loadVariable('EXTDSUN_LEVE');
	$apresleve = loadVariable('EXTDSUN_APRESLEVE');
	$avantcouche = loadVariable('EXTDSUN_AVANTCOUCHE');
	$secouche = loadVariable('EXTDSUN_SECOUCHE');
	$couche = loadVariable('EXTDSUN_COUCHE');
	$aprescouche = loadVariable('EXTDSUN_APRESCOUCHE');
	if ($heurenum == $avantleve) 
	{
		$status = 1;
	}
	if ($heurenum == $seleve) {
		$status = 2;
	}
	if ($heurenum == $leve) {
		$status = 3;
	}
	if ($heurenum == $apresleve) {
		$status = 4;
	}
	if ($heurenum == $avantcouche) {
		$status = 5;
	}
	if ($heurenum == $secouche) {
		$status = 6;
	}
	if ($heurenum == $couche) {
		$status = 7;
	}
	if ($heurenum == $aprescouche) {
		$status = 8;
	}
	
	// initialisation
	if ($status == '')
	{
		$status = loadVariable('status');
		if ($status == '')
		{
			$status = 'Veuillez patienter...';
		}
	}
	else
	{
    saveVariable('status', $status);
	}

	$xml .=  "<SUN>";
	$xml .=  "<STATUS>".$status."</STATUS>";
	$xml .=  "<SUNSET>".$secouche."</SUNSET>";
	$xml .=  "<SUNRISE>".$seleve."</SUNRISE>";
	$xml .=  "</SUN>";
	sdk_header('text/xml');
	echo $xml;
?>
