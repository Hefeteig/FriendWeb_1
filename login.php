<?php
	session_start();
	if(isset($_POST['email']) or isset($_POST['password']))
	{
		//Filtern
		$email = trim($_POST['email']);
		$email = strip_tags($email);
		$password = strip_tags($_POST['password']);
		$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
		mysqli_select_db($sql, "friendweb");
		
		$email = mysqli_real_escape_string($sql, $email);
		$password = mysqli_real_escape_string($sql, $password);
		
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
		$db_saltedHash = mysqli_query($sql, $load);
		$db_saltedHash = mysqli_fetch_row($db_saltedHash);
		$is_active = "SELECT `active` FROM `users` WHERE `email` = '".$email."'";
		$activated = mysqli_query($sql, $is_active);
		$activated = mysqli_fetch_row($activated);
		
		/*
		echo $db_saltedHash[0] . "<br />";
		echo $dc_saltedHash . "<br />";
		$test = saltPassword('', $salt);
		echo $test . "<br />";
		*/
		
		//Eventuellen Fehler definieren
		if ($db_saltedHash[0] != $dc_saltedHash)
		{
			$error = "Login nicht erfolgreich. Du hast entweder einen falschen Benutzernamen oder ein falsches Passwort eingegeben. <a href='forgot.php'>Passwort vergessen?</a><br/>";
		}
		elseif ($activated[0] != 1)
		{
			$error = "Dein Account wurde nocht nicht bestätigt.";
		}
		else
		{
			$error = "Ein unbekannter Fehler ist aufgetreten, bitte <a href='contact.php'>kontaktiere</a> den Administrator.";
		}
		
		if($db_saltedHash[0] == $dc_saltedHash && $activated[0] == 1)
		{	
			//Einloggen
			$get_userid = "SELECT `userid` FROM `users` WHERE `email` = '".$email."'";
			$userid = mysqli_query($sql, $get_userid);
			$userid = mysqli_fetch_row($userid);
			
			$_SESSION["userid"] = $userid;
			session_cache_limiter(240);
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
		mysqli_close($sql);
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