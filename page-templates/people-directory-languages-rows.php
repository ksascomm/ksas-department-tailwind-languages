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

<main id="site-content" class="site-main prose sm:prose lg:prose-lg mx-auto">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

		endwhile; // End of the loop.
		?>
		<div class="isotope-to-sort bg-grey-lightest border-solid border-grey border-2 p-4 mx-2 lg:mx-0 my-4" role="region" aria-label="Filters" id="filters">
		<?php
				$ids_to_exclude            = array();
				$faculty_titles_to_exclude = get_terms(
					array(
						'taxonomy' => 'role',
						'fields'   => 'ids',
						'slug'     => array( 'graduate', 'job-market-candidate', 'graduate-student', 'research' ),
					)
				);
				// Convert the role slug to corresponding IDs.
				if ( ! is_wp_error( $faculty_titles_to_exclude ) && count( $faculty_titles_to_exclude ) > 0 ) {
					$ids_to_exclude = $faculty_titles_to_exclude;
				}
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
				?>
			<?php if ( count( $faculty_titles ) > 1 ) : ?>	
			<fieldset class="flex flex-col justify-start lg:flex-row">
				<legend class="px-2 mb-2 text-xl font-bold font-heavy">Filter by Position or Title:</legend>
				<?php foreach ( $faculty_titles as $faculty_title ) : ?>
					<button class="p-2 mx-1 my-2 text-lg font-heavy font-bold leading-tight text-center text-white capitalize align-bottom border-b-0 all button bg-blue hover:bg-blue-light hover:text-primary xl:my-0" href="javascript:void(0)" data-filter=".<?php echo esc_html( $faculty_title->slug ); ?>"><?php echo esc_html( $faculty_title->name ); ?></button>
				<?php endforeach; ?>
				
			</fieldset>
			<?php endif; ?>
			<fieldset class="w-auto px-2 my-2 search-form">
				<legend class="px-2 mt-4 mb-2 text-xl font-bold font-heavy">Search by name, title, or research interests:</legend>
				<label class="sr-only" for="id_search">Enter term</label>
				<input class="w-full p-2 ml-2 quicksearch form-input md:w-1/2" type="text" name="search" id="id_search" aria-label="Search Form" placeholder="Enter description keyword"/>
			</fieldset>
		</div>
		<div class="mt-8 ml-4 mr-2" id="isotope-list" >
			<div class="flex flex-wrap">
		<?php
			$positions        = get_terms(
				array(
					'taxonomy'   => 'role',
					'orderby'    => 'ID',
					'order'      => 'ASC',
					'hide_empty' => true,
					'exclude'    => $ids_to_exclude,
				)
			);
			$position_slugs   = array();
			$position_classes = implode( ' ', $position_slugs );
			foreach ( $positions as $position ) :
				$position_slug = $position->slug;
				$position_name = $position->name;

				$people_query = new WP_Query(
					array(
						'post_type'      => 'people',
						'role'           => $position_slug,
						'filter'         => $program_slug,
						'meta_key'       => 'ecpt_people_alpha',
						'orderby'        => 'meta_value',
						'order'          => 'ASC',
						'posts_per_page' => 100,
					)
				);

				if ( $people_query->have_posts() ) :
					?>
					<div class="item pt-2 w-full role-title quicksearch-match <?php echo esc_html( $position->slug ); ?>">
						<h2 class="uppercase my-4! after:block after:w-1/2 after:pt-3 after:border-b-4 after:border-blue content-[''];"><?php echo esc_html( $position_name ); ?></h2>
					</div>
					<?php
					while ( $people_query->have_posts() ) :
						$people_query->the_post();
						?>

						<?php get_template_part( 'template-parts/content', 'people-sort' ); ?>

						<?php
							endwhile;
				endif;
			endforeach;
			?>
				<div id="noResult">
					<h2>No matching results</h2>
				</div>
			<?php
			wp_reset_postdata();
			?>
		</div>
	</main><!-- #main -->

<?php
get_footer();
