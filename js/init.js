var initplugins = new Array();

function init()
{
    for(var a = 0; a<plugins.length; a++)
    {
        initplugins.push(new Plugin(plugins[a][0],eval(plugins[a][0] + ".init")));
    }
    
    for(var a = 0; a<plugins.length; a++)
    {
        if(eval(plugins[a][0] + ".actions")===undefined)
        {
            eval(plugins[a][0] + ".actions = new Array();");
        }
        for(var b = 0; b<eval(plugins[a][0] + ".actions").length; b++)
        {
            initplugins[a].actions.push(eval(plugins[a][0] + ".actions")[b][1]);
        }
    }
    
    for(var a = 0; a<plugins.length; a++)
    {
        for(var b = 0; b<plugins[a][1].length; b++)
        {
            for(var c = 0; c<initplugins.length; c++)
            {
                if(plugins[a][1][b]===initplugins[c].name)
                {
                    initplugins[a].depend++;
                    initplugins[c].afterActionlisteners.push(new ActionListener("init", "initialize",initplugins[a]));
                }
            }
        }
    }
    
    for(var c = 0; c<initplugins.length; c++)
    {
        if(initplugins[c].depend===0)
        {
            initplugins[c].initialize(); 
        }
    }
}