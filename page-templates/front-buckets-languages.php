<?php
/**
 * Template Name: Front (Buckets for Languages)
 * The template for displaying the front page with 3 buckets via ACF.
 * Options for events feed above news, widget within news,
 * and widgets below news feed.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Department_Tailwind
 */

get_header();
?>

	<main id="site-content" class="site-main front prose md:prose-lg lg:prose-xl">

		<?php
		while ( have_posts() ) :
			the_post()
			?>
			<?php
			get_template_part( 'template-parts/content', 'front-studyfields-explore' );

		endwhile; // End of the loop.
		?>
		<?php if ( is_active_sidebar( 'below-explore' ) ) : ?>
			<?php get_template_part( 'template-parts/widgets-below-explore' ); ?>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'events-featured' ) ) : ?>
			<?php get_template_part( 'template-parts/widgets-events-featured' ); ?>
		<?php endif; ?>
		<?php
		if ( get_field( 'show_homepage_news_feed', 'option' ) ) :
			// If ACF Conditional is YES, display news feed.
			$heading       = get_field( 'homepage_news_header', 'option' );
			$news_quantity = get_field( 'homepage_news_posts', 'option' );
			?>

		<div class="mt-2 mb-4 relative h-1 pb-4 after:absolute after:bg-[rgb(229_226_224_/_var(--tw-bg-opacity,1))] after:border-[rgb(49_38_29_/_var(--tw-border-opacity,1))] after:absolute after:z-[1] after:top-[-9px] after:left-[calc(50%_-_9px)] after:w-[18px] after:h-[18px] after:border after:shadow-[inset_0_0_0_2px_#fefefe,0_0_0_4px_#fefefe] after:rounded-[50%] after:border-solid; before:absolute before:w-[90%] before:h-px before:bg-[linear-gradient(_to_right,transparent,rgb(49,38,29),transparent_)] before:top-0 before:inset-x-[5%]"></div>

		<div class="news-section px-2 sm:px-0 py-12">
			<div class="prose sm:prose lg:prose-lg xl:prose-xl mx-auto">
				<div class="flex flex-wrap justify-between px-4 lg:px-2">
					<div>
						<h2 class="pb-4 md:pb-0 my-0!"><?php echo esc_html( $heading ); ?></h2>
					</div>
					<div>
						<a class="not-prose bg-blue text-white inline-flex py-2 px-3 text-base items-center border-none! hover:text-primary hover:bg-blue-light" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
							View All Posts&nbsp;<span class="fa-solid fa-circle-chevron-right" aria-hidden="true"></span></a>
					</div>
				</div>
			</div>
			<?php
			$news_query = new WP_Query(
				array(
					'post_type'      => 'post',
					'posts_per_page' => $news_quantity,
				)
			);
			?>
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 p-4 mx-auto">
				<?php
				if ( $news_query->have_posts() ) :
					while ( $news_query->have_posts() ) :
						$news_query->the_post();
						get_template_part( 'template-parts/content', 'front-post-excerpt' );
				endwhile;
				endif;
				?>
				<?php if ( is_active_sidebar( 'news-inline' ) ) : ?>
					<?php get_template_part( 'template-parts/widgets-news-inline' ); ?>
				<?php endif; ?>
				</div>
		</div>
		<?php else : // field_name returned false. ?>
		<?php endif; // end of if field_name logic. ?>
	</main><!-- #main -->
	<?php if ( is_active_sidebar( 'below-news' ) ) : ?>
		<?php get_template_part( 'template-parts/widgets-below-news' ); ?>
	<?php endif; ?>
<?php
get_footer();
