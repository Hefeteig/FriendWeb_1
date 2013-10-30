<?php
	$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($sql, "friendweb");
	
	$email = $_GET['email'];
	$get_userid = "SELECT `userid` FROM `users` WHERE `email` = '".$email."'";
	$userid = mysqli_query($sql, $get_userid);
	$userid = mysqli_fetch_row($userid);
	$userid = $userid[0];
	
	if($userid)
	{
		$set_active = "UPDATE `users` SET `active` = 1 WHERE `userid` = ".$userid."";
		mysqli_query($sql, $set_active);
		
		$insert_activatedplugins_1 = "INSERT INTO `activatedplugins` (`plugin`, `user`) VALUES ('MainStructure', ".$userid.")";
		$insert_activatedplugins_2 = "INSERT INTO `activatedplugins` (`plugin`, `user`) VALUES ('StyleStructure', ".$userid.")";
		mysqli_query($sql, $insert_activatedplugins_1);
		mysqli_query($sql, $insert_activatedplugins_2);
		
		$insert_friends = "INSERT INTO `friends` (`userid`, `friends`) VALUES (".$userid.", '')";
		mysqli_query($sql, $insert_friends);
		
		mysqli_close($sql);
		
		require_once "lib/Twig/Autoloader.php";
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem("./");
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate("login.html");
		$params = array(
			"if_failed" => "<div class='alert alert-info'>Dein Account wurde erfolgreich best&auml;tigt.</div>",
			"register" => "",
			"email" => "",
			"password" => "",
			"Name" => "",
			"Mail" => "",
			"Passwort_1" => "",
			"Passwort_2" => ""
		);
		$template->display($params);
	}
	else
	{
		require_once "lib/Twig/Autoloader.php";
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem("./");
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate("login.html");
		$params = array(
			"if_failed" => "<div class='alert alert-error'>Dein Account konnte nicht best&auml;tigt werden. Wenn du nicht den Best&auml;tigungslink ver&auml;ndert hast, <a href='contact.php'>wende</a> dich bitte an den Administrator.</div>",
			"register" => "",
			"email" => "",
			"password" => "",
			"Name" => "",
			"Mail" => "",
			"Passwort_1" => "",
			"Passwort_2" => ""
		);
		$template->display($params);
	}
?>