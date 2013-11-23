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
		
		//Falls Name, E-Mail oder Passwort geändert
		if($_POST['change_name'] != '')
		{
			//Name
			//Filtern
			$new_name = trim($_POST['change_name']);
			$new_name = strip_tags($new_name);
			$new_name = mysqli_real_escape_string($sql, $new_name);
			
			//Prüfen ob Nutzername vergeben
			$user_query = "SELECT `name` FROM `users` WHERE `name` = '".$new_name."'";
			$user_exist = mysqli_query($sql, $user_query);
			$user_exist = mysqli_fetch_row($user_exist);
			
			//Alten Nutzernamen bestimmen
			$old_name_query = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
			$old_name = mysqli_query($sql, $old_name_query);
			$old_name = mysqli_fetch_row($old_name);
			$old_name = $old_name[0];
			
			//Fehler definieren
			$error_2 = '';
			$error_3 = '';
			if($new_name == $old_name)
			{
				$error_1 = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Du heißt bereits &quot;".$new_name."&quot;</div>";
			}
			elseif($user_exist)
			{
				$error_1 = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Es gibt schon einen Nutzer mit dem Namen &quot;".$new_name."&quot;</div>";
			}
			elseif(empty($_POST['change_name']))
			{
				$error_1 = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Du musst einen neuen Namen eintragen um deinen alten zu ändern.</div>";
			}
			elseif(preg_match("(to:|cc:|bcc:|from:|subject:|reply-to:|content-type:|MIME-Version:|multipart/mixed|Content-Transfer-Encoding:)ims", $new_name))
			{
				$error_1 = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Dein neuer Name enhält fehlerhafte Ausdrücke.</div>";
			}
			else
			{
				$cn = "UPDATE `users` SET `name` = '".$new_name."' WHERE `name` = '".$old_name."'";
				mysqli_query($sql, $cn);
				$error_1 = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>Du heißt ab jetzt &quot;".$new_name."&quot;.</div>";
			}
		}
		elseif($_POST['change_email'] != '')
		{
			//E-Mail
			echo "email geändert";
			$error_1 = '';
			$error_2 = '';
			$error_3 = '';
		}
		elseif($_POST['change_pw'] != '')
		{
			//Passwort
			//Filtern
			$new_pw = strip_tags($_POST['change_pw']);
			$new_pw = mysqli_real_escape_string($sql, $new_pw);
			
			//Hashen und prüfen
			
			$get_email = "SELECT `email` FROM `users` WHERE `userid` = '".$userid."'";
			$email = mysqli_query($sql, $get_email);
			$email = mysqli_fetch_row($email);
			$email = $email[0];
			
			function saltPassword($pe)
			{
				 return hash('sha512', $pe);
			}
			
			$pe = $new_pw . $email;
			$dc_saltedHash = saltPassword($pe);
			
			$load = "SELECT `password` FROM `users` WHERE `email` = '".$email."'";
			$db_saltedHash = mysqli_query($sql, $load);
			$db_saltedHash = mysqli_fetch_row($db_saltedHash);
			$db_saltedHash = $db_saltedHash[0];
			
			//Fehler definieren
			$error_1 = '';
			$error_2 = '';
			if($dc_saltedHash == $db_saltedHash)
			{
				$error_3 = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Wenn du dein Passwort ändern willst, musst du schon ein anderes als dein altes eingeben.</div>";
			}
			else
			{
				$update_pw = "UPDATE `users` SET `password` = '".$dc_saltedHash."' WHERE `userid` = '".$userid."'";
				mysqli_query($sql, $update_pw);
				$error_3 = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>Dein Passwort wurde erfolgreich geändert.</div>";
			}
		}
		else
		{
			$error_1 = '';
			$error_2 = '';
			$error_3 = '';
		}
		
		//Profildaten auslesen
		$get_name = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
		$get_email = "SELECT `email` FROM `users` WHERE `userid` = '".$userid."'";
		$get_register_date = "SELECT `register_date` FROM `users` WHERE `userid` = '".$userid."'";
		$name = mysqli_query($sql, $get_name);
		$email = mysqli_query($sql, $get_email);
		$register_date = mysqli_query($sql, $get_register_date);
		$name = mysqli_fetch_row($name);
		$email = mysqli_fetch_row($email);
		$register_date = mysqli_fetch_row($register_date);
?>
	<div id="protokoll">
		<br /><br /><br /><br />
		<div class="site_title">Profileinstellungen</div><br /><br /><br /><br />
		<div class="login_causes">
			<form name="change_data" action="settings_profile.php" method="post">
				Name: <?php echo $name[0];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="change_name" size="15" maxlength="50">&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary" ><i class="icon-pencil"></i>&nbsp;&nbsp;Ändern</button><br /><br /><?php echo $error_1; ?><br /><br /><br />
				E-Mail: <?php echo $email[0];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="change_email" size="15" maxlength="50">&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary" ><i class="icon-pencil"></i>&nbsp;&nbsp;Ändern</button><br /><br /><?php echo $error_2; ?><br /><br /><br />
				Registrierungsdatum: <?php echo $register_date[0];?><br /><br /><br /><br /><br />
				<input type="text" name="change_pw" size="15" maxlength="50">&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary" ><i class="icon-pencil"></i>&nbsp;&nbsp;Passwort Ändern</button><br /><br /><?php echo $error_3; ?>
			</form>
		</div>
	</div>
	
	<div id="friends" onload="friendrequest('friends.php')">
<?php
	//require 'friends.php';
	//mysqli_close($sql);
?>
	</div>
<?php
		mysqli_close($sql);
	}
	else
	{
		header("Location: index.php");
	}
?>