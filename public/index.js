"use strict";

$(function() {
	var mdref = {
		log: function log() {
			console.log.apply(console, arguments);
		},
		is_constant: function is_constant(s) {
			s = s.replace(/v\d+(_\d+)?$/, "");
			if (s.length < 2) {
				return false;
			}
			return s.toUpperCase(s) === s;
		},
		is_variable: function is_variable(s) {
			return s.substring(0,1) === "$";
		},
		type: function type(s, nn) {
			var i, j, t;
			// mdref.log("type", s);
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
			case "RuntimeException":
			case "UnexpectedValueException":
			case "DomainException":
			case "InvalidArgumentException":
			case "BadMethodCallException":
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
			case "SeekableIterator":
			case "SplObserver":
			case "SplSubject":
			case "SplObjectStorage":
			case "JsonSerializable":
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
				if (!mdref.is_constant(t) && !mdref.is_variable(t)) {
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
				if (!mdref.is_variable(s)) {
					break;
				}
			// special constants
			case "null":
			case "true":
			case "false":
				return "<span class=\"var\">";
			}

			// constants
			if (mdref.is_constant(s)) {
				return "<span class=\"constant\">";
			}
		},
		wrap: function wrap(n, nn) {
			var $n = $(n)
			var a = [];

			$n.text().split(/([^a-zA-Z0-9_\\\$:]+)/).forEach(function(v) {
				var t;
				
				if ((t = mdref.type(v, nn))) {
					a.push($(t).text(v));
				} else if (a.length && a[a.length-1].nodeName === "#text") {
					/* if we already have a text node and the next is also gonna be a text
					 * node, then join them, becuase chrome v30+ or something eats whitespace
					 * for breakfast, lunch and dinner!
					 */
					a[a.length-1].textContent += v;
				} else {
					a.push(document.createTextNode(v));
				}
			});
			$n.replaceWith(a);
		},
		walk: function walk(i, e) {
			// mdref.log("walk", i, e);

			$.each($.makeArray(e.childNodes), function(i, n) {
				switch (n.nodeName) {
				case "A":
				case "BR":
				case "HR":
				case "EM":
				case "CODE":
				case "SPAN":
					break;
				case "#text":
					mdref.wrap(n, e.nodeName);
					break;
				default:
					mdref.walk(-1, n);
					break;
				}
			});
		},
		blink: function blink(c) {
			var $c = $(c);

			$c.fadeOut("fast").queue(function(next) {
				this.style.color = "red";
				next();
			}).fadeIn("fast").fadeOut("slow").queue(function(next) {
				this.style.color = "";
				next();
			}).fadeIn("slow");
		},
		hashchange: function hashchange() {
			if (location.hash.length > 1) {
				var hash = location.hash.substring(1);
				var name = mdref.is_variable(hash) ? ".var" : ".constant";
				var scrolled = false;

				$(name).each(hash.substring(hash.length-1) === "_" ? function(i, c) {
					if (c.textContent.substring(0, hash.length) === hash) {
						if (!scrolled) {
							$(window).scrollTop($(c).offset().top - 100);
							scrolled = true;
						}
						mdref.blink(c);
					}
				} : function(i, c) {
					if (c.textContent === hash) {
						$(window).scrollTop($(c).offset().top - 100);
						mdref.blink(c);
						return false;
					}
				});
			}
		}
	};
	
	$("h1,h2,h3,h4,h5,h6,p,li,code,td").each(mdref.walk);
	$(window).on("hashchange", mdref.hashchange);
	mdref.hashchange();
});
