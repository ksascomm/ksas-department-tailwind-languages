<?php
/**
 * Conditionally add data tables and courses scripts to Language Programs page
 *
 * Note that this function is hooked into the wp_enqueue_scripts
 */
wp_enqueue_style( 'data-tables', '//cdn.datatables.net/2.0.3/css/jquery.dataTables.min.css', array(), true );

wp_enqueue_style( 'data-tables-searchpanes', '//cdn.datatables.net/searchpanes/2.3.0/css/searchPanes.dataTables.min.css', array(), true );

wp_enqueue_style( 'courses-css', get_stylesheet_directory_uri() . '/css/courses.css', false, '1.0.1', 'all' );

wp_register_script( 'data-tables', '//cdn.datatables.net/2.0.3/js/jquery.dataTables.min.js', array(), '2.0.3', false );
wp_enqueue_script( 'data-tables');
wp_script_add_data( 'data-tables', 'defer', true );

wp_register_script( 'data-tables-searchpanes', '//cdn.datatables.net/searchpanes/2.3.0/js/dataTables.searchPanes.min.js', array(), '2.3.0', false );
wp_enqueue_script( 'data-tables-searchpanes');
wp_script_add_data( 'data-tables-searchpanes', 'defer', true );

wp_register_script( 'data-tables-select', '//cdn.datatables.net/select/2.0.0/js/dataTables.select.min.js', array(), '2.0.0', false );
wp_enqueue_script( 'data-tables-select');
wp_script_add_data( 'data-tables-select', 'defer', true );

wp_register_script( 'courses-js', get_stylesheet_directory_uri() . '/js/courses.js', array( 'jquery' ), '1.0.0', true );
wp_enqueue_script( 'courses-js');