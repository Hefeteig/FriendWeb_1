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
		<br /><br /><br /><br />
		<div class="site_title">Nachrichten schreiben</div><br /><br /><br /><br />
			<div class="write_messages">
				<form name="write_message" action="write_message.php" method="post">
					Empfänger:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="receiver" size="40" maxlength="50" autofocus/><br /><br />
					Nachricht:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="message" cols="10" rows="5" maxlength="999"></textarea><br /><br /><br />
					<div class="center">
					<button type="submit" class="btn btn-large btn-primary">Senden</button>
					</div>
				</form><br />
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
				echo "<br /><div class='status_on round_corners'><br />&nbsp;&nbsp;&nbsp;".$current_friend[0]."<br /><br /></div>";
			}
			else
			{
				echo "<br /><div class='status_off round_corners'><br />&nbsp;&nbsp;&nbsp;".$current_friend[0]."<br /><br /></div>";
			}
		}
?>
	</div>
<?php
		if(isset($_POST['receiver']) && $_POST['message'] == '')
		{
			echo "<div class='alert alert-block alert_message'>Bitte gib eine Nachricht ein.</div>";
		}
		elseif(isset($_POST['message']) && $_POST['receiver'] == '')
		{
			echo "<div class='alert alert-block alert_message'>Bitte gib eine Nachricht ein.</div>";
		}
		if(isset($_POST['receiver']) && isset($_POST['message']))
		{
			//Eingaben filtern
			$receiver = $_POST['receiver'];
			$message = $_POST['message'];
			$receiver = trim($receiver);
			$receiver = strip_tags($receiver);
			$receiver = mysqli_real_escape_string($sql, $receiver);
			$message = strip_tags($message);
			$message = mysqli_real_escape_string($sql, $message);
			
			//Empfänger ID ermitteln
			$get_fid = "SELECT `userid` FROM `users` WHERE `name` = '".$receiver."'";
			$fid = mysqli_query($sql, $get_fid);
			$fid = mysqli_fetch_row($fid);
			
			//Empfängeraccount bestätigt?
			$is_active = "SELECT `active` FROM `users` WHERE `userid` = '".$fid[0]."'";
			$active = mysqli_query($sql, $is_active);
			$active = mysqli_fetch_row($active); 
			
			//Empfänger in Kontaktliste?
			$is_friend = "SELECT `confirmed` FROM `friends` WHERE `userid` = '".$userid."' AND `friendid` = '".$fid[0]."'";
			$friend = mysqli_query($sql, $is_friend);
			$friend = mysqli_fetch_row($friend);
			
			if($active[0] == '0')
			{
				//Kontakt hat Account noch nicht aktiviert
				echo "<div class='alert alert-block alert_message'>Der ausgewählte Nutzer hat seinen Account noch nicht bestätigt, du kannst ihm erst eine Nachricht schicken wenn der Account aktiviert wurde.</div>";
			}
			elseif($fid[0] == 0)
			{
				//Empfänger nicht vorhanden
				echo "<div class='alert alert-block alert_message'>Du kannst &quot;".$receiver."&quot; keine Nachricht schreiben, weil er nicht in deiner Kontaktliste ist oder weil es ihn nicht gibt.</div>";
			}
			elseif($fid[0] == $userid)
			{
				//Nachricht an sich selber
				echo "<div class='alert alert-block alert_message'>Bist du schon so verzweifelt, dass du mit dir selber chatten willst?</div>";
			}
			elseif($friend[0] == 0)
			{
				//Empfänger nicht in Kontaktliste aber Anfrage gesendet
				echo "<div class='alert alert-block alert_message'>Du kannst &quot;".$receiver."&quot; keine Nachricht schreiben, weil er deine Kontaktanfrage noch nicht bestätigt hat.</div>";
			}
			elseif($friend[0] == 1)
			{
				//Senden
				//Username bestimmen
				$get_user = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
				$user = mysqli_query($sql, $get_user);
				
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
				mysqli_query($sql, $insert_message);
				echo "<div class='alert alert-success alert_message'>Nachricht erfolgreich versendet. Der Chat wurde in den <a href='message_course.php'>Nachrichtenverlauf</a> verschoben.</div>";
			}
			else
			{
				//Unbekannter Fehler
				echo "<div class='alert alert-block alert_message'>Ein unbekannter Fehler ist aufgetreten, bitte <a href='contact.php'>kontaktiere</a> den Administrator.</div>";
			}
		}
		mysqli_close($sql);
	}
	else
	{
		header("Location: index.php");
	}
?>