<?php
/**
 * Template part for displaying page content in front-page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KSAS_Department_Tailwind
 */

?>
<?php
	$studyfieldacf  = get_field( 'studyfield' );
	$studyfield_url = 'https://krieger.jhu.edu/wp-json/wp/v2/studyfields?slug=' . $studyfieldacf;
if ( WP_DEBUG || false === ( $studyfield = get_transient( 'studyfield_api_query' ) ) ) {
	$studyfield = wp_remote_get( $studyfield_url );
	set_transient( 'studyfield_api_query', $studyfield, 2419200 );
}

	// Display a error nothing is returned.
if ( is_wp_error( $studyfield ) ) {
	$error_string = $studyfield->get_error_message();
	echo '<script>console.log("Error:' . $error_string . '")</script>';

}
	// Get the body.
	$studyfield_response = json_decode( wp_remote_retrieve_body( $studyfield ) );

	// Display a warning nothing is returned.
if ( empty( $studyfield_response ) ) {
	echo '<script>console.log("Error: There is no API Response")</script>';
}

if ( ! empty( $studyfield_response ) ) :
	?>

	<?php
	foreach ( $studyfield_response as $studyfield_data ) :
		$studyfield_tagline = $studyfield_data->post_meta_fields->ecpt_headline[0];
		$studyfield_degrees = $studyfield_data->post_meta_fields->ecpt_degreesoffered[0];
	endforeach;
	?>
<?php endif; ?>

<div class="flex border-t border-blue hero bg-grey-cool bg-opacity-50 front-featured-image-area">
	<div class="flex items-center text-left px-8 md:px-12 pb-4 md:py-0 lg:w-7/12 ">
		<div>
			<h2 class="text-primary text-3xl md:text-3xl lg:text-4xl mt-8 lg:mt-0 font-heavy font-bold">
				<?php if ( ! empty( $studyfield_tagline ) ) : ?>
					<?php echo esc_html( $studyfield_tagline ); ?>
				<?php else : ?>
					<?php the_title(); ?>
				<?php endif; ?>
			</h2>
			<div class="mt-2 text-primary text-lg md:text-xl tracking-tight">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<div class="hidden lg:block lg:w-5/12 front featured-image">
		<?php if ( have_rows( 'homepage_hero_images' ) ) : ?>
			<?php
			$random_images = get_field( 'homepage_hero_images' );
			shuffle( $random_images );
			// print("<pre>".print_r($random_images,true)."</pre>");
			$random_img_url   = $random_images[0]['homepage_hero_image']['url'];
			$random_img_alt   = $random_images[0]['homepage_hero_image']['alt'];
			$random_img_title = $random_images[0]['homepage_hero_image']['title'];
			?>
			<img class="!mt-0 h-56 w-full object-cover sm:h-72 lg:w-full lg:h-full slide-<?php echo esc_html( $random_img_title ); ?>" src="<?php echo esc_url( $random_img_url ); ?>" alt="<?php echo esc_html( $random_img_alt ); ?>" />
		<?php else : ?>
			<?php
			the_post_thumbnail(
				'full',
				array(
					'class' => 'h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full',
				)
			);
			?>
		<?php endif; ?>
	</div>
</div>

<?php
if ( function_exists( 'get_field' ) && get_field( 'explore_the_department_languages' ) ) :
	?>
	<div class="container">
	<?php
	if ( have_rows( 'explore_the_department_languages' ) ) :
		$count = count( get_field( 'explore_the_department_languages' ) );
		?>
		<?php $heading = get_field( 'buckets_heading_languages' ); ?>
		<!--Print Heading if there-->
		<?php if ( $heading ) : ?>
			<div class="px-8 mt-12 xl:mt-18 mb-4">
				<h2 class="!my-0  mx-auto font-semi font-semibold"><?php echo esc_html( $heading ); ?></h2>
			</div>
		<?php endif; ?>
		<!--Show Columns Dynamically-->
		<?php if ( $count == 2 ) : ?>
			<div class="mx-auto grid grid-cols-1 xl:grid-cols-3 px-4 xl:justify-items-center">
		<?php elseif ( $count == 3 ) : ?>
			<div class="mx-auto grid grid-cols-1 xl:grid-cols-3 px-4 xl:justify-items-center">
		<?php elseif ( $count == 6 ) : ?>
			<div class="mx-auto grid grid-cols-1 xl:grid-cols-3 px-4 xl:justify-items-center">		
		<?php endif; ?>
		<?php
		while ( have_rows( 'explore_the_department_languages' ) ) :
			the_row();
			?>
			<?php
			// If there's an image for the bucket, do CSS magic.
			if ( get_sub_field( 'explore_bucket_image' ) ) :
				?>
			<div class="bucket relative not-prose bucket-<?php echo get_row_index(); ?>">
				<?php
				$image = get_sub_field( 'explore_bucket_image' );
				echo wp_get_attachment_image( $image['ID'], 'full', false, array( 'class' => 'lg:blur-[1px] w-full' ) );
				 ?>
				<div class="p-6 bucket-text lg:top-0 lg:right-0 lg:left-0 lg:bottom-0 lg:inset-0 lg:absolute">
			<?php else : ?>
			<div class="p-2">
				<div class="h-full rounded-lg field mb-4 px-6 py-4 overflow-hidden bg-grey-lightest grey-card-outline">
			<?php endif;?>
					<h3 class="text-2xl 2xl:text-3xl not-prose font-semi font-semibold">
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
					<?php if ( get_sub_field( 'major' ) == 1 ||  get_sub_field( 'minor' ) == 1 ) : ?>
						<ul class="degrees">
						<?php if ( get_sub_field( 'major' ) == 1 ) : ?>
							<li class="degree on major">Major</li>
						<?php endif;?>
						<?php if ( get_sub_field( 'minor' ) == 1 ) : ?>
							<li class="degree on minor">Minor</li>
						<?php endif;?>
						<?php $graduate_degree_checked_options = get_sub_field( 'graduate_degree' ); ?>
						<?php if ( $graduate_degree_checked_options ) : ?>
							<?php foreach ( $graduate_degree_checked_options as $graduate_degree_checked_option ) : ?>
								<li class="degree on <?php echo $graduate_degree_checked_option['value']; ?>"><?php echo $graduate_degree_checked_option['label']; ?></li>
							<?php endforeach; ?>
						<?php endif; ?>
						</ul>
					<?php endif;?>
				</div>
			</div>
		<?php endwhile; ?>
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
