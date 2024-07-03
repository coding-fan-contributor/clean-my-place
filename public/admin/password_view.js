/*Nina Kong*/

function showPassword() {
	var target = $("#showHide");
	target.click(function() {
		if ($(".passwordswl").attr("type")=="password") {
			$(".passwordswl").attr("type", "text");
		} else {
			$(".passwordswl").attr("type", "password");
		}
	})
}

$(document).ready(function () {
	"use strict";
	showPassword();
});
