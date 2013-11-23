<?php

	function loadPlugin($name, $plugins)
	{
		include $name . "/main.php";
		array_push($plugins, new $name($name));
		return $plugins;
	}

	session_start();
	if(isset($_COOKIE["userid"]) || isset($_SESSION["userid"]))
	{
		include("simplehtmldom/simple_html_dom.php");
		include "plugin.php";
			require_once './lib/Twig/Autoloader.php';
			Twig_AutoLoader::register();
			
		Plugin::$mainhtml = file_get_html('main.html');	
		$plugins = array();
		
		$id;
		if(isset($_COOKIE["userid"]))
		{
			$id = $_COOKIE["userid"];
		}
		else
		{
			$id = $_SESSION["userid"];
		}
		$connect = mysqli_connect("localhost", "root", "XAMPPpassword");
		mysqli_select_db($connect, "friendweb");
		
		$plg = array();
		$result = array();
		//$id = $id[0];
		
		$result = mysqli_query($connect, "SELECT `plugin` FROM `activatedplugins` WHERE `user` = '".$id."'");
		
		while ($data = mysqli_fetch_array($result))
		{
			$add = $data[0];
			array_push($plg, $add);
		}
		
		foreach($plg as $curplg)
		{
			$plugins = loadPlugin($curplg, $plugins);
		}
		
		
		for($a = 0; $a<count($plugins); $a++)
		{
			for($b = 0; $b<count($plugins[$a]->dependencies); $b++)
			{
				for($c = 0; $c<count($plugins); $c++)
				{
					if($plugins[$a]->dependencies[$b]==$plugins[$c]->name)
					{
						array_push($plugins[$c]->dependent, $plugins[$a]);
						array_push($plugins[$a]->parent_plg, $plugins[$c]);
					}
				}
			}
		}
		
		for($a = 0; $a<count($plugins); $a++)
		{
			if(count($plugins[$a]->dependencies)==0)
			{
				$plugins[$a]->init();
			}
		}
		
		for($a = 0; $a<count($plugins); $a++)
		{
			if(count($plugins[$a]->dependencies)==0)
			{
				$plugins[$a]->print_context();
			}
		}
		
			$loader = new Twig_Loader_Filesystem('./');
			$twig = new Twig_Environment($loader, array());
			$template = $twig->loadTemplate('main.js');
			$params = array(
				"plugins" => array()
			);
			foreach ($plugins as $plugin) {
				if(count($plugin->javascripts)>0)
				{
					$names = array();
					foreach($plugin->parent_plg as $parent)
					{
						array_push($names, $parent->name);
					}
					array_push($params["plugins"], array(
						"name" => $plugin->name,
						"dependencies" => $names
					));
				}
			}
			
			Plugin::$mainhtml->find("head", 0)->innertext .= "<script type=\"text/javascript\">".$template->render($params)."</script>";
			Plugin::$mainhtml->find("head", 0)->innertext .= "<script type=\"text/javascript\" src=\"js/init.js\"></script>";
			
			
		echo Plugin::$mainhtml;
	}
	else
	{
		header("Location: login.php");
	}
	mysqli_close($connect);
?>