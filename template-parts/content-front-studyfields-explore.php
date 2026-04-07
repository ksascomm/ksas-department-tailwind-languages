<?php
/**
 * Template part for displaying page content in front-page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Department_Tailwind
 */

?>

<div class="flex border-t border-blue hero bg-grey-cool bg-opacity-50 front-featured-image-area">
	<div class="flex items-center text-left px-8 md:px-12 pb-4 md:py-0 lg:w-7/12 ">
		<div class="mt-2 text-primary text-lg md:text-xl tracking-tight">
			<?php the_content(); ?>
		</div>
	</div>
<div class="w-full lg:w-5/12 front featured-image min-h-[300px] lg:min-h-full">
		<?php
		$hero_images = get_field( 'homepage_hero_images' );
		if ( ! empty( $hero_images ) && is_array( $hero_images ) ) :
			shuffle( $hero_images );
			$random_img = $hero_images[0]['homepage_hero_image'];
			?>
			<img class="m-0! h-full w-full object-cover slide-<?php echo esc_attr( sanitize_title( $random_img['title'] ) ); ?>" 
				src="<?php echo esc_url( $random_img['url'] ); ?>" 
				alt="<?php echo esc_attr( $random_img['alt'] ); ?>" />
		<?php else : ?>
			<?php the_post_thumbnail( 'full', array( 'class' => 'm-0! h-full w-full object-cover' ) ); ?>
		<?php endif; ?>
	</div>
</div>

<?php
if ( function_exists( 'get_field' ) && get_field( 'explore_the_department_languages' ) ) :
	?>
	<div class="container section-inner lg:max-xl:px-8 pt-6 pb-12">
	<?php
	if ( have_rows( 'explore_the_department_languages' ) ) :
		?>
		<?php $heading = get_field( 'buckets_heading_languages' ); ?>
		<!--Print Heading if there-->
		<?php if ( $heading ) : ?>
			<div class="px-8 mt-14 mb-8">
				<h2 class="my-0! mx-auto font-heavy font-bold"><?php echo esc_html( $heading ); ?></h2>
			</div>
		<?php endif; ?>
		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 px-4">
		<?php
		while ( have_rows( 'explore_the_department_languages' ) ) :
			the_row();
			$is_major     = get_sub_field( 'major' );
			$is_minor     = get_sub_field( 'minor' );
			$grad_degrees = get_sub_field( 'graduate_degree' );
			?>
			<div class="p-2">
				<div class="h-full rounded-lg field mb-4 px-6 py-4 overflow-hidden bg-grey-lightest grey-card-outline">
					<h3 class="text-2xl 2xl:text-3xl font-heavy font-bold mt-2!">
						<?php if ( get_sub_field( 'explore_bucket_link' ) ) : ?>
							<a href="<?php the_sub_field( 'explore_bucket_link' ); ?>">
								<?php the_sub_field( 'explore_bucket_heading' ); ?>
							</a>
						<?php else : ?>
								<?php the_sub_field( 'explore_bucket_heading' ); ?>
						<?php endif; ?>
					</h3>
					<p class="leading-normal text-lg 2xl:text-xl tracking-wide font-light">
						<?php the_sub_field( 'explore_bucket_text' ); ?>
					</p>
					<?php if ( $is_major || $is_minor || ! empty( $grad_degrees ) ) : ?>
						<ul class="degrees">
						<?php if ( $is_major ) : ?>
							<li class="degree on major">Major</li>
						<?php endif; ?>
						<?php if ( $is_minor ) : ?>
							<li class="degree on minor">Minor</li>
						<?php endif; ?>
						<?php $graduate_degree_checked_options = get_sub_field( 'graduate_degree' ); ?>
						<?php
						if ( ! empty( $grad_degrees ) ) :
							foreach ( $grad_degrees as $degree ) :
								?>
								<li class="degree on <?php echo esc_attr( $degree['value'] ); ?>">
									<?php echo esc_html( $degree['label'] ); ?>
								</li>
								<?php
							endforeach;
						endif;
						?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		<?php endwhile; ?>
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
