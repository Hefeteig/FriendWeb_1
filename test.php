<?php
	$connect = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($connect, "friendweb");
	$plg = array();
	$result = mysqli_query($connect, "SELECT `plugin` FROM `activatedplugins` WHERE `user` = 3");
	//$data = mysqli_fetch_array($result);
	//print_r($data);
	
	while ($data = mysqli_fetch_array($result))
	{
		$add = $data[0];
		array_push($plg, $add);
	}
	print_r($plg);
	
?>