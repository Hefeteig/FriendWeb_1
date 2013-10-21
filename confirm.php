<?php
	$sql_open = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($sql_open, "friendweb");
	$query = "UPDATE `users` SET `active` = 1 WHERE `email` = ".$_GET['email']."";
	mysqli_query($sql_open, $query);
	mysqli_close($sql_open);
	
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
?>