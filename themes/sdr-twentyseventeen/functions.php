<?php
/* SDRANDOS specific additions */

$SDR_RBA 			= 'David Danovsky';
$SDR_RBA_EMAIL			= 'ddanovsky@gmail.com';			// address used to send emails to the RBA
$SDR_PAYPAL_EMAIL 		= 'ddanovsky@gmail.com';			// address used for paypal registration payments
$SDR_PAYPAL_SANDBOX_TESTING 	= true; 					// change to false to go live
$SDR_PAYPAL_SANDBOX_EMAIL 	= 'osvaldo.colavin-facilitator@gmail.com';	// email used for paypal sandbox testing

$SDR_DISTANCE_ARRAY = [	'any'         => 'Any distance',
			'100km'       => '100 km',
			'200km'       => '200 km',
			'300km'       => '300 km',
			'400km'       => '400 km',
			'500km'       => '500 km',
			'600km'       => '600 km',
			'700km'       => '700 km',
			'1000km'      => '1000 km',
			'1200km'      => '1200 km'];

$SDR_ELEVATION_ARRAY =  ['any'         => 'Any elevation',
		 	 '1000ft'      => '1,000 ft',
			 '2500ft'      => '2,500 ft',
			 '5000ft'      => '5,000 ft',
  		 	 '10000ft'     => '10,000 ft',
  			 '15000ft'     => '15,000 ft',
  			 '20000ft'     => '20,000 ft'];

$SDR_LOCATION_ARRAY =   ['any'         => 'Any start location',
			 'downtown'    => 'Downtown',
			 'coastal'     => 'Coastal',
			 'southbay'    => 'South Bay',
			 'northcounty' => 'North County',
			 'inland'      => 'Inland',
			 'mountains'   => 'Mountains'];

$SDR_OWNER_ARRAY     =  ['any'             => 'Any owner',
			 'SandyAniya'      => 'Sandy Aniya',
			 'OsvaldoColavin'  => 'Osvaldo Colavin',
			 'DavidDanovsky'   => 'David Danovsky',
			 'KellyDeBoer'     => 'Kelly DeBoer',
			 'KevinFoust'      => 'Kevin Foust',
			 'HectorMaytorena' => 'Hector Maytorena',
			 'JohnMestemacher' => 'John Mestemacher',
			 'LisaNicholson'   => 'Lisa Nicholson',
			 'DaveNicolai'     => 'Dave Nicolai',
			 'GregOlmstead'    => 'Greg Olmstead',
			 'ThomasReynolds'  => 'Thomas Reynolds'];

$SDR_MONTH_ARRAY = ['any'  => 'Any month',
		    'jan'  => 'January',
		    'feb'  => 'February',
		    'mar'  => 'March',
		    'apr'  => 'April',
		    'may'  => 'May',
		    'jun'  => 'June',
		    'jul'  => 'July',
		    'aug'  => 'August',
		    'sep'  => 'September',
		    'oct'  => 'October',
		    'nov'  => 'November',
		    'dec'  => 'December'];

// Array of years from 2017 to current year + 1
$SDR_YEAR_ARRAY = ['any'  => 'Any year'];
foreach (range(2017, date('Y')+1) as $yy) { $SDR_YEAR_ARRAY[$yy] = $yy; }

$SDR_BREVETTYPE_ARRAY = ['any'  => 'Any type',
			 'acp'  => 'ACP',
			 'rusa' => 'RUSA'];

$SDR_PERM_SHAPE_ARRAY = ['any'  => 'Any shape',
			 'loop'  => 'Loop',
			 'oneway'   => 'One Way'];

/* This is to sort permanents by distance and brevets by date for archive pages */
function sdr_pre_get_posts( $query ) {
	
	// do not modify queries in the admin
	if( is_admin() ) { return $query; }

	// only modify queries for 'sdr_permanent' or 'sdr_brevet' post type
	if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'sdr_permanent' ) {
		
		$query->set('orderby', 'meta_value');	
		$query->set('meta_key', 'permanent_distance');	// sort by distance 
		$query->set('order', 'ASC'); 
		$query->set('posts_per_page', -1); // no page limit
	}
	if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'sdr_brevet' ) {
		
		$query->set('orderby', 'meta_value');	
		$query->set('meta_key', 'brevet_start_date');	// sort by event date 
		$query->set('order', 'ASC'); 
		$query->set('posts_per_page', -1); // no page limit
	}

	// return
	return $query;

}

