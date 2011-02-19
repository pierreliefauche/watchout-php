<?php

if (isset($_POST['time'])) 
	$time = $_POST['time'];
else 	if (isset($_GET['time'])) 
	$time = $_GET['time'];
else 
	$time = 'now';

if (isset($_POST['link'])) 
	$link = $_POST['link'];
else 	if (isset($_GET['link'])) 
	$link = $_GET['link'];
else 
	die('No matching data found');

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$page = curl_exec($curl);
curl_close($curl);

// suppression de ce qu'il y a avant et après ce qui nous intéresse
$page = preg_replace("/\n/", "", $page);
$page = preg_replace("/^.*table width=\"100%\" class=\"eTable\">/", "", $page);
$page = preg_replace("/<td class=\"b12\">Les autres diffusions.*$/", "", $page);

$show = array();
$matches = array();

// récupération du type du programme avec son pays et année de production
if ( preg_match('/<td colspan=\"2\" align=\"right\" class=\"b12 efond\">(.*)&nbsp;<\/td>/', $page, $matches) ) 
	$show['type'] = $matches[1];
else $show['type'] = null;

// récupération des horaires exacts
if ( preg_match('/<span class=\"eHeure\">([0-9]{2}h[0-9]{2})<\/span>.*([0-9]{2}h[0-9]{2})<br \/>/', $page, $matches) ) {
	$show['start'] = $matches[1];
	$show['end'] = $matches[2];
}

// VOST disponible ou pas ?
// if ( preg_match('/.*<td class=\"right\">VOST <\/td><td><img src=\"\/img\/(.*)\.gif.*/', $page, $matches) ) 
// 	if ( $matches[1]=='oui' ) 
// 		$show['vost'] = 'disponible';

// récupération du titre du programme
if ( preg_match('/<div class=\"b18\">([^<]*)</', $page, $matches) ) 
	$show['title'] = $matches[1];
	
// récupération du sous-titre du programme
if ( preg_match('/<\/div><div class=\"b12\">(.*)<\/div>/', $page, $matches) ) 
	if ( strlen($matches[1])>0 ) 
		$show['subtitle'] = $matches[1];

// récupération du numéro de programme pour l'image
if ( preg_match('/<td class=\"center\">[^<]*<img src=\"(.*\.jpg)\"/', $page, $matches) ) 
	$show['image'] = $matches[1];

// récupération de la saison et de l'épisode (applicable seulement aux séries)
if ( preg_match('/<br \/>Saison : ([0-9]{1,3})[^<]*<br \/>Episode : ([0-9\/]{1,9})[^<]*<br \/>/', $page, $matches) ) {
	$show['season'] = $matches[1];
	$show['episode'] = $matches[2];
}

// récupération du résumé
if ( preg_match('/<span id=\"intelliTXT\">(.*)<\/?span\/?>/', $page, $matches) ) {
	$texte = $matches[1];
	// suppression des infos qu'on ne veut pas
	$inutiles = array('/<b>Invit[^s]*s :<\/b>[^<]*<br \/><br \/>/', '/<b>Pr[^s]*sent[^ ]* par :<\/b>[^<]*<br \/><br \/>/', '/<b>R[^a]*alis[^ ]* par :<\/b>[^<]*<br \/><br \/>/', '/<b>Acteurs :<\/b>[^<]*<br \/><br \/>/', '/<b>Notre avis :<\/b>[^<]*<br \/><br \/>/', '/<br \/>/');
	$show['resume'] = preg_replace($inutiles, '', $texte);
	$show['resume'] = preg_replace(array('/<b>/', '/<\/b>/'), array('<br/><b>', '</b><br/>'), $show['resume']);
	$show['resume'] = preg_replace('/<br\/><br\/>/', '<br/>', $show['resume']);
}

?>

<?php if ( $time=='now' ): ?>
	<a href="#" class="separator"><img src="img/style/list/fermer.jpg" /></a>
	<span class="title"><?php echo $show['title'] ?></span><br/>
<?php endif ?>

<?php if (isset($show['subtitle'])): ?>
	<span class="subtitle"><?php echo $show['subtitle'] ?></span><br/>
<?php endif ?>

<span class="season">
<?php if ( isset($show['season']) ) echo 'saison '.$show['season'].((isset($show['episode']))?(' '):('<br/>')); ?>
<?php if ( isset($show['episode']) ) echo 'épisode '.$show['episode'].'<br/>'; ?>
</span>

<?php if ( $time!='now' ): ?>
	<span class="horaire">de <?php echo $show['start'] ?> à <?php echo $show['end'] ?></span>
<?php endif ?>

<?php if (isset($show['image'])): ?>
	<img src="<?php echo $show['image'] ?>" width="300" height="232" class="photo"/><br/>
<?php endif ?>

<?php if (isset($show['resume'])): ?>
	<p><?php echo $show['resume'] ?></p>
<?php endif ?>

<?php if ( $time=='now' ): ?>
	<span class="horaire">de <?php echo $show['start'] ?> à <?php echo $show['end'] ?></span><br/>
<?php endif ?>
<span class="type"><?php echo $show['type'] ?></span>
<?php if ( $time!='now' ): ?>
	<a href="#" class="separator"><img src="img/style/list/fermer.jpg" /></a>
<?php endif ?>