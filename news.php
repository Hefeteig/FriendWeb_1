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
		<br /><br /><br />
		<div class="site_title">News</div><br /><br /><br />
		<div class="news_article">
			<b><i class="icon-ok"></i> 08.12.2013 23:30</b><br /><br />
			Das Plugin "Startseite modifizieren" ist erschienen.<br /><br /><br /><br />
			<b><i class="icon-ok"></i> 01.12.2013 23:30</b><br /><br />
			FriendWeb ist online.
		</div>
	</div>
	<div id="friends">
	</div>
<?php
		mysql_close($sql);
	}
	else
	{
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('logout-header.html');
		$params = array();
		$template->display($params);
	}
?>





