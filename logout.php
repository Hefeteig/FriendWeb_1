<?php
	session_destroy();
	/*$_SESSION = array();
	if (ini_get("session.use_cookies"))
	{
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params["path"],
			$params["domain"], $params["secure"], $params["httponly"]
		);
	}*/

	require_once 'lib/Twig/Autoloader.php';
	Twig_Autoloader::register();
	$loader = new Twig_Loader_Filesystem('./');
	$twig = new Twig_Environment($loader, array());
	$template = $twig->loadTemplate('login.html');
	$params = array(
		"if_failed" => "<div class='alert alert-success'>Du bist nun ausgeloggt.</div>",
		"register" => '',
		"email" => '',
		"password" => '',
		"Name" => '',
		"Mail" => '',
		"Passwort_1" => '',
		"Passwort_2" => ''
	);
	$template->display($params);
?>