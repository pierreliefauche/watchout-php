<?php
require_once('tools.php');
/* Noms des chaînes */
$names[1] = 'TF1';
$names[2] = 'France 2';
$names[3] = 'France 3';
$names[4] = 'Canal +';
$names[5] = 'France 5';
$names[6] = 'M6';
$names[7] = 'Arte';
$names[8] = 'Direct 8';
$names[9] = 'W9';
$names[10] = 'TMC';
$names[11] = 'NT1';
$names[12] = 'NRJ12';
$names[13] = 'Public Sénat';
$names[14] = 'France 4';
$names[15] = 'BFM TV';
$names[16] = 'iTélé';
$names[17] = 'Virgin 17';
$names[18] = 'Gully';

$random = trim(money_format('%=0#3.0n', rand(0,999)));

?>
<?php if ( (isset($_GET['time']) && ($_GET['time']!='now')) ): ?>
	<?php foreach (nightChannels() as $channel): ?>
		<li id="channel-<?php echo $random.$channel['id'] ?>" class="channel">
			<table>
				<tr>
					<td class="logo30"><img src="img/channel/<?php echo $channel['id'] ?>.png" alt="<?php echo (isset($names[$channel['id']]))?($names[$channel['id']]):($channel['id']) ?>" width="30"/></td>
					<td colspan="2"><a class="now show #<?php echo $random.$channel['id'] ?>" href="<?php echo $channel['showLink'] ?>"><?php echo $channel['show'] ?></a></td>
				</tr>

			</table>
		</li>
		<li id="showDetails-<?php echo $random.$channel['id'] ?>" class="showDetails channel"></li>
<?php endforeach ?>
<?php else: ?> 
	<?php $i=0; ?>
<?php foreach (nowChannels() as $channel): ?>
	<?php if ($i++>0): ?>
		<li class="divider"></li>
	<?php endif ?>
	<li class="channel" id="channel-<?php echo $random.$channel['id'] ?>">
		<table>
			<tr>
				<td class="logo"><img src="img/channel/<?php echo $channel['id'] ?>.png" alt="<?php echo (isset($names[$channel['id']]))?($names[$channel['id']]):($channel['id']) ?>"/></td>
				<td colspan="2"><a class="now show #<?php echo $random.$channel['id'] ?>" href="<?php echo $channel['nowLink'] ?>"><?php echo $channel['now'] ?></a></td>
			</tr>
			<tr>
				<td class="time"><?php echo $channel['nowStart'] ?></td>
				<td class="progression"><div style="width:<?php echo $channel['nowDone'] ?>% " class="progressBar" id="progres-<?php echo $channel['id'] ?>"><?php echo $channel['nowDone'] ?>%</div></td>
				<td class="time bout"><?php echo $channel['nowEnd'] ?></td>
			</tr>
			<tr>
				<td class="time"><?php echo $channel['nextStart'] ?></td>
				<td colspan="2"><a class="next show #<?php echo $random.$channel['id'] ?>" href="<?php echo $channel['nextLink'] ?>"><?php echo $channel['next'] ?></a></td>
			</tr>
			<tr>
				<td class="time"><?php echo $channel['nextNextStart'] ?></td>
				<td colspan="2"><a class="next show #<?php echo $random.$channel['id'] ?>" href="<?php echo $channel['nextNextLink'] ?>"><?php echo $channel['nextNext'] ?></a></td>
			</tr>
		</table>
	</li>
	<li id="showDetails-<?php echo $random.$channel['id'] ?>" class="showDetails channel"></li>
<?php endforeach ?>
<?php endif; ?>
