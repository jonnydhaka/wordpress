/*global redux_change, redux*/

(function($) {
	"use strict";

	$("#cvec_button_action").on("click", function(e) {
		e.preventDefault();
		jQuery.ajax({
			type: "post",
			url: combine_vc_ele_object.ajax_url,
			data: { action: "combine_vc_ele_css_clear_cache" },
			success: function(msg) {
				var obj = JSON.parse(msg);
				if (obj.status == "success") {
					alert("Clear");
				}
			}
		});
	});
})(jQuery);
