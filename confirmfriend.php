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
		<div class="site_title">Kontaktanfragen verwalten</div><br /><br /><br /><br />
<?php
		if(isset($_POST['allow']))
		{
			$friends_name = $_POST['confirmfriends_name'];
			//Filterung da das versteckte input-feld mit firebug manipuliert werden kann (Im Falle eines ungültigen Eintrages wird in die Tabelle `friends` als `friendid` "0" (und auch als `userid`)eingetragen)
			$friends_name = trim($friends_name);
			$friends_name = strip_tags($friends_name);
			$friends_name = mysql_real_escape_string($friends_name);
			
			$friends_id = "SELECT `userid` FROM `users` WHERE `name` = '".$friends_name."'";
			$fid = mysql_query($friends_id);
			$fid = mysql_fetch_row($fid);
			
			//Zu viele Freunde?
			$count_friends = "SELECT `userid` FROM `friends` WHERE `userid` = '".$userid."' AND `confirmed` = 1";
			$counted_friends = mysql_query($count_friends);
			$array = array();
			for($j = 0; $array[$j] = mysql_fetch_assoc($counted_friends); $j++);
			array_pop($array);
			$number = count($array);
			
			if($number >= 500)
			{
				//Zu viele Freunde
				echo "<div class='alert alert-block alert_message'>Du hast schon 500 Kontakte, mehr geht leider nicht.</div>";
			}
			elseif($fid[0] == 0)
			{
				//Manipuliertes input-feld
				echo "<div class='alert alert-block alert_message'>Es gibt keinen Nutzer der &quot;".$friends_name."&quot; heißt. Wenn du die Formulare nicht manipuliert hast, <a href='contact.php'>kontaktiere</a> bitte den Administrator.</div>";
			}
			else
			{
				//Anfrage bestätigen
				$confirm_friend_1 = "UPDATE `friends` SET `confirmed` = 1 WHERE `userid` = '".$userid."' AND `friendid` = '".$fid[0]."'";
				$confirm_friend_2 = "UPDATE `friends` SET `confirmed` = 1 WHERE `userid` = '".$fid[0]."' AND `friendid` = '".$userid."'";
				mysql_query($confirm_friend_1);
				mysql_query($confirm_friend_2);
				echo "<div class='alert alert-success alert_message'>Kontaktanfrage bestätigt.</div>";
			}
		}
		elseif(isset($_POST['delete']))
		{
			$friends_name = $_POST['confirmfriends_name'];
			//Filterung da das versteckte input-feld mit firebug manipuliert werden kann (Im Falle eines ungültigen Eintrages wird in die Tabelle `friends` als `friendid` "0" (und auch als `userid`)eingetragen)
			$friends_name = trim($friends_name);
			$friends_name = strip_tags($friends_name);
			$friends_name = mysql_real_escape_string($friends_name);
			
			$friends_id = "SELECT `userid` FROM `users` WHERE `name` = '".$friends_name."'";
			$fid = mysql_query($friends_id);
			$fid = mysql_fetch_row($fid);
			
			if($fid[0] == 0)
			{
				//Manipuliertes input-feld
				echo "<div class='alert alert-block alert_message'>Es gibt keinen Nutzer der &quot;".$friends_name."&quot; heißt. Wenn du die Formulare nicht manipuliert hast, <a href='contact.php'>kontaktiere</a> bitte den Administrator.</div>";
			}
			else
			{
				//Anfrage bestätigen
				$confirm_friend_1 = "DELETE FROM `friends` WHERE `userid` = '".$userid."' AND `friendid` = '".$fid[0]."'";
				$confirm_friend_2 = "DELETE FROM `friends` WHERE `userid` = '".$fid[0]."' AND `friendid` = '".$userid."'";
				mysql_query($confirm_friend_1);
				mysql_query($confirm_friend_2);
				echo "<div class='alert alert-success alert_message'>Kontaktanfrage abgelehnt.</div>";
			}
		}
		
		$array = array();
		$check_requests = "SELECT `friendid` FROM `friends` WHERE `userid` = '".$userid."' AND `confirmed` = 0 AND `durch` != '".$userid."'";
		$requests = mysql_query($check_requests);
		for($j = 0; $array[$j] = mysql_fetch_assoc($requests); $j++);
		array_pop($array);
		
		foreach($array as $current_friendid)
		{
			$get_user = "SELECT `name` FROM `users` WHERE `userid` = '".$current_friendid['friendid']."'";
			$current_friend = mysql_query($get_user);
			$current_friend = mysql_fetch_row($current_friend);
			if($current_friend)
			{
				echo "<br /><div class='offene_anfragen'><br />Offene Anfragen:<br /><br /></div>";
				$i = 0;
				while($i < count($current_friend))
				{
					echo "<br />
							<div class='search_result'>
								<form name='confirmfriend' action='confirmfriend.php' method='post'>
									<input type='hidden' name='confirmfriends_name' value='".$current_friend[$i]."'></input>" .$current_friend[$i]. "&nbsp;&nbsp;&nbsp;
									<button type='submit' class='btn btn-primary' name='allow'><i class='icon-plus'></i> Bestätigen</button>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<button type='submit' class='btn btn-primary' name='delete'><i class='icon-remove'></i> Ablehnen</button>
								</form>
							</div>
						<br /><br />";
					$i++;
				}
			}
		}
		if (!$array)
		{
			echo "<br /><div class='offene_anfragen'><br />Du hast keine unbestätigten Kontaktanfragen.<br /><br /></div>";
		}
?>
	</div>
	<div id="friends">
	</div>
<?php
		mysql_close($sql);
	}
	else
	{
		header("Location: index.php");
	}
?>