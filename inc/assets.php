<?php
/**
 * Manage static assets
 *
 * @package Multisite_Cron_Manager
 */

namespace Multisite_Cron_Manager;

/**
 * Get the version for a given asset.
 *
 * @param string $asset_path Entry point and asset type separated by a '.'.
 * @return string The asset version.
 */
function get_versioned_asset_path( $asset_path ) {
	static $asset_map;

	// Create public path.
	$base_path = PLUGIN_URL . '/build/';

	if ( ! isset( $asset_map ) ) {
		$asset_map_file = PLUGIN_DIR . '/build/assetMap.json';

		if ( file_exists( $asset_map_file ) && 0 === validate_file( $asset_map_file ) ) {
			ob_start();
			include $asset_map_file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.IncludingFile, WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			$asset_map = json_decode( ob_get_clean(), true );
		} else {
			$asset_map = [];
		}
	}

	/*
	 * Appending a '.' ensures the explode() doesn't generate a notice while
	 * allowing the variable names to be more readable via list().
	 */
	list( $entrypoint, $type ) = explode( '.', "$asset_path." );
	$versioned_path            = isset( $asset_map[ $entrypoint ][ $type ] ) ? $asset_map[ $entrypoint ][ $type ] : false;

	if ( $versioned_path ) {
		return $base_path . $versioned_path;
	}

	return '';
}
