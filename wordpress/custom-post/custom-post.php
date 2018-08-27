<?php

//Register Custom Post Type
function function_post_type() {

    $labels = array(
        'name' => _x('Post Name', 'Post Type General Name', 'text-domain'),
        'singular_name' => _x('Post Name', 'Post Type Singular Name', 'text-domain'),
        'menu_name' => esc_html__('Post Name', 'text-domain'),
        'name_admin_bar' => esc_html__('Post Name', 'text-domain'),
        'archives' => esc_html__('Item Archives', 'text-domain'),
        'parent_item_colon' => esc_html__('Parent Item:', 'text-domain'),
        'all_items' => esc_html__('All Post Name', 'text-domain'),
        'add_new_item' => esc_html__('Add New Post Name', 'text-domain'),
        'add_new' => esc_html__('Add New Post Name', 'text-domain'),
        'new_item' => esc_html__('New Post Name Item', 'text-domain'),
        'edit_item' => esc_html__('Edit Post Name Item', 'text-domain'),
        'update_item' => esc_html__('Update Post Name Item', 'text-domain'),
        'view_item' => esc_html__('View Post Name Item', 'text-domain'),
        'search_items' => esc_html__('Search Item', 'text-domain'),
        'not_found' => esc_html__('Not found', 'text-domain'),
        'not_found_in_trash' => esc_html__('Not found in Trash', 'text-domain'),
        'featured_image' => esc_html__('Featured Image', 'text-domain'),
        'set_featured_image' => esc_html__('Set featured image', 'text-domain'),
        'remove_featured_image' => esc_html__('Remove featured image', 'text-domain'),
        'use_featured_image' => esc_html__('Use as featured image', 'text-domain'),
        'insert_into_item' => esc_html__('Insert into item', 'text-domain'),
        'uploaded_to_this_item' => esc_html__('Uploaded to this item', 'text-domain'),
        'items_list' => esc_html__('Items list', 'text-domain'),
        'items_list_navigation' => esc_html__('Items list navigation', 'text-domain'),
        'filter_items_list' => esc_html__('Filter items list', 'text-domain'),
    );
	$slug_postype = 'Post-Names';
    $args = array(
        'labels' => $labels,
        'description' => esc_html__('Description.', 'text-domain'),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => apply_filters('theme_name_postype_slug',$slug_postype) ),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => true,
        'supports'           => array(
					'title',
					'editor',
					'thumbnail',
					'revisions',
					'comments',
					'author',
					'custom-fields'
				),
    );
    register_post_type('Post_Names', $args);
}

add_action('init', 'function_post_type', 0);

function add_custom_taxonomies() {
    register_taxonomy('Post_Names_types', 'Post_Names', array(
        'hierarchical' => true,
        'labels' => array(
            'name' => _x('Post Name Types', 'taxonomy general name', 'text-domain'),
            'singular_name' => _x('Post Name Type', 'taxonomy singular name', 'text-domain'),
            'search_items' => esc_html__('Search Post Name Type', 'text-domain'),
            'all_items' => esc_html__('All Post Name Type', 'text-domain'),
            'parent_item' => esc_html__('Parent Post Name Type', 'text-domain'),
            'parent_item_colon' => esc_html__('Parent Post Name Type:', 'text-domain'),
            'edit_item' => esc_html__('Edit Post Name Type', 'text-domain'),
            'update_item' => esc_html__('Update Post Name Type', 'text-domain'),
            'add_new_item' => esc_html__('Add Post Name New Type', 'text-domain'),
            'new_item_name' => esc_html__('New Post Name Type Name', 'text-domain'),
            'menu_name' => esc_html__('Post Name Types'),
        ),
      
        'rewrite' => array(
            'slug' => 'Post_Names_types', // This controls the base slug that will display before each term
            'with_front' => false, // Don't display the category base before "/locations/"
            'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        ),
    ));
}

add_action('init', 'add_custom_taxonomies', 0);
