<?php
/**
 * KSAS Department Language Programs functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package KSAS_Department_Tailwind
 */

/**
 * 1. ENQUEUE STYLES & SCRIPTS
 */

add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );
	/**
	 * Sets up styles and scripts for this child theme
	 *
	 * Note that this function is hooked into the wp_enqueue_scripts
	 */
function child_theme_enqueue_styles() {
	$parent_style = 'ksas-department-tailwind-style';

	// Parent theme style.
	wp_enqueue_style( $parent_style, get_template_directory_uri() . '/dist/css/style.css', array(), filemtime( get_template_directory() . '/resources/css' ), 'all' );

	// Child theme style.
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( $parent_style ),
		wp_get_theme()->get( 'Version' )
	);
}

/**
 * 2. Create the custom Programs taxonomy
 *
 * @return void
 */
function create_the_programs() {
	$labels = array(
		'name'               => _x( 'Programs', 'taxonomy general name' ),
		'singular_name'      => _x( 'Program', 'taxonomy singular name' ),
		'add_new'            => _x( 'Add New Program', 'Program' ),
		'add_new_item'       => __( 'Add New Program' ),
		'edit_item'          => __( 'Edit Program' ),
		'new_item'           => __( 'New Program' ),
		'view_item'          => __( 'View Program' ),
		'search_items'       => __( 'Search Programs' ),
		'not_found'          => __( 'No Program found' ),
		'not_found_in_trash' => __( 'No Program found in Trash' ),
	);

	$pages = array( 'profile', 'post', 'bulletinboard', 'page', 'faculty-books' );

	$args = array(
		'labels'            => $labels,
		'singular_label'    => __( 'Program' ),
		'public'            => true,
		'show_ui'           => true,
		'hierarchical'      => true,
		'show_in_rest'      => true, // Needed for tax to appear in Gutenberg editor.
		'show_tagcloud'     => false,
		'show_in_nav_menus' => false,
		'rewrite'           => array(
			'slug'       => 'program',
			'with_front' => false,
		),
	);
	register_taxonomy( 'program', $pages, $args );
}
add_action( 'init', 'create_the_programs' );

/**
 * 3. PROGRAM DATA HELPER (Centralized logic)
 */
function get_current_program_context() {
	static $context = null;
	if ( null !== $context ) {
		return $context;
	}

	$obj_id  = get_queried_object_id();
	$context = array(
		'name' => '',
		'slug' => '',
	);

	if ( is_page() && ! is_page_template( 'page-templates/program-homepage.php' ) ) {
		$parents         = get_post_ancestors( $obj_id );
		$top_id          = ( ! empty( $parents ) ) ? end( $parents ) : $obj_id;
		$parent          = get_post( $top_id );
		$context['name'] = $parent->post_title;
		$context['slug'] = $parent->post_name;
	} elseif ( is_page_template( 'page-templates/program-homepage.php' ) ) {
		$context['name'] = get_the_title( $obj_id );
		$context['slug'] = sanitize_title( $context['name'] );
	} elseif ( is_category() || is_tax() ) {
		$term            = get_queried_object();
		$context['name'] = $term->name;
		$context['slug'] = $term->slug;
	} elseif ( is_singular() ) {
		$tax   = is_singular( 'people' ) ? 'filter' : 'program';
		$terms = get_the_terms( $obj_id, $tax );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$context['name'] = implode( ', ', wp_list_pluck( $terms, 'name' ) );
			$context['slug'] = implode( ' ', wp_list_pluck( $terms, 'slug' ) );
		}
	}
	return $context;
}

/**
 * Legacy wrapper for getting program slug.
 *
 * @return string
 */
function get_the_program_slug() {
	$data = get_current_program_context();
	return $data['slug']; }
/**
 * Legacy wrapper for getting program name.
 *
 * @return string
 */
function get_the_program_name() {
	$data = get_current_program_context();
	return $data['name']; }

/**
 * 4. ADMIN COLUMN CUSTOMIZATION
 */
add_filter( 'manage_post_posts_columns', 'add_program_columns' );
/**
 * Add program columns to wp-admin post list.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function add_program_columns( $columns ) {
	unset( $columns['author'], $columns['comments'] );
	$columns['program'] = __( 'Program' );
	return $columns;
}

add_action( 'manage_posts_custom_column', 'custompost_columns', 10, 2 );
/**
 * Render the content for custom admin columns.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 * @return void
 */
function custompost_columns( $column, $post_id ) {
	if ( 'program' === $column ) {
		$terms = get_the_term_list( $post_id, 'program', '', ', ', '' );
		echo ( $terms ) ? wp_kses_post( $terms ) : esc_html__( 'No Program Assigned', 'ksas-department-tailwind' );
	}
}

add_action( 'restrict_manage_posts', 'post_program_add_taxonomy_filters' );
/**
 * Add taxonomy filters to the admin post list.
 *
 * @return void
 */
