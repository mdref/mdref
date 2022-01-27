"use strict";

$(function() {
	var mdref = {
		log: function log() {
			console.log.apply(console, arguments);
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
				var hash = decodeURIComponent(location.hash.substring(1));
				var e;
				if ((e = document.getElementById(location.hash.substring(1)))) {
					mdref.blink(e);
				} else {
					var scrolled = false;

					if (hash.substring(hash.length-1) === "*") {
						hash = hash.substring(0, hash.length-1);
					}
					$((hash.substring(0,1) === "$") ? ".var" : ".constant").each(function(i, c) {
						if (c.textContent.substring(0, hash.length) === hash) {
							if (!scrolled) {
								$(window).scrollTop($(c).offset().top - 100);
								scrolled = true;
							}
							mdref.blink(c);
						}
					});
				}
			}
		}
	};

	$(window).on("hashchange", mdref.hashchange);
	mdref.hashchange();

	$("#disqus_activator").on("click", function() {
		var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	});
	$.ajax("https://disqus.com/api/3.0/threads/details.json?thread:ident="+(disqus_identifier||"index")+"&forum=mdref&api_key=VmhVG4z5jjtY8SCaMstOjfUuwniMv43Xy9FCU9YfEzhsrl95dNz1epykXSJn8jt9"). then(function(json) {
		if (json && json.response) {
			$("#disqus_activator span").text(json.response.posts);
		}
	});
	setTimeout(function() {
		$("footer").addClass("hidden");
	}, 1000);
});
