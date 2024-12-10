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
			$heading = get_field( 'homepage_news_header', 'option' );
			if ( is_active_sidebar( 'news-inline' ) ) :
				$news_quantity = '2';
			else :
				$news_quantity = get_field( 'homepage_news_posts', 'option' );
			endif;
			?>

		<div class="divider div-transparent div-dot  my-12"></div>

		<div class="news-section px-2 sm:px-0">
			<div class="prose sm:prose lg:prose-lg xl:prose-xl mx-auto">
				<div class="flex flex-wrap justify-between px-4 lg:px-2">
					<div>
						<h2 class="pb-4 md:pb-0"><?php echo esc_html( $heading ); ?></h2>
					</div>
					<div>
						<a class="button" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
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
