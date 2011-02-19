<?php
// ########### Google Analytics
  // Copyright 2009 Google Inc. All Rights Reserved.
  $GA_ACCOUNT = "MO-6506635-7";
  $GA_PIXEL = "/ga.php";

  function googleAnalyticsGetImageUrl() {
    global $GA_ACCOUNT, $GA_PIXEL;
    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);
    $referer = $_SERVER["HTTP_REFERER"];
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];
    if (empty($referer)) {
      $referer = "-";
    }
    $url .= "&utmr=" . urlencode($referer);
    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }
    $url .= "&guid=ON";
    return str_replace("&", "&amp;", $url);
  }

// ########### 

// jours de la semaine en français
$days[0] = 'dimanche';
$days[1] = 'lundi';
$days[2] = 'mardi';
$days[3] = 'mercredi';
$days[4] = 'jeudi';
$days[5] = 'vendredi';
$days[6] = 'samedi';

// récupération du jour de la semaine (le chiffre)
$day = date('w', time());

echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
	<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
	<link href="style.css" rel="stylesheet" type="text/css" />
	<title>watchout! programme TV sur iPhone</title>
	<meta content="tv television program programme iphone france paf français apple" name="Keywords" />
	<meta content="Le programme TV sur iPhone!" name="description" />
	<link rel="apple-touch-icon" href="img/home-icon.png" />
	<meta name="verify-v1" content="4S2PhWFYxGN12RA9Z4NJ1UFRBjeqwcfil8B8mJSNvfg=" />
</head>

<body>
	<div id="header">
		
		<ul id="week">
			<li><a class="tab" href="#<?php echo $days[($day+1)%7] ?>">demain</a></li>
			<?php for ($i=2; $i<7; $i++): ?>
				<li><a class="tab" href="#<?php echo $days[($day+$i)%7] ?>"><?php echo $days[($day+$i)%7] ?></a></li>
			<?php endfor ?>
		</ul>

		<ul id="tab">
			<li id="tab-now" class="tab unselected selected"><a class="tab" href="#now">actuellement</a></li>
			<li id="tab-tonight" class="tab unselected"><a class="tab" href="#tonight">ce&nbsp;&nbsp;&nbsp;soir</a></li>
			<li id="tab-night" class="tab unselected"><a class="tab" href="#night">soirée</a></li>
		</ul>
	</div>

	<div id="channels">
		<ul id="listLeft" class="channels">
			<?php include('channelList.php')?>
		</ul>
		
		<ul id="listRight" class="channels">
			<?php $_GET['time']='night'; include('channelList.php') ?>
		</ul>
	</div>
	
	<!--div id="footer">
		<span>poweredby.grafenko</span>
		<span><a href="mailto:watchout@scallioncorp.com">watchout@scallioncorp.com</a></span-->
		<!-- Google Analytics -->
		<img src="<?php echo googleAnalyticsGetImageUrl(); ?>" style="display:none;" alt="google analytics"/>
	<!--/div-->
	
	<script type="text/javascript" charset="utf-8" src="mootools-all.js"></script>
	<script type="text/javascript" charset="utf-8" src="mootools-more.js"></script>
	<script type="text/javascript" charset="utf-8" src="iphone.js"></script>
	
</body>

</html>
