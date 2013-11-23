<?php
	echo "<br><br>Hallo";
	//$userid = $_GET['id'];
	/*
	$userid = '5';
	echo "<br><br>Hallo";
	
	$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($sql, "friendweb");
	$select_friends = "SELECT `friendid` FROM `friends` WHERE `userid` = '".$userid."' AND `confirmed` = 1";
	$friends = mysqli_query($sql, $select_friends);
	for($j = 0; $array[$j] = mysqli_fetch_assoc($friends); $j++);
	array_pop($array);
	if($array == array())
	{
		echo "<br /><br />&nbsp;&nbsp;&nbsp;Du hast noch keine Kontakte.";
	}
	
	$fname_array = array();
	foreach($array as $current_friendid)
	{
		$get_user = "SELECT `name`, `status` FROM `users` WHERE `userid` = '".$current_friendid['friendid']."'";
		$current_friend = mysqli_query($sql, $get_user);
		$current_friend = mysqli_fetch_array($current_friend);
		array_push($fname_array, $current_friend);
	}
	foreach($fname_array as $cf)
	{
		if($cf[1] == 1)
		{
			echo "<br /><div class='status_on round_corners'><br />&nbsp;&nbsp;&nbsp;".$cf[0]."<br /><br /></div>";
		}
	}
	foreach($fname_array as $cf)
	{
		if($cf[1] == 0)
		{
			echo "<br /><div class='status_off round_corners'><br />&nbsp;&nbsp;&nbsp;".$cf[0]."<br /><br /></div>";
		}
	}
	*/
?>