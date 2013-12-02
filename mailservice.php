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
		error_reporting(0);
		
		//E-Mailcounter updaten
		$db_date_query = "SELECT `time` FROM `mailservice` WHERE `userid` = '".$userid."'";
		$db_time = mysql_query($db_date_query);
		$db_time = mysql_fetch_row($db_time);
		$cur_date = date("Y-m-d");
		
		$contains = strpos($db_time[0], $cur_date);
		if($contains === false)
		{
			$update = "UPDATE `mailservice` SET `written_emails` = 0 WHERE `userid` = '".$userid."'";
			mysql_query($update);
		}
		
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
		<div class="site_title">Mailservice</div><br /><br /><br /><br />
			<div class="login_causes">
			<?php
				//Bestimmen wie viele E-Mails noch übrig sind
				$written = "SELECT `written_emails` FROM `mailservice` WHERE `userid` = '".$userid."'";
				$result = mysql_query($written);
				$still = mysql_fetch_row($result);
				$still = $still[0];
				settype($still, "integer");
				$still = 15 - $still;
				
				echo "<div class='alert alert-info'>Du kannst noch ".$still." E-Mails schreiben.<button type='button' class='close' data-dismiss='alert'>&times;</button></div><br />";
					
				if (isset ($_POST['to']) or isset ($_POST['from_1']) or isset ($_POST['from_2']) or isset ($_POST['head']) or isset ($_POST['text']) or isset ($_POST['count']))
				{
					//Eingaben Filtern
					$to = trim($_POST['to']);
					$from_1 = trim($_POST['from_1']);
					$from_2 = trim($_POST['from_2']);
					$count = trim($_POST['count']);
					$to = strip_tags($to);
					$from_1 = strip_tags($from_1);
					$from_2 = strip_tags($from_2);
					$count = strip_tags($count);
					$head = strip_tags($_POST['head']);
					$text = strip_tags($_POST['text']);
					$to = mysql_real_escape_string($to);
					$from_1 = mysql_real_escape_string($from_1);
					$from_2 = mysql_real_escape_string($from_2);
					$count = mysql_real_escape_string($count);
					$head = mysql_real_escape_string($head);
					$text = mysql_real_escape_string($text);
					
					if ( empty ($to) == 1 or empty ($from_1) == 1 or empty ($from_2) == 1 or empty ($head) == 1 or empty ($text) == 1 or !is_numeric($count) or empty ($count) == 1 or $count > 15 
						or preg_match("(to:|cc:|bcc:|from:|subject:|reply-to:|content-type:|MIME-Version:|multipart/mixed|Content-Transfer-Encoding:)ims", $to . $from_1 . $from_2 . $head . $text . $count)
						or filter_var($from_2, FILTER_VALIDATE_EMAIL) == FALSE or $_POST['bedingung'] != "confirmed")
					{
						//Wenn Fehler aufgetreten
						if(preg_match("(to:|cc:|bcc:|from:|subject:|reply-to:|content-type:|MIME-Version:|multipart/mixed|Content-Transfer-Encoding:)ims", $to . $from_1 . $from_2 . $head . $text . $count))
						{
							$error = "Du hast fehlerhafte Ausdrücke eingegeben.";
						}
						elseif(empty ($to) == 1 or empty ($from_1) == 1 or empty ($from_2) == 1 or empty ($head) == 1 or empty ($text) == 1 or !is_numeric($count) or empty ($count) == 1)
						{
							$error = "Bitte fülle alle Felder aus.";
						}
						elseif($_POST['bedingung'] != "confirmed")
						{
							$error = "Du musst die Nutzungsbedingungen lesen und akzeptieren, um den Mailservice zu nutzen.";
						}
						elseif(filter_var($from_2, FILTER_VALIDATE_EMAIL) == FALSE)
						{
							$error = "Die Absenderadresse entspricht nicht dem gültigen E-Mail Schema, bitte trage eine neue ein. Es muss keine existierende E-Mail sein, sondern nur die notwendigen Ausdrücke (z.B. @) enthalten.";
						}
						elseif(!is_numeric($count) or $count > 15)
						{
							$error = "Bitte gib eine Zahl unter 15 ein.";
						}
						else
						{
							$error = "Es wurden nicht alle Felder korrekt ausgefüllt.";
						}
						echo "<div class='alert alert-error'>".$error."<button type='button' class='close' data-dismiss='alert'>&times;</button></div><br /><br />";
						
						require_once 'lib/Twig/Autoloader.php';
						Twig_Autoloader::register();
						$loader = new Twig_Loader_Filesystem('./');
						$twig = new Twig_Environment($loader, array());
						$template = $twig->loadTemplate('mailservice.html');
						$params = array(
							"to" => $to,
							"from1" => $from_1,
							"from2" => $from_2,
							"head" => $head,
							"text" => $text,
							"count" => $count
						);
						$template->display($params);
					}
					else
					{	
						//E-Mailanzahl abrufen
						$check = "SELECT `written_emails` FROM `mailservice` WHERE `userid` = '".$userid."'";
						$check_result = mysql_query($check);
						$check_result = mysql_fetch_row($check_result);
						
						$left = $check_result[0] + $count;
						
						if($check_result[0] >= 15)
						{
							//Limit erreicht
							echo "<div class='alert alert-error alert_message'>Dein Limit ist schon erreicht, bitte schau später nochmal vorbei.</div><br /><br />";
							require_once 'lib/Twig/Autoloader.php';
							Twig_Autoloader::register();
							$loader = new Twig_Loader_Filesystem('./');
							$twig = new Twig_Environment($loader, array());
							$template = $twig->loadTemplate('mailservice.html');
							$params = array(
								"to" => $to,
								"from1" => $from_1,
								"from2" => $from_2,
								"head" => $head,
								"text" => $text,
								"count" => $count
							);
							$template->display($params);
						}
						elseif($left > 15)
						{
							//Zu viele E-Mails eingetragen
							echo "<div class='alert alert-error alert_message'>Du kannst nicht so viele E-Mails schreiben, da die Anzahl der geschriebenen E-Mails über deinem täglichen Limit liegen würde.</div><br /><br />";
							require_once 'lib/Twig/Autoloader.php';
							Twig_Autoloader::register();
							$loader = new Twig_Loader_Filesystem('./');
							$twig = new Twig_Environment($loader, array());
							$template = $twig->loadTemplate('mailservice.html');
							$params = array(
								"to" => $to,
								"from1" => $from_1,
								"from2" => $from_2,
								"head" => $head,
								"text" => $text,
								"count" => $count
							);
							$template->display($params);
						}
						elseif($left <= 15)
						{
							//Senden
							ini_set('SMTP','smtp.1und1.de');
							ini_set('smtp_port',25);
							
							$convert = $text;
							$convert = str_replace("ä", "&auml;", str_replace("ö", "&ouml;", str_replace("ü", "&uuml;", str_replace("Ä", "&Auml;", str_replace("Ö", "&Ouml;", str_replace("Ü", "&Uuml;", str_replace("ß", "&szlig;", $convert)))))));
							$absender = $from_1 . ' <' . $from_2 . '>';
							$headers   = array();
							$headers[] = "MIME-Version: 1.0";
							$headers[] = "Content-type: text/html; charset=iso-8859-1";
							$headers[] = "From: {$absender}";
							$i = 0;
							while($i < $count)
							{
								mail ($to, $head, $convert, implode("\r\n",$headers));
								$i++;
							}
							$count_up = "UPDATE `mailservice` SET `written_emails` = '".$left."', `time` = NOW() WHERE `userid` = '".$userid."'";
							mysql_query($count_up);
							
							echo "<div class='alert alert-success alert_message'>E-Mails erfolgreich versendet.</div><br /><br />";
							require_once 'lib/Twig/Autoloader.php';
							Twig_Autoloader::register();
							$loader = new Twig_Loader_Filesystem('./');
							$twig = new Twig_Environment($loader, array());
							$template = $twig->loadTemplate('mailservice.html');
							$params = array(
								"to" => $to,
								"from1" => $from_1,
								"from2" => $from_2,
								"head" => $head,
								"text" => $text,
								"count" => $count
							);
							$template->display($params);
						}
						else
						{
							//Unbekannter Fehler
							echo "<div class='alert alert-error alert_message'>Es ist ein unbekannter Fehler aufgetreten, bitte <a href='contact.php'>kontaktiere</a> den Administrator.</div>";
							require_once 'lib/Twig/Autoloader.php';
							Twig_Autoloader::register();
							$loader = new Twig_Loader_Filesystem('./');
							$twig = new Twig_Environment($loader, array());
							$template = $twig->loadTemplate('mailservice.html');
							$params = array(
								"to" => $to,
								"from1" => $from_1,
								"from2" => $from_2,
								"head" => $head,
								"text" => $text,
								"count" => $count
							);
							$template->display($params);
						}
					}
				}
				else
				{
					//Seite zum ersten Mal aufgerufen
					require_once 'lib/Twig/Autoloader.php';
					Twig_Autoloader::register();		
					$loader = new Twig_Loader_Filesystem('./');
					$twig = new Twig_Environment($loader, array());
					$template = $twig->loadTemplate('mailservice.html');
					$params = array(
						"to" => '',
						"from1" => '',
						"from2" => '',
						"head" => '',
						"text" => '',
						"count" => ''
					);
					$template->display($params);
				}
				mysql_close($sql);
?>
			</div>
	</div>
	
	<div id="friends">
	</div>
<?php
	}
	else
	{
		header("Location: index.php");
	}
?>