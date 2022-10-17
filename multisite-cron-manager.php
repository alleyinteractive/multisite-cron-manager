<?php
/**
 * Plugin Name:     Multisite Cron Manager
 * Plugin URI:      https://github.com/alleyinteractive/multisite-cron-manager
 * Description:     Oversee your network's cron queues
 * Author:          Matthew Boynes
 * Author URI:      https://alley.co/
 * Text Domain:     multisite-cron-manager
 * Domain Path:     /languages
 * Version:         0.1.1
 *
 * @package         Multisite_Cron_Manager
 */

namespace Multisite_Cron_Manager;

if ( is_multisite() ) {
	// Record the last cron run.
	if ( wp_doing_cron() ) {
		add_action(
			'init',
			function () {
				set_transient( 'mcm_last_cron_run', time() );

				// Attempt to determine the cron action if Cron Control is in use.
				if (
					class_exists( '\Automattic\WP\Cron_Control\Events_Store' )
					&& function_exists( '\Automattic\WP\Cron_Control\get_event_by_attributes' )
					&& defined( 'WP_CLI' ) && WP_CLI
					&& ! empty( $GLOBALS['argv'] )
				) {
					$args = [];
					foreach ( $GLOBALS['argv'] as $arg ) {
						if ( preg_match( '/^--(timestamp|action|instance)=(.+)$/', $arg, $matches ) ) {
							$args[ $matches[1] ] = $matches[2];
						}
					}

					if ( isset( $args['timestamp'], $args['action'], $args['instance'] ) ) {
						$event = \Automattic\WP\Cron_Control\get_event_by_attributes(
							[
								'timestamp'     => $args['timestamp'],
								'action_hashed' => $args['action'],
								'instance'      => $args['instance'],
								'status'        => \Automattic\WP\Cron_Control\Events_Store::STATUS_PENDING,
							]
						);

						if ( is_object( $event ) ) {
							set_transient( 'mcm_last_cron_action', $event->action );
						}
					}
				}
			}
		);
	}

	// phpcs:disable WordPressVIPMinimum.Constants.ConstantString
	define( __NAMESPACE__ . '\PLUGIN_URL', plugins_url( '', __FILE__ ) );
	define( __NAMESPACE__ . '\PLUGIN_DIR', __DIR__ );
	// phpcs:enable

	require_once __DIR__ . '/inc/functions.php';
	require_once __DIR__ . '/inc/rest-api.php';

	if ( is_network_admin() ) {
		require_once __DIR__ . '/inc/assets.php';
		require_once __DIR__ . '/inc/admin.php';
	}
}
