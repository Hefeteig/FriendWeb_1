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
		<div class="site_title">Nachrichtenverlauf</div><br /><br /><br /><br />
<?php
		//Nachricht gesendet
		if(isset($_POST['receiver']) && $_POST['message'] == '')
		{
			echo "<div class='alert alert-block alert_message'>Bitte gib eine Nachricht ein.</div>";
		}
		elseif(isset($_POST['message']) && $_POST['receiver'] == '')
		{
			echo "<div class='alert alert-block alert_message'>Es konnte kein Empfänger festgestellt werden. Falls du nicht die Formulare manipuliert hast <a href='contact.php'>kontaktiere</a> bitte den Administrator.</div>";
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
				echo "<div class='alert alert-block alert_message'>Du kannst &quot;".$receiver."&quot; keine Nachricht schreiben, weil es ihn nicht gibt.</div>";
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
			}
			else
			{
				//Unbekannter Fehler
				echo "<div class='alert alert-block alert_message'>Ein unbekannter Fehler ist aufgetreten, bitte <a href='contact.php'>kontaktiere</a> den Administrator.</div>";
			}
		}
		
		
		//Nachrichten abrufen
		function decodeRand($str, $seed)
		{
			mt_srand($seed);
			$blocks = explode('-', $str);
			$out = array();
			foreach ($blocks as $block)
			{
				$ord = (intval($block) - mt_rand(350, 16000)) / 3;
				$out[] = chr($ord);
			}
			mt_srand();
			return implode('', $out);
		}
		
		$get_messages = "SELECT `from_id`,`to_id`,`content`,`datum` FROM `messages` WHERE `to_id` = '".$userid."' OR `from_id` = '".$userid."'";
		$result = mysqli_query($sql, $get_messages);
		
		$i = 0;
		$chats = array();
		while($row = mysqli_fetch_assoc($result))
		{	
			if($row['from_id'] == $userid)
			{
				$seed = $userid + 284917;
				$content = decodeRand($row['content'], $seed);
			}
			elseif($row['to_id'] == $userid)
			{
				$seed = $row['from_id'] + 284917;
				$content = decodeRand($row['content'], $seed);
			}
			else
			{
				$content = "<div class='alert alert-error'>Es ist ein Fehler bei der Entschlüsselung der Nachricht aufgetreten. Bitte <a href'contact.php'>kontaktiere</a> den Administrator";
			}
			
			$chats[$i]['from_id'] = $row['from_id'];
			$chats[$i]['to_id'] = $row['to_id'];
			$chats[$i]['content'] = $content;
			$chats[$i]['datum'] = $row['datum'];
			$i++;
		}
		
		$sorted_chat = array();
		foreach($chats as $current)
		{
			$id = $current['from_id'] == $userid ? $current['to_id'] : $current['from_id'];
			if(array_key_exists($id . ".", $sorted_chat))
			{
				array_push($sorted_chat[$id . "."], $current);
			}
			else
			{
				$sorted_chat[$id . "."] = array($current);
			}
		}
		$sorted_chat = array_reverse($sorted_chat, TRUE);
		
		$get_users = "SELECT `name`, `userid` FROM `users`";
		$users = mysqli_query($sql, $get_users);
		
		$row;
		$user_array = array();
		while($row = mysqli_fetch_assoc($users))
		{
			$user_array[$row['userid']] = $row['name'];
		}
		
		foreach($sorted_chat as $message)
		{
			$fid =  array_keys($sorted_chat, $message)[0];
			$fname = str_replace(".", "", $fid);
			$fname = $user_array[$fname];
			$is_online = "SELECT `status` FROM `users` WHERE `name` = '".$fname."'";
			$online = mysqli_query($sql, $is_online);
			$online = mysqli_fetch_row($online);
			if($online[0] == 1)
			{
				echo "<div class='chat_all'><div class='chat_header'>";
				//Name des Chatpartners
				echo "<div class='status_on'><br />" . $fname . "<br /><br /></div>";
				echo "</div><div class='chat_content'><br />";
				//Chatcontent
				$message = array_reverse($message, TRUE);
				foreach($message as $cm)
				{
					$name = $cm['from_id'];
					$name = $user_array[$name];
					echo "&nbsp;&nbsp;&nbsp;<b>" . $name . "</b> (" . $cm['datum'] . " ): &nbsp;&nbsp;" . $cm['content'] . "<br /><br />";
				}
				echo "</div></div>";
				//Input Feld
				echo "
					<form name='answer_form' action='chat.php' method='post' class='answer_form'>
						<input type='text' name='message' size='40' maxlength='999' />
						<input type='hidden' name='receiver' value='".$fname."' />
					</form>
				";
				echo "<br /><br /><br /><br />";
			}
		}
		foreach($sorted_chat as $message)
		{
			$fid =  array_keys($sorted_chat, $message)[0];
			$fname = str_replace(".", "", $fid);
			$fname = $user_array[$fname];
			$is_online = "SELECT `status` FROM `users` WHERE `name` = '".$fname."'";
			$online = mysqli_query($sql, $is_online);
			$online = mysqli_fetch_row($online);
			if($online[0] == 0)
			{
				echo "<div class='chat_all'><div class='chat_header'>";
				//Name des Chatpartners
				echo "<div class='status_off'><br />" . $fname . "<br /><br /></div>";
				echo "</div><div class='chat_content'><br />";
				//Chatcontent
				$message = array_reverse($message, TRUE);
				foreach($message as $cm)
				{
					$name = $cm['from_id'];
					$name = $user_array[$name];
					echo "&nbsp;&nbsp;&nbsp;<b>" . $name . "</b> (" . $cm['datum'] . " ): &nbsp;&nbsp;" . $cm['content'] . "<br /><br />";
				}
				echo "</div></div>";
				//Input Feld
				echo "
					<form name='answer_form' action='chat.php' method='post' class='answer_form'>
						<input type='text' name='message' size='40' maxlength='999' />
						<input type='hidden' name='receiver' value='".$fname."' />
					</form>
				";
				echo "<br /><br /><br /><br />";
			}
		}
?>
		</div>
	<div id="friends">
<?php
		$select_friends = "SELECT `friendid` FROM `friends` WHERE `userid` = '".$userid."' AND `confirmed` = 1";
		$friends = mysqli_query($sql, $select_friends);
		for($j = 0; $array[$j] = mysqli_fetch_assoc($friends); $j++);
		array_pop($array);
		if($array == array())
		{
			echo "<br /><br />&nbsp;&nbsp;&nbsp;Du hast noch keine Kontakte.";
		}
		
		$fname_array = array();
		foreach($array as $current_friendid)
		{
			$get_user = "SELECT `name`, `status` FROM `users` WHERE `userid` = '".$current_friendid['friendid']."'";
			$current_friend = mysqli_query($sql, $get_user);
			$current_friend = mysqli_fetch_array($current_friend);
			array_push($fname_array, $current_friend);
		}
		foreach($fname_array as $cf)
		{
			if($cf[1] == 1)
			{
				echo "<br /><div class='status_on round_corners'><br />&nbsp;&nbsp;&nbsp;".$cf[0]."<br /><br /></div>";
			}
		}
		foreach($fname_array as $cf)
		{
			if($cf[1] == 0)
			{
				echo "<br /><div class='status_off round_corners'><br />&nbsp;&nbsp;&nbsp;".$cf[0]."<br /><br /></div>";
			}
		}
?>
	</div>
<?php
		mysqli_close($sql);
	}
	else
	{
		header("index.php");
	}
?>