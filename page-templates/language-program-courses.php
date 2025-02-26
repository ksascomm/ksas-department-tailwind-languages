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
	require get_stylesheet_directory() . '/lib/Zebra_cURL.php';

	// Set query string variables.
	$department_unclean         = 'Modern Languages and Literatures';
	$department                 = str_replace( ' ', '%20', $department_unclean );
	$department                 = str_replace( '&', '%26', $department );
	$program_slug               = get_the_program_slug( $post );
	$subdepartment_unclean      = $program_slug;
	$subdepartment_select         = get_field( 'program_course_select' );
	$subdepartment_select_unclean = $subdepartment_select->name;
	$subdepartment                = str_replace( ' ', '%20', $subdepartment_select_unclean );
	$subdepartment                = str_replace( '-', '%20', $subdepartment );
	$subdepartment                = str_replace( '&', '%26', $subdepartment );
	$fall                       = 'fall%202024';
	$summer                     = 'summer%202024';
	$spring                     = 'spring%202025';
	$open                       = 'open';
	$approval                   = 'approval%20required';
	$closed                     = 'closed';
	$waitlist                   = 'waitlist%20only';
	$reserved_open              = 'reserved%20open';
	$key                        = '0jCaUO1bHwbG1sFEKQd3iXgBgxoDUOhR';

	// Create first Zebra Curl class.
	$course_curl = new Zebra_cURL();
	$course_curl->option(
		array(
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_CONNECTTIMEOUT => 60,
		)
	);
	// Cache for 14 days.
	$course_curl->cache( WP_CONTENT_DIR . '/sis-cache/' . $subdepartment, 1209600 );

// Create API Url calls.
$courses_fall_url = 'https://sis.jhu.edu/api/classes?key=' . $key . '&School=Krieger%20School%20of%20Arts%20and%20Sciences&Term=' . $spring . '&Term=' . $fall . '&Department=AS%20' . $department . '&SubDepartment=' . $subdepartment . '&status=' . $open . '&status=' . $approval . '&status=' . $waitlist . '&status=' . $reserved_open;

$course_data = array();
$output      = '';

// get the first set of data.
$course_curl->get(
	$courses_fall_url,
	function( $result ) use ( &$course_data ) {

		$key = '0jCaUO1bHwbG1sFEKQd3iXgBgxoDUOhR';

		if ( ( is_array( $result ) && ! empty( $result ) ) || is_object( $result ) ) {

			$result->body = ! is_array( $result->body ) ? json_decode( html_entity_decode( $result->body ) ) : $result->body;

			foreach ( $result->body as $course ) {

				$section = $course->{'SectionName'};
				$level   = $course->{'Level'};

				if (
					strpos( $level, 'Graduate' ) !== false
				|| strpos( $level, 'Undergraduate' ) !== false
				|| ( $level === '' ) !== false
				) {
					$number       = $course->{'OfferingName'};
					$clean_number = preg_replace( '/[^A-Za-z0-9\-]/', '', $number );
					$dirty_term   = $course->{'Term_IDR'};
					$clean_term   = str_replace( ' ', '%20', $dirty_term );
					$details_url  = 'https://sis.jhu.edu/api/classes/' . $clean_number . $section . '/' . $clean_term . '?key=' . $key;

					// add to array!
					$course_data[] = $details_url;
				}
			}
		}

	}
);

	// Now that we have the first set of data.
	$course_curl->get(
		$course_data,
		function( $result ) use ( &$output ) {

			$result->body = ! is_array( $result->body ) ? json_decode( html_entity_decode( $result->body ) ) : $result->body;

			$title               = $result->body[0]->{'Title'};
			$term                = $result->body[0]->{'Term_IDR'};
			$clean_term          = str_replace( ' ', '-', $term );
			$meetings            = $result->body[0]->{'Meetings'};
			$status              = $result->body[0]->{'Status'};
			$seatsavailable      = $result->body[0]->{'SeatsAvailable'};
			$course_number       = $result->body[0]->{'OfferingName'};
			$clean_course_number = preg_replace( '/[^A-Za-z0-9\-]/', '', $course_number );
			$credits             = $result->body[0]->{'Credits'};
			$section_number      = $result->body[0]->{'SectionName'};
			$instructor          = $result->body[0]->{'InstructorsFullName'};
			$course_level        = $result->body[0]->{'Level'};
			$location            = $result->body[0]->{'Location'};
			$description         = $result->body[0]->{'SectionDetails'}[0]->{'Description'};
			$room                = $result->body[0]->{'SectionDetails'}[0]->{'Meetings'}[0]->{'Building'};
			$roomnumber          = $result->body[0]->{'SectionDetails'}[0]->{'Meetings'}[0]->{'Room'};
			$sectiondetails      = $result->body[0]->{'SectionDetails'}[0];
			$tags                = array();

			if ( isset( $sectiondetails->{'PosTags'} ) ) {
				if ( ! empty( $sectiondetails->{'PosTags'} ) ) {
						$postag = $sectiondetails->{'PosTags'};
					foreach ( $postag as $tag ) {
						$tags[] = $tag->{'Tag'};
					}
				}
			}
			$print_tags = empty( $tags ) ? 'n/a' : implode( ', ', $tags );

			$output .= '<tr><td>' . $course_number . '&nbsp;(' . $section_number . ')</td><td>' . $title . '</td><td class="show-for-medium">' . $meetings . '</td><td class="show-for-medium">' . $instructor . '</td><td class="show-for-medium">' . $room . '&nbsp;' . $roomnumber . '</td><td>' . $term . '</td>';

			$output .= '<td><p class="hidden">' . $description . '</p><button class="modal-button bg-blue text-white px-2 hover:text-black hover:bg-blue-light" href="#course-' . $clean_course_number . $section_number . $clean_term . '">More Info<span class="sr-only">-' . $title . '-' . $section_number . '</span></button></td></tr>';

			$output .= '<div class="modal" id="course-' . $clean_course_number . $section_number . $clean_term . '"><div class="modal-content"><div class="modal-header"><span class="close">×</span><h1 id="' . $clean_term . $course_number . '-' . $section_number . '">' . $title . '<br><small>' . $course_number . '&nbsp;(' . $section_number . ')</small></h1></div><div class="modal-body"><p>' . $description . '<ul><li><strong>Days/Times:</strong> ' . $meetings . ' </li><li><strong>Instructor:</strong> ' . $instructor . ' </li><li><strong>Room:</strong> ' . $room . '&nbsp;' . $roomnumber . ' </li><li><strong>Status:</strong> ' . $status . '</li><li><strong>Seats Available:</strong> ' . $seatsavailable . '</li><li><strong>PosTag(s):</strong> ' . $print_tags . '</li></ul></p></div></div></div>';
		}
	);

	?>

<main id="site-content" class="site-main prose sm:prose lg:prose-lg mx-auto">
<?php
	$theme = wp_get_theme(); // Gets the current theme.
if ( 'ksas-blocks' === $theme->template ) :
	// Gets the parent theme template.
	?>

		<?php
		if ( function_exists( 'bcn_display' ) ) :
			?>
		<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<?php bcn_display(); ?>
		</div>
		<?php endif; ?>
	<?php endif; ?>
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
				<th>Course Details</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $output; ?>
		</tbody>
	</table>
	</div>
</main><!-- #main -->
<?php
get_footer();
