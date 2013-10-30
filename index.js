function is_constant(s) {
	return s.length > 3 &&  s.toUpperCase(s) === s;
}

function is_variable(s) {
	return s.substring(0,1) === "$";
}

function type(s) {
	var i, j, t;
	
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
	case "mixed":
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
	
	// class members
	if (-1 !== (i = s.indexOf("::"))) {
		t = s.substring(i+2);
		if (!is_constant(t) && !is_variable(t)) {
			// methods
			return "<a href=\"/" + s.replace(/::|\\/g, "/") + "\">";
		}
	}
	if (-1 !== (j = s.indexOf("\\"))) {
		return "<a href=\"/" + s.replace(/\\/g, "/").replace(/::|$/, "#") + "\">";
	}
	
	switch (s.toLowerCase()) {
	// variables
	default:
		if (!is_variable(s)) {
			break;
		}
	// special constants
	case "null":
	case "true":
	case "false":
		return "<span class=\"var\">";
	}
	
	// constants
	if (is_constant(s)) {
		return "<span class=\"constant\">";
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

function hashchange() {
	if (location.hash.length > 1) {
		var hash = location.hash.substring(1);
		
		$(is_variable(hash) ? ".var" : ".constant").each(function(i, c) {
			
			if (c.textContent === hash) {
				var $c = $(c);
				
				$(window).scrollTop($c.offset().top - 100);
				$c.fadeOut("slow").queue(function(next) {
					this.style.color = "red";
					next();
				}).fadeIn("fast").fadeOut("fast").queue(function(next) {
					this.style.color = "";
					next();
				}).fadeIn("slow");
				return false;
			}
		});
	}
}

$(function() {
	$("h1,h2,h3,h4,h5,h6,p,li,code").each(walk);
	$(window).on("hashchange", hashchange);
	hashchange();
});
