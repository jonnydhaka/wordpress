$('.tt-product-head__info form.cart').on('submit', function (e)
{
    e.preventDefault();
    var product_url = window.location,
        form = $(this);
	form.find('.single_add_to_cart_button').addClass('tt-btn__state--wait');
    $.post(product_url, form.serialize() + '&_wp_http_referer=' + product_url, function (result)
    {
        $.ajax({
			url: wc_cart_fragments_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ),
			type: 'POST',
			success: function( data ) {
				if ( data && data.fragments ) {
					$.each( data.fragments, function( key, value ) {
						$( key ).replaceWith( value );
					});
					form.find('.single_add_to_cart_button').removeClass('tt-btn__state--wait');
					form.find('.single_add_to_cart_button').addClass('tt-btn__state--active');
					$( document.body ).trigger( 'wc_fragments_refreshed' );
					$( document.body ).trigger( 'added_to_cart' );
				}
			}
		
		});
    });
});