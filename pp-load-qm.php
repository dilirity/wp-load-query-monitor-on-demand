<?php
/**
 * Plugin Name: Load Query Monitor on Demand
 * Version: 1.0.0
 * Author: Petar (Pete) Petrov
 * Description: Load the Query Monitor when passing a GET argument to the URL, instead of on every page load.
 */

add_filter( 'option_active_plugins', function ( $plugins ) {
	if ( ! isset( $_GET['pp_load_qm'] ) ) {
		return $plugins;
	}

	$query_monitor_plugin_file_path_partial = 'query-monitor/query-monitor.php';
	if ( in_array( $query_monitor_plugin_file_path_partial, $plugins ) ) {
		return $plugins;
	}

	$query_monitor_plugin_file_path_full = WP_PLUGIN_DIR . '/' . $query_monitor_plugin_file_path_partial;

	if ( file_exists( $query_monitor_plugin_file_path_full ) ) {
		pp_qm_probably_create_symlink();

		$plugins[] = $query_monitor_plugin_file_path_partial;
	}

	return $plugins;
} );

function pp_qm_probably_create_symlink() {
	$file_path         = wp_normalize_path( ABSPATH . 'wp-content/plugins/query-monitor/wp-content/db.php' );
	$symlink_file_path = wp_normalize_path( ABSPATH . 'wp-content/db.php' );

	// no need to create the file
	if ( file_exists( $symlink_file_path ) ) {
		return;
	}

	$symlink_create_command = 'ln -s ' . $file_path . ' ' . $symlink_file_path;

	if ( function_exists( 'exec' ) ) {
		exec( $symlink_create_command );
	} else if ( function_exists( 'shell_exec' ) ) {
		shell_exec( $symlink_create_command );
	}
}
