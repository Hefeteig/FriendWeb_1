<?php
	//Filtern
	$email = trim($_POST['Mail']);
	$user = trim($_POST['Name']);
	$email = strip_tags($email);
	$user = strip_tags($user);
	$password = strip_tags($_POST['Passwort_1']);
	$sql = mysqli_connect("localhost", "root", "XAMPPpassword");
	mysqli_select_db($sql, "friendweb");
	
	$email = mysqli_real_escape_string($sql, $email);
	$user = mysqli_real_escape_string($sql, $user);
	$password = mysqli_real_escape_string($sql, $password);
	
	//Nutzer vorhanden?
	$user_query = "SELECT `name` FROM `users` WHERE `name` = '".$user."'";
	$email_query = "SELECT `email` FROM `users` WHERE `email` = '".$email."'";
	$user_exist = mysqli_query($sql, $user_query);
	$user_exist = mysqli_fetch_row($user_exist);
	$email_exist = mysqli_query($sql, $email_query);
	$email_exist = mysqli_fetch_row($email_exist);
	$asdf = '';
	if (empty ($_POST['Mail']) == 1 or empty ($_POST['Name']) == 1 or empty ($_POST['Passwort_1']) == 1 or empty ($_POST['Passwort_2']) == 1 or $_POST['Passwort_1'] != $_POST['Passwort_2'] or filter_var($_POST['Mail'], FILTER_VALIDATE_EMAIL) == FALSE
		or preg_match("(to:|cc:|bcc:|from:|subject:|reply-to:|content-type:|MIME-Version:|multipart/mixed|Content-Transfer-Encoding:)ims", $_POST['Name'] . $_POST['Mail'] . $_POST['Passwort_1'] . $_POST['Passwort_2'])
		or preg_match("((drop)|(delete)|(table)|(--)|(;))", $_POST['Name'] . $_POST['Mail']. $_POST['Passwort_1'] . $_POST['Passwort_2'])
		or $user_exist[0] or $email_exist[0])
	{
		//Fehler definieren
		if(empty ($_POST['Mail']) == 1 or empty ($_POST['Name']) == 1 or empty ($_POST['Passwort_1']) == 1 or empty ($_POST['Passwort_2']) == 1){$error = "Bitte f&uuml;lle alle Felder aus";}
		elseif ($_POST['Passwort_1'] != $_POST['Passwort_2']){$error = "Passw&ouml;rter stimmen nicht überein";}
		elseif(filter_var($_POST['Mail'], FILTER_VALIDATE_EMAIL) == FALSE){$error = "Keine g&uuml;ltige E-Mail";}
		elseif(preg_match("(to:|cc:|bcc:|from:|subject:|reply-to:|content-type:|MIME-Version:|multipart/mixed|Content-Transfer-Encoding:)ims", $_POST['Name'] . $_POST['Mail'] . $_POST['Passwort_1'] . $_POST['Passwort_2'])){$error = "Fehlerhafte Ausdrücke eingegeben";}
		elseif($user_exist[0]){$error = "Nutzername schon vergeben";}
		elseif($email_exist[0]){$error = "E-Mail existiert bereits";}
		elseif(preg_match("((drop)|(delete)|(table)|(--)|(;))", $_POST['Name'] . $_POST['Mail']. $_POST['Passwort_1'] . $_POST['Passwort_2'])){$error = "<img src='pics/injection.png' alt='injection.png'<br /><br /><b>Inject the whole SQL-query...</b>";}
		else{$error = "Ein unbekannter Fehler ist aufgetreten, bitte <a href='contact.php'>kontaktiere</a> den Administrator.";}
		
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('login.html');
		$params = array(
			"name" => $_POST['Name'],
			"mail" => $_POST['Mail'],
			"register" => "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>" . $error . "</div>"
		);
		$template->display($params);
	}
	else
	{
		//E-Mail vorbereiten und senden
		$rand_number = mt_rand(1, 999999999);
		$absender = "FriendWeb <friend@web.de>";
		$headers   = array();
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=iso-8859-1";
		$headers[] = "From: {$absender}";
		
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$params = array(
			"name" => $user,
			"mail" => $email,
		);
		$content = $twig->render("email.phtml", $params);

		mail ($email, "Registrierung bei FriendWeb", $content, implode("\r\n",$headers));
		
		//Passwort-Hash mit Salt erstellen und in DB eintragen
		function saltPassword($password, $salt)
		{
			 return hash('sha512', $salt.$password);
		}
		
		$salt = $email;
		$saltedHash = saltPassword($password, $salt);
		$insert = "INSERT INTO `users` (`name`, `email`, `password`) VALUES ('".$user."', '".$email."', '".$saltedHash."')";
		mysqli_query($sql, $insert);
		
		//Datei um Registrierung abzuschließen erstellen
		$handle = fopen('registername='.$user.'&mail='.$email.$rand_number.'.php', "w+");
		$daten = '
				<?php
				$sql_open = mysqli_connect("localhost", "root", "XAMPPpassword");
				mysqli_select_db($sql_open, "friendweb");
				$query = "UPDATE `users` SET `active` = 1 WHERE `email` = '."'".$_GET['email']."'".'";
				mysqli_query($sql_open, $query);
				mysqli_close($sql_open);
				
				require_once "lib/Twig/Autoloader.php";
				Twig_Autoloader::register();
				$loader = new Twig_Loader_Filesystem("./");
				$twig = new Twig_Environment($loader, array());
				$template = $twig->loadTemplate("login.html");
				$params = array(
					"if_failed" => "<div class='."alert alert-info".'>Dein Account wurde erfolgreich best&auml;tigt.</div>",
					"register" => "",
					"email" => "",
					"password" => "",
					"Name" => "",
					"Mail" => "",
					"Passwort_1" => "",
					"Passwort_2" => ""
				);
				$template->display($params);
				?>
				';
		fwrite($handle, $daten);
		fclose($handle);
		
		//Erfolgsmeldung
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('login.html');
		$params = array(
			"if_failed" => '',
			"register" => "<div class='alert alert-success'>Das Registrierungsformular wurde erfolgreich an deine E-Mail-Adresse versendet, bitte schau auch im Spamordner nach.<br />Wenn die Registrierung nicht innerhalb von 24 Stunden bestätigt wurde, wird der Link und die Registrierungsdaten gelöscht.</div>",
			"email" => '',
			"password" => '',
			"Name" => '',
			"Mail" => '',
			"Passwort_1" => '',
			"Passwort_2" => ''
		);
		$template->display($params);
	}
	mysqli_close($sql);
?>