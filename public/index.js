"use strict";

document.addEventListener("DOMContentLoaded", function() {
	const doTransition = function(e, trans, state, speed) {
		e.classList.remove(trans + "-in", trans + "-out", "trans-slow", "trans-fast");
		e.classList.add(trans + "-" + state, "trans-" + speed);
		return (cb) => setTimeout(cb, speed === "slow" ? 600 : 200);
	};
	const letElementBlink = function(e, last) {
		setTimeout(() => doTransition(e, "fade", "out", "fast")(function() {
			e.classList.add("blink");
			doTransition(e, "fade", "in", "fast")(function() {
				e.classList.remove("blink");
				doTransition(e, "fade", "out", "slow")(function () {
					doTransition(e, "fade", "in", "slow");
				});
			});
		}), 200);
	};
	const onHashChange = function() {
		if (location.hash.length > 1) {
			let hash = decodeURIComponent(location.hash.substring(1));
			let e = document.getElementById(hash) || document.getElementById(location.hash.substring(1));
			if (e) {
				letElementBlink(e);
			} else {
				if (hash.substring(hash.length-1) === "*") {
					hash = hash.substring(0, hash.length-1);
				}
				let klass = (hash.substring(0,1) === "$") ? "var" : "constant";
				let scrolled = false;
				Array.prototype.forEach.call(document.getElementsByClassName(klass), function(e) {
					if (e.textContent.substring(0, hash.length) !== hash) {
						return;
					}
					if (!scrolled) {
						scrolled = true;
						window.scrollTo(0, e.offsetTop > 64 ? e.offsetTop - 64 : 0);
					}
					letElementBlink(e);
				});
			}
		}
	};

	onHashChange();
	window.addEventListener("hashchange", onHashChange);
	setTimeout(()=>document.getElementsByTagName("footer")[0].classList.add("hidden"), 1000);
})
