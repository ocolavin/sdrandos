<?php
/*
Plugin Name: Site Plugin for sdrandos.com
Description: Site specific code changes for sdrandos.com
*/
/* Start Adding Functions Below this Line */

/* Global SDR variables, modify with care */

/*
 * Needed to make accurate time difference calculation
 * without this the time() function returns time in UTC time zone
*/

date_default_timezone_set('America/Los_Angeles');

/**************** SDRandos functions *******************/

function sdr_the_perm_shape_symbol ($perm_shape) {
		switch ($perm_shape) {
			case 'loop'            : echo ' &#8634'; break;
			case 'out-and-back'    : echo ' &#8644'; break;
			case 'point-to-point'  : echo ' &#8594'; break;
			default		       : echo ' ?';
		}
}

function sdr_fix_local_link ($link) {

	// if $link is relative (does not start with http), prepend local site url
	// this is to fix relative links imported with the old permanent data base

	if (empty($link) || preg_match('/^(\s)*http/', $link)) {
		return $link; 
	} else {
		return "http://www.sdrandos.com/$link";
	}
}

function sdr_the_href_link ($link, $link_text, $new_tab = TRUE) {
// creates a link only if $link is not empty
	if (!empty($link)) { 
		echo '<a href="'; echo esc_url($link);
		echo (!empty($new_tab) ? '" target="_blank">' : '">');
	}
	echo esc_html($link_text);
	if (!empty($link)) echo '</a>';
}

function sdr_the_mailto_link ($link, $subject, $link_text) {
// creates a link only if $link is not empty
	sdr_the_href_link("mailto:$link?subject=$subject", $link_text, true);
}

function sdr_print_list($list,$sep) {
// prints a list from a string containing items separated by $sep (usually a comma)
	$list_array = explode($sep,$list);
	if ($list_array) {
		echo '<div><ul>';
		foreach ($list_array as $name) echo "<li>$name</li>";
		echo '</ul></div>';
	}
}

function sdr_get_scrambled_string($sdr_string) {

	return antispambot($sdr_string);

}

function sdr_the_scrambled_string($sdr_string) {

	echo antispambot($sdr_string);

}

/* This function creates a SELECT item in a filter form for the brevets and permanents archive pages */

function sdr_the_select($sel_name, $filt_val, $sel_array) {
/* 
  $sel_name is a string used as the SELECT item name, it is also the name of the parameter sent by the associated GET/POST request, e.g. 'brev_dist'
  $filt_val is the value to be SELECTED in the drop down list
  $sel_array is an array $key => $val used to populate the drop down list, $val is the string displayed in the list and $key is the associated value
*/
	echo "<select class=\"sdr-filter\" name=\"$sel_name\">";
	foreach ($sel_array as $sel_key => $sel_val) {
//		if ($sel_key == 'any') {
//			echo "<option value=''>$sel_val</option>"; // this would usually be the first option in the drop-down list
//		} else {
			echo "<option value=\"$sel_key\"";
			if ($filt_val == $sel_key) echo ' selected';
			echo ">$sel_val</option>";
//		}
	}
	echo '</select>';
} /* end function sdr_the_select */


