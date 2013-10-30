<?php
	if(isset($_COOKIE["userid"]) || isset($_SESSION["userid"]))
	{
		echo "Eingeloggt.";
	}
	else
	{
		echo "Ausgeloggt.";
	}
?>