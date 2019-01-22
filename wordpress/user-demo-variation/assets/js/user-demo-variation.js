(function ($) {
	"use strict";
	$(document).ready(function(){

		$.ajax({
			url: userviewdemoobjectlist.ajax_url,
			type: 'post',
            data: { action: 'ajax_add_html'},
            dataType: 'html',
            success: function (html) {
            $('body').prepend(html)
			var ttBoxedbutton = $(html);
			if (ttBoxedbutton.length){
				$(document).on('click', '.rtlbutton', function(e) {
					e.preventDefault;
					var target = e.target,
						$html = $('html'),
						$ttPageContent = $('#tt-pageContent'),

						$link = $('<link>', {
						rel: 'stylesheet',
						href: userviewdemoobjectlist.link +'/rtl.css',
						class: 'rtl'
						});
					if (!$(this).hasClass('external-link')){
						$(this).toggleClass('active');
					};


					var blocks = {
						ttBlogMasonry: $ttPageContent.find('.tt-blog-masonry'),
						ttPortfolioMasonry: $ttPageContent.find('.tt-portfolio-masonry'),
						ttProductMasonry: $ttPageContent.find('.tt-product-listing-masonry'),
						ttLookBookMasonry: $ttPageContent.find('.tt-lookbook-masonry'),
						ttLookbook: $ttPageContent.find('.tt-lookbook'),
					};

					if ($(this).hasClass('boxbutton-js')){
						$html.toggleClass('tt-boxed');
						if (blocks.ttProductMasonry.length) {
						gridProductMasonr();
						};
						if (blocks.ttLookBookMasonry.length) {
						gridLookbookMasonr();
						};
						if (blocks.ttBlogMasonry.length) {
						gridGalleryMasonr();
						};
						if (blocks.ttPortfolioMasonry.length) {
						gridPortfolioMasonr();
						initPortfolioPopup();
						};
						if (blocks.ttLookbook.length){
							ttLookbook(ttwindowWidth);
						};
						$('.slick-slider').slick('refresh');
					};

					if ($(this).hasClass('rtlbutton-js') && $(this).hasClass('active')){
						$link.appendTo('head');
					} else if($(this).hasClass('rtlbutton-js') && !$(this).hasClass('active')){
					$('link.rtl').remove();
					};
				});
				$(document).on('click', '.rtlbutton-color li', function(e){
					$('link[href^="css/theme-"]').remove();

					var dataValue = $(this).attr('data-color'),
						htmlLocation =  $('link[href^="css/theme-"]');

					if($(this).hasClass('active')) return;

					$(this).toggleClass('active').siblings().removeClass('active');
					console.log(dataValue)
					if(dataValue != undefined){
						$('head').append('<link rel="stylesheet" href="'+ userviewdemoobjectlist.link +'theme-'+dataValue+'.css" rel="stylesheet">');
					} else {
						//$('head').append('<link rel="stylesheet" href="css/theme.css" rel="stylesheet">');
					};

					return false;
				});
			};
			}
		});
});
})(jQuery);