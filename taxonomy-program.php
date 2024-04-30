<?php
/**
 * The template for displaying Program News archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Department_Tailwind
 */

get_header();
$program_name    = get_the_program_name( $post );
?>

<main id="site-content" class="site-main prose sm:prose lg:prose-lg mx-auto">
	
		<?php
		if ( have_posts() ) : ?>			
			<header class="page-header">
				<h1 class="entry-title tracking-tight leading-10 sm:leading-none py-8"><?php echo $program_name;?> News Archive</h1>
			</header>
		<?php
		if ( function_exists( 'bcn_display' ) ) :
			?>
		<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
			<?php bcn_display(); ?>
		</div>
		<?php endif; ?>
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post(); 
				if ( 'post' == get_post_type() ) :

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'post-excerpt' );

				endif;
			endwhile;

			if ( function_exists( 'ksas_department_tailwind_pagination' ) ) :

				ksas_department_tailwind_pagination();

			else :

				the_posts_navigation();

			endif;

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();

