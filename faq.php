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
		<div class="site_title">FAQ</div><br /><br /><br />
		<div class="login_causes">
			<b>-</b> Wie wird alles <b>finanziert</b>? Antwort: Bisher ausschlie&szlig;lich aus eigener Tasche des Administrators.<br /><br />
			<b>-</b> Wie lange wird der <b>Chatverlauf gespeichert</b>? Antwort: Alle Nachrichten werden an jeden Monatsbeginn gelöscht.<br /><br />
			<b>-</b> Was wird alles <b>gespeichert</b>? Antwort: Nutzername, E-Mail, Passwort und Registrierungsdatum. Nat&uuml;rlich nicht im Klartext, sondern nur die gehashten Werte.<br /><br />
			<b>-</b> Wo kann ich <b>Vorschl&auml;ge f&uuml;r Plugins</b> einreichen? Antwort: Schreib einfach eine E-Mail mit den Betreff "Vorschlag f&uuml;r Plugin" an contact@friend-web.de und wir antworten in 2-3 Werktagen.<br /><br />
			<b>-</b> Wird FriendWeb <b>mir E-Mails schreiben</b>? Antwort: Nur zur Accountbest&auml;tigung, andere Informationen werden von FriendWeb nur in der Kategorie "News" mitgeteilt. Falls du doch eine E-Mail von FriendWeb bekommen hast, ist diese eine Phishing E-Mail.<br /><br />
			<b>-</b> Wo kann ich <b>Bugs oder Sicherheitsl&uuml;cken melden</b>? Antwort: Schreibe daf&uuml;r eine E-Mail an bugs@friend-web.de. Vorraussetzungen f&uuml;r eine Belohnung sind:<br />1. Der Fehler darf noch nicht gemeldet worden sein.<br />2. Nur Meldungen &uuml;ber diese E-Mail Adresse sind g&uuml;ltig.<br />3. Der Fehler darf, nach seiner Entdeckung, nicht weiter ausgenutzt und umgehend gemeldet werden.<br /><br />
		</div>
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
			<div class="question">FAQ:</div><br /><br />
			<b>-</b> Wie wird alles <b>finanziert</b>? Antwort: Bisher ausschlie&szlig;lich aus eigener Tasche des Administrators.<br /><br />
			<b>-</b> Wie lange wird der <b>Chatverlauf gespeichert</b>? Antwort: Alle Nachrichten werden an jeden Monatsbeginn gelöscht.<br /><br />
			<b>-</b> Was wird alles <b>gespeichert</b>? Antwort: Nutzername, E-Mail, Passwort und Registrierungsdatum. Nat&uuml;rlich nicht im Klartext, sondern nur die gehashten Werte.<br /><br />
			<b>-</b> Wo kann ich <b>Vorschl&auml;ge f&uuml;r Plugins</b> einreichen? Antwort: Schreib einfach eine E-Mail mit den Betreff "Vorschlag f&uuml;r Plugin" an contact@friend-web.de und wir antworten in 2-3 Werktagen.<br /><br />
			<b>-</b> Wird FriendWeb <b>mir E-Mails schreiben</b>? Antwort: Nur zur Accountbest&auml;tigung, andere Informationen werden von FriendWeb nur in der Kategorie "News" mitgeteilt. Falls du doch eine E-Mail von FriendWeb bekommen hast, ist diese eine Phishing E-Mail.<br /><br />
			<b>-</b> Wo kann ich <b>Bugs oder Sicherheitsl&uuml;cken melden</b>? Antwort: Schreibe daf&uuml;r eine E-Mail an bugs@friend-web.de. Vorraussetzungen f&uuml;r eine Belohnung sind:<br />1. Der Fehler darf noch nicht gemeldet worden sein.<br />2. Nur Meldungen &uuml;ber diese E-Mail Adresse sind g&uuml;ltig.<br />3. Der Fehler darf, nach seiner Entdeckung, nicht weiter ausgenutzt und umgehend gemeldet werden.<br /><br />
		</div>
	</div>
<?php
	}
?>