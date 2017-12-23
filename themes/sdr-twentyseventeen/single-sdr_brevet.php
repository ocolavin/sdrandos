<?php
/**
 * The template for displaying SDR brevet single posts
 *
 */

get_header(); ?>

<div class="wrap">
	<main id="main" class="site-main" role="main">

	<?php // print the brevet name with useful info
	if ( have_posts() ) : // required for compatibility with plugins that hook into have_posts
		the_post(); // without this the_content() does not work, although it does not bother the_title() the_field()
		$event_name = get_the_title();
		$event_distance = get_field('brevet_distance');
		$event_type = get_field('brevet_type') . ' brevet'; // need to fix for fleche and dart
		echo "<h1>$event_name - $event_distance km $event_type</h1>";
	?>

	<?php // print the rest of the brevet info in a table
		$start_time = get_field('brevet_start_date');
		$duration =  get_field('brevet_time_limit');
		$org_email = get_field('brevet_organizer_email');
		$event_cost = get_field('brevet_cost');

		echo '<table><tr>';
			echo "<th>Start time</th><td>$start_time</td>";
		echo '</tr><tr>';
			echo "<th>Time limit</th><td>$duration</td>"; 
		echo '</tr><tr>'; // display ride cut-off time
			echo '<th>Ride ends</th><td>'; echo date('l F j, Y g:i a',strtotime("$start_time + $duration")); echo '</td>';
		echo '</tr><tr>'; //display start location
			echo '<th>Start location</th><td>'; 
			echo wp_kses_post(get_field('brevet_start_location')); 
			echo '</td>'; 
		echo '</tr><tr>'; // display location of nearest parking
			echo '<th>Nearest parking</th><td>'; the_field('brevet_start_parking'); echo '</td>'; 
		echo '</tr><tr>'; // display links to important documents
			echo '<th>Links</th><td>'; 
			sdr_the_href_link(get_field('brevet_cue_sheet_pdf'),'PDF cue sheet');
			echo ', ';
			sdr_the_href_link(get_field('brevet_cue_sheet_xls'),'XLS cue sheet');
			echo ', ';
			sdr_the_href_link(get_field('brevet_waiver_form'),'waiver'); echo '</td>';
		echo '</tr><tr>'; // display organizer's name and email link
			echo '<th>Organizer</th><td>';
			sdr_the_mailto_link($org_email, $event_name, get_field('brevet_organizer'));
			echo '</td>';
		echo '</tr><tr>'; // display registration cost, day-of registration status, advance registration status
			echo '<th>Registration info</th><td>';
			echo "Cost: \$$event_cost - ";
			if (get_field('brevet_day_of_registration')) {
				echo 'Day-of registration available.';
			} else {
				echo 'No day-of registration available.';
			}
			echo '<br>';
			$reg_deadline = get_field('brevet_advance_registration_deadline');
			$cur_time = time();