function sdr_the_brevet_table ($filt_type, $filt_year, $filt_month, $filt_dist) {

	$filt_type = strtoupper($filt_type);
	echo "<h1> $filt_type Sanctioned Rides </h1>";
	echo '<table>'; 
	/* Setting the class attribute to the <th> element is sufficient to generate constant width columns */
	echo '<thead><tr> <th class="sdr-brevet-date">Start Date & Time</th> <th class="sdr-brevet-title">Route Name</th> <th>Distance</th> <th>Type</th> </tr></thead>';
	echo '<tbody>';

	/* Start the ACP or RUSA Loop */
	while ( have_posts() ) : the_post();
		if ( strpos(get_field('brevet_type'), $filt_type) === 0) { /* works because brevet_type always starts with ACP or RUSA */ 

			$start_date = get_field('brevet_start_date');	// start date as stored in data base
			$start_date_ts = strtotime($start_date);	// convert to time stamp
			$start_month = strtolower(date("M", $start_date_ts)); 	// $month as a 3-letter lowercase symbol
			$start_year = date("Y", $start_date_ts); 	// $year as a 4-digit number 
			$brev_dist = get_field('brevet_distance');

			if ((empty($filt_year) || $filt_year == $start_year) &&	// filter by year unless $year==0 
			    (empty($filt_month) || $filt_month == $start_month) &&	// filter by month unless $month==0
			    // filter by distance unless $distance==0, return events between $distance and $distance+99km
			    (empty($filt_dist) || (($brev_dist >= $filt_dist) && ($brev_dist < $filt_dist+99)))) {	 
				echo '<tr>';
				/* Setting the class attribute to the <th> element is sufficient to generate constant width columns
				echo '<td class="sdr-brevet-date">'; the_field('brevet_start_date'); echo '</td>';   
				echo '<td class="sdr-brevet-title"> <a href="'; the_permalink(); echo'">'; the_title(); echo'</a></td>'; 
				*/
				echo '<td>'; the_field('brevet_start_date'); echo '</td>';   
				echo '<td> <a href="'; the_permalink(); echo'">'; the_title(); echo'</a></td>';
				echo '<td>'; the_field('brevet_distance'); echo 'km </td>'; 
				echo '<td>'; the_field('brevet_type'); echo'</td>';  
				echo '</tr>';
			}
		}
	endwhile;
	echo '</tbody></table>';
} /* end function sdr_the_brevet_table */

/**************** Register 'sdr_permanent' and 'sdr_brevet custom post types *******************/

add_action( 'init', 'sdrandos_init' );

// This is to invoke flush_rewrite_rules only when the plugin is activated 

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'sdrandos_flush_rewrites' );

function sdrandos_flush_rewrites() {
	// call your CPT registration function here (it should also be hooked into 'init')
	sdrandos_init();
	flush_rewrite_rules();
}

function sdrandos_init() {
	$labels = array(
		'name'               => 'Permanents', 
		'singular_name'      => 'Permanent',
		'menu_name'          => 'Permanents',
		'name_admin_bar'     => 'Permanent',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Permanent',
		'new_item'           => 'New Permanent',
		'edit_item'          => 'Edit Permanent',
		'view_item'          => 'View Permanent',
		'all_items'          => 'All Permanents',
		'search_items'       => 'Search Permanents',
		'parent_item_colon'  => 'Parent Permanents:',
		'not_found'          => 'No permanent found.',
		'not_found_in_trash' => 'No permanent found in Trash.'
	);

	$args = array(
		'labels'             => $labels,
                'description'        => 'Permanents of San Diego County',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 15,
		'supports'           => array( 'title', 'editor'),
		'taxonomies'         => array( 'category'),
        	'rewrite'            => array('slug' => 'permanents')
	);

	register_post_type( 'sdr_permanent', $args );

	$labels = array(
		'name'               => 'Brevets', 
		'singular_name'      => 'Brevet',
		'menu_name'          => 'Brevets',
		'name_admin_bar'     => 'Brevet',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Brevet',
		'new_item'           => 'New Brevet',
		'edit_item'          => 'Edit Brevet',
		'view_item'          => 'View Brevet',
		'all_items'          => 'All Brevets',
		'search_items'       => 'Search Brevets',
		'parent_item_colon'  => 'Parent Brevets:',
		'not_found'          => 'No brevet found.',
		'not_found_in_trash' => 'No brevet found in Trash.'
	);

	$args = array(
		'labels'             => $labels,
        	'description'        => 'Brevets of San Diego County',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 15,
		'supports'           => array( 'title', 'editor'),
		'taxonomies'         => array( 'category'),
        	'rewrite'            => array('slug' => 'brevets')
	);

	register_post_type( 'sdr_brevet', $args );
} // end function

// define('SDR_LOAD_PERMS', true); // set to false to disable permanents load function

