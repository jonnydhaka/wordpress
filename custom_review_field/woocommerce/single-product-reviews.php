<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! comments_open() ) {
	return;
}

?>
<div class="tt-tabs-product__review tt-review">
<div id="reviews" class="woocommerce-Reviews">

		<div class="tt-tabs__content-head"><?php
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_review_count() ) ) {
				/* translators: 1: reviews count 2: product name */
				printf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ), esc_html( $count ), '<span>' . get_the_title() . '</span>' );
			} else {
				_e( 'Reviews', 'woocommerce' );
			}
		?>
		</div>	
	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div class="tt-review__form">
			<div id="review_form_wrapper">
				<div id="review_form">

				<?php
					$commenter = wp_get_current_commenter();

					$comment_form = array(
						'title_reply'          => have_comments() ? __( 'Write a review', 'woocommerce' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
						'title_reply_to'       => __( 'Leave a Reply to %s', 'woocommerce' ),
						'title_reply_before'   => '<span id="reply-title">',
						'title_reply_after'    => '</span>',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<div class="row ttg-mt--20">' . '<div class="col-sm-4"><label for="author">' . esc_html__( 'Name', 'woocommerce' ) . '</label></div> ' .
										'<div class="col-sm-8"><input id="author" class="form-control" name="author" type="text" placeholder="Enter your name" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" required /></div></div>',
							'email'  => '<div class="row ttg-mt--20">' . '<div class="col-sm-4"><label for="email">' . esc_html__( 'E-mail:', 'woocommerce' ) . ' </label> </div>' .
										'<div class="col-sm-8"><input id="email" class="form-control" name="email" type="email" placeholder="John.smith@example.com" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" required /></div></div>',
							'review_title'  => '<div class="row ttg-mt--20">' . '<div class="col-sm-4"><label for="review_title">' . esc_html__( 'Review Title::', 'woocommerce' ) . ' </label> </div>' .
										'<div class="col-sm-8"><input id="review_title" class="form-control" name="review_title" type="text" placeholder="Give your review a title" value="" size="30" aria-required="true" required /></div></div>',
						),
						'label_submit'  => __( 'Submit Review', 'woocommerce' ),
						'submit_button'     =>'<div class="row ttg-mt--20"><div class="col-sm-8 offset-sm-4"><button type="submit" name="%1$s" id="%2$s" class="%3$s btn">%4$s</button></div></div>',
						'logged_in_as'  => '',
						'comment_field' => '',
					);

					if ( $account_page_url = wc_get_page_permalink( 'myaccount' ) ) {
						$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'woocommerce' ), esc_url( $account_page_url ) ) . '</p>';
					}

					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$comment_form['comment_field'] = '<div class="row ttg-mt--20"><div class="col-sm-4"><label for="rating">' . esc_html__( 'E-Rating:', 'woocommerce' ) . '</label></div><div class="col-sm-8"><select name="rating" id="rating" aria-required="true" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
						</select></div></div>';
					}

					$comment_form['comment_field'] .= '<div class="row ttg-mt--20"><div class="col-sm-4"><label for="comment">' . esc_html__( 'Body of Review (1500):', 'woocommerce' ) . ' </label></div><div class="col-sm-8"><textarea id="comment" class="form-control" name="comment" placeholder="Write your comments" here cols="45" rows="8" aria-required="true" required></textarea></div></div>';

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
				</div>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php _e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>

	<?php endif; ?>


	<div id="comments">

		<?php if ( have_comments() ) : ?>

			<ol class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php _e( 'There are no reviews yet.', 'woocommerce' ); ?></p>

		<?php endif; ?>
	</div>


	<div class="clear"></div>
</div>
</div>