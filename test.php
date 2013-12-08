$startpage_active = "SELECT `content` FROM `startpage` WHERE `userid` = ".$userid."";
$sp= mysql_query($startpage_active);
$sp = mysql_fetch_row($sp);
$sp = $sp[0];

if($sp)
{
	echo $sp;
}

echo "<h1><br><br><br>Hefeteig</h1>";