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
		<div class="site_title">FAQ</div><br /><br /><br />
		<div class="login_causes">
			<b>-</b> Wie wird alles <b>finanziert</b>? Antwort: Bisher ausschlie&szlig;lich aus eigener Tasche des Administrators.<br /><br />
			<b>-</b> Wie lange wird der <b>Chatverlauf gespeichert</b>? Antwort: Alle Nachrichten werden jeden Monatsbeginn gelöscht.<br /><br />
			<b>-</b> Was wird alles <b>gespeichert</b>? Antwort: Nutzername, E-Mail, Passwort und Registrierungsdatum. Nat&uuml;rlich nicht im Klartext, sondern nur die gehashten Werte.<br /><br />
			<b>-</b> Wo kann ich <b>Vorschl&auml;ge f&uuml;r Plugins</b> einreichen? Antwort: Schreib einfach eine E-Mail mit den Betreff "Vorschlag f&uuml;r Plugin" an contact@friend-web.de und wir antworten in 2-3 Werktagen.<br /><br />
			<b>-</b> Wird FriendWeb <b>mir E-Mails schreiben</b>? Antwort: Nur zur Accountbest&auml;tigung, andere Informationen werden von FriendWeb nur in der Kategorie "News" mitgeteilt. Falls du doch eine E-Mail von FriendWeb bekommen hast, ist diese eine Phishing E-Mail.<br /><br />
			<b>-</b> In welchen Abständen wird der <b>Chat und die Kontaktliste aktualisiert</b>? Antwort: Beide werden aller 10 Sekunden aktualisiert, es kann aber auch vorkommen dass eine Anfrage an den Server ein paar Sekunden länger dauert.<br /><br />
			<b>-</b> Wann wird die Anzahl meiner <b>geschriebenen E-Mails beim Mailservice zurückgesetzt</b>? Antwort: Jeden Tag um 00:00 Uhr Serverzeit.<br /><br />
			<b>-</b> Wo kann ich <b>Bugs oder Sicherheitsl&uuml;cken melden</b>? Antwort: Schreibe daf&uuml;r eine E-Mail an bugs@friend-web.de. Vorraussetzungen f&uuml;r eine Belohnung sind:<br />1. Der Fehler darf noch nicht gemeldet worden sein.<br />2. Nur Meldungen &uuml;ber diese E-Mail Adresse sind g&uuml;ltig.<br />3. Der Fehler darf, nach seiner Entdeckung, nicht weiter ausgenutzt und umgehend gemeldet werden.<br /><br />
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
?>
	<div class="main_field">
		<div class="causes">
			<div class="question">FAQ:</div><br /><br />
			<b>-</b> Wie wird alles <b>finanziert</b>? Antwort: Bisher ausschlie&szlig;lich aus eigener Tasche des Administrators.<br /><br />
			<b>-</b> Wie lange wird der <b>Chatverlauf gespeichert</b>? Antwort: Alle Nachrichten werden jeden Monatsbeginn gelöscht.<br /><br />
			<b>-</b> Was wird alles <b>gespeichert</b>? Antwort: Nutzername, E-Mail, Passwort und Registrierungsdatum. Nat&uuml;rlich nicht im Klartext, sondern nur die gehashten Werte.<br /><br />
			<b>-</b> Wo kann ich <b>Vorschl&auml;ge f&uuml;r Plugins</b> einreichen? Antwort: Schreib einfach eine E-Mail mit den Betreff "Vorschlag f&uuml;r Plugin" an contact@friend-web.de und wir antworten in 2-3 Werktagen.<br /><br />
			<b>-</b> Wird FriendWeb <b>mir E-Mails schreiben</b>? Antwort: Nur zur Accountbest&auml;tigung, andere Informationen werden von FriendWeb nur in der Kategorie "News" mitgeteilt. Falls du doch eine E-Mail von FriendWeb bekommen hast, ist diese eine Phishing E-Mail.<br /><br />
			<b>-</b> In welchen Abständen wird der <b>Chat und die Kontaktliste aktualisiert</b>? Antwort: Beide werden aller 10 Sekunden aktualisiert, es kann aber auch vorkommen dass eine Anfrage an den Server ein paar Sekunden länger dauert.<br /><br />
			<b>-</b> Wann wird die Anzahl meiner <b>geschriebenen E-Mails beim Mailservice zurückgesetzt</b>? Antwort: Jeden Tag um 00:00 Uhr Serverzeit.<br /><br />
			<b>-</b> Wo kann ich <b>Bugs oder Sicherheitsl&uuml;cken melden</b>? Antwort: Schreibe daf&uuml;r eine E-Mail an bugs@friend-web.de. Vorraussetzungen f&uuml;r eine Belohnung sind:<br />1. Der Fehler darf noch nicht gemeldet worden sein.<br />2. Nur Meldungen &uuml;ber diese E-Mail Adresse sind g&uuml;ltig.<br />3. Der Fehler darf, nach seiner Entdeckung, nicht weiter ausgenutzt und umgehend gemeldet werden.<br /><br />
		</div>
	</div>
<?php
	}
?>