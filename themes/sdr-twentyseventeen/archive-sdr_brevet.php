<?php
/**
 * The template for displaying archive brevet pages
 */

get_header(); ?>

<div class="wrap">
	<main id="main" class="site-main" role="main">

	<?php // parse parameters

	$filt_year  = get_query_var('brev_year');
	$filt_month = get_query_var('brev_month');
	$filt_dist  = get_query_var('brev_dist');
	$filt_type  = get_query_var('brev_type');

	// convert month to 3-letter lowercase acronym
	if (!empty($filt_month)) $filt_month = strtolower(date('M', strtotime($filt_month)));
	// convert type to lowercase
	$filt_type = strtolower($filt_type);
	?>

	<?php // display form to filter permanent list

	echo '<form action="'; echo esc_url( admin_url('admin-post.php') ); echo '" method="get">';
	echo '<input type="hidden" name="action" value="get_brevet_archive">';
	echo 'brevets / ';
	sdr_the_select('brev_year', $filt_year, $SDR_YEAR_ARRAY);
	echo ' / ';
	sdr_the_select('brev_month', $filt_month, $SDR_MONTH_ARRAY);
	echo ' / ';
	sdr_the_select('brev_dist', $filt_dist, $SDR_DISTANCE_ARRAY);
	echo ' / ';
	sdr_the_select('brev_type', $filt_type, $SDR_BREVETTYPE_ARRAY);
	echo ' / ';
	echo '<input class="sdr-filter" type="submit" value="Filter">'; 
	echo '</form>';
	?>

	<?php // Display the brevets that match the filter in a table

	$filt_dist += 0; 					// cast string '1000km' to int 1000, php style!

	switch ($filt_type) {
		case 'acp':
			$acp_type = true; $rusa_type= false; break;
		case 'rusa':
			$acp_type = false; $rusa_type =true; break;
		default:
			$acp_type = $rusa_type = true;
	}

	if ( have_posts() ) : 
		if ($acp_type) {
			sdr_the_brevet_table('acp', $filt_year, $filt_month, $filt_dist); 
			rewind_posts();
		}	
		if ($rusa_type) {
			sdr_the_brevet_table('rusa', $filt_year, $filt_month, $filt_dist); 
		}
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
