(function($) {
	"use strict";
	var siototal = 1;
	var siocall = 0;
	$(document).ready(function($) {
		$(".sio-demo-image-genaretor").on("click", function() {
			siototal = $(this).data("length");
			$(".sio-result-div").html();
			$(".sio-demo-image-progressbar").css("width", "0%");
			$(".sio-demo-image-progressbar span").text("0%");
			callAjax(0);
		});
		$(".sio-demo-image-downloader").on("click", function() {
			$(this).addClass("lds-hourglass");
			var $this = $(this);
			$.ajax({
				type: "POST",
				url: smartimageoverlay_ajax_object.ajax_url,
				data: {
					action: "smart_image_overlay_download"
				},
				success: function(res) {
					var resobj = JSON.parse(res);
					if (resobj.success) {
						window.location.href = resobj.massage;
						$($this).removeClass("lds-hourglass");
					}
				},
				error: function(a, b, c) {}
			});
		});
	});
	var callAjax = arraykey => {
		var promise = new Promise(function(resolve, reject) {
			resolve(arraykey);
		});
		promise.then(function(resvalidate) {
			$(".sio-progress").show();
			$(".sio-demo-image-genaretor").addClass("lds-hourglass");
			var parcentval = Math.ceil((resvalidate / siototal) * 100);
			if (parcentval > 100) {
				parcentval = 100;
			}
			$(".sio-demo-image-progressbar").css("width", parcentval + "%");
			$(".sio-demo-image-progressbar span").text(parcentval + "%");
			if (siototal == resvalidate) {
				$(".sio-demo-image-progressbar").css(
					"width",
					parcentval + "100%"
				);
				$(".sio-demo-image-progressbar span").text("Complete");
				$(".sio-result-div").append("<div>Complete</div>");
				$(".sio-demo-image-downloader").show();
				$(".sio-demo-image-genaretor").removeClass("lds-hourglass");
				return false;
			}
			$.ajax({
				type: "POST",
				url: smartimageoverlay_ajax_object.ajax_url,
				data: {
					action: "smart_image_overlay_generate",
					key: resvalidate
				},
				success: function(res) {
					var resobj = JSON.parse(res);
					$(".sio-result-div").append(
						"<div>" + resobj.massage + "</div>"
					);
					callAjax(resvalidate + 1);
				},
				error: function(a, b, c) {
					console.log(a);
					console.log(b);
					console.log(c);
				}
			});
		});
	};
})(jQuery);
