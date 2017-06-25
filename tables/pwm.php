<?php
include_once('../common.php');
$start = $_POST['start'];

$query = "SELECT PWM, Domain FROM T_PWM LIMIT " . strval($start) . ", 10;";
$stmt = $dbh->prepare($query);
$stmt->execute();
$tissues = array();
$i = 1;

while ($row = $stmt->fetch())
{
	$query = "SELECT Domain_EnsPID, Type, DomainSequence FROM T_Domain WHERE Domain LIKE :domain;";
	$param = array(":domain" => substr($row[1], 0, -3) .  "%");
	$stmt2 = $dbh->prepare($query);
	$stmt2->execute($param);
	$result = $stmt2->fetch();

	$data = "<p style='font-size:0.8em;margin-top:10px;'><b>PWM: " . $row[0] . "</b><br>";
	$data = $data . "<b>Domain: " . $row[1] . "</b></p><br>";
	$data = $data . "Domain Sequence: <div class='pwm_table_data_style'>" . $result[2] . "</div></p>";

	if ($i % 2 == 1) { ?>
	<tr>
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
			<a download="<?=$row[0]?>.mimp" href="./pwms/mimp/<?=$row[0]?>.mimp">MIMP</a>,
			<a download="<?=$row[0]?>.enologo" href="./pwms/enologo/<?=$row[0]?>.enologo">Enologo</a>,
			<a download="<?=$row[0]?>.musi" href="./pwms/musi/<?=$row[0]?>.musi">Musi</a>
		</div>
	</td>
    <td>
    	<img src="./pwms/logos/<?=$row[0]?>.png" height="100px" class="pwm-img" data-content="<?=$data?>">
    </td>
	<?php } elseif ($i % 2 == 0) { ?>
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
        	<a download="<?=$row[0]?>.mimp" href="./pwms/mimp/<?=$row[0]?>.mimp">MIMP</a>,
        	<a download="<?=$row[0]?>.enologo" href="./pwms/enologo/<?=$row[0]?>.enologo">Enologo</a>,
        	<a download="<?=$row[0]?>.musi" href="./pwms/musi/<?=$row[0]?>.musi">Musi</a>
        </div>
    </td>
    <td>
    	<img src="./pwms/logos/<?=$row[0]?>.png" height="100px" class="pwm-img" data-content="<?=$data?>">
	</tr>
	<?php } ?>

<?php
	$i += 1;
}
?>
