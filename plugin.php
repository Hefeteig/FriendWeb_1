<?php
	abstract class Plugin
	{
		public static $mainhtml;
		
		public $name;
		
		public $dependencies;
		public $loadedplugins = array();
		
		public $styles;
		public $javascripts;
		public $html;
		
		public $dependent = array();
		
		public $parent_plg = array();
		
		public $alreadyprinted = false;
		
		public function __construct($path)
		{
			$mainfile = file($path . "\config.tmpl");
			$this->name = trim($mainfile[0]);
			$this->dependencies = explode(";", trim($mainfile[1]));
			if(trim($this->dependencies[0])=="")
			{
				$this->dependencies = array();
			}
			for($a = 0; $a<count($this->dependencies); $a++)
			{
				$this->loadedplugins[$a] = false;
			}
			
			$this->styles = explode(";", $mainfile[2]);
			if(trim($this->styles[0])=="")
			{
				$this->styles = array();
			}
			for($a = 0; $a<count($this->styles); $a++)
			{
				$this->styles[$a] = arrayToString(file($path . "/styles/" . trim($this->styles[$a])));
			}
			
			$this->javascripts = explode(";", $mainfile[3]);
			if(trim($this->javascripts[0])=="")
			{
				$this->javascripts = array();
			}
			for($a = 0; $a<count($this->javascripts); $a++)
			{
				$this->javascripts[$a] = arrayToString(file($path . "/js/" . trim($this->javascripts[$a])));
			}
			
			
			ob_start();
			require($path . "\contents.php");
			$data = ob_get_clean();
			ob_end_clean();
			$this->html = str_get_html($data);
			
			$this->init_Listeners = array();
		}
		
		public function init()
		{
			$this->init_func();
			
			for($a = 0; $a<count($this->dependent); $a++)
			{
				foreach($this->parent_plg as $curplg)
				{
					array_push($this->dependent[$a]->parent_plg,$curplg);
				}
				$this->dependent[$a]->pluginloaded($this->name);
			}
		}
		
		public function pluginloaded($pluginname)
		{
			for($a = 0; $a<count($this->loadedplugins); $a++)
			{
				if($this->dependencies[$a]==$pluginname)
				{
					$this->loadedplugins[$a] = true;
				}
			}
			
			$all = true;
			for($a = 0; $a<count($this->loadedplugins); $a++)
			{
				if($this->loadedplugins[$a]==false)
				{
					$all = false;
				}
			}
			if($all)
			{
				$this->init();
			}
		}
		
		public function getParent($name)
		{
			foreach($this->parent_plg as $curpar)
			{
				if($curpar->name == $name)
				{
					return $curpar;
				}
			}
			return null;
		}
		
		public function print_context()
		{
			if(!$this->alreadyprinted)
			{
				$this->alreadyprinted = true;
				for($a = 0; $a<count($this->styles); $a++)
				{
					self::$mainhtml->find("head", 0)->innertext .= "<style type='text/css'>" . $this->styles[$a] . "</style>";
				}
				for($a = 0; $a<count($this->javascripts); $a++)
				{
					self::$mainhtml->find("head", 0)->innertext .= "<script type='text/javascript'>" . $this->javascripts[$a] . "</script>";
				}
				
				foreach($this->html->find("div.parent") as $element)
				{
					$finder = explode(";",$element->id);
					$elem = self::$mainhtml->find($finder[0],$finder[1]);
					if($elem)
					{
						$elem->innertext .= $element->innertext;
						self::$mainhtml = str_get_html(self::$mainhtml);
					}
					for($a = 0; $a<count($this->dependent); $a++)
					{
						$this->dependent[$a]->print_context();
					}
				}
			}
		}
		
		abstract function init_func();
	}
	
	function arrayToString($array)
	{
		$ret = "";
		for($a = 0; $a<count($array); $a++)
		{
			$ret .= $array[$a]."\n";
		}
		return $ret;
	}
?>
