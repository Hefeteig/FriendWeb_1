<?php
	session_start();
	$userid = $_SESSION['userid'][0];
	$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($sql, "friendweb");
	
	require_once 'lib/Twig/Autoloader.php';
	Twig_Autoloader::register();
	$loader = new Twig_Loader_Filesystem('./');
	$twig = new Twig_Environment($loader, array());
	$template = $twig->loadTemplate('main.html');
	$params = array();
	$template->display($params);
	
	if(isset($_SESSION["userid"]))
	{
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
		<div class="site_title">Kontakt hinzufügen</div><br /><br /><br /><br />
		
			<div class="center">
				<form name="search" action="addfriend.php" method="post">
					<input type="text" size="40" maxlength="50" name="searched_friend">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="submit" class="btn btn-large btn-primary">Suchen</button>
				</form>
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
?>
	</div>
<?php
		if(isset($_POST['searched_friend']) || isset($_POST['friends_name']))
		{
			if(isset($_POST['searched_friend']))
			{
				//Gesuchten Namen filtern
				$sf = trim($_POST['searched_friend']);
				$sf = strip_tags($sf);
				$sf = mysqli_real_escape_string($sql, $sf);
				
				//Nach Namen suchen
				$search = "SELECT `name` FROM `users` WHERE `name` = '".$sf."'";
				$result = mysqli_query($sql, $search);
				$result = mysqli_fetch_row($result);
				
				if($result)
				{
					echo "<div class='result_heading'>Ergebnis:</div><br /><br />";
					$i = 0;
					while($i < count($result))
					{
					   echo "<div class='search_result'><form name='addfriend' action='addfriend.php' method='post'><input type='text' name='friends_name' class='hidden' value='".$result[$i]."'>" .$result[$i]. "&nbsp;&nbsp;&nbsp;<button type='submit' class='btn btn-primary'><i class='icon-plus'></i> Kontaktanfrage senden</button></form></div><br /><br />";
					   $i++;
					}
				}
				else
				{
					echo "<div class='result_heading'>Es wurde kein Nutzer mit dem Namen &quot;".$sf."&quot; gefunden.</div>";
				}
			}
			//Kontaktanfrage senden
			if(isset($_POST['friends_name']))
			{
				$name = $_POST['friends_name'];
				$friends_id = "SELECT `userid` FROM `users` WHERE `name` = '".$name."'";
				$fid = mysqli_query($sql, $friends_id);
				$fid = mysqli_fetch_row($fid);
				
				//Anfrage schon gesendet und angenommen?
				$friend_query_1 = "SELECT `userid` FROM `friends` WHERE `friendid` = '".$fid[0]."' AND `userid` = '".$userid."' AND `confirmed` = 1";
				$already_friends_1 = mysqli_query($sql, $friend_query_1);
				$already_friends_1 = mysqli_fetch_row($already_friends_1);
				
				//Anfrage schon gesendet und nicht angenommen?
				$friend_query_2 = "SELECT `userid` FROM `friends` WHERE `friendid` = '".$fid[0]."' AND `userid` = '".$userid."' AND `confirmed` = 0";
				$already_friends_2 = mysqli_query($sql, $friend_query_2);
				$already_friends_2 = mysqli_fetch_row($already_friends_2);
				
				//Zu viele Freunde
				
				if($fid[0] == $userid)
				{
					//Freundschaft mit sich selber
					echo "<div class='alert alert-block alert_message'>Bist du schon so verzweifelt, dass du mit dir selber befreundet sein willst?</div>";
				}
				elseif($already_friends_1)
				{
					//Anfrage schon gesendet und angenommen
					echo "<div class='alert alert-block alert_message'>Du bist mit diesem Nutzer schon befreundet.</div>";
				}
				elseif($already_friends_2)
				{
					//Anfrage schon gesendet aber nicht angenommen
					echo "<div class='alert alert-block alert_message'>Du hast diesem Nutzer bereits eine Kontaktanfrage gesendet, die aber noch nicht bestätigt wurde. Lässt du dir das gefallen?</div>";
				}
				/*elseif($too_many_friends)
				{
					//Anfrage schon gesendet aber nicht angenommen
					echo "<div class='alert alert-block alert_message'>Du hast schon 500 Kontakte, mehr geht leider nicht.</div>";
				}*/
				else
				{
					//Anfrage senden
					$insert_friend_1 = "INSERT INTO `friends` (`userid`, `friendid`, `confirmed`) VALUES ('".$fid[0]."', '".$userid."', 0)";
					$insert_friend_2 = "INSERT INTO `friends` (`userid`, `friendid`, `confirmed`) VALUES ('".$userid."', '".$fid[0]."', 0)";
					mysqli_query($sql, $insert_friend_1);
					mysqli_query($sql, $insert_friend_2);
					echo "<div class='alert alert-success alert_message'>Kontaktanfrage erfolgreich versendet.</div>";
				}
			}
		}
	}
	else
	{
		header("Location: index.php");
	}
?>