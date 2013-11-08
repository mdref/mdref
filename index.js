function log() {
	// console.log.apply(console, arguments);
}

function is_constant(s) {
	s = s.replace(/v\d+(_\d+)?$/, "");
	if (s.length < 2) {
		return false;
	}
	return s.toUpperCase(s) === s;
}

function is_variable(s) {
	return s.substring(0,1) === "$";
}

var is_in_string = false;

function type(s, nn) {
	var i, j, t;
	//log("type", s);
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
	case "resource":
	case "array":
	case "object":
	case "callable":
	case "mixed":
	// Zend/SPL
	case "stdClass":
	case "Exception":
	case "ErrorException":
	case "Closure":
	case "Generator":
	case "Countable":
	case "Serializable":
	case "Traversable":
	case "Iterator":
	case "IteratorAggregate":
	case "ArrayAccess":
	case "ArrayObject":
	case "ArrayIterator":
	case "RecursiveArrayIterator":
	case "SplObserver":
	case "SplSubject":
	case "SplObjectStorage":
		return "<code>";
		
	// keywords
	case "is":
		if (nn !== "H1") {
			return;
		}
	case "extends":
	case "implements":
		if (nn === "H1") {
			return "<br>&nbsp;<em>";
		}
	case "class":
	case "interface":
	case "namespace":
	case "public":
	case "protected":
	case "private":
	case "static":
	case "final":
	case "abstract":
	case "self":
	case "parent":
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
			return "<a href=\"" + s.replace(/::|\\/g, "/") + "\">";
		}
	}
	if (-1 !== (j = s.indexOf("\\")) && s.substr(j+1,1) !== "n") {
		return "<a href=\"" + s.replace(/\\/g, "/").replace(/::|$/, "#") + "\">";
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

function node(s, nn) {
	//log("node", s);
	
	var t;
	
	if ((t = type(s, nn))) {
		return $(t).text(s);
	}
	return document.createTextNode(s);
}
function wrap(n, nn) {
	var $n = $(n)
	var a = [];

	$n.text().split(/([^a-zA-Z0-9_\\\$:]+)/).forEach(function(v) {
		a.push(node(v, nn));
	});
	$n.replaceWith(a);
}
function walk(i, e) {
	log("walk", i, e);

	$.each($.makeArray(e.childNodes), function(i, n) {
		switch (n.nodeName) {
		case "A":
		case "BR":
		case "HR":
			break;
		case "#text":
			wrap(n, e.nodeName);
			break;
		default:
			walk(-1, n);
			break;
		}
	});
}

function blink(c) {
	var $c = $(c);
	
	$c.fadeOut("fast").queue(function(next) {
		this.style.color = "red";
		next();
	}).fadeIn("fast").fadeOut("slow").queue(function(next) {
		this.style.color = "";
		next();
	}).fadeIn("slow");
}

function hashchange() {
	if (location.hash.length > 1) {
		var hash = location.hash.substring(1);
		var name = is_variable(hash) ? ".var" : ".constant";
		var scrolled = false;
		
		$(name).each(hash.substring(hash.length-1) === "_" ? function(i, c) {
			if (c.textContent.substring(0, hash.length) === hash) {
				if (!scrolled) {
					$(window).scrollTop($(c).offset().top - 100);
					scrolled = true;
				}
				blink(c);
			}
		} : function(i, c) {
			if (c.textContent === hash) {
				$(window).scrollTop($(c).offset().top - 100);
				blink(c);
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
