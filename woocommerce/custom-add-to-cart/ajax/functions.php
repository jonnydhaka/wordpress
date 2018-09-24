<?php
//service cart add
require_once("/priceclass.php");

add_filter( 'woocommerce_add_cart_item', 'themeservice_add_cart_item' , 10, 2 );

if (!function_exists('themeservice_add_cart_item')) {

    function themeservice_add_cart_item( $cart_item, $cart_id ) {	
        $post_type = get_post_type( $cart_item['data']->get_id() );
        if ( in_array( $post_type, array( 'devices' ) ) ) {
            $cart_item['data']->set_props(
                array(
                    'product_id'     => $cart_item['product_id'],
                    'check_in_date'  => $cart_item['check_in_date'],
                    'check_out_date' => $cart_item['check_out_date'],
                    'woo_cart_id'    => $cart_id,
                )
            );
        }
        return $cart_item;
    }

}

add_filter( 'woocommerce_product_class','themeservice_product_class' , 10, 4 );

if (!function_exists('themeservice_product_class')) {

    function themeservice_product_class( $classname, $product_type, $post_type, $product_id ) {
        if ( 'devices' == get_post_type( $product_id ) ) {
            $classname = 'HB_WC_Product_Room';
        }
        return $classname;
    }

}

add_action('wp_ajax_service_add_to_cart', 'themeservice_add_to_cart');
add_action('wp_ajax_nopriv_service_add_to_cart', 'themeservice_add_to_cart');

if (!function_exists('themeservice_add_to_cart')) {

    function themeservice_add_to_cart (){
        global $woocommerce;
        if ( ! $woocommerce || ! $woocommerce->cart ) {
            return $_POST['product_id'];
        }
        WC()->session->set( 'custom_price'. $_POST['product_id'], ($_POST['price']/100) );
        $cart_items = $woocommerce->cart->get_cart();
        $woo_cart_param = array(
            'product_id'     => $_POST['product_id'],
            'check_in_date'  =>'',
            'check_out_date' => '',
            'quantity'       => $_POST['quantity'],
            'service_list'   =>trim($_POST['service'], ",")
        );
        $woo_cart_id = $woocommerce->cart->generate_cart_id( $woo_cart_param['product_id'], null, array(), $woo_cart_param );
        if ( array_key_exists( $woo_cart_id, $cart_items ) ) {
            $woocommerce->cart->set_quantity( $woo_cart_id, $_POST['quantity'] );
        } else {
            $woocommerce->cart->add_to_cart( $woo_cart_param['product_id'], $woo_cart_param['quantity'], null, array(), $woo_cart_param );
        }
        $woocommerce->cart->calculate_totals();
        // Save cart to session
        $woocommerce->cart->set_session();
        // Maybe set cart cookies
        $woocommerce->cart->maybe_set_cart_cookies();
        echo 'success';
        wp_die();
    }
}

add_action('woocommerce_add_order_item_meta','themeservice_add_product_custom_field_to_order_item_meta', 9, 3 );

if (!function_exists('themeservice_add_product_custom_field_to_order_item_meta')) {
    function themeservice_add_product_custom_field_to_order_item_meta( $item_id, $item_values, $item_key ) {
        if( ! empty( $item_values['service_list'] ) )
            wc_update_order_item_meta( $item_id, 'ServiceList', sanitize_text_field( $item_values['service_list'] ) );
    }
}
//service cart add end


if (!function_exists('themename_remove_item_from_cart')) {

    function themename_remove_item_from_cart() {
        $cart = WC()->instance()->cart;
        $id = $_POST['product_id'];
        if(isset($_POST['cid']) && $_POST['cid']!=''){
            $cart_item_id = $cart->find_product_in_cart($_POST['cid']);
        }else{
            $cart_id = $cart->generate_cart_id($id);
            $cart_item_id = $cart->find_product_in_cart($cart_id);
        }
        $array = array();
        if ($cart_item_id) {
            $cart->set_quantity($cart_item_id, 0);
            WC_AJAX::get_refreshed_fragments();
        } else {
            $array['error'] = true;
            echo json_encode($array);
        }
        exit();
    }

}

//in js

$('body').on('click', '.service-cart', function () {
    var id = $(this).data('product_id');
    var quantity=1;
    var price = $(this).attr('data-price');
    var service = $(this).attr('data-service-title');
    if(price==0){
        return false;
    }
    
    $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: {
            action: 'service_add_to_cart', product_id: id,price:price,quantity:quantity,service:service},
        success: function (res) {
            if(res='success')
            $("body").trigger("wc_fragment_refresh")
        }
    });
})

$('body').on('change', '.service_check', function () {
    var target=$(this).data('target'),
        price=$(this).data('price'),
        title=$(this).data('title'),
        send_price=parseInt($('#'+target).attr('data-price')),
        send_title=$('#'+target).attr('data-service-title').replace(/(^,)|(,$)/g, "");
        price=price.replace('.', '');
        price=parseInt(price);
    if($(this).is(":checked")){
        var new_price=parseInt(send_price+price)
        var new_title=send_title+','+title
    }else{
        if(send_price>price){
            var new_price= parseInt(send_price - price)
        }else{
            var new_price=0
        }
        var new_title= send_title.replace(title, ''); 
    }
    $('#'+target).attr('data-price',new_price);
    $('#'+target).attr('data-service-title',new_title);
})

$('.header-cart').on('click', '.prd-sm-delete', function () {
    var id = $(this).data('product_id')
    var qty = $(this).data('qty')
    var cid = $(this).data('cid')
    $.ajax({
        type: "POST",
        url: ajax_object.ajax_url,
        data: {
            action: 'remove_item_from_cart', product_id: id,cid:cid},
        success: function (res) {
            if (res.fragments) {
                $("body").trigger("wc_fragment_refresh")
            }

        }
    });
})

//add button

<a href="javascript:void(0)" class="btn btn-invert service-cart" data-price="0" data-id="<?php echo $row->ID ?>" data-quantity="1" data-product_id="<?php echo $row->ID ?>" data-product_sku="" data-service-title="" id="target-btn-<?php echo $counter_1; ?>"><?php esc_html_e('ADD TO CART','themeservice-core'); ?><i class="icon-arrow-triangle"></i></a>

//cart page
/*if(isset($cart_item['service_list'])){
    echo sprintf(__('%s','themeservice'),'<div class="service-list-div"></div>Service: '.$cart_item['service_list'].'</div>');
}
//mini cart
 $cart_id = '';
if (isset($cart_item['service_list'])) {
    $cart_id = $cart_item['key'];
}

<div class="prd-sm-delete product-remove" data-cid="<?php echo esc_attr( $cart_id ) ?>" data-product_id="<?php echo esc_attr( $product_id ) ?>" data-product_sku="<?php echo esc_attr( $_product->get_sku() ) ?>" data-qty="<?php echo esc_attr($cart_item['quantity']) ?>">X
//review order page

<?php if(isset($cart_item['service_list'])){
    echo sprintf(__('%s','themeservice'),'<div class="service-list-div"></div>Service: '.$cart_item['service_list'].'</div>');
}*/
