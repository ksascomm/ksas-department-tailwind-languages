<?php
/**
 * Template Name: Program Homepage
 *
 * @package KSAS_Department_Tailwind
 */

get_header();
?>

	<main id="site-content" class="mx-auto prose site-main lg:prose-lg">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

		endwhile; // End of the loop.
		?>
	<?php get_sidebar(); ?>
	</main><!-- #main -->
	
<?php
get_footer();
