<?php
	session_start();
	if(isset($_POST['email']) or isset($_POST['password']))
	{
		//Filtern
		$email = trim($_POST['email']);
		$email = strip_tags($email);
		$password = strip_tags($_POST['password']);
		
		require 'db.php';
		
		$email = mysql_real_escape_string($email);
		$password = mysql_real_escape_string($password);
		
		// Erzeugung von Passwort-Hash mit Salt
			//db = database und dc = document
		function saltPassword($pe)
		{
			 return hash('sha512', $pe);
		}
		
		$pe = $password . $email;
		$dc_saltedHash = saltPassword($pe);
		
		// Prüfen
		$load = "SELECT `password` FROM `users` WHERE `email` = '".$email."'";
		$db_saltedHash = mysql_query($load);
		$db_saltedHash = mysql_fetch_row($db_saltedHash);
		$is_active = "SELECT `active` FROM `users` WHERE `email` = '".$email."'";
		$activated = mysql_query($is_active);
		$activated = mysql_fetch_row($activated);
		
		//Eventuellen Fehler definieren
		if ($activated[0] == 0)
		{
			$error = "Dein Account wurde nocht nicht bestätigt.";
		}
		elseif ($db_saltedHash[0] != $dc_saltedHash)
		{
			$error = "Login nicht erfolgreich. Du hast entweder eine falsche E-Mail oder ein falsches Passwort eingegeben.";
		}
		elseif($activated[0] != 0 or $activated[0] != 1)
		{
			$error = "Es gibt keinen Account mit diesen Daten.";
		}
		else
		{
			$error = "Ein unbekannter Fehler ist aufgetreten. Wenn du Cookies trotzdem aktiviert hast, <a href='contact.php'>kontaktiere</a> bitte den Administrator.";
		}
		
		if($db_saltedHash[0] == $dc_saltedHash && $activated[0] == 1)
		{	
			//Einloggen
			$get_userid = "SELECT `userid` FROM `users` WHERE `email` = '".$email."'";
			$userid = mysql_query($get_userid);
			$userid = mysql_fetch_row($userid);
			
			$_SESSION["userid"] = $userid;
			header("Location: index.php");
		}
		else
		{
			//Fehlerausgabe
			require_once 'lib/Twig/Autoloader.php';
			Twig_Autoloader::register();
			$loader = new Twig_Loader_Filesystem('./');
			$twig = new Twig_Environment($loader, array());
			$template = $twig->loadTemplate('login.html');
			$params = array(
				"if_failed" => "<div class='alert alert-error'>".$error."</div>",
				"register" => '',
				"email" => $email,
				"password" => '',
				"Name" => '',
				"Mail" => '',
				"Passwort_1" => '',
				"Passwort_2" => ''
			);
			$template->display($params);
		}
		mysql_close($sql);
	}
	else
	{
		//Seite zum ersten Mal aufgerufen
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('login.html');
		$params = array(
			"if_failed" => '',
			"register" => '',
			"email" => '',
			"password" => '',
			"Name" => '',
			"Mail" => '',
			"Passwort_1" => '',
			"Passwort_2" => ''
		);
		$template->display($params);
	}
?>