<?php
/**
 * The template for displaying SDR permanent single posts
 *
 */

get_header(); ?>

<div class="wrap">
	<main id="main" class="site-main" role="main">

	<?php // print the permanent name with useful info
	if ( have_posts() ) : // required for compatibility with plugins that hook into have_posts
		the_post(); // without this the_content() does not work, although it does not bother the_title() the_field()
		$perm_number = get_field('permanent_number');
		echo '<h1>RUSA #'; 
		sdr_the_href_link("http://www.rusa.org/cgi-bin/permview_GF.pl?permid=$perm_number", $perm_number); echo ' - '; 
		the_title(); echo ' - '; 
		the_field('permanent_distance'); echo 'km - ';
		the_field('permanent_elevation'); echo 'ft - ';
		sdr_the_perm_shape_symbol(get_field('permanent_shape'));
		echo '</h1>';
	?>

	<?php // print the rest of the permanent info in a table
		echo '<table><tr><td>';
		echo 'Starts in '; the_field('permanent_start_location'); 
		if (get_field('permanent_zip_code')) { echo ', '; the_field('permanent_zip_code'); }
		echo '. ';
		
		if (get_field('permanent_cost') == 0) {
			echo 'No fee.';
		} else {
			echo 'Fee is $'; the_field('permanent_cost'); echo '.';
		}
		echo '</td></tr>';
		echo '<tr><td>Links to '; 
		sdr_the_href_link(sdr_fix_local_link(get_field('permanent_cue_sheet')),'cue sheet');
		echo ', ';
		sdr_the_href_link(sdr_fix_local_link(get_field('permanent_map')),'map');
		echo ', ';
		sdr_the_href_link(sdr_fix_local_link(get_field('permanent_brevet_card')),'brevet card');
		echo '</td></tr><tr><td>';
		echo 'Contact ';
		sdr_the_mailto_link(get_field('permanent_owners_email'), get_the_title(), get_field('permanent_owner'));
		echo ' to ride this permanent.</td></tr></table>';
	?>
	
	<?php // print the permanent description
		the_content(); 
	?>

	<?php // display the embedded map and embedded video if any
		the_field('permanent_embedded_map');
		the_field('permanent_video');
	else :
		get_template_part( 'template-parts/post/content', 'none' );
	endif;	?>

	</main><!-- #main -->
</div><!-- .wrap -->

<?php get_footer();
