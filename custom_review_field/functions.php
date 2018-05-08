<?php
add_action( 'comment_post', 'theme_name_comment_meta_data' );

function theme_name_comment_meta_data( $comment_id ) {

 if(isset( $_POST[ 'review_title' ])){
  add_comment_meta( $comment_id, 'review_title', $_POST[ 'review_title' ] );
 }
    
}