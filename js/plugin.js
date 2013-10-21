function Plugin(name, init)
{
	this.name = name;
	this.init = init;
	this.actions = new Array();
	this.preActionlisteners = new Array();
	this.afterActionlisteners = new Array();
        this.depend = 0;
	
	this.initialize = function()
	{
		this.init();
		
                var actionid = new ActionId(new Date(), name);
                
		for(var a = 0; a<this.afterActionlisteners.length; a++)
		{
			if(this.afterActionlisteners[a].name==="init")
			{
				this.afterActionlisteners[a].react(actionid,null,null);
			}
		}
	};
	
	this.callAction = function(name, input, actionid)
	{
                if(actionid===null)
                {
                    actionid = new ActionId(new Date(), name);
                }
                
		for(var a = 0; a<this.preActionlisteners.length; a++)
		{
			if(this.preActionlisteners[a].name===name)
			{
				this.preActionlisteners[a].react(actionid,input);
			}
		}
		
		var output;
		for(var a = 0; a<this.actions.length; a++)
		{
			if(this.actions[a].name===name)
			{
				output = this.actions[a].call(input);
			}
		}
		
		for(var a = 0; a<this.afterActionlisteners.length; a++)
		{
			if(this.afterActionlisteners[a].name===name)
			{
				this.afterActionlisteners[a].react(actionid,input,output);
			}
		}
	};
}

function ActionListener(name, reaction, obj)
{
	this.name = name;
	this.methodname = reaction;
        this.obj = obj;
        
        this.alreadyReacted = new Array();
        
        this.react = function(actionId, input, output)
        {
            var reacted = false;
            for(var a = 0; a<this.alreadyReacted.length; a++)
            {
                if(this.alreadyReacted[a]===actionId)
                {
                    reacted = true;
                }
            }
            if(!reacted)
            {
                this.alreadyReacted.push(actionId);
                if(input===null)
                {
                    obj[this.methodname](name);
                }
                else if(output===null)
                {
                    obj[this.methodname](name, input);
                }
                else
                {
                    obj[this.methodname](name, input, output);
                }
            }
        };
}

function Action(name, func)
{
	this.name = name;
	this.func = func;
	
	this.call = function(arguments)
	{
		var toCall = "this.func(";
		for(var a = 0; a<arguments.length; a++)
		{
			if(a>0)
			{
				toCall += ",";
			}
			if(typeof arguments[a] === "string")
			{
				toCall += "'" + arguments[a] + "'";
			}
			else
			{
				toCall += arguments[a];
			}
		}
		toCall += ")";
		return eval(toCall);
	};
}

function ActionId(timepoint, name)
{
    this.timepoint = timepoint;
    this.name = name;
}