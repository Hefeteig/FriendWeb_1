<div id="body;0" class="parent">
	<div class="fw_header">
		<div class="navbar navbar-inverse over">
			<div class="navbar-inner">
				
				<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</a>
				 
				<!-- Be sure to leave the brand out there if you want it shown -->
				<a class="brand" href="index.php"><img src="pics/friendweb_F.png" alt="FriendWeb Logo"></a>
				 
				<!-- Everything you want hidden at 940px or less, place within here -->
				<div class="nav-collapse collapse">
				<!-- .nav, .navbar-search, .navbar-form, etc -->
				
					<div class="heading">
							<br><br>
							<a href="index.php"><button class="btn btn-primary" type="button"><i class="icon-home"></i> Startseite</button></a>&nbsp;&nbsp;
							
							<div class="btn-group">
								<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href=""><i class="icon-envelope"></i> Nachrichten&nbsp;&nbsp;<span class="caret"></span></button>
								<ul class="dropdown-menu">
									<li><a href="chat.php"><div class="menu_style">Verlauf</div></a></li>
									<li><a href="write_message.php"><div class="menu_style">Nachricht schreiben</div></a></li>
								</ul>
							</div>&nbsp;&nbsp;
							
							<div class="btn-group">
								<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href=""><i class="icon-user"></i> Kontakte&nbsp;&nbsp;<span class="caret"></span></button>
								<ul class="dropdown-menu">
									<li><a href="addfriend.php"><div class="menu_style">Hinzuf&uuml;gen</div></a></li>
									<li><a href="confirmfriend.php"><div class="menu_style">Anfragen verwalten</div></a></li>
								</ul>
							</div>&nbsp;&nbsp;
							
							<div class="btn-group">
								<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href=""><i class="icon-cog"></i> Einstellungen&nbsp;&nbsp;<span class="caret"></span></button>
								<ul class="dropdown-menu">
									<li><a href="settings_plugins.php"><div class="menu_style">Plugins</div></a></li>
									<li><a href="settings_profile.php"><div class="menu_style">Profil</div></a></li>
								</ul>
							</div>&nbsp;&nbsp;
							
							<a href="mailservice.php"><button class="btn btn-primary" type="button"><i class="icon-pencil"></i> Mailservice</button></a>&nbsp;&nbsp;
							
							<a href="news.php"><button class="btn btn-primary" type="button"><i class="icon-globe"></i> News</button></a>&nbsp;&nbsp;
							
							<div class="btn-group">
								<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href=""><i class="icon-plus"></i> Sonstiges&nbsp;&nbsp;<span class="caret"></span></button>
								<ul class="dropdown-menu">
									<li><a href="faq.php"><div class="menu_style">FAQ</div></a></li>
									<li><a href="contact.php"><div class="menu_style">Kontakt</div></a></li>
									<li><a href="impressum.php"><div class="menu_style">Impressum</div></a></li>
								</ul>
							</div>
							
							<div class="right"><a href="logout.php"><button class="btn btn-primary" type="button"><i class="icon-circle-arrow-right"></i> Ausloggen</button></a></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="protokoll">
		<br /><br /><br /><br />
		<?php
			$userid = $_SESSION['userid'][0];
			require 'db.php';
			$get_user = "SELECT `name` FROM `users` WHERE `userid` = '".$userid."'";
			$user = mysql_query($get_user);
			$user = mysql_fetch_row($user);
		?>
		<div class="site_title">Hallo <?php echo $user[0]; ?></div>
		<div class="login_causes">
			<br /><br /><br /><br />
			<?php
				$startpage_active = "SELECT `content` FROM `startpage` WHERE `userid` = ".$userid."";
				$sp= mysql_query($startpage_active);
				$sp = mysql_fetch_row($sp);
				$sp = $sp[0];
				
				if($sp != '')
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
					$sp_1 = decodeRand($sp, $seed);
				}
				elseif($sp === '')
				{
					$sp_1 = "Du hast das Plugin &quot;Startseite modifizieren&quot; aktiviert und kannst nun diesen Text in den Einstellungen verändern.";
				}
				
				if($sp_1)
				{
					echo $sp_1;
				}
				elseif($sp_2)
				{
					echo $sp_2;
				}
				else
				{
					echo "Dieser Text kann durch das Plugin &quot;Startseite modifizieren&quot; verändert werden.";
				}
			?>
		</div>
	</div>
	<div id="friends">
	</div>
</div>