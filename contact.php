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
		<div class="site_title">Kontakt</div><br /><br /><br />
		<div class="login_causes">
			Um Kontakt mit dem FriendWeb-Team aufzunehmen, schreibe bitte eine E-Mail an contact@friend-web.de.<br /><br />Bitte schildere dein Anliegen so genau wie m&ouml;glich, damit wir schnell und effektiv helfen k&ouml;nnen.
			<br /><br />Eine Antwort erfolgt normalerweise nach 2-3 Werktagen.
		</div>
	</div>
	<div id="friends">
	</div>
<?php
		mysqli_close($sql);
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
			<div class="question">Kontakt:</div><br /><br />
			Um Kontakt mit dem FriendWeb-Team aufzunehmen, schreibe bitte eine E-Mail an contact@friend-web.de.<br /><br />Bitte schildere dein Anliegen so genau wie m&ouml;glich, damit wir schnell und effektiv helfen k&ouml;nnen.
			<br /><br />Eine Antwort erfolgt normalerweise nach 2-3 Werktagen.
		</div>
	</div>
<?php
	}
?>