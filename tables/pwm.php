<?php
include_once('../common.php');
$start = $_POST['start'];

$query = "SELECT PWM, Domain FROM T_PWM LIMIT " . strval($start) . ", 10;";
$stmt = $dbh->prepare($query);
$stmt->execute();
$tissues = array();
$i = 0;

while ($row = $stmt->fetch())
{
	$i += 1;
	$query = "SELECT Domain_EnsPID, Type FROM T_Domain WHERE Domain LIKE :domain;";
	$param = array(":domain" => substr($row[1], 0, -3) .  "%");
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute($param);
	$result = $stmt2->fetch();

	$data = "<h4>PWM: {$row[0]}</h4><h4>Domain: {$row[1]}</h4>";

	if ($i % 2 == 1) echo "<tr>"; ?>
    <td>
		<div><b>PWM:</b> <?=$row[0]?></div>
		<div><b>Domain/Pattern:</b> <?=$row[1]?></div>
		<div>
			<b>Protein:</b>
			<a href="./network/?genename=<?=$result[0]?>"><?=$result[0]?></a>
		</div>
		<div><b>Type:</b> <?=$result[1]?></div>
		<div>
			<b>Files:</b>
			<?php if(is_file(__DIR__ ."/../pwms/mimp/{$row[0]}.mimp")) { ?>
			<a download="<?=$row[0]?>.mimp" href="./pwms/mimp/<?=$row[0]?>.mimp">MIMP</a>,
			<?php } ?>
			<?php if(is_file(__DIR__ ."/../pwms/enologo/{$row[0]}.enologo")) { ?>
			<a download="<?=$row[0]?>.enologo" href="./pwms/enologo/<?=$row[0]?>.enologo">Enologo</a>,
			<?php } ?>
			<?php if(is_file(__DIR__ ."/../pwms/musi/{$row[0]}.musi")) { ?>
			<a download="<?=$row[0]?>.musi" href="./pwms/musi/<?=$row[0]?>.musi">Musi</a>
			<?php } ?>
		</div>
	</td>
    <td>
    	<img src="./pwms/logos/<?=$row[0]?>.png" height="100px" class="pwm-img" data-content="<?=$data?>">
    </td>
	<?php if ($i % 2 == 0) echo "</tr>"; ?>
<?php } ?>
