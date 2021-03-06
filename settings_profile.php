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
		
		//Profildaten auslesen
		$get_name = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
		$get_email = "SELECT `email` FROM `users` WHERE `userid` = '".$userid."'";
		$name = mysql_query($get_name);
		$email = mysql_query($get_email);
		$name = mysql_fetch_row($name);
		$email = mysql_fetch_row($email);
		
		//Falls Name, E-Mail oder Passwort geändert
		if($_POST['change_name'] != '')
		{
			//Name
			//Filtern
			$new_name = trim($_POST['change_name']);
			$new_name = strip_tags($new_name);
			$new_name = mysql_real_escape_string($new_name);
			
			//Prüfen ob Nutzername vergeben
			$user_query = "SELECT `name` FROM `users` WHERE `name` = '".$new_name."'";
			$user_exist = mysql_query($user_query);
			$user_exist = mysql_fetch_row($user_exist);
			
			//Alten Nutzernamen bestimmen
			$old_name_query = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
			$old_name = mysql_query($old_name_query);
			$old_name = mysql_fetch_row($old_name);
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
			elseif(empty($new_name))
			{
				$error_1 = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>&times;</button>Du hast für deinen neuen Namen Elemente (z.B. aus HTML) verwendet, die rausgefiltert wurden
							und dadurch würde dein neuer Name keine Zeichen enthalten. Versuche es bitte erneut mit anderen Zeichen.</div>";
			}
			else
			{
				$cn = "UPDATE `users` SET `name` = '".$new_name."' WHERE `name` = '".$old_name."'";
				mysql_query($cn);
				$error_1 = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>Du heißt ab jetzt &quot;".$new_name."&quot;.</div>";
				$changed = true;
			}
		}
		/*elseif($_POST['change_email'] != '')
		{
			//E-Mail
			echo "email geändert";
			$error_1 = '';
			$error_2 = '';
			$error_3 = '';
		}*/
		elseif($_POST['change_pw'] != '')
		{
			//Passwort
			//Filtern
			$new_pw = strip_tags($_POST['change_pw']);
			$new_pw = mysql_real_escape_string($new_pw);
			
			//Hashen und prüfen
			
			$get_email = "SELECT `email` FROM `users` WHERE `userid` = '".$userid."'";
			$email = mysql_query($get_email);
			$email = mysql_fetch_row($email);
			$email = $email[0];
			
			function saltPassword($pe)
			{
				 return hash('sha512', $pe);
			}
			
			$pe = $new_pw . $email;
			$dc_saltedHash = saltPassword($pe);
			
			$load = "SELECT `password` FROM `users` WHERE `email` = '".$email."'";
			$db_saltedHash = mysql_query($load);
			$db_saltedHash = mysql_fetch_row($db_saltedHash);
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
				mysql_query($update_pw);
				$error_3 = "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button>Dein Passwort wurde erfolgreich geändert.</div>";
			}
		}
		else
		{
			$error_1 = '';
			$error_2 = '';
			$error_3 = '';
		}

?>
	<div id="protokoll">
		<br /><br /><br /><br />
		<div class="site_title">Profileinstellungen</div><br /><br /><br /><br />
		<div class="login_causes">
			<form name="change_data" action="settings_profile.php" method="post">
				Name:
				<?php
					if($changed == true)
					{
						$get_name = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
						$name = mysql_query($get_name);
						$name = mysql_fetch_row($name);
						echo $name[0];
					}
					else
					{
						echo $name[0];
					}
				?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="change_name" class="input-xlarge" maxlength="50">&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary" ><i class="icon-pencil"></i>&nbsp;&nbsp;Ändern</button><br /><br /><?php echo $error_1; ?><br />
				E-Mail: <?php echo $email[0];?><br /><br /><br />
				<input type="text" name="change_pw" class="input-xlarge" maxlength="50">&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-primary" ><i class="icon-pencil"></i>&nbsp;&nbsp;Passwort ändern</button><br /><br /><?php echo $error_3; ?>
			</form>
		</div>
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