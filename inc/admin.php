<?php
/**
 * Admin customizations
 *
 * @package Multisite_Cron_Manager
 */

namespace Multisite_Cron_Manager;

add_action(
	'network_admin_menu',
	function () {
		add_menu_page(
			__( 'Network Cron Manager', 'multisite-cron-manager' ),
			__( 'Cron', 'multisite-cron-manager' ),
			'manage_network_options',
			'ms-cron',
			__NAMESPACE__ . '\network_settings_screen'
		);
	}
);

/**
 * Render the network settings screen.
 */
function network_settings_screen() {
	wp_enqueue_script( 'mcm-admin-js', get_versioned_asset_path( 'app.js' ), [ 'wp-api' ], '1.0', true );
	wp_localize_script(
		'mcm-admin-js',
		'cronData',
		[
			'dateStamp' => wp_date( 'Y-m-d H:i:s T' ),
			'rows'      => get_cron_data_for_all_sites(),
			'nonce'     => wp_create_nonce( 'wp_rest' ),
		]
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Network Cron Manager', 'multisite-cron-manager' ); ?></h1>

		<div id="mcm-table"></div>
	</div>
	<?php
}
