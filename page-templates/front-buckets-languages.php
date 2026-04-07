<?php
/**
 * Template Name: Front (Buckets for Languages)
 *
 * @package KSAS_Department_Tailwind
 */

get_header();
?>

<main id="site-content" class="site-main front prose md:prose-lg lg:prose-xl max-w-none">

	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'front-studyfields-explore' );
	endwhile;
	?>

	<?php if ( is_active_sidebar( 'below-explore' ) ) : ?>
		<?php get_template_part( 'template-parts/widgets-below-explore' ); ?>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'events-featured' ) ) : ?>
		<?php get_template_part( 'template-parts/widgets-events-featured' ); ?>
	<?php endif; ?>

	<?php
	// Fetch News Configuration.
	$show_news     = get_field( 'show_homepage_news_feed', 'option' );
	$news_heading  = get_field( 'homepage_news_header', 'option' );
	$news_quantity = get_field( 'homepage_news_posts', 'option' );

	// Ensure we have a valid integer for the query.
	$posts_to_show = ( ! empty( $news_quantity ) ) ? (int) $news_quantity : 3;

	if ( $show_news ) :
		?>

		<div class="mt-2 mb-4 relative h-1 pb-4 after:absolute after:bg-[rgb(229_226_224/var(--tw-bg-opacity,1))] after:border-[rgb(49_38_29/var(--tw-border-opacity,1))] after:z-1 after:-top-2.25 after:left-[calc(50%-9px)] after:w-4.5 after:h-4.5 after:border after:shadow-[inset_0_0_0_2px_#fefefe,0_0_0_4px_#fefefe] after:rounded-[50%] after:border-solid; before:absolute before:w-[90%] before:h-px before:bg-[linear-gradient(to_right,transparent,rgb(49,38,29),transparent)] before:top-0 before:inset-x-[5%]"></div>

		<div class="container px-2 py-12 mx-auto news-section section-inner sm:px-0">
			<div class="flex flex-wrap justify-between items-end px-4 lg:px-0 2xl:max-w-[1800px] 2xl:mx-auto mb-8">
				<h2 class="pb-4 md:pb-0 my-0! font-bold font-heavy">
					<?php echo esc_html( $news_heading ); ?>
				</h2>
				<div class="pb-4">
					<a class="not-prose bg-blue text-white inline-flex py-2 px-4 text-base font-bold items-center transition-colors hover:bg-blue-light" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
						View All Posts <span class="ml-2 fa-solid fa-circle-chevron-right" aria-hidden="true"></span>
					</a>
				</div>
			</div>

			<?php
			$news_query = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => $posts_to_show,
					'ignore_sticky_posts' => 1, // Usually preferred for homepage feeds.
				)
			);

			if ( $news_query->have_posts() ) :
				?>
				<div class="grid grid-cols-1 gap-8 p-4 xl:grid-cols-3 justify-items-stretch 2xl:max-w-[1800px] 2xl:mx-auto">
					<?php
					while ( $news_query->have_posts() ) :
						$news_query->the_post();
						get_template_part( 'template-parts/content', 'front-post-excerpt' );
					endwhile;
					?>
				</div>
				<?php
				wp_reset_postdata(); // CRITICAL: Restore the global $post object.
			endif;
			?>
		</div>
	<?php endif; ?>
</main>

<?php if ( is_active_sidebar( 'below-news' ) ) : ?>
	<?php get_template_part( 'template-parts/widgets-below-news' ); ?>
<?php endif; ?>

<?php get_footer(); ?>