<?php
/**
 * Template Name: Language Program Courses
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Department_Tailwind
 */

get_header();
?>
<?php
	// Load Zebra Curl.
	require_once get_stylesheet_directory() . '/lib/Zebra_cURL.php';

	// Set query string variables.
	$dept_raw    = 'Modern Languages and Literatures';
	$department  = rawurlencode( $dept_raw );
	$subdept_obj = get_field( 'program_course_select' );

	// Safety check: if ACF is empty, stop or provide default.
if ( ! $subdept_obj ) {
	return;
}
	$subdept_name = str_replace( '-', ' ', $subdept_obj->name );
	$sub_dept_url = rawurlencode( $subdept_name );

	// Manual Semester Control.
	$fall   = 'fall%202026';
	$summer = 'summer%202026';
	$spring = 'spring%202026';
	$key    = '0jCaUO1bHwbG1sFEKQd3iXgBgxoDUOhR';

	// Create first Zebra Curl class.
	$course_curl = new Zebra_cURL();
	$course_curl->option(
		array(
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_CONNECTTIMEOUT => 60,
		)
	);
	// Cache for 14 days.
	$course_curl->cache( WP_CONTENT_DIR . '/sis-cache/' . sanitize_title( $subdept_name ), 1209600 );

	// Create API Url calls.
	$api_url = "https://sis.jhu.edu/api/classes?key={$key}&School=" . rawurlencode( 'Krieger School of Arts and Sciences' ) . "&Term={$spring}&Term={$fall}&Department=AS%20{$department}&SubDepartment={$sub_dept_url}";

	$course_data = array();

	// 5. Get the first set of data.
	$course_curl->get(
		$api_url,
		function ( $result ) use ( &$course_data, $key ) {

			if ( empty( $result->body ) ) {
				return;
			}

			// Decode JSON safely.
			$body = ! is_array( $result->body ) ? json_decode( html_entity_decode( $result->body ) ) : $result->body;

			if ( ! is_array( $body ) ) {
				return;
			}

			foreach ( $body as $course ) {
				$section = $course->{'SectionName'} ?? '';
				$level   = $course->{'Level'} ?? '';

				// Clean check for course levels.
				$is_grad  = ( false !== strpos( $level, 'Graduate' ) );
				$is_under = ( false !== strpos( $level, 'Undergraduate' ) );
				$is_empty = ( '' === $level );

				if ( $is_grad || $is_under || $is_empty ) {
					$number       = $course->{'OfferingName'};
					$clean_number = preg_replace( '/[^A-Za-z0-9\-]/', '', $number );

					$term_raw   = $course->{'Term_IDR'};
					$clean_term = rawurlencode( $term_raw );

					$course_data[] = "https://sis.jhu.edu/api/classes/{$clean_number}{$section}/{$clean_term}?key={$key}";
				}
			}
		}
	);

	// Now that we have the first set of data.
	$course_curl->get(
		$course_data,
		function ( $result ) use ( &$output ) {
			$body = ! is_array( $result->body ) ? json_decode( html_entity_decode( $result->body ) ) : $result->body;

			if ( empty( $body ) || ! isset( $body[0] ) ) {
				return;
			}

			$course              = $body[0];
			$title               = $course->{'Title'} ?? 'No Title';
			$term                = $course->{'Term_IDR'} ?? '';
			$clean_term          = str_replace( ' ', '-', $term );
			$course_number       = $course->{'OfferingName'} ?? '';
			$clean_course_number = preg_replace( '/[^A-Za-z0-9\-]/', '', $course_number );
			$section_number      = $course->{'SectionName'} ?? '';
			$instructor          = $course->{'InstructorsFullName'} ?? 'Staff';
			$location            = $course->{'Location'} ?? '';
			$meetings            = $course->{'Meetings'} ?? 'TBA';
			$status              = $course->{'Status'} ?? 'N/A';
			$seats               = $course->{'SeatsAvailable'} ?? '0';

			$section_details = $course->{'SectionDetails'}[0] ?? null;
			$description     = $section_details->{'Description'} ?? 'No description available.';
			$credits         = $section_details->{'Credits'} ?? '';
			// --- NEW ROOM LOGIC ---
			$room        = $section_details->{'Meetings'}[0]->{'Building'} ?? '';
			$roomnumber  = $section_details->{'Meetings'}[0]->{'Room'} ?? '';
			$room2       = '';
			$roomnumber2 = '';
			if ( isset( $section_details->{'Meetings'}[1] ) && is_object( $section_details->{'Meetings'}[1] ) ) {
				$second_meeting = $section_details->{'Meetings'}[1];
				$room2          = $second_meeting->{'Building'} ?? '';
				$roomnumber2    = $second_meeting->{'Room'} ?? '';
			}
			$room_info = trim( $room . ' ' . $roomnumber );
			if ( ! empty( $room2 ) || ! empty( $roomnumber2 ) ) {
				$room_info .= '; ' . trim( $room2 . ' ' . $roomnumber2 );
			}

			$location_display = ( strtolower( trim( $location ) ) === 'online' ) ? 'Online' : $room_info;

			// --- NEW TAGS LOGIC ---
			$tags = array();
			if ( isset( $section_details->{'PosTags'} ) && is_array( $section_details->{'PosTags'} ) ) {
				foreach ( $section_details->{'PosTags'} as $tag ) {
					$tags[] = $tag->{'Tag'};
				}
			}
			$print_tags = empty( $tags ) ? 'n/a' : implode( ', ', $tags );

			ob_start(); // Buffer the output to avoid PHPCS "Direct echo" warnings in some contexts.
			?>
		<tr>
			<td><?php echo esc_html( $course_number ); ?>&nbsp;(<?php echo esc_html( $section_number ); ?>)</td>
			<td><?php echo esc_html( $title ); ?></td>
			<td><?php echo esc_html( $meetings ); ?></td>
			<td><?php echo esc_html( $instructor ); ?></td>
			<td><?php echo esc_html( $location_display ); ?></td>
			<td><?php echo esc_html( $term ); ?></td>
			<td class="none">
				<div class="course-details-accordion">
					<ul class="additional-info">
						<li><strong>Description:</strong> <?php echo wp_kses_post( $description ); ?></li>
						<li><strong>Credits:</strong> <?php echo esc_html( $credits ); ?></li>
						<li><strong>Status:</strong> <?php echo esc_html( $status ); ?></li>
						<li><strong>Seats Available:</strong> <?php echo esc_html( $seats ); ?></li>
						<li><strong>Tags:</strong> <?php echo esc_html( $print_tags ); ?></li>
					</ul>
				</div>
			</td>
		</tr>
			<?php
			$output .= ob_get_clean();
		}
	);

	?>

<main id="site-content" class="site-main prose sm:prose lg:prose-lg mx-auto">
	<?php
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/content', 'page' );

	endwhile; // End of the loop.
	?>
	<div class="course-listings all-courses">
	<table aria-describedby="tblDescfall" class="course-table">
		<thead>
			<tr>
				<th>Course # (Section)</th>
				<th>Title</th>
				<th>Day/Times</th>
				<th>Instructor</th>
				<th>Location</th>
				<th>Term</th>
				<th class="none">Additional Details</th>
			</tr>
		</thead>
		<tbody>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $output;
			?>
		</tbody>
	</table>
	</div>
</main><!-- #main -->
<?php
get_footer();
