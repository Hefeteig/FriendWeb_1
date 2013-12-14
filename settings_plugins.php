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
		$sp_settings = false;
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
			$changes = "<div class='alert alert-success'>Das Plugin &quot;Startseite modifizieren&quot; wurde erfolgreich deaktiviert.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
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
			$content = str_replace('\n', '<br />', $content);
			
			if($content != '')
			{
				//Verschlüsseln
				//Seed errechnen
				$seed = $userid + 284917;
				
				function encodeRand($str, $seed)
				{
					mt_srand($seed);
					$out = array();
					for ($x=0, $l=strlen($str); $x<$l; $x++)
					{
						$out[$x] = (ord($str[$x]) * 3) + mt_rand(350, 16000);
					}
					mt_srand();
					return implode('-', $out);
				}
				$cryptedcontent = encodeRand($content, $seed);
			}
			else
			{
				$content = '';
			}
			
			$update_content = "UPDATE `startpage` SET `content` = '".$cryptedcontent."' WHERE `userid` = '".$userid."'";
			mysql_query($update_content);
			$changes = "<div class='alert alert-success'>Einstellungen gespeichert.<button type='button' class='close' data-dismiss='alert'>&times;</button></div>";
		}
		
		$check_startpage = "SELECT `plugin` FROM `activatedplugins` WHERE `plugin` = 'Startpage' AND `user` = ".$userid."";
		$startpage = mysql_query($check_startpage);
		$startpage = mysql_fetch_row($startpage);
?>
	<div id="protokoll">
		<br /><br /><br />
		<div class="site_title">Plugineinstellungen</div><br /><br /><br />
		<div class="news_article">
			<br />
			<?php echo $changes; ?>
			<br /><br />
			<b><i class="icon-pencil"></i> Startseite modifizieren</b><br /><br />
			Wie der Name schon sagt kannst du damit deine eigene Startseite erstellen, die erscheint wenn du dich einloggst.<br />
			Egal ob es einfach nur eine Erinnerung an einen Termin oder ein lustiges Bild sein soll, du hast die Wahl!<br />
			Du kannst sowohl einen einfachen Text schreiben, aber auch HTML verwenden. Die eingetragenen Daten werden natürlich auch verschlüsselt.<br /><br />
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
						
						if($content == '')
						{
							$content = '';
						}
						else
						{
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
							$seed = $userid + 284917;
							$content = decodeRand($content, $seed);
						}
						
						echo "Inhalt:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name='content' rows='5' class='input-xxlarge' maxlength='1500' autofocus>";
						echo $content;
						echo "</textarea><br /><br />";
						echo "<input type='submit' class='btn btn-primary' name='startpage_content' value='Speichern' />";
					}
				?>
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
		require_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('./');
		$twig = new Twig_Environment($loader, array());
		$template = $twig->loadTemplate('logout-header.html');
		$params = array();
		$template->display($params);
	}
?>





