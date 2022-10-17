<?php
/**
 * This file contains REST API endpoints
 *
 * @package Multisite_Cron_Manager
 */

namespace Multisite_Cron_Manager;

add_action( 'rest_api_init', __NAMESPACE__ . '\register_rest_routes' );

/**
 * REST API namespace.
 */
const REST_NS = 'multisite-cron-manager/v1';

/**
 * Register the REST API routes.
 */
function register_rest_routes() {
	$rest_args = [
		'methods'             => \WP_REST_Server::READABLE,
		'callback'            => __NAMESPACE__ . '\rest_response',
		'permission_callback' => function() {
			return current_user_can( 'manage_network_options' );
		},
	];
	register_rest_route( REST_NS, '/list', $rest_args );
}

/**
 * Send API response for REST endpoint.
 *
 * @param \WP_REST_Request $request REST request data.
 * @return \WP_REST_Response WP_REST_Response instance.
 */
function rest_response( $request ) {
	// Send the response.
	return rest_ensure_response(
		[
			'dateStamp' => wp_date( 'Y-m-d H:i:s T' ),
			'rows'      => get_cron_data_for_all_sites(),
		],
	);
}
