<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<main id="main" class="site-main" role="main">

	<?php // parse parameters

	$filt_dist = get_query_var('perm_dist');
	$filt_elev = get_query_var('perm_elev');
	$filt_owner = get_query_var('perm_owner');
	$filt_start = get_query_var('perm_start');
	$filt_loop = get_query_var('perm_loop');
	?>

	<?php // display form to filter permanent list

	echo '<form action="'; echo esc_url( admin_url('admin-post.php') ); echo '" method="get">';
	echo '<input type="hidden" name="action" value="get_permanent_archive">';
	echo 'permanents / ';
	sdr_the_select('perm_dist', $filt_dist, $SDR_DISTANCE_ARRAY);
	echo ' / ';
	sdr_the_select('perm_elev', $filt_elev, $SDR_ELEVATION_ARRAY);
	echo ' / ';
	sdr_the_select('perm_owner', $filt_owner, $SDR_OWNER_ARRAY);
	// echo ' > ';
	// sdr_the_select('perm_start', $filt_start, $SDR_LOCATION_ARRAY);
	echo ' / ';
	sdr_the_select('perm_loop', $filt_loop, $SDR_PERM_SHAPE_ARRAY);
	echo ' / ';
	echo '<input class="sdr-filter" type="submit" value="Filter">'; 
	echo '</form>';
	?>

	<h1>San Diego County Permanents</h1>

	<?php // display the permanents that match the filter in a table
	if ( have_posts() ) : ?>
		&#8634  Loop  &#9474  &#8644  Out-and-Back  &#9474  &#8594  Point-to-Point
		<table>
		<thead><tr><th>Distance</th><th>Elevation</th><th>Route Name/Shape</th><th>Owner</th><th>Start Location</th><!--<th>Shape</th>--><th>Fee</th></tr></thead>
		<tbody>
		<?php
		$filt_dist += 0; // casts string '1000km' to int 1000, php style!
		$filt_elev += 0; // casts string '1000ft' to int 1000, php style!
		($filt_owner == 'any') ? $filt_owner = '' : $filt_owner = $SDR_OWNER_ARRAY[$filt_owner];  // translates 'FnameLname' to 'Fname Lname'

		switch ($filt_loop) {
			case 'any'    : $filt_loop = ''; break;
			case 'loop'   : $filt_loop = 'loop'; break;
			case 'oneway' : $filt_loop = 'point-to-point'; break;
		}

		/* Start the Loop */
		while ( have_posts() ) : the_post();
			$perm_dist  = get_field('permanent_distance');
			$perm_elev  = get_field('permanent_elevation');
			$perm_title = get_the_title();
			$perm_link  = get_permalink();
			$perm_owner = get_field('permanent_owner');
			$perm_email = get_field('permanent_owners_email');
			$perm_start = get_field('permanent_start_location');
			$perm_shape = get_field('permanent_shape');
			$perm_cost  = get_field('permanent_cost');

			if ((empty($filt_dist)  || ($perm_dist >= $filt_dist && $perm_dist < $filt_dist + 99)) &&
			    (empty($filt_elev)  || ($perm_elev <= $filt_elev)) &&
			    (empty($filt_owner) || ($perm_owner == $filt_owner)) &&
			    (empty($filt_loop)  || ($perm_shape == $filt_loop) || ($filt_loop == 'loop' && $perm_shape == 'out-and-back'))) {
				echo '<tr>';
				echo "<td>$perm_dist km</td>"; 
				echo "<td>$perm_elev ft</td>"; 
				echo "<td>"; sdr_the_href_link($perm_link, $perm_title); 
				sdr_the_perm_shape_symbol($perm_shape);
				echo '</td>';
				echo '<td>'; sdr_the_mailto_link($perm_email,$perm_title,$perm_owner); echo '</td>'; 
				echo "<td>$perm_start</td>"; 
				// echo "<td>$perm_shape</td>"; 
				echo "<td>\$$perm_cost</td>"; 
				echo '</tr>';
			}
		endwhile;
	?>
	</tbody></table>
	<?php
	else :

		get_template_part( 'template-parts/post/content', 'none' );

	endif; 
	?>

	</main><!-- #main -->
</div><!-- .wrap -->

<?php get_footer();
