<?php
	session_start();
	$userid = $_SESSION['userid'][0];
	
	$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($sql, "friendweb");
	$select_friends = "SELECT `friendid` FROM `friends` WHERE `userid` = '".$userid."' AND `confirmed` = 1";
	$friends = mysqli_query($sql, $select_friends);
	
	//Onlinestatus updaten
	$update_query = "UPDATE `users` SET `last_update` = CURRENT_TIMESTAMP WHERE `userid` = '".$userid."'";
	$update = mysqli_query($sql, $update_query);
	
	for($j = 0; $array[$j] = mysqli_fetch_assoc($friends); $j++);
	array_pop($array);
	if($array == array())
	{
		echo "<br /><br />&nbsp;&nbsp;&nbsp;Du hast noch keine Kontakte.";
	}
	
	$fname_array = array();
	foreach($array as $current_friendid)
	{
		$get_user = "SELECT `name`, `last_update` FROM `users` WHERE `userid` = '".$current_friendid['friendid']."'";
		$current_friend = mysqli_query($sql, $get_user);
		$current_friend = mysqli_fetch_array($current_friend);
		
		$select_time = "SELECT TIMEDIFF(NOW(), '".$current_friend[1]."') FROM `users` WHERE `name` = '".$current_friend[0]."'";
		$time_diff = mysqli_query($sql, $select_time);
		$time_diff = mysqli_fetch_row($time_diff);
		$time_diff = explode(':', $time_diff[0]);
		$current_friend[1] = $time_diff[2];
		if($time_diff[0] != '00' or $time_diff[1] != '00')
		{
			$current_friend[1] = 40;
		}
		
		array_push($fname_array, $current_friend);
	}
	
	
	foreach($fname_array as $cf)
	{
		if($cf[1] <= 25)
		{
			echo "<br /><div class='status_on round_corners'><br />&nbsp;&nbsp;&nbsp;".$cf[0]."<br /><br /></div>";
		}
	}
	foreach($fname_array as $cf)
	{
		if($cf[1] >= 25)
		{
			echo "<br /><div class='status_off round_corners'><br />&nbsp;&nbsp;&nbsp;".$cf[0]."<br /><br /></div>";
		}
	}
?>