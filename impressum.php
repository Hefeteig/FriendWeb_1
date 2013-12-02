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
		<div class="site_title">Impressum</div><br /><br /><br />
		<div class="login_causes">
			<p>Dies ist eine private Internetpräsenz.<p>
			<h3>Haftungsausschluss</h3>
            <p><b>1. Inhalt des Onlineangebotes:</b><br> Der Autor übernimmt  keinerlei Gewähr für die Aktualität, Korrektheit,    Vollständigkeit  oder Qualität der bereitgestellten Informationen.    Haftungsansprüche  gegen den Autor, welche sich auf Schäden materieller    oder ideeller Art  beziehen, die durch die Nutzung oder Nichtnutzung  der   dargebotenen  Informationen bzw. durch die Nutzung fehlerhafter  und   unvollständiger  Informationen verursacht wurden, sind  grundsätzlich   ausgeschlossen,  sofern seitens des Autors kein  nachweislich   vorsätzliches oder grob  fahrlässiges Verschulden  vorliegt.   Alle Angebote sind freibleibend und  unverbindlich. Der  Autor behält es   sich ausdrücklich vor, Teile der  Seiten oder das  gesamte Angebot ohne   gesonderte Ankündigung zu  verändern, zu  ergänzen, zu löschen oder die   Veröffentlichung zeitweise  oder  endgültig einzustellen.<p>
            <p><b>2. Verweise und Links:</b><br>  Bei direkten oder indirekten Verweisen auf fremde  Webseiten    (“Hyperlinks”), die außerhalb des Verantwortungsbereiches  des Autors    liegen, würde eine Haftungsverpflichtung ausschließlich in  dem Fall in    Kraft treten, in dem der Autor von den Inhalten Kenntnis  hat und es  ihm   technisch möglich und zumutbar wäre, die Nutzung im  Falle  rechtswidriger   Inhalte zu verhindern.   Der Autor erklärt  hiermit  ausdrücklich, dass zum Zeitpunkt der   Linksetzung keine  illegalen  Inhalte auf den zu verlinkenden Seiten   erkennbar waren. Auf  die  aktuelle und zukünftige Gestaltung, die Inhalte   oder die   Urheberschaft der verlinkten/verknüpften Seiten hat der Autor     keinerlei Einfluss. Deshalb distanziert er sich hiermit ausdrücklich von     allen Inhalten aller verlinkten /verknüpften Seiten, die nach der     Linksetzung verändert wurden. Diese Feststellung gilt für alle  innerhalb    des eigenen Internetangebotes gesetzten Links und Verweise  sowie für    Fremdeinträge in vom Autor eingerichteten Gästebüchern,     Diskussionsforen, Linkverzeichnissen, Mailinglisten und in allen anderen     Formen von Datenbanken, auf deren Inhalt externe Schreibzugriffe     möglich sind. Für illegale, fehlerhafte oder unvollständige Inhalte und     insbesondere für Schäden, die aus der Nutzung oder Nichtnutzung     solcherart dargebotener Informationen entstehen, haftet allein der     Anbieter der Seite, auf welche verwiesen wurde, nicht derjenige, der     über Links auf die jeweilige Veröffentlichung lediglich verweist.<p>
			<p><b>3. Urheber- und Kennzeichenrecht:</b><br> Der Autor ist  bestrebt, in allen  Publikationen die Urheberrechte der   verwendeten  Bilder, Grafiken,  Tondokumente, Videosequenzen und Texte zu   beachten,  von ihm selbst  erstellte Bilder, Grafiken, Tondokumente,    Videosequenzen und Texte zu  nutzen oder auf lizenzfreie Grafiken,    Tondokumente, Videosequenzen  und Texte zurückzugreifen.   Alle innerhalb  des Internetangebotes  genannten und ggf. durch Dritte   geschützten  Marken- und Warenzeichen  unterliegen uneingeschränkt den   Bestimmungen  des jeweils gültigen  Kennzeichenrechts und den   Besitzrechten der  jeweiligen eingetragenen  Eigentümer. Allein aufgrund   der bloßen  Nennung ist nicht der Schluss  zu ziehen, dass Markenzeichen   nicht  durch Rechte Dritter geschützt  sind!   Das Copyright für  veröffentlichte, vom Autor selbst erstellte  Objekte   bleibt allein beim  Autor der Seiten. Eine Vervielfältigung  oder   Verwendung solcher  Grafiken, Tondokumente, Videosequenzen und  Texte in   anderen  elektronischen oder gedruckten Publikationen ist  ohne   ausdrückliche  Zustimmung des Autors nicht gestattet.<p>
			<p><b>4. Datenschutz:</b><br> Sofern innerhalb des  Internetangebotes die Möglichkeit zur Eingabe    persönlicher oder  geschäftlicher Daten (Emailadressen, Namen,    Anschriften) besteht, so  erfolgt die Preisgabe dieser Daten seitens des    Nutzers auf  ausdrücklich freiwilliger Basis. Die Inanspruchnahme und    Bezahlung  aller angebotenen Dienste ist – soweit technisch möglich  und   zumutbar –  auch ohne Angabe solcher Daten bzw. unter Angabe    anonymisierter Daten  oder eines Pseudonyms gestattet. Die Nutzung der  im   Rahmen des  Impressums oder vergleichbarer Angaben veröffentlichten    Kontaktdaten  wie Postanschriften, Telefon- und Faxnummern sowie    Emailadressen durch  Dritte zur Übersendung von nicht ausdrücklich    angeforderten  Informationen ist nicht gestattet. Rechtliche Schritte    gegen die  Versender von sogenannten Spam-Mails bei Verstössen gegen    dieses  Verbot sind ausdrücklich vorbehalten.<p>
			<p><b>5. Rechtswirksamkeit:</b><br> dieses Haftungsausschlusses Dieser Haftungsausschluss ist als Teil des Internetangebotes zu     betrachten, von dem aus auf diese Seite verwiesen wurde. Sofern Teile     oder einzelne Formulierungen dieses Textes der geltenden Rechtslage     nicht, nicht mehr oder nicht vollständig entsprechen sollten, bleiben     die übrigen Teile des Dokumentes in ihrem Inhalt und ihrer Gültigkeit     davon unberührt.<p>
            <p><b>Datenschutzerklärung:</b><br> Wir behandeln Ihre  personenbezogenen Daten vertraulich und  entsprechend   der gesetzlichen  Datenschutzvorschriften. Wir nehmen den  Schutz Ihrer   persönlichen  Daten sehr ernst.<p>
			<p><b>Erheben von Daten:</b><br> Wir erheben, verarbeiten und  nutzen personenbezogene Daten nur,  soweit   sie für die Begründung,  inhaltliche Ausgestaltung oder  Änderung des   Rechtsverhältnisses  erforderlich sind (Bestandsdaten).  Personenbezogene   Daten über die  Inanspruchnahme unserer  Internetseiten (Nutzungsdaten)   erheben,  verarbeiten und nutzen wir  nur, soweit dies erforderlich ist,   um dem  Nutzer die Inanspruchnahme  des Dienstes zu ermöglichen oder    abzurechnen.<p>
			<p><b>Übermitteln von Daten:</b><br> Wir übermitteln  personenbezogene Daten an Dritte nur dann, wenn dies  im   Rahmen der  Vertragsabwicklung notwendig ist etwa an die mit der    Lieferung der  Ware betrauten Unternehmen oder das mit der    Zahlungsabwicklung  beauftragte Kreditinstitut. Eine weitergehende Übermittlung der  Daten erfolgt nicht bzw. nur dann,    wenn Sie der Übermittlung  ausdrücklich zugestimmt haben. Eine  Weitergabe   Ihrer Daten an Dritte  ohne ausdrückliche Einwilligung etwa  zu Zwecken   der Werbung erfolgt  nicht. Datenverarbeitung auf dieser  Internetseite   Wir erheben und  speichern automatisch Informationen in  Log Files, die   Ihr Browser  automatisch an uns übermittelt. Dies sind:    -Browsertyp    -Browserversion  -verwendetes Betriebsystem   -Referrer URL  -IP Adresse   -Uhrzeit der Serveranfrage. Diese Daten sind nicht bestimmten  Personen zuordenbar. Eine    Zusammenführung dieser Daten mit anderen  Datenquellen wird nicht    vorgenommen.<p>
			<p><b>Cookies:</b><br> Die Internetseiten verwenden  teilweise so genannte Cookies. Diese  dienen   dazu, unser Angebot  nutzerfreundlicher, effektiver und  sicherer zu   machen. Cookies sind  kleine Textdateien, die auf Ihrem  Rechner abgelegt   werden und die Ihr  Browser speichert. Die meisten  der von uns   verwendeten Cookies sind so  genannte “Session-Cookies”.  Sie werden nach   Ende Ihres Besuchs  automatisch gelöscht. Cookies  richten auf Ihrem   Rechner keinen Schaden  an und enthalten keine  Viren.<p>
			<p><b>Webanalyse:</b><br> Diese Website benutzt einen  Webanalysedienst der sog. “Cookies”    verwendet. Cookies sind  Textdateien, die auf Ihrem Computer gespeichert    werden und die eine  Analyse der Benutzung der Website durch Sie    ermöglicht. Die durch den  Cookie erzeugten Informationen über Ihre    Benutzung dieser Website  (einschließlich Ihrer IP-Adresse) wird an  einen   Server in den USA  übertragen und dort gespeichert. Diese  Informationen   werden benutzt,  um die Nutzung dieser Website  auszuwerten, um Reports   über die  Websiteaktivitäten für die  Websitebetreiber zusammenzustellen   und um  weitere mit der  Websitenutzung und der Internetnutzung verbundene    Dienstleistungen zu  erbringen. Auch werden von Seiten des    Webanalysedienstes diese  Informationen gegebenenfalls an Dritte    übertragen, sofern dies  gesetzlich vorgeschrieben oder soweit Dritte    diese Daten im Auftrag  verarbeitet.<p>
			<p>Wir greifen auf  Drittanbieter zurück, um Anzeigen zu schalten, wenn  Sie   unsere Website  besuchen. Diese Unternehmen nutzen möglicherweise    Informationen (dies  schließt nicht Ihren Namen, Ihre Adresse,    E-Mail-Adresse oder  Telefonnummer ein) zu Ihren Besuchen dieser und    anderer Websites,  damit Anzeigen zu Produkten und Diensten geschaltet    werden können, die  Sie interessieren. Falls Sie mehr über diese  Methoden   erfahren  möchten oder wissen möchten, welche Möglichkeiten  Sie haben,   damit  diese Informationen nicht von den Unternehmen  verwendet werden   können,  klicken Sie hier.<p>
			<p>Sie können die Installation der Cookies  durch eine entsprechende    Einstellung Ihrer Browser Software  verhindern. Durch die Nutzung dieser    Website erklären Sie sich mit der  Bearbeitung der über Sie erhobenen    Daten in der zuvor beschriebenen  Art und Weise und zu dem zuvor    benannten Zweck einverstanden.<p>
			<br><br><p>Nicolai Wolters<br>Am Schwedenteich 23<br>01477 Arnsdorf<br>Deutschland /Germany<p>
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
			<div class="question">Impressum:</div><br /><br />
			Impressum
		</div>
	</div>
<?php
	}
?>