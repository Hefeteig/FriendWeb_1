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
				alert("Request failed. Page not found\nStopped at readyState: " + xmlhttp.readyState);
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

function Parameter(_name, _value)
{
	this.name = _name;
	this.value = _value;
}

$("#content").hide();
$("#formdiv").hide();
function login ()
{
	var request = new AjaxRequest("login.php" , changeToChat, new Array(new Parameter("name", document.getElementById("name").value)));
	request.send();
}
function changeToChat (request)
{
	$("#logindiv").hide();
	$("#content").show();
	$("#formdiv").show();
	alert("Eingeloggt als " + request.responseText);
	chatrequest();
}

function chatrequest()
{
	var request = new AjaxRequest("getchat.php" , updateContent, new Array());
	request.send();
	
	window.setTimeout(chatrequest, 2000);
}
function updateContent(request)
{
	$("#content").html(request.responseText);
}

function send()
{
	var request = new AjaxRequest("insertchat.php" , updateContent, new Array(new Parameter("content", document.getElementById("input").value)));
	request.send();
}