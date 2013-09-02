<?php

/*
Plugin Name: Styles Debug: Display all settings
Plugin URI: http://stylesplugin.com
Description: Output all settings related to Styles.
Version: 1.0
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
*/

/**
 * Copyright (c) 2013 Brainstorm Media. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

add_action( 'admin_init', 'Styles_Debug_Output_All_Settings::init' );

class Styles_Debug_Output_All_Settings {

	public static function init() {
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

		if ( isset( $_GET['debug-styles'] ) ) {
			self::output_settings();
		}

	}

	/**
	 * Add additional links to the plugin row
	 */
	public static function plugin_row_meta( $meta, $basename ) {
		if ( $basename == plugin_basename( __FILE__ ) ) {
			$meta[] = '<a href="' . network_admin_url( '?debug-styles' ) . '">Display and email Styles settings</a>';
		}
		return $meta;
	}

	public static function output_settings() {
		global $wpdb;

		$sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE 'storm-styles-%'";
		$result = $wpdb->get_results( $sql );

		if (empty( $result ) ) {
			exit( 'No Styles settings found.' );
		}

		ob_start();

		$theme = wp_get_theme();

		?>
		<style>
			body { font-family:monospace !important; }
			td, th { font-size: 12px; margin:0; padding: 10px; text-align: left; }
			th { background-color: #000; color: #fff; }
			tr:nth-child(odd) { background-color: #ccc; }
		</style>

		<h2>Styles Settings</h2>
		<table>
			<tr>
				<th>option_id</th>
				<th>option_name</th>
				<th>option_value</th>
				<th>autoload</th>
			</tr>
		<?php

		foreach( (array) $result as $row ) {
			?>
			<tr>
				<td><?php echo $row->option_id ?></td>
				<td><?php echo $row->option_name ?></td>
				<td><?php echo $row->option_value ?></td>
				<td><?php echo $row->autoload ?></td>
			</tr>
			<?php
		}
		?>
		</table>

		<h2>Active Theme</h2>
		<pre><?php print_r( $theme ); ?></pre>

		<?php
		$output = ob_get_clean();

		echo $output;

		// Attempt to mail the setings
		wp_mail( 'pdclark@brainstormmedia.com', 'Styles Debug: ' . get_site_url(), $output );

		exit;
	}


}