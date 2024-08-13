<?php
/**
 * Plugin initialization file.
 *
 * @package   SQL_Buddy
 * @license   https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link      https://github.com/deliciousbrains/sql-buddy/
 */

namespace SQL_Buddy;

/**
 * Handles plugin activation.
 *
 * Throws an error if the site is running on PHP < 7.0
 *
 * @since 1.0.0
 *
 * @param bool $network_wide Whether to activate network-wide.
 *
 * @return void
 */
function activate( $network_wide ) {
	if ( version_compare( PHP_VERSION, SQL_BUDDY_MINIMUM_PHP_VERSION, '<' ) ) {
		wp_die(
		/* translators: %s: PHP version number */
			esc_html( sprintf( __( 'SQL Buddy requires PHP %s or higher.', 'sql-buddy' ), SQL_BUDDY_MINIMUM_PHP_VERSION ) ),
			esc_html__( 'Plugin could not be activated', 'sql-buddy' )
		);
	}

	do_action( 'sql_buddy_activation', $network_wide );
}

/**
 * Handles plugin deactivation.
 *
 * @since 1.0.0
 *
 * @param bool $network_wide Whether to deactivate network-wide.
 *
 * @return void
 */
function deactivate( $network_wide ) {
	if ( version_compare( PHP_VERSION, SQL_BUDDY_MINIMUM_PHP_VERSION, '<' ) ) {
		return;
	}

	do_action( 'sql_buddy_deactivation', $network_wide );
}

register_activation_hook( SQL_BUDDY_PLUGIN_FILE, '\SQL_Buddy\activate' );
register_deactivation_hook( SQL_BUDDY_PLUGIN_FILE, '\SQL_Buddy\deactivate' );

if ( ! class_exists( \SQL_Buddy\Plugin::class ) ) {
	/**
	 * Displays an admin notice about why the plugin is unable to load.
	 *
	 * @return void
	 */
	function _print_missing_build_admin_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<strong><?php esc_html_e( 'SQL Buddy plugin could not be initialized.', 'sql-buddy' ); ?></strong>
			</p>
			<p>
				<?php
				echo wp_kses(
					sprintf(
					/* translators: %s: build commands. */
						__( 'You appear to be running an incomplete version of the plugin. Please run %s to finish installation.', 'sql-buddy' ),
						'<code>composer install &amp;&amp; npm install &amp;&amp; npm run build</code>'
					),
					[
						'code' => [],
					]
				);
				?>
			</p>
		</div>
		<?php
	}

	add_action( 'admin_notices', __NAMESPACE__ . '\_print_missing_build_admin_notice' );
}

global $sql_buddy;

$sql_buddy = new Plugin();
$sql_buddy->register();
