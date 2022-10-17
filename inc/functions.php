<?php
/**
 * Helper Functions
 *
 * @package Multisite_Cron_Manager
 */

namespace Multisite_Cron_Manager;

/**
 * Get cron data for all sites on the network.
 *
 * @return array[] {
 *     Cron data for the network.
 *     @type int    $id         Site ID.
 *     @type string $domain     Site ID.
 *     @type array  $next_job   {
 *         Data about the next cron job.
 *         @type string $action     The next action to run.
 *         @type int    $timestamp  The timestamp of the next cron event.
 *         @type string $human_time The date & time of the next event.
 *         @type string $diff       The time difference from now.
 *         @type string $diff_dir   "-" if event time is in the past, "+" otherwise.
 *     }
 *     @type array  $last_run   {
 *         Data about the last cron run.
 *         @type string $action     The cron action that ran.
 *         @type int    $timestamp  The timestamp the cron started.
 *         @type string $human_time The date & time it started.
 *         @type string $diff       The time difference from now.
 *         @type string $diff_dir   This should always be "-".
 *     }
 * }
 */
function get_cron_data_for_all_sites(): array {
	$sites = get_sites(
		[
			'number'   => 1000,
			'network'  => get_current_network_id(),
			'archived' => 0,
			'deleted'  => 0,
		]
	);

	$data = array_map(
		function ( $site ) {
			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.switch_to_blog_switch_to_blog
			switch_to_blog( $site->blog_id );
			$cron = get_cron_array();

			$datum = [
				'id'        => (int) $site->blog_id,
				'domain'    => $site->domain . $site->path,
				'next_jobs' => get_next_cron_jobs( $cron ),
				'last_run'  => array_merge(
					get_date_props( (int) get_transient( 'mcm_last_cron_run' ) ),
					[ 'action' => get_transient( 'mcm_last_cron_action' ) ]
				),
			];

			restore_current_blog();
			return $datum;
		},
		$sites
	);

	return $data;
}

/**
 * Get useful properties about a timestamp for use in the cron table, especially
 * relative data.
 *
 * @param int $timestamp Timestamp.
 * @return array
 */
function get_date_props( int $timestamp ): array {
	$current = time();
	return [
		'timestamp'  => $timestamp,
		'human_time' => $timestamp ? wp_date( 'Y-m-d H:i:s T', $timestamp ) : '',
		'diff'       => $timestamp ? human_time_diff( $timestamp, $current ) : '',
		'diff_dir'   => $timestamp < $current ? '-' : '+',
	];
}

/**
 * Get the current cron array from the database.
 *
 * @return array
 */
function get_cron_array(): array {
	// Cron Control caches the cron array; clear it to get it for the current site.
	if ( class_exists( '\Automattic\WP\Cron_Control\Events_Store' ) ) {
		\Automattic\WP\Cron_Control\Events_Store::instance()->flush_internal_caches();
	}
	$cron = get_option( 'cron', [] );

	return is_array( $cron ) ? $cron : [];
}

/**
 * Get either all past due or the next cron job from the cron array.
 *
 * @param array $cron Cron array.
 * @return array[] {
 *     Data about the cron job.
 *     @type string $action     The next action to run.
 *     @type int    $timestamp  The timestamp of the next cron event.
 *     @type string $human_time The date & time of the next event.
 *     @type string $diff       The time difference from now.
 *     @type string $diff_dir   "-" if event time is in the past, "+" otherwise.
 * }
 */
function get_next_cron_jobs( array $cron ): array {
	$current_time = time();
	$next_jobs    = [];

	foreach ( $cron as $timestamp => $jobs ) {
		// phpcs:ignore WordPressVIPMinimum.Variables.VariableAnalysis.UnusedVariable
		foreach ( (array) $jobs as $action => $details ) {
			$next_jobs[] = array_merge( get_date_props( (int) $timestamp ), compact( 'action' ) );
		}
		if ( $timestamp > $current_time ) {
			break;
		}
	}

	return $next_jobs;
}
