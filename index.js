function type(s) {
	
	// nothing
	if (!s.match(/[a-zA-Z]/)) {
		return;
	}
	
	switch (s) {
	// types
	case "void":
	case "bool":
	case "int":
	case "float":
	case "string":
	case "array":
	case "object":
	case "callable":
		return "<code>";
		
	// keywords
	case "class":
	case "interface":
	case "namespace":
	case "extends":
	case "implements":
	case "public":
	case "protected":
	case "private":
	case "static":
	case "final":
	case "abstract":
	// phrases
	case "Optional":
	case "optional":
		return "<em>";
	}
	
	var is_namespace, is_method;
	
	if ((is_method = (s.indexOf("::") !== -1)) || (is_namespace = (s.indexOf("\\") !== -1))) {
		return "<a href=\"/" + s.replace(/::|\\/g, "/") + (is_method ? ".md":"") + "\">";
	}
	
	switch (s.toLowerCase()) {
	// variables
	default:
		if (s.substring(0,1) !== "$") {
			break;
		}
	// special constants
	case "null":
	case "true":
	case "false":
		return "<span class=\"var\">";
	}
	
	// constants
	if (s.toUpperCase() === s) {
		return "<code>";
	}
}
function node(s) {
	//console.log("node", s);
	
	var t;
	
	if ((t = type(s))) {
		return $(t).text(s);
	}
	return document.createTextNode(s);
}
function wrap(n) {
	var $n = $(n)
	var a = [];

	$n.text().split(/([^a-zA-Z_\\\$:]+)/).forEach(function(v) {
		a.push(node(v));
	});
	$n.replaceWith(a);
}
function walk(i, e) {
	//console.log("walk", i, e);

	e && $.each(e.childNodes, function(i, n) {
		//console.log(n.nodeName);
		switch (n.nodeName) {
		case "A":
			break;
		case "#text":
			wrap(n);
			break;
		default:
			walk(n);
		}
	});
}
$(document).ready(function() {
	//console.log("ready");

	$("h1,h2,h3,h4,h5,h6,p,li,code").each(walk);
});
