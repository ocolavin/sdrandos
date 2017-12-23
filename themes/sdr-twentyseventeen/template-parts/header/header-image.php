<?php
/**
 * Displays header media
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<div class="custom-header">

		<div class="custom-header-media">
			<?php 
			the_custom_header_markup(); 
			// if (is_front_page()): echo do_shortcode("[metaslider id=359]"); else: the_custom_header_markup(); endif; 
			?>
		</div>

	<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>

</div><!-- .custom-header -->
