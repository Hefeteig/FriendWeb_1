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
		<br /><br /><br />
		<div class="site_title">Impressum</div><br /><br /><br />
		<div class="login_causes">
			Impressum
		</div>
	</div>
	<div id="friends">
<?php
	require 'friends.php';
	mysqli_close($sql);
?>
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
		<div class="causes">
			<div class="question">Impressum:</div><br /><br />
			Impressum
		</div>
	</div>
<?php
	}
?>