if (defined('SDR_LOAD_PERMS')) {
/**
 * Show 'insert posts' button on backend
 */

/* uncomment to enable admin button to load permanents in data base */
add_action( "admin_notices", function() {
    echo "<div class='updated'>";
    echo "<p>";
    echo "To insert the posts into the database, click the button to the right.";
    echo "<a class='button button-primary' style='margin:0.25em 1em' href='{$_SERVER["REQUEST_URI"]}&insert_permanents'>Insert Permanents</a>";
    echo "</p>";
    echo "</div>";
});

function sdr_log_message($message) {
    error_log($message . PHP_EOL, 3, __DIR__ . '/sdr_php_error.log');   
}

/**
 * Create and insert posts from CSV files
 */

/* uncomment to enable admin button to load permanents in data base */
add_action( "admin_init", function() {

	global $wpdb;

	// I'd recommend replacing this with your own code to make sure
	//  the post creation _only_ happens when you want it to.
	if ( ! isset( $_GET["insert_permanents"] ) ) {
		return;
	}

	// Get the data from all those CSVs!
	$posts = function() {
		$data = array();
		$errors = array();

		// Get array of CSV files
		$files = array(__DIR__ . "/permanents.csv" );

		foreach ( $files as $file ) {

			// Attempt to change permissions if not readable
			if ( ! is_readable( $file ) ) {
				chmod( $file, 0744 );
			}

			// Check if file is writable, then open it in 'read only' mode
			if ( is_readable( $file ) && $_file = fopen( $file, "r" ) ) {

				// To sum this part up, all it really does is go row by
				//  row, column by column, saving all the data
				$post = array();

				// Get first row in CSV, which is of course the headers
		    	        $header = fgetcsv( $_file );

		                while ( $row = fgetcsv( $_file ) ) {

		                    foreach ( $header as $i => $key ) {
	                                    $post[$key] = $row[$i];
	                            }

	                            $data[] = $post;
		                }

				fclose( $_file );

			} else {
				$errors[] = "File '$file' could not be opened. Check the file's permissions to make sure it's readable by your server.";
			}
		}

		if ( ! empty( $errors ) ) {
			// ... do stuff with the errors
		}

		return $data;
	};

	foreach ( $posts() as $post ) {

		// If the post exists, this is an update
		if ( ($existing_post = get_page_by_title($post['permanent_title'],OBJECT,'sdr_permanent')) ) {
           	   $post["id"] = $existing_post->ID;
		   sdr_log_message( "Modifying existing permanent {$post['permanent_title']}" );
		} else { // post does not exist. Insert the post into the database
		   sdr_log_message( "Create new permanent " . $post['permanent_title'] );
		   $post["id"] = wp_insert_post( array(
			 "post_title"   => $post["permanent_title"],
			 "post_type"    => "sdr_permanent",
			 "post_status"  => "publish"
		   ));
        	}

		// Update post's custom fields

		update_field( 'permanent_distance',       $post["permanent_distance"],       $post["id"] );
		update_field( 'permanent_elevation',      $post["permanent_elevation"],      $post["id"] );
		update_field( 'permanent_number',         $post["permanent_number"],         $post["id"] );
		update_field( 'permanent_owner',          $post["permanent_owner"],          $post["id"] );
		update_field( 'permanent_owners_email',   $post["permanent_owners_email"],   $post["id"] );
		update_field( 'permanent_cue_sheet',      $post["permanent_cue_sheet"],      $post["id"] );
		update_field( 'permanent_map',            $post["permanent_map"],            $post["id"] );
		update_field( 'permanent_embedded_map',   $post["permanent_embedded_map"],   $post["id"] );
		update_field( 'permanent_video',          $post["permanent_video"],          $post["id"] );
		update_field( 'permanent_start_location', $post["permanent_start_location"], $post["id"] );
		
//		sdr_log_message( "{$post['permanent_title']} {$post['permanent_cue_sheet']}" );
	}
});
} // if defined

/* Stop Adding Functions Below this Line */
?>
