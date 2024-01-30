<?php
/**
 * Template Name: People Directory (Select Roles)
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Blocks
 */

get_header();
?>

<main id="site-content" class="site-main prose sm:prose lg:prose-lg mx-auto">
		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

		endwhile; // End of the loop.
		?>
		<div class="mt-8 ml-4 mr-2" id="isotope-list" >
			<div class="flex flex-wrap">
		<?php

		$topics = get_field( 'role_select' );
		$program_slug = get_the_program_slug( $post );
		print_r ($program_people_select);
		if ( $topics ) {
			if ( ! is_array( $topics ) ) {
				$topics = array( $topics );
			}
			$args                = array(
				'post_type'      => 'people',
				'meta_key'       => 'ecpt_people_alpha',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'posts_per_page' => 100,
				'filter'         => $program_slug,
				'tax_query'      => array(
					array(
						'taxonomy' => 'role',
						'terms'    => $topics,
					),
				),
			);
			$select_people_query = new WP_Query( $args );
			if ( $select_people_query->have_posts() ) :
				while ( $select_people_query->have_posts() ) :
					$select_people_query->the_post();
					?>

					<?php get_template_part( 'template-parts/content', 'people-cards' ); ?>

							<?php
						endwhile;
			endif;
			?>
			<?php
			wp_reset_postdata();
		}
		?>
			</div>
		</div>
	</main><!-- #main -->

<?php
get_footer();
