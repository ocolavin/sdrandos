<?php
/**
 * The template for displaying the PayPal return page
 *
 */

get_header(); ?>

<div class="wrap">
	<main id="main" class="site-main" role="main">

		<?php

		echo '<h1>Brevet registration confirmation page</h1>';

		if (($post_id=$_POST['custom']) && ($brev_title=$_POST['item_number'])) { // POST data: there was a payment

			// for robustness, we check that the post id and brevet title of the POST data match in the database,
			// if not, something's fishy and we abort

			if ($brev_title == get_the_title($post_id)) { // we're good

				echo '<h2>Brevet registration confirmed</h2>';
				$rider_name = esc_html($_POST['option_selection1']);
				$rider_rusaid = esc_html($_POST['option_selection2']);
				// if no RUSA# provided by user, PayPal returns N/A
				// $rider_rusaid = ($rider_rusaid=='N/A') ? 'no RUSA id' : "RUSA# $rider_rusaid";
				$rider_rusaid = "RUSA# $rider_rusaid";
				$brev_link = get_the_permalink($post_id);

				echo "$rider_name ($rider_rusaid) is registered for the ";
				sdr_the_href_link($brev_link, $brev_title, false);
				echo " event (pending payment confirmation).";

				// add rider to brevet rider list

				if ($rider_name) {
					$rider_list = get_field('brevet_rider_list', $post_id);
					$rider_list .= "$rider_name ($rider_rusaid), ";
					update_field('brevet_rider_list', $rider_list, $post_id);
				}

			} else { // inconsistent POST data, registration is cancelled

				echo '<h2>Brevet registration NOT confirmed</h2>';
				echo 'Check with the '; 
				$subject = 'Registration issue with ' . $brev_title . "/" . get_the_title($post_id) ; 
				sdr_the_mailto_link($SDR_RBA_EMAIL, $subject, 'RBA');
				echo '.<br>'; 
			}


                } else if ($post_id = $_GET['brev_id']) { // no POST data: it is a cancellation

			echo '<h2>Brevet registration cancelled</h2>';
			$brev_link = esc_url(get_permalink($post_id));
			$brev_title = esc_html(get_the_title($post_id));
			echo 'Return to ';
			sdr_the_href_link($brev_link, $brev_title, false);
			echo '.<br>';

		} else { // not the right POST or GET data, the page was called without/with wrong parameters

			echo 'Insufficient data to confirm/cancel registration.';
		}

		?>

	</main><!-- #main -->
</div><!-- .wrap -->

<?php get_footer();