add_action('pre_get_posts', 'sdr_pre_get_posts');

/* This is to have a pretty permalink structure like brevets/2018/may or permanents/200km/92122 */

function sdr_array_concat_values($sdr_array, $concat_sep) {
	$concat_str = $sep = '';
	foreach ($sdr_array as $value => $x) {
		if ($value != 'any') { $concat_str .= "$sep$value" ; $sep = $concat_sep; }
	}
	return $concat_str;
}

function sdr_get_re_list () {

	$brev_year_re  = '20[0-9]{2}'; // good for 2000-2099

	$brev_month_re = 'jan(?:uary)?|feb(?:ruary)?|mar(?:ch)?|apr(?:il)?|may|jun(?:e)?|jul(?:y)?|aug(?:ust)?|sep(?:tember)?|oct(?:ober)?|nov(?:ember)?|dec(?:ember)?';

	global $SDR_BREVETTYPE_ARRAY;
	$perm_owner_re = sdr_array_concat_values($SDR_BREVETTYPE_ARRAY,'|');

	/* without the ft and km ending, pattern matching mixes up distance and elevation */
	$brev_dist_re  = '[1-9][0-9]+km';
	$perm_elev_re  = '[1-9][0-9]+ft';
	$perm_start_re = '919[0-9]{2}|92[0-1][0-9]{2}'; // zip codes in the form 919xx or 920xx or 921xx 
/*
	$perm_owner_re = 'LisaNicholson|JohnMestemacher|KellyDeBoer|DavidDanovsky|OsvaldoColavin|HectorMaytorena|SandyAniya|KevinFoust|DaveNicolai|ThomasReynolds|GregOlmstead'; 
*/
	global $SDR_OWNER_ARRAY;
	$perm_owner_re = sdr_array_concat_values($SDR_OWNER_ARRAY,'|');

	global $SDR_PERM_SHAPE_ARRAY;
	$perm_loop_re = sdr_array_concat_values($SDR_PERM_SHAPE_ARRAY,'|');
	
	return array($brev_year_re, $brev_month_re, $brev_type_re, $brev_dist_re, 
			$perm_elev_re, $perm_start_re, $perm_owner_re, $perm_loop_re);
}

function sdr_rewrite_tags () {

	// list($brev_yy_re, $brev_mm_re, $brev_type_re, $brev_dist_re, $perm_elev_re, $perm_start_re, $perm_owner_re) = sdr_get_re_list();

	add_rewrite_tag('%brev_year%',  "([^&]+)"); // 'yy' (year) tag for brevets
	add_rewrite_tag('%brev_month%', "([^&]+)"); // 'mm' (month) tag for brevets
	add_rewrite_tag('%brev_dist%',  "([^&]+)"); // 'dist' (distance) tag for brevets
	add_rewrite_tag('%brev_type%',  "([^&]+)"); // 'type' (ride type, ACP or RUSA) tag for brevets
	add_rewrite_tag('%perm_dist%',  "([^&]+)"); // 'dist' (distance) tag for permanents
	add_rewrite_tag('%perm_elev%',  "([^&]+)"); // 'elev' (elevation) tag for permanents
	add_rewrite_tag('%perm_start%', "([^&]+)"); // 'start' (zip code) tag for permanents
	add_rewrite_tag('%perm_owner%', "([^&]+)"); // 'owner' (owner name) tag for permanents
	add_rewrite_tag('%perm_loop%', "([^&]+)");  // 'loop' tag for permanents
}

add_action('init', 'sdr_rewrite_tags');

