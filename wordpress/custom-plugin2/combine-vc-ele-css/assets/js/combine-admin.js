if (typeof wp.customize != "undefined") {
	wp.customize.control("combine_cache_assets_button", function(control) {
		control.container.find(".combine_vc_ele_css").on("click", function() {
			console.log("Button was clicked.");
			jQuery.ajax({
				type: "post",
				url: combine_vc_ele_object.ajax_url,
				data: { action: "combine_vc_ele_css_clear_cache" },
				success: function(msg) {
					obj = JSON.parse(msg);
					if (obj.status == "success") {
						alert("Clear");
					}
				}
			});
		});
	});
}
