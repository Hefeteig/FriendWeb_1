<?php
	session_start();
	require_once 'lib/Twig/Autoloader.php';
	Twig_Autoloader::register();
	$loader = new Twig_Loader_Filesystem('./');
	$twig = new Twig_Environment($loader, array());
	$template = $twig->loadTemplate('main.html');
	$params = array();
	$template->display($params);
	
	if(isset($_SESSION["userid"]))
	{
		$userid = $_SESSION['userid'][0];
		$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
		mysqli_select_db($sql, "friendweb");
		
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('login-header.html');
		$params = array();
		$template->display($params);
?>
	<div id="protokoll">
		<br /><br /><br /><br />
		<div class="site_title">Nachrichten schreiben</div><br /><br /><br /><br />
			
		</div>
	<div id="friends">
<?php
	$select_friends = "SELECT `friendid` FROM `friends` WHERE `userid` = '".$userid."' AND `confirmed` = 1";
	$friends = mysqli_query($sql, $select_friends);
	for($j = 0; $array[$j] = mysqli_fetch_assoc($friends); $j++);
	array_pop($array);
	
	foreach($array as $current_friendid)
	{
		$get_user = "SELECT `name` FROM `users` WHERE `userid` = '".$current_friendid['friendid']."'";
		$get_status = "SELECT `status` FROM `users` WHERE `userid` = '".$current_friendid['friendid']."'";
		$current_friend = mysqli_query($sql, $get_user);
		$current_friend = mysqli_fetch_row($current_friend);
		$status = mysqli_query($sql, $get_status);
		$status = mysqli_fetch_row($status);
		if($status[0] == 1)
		{
			echo "<br /><div class='status_on'><br />&nbsp;&nbsp;&nbsp;".$current_friend[0]."<br /><br /></div>";
		}
		else
		{
			echo "<br /><div class='status_off'><br />&nbsp;&nbsp;&nbsp;".$current_friend[0]."<br /><br /></div>";
		}
	}
?>
	</div>
<?php
		mysqli_close($sql);
	}
	else
	{
		header("Location: index.php");
	}
?>