function post_program_add_taxonomy_filters() {
	global $typenow;
	if ( 'post' === $typenow ) {
		// 1. Check for the nonce (WordPress uses 'nonce' or '_wpnonce' in admin lists).
		if ( isset( $_GET['program'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'bulk-posts' ) ) {
			$selected = sanitize_text_field( wp_unslash( $_GET['program'] ) );
		} else {
			// 2. Default if nonce is missing or input isn't set.
			$selected = isset( $_GET['program'] ) ? sanitize_text_field( wp_unslash( $_GET['program'] ) ) : '';
		}
		$info = get_taxonomy( 'program' );
		wp_dropdown_categories(
			array(
				'show_option_all' => sprintf(
					/* translators: %s: Taxonomy label name */
					__( 'Show all %s', 'ksas-department-tailwind' ),
					$info->label
				),
				'taxonomy'        => 'program',
				'name'            => 'program',
				'orderby'         => 'name',
				'selected'        => $selected,
				'show_count'      => true,
				'hide_empty'      => false,
				'value_field'     => 'slug',
			)
		);
	}
}

/**
 * 5. SEO & DOCUMENT TITLES
 */
add_action(
	'after_setup_theme',
	function () {
		remove_filter( 'pre_get_document_title', 'custom_ksasacademic_page_title', 9999 );
	}
);

add_filter( 'pre_get_document_title', 'custom_ksasacademic_mll_page_title', 99999 );
/**
 * Custom logic for document <title> tag.
 *
 * @param string $title Default title.
 * @return string Modified title.
 */
function custom_ksasacademic_mll_page_title( $title ) {
	$program   = get_current_program_context();
	$site_name = get_bloginfo( 'name' );
	$desc      = get_bloginfo( 'description' );
	$suffix    = ' | Johns Hopkins University';

	if ( is_front_page() ) {
		return "$desc $site_name$suffix";
	}

	if ( is_page_template( 'page-templates/program-homepage.php' ) ) {
		return "{$program['name']} Program | $desc $site_name$suffix";
	}

	if ( ! empty( $program['name'] ) && is_page() ) {
		return get_the_title() . " | {$program['name']} | $desc $site_name$suffix";
	}

	return $title;
}

/**
 * 6. THEME EXTRAS (Body Classes & Widgets)
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular() ) {
			$terms = get_the_terms( get_the_ID(), 'program' );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$classes[] = 'program-' . $terms[0]->slug;
			}
		}
		return $classes;
	}
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() && $query->is_main_query() && is_tax( 'program' ) ) {
			$query->set( 'posts_per_page', '-1' );
		}
	}
);

/**
 * 7. ASSETS & LIBRARIES
 */
add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		$paths[] = get_template_directory() . '/acf-json';
		return $paths;
	}
);

add_action( 'wp_enqueue_scripts', 'ksas_blocks_child_custom_posts_scripts' );
/**
 * Enqueue scripts conditionally for specific templates.
 *
 * @return void
 */
function ksas_blocks_child_custom_posts_scripts() {
	if ( is_page_template( 'page-templates/people-directory-languages-rows.php' ) ) {

		wp_enqueue_script( 'isotope-packaged', 'https://unpkg.com/isotope-layout@3.0.6/dist/isotope.pkgd.min.js', array(), '3.0.6', true );

		wp_enqueue_script(
			'isotope-local',
			get_template_directory_uri() . '/dist/js/isotope.js',
			array( 'jquery' ),
			defined( 'KSAS_DEPARTMENT_TAILWIND_VERSION' ) ? KSAS_DEPARTMENT_TAILWIND_VERSION : '1.0.0',
			true
		);
	} elseif ( is_page_template( 'page-templates/language-program-courses.php' ) ) {
		// Enqueue DataTables Styles - Fourth param must be version string.
		wp_enqueue_style( 'data-tables', '//cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css', array(), '2.1.8' );
		wp_enqueue_style( 'data-tables-searchpanes', '//cdn.datatables.net/searchpanes/2.3.3/css/searchPanes.dataTables.min.css', array(), '2.3.3' );
		wp_enqueue_style( 'data-tables-responsive', '//cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.min.css', array(), '3.0.3' );

		// Local CSS with version.
		wp_enqueue_style( 'courses-css', get_stylesheet_directory_uri() . '/css/courses.css', array(), '1.0.2', 'all' );

		// Register and Enqueue Scripts.
		wp_register_script( 'data-tables', '//cdn.datatables.net/2.1.8/js/dataTables.min.js', array( 'jquery' ), '2.1.8', true );
		wp_script_add_data( 'data-tables', 'defer', true );
		wp_enqueue_script( 'data-tables' );

		wp_register_script( 'data-tables-searchpanes', '//cdn.datatables.net/searchpanes/2.3.3/js/dataTables.searchPanes.min.js', array( 'data-tables' ), '2.3.3', true );
		wp_script_add_data( 'data-tables-searchpanes', 'defer', true );
		wp_enqueue_script( 'data-tables-searchpanes' );

		wp_register_script( 'data-tables-select', '//cdn.datatables.net/select/2.1.0/js/dataTables.select.min.js', array( 'data-tables' ), '2.1.0', true );
		wp_script_add_data( 'data-tables-select', 'defer', true );
		wp_enqueue_script( 'data-tables-select' );

		wp_enqueue_script( 'data-tables-responsive', '//cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js', array( 'data-tables' ), '3.0.3', true );
		wp_script_add_data( 'data-tables-responsive', 'defer', true );

		// Local JS - Added data-tables as a dependency.
		wp_enqueue_script(
			'courses-js',
			get_stylesheet_directory_uri() . '/js/courses.js',
			array( 'jquery', 'data-tables' ),
			'1.1.0',
			true
		);
	}
}
