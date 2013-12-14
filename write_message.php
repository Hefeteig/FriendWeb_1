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
		<div class="site_title">Nachrichten schreiben</div><br /><br /><br /><br />
			<div class="write_messages">
				<form name="write_message" action="write_message.php" method="post">
					Empfänger:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="receiver" class="input-xlarge" maxlength="50" autofocus/><br /><br />
					Nachricht:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="message" rows="5" class="input-xxlarge" maxlength="999"></textarea><br /><br /><br />
					<div class="center">
					<button type="submit" class="btn btn-large btn-primary">Senden</button>
					</div>
				</form><br />
			</div>
<?php
		if(isset($_POST['receiver']) && $_POST['message'] == '')
		{
			echo "<div class='alert alert-block alert_message'>Bitte gib eine Nachricht ein.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
		}
		elseif(isset($_POST['message']) && $_POST['receiver'] == '')
		{
			echo "<div class='alert alert-block alert_message'>Bitte gib einen Empfänger an.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
		}
		if(isset($_POST['receiver']) && isset($_POST['message']))
		{
			//Eingaben filtern
			$receiver = $_POST['receiver'];
			$message = $_POST['message'];
			$receiver = trim($receiver);
			$receiver = strip_tags($receiver);
			$receiver = mysql_real_escape_string($receiver);
			$message = strip_tags($message);
			$message = mysql_real_escape_string($message);
			
			//Empfänger ID ermitteln
			$get_fid = "SELECT `userid` FROM `users` WHERE `name` = '".$receiver."'";
			$fid = mysql_query($get_fid);
			$fid = mysql_fetch_row($fid);
			
			//Empfängeraccount bestätigt?
			$is_active = "SELECT `active` FROM `users` WHERE `userid` = '".$fid[0]."'";
			$active = mysql_query($is_active);
			$active = mysql_fetch_row($active); 
			
			//Empfänger in Kontaktliste?
			$is_friend = "SELECT `confirmed` FROM `friends` WHERE `userid` = '".$userid."' AND `friendid` = '".$fid[0]."'";
			$friend = mysql_query($is_friend);
			$friend = mysql_fetch_row($friend);
			
			if($active[0] == '0')
			{
				//Kontakt hat Account noch nicht aktiviert
				echo "<div class='alert alert-block alert_message'>Der ausgewählte Nutzer hat seinen Account noch nicht bestätigt, du kannst ihm erst eine Nachricht schicken wenn der Account aktiviert wurde.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			elseif($receiver == '')
			{
				echo "<div class='alert alert-block alert_message'>Bitte gib einen Empfänger ein.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			elseif($message == '')
			{
				echo "<div class='alert alert-block alert_message'>Bitte gib eine Nachricht ein.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			elseif($fid[0] == 0)
			{
				//Empfänger nicht vorhanden
				echo "<div class='alert alert-block alert_message'>Du kannst &quot;".$receiver."&quot; keine Nachricht schreiben, weil es ihn nicht gibt.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			elseif($fid[0] == $userid)
			{
				//Nachricht an sich selber
				echo "<div class='alert alert-block alert_message'>Bist du schon so verzweifelt, dass du mit dir selber chatten willst?<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			elseif($friend[0] == 0)
			{
				//Empfänger nicht in Kontaktliste aber Anfrage gesendet
				echo "<div class='alert alert-block alert_message'>Du kannst &quot;".$receiver."&quot; keine Nachricht schreiben, weil er deine Kontaktanfrage noch nicht bestätigt hat oder du ihm noch keine gesendet hast.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			elseif($friend[0] == 1)
			{
				//Senden
				//Username bestimmen
				$get_user = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
				$user = mysql_query($get_user);
				
				//Verschlüsseln
				
				//Seed errechnen
				$seed = $userid + 284917;
				
				function encodeRand($str, $seed)
				{
					mt_srand($seed);
					$out = array();
					for ($x=0, $l=strlen($str); $x<$l; $x++)
					{
						$out[$x] = (ord($str[$x]) * 3) + mt_rand(350, 16000);
					}
					mt_srand();
					return implode('-', $out);
				}
				$cryptedMessage = encodeRand($message, $seed);
				
				$insert_message = "INSERT INTO `messages` (`from_id`, `to_id`, `content`) VALUES ('".$userid."', '".$fid[0]."', '".$cryptedMessage."')";
				mysql_query($insert_message);
				echo "<div class='alert alert-success alert_message'>Nachricht erfolgreich versendet. Der Chat wurde in den
					<a href='chat.php'>Nachrichtenverlauf</a> verschoben.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
			else
			{
				//Unbekannter Fehler
				echo "<div class='alert alert-block alert_message'>Ein unbekannter Fehler ist aufgetreten, bitte <a href='contact.php'>kontaktiere</a> den Administrator.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			}
		}
		mysql_close($sql);
?>
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