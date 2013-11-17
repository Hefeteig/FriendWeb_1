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
			echo "<div class='chat_all'><div class='chat_header'>";
			//Name des Chatpartners
			$fid =  array_keys($sorted_chat, $message)[0];
			$fname = str_replace(".", "", $fid);
			$fname = $user_array[$fname];
			echo "<br />" . $fname . "<br /><br />";
			echo "</div><div class='chat_content'><br />";
			//Chatcontent
			foreach($message as $cm)
			{
				$name = $cm['from_id'];
				$name = $user_array[$name];
				echo "&nbsp;&nbsp;&nbsp;<b>" . $name . "</b> (" . $cm['datum'] . " ): &nbsp;&nbsp;" . $cm['content'] . "<br /><br />";
			}
			echo "</div>";
			//Input Feld
			echo "";
			echo "</div><br /><br /><br /><br />";
		}
?>
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
		mysqli_close($sql);
	}
	else
	{
		header("index.php");
	}
?>