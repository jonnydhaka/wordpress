<?php
 require_once(BESTEL_THEME_DIR . "/customwoo.php");
 
 
add_filter( 'woocommerce_data_stores', 'woocommerce_data_stores' );

function woocommerce_data_stores ( $stores ) {      
    $stores['product'] = 'WCCPT_Product_Data_Store_CPT';
    return $stores;
}

add_filter('woocommerce_product_get_price', 'my_woocommerce_product_get_price', 10, 2 );

function my_woocommerce_product_get_price( $price, $product ) {
	
	$price = 15.00;
    return $price;
}

function save_post_meta($post_id){
  if(isset($_POST['_regular_price']))
  update_post_meta($post_id, '_regular_price', sanitize_text_field($_POST['_regular_price']));      

}

add_action('save_post', 'save_post_meta');

/*Now if you try adding to cart using url param add-to-cart=[POST_ID] like 
http://localhost/wordpress/cart/?add-to-cart=244 will add the item to cart.

    You can also use button to add to cart.
    
    <form action="" method="post">
        <input name="add-to-cart" type="hidden" value="<?php the_ID(); ?>" />
        <input name="quantity" type="number" value="1" min="1"  />
        <input name="submit" type="submit" value="Add to cart" />
    </form>*/