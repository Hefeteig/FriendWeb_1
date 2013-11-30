<?php
	session_start();
	$userid = $_SESSION['userid'][0];
	require 'db.php';
	
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
	$result = mysql_query($get_messages);
	
	$i = 0;
	$chats = array();
	while($row = mysql_fetch_assoc($result))
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
	$users = mysql_query($get_users);
	
	$row;
	$user_array = array();
	while($row = mysql_fetch_assoc($users))
	{
		$user_array[$row['userid']] = $row['name'];
	}
	
	foreach($sorted_chat as $message)
	{
		$var = array_keys($sorted_chat, $message);
		$fid =  $var[0];
		$fname = str_replace(".", "", $fid);
		$fname = $user_array[$fname];
		
		$get_time = "SELECT `last_update` FROM `users` WHERE `name` = '".$fname."'";
		$time = mysql_query($get_time);
		$time = mysql_fetch_row($time);
		$time = $time[0];
		
		$is_online = "SELECT TIMEDIFF(NOW(), '".$time."') FROM `users` WHERE `name` = '".$fname."'";
		$online = mysql_query($is_online);
		$online = mysql_fetch_row($online);
		$online = explode(':', $online[0]);
		
		if($online[0] != '00' or $online[1] != '00')
		{
			$online[2] = 40;
		}
		$online = $online[2];
		
		if($online <= 25)
		{
			echo "<div class='chat_all'><div class='chat_header'>";
			//Name des Chatpartners
			echo "<div class='status_on'><br />" . $fname . "<br /><br /></div>";
			echo "</div><br />";
			//Chatcontent
			$message = array_reverse($message, TRUE);
			foreach($message as $cm)
			{
				$name = $cm['from_id'];
				$name = $user_array[$name];
				echo "&nbsp;&nbsp;&nbsp;<b>" . $name . "</b> (" . $cm['datum'] . "): &nbsp;&nbsp;" . $cm['content'] . "<br /><br />";
			}
			echo "</div>";
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
		$var = array_keys($sorted_chat, $message);
		$fid =  $var[0];
		$fname = str_replace(".", "", $fid);
		$fname = $user_array[$fname];
		
		$get_time = "SELECT `last_update` FROM `users` WHERE `name` = '".$fname."'";
		$time = mysql_query($get_time);
		$time = mysql_fetch_row($time);
		$time = $time[0];
		
		$is_online = "SELECT TIMEDIFF(NOW(), '".$time."') FROM `users` WHERE `name` = '".$fname."'";
		$online = mysql_query($is_online);
		$online = mysql_fetch_row($online);
		$online = explode(':', $online[0]);
		
		if($online[0] != '00' or $online[1] != '00')
		{
			$online[2] = 40;
		}
		$online = $online[2];
		
		if($online >= 25)
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
	mysql_close($sql);
?>