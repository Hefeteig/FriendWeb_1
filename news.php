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
			<b><i class="icon-ok"></i> 05.11.2013:</b><br /><br />
			FriendWeb ist online.
		</div>
	</div>
	<div id="friends">
		<br />Kontakte
	</div>
<?php
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
?>
		<div class="main_field">
			<br /><br /><br />
			<div class="site_title">News</div><br /><br /><br />
			<div class="news_article">
				<b><i class="icon-ok"></i> 05.11.2013:</b><br /><br />
				FriendWeb ist online.
			</div>
		</div>
<?php
	}
?>





