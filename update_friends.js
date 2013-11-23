
function AjaxRequest(_file, _readyFunction, _parameters)
{
	var file = _file;
	var readyFunction = _readyFunction;
	var parameters = _parameters;
	
	this.send = function()
	{
		var xmlhttp;
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				readyFunction(xmlhttp);
			}
			else if(xmlhttp.status==404)
			{
				alert("Es ist eine Fehler beim laden deiner Kontaktliste entstanden, bitte teile folgenden Text dem Administrator mit:\n Page not found Stopped at readyState: " + xmlhttp.readyState);
			}
		}
		xmlhttp.open("POST",file,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		
		var sendparams = "";
		for(var a = 0; a<parameters.length; a++)
		{
			if(a>0)
			{
				sendparams += "&";
			}
			sendparams += parameters[a].name + "=" + parameters[a].value;
		}
		
		xmlhttp.send(sendparams);
	}
}


function friendrequest()
{
	var request = new AjaxRequest("friends.php", function(request){document.getElementById("friends").innerHTML=request.responseText;}, []);
	request.send();
	window.setTimeout(friendrequest, 10000);
}
/*
function AjaxRequest()
{
	
	
	this.send = function()
	{
		var xmlhttp;
		if (window.XMLHttpRequest)
		{
			xmlhttp=new XMLHttpRequest();
		}
		else
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
				 document.getElementById("friends").innerHTML=xmlhttp.responseText;
			}
			else if(xmlhttp.status==404)
			{
				alert("Es ist eine Fehler beim laden deiner Kontaktliste entstanden, bitte teile folgenden Text dem Administrator mit:\n Page not found, stopped at readyState: " + xmlhttp.readyState);
			}
		}
		xmlhttp.open("GET", "friends.php?id=5", true);
		xmlhttp.send();
	}
}*/