function sdr_rewrite_rules () {

	list($brev_year_re, $brev_month_re, $brev_type_re, $brev_dist_re, $perm_elev_re, $perm_start_re, $perm_owner_re, $perm_loop_re) = sdr_get_re_list();

	// rewrite rules for brevets, 4 rules required to match any combination of year/month/distance/type

	// matches brevets/<year>/<month>?/<distance>?/<type>?
	add_rewrite_rule("^(?i)brevets/($brev_year_re)/?($brev_month_re)?/?($brev_dist_re)?/?($brev_type_re)?/?",
			 'index.php?post_type=sdr_brevet&brev_year=$matches[1]&brev_month=$matches[2]&brev_dist=$matches[3]&brev_type=$matches[4]','top');

	// matches brevets/<month>/<distance>?/<type>?
	add_rewrite_rule("^(?i)brevets/($brev_month_re)/?($brev_dist_re)?/?($brev_type_re)?/?",
			 'index.php?post_type=sdr_brevet&brev_year=&brev_month=$matches[1]&brev_dist=$matches[2]&brev_type=$matches[3]','top');

	// matches brevets/<distance>/<type>?
	add_rewrite_rule("^(?i)brevets/($brev_dist_re)/?($brev_type_re)?/?",
			 'index.php?post_type=sdr_brevet&brev_year=&brev_month=&brev_dist=$matches[1]&brev_type=$matches[2]','top');

	// matches brevets/<type>
	add_rewrite_rule("^(?i)brevets/($brev_type_re)/?",
			 'index.php?post_type=sdr_brevet&brev_year=&brev_month=&brev_dist=&brev_type=$matches[1]','top');

	/*
	* rewrite rules for permanents, 5 rules required to match any combination of distance/elevation/start/owner
	*/
	
	// matches permanents/<distance>/<elevation>?/<start>?/<owner>?/<loop>?
	add_rewrite_rule("^(?i)permanents/($brev_dist_re)(?:/($perm_elev_re))?(?:/($perm_start_re))?(?:/($perm_owner_re))?(?:/($perm_loop_re))?/?$",
			 'index.php?post_type=sdr_permanent&perm_dist=$matches[1]&perm_elev=$matches[2]&perm_start=$matches[3]&perm_owner=$matches[4]&perm_loop=$matches[5]','top');

	// matches permanents/<elevation>/<start>?/<owner>?/<loop>?
	add_rewrite_rule("^(?i)permanents/($perm_elev_re)(?:/($perm_start_re))?(?:/($perm_owner_re))?(?:/($perm_loop_re))?/?$",
			 'index.php?post_type=sdr_permanent&perm_dist=&perm_elev=$matches[1]&perm_start=$matches[2]&perm_owner=$matches[3]&perm_loop=$matches[4]','top');

	// matches permanents/<start>/<owner>?/<loop>?
	add_rewrite_rule("^(?i)permanents/($perm_start_re)(?:/($perm_owner_re))?(?:/($perm_loop_re))?/?$",
			 'index.php?post_type=sdr_permanent&perm_dist=&perm_elev=&perm_start=$matches[1]&perm_owner=$matches[2]&perm_loop=$matches[3]','top');

	// matches permanents/<owner>/<loop>?
	add_rewrite_rule("^(?i)permanents/($perm_owner_re)(?:/($perm_loop_re))?/?$",
			 'index.php?post_type=sdr_permanent&perm_dist=&perm_elev=&perm_start=&perm_owner=$matches[1]&perm_loop=$matches[2]','top');

	// matches permanents/<loop>
	add_rewrite_rule("^(?i)permanents/($perm_loop_re)/?$",
			 'index.php?post_type=sdr_permanent&perm_dist=&perm_elev=&perm_start=&perm_owner=&perm_loop=$matches[1]','top');

}

add_action('init', 'sdr_rewrite_rules');

function sdr_get_brevet_archive() {

	// redirect to permalink

	$url = site_url('/brevets/');
	$param = $_GET['brev_year'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['brev_month'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['brev_dist'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['brev_type'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';

	wp_redirect( $url );
	exit;
}

add_action( 'admin_post_nopriv_get_brevet_archive', 'sdr_get_brevet_archive' );
add_action( 'admin_post_get_brevet_archive', 'sdr_get_brevet_archive' );

function sdr_get_permanent_archive() {

	// redirect to permalink

	$url = site_url('/permanents/');
	$param = $_GET['perm_dist'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['perm_elev'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['perm_start'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['perm_owner'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';
	$param = $_GET['perm_loop'];
	if ($param && !preg_match('/any$/', $param))  $url = $url . $param . '/';

	wp_redirect( $url );
	exit;
}

add_action( 'admin_post_nopriv_get_permanent_archive', 'sdr_get_permanent_archive' );
add_action( 'admin_post_get_permanent_archive', 'sdr_get_permanent_archive' );
?>