//			$cur_time = strtotime("$start_time + 1 day"); // test purposes
			if ($cur_time > strtotime($reg_deadline)) {
				echo 'Online registration is now closed.</td>';
			} else {
				echo "Online registration is open until $reg_deadline.</td>";
		echo '</tr><tr>'; // display registration button
			echo '<th>Register</th><td>';
	?>
<script language="JavaScript">
function sdr_reg_validate(){
   if (document.registration.os0.value.length == "0")
      {
         alert("Please type your name into the form.");
         document.registration.os0.focus();
         return false;
      }
   else if (document.registration.CHKBOX_1.checked == false)
      {
         alert("You must check the box to be able to register.");
         document.registration.CHKBOX_1.focus();
         return false;
      }
   else
      {
      return true;
      }
}
</script>

	<?php

   if ($SDR_PAYPAL_SANDBOX_TESTING) {
	echo '<form name="registration" method="post" action="https://www.sandbox.paypal.com/cgi-bin/webscr" onsubmit="return sdr_reg_validate();">';
	echo "<input name=\"business\" 	value=\"$SDR_PAYPAL_SANDBOX_EMAIL\" 			type=\"hidden\">";
   } else {
	echo '<form name="registration" method="post" action="https://www.paypal.com/cgi-bin/webscr" onsubmit="return sdr_reg_validate();">';
	echo "<input name=\"business\" 	value=\"$SDR_PAYPAL_EMAIL\" 				type=\"hidden\">";
   }

   echo '<input name="cmd" 		value="_xclick" 					type="hidden">';

   echo "<input name=\"item_name\" 	value=\"San Diego Randonneurs $event_type\"		type=\"hidden\">";
   // this value is important for the return processing, do not change
   echo "<input name=\"item_number\" 	value=\"$event_name\"	 				type=\"hidden\">";
   echo "<input name=\"amount\" 	value=\"$event_cost\"		 			type=\"hidden\">";
   
   echo '<input name="shipping" 	value="0.00" 						type="hidden">'; 
   echo '<input name="no_shipping" 	value="2" 						type="hidden">';
   
   $post_id = get_the_ID();
   $url = site_url();
   // PayPal return and cancel use the same page, return will generate a POST call with all payment parameters while cancel will generate a GET call
   // with the brevet post id as sole parameter
   echo "<input name=\"return\" 	value=\"$url/paypal-return\" 				type=\"hidden\">";
   echo "<input name=\"cancel_return\" 	value=\"$url/paypal-return/?brev_id=$post_id\" 		type=\"hidden\">";

   // rm=2 is required to make PayPal generate a POST request to the return URL with all transaction parameters
   echo '<input name="rm" 		value="2" 						type="hidden">';
   // this value is important for the return processing, do not change
   echo "<input name=\"custom\" 	value=\"$post_id\" 					type=\"hidden\">";

   echo '<input name="currency_code" 	value="USD" 						type="hidden">';
   echo '<input name="tax" 		value="0.00" 						type="hidden">';
   echo '<input name="lc" 		value="US" 						type="hidden">';
   echo '<input name="bn" 		value="PP-BuyNowBF" 					type="hidden">';

   echo '<input name="on0" 		value="RiderInfo" required				type="hidden"><b>Rider Name: </b>';
   echo '<input class="sdr-name" name="os0" maxlength="30" type="text">';

   echo '<input name="on1" 		value="RUSA#" 						type="hidden"><b> RUSA #: </b>';
   echo '<input class="sdr-rusaid" name="os1" maxlength="5" type="text"> (optional)';

   echo '<br>';
   echo '<input name="CHKBOX_1" 	value="1"	 					type="checkbox">';
   echo ' By checking this box, the rider agrees to abide by the <a target="_blank" href="https://www.rusa.org/pages/rulesForRiders">Rules for Riders</a>. ';
   echo 'Failure to abide by the rules may result in disqualification at the discretion of event staff.';

   echo '<br><input src="https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" border="0" type="image">';
   echo '</form>';
/* end of work in progress */
			echo '</td>';
			} // else registration is open
		echo '</tr><tr>'; // display list of currently registered riders
			echo '<th>Registered&nbspriders</th><td>'; // &nbsp is to avoid text wrapping in this column
			// expecting a comma separated list here
			$brev_riders = preg_split("/\s*[,;]\s*/", get_field('brevet_rider_list'));
			$break = ''; 
			foreach ($brev_riders as $rider) {
				if (!empty($rider)) { echo $break; echo esc_html($rider); $break='<br>'; }
			}
			echo '</td>';
		echo '</tr><tr>'; // display date of workers ride
			echo '<th>Workers ride</th><td>'; 
			if (empty(get_field('brevet_workers_ride_date'))) {
				echo 'No workers ride planned.';
			} else {
				the_field('brevet_workers_ride_date');
			}
			echo '</td>';
		echo '</tr><tr>'; // display list of volunteers
			echo '<th>Volunteers</th><td>';
			sdr_the_mailto_link($org_email, "Volunteer to $event_name", 'Click here to volunteer');
			echo '<br>';
			// expecting a comma separated list here
			$volunteers = preg_split("/\s*[,;]\s*/", get_field('brevet_volunteer_list')); 
			$break = ''; 
			foreach ($volunteers as $rider) {
				if (!empty($rider)) { echo $break; echo esc_html($rider); $break='<br>'; }
			}
			echo '</td>';
		echo '</tr></table>';
	?>
	
	<?php // print the brevet description
		the_content(); 
	?>

	<?php // display the embedded map if any
		the_field('brevet_embedded_map');
	else :
		get_template_part( 'template-parts/post/content', 'none' );
	endif;	?>

	</main><!-- #main -->
</div><!-- .wrap -->

<?php get_footer();
