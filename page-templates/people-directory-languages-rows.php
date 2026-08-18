<?php
/**
 * Template Name: People Directory Language Program (Rows)
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Department_Tailwind
 */

get_header();
$program_slug = get_the_program_slug( $post );
?>

<main id="site-content" class="site-main prose lg:prose-lg mx-auto">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'page' );
	endwhile;
	?>

	<?php
	// 1. Get terms to exclude.
	$faculty_titles_to_exclude = get_terms(
		array(
			'taxonomy' => 'role',
			'fields'   => 'ids',
			'slug'     => array( 'graduate', 'job-market-candidate', 'graduate-student', 'research' ),
		)
	);

	$ids_to_exclude = ( ! is_wp_error( $faculty_titles_to_exclude ) && ! empty( $faculty_titles_to_exclude ) )
		? $faculty_titles_to_exclude
		: array();

	// 2. Fetch all candidate role terms.
	$faculty_titles = get_terms(
		array(
			'taxonomy'   => 'role',
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'hide_empty' => true,
			'exclude'    => $ids_to_exclude,
			'filter'     => $program_slug,
		)
	);

	$active_faculty_titles = array();
	if ( ! empty( $faculty_titles ) && ! is_wp_error( $faculty_titles ) ) {
		$active_faculty_titles = array_filter(
			$faculty_titles,
			function ( $term ) use ( $program_slug ) {
				$count_query = get_posts(
					array(
						'post_type'      => 'people',
						'role'           => $term->slug,
						'filter'         => $program_slug,
						'posts_per_page' => 1,
						'fields'         => 'ids',
					)
				);
				return ! empty( $count_query );
			}
		);
	}
	?>

	<!-- 3. Filter Form (renders only if active roles exist) -->
	<?php if ( ! empty( $active_faculty_titles ) ) : ?>
		<form class="p-4 mx-4 my-4 border-2 border-solid isotope-to-sort bg-grey-lightest border-grey max-w-10/12" id="filters" onsubmit="return false;">
			<?php if ( count( $active_faculty_titles ) > 1 ) : ?>  
				<fieldset class="flex flex-col justify-start lg:flex-row">
					<legend class="px-2 mb-2 text-xl font-bold font-heavy">Filter by Position or Title:</legend>
					<?php foreach ( $active_faculty_titles as $faculty_title ) : ?>
						<button type="button" class="p-2 mx-1 my-2 text-lg font-heavy font-bold leading-tight text-center text-white capitalize align-bottom border-b-0 all button bg-blue hover:bg-blue-light hover:text-primary xl:my-0" data-filter=".<?php echo esc_attr( $faculty_title->slug ); ?>" aria-pressed="false">
							<?php echo esc_html( $faculty_title->name ); ?>
						</button>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>

			<fieldset class="w-auto px-2 my-2 search-form">
				<legend class="px-2 mt-4 mb-2 text-xl font-bold font-heavy">Search by name, title, or research interests:</legend>
				<label class="sr-only" for="id_search">Enter term</label>
				<input class="w-full p-2 ml-2 quicksearch form-input md:w-1/2" type="text" name="search" id="id_search" aria-label="Search Form" placeholder="Enter description keyword"/>
			</fieldset>
		</form>
	<?php endif; ?>

	<!-- 4. Directory Output Loop -->
	<div class="w-full mt-8 ml-6 mr-2" id="isotope-list" aria-live="polite">
		<div class="flex flex-wrap w-full">
			<?php
			if ( ! empty( $active_faculty_titles ) ) :
				foreach ( $active_faculty_titles as $position ) :
					$people_query = new WP_Query(
						array(
							'post_type'      => 'people',
							'role'           => $position->slug,
							'filter'         => $program_slug,
							'meta_key'       => 'ecpt_people_alpha',
							'orderby'        => 'meta_value',
							'order'          => 'ASC',
							'posts_per_page' => 100,
						)
					);

					if ( $people_query->have_posts() ) :
						?>
						<div class="item pt-2 w-full role-title quicksearch-match <?php echo esc_attr( $position->slug ); ?>">
							<h2 class="uppercase my-4! after:block after:w-1/2 after:pt-3 after:border-b-4 after:border-blue content-[''];">
								<?php echo esc_html( $position->name ); ?>
							</h2>
						</div>
						<?php
						while ( $people_query->have_posts() ) :
							$people_query->the_post();
							get_template_part( 'template-parts/content', 'people-sort' );
						endwhile;
						wp_reset_postdata();
					endif;
				endforeach;
			endif;
			?>
		</div>
	</div>

	<div id="noResult" class="hidden w-3/4 py-12 mx-4 mt-4 text-center border-2 border-dashed border-grey-light h-48">
		<p class="text-2xl font-bold text-blue font-heavy">No matching results</p>
		<p class="text-lg">Try adjusting your filters or search terms.</p>
	</div>
</main><!-- #main -->

<?php
get_footer();