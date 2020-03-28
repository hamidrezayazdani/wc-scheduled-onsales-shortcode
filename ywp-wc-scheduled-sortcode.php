<?php

/*
Plugin Name: Woocommerce scheduled on-sale products list shortcode
Plugin URI:  github
Description: This plugin able you to use [ywp_schedule_product] shortcode to show list of scheduled products in woocommerce
Version:     1.0
Author:      Hamid Reza Yazdani (yazdaniwp)
Author URI:  https://www.linkedin.com/in/hamid-reza-yazdani/
License:     GPL2
*/
defined( 'ABSPATH' ) || exit;

class YWP_Schedule_Products_SC {

	private $_data = array();

	public function __construct() {
		add_shortcode( 'ywp_schedule_product', array( $this, 'ywp_schedule_product_availability_shortcode' ) );
	}
	
	function ywp_schedule_product_availability_shortcode( $atts ) {
		$time = time();

		extract( shortcode_atts( array(
			'limit' 	=> '',
			'columns'	=>'' ), $atts)
		);

		$columns = ! empty( $columns ) ? $columns : 3;
		$limit = ! empty( $limit ) ? $limit : 12;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$args = array(
			'post_type'      	=> array( 'product', 'product_variation' ),
			'post__in'			=> array_merge( array( 0 ), wc_get_product_ids_on_sale() ),
			'paged'				=> $paged,
			'posts_per_page'	=> $limit,
			'meta_query' => array(
				/*'relation' => 'AND', // If you set on sale start date uncomment this section
				array(
					'key'     => '_sale_price_dates_from',
					'value'   =>   $time,
					'compare' => '<',		
				),*/
				array(
					'key'     => '_sale_price_dates_to',
					'value'   =>   $time,
					'compare' => '>',		
				),
			),
		);

		$loop = new WP_Query( $args );

		if ( $loop->have_posts() ) {
		?>
			<ul class ="products columns-<?php echo $columns; ?>">
			<?php
				while ( $loop->have_posts() ) : $loop->the_post();
					woocommerce_get_template_part( 'content', 'product' );
				endwhile;
			?>
			</ul>
		<?php
			if ( function_exists( 'pagination' ) )
				pagination( $loop->max_num_pages );
		}
		else
			echo __( 'No products found' );

		wp_reset_postdata();
	}

}

new YWP_Schedule_Products_SC();
?>