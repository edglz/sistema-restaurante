var $_getVariables={isset:false};
var $_getGlobalVariables={};
var $_GETAllVariables = function (){
	var scripts = document.getElementsByTagName("script");
	for(var i=0;i<scripts.length;i++){
	    var script = ( scripts[i].src+"" ).split("/");
	    script = script[script.length-1].split("?",2);
	    if (script.length>1)
	    {	
	        var parameters = script[1].split("&") 
	        for (var j=0;j<parameters.length;j++){
	            var vars = parameters[j].split("=");
	            if (!$_getVariables[script[0]]) $_getVariables[script[0]] = {};
	            $_getVariables[script[0]][vars[0]]=vars[1];
	            $_getGlobalVariables[vars[0]]=vars[1];
	        }
	    }
	}
	$_getVariables.isset=true;
};
$_GET = function(paramToGet,jsFile)
{
	if (!$_getVariables.isset) 
		$_GETAllVariables();
	if (jsFile)
		return $_getVariables[jsFile][paramToGet];
	else
		return $_getGlobalVariables[paramToGet];
};