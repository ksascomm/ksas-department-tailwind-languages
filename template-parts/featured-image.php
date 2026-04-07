<?php
/**
 * Template part for displaying featured images with Language Program name
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Flagship_Tailwind
 */

?>
<div class="alignfull featured-image-area front-featured-image-area h-auto mt-0! bg-white lg:bg-grey-lightest">
	<div class="flex h-auto lg:h-80 ">
		<div class="flex lg:pr-6 text-left pl-6 md:pl-[4%] 2xl:pl-[6%] 3xl:pl-[12%] 4xl:pl-[15%] lg:items-center lg:justify-start sm:w-full lg:w-2/5">
			<h1 class="tracking-tight leading-10 sm:leading-none lg:text-4xl xl:text-[44px] py-8 mb-0">
				<?php echo esc_html( get_the_title() ); ?>
				
				<?php
				$program_name   = get_the_program_name( $post );
				$valid_programs = array( 'French', 'German', 'Hebrew and Yiddish', 'Italian', 'Portuguese', 'Spanish', 'Spanish and Portuguese' );

				if ( in_array( $program_name, $valid_programs, true ) ) :
					?>
					<br>
					<small class="font-bold font-heavy text-2xl!">
						<?php
						/* translators: %s: Program Name */
						echo esc_html( sprintf( __( '%s Program', 'flagship-tailwind' ), $program_name ) );
						?>
					</small>
				<?php endif; ?>
			</h1>
		</div>

		<div class="hidden lg:block lg:w-3/5" style="clip-path:polygon(5% 0, 100% 0%, 100% 100%, 0 100%)">
			<?php
			if ( has_post_thumbnail() ) :
				the_post_thumbnail(
					'full',
					array(
						'class' => 'h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full',
						'title' => esc_attr__( 'Feature image', 'flagship-tailwind' ),
					)
				);
			else :
				$theme_uri = get_template_directory_uri();
				$i         = wp_rand( 1, 9 );
				$image_url = "{$theme_uri}/dist/images/header-images/interior-banner-{$i}.jpg";
				?>
				<img 
					src="<?php echo esc_url( $image_url ); ?>" 
					alt="<?php esc_attr_e( 'Hero Image of Students on Campus', 'flagship-tailwind' ); ?>" 
					class="object-cover w-full h-56 sm:h-72 lg:w-full lg:h-full stock-image"
				>
			<?php endif; ?>
		</div>
	</div>
</div>
