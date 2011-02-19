<?php

function nightChannels() {
	// récupération du jour de la semaine voulu
	$day = $_GET['time'];
	if ( $day=="night" ) {
		// jours de la semaine en français
		$days[0] = 'dimanche';
		$days[1] = 'lundi';
		$days[2] = 'mardi';
		$days[3] = 'mercredi';
		$days[4] = 'jeudi';
		$days[5] = 'vendredi';
		$days[6] = 'samedi';
		$day = $days[date('w', time())];
	}
	
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "http://www.programme.tv/soiree/".$day.".php");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$page = curl_exec($curl);
	curl_close($curl);
	
	// suppression de ce qu'il y a avant et après la liste des programmes
	$page = preg_replace('/\n/i', "", $page);
	$page = preg_replace("/^.*<table class=\"contenu_soiree\">[^<]*<tr>/", "", $page);
	$page = preg_replace("/<script[^<]*script>[^<]*<\/td>/", "</td>", $page);
	$page = preg_replace("/<div id=\"droite\">.*$/", "", $page);
	$channels = split("<td valign=\"top\"[^>]*class=\"soiree[^\"]*\">", $page);
	$chans = array();
	
	// parcours des chaines et récupération des infos dans le tableau $matches pour chaque chaîne
	for ($i=1; $i<count($channels); $i++) { 
		
		preg_match("/chaine\/([0-9]{1,2})p?\.gif.*([0-9]{2}\.[0-9]{2}).*href=\"([^\"]*)\" class=\"bb12\">([^<]*)/", $channels[$i], $matches);
		array_shift($matches);
		/*
		matches:
		0: chaine
		1: heure de début du programme
		2: lien pour le programme
		3: programme
		*/

		$chans[$matches[0]]['id'] = $matches[0];
		$chans[$matches[0]]['showLink'] = $matches[2];
		$chans[$matches[0]]['show'] = $matches[3];
		$chans[$matches[0]]['showStart'] = ereg_replace("\.", "h", $matches[1]);
	}
	
	return $chans;
}

function nowChannels() {
  $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "http://www.programme.tv/actuellement/");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$page = curl_exec($curl);
	curl_close($curl);

	// suppression de ce qu'il y a avant et après la liste des programmes
	$page = preg_replace('/\n/i', "", $page);
	$page = preg_replace("/^.*<table class=\"table_actu\">/", "", $page);
	$page = preg_replace("/<div id=\"droite\">.*$/", "", $page);
	$channels = preg_split("/<tr>\h*<td class=\"width50p( bar\_gris)?\">/i", $page);
	$chans = array();

	// parcours des chaines et récupération des infos dans le tableau $matches pour chaque chaîne
	for ($i=1; $i<count($channels); $i++) { 
	
	preg_match("/chaine\/([0-9]{1,2})p?\.gif.*href=\"([^\"]*)\".*<b>(.*)<\/b>.*([0-9]{2}h[0-9]{2}).*actu_chiffre\">([0-9]{1,3})%<\/span>.*&nbsp;([0-9]{2}h[0-9]{2}).*([0-9]{2}h[0-9]{2}).*href=\"([^\"]*)\".*<b>(.*)<\/b>.*([0-9]{2}h[0-9]{2}).*href=\"([^\"]*)\".*<b>(.*)<\/b>/", $channels[$i], $matches);
		array_shift($matches);
		/*
		matches:
		0: chaine 
		1: lien pour le programme actuel
		2: programme actuel
		3: heure début programme actuel
		4: pourcentage d'avancement du programme actuel
		5: heure fin programme actuel
		6: heure début programme qui suit
		7: lien pour le programme qui suit
		8: programme qui suit
		9: heure début programme d'encore après
		10: lien pour le programme d'encore après
		11: programme d'encore après
		*/
		//echo '<pre>'; print_r($matches); echo '</pre>';

		$chans[$matches[0]]['id'] = $matches[0];
		$chans[$matches[0]]['nowLink'] = $matches[1];
		$chans[$matches[0]]['now'] = $matches[2];
		$chans[$matches[0]]['nowStart'] = $matches[3];
		$chans[$matches[0]]['nowEnd'] = $matches[5];
		$chans[$matches[0]]['nowDone'] = $matches[4];
		$chans[$matches[0]]['next'] = $matches[8];
		$chans[$matches[0]]['nextStart'] = $matches[6];
		$chans[$matches[0]]['nextLink'] = $matches[7];
		$chans[$matches[0]]['nextNext'] = $matches[11];
		$chans[$matches[0]]['nextNextStart'] = $matches[9];
		$chans[$matches[0]]['nextNextLink'] = $matches[10];
	}
	
	return $chans;
}