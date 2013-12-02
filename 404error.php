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
		require 'db.php';
		
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
		<div class="site_title">404 Fehler</div><br /><br /><br /><br />
		<div class="login_causes">
			Die angeforderte Seite gibt es nicht.<br />Bitte überprüfe die URL auf Tippfehler oder <a href="contact.php">kontaktiere</a> gegebenenfalls den Administrator.<br />
		</div>
	</div>
	
	<div id="friends">
	</div>
<?php
		mysql_close($sql);
	}
	else
	{
		header("Location: index.php");
	}
?>