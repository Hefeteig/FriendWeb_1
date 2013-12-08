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
		
		$changes = "";
		/*$sp_settings = false;
		if($_POST['startpage_on'])
		{
			$insert_sp_1 = "INSERT INTO `activatedplugins` (`plugin`, `user`) VALUES ('Startpage', ".$userid.")";
			$insert_sp_2 = "INSERT INTO `startpage` (`content`, `userid`) VALUES ('', ".$userid.")";
			mysql_query($insert_sp_1);
			mysql_query($insert_sp_2);
			$changes = "<div class='alert alert-success'>Das Plugin Startpage wurde erfolgreich aktiviert. Du kannst nun Einstellungen vornehmen.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
			
		}
		elseif($_POST['startpage_off'])
		{
			$delete_sp_1 = "DELETE FROM `activatedplugins` WHERE `plugin` = 'Startpage' AND `user` = ".$userid."";
			$delete_sp_2 = "DELETE FROM `startpage` WHERE `userid` = ".$userid."";
			mysql_query($delete_sp_1);
			mysql_query($delete_sp_2);
			$changes = "<div class='alert alert-success'>Das Plugin Startpage wurde erfolgreich deaktiviert.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
		}
		elseif($_POST['startpage_settings'])
		{
			$sp_settings = true;
		}
		elseif($_POST['startpage_content'])
		{
			//Filtern (bis auf HTML Elemente)
			$content = trim($_POST['content']);
			$content = mysql_real_escape_string($content);
			$update_content = "UPDATE `startpage` SET `content` = '".$content."' WHERE `userid` = '".$userid."'";
			mysql_query($update_content);
			$changes = "<div class='alert alert-success'>Einstellungen gespeichert.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
		}
		
		$check_startpage = "SELECT `plugin` FROM `activatedplugins` WHERE `plugin` = 'Startpage' AND `user` = ".$userid."";
		$startpage = mysql_query($check_startpage);
		$startpage = mysql_fetch_row($startpage);*/
?>
	<div id="protokoll">
		<br /><br /><br />
		<div class="site_title">Plugineinstellungen</div><br /><br /><br />
		<div class="news_article">
			<br />
			<?php echo $changes; ?>
			<br /><br />
			Am 08.12.2013 erscheint das erste Plugin mit dem du deine persönliche Startseite erstellen kannst.
			<?php/*
			<b><i class="icon-pencil"></i> Startseite modifizieren</b><br /><br />
			Wie der Name schon sagt kannst du damit deine eigene Startseite erstellen, die erscheint wenn du dich einloggst.<br />
			Egal ob es einfach nur eine Erinnerung an einen Termin oder ein lustiges Bild sein soll, du hast die Wahl!<br />
			Du kannst sowohl einen einfachen Text schreiben, aber auch HTML verwenden.<br /><br />
			<form name="startpage" action="settings_plugins.php" method="post">
				<?php
					if($startpage == 0)
					{
						echo "<input type='submit' class='btn btn-success' name='startpage_on' value='Aktivieren' />";
					}
					else
					{
						echo "<input type='submit' class='btn btn-danger' name='startpage_off' value='Deaktivieren' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='btn btn-primary' name='startpage_settings' value='Einstellungen' /><br /><br />";
					}
					
					if($sp_settings == true)
					{
						$content_query = "SELECT `content` FROM `startpage` WHERE `userid` = '".$userid."'";
						$content = mysql_query($content_query);
						$content = mysql_fetch_row($content);
						$content = $content[0];
						echo "Inhalt:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name='content' rows='5' class='input-xxlarge' maxlength='1500' autofocus>";
						echo $content;
						echo "</textarea><br /><br />";
						echo "<input type='submit' class='btn btn-primary' name='startpage_content' value='Speichern' />";
					}
				?>
			</form>
			*/?>
		</div>
	</div>
	<div id="friends">
	</div>
<?php
		mysql_close($sql);
	}
	else
	{
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('logout-header.html');
		$params = array();
		$template->display($params);
	}
?>




